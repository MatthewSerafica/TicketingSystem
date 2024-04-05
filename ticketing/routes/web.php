<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\AdminDepartmentController;
use App\Http\Controllers\AdminGenerateReportController;
use App\Http\Controllers\AdminOfficeController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminServiceReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeNotificationController;
use App\Http\Controllers\TechnicianNotificationController;
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
        Route::put('/admin/tickets/update-service/{ticket_id}', [AdminTicketController::class, 'service'])->name('admin.tickets.update.service');
        Route::put('/admin/tickets/update/{field}/{ticket_id}', [AdminTicketController::class, 'update'])->name('admin.tickets.update');
        Route::put('/admin/tickets/update-complexity/{ticket_id}', [AdminTicketController::class, 'complexity'])->name('admin.tickets.update.complexity');
        Route::put('/admin/tickets/replace/technician', [AdminTicketController::class, 'replace'])->name('admin.tickets.replace.technician');
        Route::delete('/admin/tickets/remove/technician', [AdminTicketController::class, 'remove'])->name('admin.tickets.remove.technician');
        Route::get('/admin/tickets/recommended/{department}', [AdminTicketController::class, 'recommend'])->name('admin.recommended');

        Route::get('/admin/users', [AdminUsersController::class, 'index'])->name('admin.users');
        Route::get('/admin/users/create', [AdminUsersController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users/create/store', [AdminUsersController::class, 'store'])->name('admin.users.store');
        Route::post('/admin/users/bulk', [AdminUsersController::class, 'bulk'])->name('admin.users.bulk');
        Route::get('/admin/users/show/{user_id}', [AdminUsersController::class, 'show'])->name('admin.users.show');
        Route::put('/admin/users/{user_id}/{field}', [AdminUsersController::class, 'update'])->name('admin.users.update');
        Route::put('/admin/users/employee/{user_id}/{field}', [AdminUsersController::class, 'employee'])->name('admin.users.update.employee');
        Route::put('/admin/users/technician/{user_id}/{field}', [AdminUsersController::class, 'technician'])->name('admin.users.update.technician');

        Route::get('/admin/reports/service-report', [AdminServiceReportController::class, 'index'])->name('admin.reports.service-reports');
        Route::get('/admin/reports/service-report/create', [AdminServiceReportController::class, 'create'])->name('admin.reports.service-report.create');
        Route::post('/admin/reports/service-report/create/store', [AdminServiceReportController::class, 'store'])->name('admin.reports.service-report.store');
        Route::get('/admin/check-service-id/{serviceId}', [AdminServiceReportController::class, 'check_service_id']);
        Route::get('/admin/reports/service-report/{id}/assigned-technicians', [AdminServiceReportController::class, 'assigned'])->name('admin.tickets.assigned');

        Route::get('/admin/reports/generate-report', [AdminGenerateReportController::class, 'index'])->name('admin.reports.generate-report');
        Route::get('/admin/reports/generate-report/{year}/{month}/print', [AdminGenerateReportController::class, 'print'])->name('admin.reports.generate-report.print');

        Route::get('/admin/department', [AdminDepartmentController::class, 'index'])->name('admin.department');
        Route::get('/admin/department/create', [AdminDepartmentController::class, 'create'])->name('admin.department.create');
        Route::post('/admin/department/create/store', [AdminDepartmentController::class, 'store'])->name('admin.department.store');
        Route::put('/admin/department/update/{department_id}', [AdminDepartmentController::class, 'update'])->name('admin.department.update');
        Route::delete('/admin/department/delete/{department_id}', [AdminDepartmentController::class, 'destroy'])->name('admin.department.delete');

        Route::get('/admin/office', [AdminOfficeController::class, 'index'])->name('admin.office');
        Route::get('/admin/office/create', [AdminOfficeController::class, 'create'])->name('admin.office.create');
        Route::post('/admin/office/create/store', [AdminOfficeController::class, 'store'])->name('admin.office.store');
        Route::put('/admin/office/update/{office_id}', [AdminOfficeController::class, 'update'])->name('admin.office.update');
        Route::delete('/admin/office/delete/{office_id}', [AdminOfficeController::class, 'destroy'])->name('admin.office.delete');

        Route::get('/admin/services', [AdminServiceController::class, 'index'])->name('admin.services');
        Route::get('/admin/services/create', [AdminServiceController::class, 'create'])->name('admin.services.create');
        Route::post('/admin/services/create/store', [AdminServiceController::class, 'store'])->name('admin.services.store');
        Route::put('/admin/services/update/{service_id}', [AdminServiceController::class, 'update'])->name('admin.services.update');
        Route::delete('/admin/services/delete/{service_id}', [AdminServiceController::class, 'destroy'])->name('admin.services.delete');

        Route::get('/admin/notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications');
        Route::post('/admin/notifications/seen', [AdminNotificationController::class, 'update'])->name('admin.notifications.seen');
        Route::delete('/admin/notifications/mark/{notif_id}', [AdminNotificationController::class, 'marked'])->name('admin.notifications.marked');

        Route::get('/admin/users/change', [AdminUsersController::class, 'password'])->name('admin.change');
        Route::post('/admin/users/change-password/{user_id}', [AdminUsersController::class, 'changePassword'])->name('admin.change-password');
    });

    Route::middleware(['auth', 'employee'])->group(function () {
        Route::get('/employee', [EmployeeTicketController::class, 'index'])->name('employee');
        Route::get('/employee/change', [EmployeeTicketController::class, 'password'])->name('employee.change');
        Route::post('/employee/change-password/{user_id}', [EmployeeTicketController::class, 'changePassword'])->name('employee.change-password');
        Route::get('/employee/profile/{user_id}', [EmployeeTicketController::class, 'profile'])->name('employee.profile');
        Route::get('/employee/create', [EmployeeTicketController::class, 'create'])->name('employee.create');
        Route::post('/employee/create/store', [EmployeeTicketController::class, 'store'])->name('employee.store');
        Route::get('/employee/notifications', [EmployeeNotificationController::class, 'index'])->name('employee.notifications');
        Route::post('/employee/notifications/seen', [EmployeeNotificationController::class, 'update'])->name('employee.notifications.seen');
    });

    Route::middleware(['auth', 'technician'])->group(function () {
        Route::get('/technician', [TechnicianDashboardController::class, 'index'])->name('technician');
        Route::put('/technician/update/status/{is_working}', [TechnicianDashboardController::class, 'updateStatus'])->name('technician.update.status');
        Route::get('/technician/change', [TechnicianDashboardController::class, 'password'])->name('technician.change');
        Route::post('/technician/change-password/{user_id}', [TechnicianDashboardController::class, 'changePassword'])->name('technician.change-password');
        Route::get('/technician/profile', [TechnicianDashboardController::class, 'profile'])->name('technician.profile');

        Route::get('/technician/tickets', [TechnicianTicketController::class, 'index'])->name('technician.tickets');
        Route::get('/technician/tickets/create', [TechnicianTicketController::class, 'create'])->name('technician.tickets.create');
        Route::post('/technician/tickets/create/store', [TechnicianTicketController::class, 'store'])->name('technician.tickets.store');
        Route::put('/technician/tickets/update-complexity/{ticket_id}', [TechnicianTicketController::class, 'complexity'])->name('technician.tickets.update.complexity');
        Route::put('/technician/tickets/update-status/{ticket_id}', [TechnicianTicketController::class, 'status'])->name('technician.tickets.update.status');
        Route::put('/technician/tickets/update-service/{ticket_id}', [TechnicianTicketController::class, 'service'])->name('technician.tickets.update.service');
        Route::put('/technician/tickets/update/{field}/{ticket_id}', [TechnicianTicketController::class, 'update'])->name('technician.tickets.update');

        Route::get('/technician/service-report', [TechnicianServiceController::class, 'index'])->name('technician.service-reports');
        Route::get('/technician/service-report/create', [TechnicianServiceController::class, 'create'])->name('technician.service-report.create');
        Route::get('/technician/service-report/update', [TechnicianServiceController::class, 'update'])->name('technician.service-report.update');
        Route::post('/technician/service-report/create/store', [TechnicianServiceController::class, 'store'])->name('technician.service-report.store');
        Route::get('/technician/service-report/service-report/{id}/assigned-technicians', [TechnicianServiceController::class, 'assigned'])->name('technician.tickets.assigned');

        Route::get('/technician/notifications', [TechnicianNotificationController::class, 'index'])->name('technician.notifications');
        Route::post('/technician/notifications/seen', [TechnicianNotificationController::class, 'update'])->name('technician.notifications.seen');
        Route::delete('/technician/notifications/mark/{notif_id}', [TechnicianNotificationController::class, 'marked'])->name('technician.notifications.marked');
    });
});
