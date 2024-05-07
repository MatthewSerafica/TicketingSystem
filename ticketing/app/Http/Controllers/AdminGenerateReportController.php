<?php

namespace App\Http\Controllers;

use App\Http\Resources\CollateResource;
use App\Models\ArchivedTicket;
use App\Models\ServiceReport;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;

class AdminGenerateReportController extends Controller
{
    public function index()
    {
        $monthsAndYears = Ticket::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $tickets = Ticket::select('*')
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year')
            ->with('employee.user')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return inertia('Admin/Reports/GenerateReports/Index', [
            'monthsAndYears' => $monthsAndYears,
            'tickets' => $tickets,
        ]);
    }



    public function show($from, $to)
    {
        $tickets = Ticket::select('*')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->with('employee.user', 'assigned.technician.user')
            ->get();

        return inertia('Admin/Reports/GenerateReports/Show', [
            'tickets' => $tickets,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function showCollate()
    {
        $users = User::where('user_type', 'technician')->with(['technician'])->get();

        // Creating an array to hold the CollateResource collection
        $collateResources = [];

        foreach ($users as $user) {
            // Dynamically set the custom attributes for each user
            $user->average_resolution_time = $this->getAverageResolutionTime($user);
            $user->complexity_counts = $this->getComplexityCounts($user);
            $user->resolved_today = $this->getResolvedToday($user);
            $user->assigned_today = $this->getAssignedToday($user);

            // Create a new CollateResource for each user and add it to the collection
            $collateResources[] = new CollateResource($user);
        }
        return inertia('Admin/Reports/GenerateReports/ShowCollate', [
            'users' => $collateResources,
        ]);
    }

    public function printCollate()
    {
        return inertia('Admin/Reports/GenerateReports/PrintCollate', [
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
    public function print($year, $month)
    {
        $tickets = Ticket::select('*')
            ->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
            ->orderByDesc('created_at')
            ->with('employee.user', 'assigned.technician.user')
            ->get();

        return inertia('Admin/Reports/GenerateReports/Print', [
            'tickets' => $tickets,
            'month' => $month,
            'year' => $year,
        ]);
    }
}
