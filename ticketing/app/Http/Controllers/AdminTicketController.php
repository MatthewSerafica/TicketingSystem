<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Service;
use App\Models\ServiceReport;
use App\Models\Technician;
use App\Models\Ticket;
use App\Notifications\UpdateTicketStatus;
use App\Notifications\UpdateTicketTechnician;
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
            'technician' => 'nullable',
            'rr_no' => 'nullable|numeric',
            'rs_no' => 'nullable|numeric',
            'ms_no' => 'nullable|numeric',
            'sr_no' => 'nullable|numeric',
        ]);


        if ($request->filled('rr_no') && is_numeric($request->rr_no)) {
            $ticketData['rr_no'] = $request->rr_no;
        } elseif ($request->filled('rr_no') && !is_numeric($request->rr_no)) {
            return redirect()->back()->with('error', 'RR number must be numeric.');
        }

        if ($request->filled('ms_no')) {
            if (!is_numeric($request->ms_no)) {
                return redirect()->back()->with('error', 'MS number must be numeric.');
            }
        }

        if ($request->filled('rs_no')) {
            if (!is_numeric($request->rs_no)) {
                return redirect()->back()->with('error', 'RS number must be numeric.');
            }
        }

        if ($request->filled('sr_no')) {
            if (!is_numeric($request->sr_no)) {
                return redirect()->back()->with('error', 'SR number must be numeric.');
            }
        }

        $employee = Employee::where('employee_id', $request->employee)->firstOrFail();
        if ($employee->made_ticket >= 5) {
            return redirect()->back()->with('error', 'You have already made the maximum number of tickets.');
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

        // Check if rr_no is filled and numeric before adding to ticketData
        if ($request->filled('rr_no') && is_numeric($request->rr_no)) {
            $ticketData['rr_no'] = $request->rr_no;
        }

        Ticket::create($ticketData);
        $employee->update(['made_ticket' => $employee->made_ticket + 1]);

        return redirect()->to('/admin/tickets')->with('success', 'Ticket Created');
    }

    public function update(Request $request, $field, $id)
    {
        $request->validate([
            $field => 'nullable',
        ]);

        $ticket = Ticket::where('ticket_number', $id)->first();

        if ($field === 'technician_id') {
            return $this->updateTechnician($request, $ticket);
        } else {
            return $this->updateField($request, $field, $ticket);
        }
    }

    private function updateField(Request $request, $field, $ticket)
    {
        $ticket->$field = $request->$field;
        $ticket->save();

        $fieldMappings = [
            'rr_no' => 'RR No.',
            'ms_no' => 'MS No.',
            'rs_no' => 'RS No.',
            'sr_no' => 'SR No.',
            'remarks' => 'Remark',
        ];

        $input = $fieldMappings[$field] ?? $field;

        return redirect()->back()->with('success', 'Receiving Report Update!')->with('message', $input . ' ' . $request->$field . ' is now assigned to Ticket #' . $ticket->ticket_number);
    }

    private function updateTechnician(Request $request, $ticket)
    {
        $request->validate([
            'technician_id' => 'required',
        ]);

        $technician = Technician::findOrFail($request->technician_id);
        $old = Technician::find($ticket->technician);

        if ($old) {
            if ($old->tickets_assigned > 0) {
                $old->update(['tickets_assigned' => $old->tickets_assigned - 1]);
                $technician->update(['tickets_assigned' => $technician->tickets_assigned + 1]);
            } else {
                $old->update(['tickets_assigned' => 0]);
                $technician->update(['tickets_assigned' => $technician->tickets_assigned + 1]);
            }
        } else {
            $technician->update(['tickets_assigned' => $technician->tickets_assigned + 1]);
        }

        $ticket->technician = $request->technician_id;
        $ticket->save();

        $technician->user->notify(
            new UpdateTicketTechnician($ticket)
        );

        return redirect()->back()->with('success', 'Technician Update!')->with('message', 'Technician ' . $technician->user->name . ' is now assigned to Ticket #' . $ticket->ticket_number);
    }

    public function status(Request $request, $ticket_id)
    {
        $request->validate([
            'status' => 'required',
            'old_status' => 'required',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();
        $employee = Employee::where('employee_id', $ticket->employee)->with('user')->first();
        $technician = Technician::where('technician_id', $ticket->technician)->with('user')->first();

        $ticket->status = $request->status;
        if ($ticket->status == 'Resolved' && $request->old_status != 'Resolved') {
            if ($employee->made_ticket > 0) {
                $ticket->resolved_at = now();
                $employee->update(['made_ticket' => $employee->made_ticket - 1]);
                $technician->update(['tickets_resolved' => $technician->tickets_resolved + 1]);
            } else {
                $ticket->resolved_at = now();
                $employee->update(['made_ticket' => 0]);
                $technician->update(['tickets_resolved' => $technician->tickets_resolved + 1]);
            }
        } else if ($request->old_status == 'Resolved' && $ticket->status != 'Resolved') {
            $ticket->resolved_at = null;
            $employee->update(['made_ticket' => $employee->made_ticket + 1]);
            if ($technician->tickets_resolved > 0) {
                $technician->update(['tickets_resolved' => $technician->tickets_resolved - 1]);
            } else {
                $technician->update(['tickets_resolved' => 0]);
            }
        } else {
            $ticket->resolved_at = null;
        }
        $employee->user->notify(
            new UpdateTicketStatus($ticket)
        );
        $ticket->save();

        return redirect()->back()->with('success', 'Status Update!')->with('message', 'Ticket No. ' . $ticket->ticket_number . ' is now ' . $request->status);
    }

    public function technician(Request $request, $ticket_id)
    {
        $request->validate([
            'technician_id' => 'required',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();
        $technician = Technician::findOrFail($request->technician_id);
        $old = Technician::findOrFail($ticket->technician_id);

        if ($old) {
            if ($old->tickets_assigned > 0) {
                $old->tickets_assigned = $old->tickets_assigned - 1;
                $technician->tickets_assigned = $technician->tickets_assigned + 1;
            } else {
                $old->tickets_assigned = 0;
                $technician->tickets_assigned = $technician->tickets_assigned + 1;
            }
        } else {
            $technician->tickets_assigned = $technician->tickets_assigned + 1;
        }

        $ticket->technician = $request->technician_id;
        $ticket->save();
        return redirect()->back()->with('success', 'Technician Update!')->with('message', $technician->user->name . ' is now assigned to Ticket #' . $ticket->ticket_number);
    }

    public function service(Request $request, $ticket_id)
    {
        $request->validate([
            'service' => 'required',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();

        $ticket->service = $request->service;
        $ticket->save();
        return redirect()->back()->with('success', 'Service Update!')->with('message', $request->service . ' service is now assigned to Ticket No. ' . $ticket->ticket_number);
    }

    public function remark(Request $request, $ticket_id)
    {
        $request->validate([
            'remark' => 'nullable',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();

        $ticket->remarks = $request->remark;
        $ticket->save();
        return redirect()->back()->with('success', 'Receiving Report Update!')->with('message', 'Successfuly added remarks to Ticket No. ' . $ticket->ticket_number);
    }

    public function complexity(Request $request, $ticket_id)
    {
        $request->validate([
            'complexity' => 'nullable',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();

        $ticket->complexity = $request->complexity;
        $ticket->save();
        return redirect()->back()->with('success', 'Receiving Report Update!')->with('message', 'Ticket No. ' . $ticket->ticket_number . ' is now set as ' . $request->complexity);
    }
}
