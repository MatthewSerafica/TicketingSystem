<?php

namespace App\Http\Controllers;

use App\Models\AssignedTickets;
use App\Models\Employee;
use App\Models\HistoryNumber;
use App\Models\Log;
use App\Models\Service;
use App\Models\Technician;
use App\Models\Problem;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use App\Notifications\TicketMade;
use App\Notifications\UpdateTechnicianReplace;
use App\Notifications\UpdateTicketStatus;
use App\Notifications\UpdateTicketTechnician;
use App\Models\TicketTask;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->only(['search', 'filterTickets', 'all', 'new', 'pending', 'ongoing', 'resolved', 'from_date_filter', 'to_date_filter']);

        $query = Ticket::query()->with('employee.user', 'assigned.technician.user');

        if ($request->filled(['from_date_filter', 'to_date_filter'])) {
            $fromDate = $request->input('from_date_filter');
            $toDate = $request->input('to_date_filter');
            $query->whereBetween('created_at', ["$fromDate 00:00:00", "$toDate 23:59:59"]);
        } else {
            $query->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month);
        }

        $query->orderBy($request->input('sort', 'ticket_number'), $request->input('direction', 'desc'));


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
                ->orWhereHas('assigned.technician.user', function ($subquery) use ($search) {
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

        $additionalData = $this->getAdditionalData();

        return inertia('Admin/Tickets/Index', array_merge(['tickets' => $tickets, 'filters' => $filter], $additionalData));
    }
    protected function getAdditionalData()
    {
        $services = Service::all();
        $latest_rs = HistoryNumber::select('rs_no')->whereNotNull('rs_no')->orderByDesc('rs_no')->first();
        $latest_ms = HistoryNumber::select('ms_no')->whereNotNull('ms_no')->orderByDesc('ms_no')->first();
        $latest_rr = HistoryNumber::select('rr_no')->whereNotNull('rr_no')->orderByDesc('rr_no')->first();
        $latest_sr = HistoryNumber::select('sr_no')->whereNotNull('sr_no')->orderByDesc('sr_no')->first();
        return [
            'services' => $services,
            'rs' => $latest_rs,
            'ms' => $latest_ms,
            'rr' => $latest_rr,
            'sr' => $latest_sr,
        ];
    }

    public function create(Request $request)
    {
        $latest = HistoryNumber::whereNotNull('rs_no')->orderByDesc('rs_no')->first();
        $new = $latest ? $this->increment($latest->ticket_number, $latest->rs_no) : '0001';
        $technicians = Technician::with('user')
            ->where('is_working', '=', 1)
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

        $problems = Problem::get();

        $filter = $request->only(['search']);
        return inertia('Admin/Tickets/Create', [
            'technicians' => $technicians,
            'employees' => $employees,
            'services' => $services,
            'filters' => $filter,
            'new_rs' => $new,
            'problems' => $problems,
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
        $request->validate([
            'request_type' => 'required|string',
            'description' => 'nullable|string',
            'employee' => 'required',
            'problem' => 'required|string',
            'service' => 'nullable|string',
            'rs_no' => 'nullable|numeric',
        ]);

        try {

            if ($request->filled('rs_no')) {
                if (!is_numeric($request->rs_no)) {
                    return redirect()->back()->with('error', 'RS number must be numeric.');
                }
            }

            DB::transaction(function () use ($request) {
                $employee = Employee::where('employee_id', $request->employee)->firstOrFail();
                $ticket_data = [
                    'request_type' => $request->request_type,
                    'rs_no' => $request->rs_no,
                    'employee_id' => $request->employee,
                    'issue' => $request->problem,
                    'description' => $request->description,
                    'service' => $request->service,
                    'status' => 'Pending',
                ];

                $ticket = Ticket::create($ticket_data);
                $this->handleHistoryNumberStore($ticket, $request->rs_no);
                $this->handleTechnicianStore($ticket, $request->technicians);

                $employee->update(['made_ticket' => $employee->made_ticket + 1]);
                $employee->user->notify(
                    new TicketMade($ticket, $request->technician, $employee->user->name, $employee->office, $employee->department)
                );

                $ticket_data_json = json_encode($ticket_data, JSON_PRETTY_PRINT);
                $action_taken = "Created a new ticket #" . $ticket->ticket_number . "\n" .
                    "Details: " . $ticket_data_json . "\n";
                $this->logAction($action_taken);
            });

            return redirect()->to('/admin/tickets')->with('success', 'Ticket Created')->with('message', 'All assigned technicians are notified!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->to('/admin/tickets')->with('error', 'An error occurred!')->with('message', $e->getMessage());
        }
    }

    private function handleHistoryNumberStore($ticket, $rs_no)
    {
        $history_data = [
            'ticket_number' => $ticket->ticket_number,
            'rs_no' => $rs_no,
        ];

        HistoryNumber::create($history_data);
    }

    private function handleTechnicianStore($ticket, $technicians)
    {
        $assigned_technicians = $technicians;

        foreach ($assigned_technicians as $technician_id) {
            AssignedTickets::create([
                'ticket_number' => $ticket->ticket_number,
                'technician' => $technician_id,
            ]);

            $this->sendTechnicianNotification($technician_id, $ticket);
        }
    }

    public function show(Request $request, $id)
    {
        $ticket = Ticket::where('ticket_number', $id)
            ->with(['employee.user', 'assigned.technician.user'])
            ->firstOrFail();

        $comments = TicketComment::where('ticket_number', $id)
            ->whereNot('is_deleted', 1)
            ->whereNull('parent_comment_id')
            ->with('user', 'tagged.user')
            ->orderBy('created_at', 'desc')
            ->get();


        $comments->each(function ($comment) {
            $comment->time_since_posted = $comment->created_at->diffForHumans();
        });

        $replies = TicketComment::where('ticket_number', $id)
            ->whereNot('is_deleted', 1)
            ->whereNotNull('parent_comment_id')
            ->with('user', 'tagged.user')
            ->orderBy('created_at', 'desc')
            ->get();

        $replies->each(function ($reply) {
            $reply->time_since_posted = $reply->created_at->diffForHumans();
        });

        $services = Service::all();

        $tasks = TicketTask::where('ticket_number', $id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();


        $latest_rs = HistoryNumber::select('rs_no')->whereNotNull('rs_no')->orderByDesc('rs_no')->first();
        $latest_ms = HistoryNumber::select('ms_no')->whereNotNull('ms_no')->orderByDesc('ms_no')->first();
        $latest_rr = HistoryNumber::select('rr_no')->whereNotNull('rr_no')->orderByDesc('rr_no')->first();
        $latest_sr = HistoryNumber::select('sr_no')->whereNotNull('sr_no')->orderByDesc('sr_no')->first();
        return inertia('Admin/Tickets/Show', [
            'ticket' => $ticket,
            'comments' => $comments,
            'replies' => $replies,
            'services' => $services,
            'tasks' => $tasks,
            'rs' => $latest_rs,
            'ms' => $latest_ms,
            'rr' => $latest_rr,
            'sr' => $latest_sr,
        ]);
    }

    public function comment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $user_id = auth()->id();

                TicketComment::create([
                    'ticket_number' => $id,
                    'user_id' => $user_id,
                    'content' => $request->content,
                ]);
            });

            return redirect()->back()->with('success', 'Commented on the ticket!')->with('message', 'Comment successfully posted!');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Unable to post comment at this time. Please try again later.');
        }
    }

    public function reply(Request $request, $id, $comment_id)
    {
        $request->validate([
            'parent_comment_id' => 'nullable',
            'content' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $user_id = auth()->id();

            $reply = TicketComment::create([
                'ticket_number' => $id,
                'parent_comment_id' => $comment_id,
                'user_id' => $user_id,
                'content' => $request->content,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Replied to the comment!')->with('message', 'Reply successfully posted!');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', 'Failed to post reply. Please try again.');
        }
    }

    public function updateComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $comment = TicketComment::findOrFail($id);

            $comment->content = $request->content;
            $comment->save();

            DB::commit();
            return redirect()->back()->with('success', 'Comment successfully updated');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function deleteComment($id)
    {
        DB::beginTransaction();
        try {
            $comment = TicketComment::findOrFail($id);
            $comment->is_deleted = 1;
            $comment->save();

            $this->handleCommentChild($id);
            DB::commit();
            return redirect()->back()->with('success', 'Comment successfully deleted');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function handleCommentChild($id)
    {
        $children = TicketComment::where('parent_comment_id', $id)->get();

        if ($children) {
            foreach ($children as $child) {
                $child->is_deleted = 1;
                $child->save();
                $grandChildren = TicketComment::where('parent_comment_id', $child->id)->get();
                if ($grandChildren) {
                    foreach ($grandChildren as $grandChild) {
                        $grandChild->is_deleted = 1;
                        $grandChild->save();
                    }
                }
            }
        }
    }

    protected function sendTechnicianNotification($technician_id, $ticket)
    {
        $technician = Technician::where('technician_id', $technician_id)->firstOrFail();
        $technician->update(['tickets_assigned' => $technician->tickets_assigned + 1]);

        $employee = Employee::findOrFail($ticket->employee_id);
        $technician->user->notify(
            new UpdateTicketTechnician($ticket, $employee->user->name, $employee->department, $employee->office)
        );
    }

    public function update(Request $request, $field, $id)
    {
        $request->validate([
            $field => 'nullable',
        ]);

        if (!$request->$field) {
            return redirect()->back()->with('error', 'An empty field error occurred!')->with('message', 'Please do not leave the field empty.');
        }

        $ticket = Ticket::firstWhere('ticket_number', $id);

        if ($field === 'technician') {
            return $this->updateTechnician($request, $ticket);
        } else {
            return $this->updateField($request, $field, $ticket);
        }
    }

    private function updateField(Request $request, $field, $ticket)
    {
        DB::beginTransaction();
        try {
            $ticket->$field = $request->$field;
            $ticket->save();

            if ($field !== 'remarks' && $field !== 'description') {
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
                'description' => 'Description',
            ];

            $input = $fieldMappings[$field] ?? $field;

            $action_taken = "Updated the ticket #" . $ticket->ticket_number . "'s " . $input . ': ' . $request->$field;
            $this->logAction($action_taken);

            DB::commit();
            if (!$request->$field) {
                return redirect()->back()->with('success', 'Ticket Update!')->with('message', $input . ' is now removed from Ticket #' . $ticket->ticket_number);
            } else {
                return redirect()->back()->with('success', 'Ticket Update!')->with('message', $input . ' ' . $request->$field . ' is now assigned to Ticket #' . $ticket->ticket_number);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function updateTechnician(Request $request, $ticket)
    {
        DB::beginTransaction();
        try {
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


            $employee = Employee::findOrFail($ticket->employee_id);

            if ($technician) {
                $technician->user->notify(
                    new UpdateTicketTechnician($ticket, $employee->user->name, $employee->department, $employee->office,)
                );
            }

            $action_taken = "Updated the ticket #" . $ticket->ticket_number . "'s " . 'assigned technicians';
            $this->logAction($action_taken);

            DB::commit();
            return redirect()->back()->with('success', 'Technician Update!')->with('message', 'Technician ' . $technician->user->name . ' is now assigned to Ticket #' . $ticket->ticket_number);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred!');
        }
    }

    public function status(Request $request, $ticket_id)
    {
        $request->validate([
            'status' => 'required',
            'old_status' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $ticket = Ticket::where('ticket_number', $ticket_id)->firstOrFail();
            $employee = Employee::find($ticket->employee_id);
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
            $ticket->save();

            $employee->user->notify(new UpdateTicketStatus($ticket));

            $action_taken = "Updated the ticket #" . $ticket->ticket_number . "'s " . 'status: ' . $request->old_status . ' to ' . $request->status;
            $this->logAction($action_taken);

            DB::commit();
            return redirect()->back()
                ->with('success', 'Status Update!')
                ->with('message', 'Ticket No. ' . $ticket->ticket_number . ' is now ' . $request->status);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred!');
        }
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

    public function service(Request $request, $ticket_id)
    {
        $request->validate([
            'service' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $ticket = Ticket::where('ticket_number', $ticket_id)->first();

            $ticket->service = $request->service;
            if ($ticket->status == 'New') {
                $ticket->status = 'Pending';
            }
            $ticket->save();

            $action_taken = "Updated the ticket #" . $ticket->ticket_number . "'s " . 'service: ' . $request->service;
            $this->logAction($action_taken);

            DB::commit();
            return redirect()->back()->with('success', 'Service Update!')->with('message', $request->service . ' service is now assigned to Ticket No. ' . $ticket->ticket_number);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred!');
        }
    }

    public function remark(Request $request, $ticket_id)
    {
        $request->validate([
            'remark' => 'nullable',
        ]);

        DB::beginTransaction();
        try {

            $ticket = Ticket::where('ticket_number', $ticket_id)->first();

            $ticket->remarks = $request->remark;
            $ticket->save();

            $action_taken = "Updated the ticket #" . $ticket->ticket_number . "'s " . 'remarks: ' . $request->remark;
            $this->logAction($action_taken);

            DB::commit();
            return redirect()->back()->with('success', 'Receiving Report Update!')->with('message', 'Successfuly added remarks to Ticket No. ' . $ticket->ticket_number);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred!');
        }
    }

    public function complexity(Request $request, $ticket_id)
    {
        $request->validate([
            'complexity' => 'nullable',
        ]);

        DB::beginTransaction();
        try {

            $ticket = Ticket::where('ticket_number', $ticket_id)->first();

            $ticket->complexity = $request->complexity;
            if ($ticket->status == 'New') {
                $ticket->status = 'Pending';
            }
            $ticket->save();


            $auth = Auth::user();
            $action_taken = "Updated the ticket #" . $ticket->ticket_number . "'s " . 'complexity: ' . $request->complexity;
            $this->logAction($action_taken);

            DB::commit();
            return redirect()->back()->with('success', 'Receiving Report Update!')->with('message', 'Ticket No. ' . $ticket->ticket_number . ' is now set as ' . $request->complexity);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred!');
        }
    }

    public function replace(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required',
            'technician' => 'required',
            'old' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $ticket = Ticket::where('ticket_number', $request->ticket_number)->first();
            $assigned = AssignedTickets::where(['ticket_number' => $request->ticket_number, 'technician' => $request->old])->with('technician.user')->first();

            $old = Technician::where('technician_id', $request->old)->with('user')->first();
            $new = Technician::where('technician_id', $request->technician)->with('user')->first();

            if ($old->tickets_assigned >= 0) {
                if ($old->tickets_assigned <= 0) {
                    $old->tickets_assigned = 0;
                } else {
                    $old->tickets_assigned = $old->tickets_assigned - 1;
                }
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


            $employee = Employee::findOrFail($ticket->employee_id);
            $old->user->notify(
                new UpdateTechnicianReplace($ticket, $employee->user->name, $employee->department, $employee->office,)
            );

            $new->user->notify(
                new UpdateTicketTechnician($ticket, $employee->user->name, $employee->department, $employee->office,)
            );

            $action_taken = "Updated the ticket #" . $ticket->ticket_number . "'s " . 'assigned technicians, replaced a technician';
            $this->logAction($action_taken);

            DB::commit();
            return redirect()->back()->with('success', 'Ticket Updated!')->with('message', 'Technician has been replaced!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred!')->with('message', $e->getMessage());
        }
    }

    public function remove(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required',
            'technician' => 'required',
        ]);

        DB::beginTransaction();
        try {
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

                $auth = Auth::user();
                $action_taken = "Removed a technician from  ticket #" .  $request->ticket_number;
                $this->logAction($action_taken);

                DB::commit();
                return redirect()->back()->with('success', 'Technician removed!')->with('message', 'Technician successfully removed from Ticket #' . $request->ticket_number);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred!')->with('message', $e->getMessage());
        }
    }

    public function recommend($department, $id)
    {
        if ($id) {
            $assignedTechnicianIds = AssignedTickets::where('ticket_number', $id)
                ->pluck('technician');
            $technicians = Technician::where('assigned_department', $department)
                ->with('user')
                ->where('is_working', '=', 1)
                ->whereNotIn('technician_id', $assignedTechnicianIds)
                ->get();
        } else {
            $technicians = Technician::where('assigned_department', $department)
                ->with('user')
                ->where('is_working', '=', 1)
                ->get();
        }

        return response()->json(['recommended' => $technicians]);
    }
    public function technicians($id)
    {
        $assignedTechnicianIds = AssignedTickets::where('ticket_number', $id)
            ->pluck('technician');

        $technicians = Technician::with('user')
            ->where('is_working', '=', 1)
            ->whereNotIn('technician_id', $assignedTechnicianIds)
            ->get();

        return response()->json(['fetchedTechs' => $technicians]);
    }

    public function problem(Request $request)
    {
        $request->validate([
            'problem' => 'required',
        ]);

        DB::beginTransaction();
        try {

            $problemData = $request->only('problem');

            Problem::create($problemData);

            $action_taken = "Added a new problem " .  $request->problem;
            $this->logAction($action_taken);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred!')->with('message', $e->getMessage());
        }
    }

    public function services(Request $request)
    {
        $request->validate([
            'service' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $serviceData = $request->only('service');

            Service::create($serviceData);

            $action_taken = "Added a new service " .  $request->service;
            $this->logAction($action_taken);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred!')->with('message', $e->getMessage());
        }
    }

    public function addTask(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required',
            'user_id' => 'required',
            'task_name' => 'required',
            'is_resolved' => 'nullable',
        ]);
        DB::beginTransaction();
        try {

            $task = [
                'ticket_number' => $request->ticket_number,
                'task_name' => $request->task_name,
                'user_id' => $request->user_id,
                'is_resolved' => $request->is_resolved,
            ];

            TicketTask::create($task);

            $action_taken = "Added a new task for ticket #" . $request->ticket_number . ": " . $request->task_name;
            $this->logAction($action_taken);

            DB::commit();
            return redirect()->back()->with('success', 'Added Task!')->with('message', 'Task Added successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateTask(Request $request, $id)
    {
        $request->validate([
            'task_name' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $task = TicketTask::where('id', $id)->first();
            $task->task_name = $request->input('task_name', $task->task_name);
            $task->save();

            $action_taken = "Updated a task for ticket #" . $request->ticket_number . " new name: " . $request->task_name;
            $this->logAction($action_taken);

            DB::commit();
            return redirect()->back()
                ->with('success', 'Task Updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function resolveTask(Request $request, $id)
    {
        $request->validate([
            'is_resolved' => 'nullable|boolean',
        ]);
        DB::beginTransaction();
        try {
            $task = TicketTask::where('id', $id)->first();

            $isResolved = $request->input('is_resolved');

            $auth = Auth::user();
            $action_taken = "Updated a task for ticket #" . $request->ticket_number . ", resolved: " . $task->task_name;

            if ($isResolved === true) {
                $task->is_resolved = now()->toDateTimeString();
                $this->logAction($action_taken);
            } else {
                $task->is_resolved = null;
                $this->logAction($action_taken);
            }
            $task->save();

            DB::commit();
            return redirect()->back()
                ->with('success', 'Task Updated!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function deleteTask(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $task = TicketTask::where('id', $id)->first();
            $task->delete();

            $action_taken = "Removed a task for ticket #" . $request->ticket_number . ": " . $task->task_name;
            $this->logAction($action_taken);

            DB::commit();
            return redirect()->back()
                ->with('success', 'Task Deleted successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function logAction($action)
    {
        $auth = Auth::user();
        if ($auth) {
            Log::create([
                'name' => $auth->name,
                'user_type' => $auth->user_type,
                'actions_taken' => $action,
            ]);
        } else {
            Log::create([
                'name' => request()->ip(),
                'user_type' => request()->header('User-Agent'),
                'actions_taken' => $action,
            ]);
        }
    }
}
