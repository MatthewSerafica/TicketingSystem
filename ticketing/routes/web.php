<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\AdminDepartmentController;
use App\Http\Controllers\AdminGenerateReportController;
use App\Http\Controllers\AdminLogsController;
use App\Http\Controllers\AdminOfficeController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminServiceReportController;
use App\Http\Controllers\AdminProblemController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeNotificationController;
use App\Http\Controllers\TechnicianNotificationController;
use App\Http\Controllers\EmployeeTicketController;
use App\Http\Controllers\TechnicianDashboardController;
use App\Http\Controllers\TechnicianForumController;
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

    Route::middleware(['auth', 'admin_or_super',])->group(function () {
        Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin');
        Route::get('/admin/tickets', [AdminTicketController::class, 'index'])->name('admin.tickets');
        Route::get('/admin/tickets/create', [AdminTicketController::class, 'create'])->name('admin.tickets.create');
        Route::get('/admin/tickets/{id}', [AdminTicketController::class, 'show'])->name('admin.tickets.show');
        Route::post('/admin/tickets/{id}/comment', [AdminTicketController::class, 'comment'])->name('admin.ticket.comment');
        Route::post('/admin/tickets/{id}/reply/{comment_id}', [AdminTicketController::class, 'reply'])->name('admin.ticket.reply');
        Route::put('/admin/tickets/comment/edit/{comment_id}', [AdminTicketController::class, 'updateComment'])->name('admin.ticket.edit.comment');
        Route::delete('/admin/tickets/delete/comment/{id}', [AdminTicketController::class, 'deleteComment'])->name('admin.ticket.delete.comment');

        Route::post('/admin/tickets/tasks', [AdminTicketController::class, 'addTask'])->name('admin.tickets.task');
        Route::put('/admin/tickets/tasks/update/{id}', [AdminTicketController::class, 'updateTask'])->name('admin.tickets.task.update');
        Route::delete('/admin/tickets/tasks/delete/{id}', [AdminTicketController::class, 'deleteTask'])->name('admin.tickets.task.delete');


        Route::post('/admin/tickets/create/store', [AdminTicketController::class, 'store'])->name('admin.tickets.store');
        Route::put('/admin/tickets/update-status/{ticket_id}', [AdminTicketController::class, 'status'])->name('admin.tickets.update.status');
        Route::put('/admin/tickets/update-service/{ticket_id}', [AdminTicketController::class, 'service'])->name('admin.tickets.update.service');
        Route::put('/admin/tickets/update/{field}/{ticket_id}', [AdminTicketController::class, 'update'])->name('admin.tickets.update');
        Route::put('/admin/tickets/update-complexity/{ticket_id}', [AdminTicketController::class, 'complexity'])->name('admin.tickets.update.complexity');
        Route::put('/admin/tickets/replace/technician', [AdminTicketController::class, 'replace'])->name('admin.tickets.replace.technician');
        Route::delete('/admin/tickets/remove/technician', [AdminTicketController::class, 'remove'])->name('admin.tickets.remove.technician');
        Route::get('/admin/tickets/recommended/{department}', [AdminTicketController::class, 'recommend'])->name('admin.recommended');
        Route::post('/admin/tickets/problems/create/store', [AdminTicketController::class, 'problem'])->name('admin.ticket.problems.store');
        Route::post('/admin/tickets/services/create/store', [AdminTicketController::class, 'services'])->name('admin.ticket.services.store');


        Route::get('/admin/forum', [TechnicianForumController::class, 'index'])->name('admin.forum');
        Route::get('/admin/forum/post/{post_id}', [TechnicianForumController::class, 'show'])->name('admin.forum.post');
        Route::get('/admin/forum/post/users/{term}', [TechnicianForumController::class, 'getUsers'])->name('admin.forum.post.users');
        Route::delete('/admin/forum/delete/{post_id}', [TechnicianForumController::class, 'delete'])->name('admin.forum.delete');
        Route::post('/admin/forum/store', [TechnicianForumController::class, 'store'])->name('admin.forum.store');
        Route::post('/admin/forum/comment/{post_id}/{title}', [TechnicianForumController::class, 'comment'])->name('admin.forum.comment');
        Route::put('/admin/forum/comment/{post_id}', [TechnicianForumController::class, 'update'])->name('admin.forum.edit.comment');
        Route::delete('/admin/forum/delete/comment/{post_id}', [TechnicianForumController::class, 'deleteComment'])->name('admin.forum.delete.comment');
        Route::post('/admin/forum/comment/{post_id}/{title}/reply/{comment_id}', [TechnicianForumController::class, 'reply'])->name('admin.forum.reply');

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
        Route::get('/admin/reports/service-report/{id}', [AdminServiceReportController::class, 'show'])->name('admin.reports.service-report.show');
        Route::post('/admin/reports/service-report/create/store', [AdminServiceReportController::class, 'store'])->name('admin.reports.service-report.store');
        Route::put('/admin/reports/service-report/{id}/edit', [AdminServiceReportController::class, 'update'])->name('admin.reports.service-report.update');
        Route::get('/admin/check-service-id/{serviceId}', [AdminServiceReportController::class, 'check_service_id']);
        Route::get('/admin/reports/service-report/{id}/assigned-technicians', [AdminServiceReportController::class, 'assigned'])->name('admin.tickets.assigned');

        Route::get('/admin/reports/generate-report', [AdminGenerateReportController::class, 'index'])->name('admin.reports.generate-report');
        Route::get('/admin/reports/collate', [AdminGenerateReportController::class, 'showCollate'])->name('admin.reports.collate');
        Route::get('/admin/reports/collate/print', [AdminGenerateReportController::class, 'printCollate'])->name('admin.reports.collate.print');
        Route::get('/admin/reports/generate-report/show/{from}/{to}', [AdminGenerateReportController::class, 'show'])->name('admin.reports.generate-report.show');
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

        Route::get('/admin/problems', [AdminProblemController::class, 'index'])->name('admin.problems');
        Route::get('/admin/problems/create', [AdminProblemController::class, 'create'])->name('admin.problems.create');
        Route::post('/admin/problems/create/store', [AdminProblemController::class, 'store'])->name('admin.problems.store');
        Route::put('/admin/problems/update/{problem_id}', [AdminProblemController::class, 'update'])->name('admin.problems.update');
        Route::delete('/admin/problems/delete/{problem_id}', [AdminProblemController::class, 'destroy'])->name('admin.problems.delete');
        
        Route::get('/admin/logs', [AdminLogsController::class, 'index'])->name('admin.logs');

        Route::get('/admin/notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications');
        Route::post('/admin/notifications/seen', [AdminNotificationController::class, 'update'])->name('admin.notifications.seen');
        Route::delete('/admin/notifications/mark/{notif_id}', [AdminNotificationController::class, 'marked'])->name('admin.notifications.marked');

        Route::get('/admin/profile', [AdminProfileController::class, 'profile'])->name('admin.profile');
        Route::post('/admin/profile/{user_id}/avatar', [AdminProfileController::class, 'avatar'])->name('admin.profile.avatar');
        Route::put('/admin/update/{user_id}/{field}', [AdminProfileController::class, 'update'])->name('admin.update');
        Route::put('/admin/tech/update/{user_id}/{field}', [AdminProfileController::class, 'technician'])->name('admin.tech.update');

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
        Route::put('/technician/update/{user_id}/{field}', [TechnicianDashboardController::class, 'update'])->name('technician.update');
        Route::put('/technician/tech/update/{user_id}/{field}', [TechnicianDashboardController::class, 'technician'])->name('technician.tech.update');
        Route::get('/technician/change', [TechnicianDashboardController::class, 'password'])->name('technician.change');
        Route::post('/technician/change-password/{user_id}', [TechnicianDashboardController::class, 'changePassword'])->name('technician.change-password');
        Route::get('/technician/profile', [TechnicianDashboardController::class, 'profile'])->name('technician.profile');
        Route::post('/technician/profile/{user_id}/avatar', [TechnicianDashboardController::class, 'avatar'])->name('technician.profile.avatar');

        Route::get('/technician/tickets', [TechnicianTicketController::class, 'index'])->name('technician.tickets');
        Route::get('/technician/tickets/create', [TechnicianTicketController::class, 'create'])->name('technician.tickets.create');
        Route::get('/technician/tickets/{id}', [TechnicianTicketController::class, 'show'])->name('technician.tickets.show');
        Route::post('/technician/tickets/{id}/comment', [TechnicianTicketController::class, 'comment'])->name('technician.ticket.comment');
        Route::post('/technician/tickets/{id}/reply/{comment_id}', [TechnicianTicketController::class, 'reply'])->name('technician.ticket.reply');
        Route::put('/technician/tickets/comment/edit/{comment_id}', [TechnicianTicketController::class, 'updateComment'])->name('technician.ticket.edit.comment');
        Route::delete('/technician/tickets/delete/comment/{id}', [TechnicianTicketController::class, 'deleteComment'])->name('technician.ticket.delete.comment');
        Route::post('/technician/tickets/create/store', [TechnicianTicketController::class, 'store'])->name('technician.tickets.store');
        Route::put('/technician/tickets/update-complexity/{ticket_id}', [TechnicianTicketController::class, 'complexity'])->name('technician.tickets.update.complexity');
        Route::put('/technician/tickets/update-status/{ticket_id}', [TechnicianTicketController::class, 'status'])->name('technician.tickets.update.status');
        Route::put('/technician/tickets/update-service/{ticket_id}', [TechnicianTicketController::class, 'service'])->name('technician.tickets.update.service');
        Route::put('/technician/tickets/update/{field}/{ticket_id}', [TechnicianTicketController::class, 'update'])->name('technician.tickets.update');
        Route::post('/technician/tickets/problems/create/store', [TechnicianTicketController::class, 'problem'])->name('technician.ticket.problems.store');
        Route::post('/technician/tickets/services/create/store', [TechnicianTicketController::class, 'services'])->name('technician.ticket.services.store');
        Route::post('/technician/tickets/tasks', [TechnicianTicketController::class, 'addTask'])->name('technician.tickets.task');
        Route::put('/technician/tickets/tasks/update/{id}', [TechnicianTicketController::class, 'updateTask'])->name('technician.tickets.task.update');

        Route::get('/technician/service-report', [TechnicianServiceController::class, 'index'])->name('technician.service-reports');
        Route::post('/technician/service-report/back/{service_id}', [TechnicianServiceController::class, 'back'])->name('technician.service-reports.back');
        Route::get('/technician/service-report/create', [TechnicianServiceController::class, 'create'])->name('technician.service-report.create');
        Route::get('/technician/service-report/update', [TechnicianServiceController::class, 'update'])->name('technician.service-report.update');
        Route::post('/technician/service-report/create/store', [TechnicianServiceController::class, 'store'])->name('technician.service-report.store');
        Route::get('/technician/service-report/service-report/{id}/assigned-technicians', [TechnicianServiceController::class, 'assigned'])->name('technician.tickets.assigned');

        Route::get('/technician/forum', [TechnicianForumController::class, 'index'])->name('technician.forum');
        Route::get('/technician/forum/post/{post_id}', [TechnicianForumController::class, 'show'])->name('technician.forum.post');
        Route::get('/technician/forum/post/users/{term}', [TechnicianForumController::class, 'getUsers'])->name('technician.forum.post.users');
        Route::delete('/technician/forum/delete/{post_id}', [TechnicianForumController::class, 'delete'])->name('technician.forum.delete');
        Route::post('/technician/forum/store', [TechnicianForumController::class, 'store'])->name('technician.forum.store');
        Route::post('/technician/forum/comment/{post_id}/{title}', [TechnicianForumController::class, 'comment'])->name('technician.forum.comment');
        Route::put('/technician/forum/comment/{post_id}', [TechnicianForumController::class, 'update'])->name('technician.forum.edit.comment');
        Route::delete('/technician/forum/delete/comment/{post_id}', [TechnicianForumController::class, 'deleteComment'])->name('technician.forum.delete.comment');
        Route::post('/technician/forum/comment/{post_id}/{title}/reply/{comment_id}', [TechnicianForumController::class, 'reply'])->name('technician.forum.reply');

        Route::get('/technician/notifications', [TechnicianNotificationController::class, 'index'])->name('technician.notifications');
        Route::post('/technician/notifications/seen', [TechnicianNotificationController::class, 'update'])->name('technician.notifications.seen');
        Route::delete('/technician/notifications/mark/{notif_id}', [TechnicianNotificationController::class, 'marked'])->name('technician.notifications.marked');
    });
});
