<?php

namespace App\Http\Controllers;

use App\Models\ManPower;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class ManPowerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manages.man-powers.index');
    }

    /*
    * Mengambil datatable dengan ajax
    */
    public function getDataTable(Request $request)
    {
        ## Abort jika request bukan AJAX
        abort_unless($request->ajax(), 400, 'Maaf tidak dapat melanjutkan request.');
        try {
            ## GET DATA manpowers
            $data = ManPower::query();
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
                        1 => 'nama_lengkap',
                        2 => 'jabatan',
                        3 => 'no_telepon',
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
                    $query->where('nama_lengkap', 'LIKE', "%{$search}%")
                        ->orWhere('jabatan', 'LIKE', "%{$search}%")
                        ->orWhere('no_telepon', 'LIKE', "%{$search}%");
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
            $project     = ManPower::find($id);
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
            'nama_lengkap'     => 'required|string|max:255',
            'jabatan'          => 'required|string',
            'no_telepon'       => 'required|string',
        ], [
            'nama_lengkap.required' => 'Kolom nama lengkap harus diisi.',
            'nama_lengkap.string'   => 'Kolom nama lengkap harus berupa teks.',
            'nama_lengkap.max'      => 'Kolom nama lengkap tidak boleh lebih dari :max karakter.',

            // jabatan
            'jabatan.required' => 'Kolom jabatan harus diisi.',
            'jabatan.string'   => 'Kolom jabatan harus berupa teks.',

            // no telepon
            'no_telepon.required' => 'Kolom nomor telepon harus diisi.',
            'no_telepon.string'   => 'Kolom nomor telepon harus berupa teks.',
        ]);
        try {
            ## create
            ManPower::create($request->only(
                'nama_lengkap',
                'jabatan',
                'no_telepon',
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
            'nama_lengkap'     => 'required|string|max:255',
            'jabatan'          => 'required|string',
            'no_telepon'       => 'required|string',
        ], [
            'nama_lengkap.required' => 'Kolom nama lengkap harus diisi.',
            'nama_lengkap.string'   => 'Kolom nama lengkap harus berupa teks.',
            'nama_lengkap.max'      => 'Kolom nama lengkap tidak boleh lebih dari :max karakter.',

            // jabatan
            'jabatan.required' => 'Kolom jabatan harus diisi.',
            'jabatan.string'   => 'Kolom jabatan harus berupa teks.',

            // no telepon
            'no_telepon.required' => 'Kolom nomor telepon harus diisi.',
            'no_telepon.string'   => 'Kolom nomor telepon harus berupa teks.',
        ]);
        try {
            ## Decode dan decrypt kode yang diterima
            $encryptedId = $request->encryptedId;
            $encryptedId = base64_decode($encryptedId);
            $id          = Crypt::decryptString($encryptedId);
            ## update
            $man_power   = ManPower::find($id);
            $man_power->nama_lengkap = $request->nama_lengkap;
            $man_power->jabatan = $request->jabatan;
            $man_power->no_telepon = $request->no_telepon;
            $man_power->save();
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
    * Proses delete manpower
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
            $project = ManPower::find($id);
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
