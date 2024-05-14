<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Service;
use App\Models\ServiceReport;
use App\Models\Technician;
use App\Models\Ticket;


class ObserverServiceController extends Controller
{
    public function index(Request $request)
    {
        $service_reports = ServiceReport::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('service_id', 'like', '%' . $search . '%')
                    ->orWhere('ticket_number', 'like', '%' . $search . '%')
                    ->orWhere('requesting_office', 'like', '%' . $search . '%')
                    ->orWhere('equipment_no', 'like', '%' . $search . '%')
                    ->orWhere('issue', 'like', '%' . $search . '%')
                    ->orWhere('technician', 'like', '%' . $search . '%');
            })
            ->orderBy('service_id', 'desc')
            ->paginate(10);

        $filters = $request->only(['search']);
        return inertia('Observer/Reports/ServiceReports/Index', [
            'service_reports' => $service_reports,
            'filters' => $filters,
        ]);
    }
    public function show($id)
    {
        $service_report = ServiceReport::where('service_id', $id)->first();
        $technicians = Technician::with('user')->get();
        $services = Service::all();
        $tickets = Ticket::whereNull('sr_no')
            ->orWhere('sr_no', $id)
            ->orWhere('sr_no', '')
            ->with('employee.user', 'assigned.technician.user')
            ->get();
        return inertia('Observer/Reports/ServiceReports/Show', [
            'technicians' => $technicians,
            'service_report' => $service_report,
            'services' => $services,
            'tickets' => $tickets,
        ]);
    }

}
