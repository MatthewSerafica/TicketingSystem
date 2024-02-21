<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeTicketController extends Controller
{
    public function index() {
        return inertia('Employee/Index');
    }
    public function create() {
        return inertia('Employee/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'employee' => 'required',
            'issue' => 'required',
            'service' => 'required',
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

        return redirect()->to('/employee')->with('success', 'Ticket Created');
    }
}
