<?php

namespace App\Http\Controllers;

use App\Models\ArchivedTicket;
use App\Models\Ticket;

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
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();
            
        return inertia('Admin/Reports/GenerateReports/Index', [
            'monthsAndYears' => $monthsAndYears,
            'tickets' => $tickets,
        ]);
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
