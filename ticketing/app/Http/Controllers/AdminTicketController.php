<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Service;
use App\Models\ServiceReport;
use App\Models\Technician;
use App\Models\Ticket;
use App\Notifications\UpdateTicketStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{

    public function index(Request $request)
    {
        $tickets = Ticket::query()
            ->with('employee.user', 'technician.user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('ticket_number', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('rr_no', 'like', '%' . $search . '%')
                    ->orWhere('ms_no', 'like', '%' . $search . '%')
                    ->orWhere('rs_no', 'like', '%' . $search . '%')
                    ->orWhere('sr_no', 'like', '%' . $search . '%')
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

        $filter = $request->only(['search']);
        $technicians = Technician::with('user')->get();
        $services = Service::all();
        return inertia('Admin/Tickets/Index', [
            'tickets' => $tickets,
            'technicians' => $technicians,
            'filters' => $filter,
            'services' => $services,
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
            'rs_no' => 'nullable',
        ]);
        $employee = Employee::where('employee_id', $request->employee)->firstOrFail();
        if ($employee->made_ticket >= 5) {
            return redirect()->back()->with('error', 'You have already made the max number of tickets.');
        }

        $ticketData = [
            'rs_no' => $request->rs_no,
            'employee' => $request->employee,
            'technician' => $request->technician,
            'issue' => $request->issue,
            'description' => $request->description,
            'service' => $request->service,
            'status' => 'Pending',
        ];

        Ticket::create($ticketData);
        $employee->update(['made_ticket' => $employee->made_ticket + 1]);

        return redirect()->to('/admin/tickets')->with('success', 'Ticket Created');
    }

    public function status(Request $request, $ticket_id)
    {
        $request->validate([
            'status' => 'required',
            'old_status' => 'required',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();
        $employee = Employee::where('employee_id', $ticket->employee)->with('user')->first();
        $ticket->status = $request->status;
        if ($ticket->status == 'Resolved' && $request->old_status != 'Resolved') {
            if ($employee->made_ticket > 0) {
                $ticket->resolved_at = now();
                $employee->update(['made_ticket' => $employee->made_ticket - 1]);
            } else {
                $ticket->resolved_at = now();
                $employee->update(['made_ticket' => 0]);
            }
        } else if ($request->old_status == 'Resolved' && $ticket->status != 'Resolved') {
            $ticket->resolved_at = null;
            $employee->update(['made_ticket' => $employee->made_ticket + 1]);
        } else {
            $ticket->resolved_at = null;
        }
        $employee->user->notify(
            new UpdateTicketStatus($ticket)
        );
        $ticket->save();

        return redirect()->back()->with('success', 'Status Update')->with('message', 'Ticket No. ' . $ticket->ticket_number . ' is now ' . $request->status);;
    }

    public function technician(Request $request, $ticket_id)
    {
        $request->validate([
            'technician_id' => 'required',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();

        $technician = Technician::findOrFail($request->technician_id);
        $ticket->technician = $request->technician_id;
        $ticket->save();
        return redirect()->back()->with('success', 'Technician Update')->with('message', $technician->user->name . ' is now assigned to Ticket #' . $ticket->ticket_number);
    }

    public function service(Request $request, $ticket_id)
    {
        $request->validate([
            'service' => 'required',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();

        $ticket->service = $request->service;
        $ticket->save();
        return redirect()->back()->with('success', 'Service Update')->with('message', $request->service . ' service is now assigned to Ticket No. ' . $ticket->ticket_number);
    }

    public function rr(Request $request, $ticket_id)
    {
        $request->validate([
            'rr_no' => 'nullable',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();

        $ticket->rr_no = $request->rr_no;
        $ticket->save();
        return redirect()->back()->with('success', 'Receiving Report Update')->with('message', 'RR No. ' . $request->rr_no . ' is now assigned to Ticket No. ' . $ticket->ticket_number);
    }
    public function ms(Request $request, $ticket_id)
    {
        $request->validate([
            'ms_no' => 'nullable',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();

        $ticket->ms_no = $request->ms_no;
        $ticket->save();
        return redirect()->back()->with('success', 'Receiving Report Update')->with('message', 'MS No. ' . $request->ms_no . ' is now assigned to Ticket No. ' . $ticket->ticket_number);
    }
    public function rs(Request $request, $ticket_id)
    {
        $request->validate([
            'rs_no' => 'nullable',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();

        $ticket->rs_no = $request->rs_no;
        $ticket->save();
        return redirect()->back()->with('success', 'Receiving Report Update')->with('message', 'RS No. ' . $request->rs_no . ' is now assigned to Ticket No. ' . $ticket->ticket_number);
    }
    public function sr(Request $request, $ticket_id)
    {
        $request->validate([
            'sr_no' => 'nullable',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();

        $ticket->sr_no = $request->sr_no;
        $ticket->save();
        return redirect()->back()->with('success', 'Receiving Report Update')->with('message', 'SR No. ' . $request->sr_no . ' is now assigned to Ticket No. ' . $ticket->ticket_number);
    }

    public function remark(Request $request, $ticket_id)
    {
        $request->validate([
            'remark' => 'nullable',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();

        $ticket->remarks = $request->remark;
        $ticket->save();
        return redirect()->back()->with('success', 'Receiving Report Update')->with('message', 'Successfuly added remarks to Ticket No. ' . $ticket->ticket_number);
    }

    public function complexity(Request $request, $ticket_id)
    {
        $request->validate([
            'complexity' => 'nullable',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();

        $ticket->complexity = $request->complexity;
        $ticket->save();
        return redirect()->back()->with('success', 'Receiving Report Update')->with('message', 'Ticket No. ' . $ticket->ticket_number . ' is now set as ' . $request->complexity);
    }
}
