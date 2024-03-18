<?php

namespace App\Http\Controllers;

use App\Models\AssignedTickets;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\TicketsAssigned;
use App\Notifications\TicketMade;
use App\Notifications\UpdateTicketStatus;
use App\Notifications\UpdateTicketTechnician;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Assign;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::query()
            ->with('employee.user', 'assigned.technician.user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('ticket_number', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('rr_no', 'like', '%' . $search . '%')
                    ->orWhere('ms_no', 'like', '%' . $search . '%')
                    ->orWhere('rs_no', 'like', '%' . $search . '%')
                    ->orWhere('sr_no', 'like', '%' . $search . '%')
                    ->orWhere('service', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%')
                    ->orWhereHas('employee.user', function ($subquery) use ($search) {
                        $subquery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('employee', function ($subquery) use ($search) {
                        $subquery->where('department', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('employee', function ($subquery) use ($search) {
                        $subquery->where('office', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('technician1.user', function ($subquery) use ($search) {
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
            ->orderBy(
                $request->input('sort', 'ticket_number'),
                $request->input('direction', 'asc')
            )
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


    public function create(Request $request)
    {
        $technicians = Technician::with('user')->get();
        $services = Service::all();
        $searchQuery = $request->input('search');

        $employees = Employee::with('user')
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->where(function ($subquery) use ($searchQuery) {
                    $subquery->whereHas('user', function ($nameQuery) use ($searchQuery) {
                        $nameQuery->where('name', 'like', '%' . $searchQuery . '%');
                    })
                        ->orWhere('department', 'like', '%' . $searchQuery . '%')
                        ->orWhere('office', 'like', '%' . $searchQuery . '%');
                });
            })
            ->get();

        $filter = $request->only(['search']);
        return inertia('Admin/Tickets/Create', [
            'technicians' => $technicians,
            'employees' => $employees,
            'services' => $services,
            'filters' => $filter,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'employee' => 'required',
            'issue' => 'required',
            'service' => 'required',
            'rr_no' => 'nullable|numeric',
            'rs_no' => 'nullable|numeric',
        ]);

        if ($request->filled('rs_no')) {
            if (!is_numeric($request->rs_no)) {
                return redirect()->back()->with('error', 'RS number must be numeric.');
            }
        }

        $employee = Employee::where('employee_id', $request->employee)->firstOrFail();
        if ($employee->made_ticket >= 5) {
            return redirect()->back()->with('error', 'You have already made the maximum number of tickets.');
        }

        $ticketData = [
            'rs_no' => $request->rs_no,
            'employee' => $request->employee,
            'issue' => $request->issue,
            'description' => $request->description,
            'service' => $request->service,
            'status' => 'Pending',
        ];

        $ticket = Ticket::create($ticketData);


        $assignedTechnicians = $request->technicians;

        foreach ($assignedTechnicians as $technicianId) {
            AssignedTickets::create([
                'ticket_number' => $ticket->ticket_number,
                'technician' => $technicianId,
            ]);

            $this->sendTechnicianNotification($technicianId, $ticket);
        }

        $employee->update(['made_ticket' => $employee->made_ticket + 1]);
        $employee->user->notify(
            new TicketMade($ticket)
        );

        foreach ([$request->technician1, $request->technician2, $request->technician3] as $technicianId) {
            if ($technicianId) {
                $this->sendTechnicianNotification($technicianId, $ticket);
            }
        }

        return redirect()->to('/admin/tickets')->with('success', 'Ticket Created')->with('message', 'All assigned technicians are notified.');
    }

    protected function sendTechnicianNotification($technicianId, $ticket)
    {
        $technician = Technician::where('technician_id', $technicianId)->firstOrFail();
        $technician->user->notify(new UpdateTicketTechnician($ticket));
    }

    public function update(Request $request, $field, $id)
    {
        $request->validate([
            $field => 'nullable',
        ]);

        $ticket = Ticket::where('ticket_number', $id)->first();

        if ($field === 'technician') {
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

        if (!$request->$field) {
            return redirect()->back()->with('success', 'Receiving Report Update!')->with('message', $input . ' is now removed from Ticket #' . $ticket->ticket_number);
        } else {
            return redirect()->back()->with('success', 'Receiving Report Update!')->with('message', $input . ' ' . $request->$field . ' is now assigned to Ticket #' . $ticket->ticket_number);
        }
    }

    private function updateTechnician(Request $request, $ticket)
    {
        $request->validate([
            'technician' => 'required',
            'ticket_number' => 'required',
        ]);

        if ($ticket->status == 'New' && $request->technician_id) {
            $ticket->status = 'Pending';
        }
        $ticket->save();

        $assign = [
            'ticket_number' => $request->ticket_number,
            'technician' => $request->technician,
        ];

        AssignedTickets::create($assign);

        $technician = Technician::find($request->technician);

        if ($technician) {
            $technician->user->notify(
                new UpdateTicketTechnician($ticket)
            );
        }
        return redirect()->back()->with('success', 'Technician Update!')->with('message', 'Technician ' . $technician->user->name . ' is now assigned to Ticket #' . $ticket->ticket_number);
    }

    // status update
    public function status(Request $request, $ticket_id)
    {
        $request->validate([
            'status' => 'required',
            'old_status' => 'required',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->firstOrFail();
        $employee = Employee::find($ticket->employee);
        $technicians = Technician::whereIn('technician_id', [$ticket->technician1, $ticket->technician2, $ticket->technician3])->get();

        $ticket->status = $request->status;

        if ($ticket->status == 'Resolved' && $request->old_status != 'Resolved') {
            $this->resolveTicket($ticket, $employee, $technicians);
        } elseif ($request->old_status == 'Resolved' && $ticket->status != 'Resolved') {
            $this->revertResolution($ticket, $employee, $technicians);
        } else {
            $ticket->resolved_at = null;
        }

        $employee->user->notify(new UpdateTicketStatus($ticket));
        $ticket->save();

        return redirect()->back()
            ->with('success', 'Status Update!')
            ->with('message', 'Ticket No. ' . $ticket->ticket_number . ' is now ' . $request->status);
    }

    private function resolveTicket($ticket, $employee, $technicians)
    {
        $ticket->resolved_at = now();
        $employee->update(['made_ticket' => max(0, $employee->made_ticket - 1)]);

        foreach ($technicians as $technician) {
            $technician->update(['tickets_resolved' => $technician->tickets_resolved + 1]);
        }
    }

    private function revertResolution($ticket, $employee, $technicians)
    {
        $ticket->resolved_at = null;
        $employee->update(['made_ticket' => $employee->made_ticket + 1]);

        $totalResolved = $technicians->sum('tickets_resolved');
        $decrementValue = ($totalResolved > 0) ? 1 : 0;

        foreach ($technicians as $technician) {
            $technician->update(['tickets_resolved' => max(0, $technician->tickets_resolved - $decrementValue)]);
        }
    }
    //status update end

    public function service(Request $request, $ticket_id)
    {
        $request->validate([
            'service' => 'required',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();

        $ticket->service = $request->service;
        if ($ticket->status == 'New') {
            $ticket->status = 'Pending';
        }
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
        if ($ticket->status == 'New') {
            $ticket->status = 'Pending';
        }
        $ticket->save();
        return redirect()->back()->with('success', 'Receiving Report Update!')->with('message', 'Ticket No. ' . $ticket->ticket_number . ' is now set as ' . $request->complexity);
    }

    public function replace(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required',
            'technician' => 'required',
            'old' => 'required',
        ]);

        $assigned = AssignedTickets::where(['ticket_number' => $request->ticket_number, 'technician' => $request->old])->first();

        $assigned->technician = $request->technician;
        $assigned->save();

        return redirect()->back()->with('success', 'Ticket Updated!')->with('message', 'Technician has been replaced!');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required',
            'technician' => 'required',
        ]);

        $assigned = AssignedTickets::where(['ticket_number' => $request->ticket_number, 'technician' => $request->technician])->first();

        if ($assigned) {
            $assigned->delete();
            return redirect()->back()->with('success', 'Technician removed!')->with('message', 'Technician successfully removed from Ticket #' . $request->ticket_number);
        }
    }
}
