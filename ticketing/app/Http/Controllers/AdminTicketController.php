<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{

    public function index() {
        $tickets = Ticket::with('employee.user', 'technician.user')
        ->whereYear('created_at', Carbon::now()->year)
        ->whereMonth('created_at', Carbon::now()->month)
        ->get();
        return inertia('Admin/Tickets/Index', [
            'tickets' => $tickets,
        ]);
    }
    public function create() {
        return inertia('Admin/Tickets/Create');
    }
}
