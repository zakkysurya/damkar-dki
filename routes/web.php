<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ManPowerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportProjectController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();
## Grouping Middleware: Auth
Route::group(['middleware' => ['auth']], function () {
    ## Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    ## Grouping Prefix - report project
    Route::prefix('report-project')->name('report-project.')->group(function () {
        ## Main page
        Route::get('/', [ReportProjectController::class, 'index'])->name('index');
        ## Access for ajax datatable
        Route::get('/data-table', [ReportProjectController::class, 'getDataTable'])->name('data-table');
        ## Detail
        Route::get('/detail/{project}/{man_power}', [ReportProjectController::class, 'showDetail'])->name('detail');
        ## Export excel
        Route::get('export', [ReportProjectController::class, 'export'])->name('export');
    });

    ## Grouping Prefix - manages
    Route::prefix('manages')->name('manages.')->group(function () {
        ## Grouping Prefix - projects
        Route::prefix('projects')->name('projects.')->group(function () {
            ## Main page
            Route::get("/", [ProjectController::class, 'index'])->name('index');
            ## Access for ajax datatable
            Route::get("data-table", [ProjectController::class, 'getDataTable'])->name('data-table');
            Route::get("show", [ProjectController::class, 'show'])->name('show');
            ## Create
            Route::post("store", [ProjectController::class, 'store'])->name('store');
            ## Update
            Route::put("update", [ProjectController::class, 'update'])->name('update');
            ## Delete
            Route::post("destroy", [ProjectController::class, 'destroy'])->name('destroy');
        });
        ## Grouping Prefix - man-powers
        Route::prefix('man-powers')->name('man-powers.')->group(function () {
            ## Main page
            Route::get("/", [ManPowerController::class, 'index'])->name('index');
            ## Access for ajax datatable
            Route::get("data-table", [ManPowerController::class, 'getDataTable'])->name('data-table');
            Route::get("show", [ManPowerController::class, 'show'])->name('show');
            ## Create
            Route::post("store", [ManPowerController::class, 'store'])->name('store');
            ## Update
            Route::put("update", [ManPowerController::class, 'update'])->name('update');
            ## Delete
            Route::post("destroy", [ManPowerController::class, 'destroy'])->name('destroy');
        });
        ## Grouping Prefix - tasks
        Route::prefix('tasks')->name('tasks.')->group(function () {
            ## Main page
            Route::get("/", [TaskController::class, 'index'])->name('index');
            ## Access for ajax datatable
            Route::get("data-table", [TaskController::class, 'getDataTable'])->name('data-table');
            Route::get("show", [TaskController::class, 'show'])->name('show');
            ## Create
            Route::post("store", [TaskController::class, 'store'])->name('store');
            ## Update
            Route::put("update", [TaskController::class, 'update'])->name('update');
            ## Delete
            Route::post("destroy", [TaskController::class, 'destroy'])->name('destroy');
        });
    });
});/*END: middleware*/
