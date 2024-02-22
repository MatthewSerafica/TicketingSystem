<?php

namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\Technician;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TechnicianTicketController extends Controller
{
    public function index(Request $request) {
        $tickets = Ticket::query()
            ->with('employee.user', 'technician.user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('ticket_number', 'like', '%' . $search . '%')
                    ->where('status', 'like', '%' . $search . '%')
                    ->orWhereHas('employee.user', function ($subquery) use ($search) {
                        $subquery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('employee', function ($subquery) use ($search) {
                        $subquery->where('department', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('technician.user', function ($subquery) use ($search) {
                        $subquery->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderBy('ticket_number')
            ->get();

        $filters = $request->only(['search']);
        $technicians = Technician::with('user')->get();
        return inertia('Technician/Tickets/Index', [
            'tickets' => $tickets,
            'technicians' => $technicians,
            'filters' => $filters,
        ]);
    }
    
    public function create() {
        $technicians = Technician::with('user')->get();
        $employees = Employee::with('user')->get();
        return inertia('Technician/Tickets/Create', [
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
        $employee->update(['made_ticket' => $employee->made_ticket + 1]);

        return redirect()->to('/technician/tickets')->with('success', 'Ticket Created');
    }

    public function status(Request $request, $ticket_id)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();

        $ticket->status = $request->status;
        $ticket->save();
    }




}
