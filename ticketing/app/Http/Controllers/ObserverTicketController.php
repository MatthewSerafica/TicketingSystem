<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\HistoryNumber;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketTask;

use Carbon\Carbon;

class ObserverTicketController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->only(['search', 'filterTickets', 'all', 'new', 'pending', 'ongoing', 'resolved', 'from_date_filter', 'to_date_filter']);


        if (!$request->filled('from_date_filter') && !$request->filled('to_date_filter')) {
            $query = Ticket::query()
                ->with('employee.user', 'assigned.technician.user')
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->orderBy(
                    $request->input('sort', 'ticket_number'),
                    $request->input('direction', 'desc')
                );
        } else {
            $query = Ticket::query()
                ->with('employee.user', 'assigned.technician.user')
                ->orderBy(
                    $request->input('sort', 'ticket_number'),
                    $request->input('direction', 'desc')
                );
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

        $services = Service::all();
        $latest_rs = HistoryNumber::select('rs_no')->whereNotNull('rs_no')->orderByDesc('rs_no')->first();
        $latest_ms = HistoryNumber::select('ms_no')->whereNotNull('ms_no')->orderByDesc('ms_no')->first();
        $latest_rr = HistoryNumber::select('rr_no')->whereNotNull('rr_no')->orderByDesc('rr_no')->first();
        $latest_sr = HistoryNumber::select('sr_no')->whereNotNull('sr_no')->orderByDesc('sr_no')->first();
        return inertia('Observer/Tickets/Index', [
            'tickets' => $tickets,
            'filters' => $filter,
            'services' => $services,
            'rs' => $latest_rs,
            'ms' => $latest_ms,
            'rr' => $latest_rr,
            'sr' => $latest_sr,
        ]);
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

        $tasks = TicketTask::where('ticket_number', $id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();


        $latest_rs = HistoryNumber::select('rs_no')->whereNotNull('rs_no')->orderByDesc('rs_no')->first();
        $latest_ms = HistoryNumber::select('ms_no')->whereNotNull('ms_no')->orderByDesc('ms_no')->first();
        $latest_rr = HistoryNumber::select('rr_no')->whereNotNull('rr_no')->orderByDesc('rr_no')->first();
        $latest_sr = HistoryNumber::select('sr_no')->whereNotNull('sr_no')->orderByDesc('sr_no')->first();
        return inertia('Observer/Tickets/Show', [
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

}
