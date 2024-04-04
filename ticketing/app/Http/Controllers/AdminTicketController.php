<?php

namespace App\Http\Controllers;

use App\Models\AssignedTickets;
use App\Models\Employee;
use App\Models\HistoryNumber;
use App\Models\Service;
use App\Models\Technician;
use App\Models\Ticket;
use App\Notifications\TicketMade;
use App\Notifications\UpdateTechnicianReplace;
use App\Notifications\UpdateTicketStatus;
use App\Notifications\UpdateTicketTechnician;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query()->with('employee.user', 'assigned.technician.user')->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->orderBy($request->input('sort', 'ticket_number'), $request->input('direction', 'asc'));

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

        $technicians = Technician::with('user')
            ->where('tickets_assigned', '!=', 5)
            ->get();

        $services = Service::all();
        $latest_rs = HistoryNumber::select('rs_no')->whereNotNull('rs_no')->orderByDesc('rs_no')->first();
        $latest_ms = HistoryNumber::select('ms_no')->whereNotNull('ms_no')->orderByDesc('ms_no')->first();
        $latest_rr = HistoryNumber::select('rr_no')->whereNotNull('rr_no')->orderByDesc('rr_no')->first();
        return inertia('Admin/Tickets/Index', [
            'tickets' => $tickets,
            'technicians' => $technicians,
            'filters' => $filter,
            'services' => $services,
            'rs' => $latest_rs,
            'ms' => $latest_ms,
            'rr' => $latest_rr,
        ]);
    }


    public function create(Request $request)
    {
        $latest = HistoryNumber::whereNotNull('rs_no')->orderByDesc('rs_no')->first();
        $new = $latest ? $this->increment($latest->ticket_number, $latest->rs_no) : '0001';
        $technicians = Technician::with('user')
            ->where('tickets_assigned', '!=', 5)
            ->get();
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
            'new_rs' => $new,
        ]);
    }

    private function increment($id, $rs_no)
    {
        $exists = HistoryNumber::where('ticket_number', $id)->exists();
        if ($exists) {
            $latest =  HistoryNumber::whereNotNull('rs_no')->orderByDesc('rs_no')->first();
            if ($latest) {
                $numeric_part = (int)$latest->rs_no;
                $new = $numeric_part + 1;
                $new_numeric = sprintf('%04d', $new);
            } else {
                $new_numeric = $rs_no;
            }
        } else {
            $new_numeric = $rs_no;
        }

        return $new_numeric;
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'description' => 'required',
                'employee' => 'required',
                'issue' => 'required',
                'service' => 'required',
                'rs_no' => 'nullable|numeric',
            ]);

            if ($request->filled('rs_no')) {
                if (!is_numeric($request->rs_no)) {
                    return redirect()->back()->with('error', 'RS number must be numeric.');
                }
            }

            $employee = Employee::where('employee_id', $request->employee)->firstOrFail();
            if ($employee->made_ticket >= 5) {
                return redirect()->back()->with('error', 'Error Creating Ticket')->with('message', 'Employee has already met ticket limit!');
            }


            $ticket_data = [
                'rs_no' => $request->rs_no,
                'employee' => $request->employee,
                'issue' => $request->issue,
                'description' => $request->description,
                'service' => $request->service,
                'status' => 'Pending',
            ];

            $ticket = Ticket::create($ticket_data);

            $history_data = [
                'ticket_number' => $ticket->ticket_number,
                'rs_no' => $request->rs_no,
            ];

            HistoryNumber::create($history_data);

            $assigned_technicians = $request->technicians;

            foreach ($assigned_technicians as $technician_id) {
                AssignedTickets::create([
                    'ticket_number' => $ticket->ticket_number,
                    'technician' => $technician_id,
                ]);

                $this->sendTechnicianNotification($technician_id, $ticket);
            }

            $employee->update(['made_ticket' => $employee->made_ticket + 1]);
            $employee->user->notify(
                new TicketMade($ticket)
            );
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->to('/admin/tickets')->with('error', 'An error occurred!')->with('message', $e->getMessage());
        }
        return redirect()->to('/admin/tickets')->with('success', 'Ticket Created')->with('message', 'All assigned technicians are notified!');
    }

    protected function sendTechnicianNotification($technician_id, $ticket)
    {
        $technician = Technician::where('technician_id', $technician_id)->firstOrFail();
        $technician->update(['tickets_assigned' => $technician->tickets_assigned + 1]);
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

        if ($field !== 'remarks') {
            $history = HistoryNumber::where('ticket_number', $ticket->ticket_number)->first();
            $history->$field = $request->$field;
            $history->save();
        }

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
        try {
            DB::beginTransaction();
            $request->validate([
                'technician' => 'required',
                'ticket_number' => 'required',
            ]);
            $technician = Technician::find($request->technician);

            if ($technician->tickets_assigned == 5) {
                DB::rollBack();
                return redirect()->back()->with('error', 'An error occurred!')->with('message', 'Technician ' . $technician->user->name . ' has 5 tickets assigned to them already!');
            } else {
                $technician->tickets_assigned = $technician->tickets_assigned + 1;
                $technician->save();
            }

            if ($ticket->status == 'New' && $request->technician_id) {
                $ticket->status = 'Pending';
            }
            $ticket->save();

            $assign = [
                'ticket_number' => $request->ticket_number,
                'technician' => $request->technician,
            ];

            AssignedTickets::create($assign);


            if ($technician) {
                $technician->user->notify(
                    new UpdateTicketTechnician($ticket)
                );
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred!');
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
        $assigned = AssignedTickets::where('ticket_number', $ticket->ticket_number)->get();

        $technicians = collect([]);
        foreach ($assigned as $assign) {
            $technicians = $technicians->merge(Technician::where('technician_id', $assign->technician)->get());
        }

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
            $technician->update([
                'tickets_resolved' => $technician->tickets_resolved + 1,
                'tickets_assigned' => max(0, $technician->tickets_assigned - 1)
            ]);
        }
    }

    private function revertResolution($ticket, $employee, $technicians)
    {
        $ticket->resolved_at = null;
        $employee->update(['made_ticket' => $employee->made_ticket + 1]);

        $totalResolved = $technicians->sum('tickets_resolved');
        $decrementValue = ($totalResolved > 0) ? 1 : 0;

        foreach ($technicians as $technician) {
            $technician->update([
                'tickets_resolved' => max(0, $technician->tickets_resolved - $decrementValue),
                'tickets_assigned' => $technician->tickets_assigned + 1,
            ]);
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
        try {
            DB::beginTransaction();

            $request->validate([
                'ticket_number' => 'required',
                'technician' => 'required',
                'old' => 'required',
            ]);

            $ticket = Ticket::where('ticket_number', $request->ticket_number)->first();
            $assigned = AssignedTickets::where(['ticket_number' => $request->ticket_number, 'technician' => $request->old])->with('technician.user')->first();

            $old = Technician::where('technician_id', $request->old)->with('user')->first();
            $new = Technician::where('technician_id', $request->technician)->with('user')->first();

            if ($old->tickets_assigned >= 0) {
                $old->tickets_assigned = 0;
                if ($new->tickets_assigned == 5) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'An error occurred!')->with('message', 'Technician ' . $new->user->name . ' has 5 tickets assigned to them already!');
                } else {
                    $new->tickets_assigned = $new->tickets_assigned + 1;
                    $new->save();
                }
                $old->save();
            } else {
                $old->tickets_assigned = 0;
                $new->tickets_assigned = $new->tickets_assigned + 1;
                $old->save();
                $new->save();
            }


            $assigned->update([
                'technician' => $new->technician_id,
            ]);

            $old->user->notify(
                new UpdateTechnicianReplace($ticket)
            );

            $new->user->notify(
                new UpdateTicketTechnician($ticket)
            );

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred!')->with('message', $e->getMessage());
        }

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
            $technician = Technician::where('technician_id', $assigned->technician)->first();
            if ($technician->tickets_assigned == 0) {
                $technician->tickets_assigned = 0;
            } else {
                $technician->tickets_assigned = $technician->tickets_assigned - 1;
                $technician->save();
            }
            return redirect()->back()->with('success', 'Technician removed!')->with('message', 'Technician successfully removed from Ticket #' . $request->ticket_number);
        }
    }

    public function recommend($department)
    {
        $technicians = Technician::where('assigned_department', $department)->with('user')->get();

        return response()->json(['recommended' => $technicians]);
    }
}
