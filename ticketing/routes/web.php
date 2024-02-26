<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\AdminDepartmentController;
use App\Http\Controllers\AdminOfficeController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeNotificationController;
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
        Route::put('/admin/tickets/update-status/{ticket_id}', [AdminTicketController::class, 'status'])->name('admin.tickets.update.status');
        Route::put('/admin/tickets/update-technician/{ticket_id}', [AdminTicketController::class, 'technician'])->name('admin.tickets.update.technician');
        Route::get('/admin/tickets/search', [AdminTicketController::class, 'search'])->name('admin.tickets.search');
        Route::get('/admin/users', [AdminUsersController::class, 'index'])->name('admin.users');
        Route::get('/admin/department', [AdminDepartmentController::class, 'index'])->name('admin.department');
        Route::get('/admin/office', [AdminOfficeController::class, 'index'])->name('admin.office');
        Route::get('/admin/notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications');
        Route::post('/admin/notifications/seen', [AdminNotificationController::class, 'update'])->name('admin.notifications.seen');
    });

    Route::middleware(['auth', 'employee'])->group(function () {
        Route::get('/employee', [EmployeeTicketController::class, 'index'])->name('employee');
        Route::get('/employee/create', [EmployeeTicketController::class, 'create'])->name('employee.create');
        Route::post('/employee/create/store', [EmployeeTicketController::class, 'store'])->name('employee.store');
        Route::get('/employee/notifications', [EmployeeNotificationController::class, 'index'])->name('employee.notifications');
        Route::post('/employee/notifications/seen', [EmployeeNotificationController::class, 'update'])->name('employee.notifications.seen');
        Route::put('/employee/tickets/update-status/{ticket_id}', [EmployeeTicketController::class, 'status'])->name('employee.tickets.update.status');
    });

    Route::middleware(['auth', 'technician'])->group(function () {
        Route::get('/technician', [TechnicianDashboardController::class, 'index'])->name('technician');
        Route::get('/technician/tickets', [TechnicianTicketController::class, 'index'])->name('technician.tickets');
        Route::get('/technician/tickets/create', [TechnicianTicketController::class, 'create'])->name('technician.tickets.create');
        Route::post('/technician/tickets/create/store', [TechnicianTicketController::class, 'store'])->name('technician.tickets.store');
        Route::put('/technician/tickets/update-status/{ticket_id}', [TechnicianTicketController::class, 'status'])->name('technician.tickets.update.status');
        Route::get('/technician/service-report', [TechnicianServiceController::class, 'index'])->name('technician.service-reports');
        Route::get('/technician/service-report/create', [TechnicianServiceController::class, 'create'])->name('technician.service-report.create');
        Route::post('/technician/service-report/create/store', [TechnicianServiceController::class, 'store'])->name('technician.service-report.store');
    });
});
