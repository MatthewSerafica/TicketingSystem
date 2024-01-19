<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index() {
        $tickets = Ticket::with('employee.user', 'technician.user')->whereDate('created_at',today())->take(3)->get();
        return inertia('Admin/Dashboard/Index', [
            'tickets' => $tickets,
        ]);
    }
}
