<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\EmployeeTicketController;
use App\Http\Controllers\TechnicianDashboardController;
use App\Http\Controllers\TechnicianServiceController;
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

Route::get('/admin', [AdminDashboardController::class, 'index']);
Route::get('/admin/tickets', [AdminTicketController::class, 'index']);
Route::get('/admin/tickets/create', [AdminTicketController::class, 'create']);

Route::get('/employee', [EmployeeTicketController::class, 'index']);
Route::get('/employee/create', [EmployeeTicketController::class, 'create'])->name('employee.create');

Route::get('/technician', [TechnicianDashboardController::class, 'index']);
Route::get('/technician/service-report', [TechnicianServiceController::class, 'index']);
Route::get('/technician/service-report/create', [TechnicianServiceController::class, 'create']);
Route::get('/technician/ticket/create', [TechnicianServiceController::class, 'create']);

