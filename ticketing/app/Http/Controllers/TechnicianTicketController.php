<?php

namespace App\Http\Controllers;

use App\Models\AssignedTickets;
use App\Models\Employee;
use App\Models\HistoryNumber;
use App\Models\Service;
use App\Models\Problem;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketMade;
use App\Models\ServiceReport;
use App\Models\TicketComment;
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

        $filter = $request->only(['search', 'filterTickets', 'all', 'new', 'pending', 'ongoing', 'resolved', 'from_date_filter', 'to_date_filter']);

        if (!$request->filled('from_date_filter') && !$request->filled('to_date_filter')) {
            $query = Ticket::query()
                ->with('employee.user', 'assigned.technician.user')
                ->whereHas('assigned.technician.user', function ($query) use ($technician) {
                    $query->where('id', $technician->user->id);
                })
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->orderBy(
                    $request->input('sort', 'ticket_number'),
                    $request->input('direction', 'desc')
                );
        } else {
            $query = Ticket::query()
                ->with('employee.user', 'assigned.technician.user')
                ->whereHas('assigned.technician.user', function ($query) use ($technician) {
                    $query->where('id', $technician->user->id);
                })
                ->orderBy($request->input('sort', 'ticket_number'), $request->input('direction', 'desc'));
        }


        if ($request->filled('from_date_filter') && $request->filled('to_date_filter')) {
            $fromDate = $request->input('from_date_filter');
            $toDate = $request->input('to_date_filter');
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

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

        $filters = $request->only(['search']);
        $technicians = Technician::with('user')
            ->where('tickets_assigned', '!=', 5)
            ->get();

        $services = Service::all();
        $latest_rs = HistoryNumber::select('rs_no')->whereNotNull('rs_no')->orderByDesc('rs_no')->first();
        $latest_ms = HistoryNumber::select('ms_no')->whereNotNull('ms_no')->orderByDesc('ms_no')->first();
        $latest_rr = HistoryNumber::select('rr_no')->whereNotNull('rr_no')->orderByDesc('rr_no')->first();
        $latest_sr = HistoryNumber::select('sr_no')->whereNotNull('sr_no')->orderByDesc('sr_no')->first();
        return inertia('Technician/Tickets/Index', [
            'tickets' => $tickets,
            'technicians' => $technicians,
            'services' => $services,
            'filters' => $filters,
            'rs' => $latest_rs,
            'ms' => $latest_ms,
            'rr' => $latest_rr,
            'sr' => $latest_sr,
        ]);
    }



    public function create(Request $request)
    {
        $latest = HistoryNumber::whereNotNull('rs_no')->orderByDesc('rs_no')->first();
        $new = $latest ? $this->increment($latest->ticket_number, $latest->rs_no) : '0001';
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
        $services = Service::all();
        return inertia('Technician/Tickets/Create', [
            'employees' => $employees,
            'filters' => $filter,
            'new_rs' => $new,
            'problems' => $problems,
            'services' => $services,
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
                'request_type' => 'required',
                'complexity' => 'required',
                'description' => 'required',
                'employee' => 'required',
                'problem' => 'required',
                'service' => 'required',
                'user' => 'required',
                'rs_no' => 'nullable|numeric',
                'assignToSelf' => 'nullable',
            ]);
            $technician = Technician::where('user_id', $request->user)->firstOrFail();
            $employee = Employee::where('employee_id', $request->employee)->with('user')->firstOrFail();

            if ($employee->made_ticket >= 5 || $technician->assigned_ticket >= 5) {
                DB::rollBack();
                return redirect()->back()->with('error', 'You have already made the max number of tickets.');
            }

            if (!$request->filled('problem')) {
                $problem = Problem::create([
                    'problem' => $request->new_problem,
                ]);
            } else {
                $problem = $request->problem;
            }

            if (!$request->filled('service')) {
                $service = Service::create([
                    'service' => $request->new_service,
                ]);
            } else {
                $service = $request->service;
            }

            $ticketData = [
                'request_type' => $request->request_type,
                'complexity' => $request->complexity,
                'rs_no' => $request->rs_no,
                'employee' => $request->employee,
                'issue' => $request->problem,
                'description' => $request->description,
                'service' => $request->service,
                'status' => 'Pending',
            ];

            $ticket = Ticket::create($ticketData);
            $employee->update(['made_ticket' => $employee->made_ticket + 1]);

            $history_data = [
                'ticket_number' => $ticket->ticket_number,
                'rs_no' => $request->rs_no,
            ];

            HistoryNumber::create($history_data);

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
                    new TicketMade($ticket, $technician->user->name, $employee->user->name, $employee->office, $employee->department)
                );
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->to('/technician/tickets')->with('error', 'Invalid Ticket')->with('message', $e->getMessage());
        }
        return redirect()->to('/technician/tickets')->with('success', 'Ticket Created')->with('message', 'Admin is notified of the ticket!');
    }

    public function show(Request $request, $id)
    {
        $ticket = Ticket::where('ticket_number', $id)
            ->with('employee.user', 'assigned.technician.user')
            ->first();

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
        return inertia('Technician/Tickets/Show', [
            'ticket' => $ticket,
            'comments' => $comments,
            'replies' => $replies,
            'services' => $services
        ]);
    }

    public function comment(Request $request, $id)
    {
        try {
            $user_id = auth()->id();
            $commenter = User::findOrFail($user_id);
            $ticket = Ticket::findOrFail($id);

            $request->validate([
                'content' => 'required',
            ]);

            $comment = [
                'ticket_number' => $id,
                'user_id' => $user_id,
                'content' => $request->content,
            ];

            $comment = TicketComment::create($comment);

            /* $tagged = $request->tagged_user;

            foreach ($tagged as $user) {
                $tagged = TaggedUser::create([
                    'post_id' => $post->id,
                    'comment_id' => $comment->id,
                    'user_id' => $user,
                ]);

                $user = User::findOrFail($user);
                $user->notify(
                    new UserTaggedComment($user, $comment)
                );
            } */

            /* if ($user_id !== $post->user_id) {
                $post->user->notify(
                    new CommentMade($comment, $commenter->name, $post->title)
                );
            } */

            return redirect()->back()->with('success', 'Commented on the ticket!')->with('message', 'Comment successfully posted!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reply(Request $request, $id, $comment_id)
    {
        try {
            $user_id = auth()->id();
            $replier = User::findOrFail($user_id);
            $ticket = Ticket::findOrFail($id);

            $request->validate([
                'parent_comment_id' => 'nullable',
                'content' => 'required',
            ]);

            $comment = [
                'ticket_number' => $id,
                'parent_comment_id' => $comment_id,
                'user_id' => $user_id,
                'content' => $request->content,
            ];

            $reply = TicketComment::create($comment);

            /* $tagged = $request->tagged_user;

            foreach ($tagged as $user) {
                $tagged = TaggedUser::create([
                    'post_id' => $post->id,
                    'comment_id' => $reply->id,
                    'user_id' => $user,
                ]);

                $user = User::findOrFail($user);
                $user->notify(
                    new UserTaggedReply($user, $reply)
                );
            }

            $originalComment = Comment::with('user')->find($reply->parent_comment_id);

            if ($user_id !== $originalComment->user->id) {
                $originalComment->user->notify(new ReplyMade($reply, $replier->name, $post->title));
            } */

            return redirect()->back()->with('success', 'Replied to the comment!')->with('message', 'Reply successfully posted!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateComment(Request $request, $id)
    {
        $comment = TicketComment::findOrFail($id);

        $request->validate([
            'content' => 'required',
        ]);
        $comment->content = $request->content;
        $comment->save();

        /* $tagged = TaggedUser::where('comment_id', $id)->get();

        foreach ($tagged as $tag) {
            $tag->delete();
        }

        $tagged = $request->tagged_user;

        foreach ($tagged as $user) {
            $tagged = TaggedUser::create([
                'post_id' => $comment->post_id,
                'comment_id' => $comment->id,
                'user_id' => $user,
            ]);
        } */

        return redirect()->back()->with('success', 'Comment successfully updated');
    }

    public function deleteComment($id)
    {
        $comment = TicketComment::findOrFail($id);
        $comment->is_deleted = 1;
        $comment->save();

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

        return redirect()->back()->with('success', 'Comment successfully deleted');
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

            if (!$request->$field) {
                return redirect()->back()->with('error', 'An empty field error occurred!')->with('message', 'Please do not leave the field empty.');
            }

            $ticket = Ticket::where('ticket_number', $id)->first();

            // If the SR number is present in the request, update it in the tickets table
            if ($field === 'sr_no') {
                $serviceReport = ServiceReport::where('ticket_number', $ticket->ticket_number)->first();
                if ($serviceReport) {
                    $serviceReport->service_id = $request->$field;
                    $serviceReport->save();
                } else {
                    // If no service report exists, create a new one with the updated SR number
                    $serviceData = [
                        'service_id' => $request->$field,
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

    public function problem(Request $request)
    {
        $request->validate([
            'problem' => 'required',
        ]);


        $problemData = [
            'problem' => $request->problem,
        ];

        Problem::create($problemData);
    }

    public function services(Request $request)
    {
        $request->validate([
            'service' => 'required',
        ]);


        $serviceData = [
            'service' => $request->service,
        ];

        Service::create($serviceData);
    }
}
