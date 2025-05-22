<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportProjectController;
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
    });
});/*END: middleware*/
