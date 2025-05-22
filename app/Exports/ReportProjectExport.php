<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportProjectExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Ambil data yang sama seperti di getDataTable
        return DB::table('tasks')
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->join('man_powers', 'tasks.man_power_id', '=', 'man_powers.id')
            ->select(
                'projects.nama_project',
                'man_powers.nama_lengkap as man_power',
                DB::raw('COUNT(tasks.id) as total_task')
            )
            ->groupBy('projects.id', 'projects.nama_project', 'man_powers.id', 'man_powers.nama_lengkap')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Project',
            'Nama Man Power',
            'Total Task',
        ];
    }
}
