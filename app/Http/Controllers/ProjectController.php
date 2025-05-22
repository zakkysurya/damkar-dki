<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manages.projects.index');
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
            $data = Project::query();
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
                        1 => 'nama_project',
                        2 => 'deskripsi',
                        3 => 'tanggal_mulai',
                        4 => 'tanggal_selesai'
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
                    $query->where('nama_project', 'LIKE', "%{$search}%")
                        ->orWhere('deskripsi', 'LIKE', "%{$search}%");
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
            $project     = Project::find($id);
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
            'nama_project'   => 'required|string|max:255',
            'deskripsi'        => 'required|string',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            'nama_project.required' => 'Kolom nama project harus diisi.',
            'nama_project.string'   => 'Kolom nama project harus berupa teks.',
            'nama_project.max'      => 'Kolom nama project tidak boleh lebih dari :max karakter.',
            'deskripsi.required'      => 'Deskripsi harus diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi.',
            'tanggal_mulai.date'     => 'Tanggal mulai tidak valid.',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi.',
            'tanggal_selesai.date'     => 'Tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);
        try {
            ## create
            Project::create($request->only(
                'nama_project',
                'deskripsi',
                'tanggal_mulai',
                'tanggal_selesai',
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
            'nama_project'      => 'required|string|max:255',
            'deskripsi'         => 'required|string',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            'nama_project.required' => 'Kolom nama project harus diisi.',
            'nama_project.string'   => 'Kolom nama project harus berupa teks.',
            'nama_project.max'      => 'Kolom nama project tidak boleh lebih dari :max karakter.',
            'deskripsi.required'      => 'Deskripsi harus diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi.',
            'tanggal_mulai.date'     => 'Tanggal mulai tidak valid.',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi.',
            'tanggal_selesai.date'     => 'Tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);
        try {
            ## Decode dan decrypt kode yang diterima
            $encryptedId = $request->encryptedId;
            $encryptedId = base64_decode($encryptedId);
            $id          = Crypt::decryptString($encryptedId);
            ## update
            $project     = Project::find($id);
            $project->nama_project = $request->nama_project;
            $project->deskripsi = $request->deskripsi;
            $project->tanggal_mulai = $request->tanggal_mulai;
            $project->tanggal_selesai = $request->tanggal_selesai;
            $project->save();
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
            $project = Project::find($id);
            $project->delete();
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
