<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketMade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeTicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();
        $ticket = Ticket::where('employee', $employee->employee_id)->with('employee.user', 'technician.user')->get();
        return inertia('Employee/Index', [
            'tickets' => $ticket,
        ]);
    }
    public function create()
    {
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

        $employee = Employee::where('user_id', $request->employee)->firstOrFail();
        if ($employee->made_ticket >= 5) {
            return redirect()->back()->with('error', 'You have already made the max number of tickets.');
        }

        $ticketData = [
            'employee' => $employee->employee_id,
            'issue' => $request->issue,
            'description' => $request->description,
            'service' => $request->service,
            'status' => 'New',
        ];

        $ticket = Ticket::create($ticketData);
        $employee->update(['made_ticket' => $employee->made_ticket + 1]);
        $admins = User::where('user_type', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(
                new TicketMade($ticket)
            );
        }

        return redirect()->to('/employee')->with('success', 'Ticket Created');
    }
}
