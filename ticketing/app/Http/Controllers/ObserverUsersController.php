<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\ArchivedTicket;
use App\Models\Department;
use App\Models\Office;
use App\Models\ServiceReport;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;


class ObserverUsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('technician', 'employee');

        // Retrieve filter state from query parameters
        $filter = $request->only(['search', 'filterUsers', 'all', 'employee', 'technician']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('user_type', 'like', '%' . $search . '%')
                    ->orWhereHas('employee', function ($subquery) use ($search) {
                        $subquery->where('department', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('filterUsers')) {
            // Apply user type filter
            $userFilter = $request->input('filterUsers');
            if ($userFilter === 'employee' || $userFilter === 'technician') {
                $query->where('user_type', $userFilter);
            }
        }

        $users = $query->paginate(10);

        $users->appends($filter);

        // Save the filter in the session to maintain state across page requests
        $request->session()->put('filter', $filter);

        return inertia('Observer/Users/Index', [
            'users' => $users,
            'filter' => $filter, // Pass the filter state to the view
        ]);
    }

    public function show($id)
    {
        $user = User::where('id', $id)->with('employee', 'technician')->firstOrFail();
        $departments = Department::all();
        $offices = Office::all();
        if ($user->user_type == 'employee' || $user->user_type == 'technician') {
            $yearly = $this->getYearlyData($user);
            $service = $this->getType($user);
            $assigned_today = $this->getAssignedToday($user);
            $resolved_today = $this->getResolvedToday($user);
            $time = $this->getAverageResolutionTime($user);
            $complexity = $this->getComplexityCounts($user);
        } else {

            $yearly = $this->getAdminYearlyData();
            $service = $this->getAdminType();
        }
        return inertia('Observer/Users/Show', [
            'users' => $user,
            'departments' => $departments,
            'offices' => $offices,
            'yearly' => $yearly ?? null,
            'service' => $service ?? null,
            'assigned_today' => $assigned_today ?? null,
            'resolved_today' => $resolved_today ?? null,
            'time' => $time ?? null,
            'complexity' => $complexity ?? null,
        ]);
    }


    private function getAssignedToday($user)
    {
        if ($user->user_type == 'technician') {
            $todayStart = Carbon::today();
            $todayEnd = Carbon::today()->endOfDay();
            $ticket = Ticket::with('assigned.technician.user')
                ->whereHas('assigned.technician.user', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->whereIn('status', ['New', 'Pending', 'Ongoing'])
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->count();
            return $ticket;
        }
    }
    private function getResolvedToday($user)
    {
        if ($user->user_type == 'technician') {
            $todayStart = Carbon::today();
            $todayEnd = Carbon::today()->endOfDay();
            $ticket = Ticket::with('assigned.technician.user')
                ->whereHas('assigned.technician.user', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->where('status', 'resolved')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->count();
            return $ticket;
        }
    }

    private function getComplexityCounts($user)
    {
        $ticketCounts = Ticket::with('assigned.technician.user')
            ->whereHas('assigned.technician.user', function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->selectRaw('complexity, COUNT(*) as count')
            ->groupBy('complexity')
            ->get();

        $counts = [
            'simple' => 0,
            'complex' => 0
        ];

        foreach ($ticketCounts as $ticket) {
            if ($ticket->complexity === 'Simple') {
                $counts['simple'] = $ticket->count;
            } elseif ($ticket->complexity === 'Complex') {
                $counts['complex'] = $ticket->count;
            }
        }

        return $counts;
    }

    private function getAverageResolutionTime($user)
    {
        $time = ServiceReport::where('technician', 'like', '%' . $user->name . '%')
            ->whereNotNull('date_done')
            ->whereNotNull('time_done')
            ->whereNotNull('date_started')
            ->whereNotNull('time_started')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, CONCAT(date_started, " ", time_started), CONCAT(date_done, " ", time_done))) AS average_resolution_time')
            ->value('average_resolution_time');

        $timeInHours = $time / 3600;

        $timeInDecimal = round($timeInHours, 2);

        return $timeInDecimal;
    }


    private function getYearlyData($user)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        if ($user->user_type == 'employee') {
            $yearly_data = Ticket::whereYear('created_at', Carbon::now()->year)
                ->where('employee', $user->employee->employee_id)
                ->get()
                ->groupBy(function ($ticket) {
                    return Carbon::parse($ticket->created_at)->format('M');
                })
                ->map(function ($grouped_tickets) {
                    return $grouped_tickets->count();
                });
        } else if ($user->user_type == 'technician') {
            $yearly_data = Ticket::whereYear('created_at', Carbon::now()->year)
                ->with('assigned.technician.user')
                ->whereHas('assigned.technician.user', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->get()
                ->groupBy(function ($ticket) {
                    return Carbon::parse($ticket->created_at)->format('M');
                })
                ->map(function ($grouped_tickets) {
                    return $grouped_tickets->count();
                });
        }
        $ordered_data = collect([]);
        foreach ($months as $month) {
            $ordered_data[$month] = $yearly_data->get($month, 0);
        }
        return $ordered_data;
    }

    private function getType($user)
    {
        if ($user->user_type == 'employee') {
            $types = Ticket::distinct('service')->where('employee', $user->employee->employee_id)->pluck('service');
            $typeCounts = [];

            foreach ($types as $type) {
                $count = Ticket::where('service', $type)->where('employee', $user->employee->employee_id)->count(); // Count tickets for each type
                $typeCounts[$type] = $count;
            }
        } else if ($user->user_type == 'technician') {
            $types = Ticket::distinct('service')
                ->with('assigned.technician.user')
                ->whereHas('assigned.technician.user', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->pluck('service');
            $typeCounts = [];

            foreach ($types as $type) {
                $count = Ticket::where('service', $type)
                    ->with('assigned.technician.user')
                    ->whereHas('assigned.technician.user', function ($query) use ($user) {
                        $query->where('id', $user->id);
                    })
                    ->count(); // Count tickets for each type
                $typeCounts[$type] = $count;
            }
        }

        return $typeCounts;
    }
    private function getAdminYearlyData()
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $ticket_data = Ticket::whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(function ($ticket) {
                return Carbon::parse($ticket->created_at)->format('M');
            })
            ->map(function ($grouped_tickets) {
                return $grouped_tickets->count();
            });

        $archived_ticket_data = ArchivedTicket::whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(function ($ticket) {
                return Carbon::parse($ticket->created_at)->format('M');
            })
            ->map(function ($grouped_tickets) {
                return $grouped_tickets->count();
            });

        $yearly_data = $ticket_data->mergeRecursive($archived_ticket_data);

        $ordered_data = collect([]);
        foreach ($months as $month) {
            $ordered_data[$month] = $yearly_data->get($month, 0);
        }
        return $ordered_data;
    }

    private function getAdminType()
    {
        $ticket_types = Ticket::distinct('service')->pluck('service');
        $archived_ticket_types = ArchivedTicket::distinct('service')->pluck('service');

        $types = $ticket_types->merge($archived_ticket_types)->unique();

        $typeCounts = [];

        foreach ($types as $type) {
            $ticket_count = Ticket::where('service', $type)->count();
            $archived_ticket_count = ArchivedTicket::where('service', $type)->count();

            $total_count = $ticket_count + $archived_ticket_count;

            $typeCounts[$type] = $total_count;
        }

        return $typeCounts;
    }



}
