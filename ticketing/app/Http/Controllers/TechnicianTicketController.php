<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Technician;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TechnicianTicketController extends Controller
{
    public function index(Request $request)
    {
        $user_id = auth()->id();
        $technician = Technician::where('user_id', $user_id)->first();

        $tickets = Ticket::query()
            ->with('employee.user', 'technician1.user', 'technician2.user', 'technician3.user')
            ->where('technician1', $technician->technician_id)
            ->orWhere('technician2', $technician->technician_id)
            ->orWhere('technician3', $technician->technician_id)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($subquery) use ($search) {
                    $subquery->where('ticket_number', 'like', '%' . $search . '%')
                        ->orWhere('status', 'like', '%' . $search . '%')
                        ->orWhere('sr_no', 'like', '%' . $search . '%')
                        ->orWhereHas('employee.user', function ($subquery) use ($search) {
                            $subquery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('employee', function ($subquery) use ($search) {
                            $subquery->where('department', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('employee', function ($subquery) use ($search) {
                            $subquery->where('office', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('technician.user', function ($subquery) use ($search) {
                            $subquery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($request->filled('filterTickets'), function ($query) use ($request) {
                $ticketFilter = $request->input('filterTickets');
                if ($ticketFilter === 'new') {
                    $query->where('status', 'like', '%' . $ticketFilter . '%');
                } elseif ($ticketFilter === 'resolved') {
                    $query->where('status', 'like', '%' . $ticketFilter . '%');
                } elseif ($ticketFilter === 'pending') {
                    $query->where('status', 'like', '%' . $ticketFilter . '%');
                } elseif ($ticketFilter === 'ongoing') {
                    $query->where('status', 'like', '%' . $ticketFilter . '%');
                }
            })
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderBy('ticket_number')
            ->paginate(10);

        $filters = $request->only(['search']);
        $technicians = Technician::with('user')->get();
        return inertia('Technician/Tickets/Index', [
            'tickets' => $tickets,
            'technicians' => $technicians,
            'filters' => $filters,
        ]);
    }


    public function create()
    {
        $employees = Employee::with('user')->get();
        return inertia('Technician/Tickets/Create', [
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
            'rs_no' => 'nullable',
        ]);
        $technician = Technician::where('user_id', $request->technician)->firstOrFail();
        $employee = Employee::where('employee_id', $request->employee)->firstOrFail();

        if ($employee->made_ticket >= 5) {
            return redirect()->back()->with('error', 'You have already made the max number of tickets.');
        }

        $ticketData = [
            'rs_no' => $request->rs_no,
            'employee' => $request->employee,
            'technician' => $technician->technician_id,
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

    public function update(Request $request, $field, $id)
    {
        $request->validate([
            $field => 'nullable',
        ]);

        $ticket = Ticket::where('ticket_number', $id)->first();
        
        $ticket->$field = $request->$field;
        
        
        if ($ticket->sr_no !== null) {
            // If the SR number is present, update the resolved_at and status
            $ticket->resolved_at = now();
            $ticket->status = 'Resolved';
        }
        $ticket->save();
    }


}
