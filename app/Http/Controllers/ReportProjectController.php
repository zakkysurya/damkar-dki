<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReportProjectController extends Controller
{
    public function index()
    {
        return view('report_project.index');
    }

    public function getDataTable(Request $request)
    {
        abort_unless($request->ajax(), 400, 'Maaf tidak dapat melanjutkan request.');

        try {
            // Join task with project and man_powers
            $data = DB::table('tasks')
                ->join('projects', 'tasks.project_id', '=', 'projects.id')
                ->join('man_powers', 'tasks.man_power_id', '=', 'man_powers.id')
                ->select(
                    'projects.nama_project',
                    'man_powers.nama_lengkap as man_power',
                    DB::raw('COUNT(tasks.id) as total_task'),
                    'projects.id as project_id',
                    'man_powers.id as man_power_id'
                )
                ->groupBy('projects.id', 'projects.nama_project', 'man_powers.id', 'man_powers.nama_lengkap');

            // Optional filtering
            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $data->where(function ($query) use ($search) {
                    $query->where('projects.nama_project', 'LIKE', "%{$search}%")
                        ->orWhere('man_powers.nama_lengkap', 'LIKE', "%{$search}%");
                });
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $projectId = base64_encode(Crypt::encryptString($row->project_id));
                    $manPowerId = base64_encode(Crypt::encryptString($row->man_power_id));

                    return '<a href="' . route('report-project.detail', ['project' => $projectId, 'man_power' => $manPowerId]) . '" class="btn btn-sm btn-primary">Detail</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error: ' . $th->getMessage()], 500);
        }
    }

    public function showDetail($project, $man_power)
    {
        $projectId = Crypt::decryptString(base64_decode($project));
        $manPowerId = Crypt::decryptString(base64_decode($man_power));

        $project = DB::table('projects')->where('id', $projectId)->first();
        $manPower = DB::table('man_powers')->where('id', $manPowerId)->first();
        $tasks = DB::table('tasks')
            ->where('project_id', $projectId)
            ->where('man_power_id', $manPowerId)
            ->get();

        return view('report_project.detail', compact('project', 'manPower', 'tasks'));
    }
}
