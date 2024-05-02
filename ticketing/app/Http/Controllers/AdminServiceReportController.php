<?php

namespace App\Http\Controllers;

use App\Models\AssignedTickets;
use App\Models\Employee;
use App\Models\HistoryNumber;
use App\Models\Service;
use App\Models\ServiceReport;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\UpdateTicketStatus;
use Illuminate\Http\Request;

class AdminServiceReportController extends Controller
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
        return inertia('Admin/Reports/ServiceReports/Index', [
            'service_reports' => $service_reports,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $latest_report = ServiceReport::orderBy('created_at', 'desc')->first();
        $new_service_id = $latest_report ? $this->incrementServiceId($latest_report->service_id) : '0001';
        $technicians = Technician::with('user')->get();
        $services = Service::all();
        $tickets = Ticket::whereNull('sr_no')
            ->orWhere('sr_no', '')
            ->with('employee.user', 'assigned.technician.user')
            ->get();
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

        $serviceData = [
            'service_id' => $request->service_id,
            'date_started' => $request->date_started,
            'time_started' => $request->time_started,
            'ticket_number' => $request->ticket_number,
            'technician' => $request->technician,
            'requesting_office' => $request->requesting_office,
            'equipment_no' => $request->equipment_no,
            'issue' => $request->issue,
            'action' => $request->action,
            'recommendation' => $request->recommendation,
            'date_done' => $request->date_done,
            'time_done' => $request->time_done,
            'remarks' => $request->remarks,
        ];

        $service = ServiceReport::create($serviceData);

        $ticket = Ticket::where('ticket_number', $request->ticket_number)->first();

        $ticket->update([
            'sr_no' => $service->service_id,
            'status' => 'Resolved',
            'remarks' => $request->remarks,
        ]);

        $employee = Employee::find($ticket->employee);
        $employee->user->notify(new UpdateTicketStatus($ticket));
        
        $history = HistoryNumber::where('ticket_number', $request->ticket_number)->first();
        $history->sr_no = $request->service_id;
        $history->save();


        $technicians = explode(' / ', $request->technician);
        foreach ($technicians as $technicianName) {
            $user = User::where('name', $technicianName)->first();
            if ($user) {
                $technician = Technician::where('user_id', $user->id)->first();
                $technician->tickets_assigned = $technician->tickets_assigned - 1;
                $technician->tickets_resolved = $technician->tickets_resolved + 1;
                $technician->save();
                if ($technician) {
                    $technician->user->notify(new UpdateTicketStatus($ticket));
                }
            }
        }

        return redirect()->to('/admin/reports/service-report')->with('success', 'Report Created');
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
        return inertia('Admin/Reports/ServiceReports/Show', [
            'technicians' => $technicians,
            'service_report' => $service_report,
            'services' => $services,
            'tickets' => $tickets,
        ]);
    }

    public function update(Request $request, $id) {
        $service_report = ServiceReport::where('service_id', $id)->first();

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

        $serviceData = [
            'service_id' => $request->service_id,
            'date_started' => $request->date_started,
            'time_started' => $request->time_started,
            'ticket_number' => $request->ticket_number,
            'technician' => $request->technician,
            'requesting_office' => $request->requesting_office,
            'equipment_no' => $request->equipment_no,
            'issue' => $request->issue,
            'action' => $request->action,
            'recommendation' => $request->recommendation,
            'date_done' => $request->date_done,
            'time_done' => $request->time_done,
            'remarks' => $request->remarks,
        ];

        $service_report->update($serviceData);

        $ticket = Ticket::where('ticket_number', $request->ticket_number)->first();

        $ticket->update([
            'sr_no' => $request->service_id,
            'status' => 'Resolved',
            'remarks' => $request->remarks,
        ]);


        return redirect()->to(route('admin.reports.service-reports'))->with('success', 'Service Report updated successfully!');
    }


    public function assigned($id)
    {
        $assigned = AssignedTickets::where('ticket_number', $id)->with('technician.user')->get();
        return response()->json($assigned);
    }
}
