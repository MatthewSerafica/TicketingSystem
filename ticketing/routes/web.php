<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeTicketController;
use App\Http\Controllers\TechnicianDashboardController;
use App\Http\Controllers\TechnicianServiceController;
use App\Http\Controllers\TechnicianTicketController;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['web'])->group(function () {
    Route::get('', [AuthController::class, 'create'])->name('login');
    Route::post('login', [AuthController::class, 'store'])->name('login.store');
    Route::delete('logout', [AuthController::class, 'destroy'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin');
        Route::get('/admin/tickets', [AdminTicketController::class, 'index'])->name('admin.tickets');
        Route::get('/admin/tickets/create', [AdminTicketController::class, 'create'])->name('admin.tickets.create');
        Route::post('/admin/tickets/create/store', [AdminTicketController::class, 'store'])->name('admin.tickets.store');
    });
    Route::middleware(['auth', 'employee'])->group(function () {
        Route::get('/employee', [EmployeeTicketController::class, 'index']);
        Route::get('/employee/create', [EmployeeTicketController::class, 'create'])->name('employee.create');
    });
    Route::middleware(['auth', 'technician'])->group(function () {
        Route::get('/technician', [TechnicianDashboardController::class, 'index']);
        Route::get('/technician/tickets', [TechnicianTicketController::class, 'index']);
        Route::get('/technician/tickets/create', [TechnicianTicketController::class, 'create']);
        Route::get('/technician/service-report', [TechnicianServiceController::class, 'index']);
        Route::get('/technician/service-report/create', [TechnicianServiceController::class, 'create']);
    });
});
