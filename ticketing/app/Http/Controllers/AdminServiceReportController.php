<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceReport;
use App\Models\Technician;
use App\Models\Ticket;
use Illuminate\Http\Request;

class AdminServiceReportController extends Controller
{
    public function index(Request $request)
    {
        $service_reports = ServiceReport::with('technician.user')->get();
        return inertia('Admin/Reports/ServiceReports/Index', [
            'service_reports' => $service_reports,
        ]);
    }

    public function create()
    {

        $latest_report = ServiceReport::orderBy('created_at', 'desc')->first();
        $new_service_id = $latest_report ? $this->incrementServiceId($latest_report->service_id) : '0001';
        $technicians = Technician::with('user')->get();
        $services = Service::all();
        $tickets = Ticket::with('technician.user', 'employee.user')->get();
        return inertia('Admin/Reports/ServiceReports/Create', [
            'technicians' => $technicians,
            'new_service_id' => $new_service_id,
            'services' => $services,
            'tickets' => $tickets,
        ]);
    }

    private function incrementServiceId($currentServiceId)
    {
        $numericPart = (int)$currentServiceId;
        $newNumericPart = $numericPart + 1;
        $newNumericPartFormatted = sprintf('%04d', $newNumericPart);
        return $newNumericPartFormatted;
    }

    public function check_service_id(Request $request, $service_id)
    {
        $exists = ServiceReport::where('service_id', $service_id)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required',
            'date_started' => 'required',
            'time_started' => 'required',
            'ticket_number' => 'required',
            'technician' => 'required',
            'requesting_office' => 'required',
            'equipment_no' => 'required',
            'issue' => 'required',
            'action' => 'required',
            'recommendation' => 'required',
            'date_done' => 'required',
            'time_done' => 'required',
            'remarks' => 'nullable',
        ]);

        $technician = Technician::where('technician_id', $request->technician)->firstOrFail();

        $serviceData = [
            'service_id' => $request->service_id,
            'date_started' => $request->date_started,
            'time_started' => $request->time_started,
            'ticket_number' => $request->ticket_number,
            'technician' => $technician->technician_id,
            'requesting_office' => $request->requesting_office,
            'equipment_no' => $request->equipment_no,
            'issue' => $request->issue,
            'action' => $request->action,
            'recommendation' => $request->recommendation,
            'date_done' => $request->date_done,
            'time_done' => $request->time_done,
            'remarks' => $request->remarks,
        ];

        ServiceReport::create($serviceData);

        return redirect()->to('/admin/reports/service-report')->with('success', 'Report Created');
    }
}
