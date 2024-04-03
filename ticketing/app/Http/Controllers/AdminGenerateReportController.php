<?php

namespace App\Http\Controllers;

use App\Models\ArchivedTicket;

class AdminGenerateReportController extends Controller
{
    public function index()
    {
        $monthsAndYears = ArchivedTicket::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $tickets = ArchivedTicket::select('*')
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
        $tickets = ArchivedTicket::select('*')
            ->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
            ->orderByDesc('created_at')
            ->get();

        return inertia('Admin/Reports/GenerateReports/Print', [
            'tickets' => $tickets,
            'month' => $month,
            'year' => $year,
        ]);
    }
}
