<?php

namespace App\Http\Controllers;

use App\Models\ManPower;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::all(["id", 'nama_project']);
        $man_powers = ManPower::all(["id", 'nama_lengkap']);
        return view('manages.tasks.index', compact('projects', 'man_powers'));
    }

    /*
    * Mengambil datatable dengan ajax
    */
    public function getDataTable(Request $request)
    {
        ## Abort jika request bukan AJAX
        abort_unless($request->ajax(), 400, 'Maaf tidak dapat melanjutkan request.');
        try {
            ## GET DATA accounts
            $data = Task::query()->with(["project", "manPower"]);
            ## Total records
            $totalRecords = $data->count();
            ## Mengatur pengurutan berdasarkan kolom yang diminta
            if ($request->has('order')) {
                foreach ($request->order as $order) {
                    $columnIndex = $order['column']; /*Indeks kolom*/
                    $direction = $order['dir']; /*ASC atau DESC*/
                    ## Menentukan nama kolom berdasarkan indeks
                    $columns = [
                        0 => 'created_at',
                        1 => 'judul_task',
                        4 => 'status',
                    ];
                    ## Setting bagian order
                    if (isset($columns[$columnIndex])) {
                        $data->orderBy($columns[$columnIndex], $direction);
                    }
                }
            } else {
                ## Default order
                $data->orderBy('created_at', 'ASC');
            }
            ## Jika melakukan pencarian data
            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $data->where(function ($query) use ($search) {
                    $query->where('judul_task', 'LIKE', "%{$search}%");
                });
            }
            ## Total records after filtering
            $filteredRecords = $data->count();
            ## Pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            ## Ambil data sesuai offset dan limit
            $data = $data->skip($start)->take($length)->get();
            ## Setting datatable
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                ->addColumn('project_nama', function ($row) {
                    return $row->project->nama_project ?? "-";
                })
                ->addColumn('man_power_nama_lengkap', function ($row) {
                    return $row->manPower->nama_lengkap ?? "-";
                })
                ->addColumn('action', function ($row) {
                    $encryptedId = Crypt::encryptString($row->id);
                    $encryptedId = base64_encode($encryptedId);
                    ## Setting other action buttons
                    $buttons = '';
                    ## Jika memiliki akses update
                    $buttons .= '<a href="javascript:;" title="Edit" class="brn btn-sm btn-warning btn-update_data" data-encrypted_id="' . $encryptedId . '">
                                    <i class="fa fa-edit"></i>
                                </a>';
                    ## Jika memiliki akses delete
                    $buttons .= '<a href="javascript:;" title="Hapus" class="btn btn-sm btn-danger btn-delete_data" data-encrypted_id="' . $encryptedId . '">
                                    <i class="fa fa-trash"></i>
                                </a>';
                    return '<div class="btn-group btn-group-sm" role="group">' . $buttons .
                        '</div>';
                })
                ->rawColumns(['action'])
                ->setTotalRecords($totalRecords) // Set total records
                ->setFilteredRecords($filteredRecords) // Set filtered records
                ->make(true); // Mengembalikan respons JSON
        } catch (QueryException $e) {
            ## Helper from customResponse.php
            return sendErrorResponse("QueryException: " . $e->getMessage(), $e->getCode());
        } catch (ValidationException $e) {
            ## Helper from customResponse.php
            return sendErrorResponse("ValidationException: " . $e->errors(), 422);
        } catch (\Throwable $th) {
            ## Helper from customResponse.php
            return sendErrorResponse("Throwable: " . $th->getMessage(), $th->getCode());
        }
    }

    /*
    * Proses tambah projects
    */
    public function show(Request $request)
    {
        $request->validate([
            'encryptedId'     => 'required|string',
        ]);
        try {
            ## Decode dan decrypt kode yang diterima
            $encryptedId = $request->encryptedId;
            $encryptedId = base64_decode($encryptedId);
            $id          = Crypt::decryptString($encryptedId);
            ## update
            $project     = Task::find($id);
            ## Response sukses
            return response()->json([
                'data' => $project,
                'status' => true,
                'message' => 'Berhasil mendapatkan data.',
            ], 200);
        } catch (\Exception $e) {
            ## Tangani kesalahan
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /*
    * Proses tambah projects
    */
    public function store(Request $request)
    {
        $request->validate([
            'judul_task'    => 'required|string|max:255',
            // 'status'        => 'string|in:in_progress,pending,done', // sesuaikan opsi status jika ada enum
            'man_power_id'  => 'required|exists:man_powers,id', // sesuaikan dengan nama tabel relasi
            'project_id'    => 'required|exists:projects,id',   // sesuaikan juga
        ], [
            // judul_task
            'judul_task.required' => 'Kolom judul task harus diisi.',
            'judul_task.string'   => 'Kolom judul task harus berupa teks.',
            'judul_task.max'      => 'Kolom judul task tidak boleh lebih dari :max karakter.',

            // man_power_id
            'man_power_id.required' => 'Kolom tenaga kerja (man power) harus dipilih.',
            'man_power_id.exists'   => 'Data tenaga kerja yang dipilih tidak ditemukan.',

            // project_id
            'project_id.required' => 'Kolom project harus dipilih.',
            'project_id.exists'   => 'Data project yang dipilih tidak ditemukan.',
        ]);
        try {
            ## create
            Task::create($request->only(
                'judul_task',
                'man_power_id',
                'project_id',
            ));
            ## Response sukses
            return response()->json([
                'status' => true,
                'message' => 'Data baru berhasil ditambahkan.',
            ], 200);
        } catch (\Exception $e) {
            ## Tangani kesalahan
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /*
    * Proses update projects
    */
    public function update(Request $request)
    {
        $request->validate([
            'encryptedId'     => 'required|string',
            'judul_task'    => 'required|string|max:255',
            'status'        => 'required|string|in:in_progress,pending,done', // sesuaikan opsi status jika ada enum
            'man_power_id'  => 'required|exists:man_powers,id', // sesuaikan dengan nama tabel relasi
            'project_id'    => 'required|exists:projects,id',   // sesuaikan juga
        ], [
            // judul_task
            'judul_task.required' => 'Kolom judul task harus diisi.',
            'judul_task.string'   => 'Kolom judul task harus berupa teks.',
            'judul_task.max'      => 'Kolom judul task tidak boleh lebih dari :max karakter.',

            // status
            'status.required' => 'Kolom status harus diisi.',
            'status.string'   => 'Kolom status harus berupa teks.',
            'status.in'       => 'Status harus berupa salah satu dari: pending, proses, atau selesai.',

            // man_power_id
            'man_power_id.required' => 'Kolom tenaga kerja (man power) harus dipilih.',
            'man_power_id.exists'   => 'Data tenaga kerja yang dipilih tidak ditemukan.',

            // project_id
            'project_id.required' => 'Kolom project harus dipilih.',
            'project_id.exists'   => 'Data project yang dipilih tidak ditemukan.',
        ]);
        try {
            ## Decode dan decrypt kode yang diterima
            $encryptedId = $request->encryptedId;
            $encryptedId = base64_decode($encryptedId);
            $id          = Crypt::decryptString($encryptedId);
            ## update
            $task   = Task::find($id);
            $task->judul_task = $request->judul_task;
            $task->man_power_id = $request->man_power_id;
            $task->project_id = $request->project_id;
            $task->status = $request->status;
            $task->save();
            ## Response sukses
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diubah.',
            ], 200);
        } catch (\Exception $e) {
            ## Tangani kesalahan
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /*
    * Proses delete account
    */
    public function destroy(Request $request)
    {
        try {
            ## Validasi input dari form
            $validated = $request->validate([
                'encryptedId' => 'required'
            ]);
            ## Decode dan decrypt kode yang diterima
            $encryptedId = $validated['encryptedId'];
            $encryptedId = base64_decode($encryptedId);
            $id          = Crypt::decryptString($encryptedId);
            ## Menghapus akun
            $task = Task::find($id);
            $task->delete();
            ## Mengembalikan respons sukses
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus.',
            ], 200);
        } catch (\Exception $e) {
            ## Tangani kesalahan lainnya
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
