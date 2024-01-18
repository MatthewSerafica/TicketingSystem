<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\EmployeeTicketController;
use App\Http\Controllers\TechnicianDashboardController;
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
Route::get('/employee', [EmployeeTicketController::class, 'index']);
Route::get('/technician', [TechnicianDashboardController::class, 'index']);