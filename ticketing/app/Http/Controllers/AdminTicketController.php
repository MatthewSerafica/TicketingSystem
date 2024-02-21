<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Technician;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{

    public function index()
    {
        $tickets = Ticket::with('employee.user', 'technician.user')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->get();
        $technicians = Technician::with('user')->get();
        return inertia('Admin/Tickets/Index', [
            'tickets' => $tickets,
            'technicians' => $technicians,
        ]);
    }
    public function create()
    {
        $technicians = Technician::with('user')->get();
        $employees = Employee::with('user')->get();
        return inertia('Admin/Tickets/Create', [
            'technicians' => $technicians,
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'description' => 'required',
            'employee' => 'required',
            'issue' => 'required',
            'service' => 'required',
            'technician' => 'required',
        ]);
        $employee = Employee::where('employee_id', $request->employee)->firstOrFail();
        if ($employee->made_ticket >= 5) {
            return redirect()->back()->with('error', 'You have already made the max number of tickets.');
        }

        $ticketData = [
            'employee' => $request->employee,
            'technician' => $request->technician,
            'issue' => $request->issue,
            'description' => $request->description,
            'service' => $request->service,
            'status' => 'Pending',
        ];

        Ticket::create($ticketData);
        $employee->update(['made_ticket'=> $employee->made_ticket + 1]);

        return redirect()->to('/admin/tickets')->with('success', 'Ticket Created');
    }
}
