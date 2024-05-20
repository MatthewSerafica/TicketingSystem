<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientCollateResource;
use App\Http\Resources\CollateResource;
use App\Http\Resources\DepartmentCollateResource;
use App\Models\Department;
use App\Models\ServiceReport;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AdminGenerateReportController extends Controller
{
    public function index()
    {
        $monthsAndYears = Ticket::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $tickets = Ticket::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year')
            ->with('employee.user')
            ->distinct()
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
            ->with('employee.user', 'assigned.technician.user')
            ->orderByDesc('created_at')
            ->get();

        return inertia('Admin/Reports/GenerateReports/Show', [
            'tickets' => $tickets,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function showCollate()
    {
        $users = User::where('user_type', 'technician')->with('technician')->get();

        $collateResources = [];

        foreach ($users as $user) {
            $user->average_resolution_time = $this->getAverageResolutionTime($user);
            $user->complexity_counts = $this->getComplexityCounts($user);
            $user->resolved_today = $this->getResolvedToday($user);
            $user->assigned_today = $this->getAssignedToday($user);

            $collateResources[] = new CollateResource($user);
        }
        return inertia('Admin/Reports/GenerateReports/ShowCollate', [
            'users' => $collateResources,
        ]);
    }
    public function clientCollate()
    {
        $users = User::where('user_type', 'employee')->with('employee')->get();

        $collateResources = [];

        foreach ($users as $user) {
            $user->average_resolution_time = $this->getAverageResolutionTime($user);
            $user->complexity_counts = $this->getComplexityCounts($user);
            $user->resolved_total = $this->getResolvedToday($user);
            $user->assigned_today = $this->getAssignedToday($user);
            $user->resolved_today = $this->getClientTicketResolved($user);

            $collateResources[] = new ClientCollateResource($user);
        }
        return inertia('Admin/Reports/GenerateReports/ClientCollate', [
            'users' => $collateResources,
        ]);
    }

    public function departmentCollate()
    {
        $departments = Department::all();

        $collateResources = [];

        foreach ($departments as $department) {
            $department->tickets_made_today = $this->getDepartmentTicketsMade($department, 'today');
            $department->tickets_resolved_today = $this->getDepartmentTicketsResolved($department, 'today');
            $department->tickets_made_total = $this->getDepartmentTicketsMade($department, 'total');
            $department->tickets_resolved_total = $this->getDepartmentTicketsResolved($department, 'total');
            $department->complexity = $this->getDepartmentComplexity($department);
            $department->time = $this->getDepartmentAverageResolutionTime($department);


            $collateResources[] = new DepartmentCollateResource($department);
        }

        return inertia('Admin/Reports/GenerateReports/DepartmentCollate', [
            'departments' => $collateResources,
        ]);
    }

    private function getDepartmentTicketsMade($department, $when)
    {
        if ($when === 'today') {
            $today = now()->startOfDay();

            $tickets = Department::where('departments.department', $department->department)
                ->join('employees', 'departments.department', '=', 'employees.department')
                ->join('tickets', 'employees.employee_id', '=', 'tickets.employee_id')
                ->where('tickets.status', '<>', 'Resolved')
                ->whereDate('tickets.created_at', $today)
                ->groupBy('departments.id')
                ->count();

            return $tickets;
        } else if ($when === 'total') {
            $tickets = Department::where('departments.department', $department->department)
                ->join('employees', 'departments.department', '=', 'employees.department')
                ->join('tickets', 'employees.employee_id', '=', 'tickets.employee_id')
                ->groupBy('departments.id')
                ->count();

            return $tickets;
        }
    }
    private function getDepartmentTicketsResolved($department, $when)
    {
        if ($when === 'today') {
            $today = now()->startOfDay();

            $tickets = Department::where('departments.department', $department->department)
                ->join('employees', 'departments.department', '=', 'employees.department')
                ->join('tickets', 'employees.employee_id', '=', 'tickets.employee_id')
                ->where('tickets.status', '=', 'Resolved')
                ->whereDate('tickets.created_at', $today)
                ->groupBy('departments.id')
                ->count();

            return $tickets;
        } else if ($when === 'total') {
            $tickets = Department::where('departments.department', $department->department)
                ->join('employees', 'departments.department', '=', 'employees.department')
                ->join('tickets', 'employees.employee_id', '=', 'tickets.employee_id')
                ->where('tickets.status', '=', 'Resolved')
                ->groupBy('departments.id')
                ->count();

            return $tickets;
        }
    }

    private function getDepartmentComplexity($department)
    {
        $tickets = Department::where('departments.department', $department->department)
            ->join('employees', 'departments.department', '=', 'employees.department')
            ->join('tickets', 'employees.employee_id', '=', 'tickets.employee_id')
            ->selectRaw('tickets.complexity, COUNT(tickets.complexity) as count')
            ->groupBy('tickets.complexity')
            ->get();

        $counts = [
            'simple' => 0,
            'complex' => 0,
        ];

        foreach ($tickets as $ticket) {
            if ($ticket->complexity === 'Simple') {
                $counts['simple'] += $ticket->count;
            } elseif ($ticket->complexity === 'Complex') {
                $counts['complex'] += $ticket->count;
            }
        }

        return $counts;
    }

    private function getDepartmentAverageResolutionTime($department)
    {
        $time = ServiceReport::with('ticket.employee')
            ->whereHas('ticket.employee', function ($query) use ($department) {
                $query->where('department', $department->department);
            })
            ->whereNotNull('date_done')
            ->whereNotNull('time_done')
            ->whereNotNull('date_started')
            ->whereNotNull('time_started')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, CONCAT(date_started, " ", time_started), CONCAT(date_done, " ", time_done))) AS average_resolution_time')
            ->value('average_resolution_time');

        if ($time) {
            $timeInHours = $time / 3600;
            $timeInDecimal = round($timeInHours, 2);
            return $timeInDecimal;
        } else {
            return 0;
        }
    }

    private function getAssignedToday($user)
    {
        $todayStart = Carbon::today();
        $todayEnd = $todayStart->copy()->endOfDay();

        $statusFilter = ['New', 'Pending', 'Ongoing'];
        $relationPath = $user->user_type == 'technician' ? 'assigned.technician.user' : 'employee.user';

        $ticketCount = Ticket::with($relationPath)
            ->whereHas($relationPath, function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->whereIn('status', $statusFilter)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->count();

        return $ticketCount;
    }

    private function getResolvedToday($user)
    {
        $todayStart = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();

        $relation = $user->user_type == 'technician' ? 'assigned.technician.user' : 'employee.user';

        $ticketCount = Ticket::with($relation)
            ->whereHas($relation, function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->where('status', 'resolved');

        if ($user->user_type == 'technician') {
            $ticketCount = $ticketCount->whereBetween('created_at', [$todayStart, $todayEnd]);
        }

        return $ticketCount->count();
    }

    private function getClientTicketResolved($user)
    {
        $cacheKey = 'resolved_tickets_count_' . $user->id . '_' . Carbon::today()->toDateString();

        $ticketCount = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
            $todayStart = Carbon::today();
            $todayEnd = Carbon::today()->endOfDay();

            return Ticket::with('employee.user')
                ->whereHas('employee.user', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->where('status', 'resolved')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->count();
        });

        return $ticketCount;
    }

    private function getComplexityCounts($user)
    {
        $relationship = $user->user_type === 'technician' ? 'assigned.technician' : 'employee';

        $ticketCountsQuery = Ticket::query()
            ->whereHas($relationship . '.user', function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->selectRaw('lower(complexity) as complexity, COUNT(*) as count')
            ->groupBy('complexity')
            ->get();

        $counts = [
            'simple' => 0,
            'complex' => 0,
        ];

        foreach ($ticketCountsQuery as $ticketCount) {
            if ($ticketCount->complexity === 'simple') {
                $counts['simple'] = $ticketCount->count;
            } elseif ($ticketCount->complexity === 'complex') {
                $counts['complex'] = $ticketCount->count;
            }
        }

        return $counts;
    }

    private function getAverageResolutionTime($user)
    {
        if ($user->user_type === 'technician') {
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
        } else {
            $time = ServiceReport::with('ticket.employee')
                ->whereHas('ticket.employee.user', function ($query) use ($user) {
                    $query->where('name', 'like', '%' . $user->name . '%');
                })
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
