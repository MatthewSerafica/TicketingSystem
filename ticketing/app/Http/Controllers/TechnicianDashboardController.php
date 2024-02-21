<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Technician;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TechnicianDashboardController extends Controller
{
    public function index() {
        $user = Auth::user();
        $employee = Technician::where('user_id', $user->id)->firstOrFail();
        $tickets = Ticket::where('employee')->with('employee.user', 'technician.user')->whereDate('created_at',today())->orderByDesc('created_at')->take(3)->get();
        return inertia('Technician/Dashboard/Index');
    }
}
