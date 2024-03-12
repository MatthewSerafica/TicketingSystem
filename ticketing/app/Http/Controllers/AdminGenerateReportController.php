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
}
