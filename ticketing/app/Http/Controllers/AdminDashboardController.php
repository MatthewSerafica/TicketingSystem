<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index() {
        $tickets = Ticket::with('employee.user', 'technician1.user', 'technician2.user', 'technician3.user')->whereDate('created_at',today())->orderByDesc('created_at')->take(3)->get();
        return inertia('Admin/Dashboard/Index', [
            'tickets' => $tickets,
        ]);
    }
}
