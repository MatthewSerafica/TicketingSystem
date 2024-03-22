<?php

namespace App\Http\Controllers;

use App\Models\AssignedTickets;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketMade;
use App\Models\ServiceReport;
use App\Notifications\UpdateTicketStatus;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TechnicianTicketController extends Controller
{
    public function index(Request $request)
    {
        $user_id = auth()->id();
        $technician = Technician::where('user_id', $user_id)->with('user')->first();

        $query = Ticket::query()
            ->with('employee.user', 'assigned.technician.user')
            ->whereHas('assigned.technician.user', function ($query) use ($technician) {
                $query->where('id', $technician->user->id);
            })
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderBy($request->input('sort', 'ticket_number'), $request->input('direction', 'asc'));

        $filter = $request->only(['search', 'filterTickets', 'all', 'new', 'pending', 'ongoing', 'resolved']);

        if ($request->filled('search')) {
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
        }

        if ($request->filled('filterTickets')) {
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
        }

        $tickets = $query->paginate(10);

        $tickets->appends($filter);

        $request->session()->put('filter', $filter);

        $filters = $request->only(['search']);
        $technicians = Technician::with('user')
            ->where('tickets_assigned', '!=', 5)
            ->get();

        $services = Service::all();
        return inertia('Technician/Tickets/Index', [
            'tickets' => $tickets,
            'technicians' => $technicians,
            'services' => $services,
            'filters' => $filters,
        ]);
    }



    public function create(Request $request)
    {
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
        return inertia('Technician/Tickets/Create', [
            'employees' => $employees,
            'filters' => $filter,
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'complexity' => 'required',
                'description' => 'required',
                'employee' => 'required',
                'issue' => 'required',
                'service' => 'required',
                'user' => 'required',
                'rs_no' => 'nullable|numeric',
                'assignToSelf' => 'nullable',
            ]);
            $technician = Technician::where('user_id', $request->user)->firstOrFail();
            $employee = Employee::where('employee_id', $request->employee)->firstOrFail();

            if ($employee->made_ticket >= 5 || $technician->assigned_ticket >= 5) {
                DB::rollBack();
                return redirect()->back()->with('error', 'You have already made the max number of tickets.');
            }

            $ticketData = [
                'complexity' => $request->complexity,
                'rs_no' => $request->rs_no,
                'employee' => $request->employee,
                'issue' => $request->issue,
                'description' => $request->description,
                'service' => $request->service,
                'status' => 'Pending',
            ];

            $ticket = Ticket::create($ticketData);
            $employee->update(['made_ticket' => $employee->made_ticket + 1]);

            if ($request->assign_to_self) {
                AssignedTickets::create([
                    'ticket_number' => $ticket->ticket_number,
                    'technician' => $technician->technician_id,
                ]);
                $technician->update(['tickets_assigned' => $technician->tickets_assigned + 1]);
            }
            $admins = User::where('user_type', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(
                    new TicketMade($ticket)
                );
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->to('/technician/tickets')->with('error', 'Invalid Ticket')->with('message', $e->getMessage());
        }
        return redirect()->to('/technician/tickets')->with('success', 'Ticket Created')->with('message', 'Admin is notified of the ticket!');
    }

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
        return redirect()->back()->with('success', 'Ticket Updated!')->with('message', $request->service . ' service is now assigned to Ticket No. ' . $ticket->ticket_number);
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
        return redirect()->back()->with('success', 'Ticket Updated!')->with('message', 'Ticket No. ' . $ticket->ticket_number . ' is now set as ' . $request->complexity);
    }

    public function status(Request $request, $ticket_id)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $ticket = Ticket::where('ticket_number', $ticket_id)->first();
        $employee = Employee::find($ticket->employee);

        $ticket->status = $request->status;
        $ticket->save();

        $employee->user->notify(new UpdateTicketStatus($ticket));

        return redirect()->back()
            ->with('success', 'Ticket Updated!')
            ->with('message', 'Ticket No. ' . $ticket->ticket_number . ' is now ' . $request->status);
    }

    public function update(Request $request, $field, $id)
    {
        try {
            $request->validate([
                $field => 'nullable',
            ]);

            $ticket = Ticket::where('ticket_number', $id)->first();

            $ticket->$field = $request->$field;

            // If the SR number is present in the request, update it in the tickets table
            if ($field === 'sr_no') {
                $ticket->save();
                $serviceReport = ServiceReport::where('ticket_number', $ticket->ticket_number)->first();
                if ($serviceReport) {
                    $serviceReport->service_id = $ticket->$field;
                    $serviceReport->save();
                } else {
                    // If no service report exists, create a new one with the updated SR number
                    $serviceData = [
                        'service_id' => $ticket->$field,
                        'ticket_number' => $ticket->ticket_number,
                        'date_done' => now(),
                        'issue' => $ticket->description,

                    ];
                    ServiceReport::create($serviceData);
                }
            }

            // Check if the SR number is present, and update resolved_at and status accordingly
            if ($ticket->sr_no !== null) {
                $ticket->resolved_at = now();
                $ticket->status = 'Resolved';
            }

            $ticket->save();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred!')->with('message', $e->getMessage());
        }

        if ($field == 'sr_no') {
            return redirect()->to('/technician/service-report/create');
        } else {
            return redirect()->back()->with('success', 'Ticket Updated!')->with('message', 'Remarks have been updated for Ticket #' . $ticket->ticket_number);
        }
    }
}
