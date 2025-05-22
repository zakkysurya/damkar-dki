<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProjects = Project::count();
        $totalTasks = Task::count();
        $tasksByStatus = Task::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard', [
            'totalProjects' => $totalProjects,
            'totalTasks' => $totalTasks,
            'tasksByStatus' => $tasksByStatus,
        ]);
    }
}
