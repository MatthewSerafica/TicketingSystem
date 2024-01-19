<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index() {
        $employee = Employee::with('user')->first();
        $ticket = Ticket::with('employee', 'technician')->first();
        return inertia('Admin/Dashboard/Index', [
            'ticket' => $ticket,
            'employee' => $employee,
        ]);
    }
}
