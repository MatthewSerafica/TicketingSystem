<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\Ticket;
use App\Models\ServiceReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TechnicianServiceController extends Controller
{
    public function index(Request $request)
    {   
        $service_report = ServiceReport::query() 
            -> with('technician.user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('ticket_number', 'like', '%' . $search . '%')
                ->orWhereHas('technician.user', function ($subquery) use ($search) {
                    $subquery->where('name', 'like', '%' . $search . '%');
                });
            })
        
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderBy('ticket_number')
            ->get();
        $filter = $request->only(['search']);
        $technicians = Technician::with('user')->get();
        return inertia('Technician/ServiceReports/Index', [
            'service_report' => $service_report,
            'technicians' => $technicians,
        ]);
    }
    
    public function create()
    {
        return inertia('Technician/ServiceReports/Create');
    }
}
