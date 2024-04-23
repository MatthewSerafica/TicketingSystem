<?php

namespace App\Http\Controllers;

use App\Models\AssignedTickets;
use App\Models\Employee;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\ServiceReport;
use App\Models\User;
use App\Notifications\ResolvedTicket;
use App\Notifications\UpdateTicketStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TechnicianServiceController extends Controller
{
    public function index(Request $request)
    {
        $user_id = auth()->id();
        $technician = Technician::where('user_id', $user_id)->with('user')->first();

        $service_report = ServiceReport::query()
            ->where('technician', 'like', '%' . $technician->user->name . '%')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('service_id', 'like', '%' . $search . '%')
                    ->orWhere('ticket_number', 'like', '%' . $search . '%')
                    ->orWhere('requesting_office', 'like', '%' . $search . '%')
                    ->orWhere('equipment_no', 'like', '%' . $search . '%')
                    ->orWhere('issue', 'like', '%' . $search . '%')
                    ->orWhere('action', 'like', '%' . $search . '%')
                    ->orWhere('recommendation', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%')
                    ->orWhereHas('technician.user', function ($subquery) use ($search) {
                        $subquery->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->orderBy('service_id')
            ->paginate(10);
        $filters = $request->only(['search']);
        $tickets = Ticket::all();
        return inertia('Technician/ServiceReports/Index', [
            'service_report' => $service_report,
            'filters' => $filters,
            'tickets' => $tickets,
        ]);
    }

    public function create()
    {
        $latest_report = ServiceReport::orderBy('created_at', 'desc')->first();
        $new_service_id = $latest_report ? $this->incrementServiceId($latest_report->service_id) : '0001';
        if ($latest_report) {
            $new_service_id = $this->incrementServiceId($latest_report->service_id);
            $ticket_id = $latest_report->ticket_number;
            $date_done = $latest_report->date_done;
            $problem = $latest_report->issue;
        } else {
            $new_service_id = '0001';
            $ticket_id = null;
            $date_done = null;
        }
        $tickets = Ticket::with('employee.user')->get();
        $technicians = Technician::all();
        return inertia('Technician/ServiceReports/Create', [
            'technicians' => $technicians,
            'new_service_id' => $new_service_id,
            'tickets' => $tickets,
            'ticket_id' => $ticket_id ? $ticket_id : null,
            'date_done' => $date_done,
            'problem'=> $problem ? $problem : null,

        ]);
    }

    private function incrementServiceId($current_service_id)
    {
        // Check if a service report with the given service_id exists
        $exists = ServiceReport::where('service_id', $current_service_id)->exists();

        // If a service report with the given service_id exists and date_started is not null
        if ($exists) {
            $latest_report = ServiceReport::where('service_id', $current_service_id)->latest()->first();
            if ($latest_report->date_started !== null) {
                $numeric_part = (int)$current_service_id;
                $new_numeric = $numeric_part + 1;
                $new_numeric_formatted = sprintf('%04d', $new_numeric);
            } else {
                $new_numeric_formatted = $current_service_id;
            }
        } else {
            // If no service report with the given service_id exists, maintain the same service_id
            $new_numeric_formatted = $current_service_id;
        }

        return $new_numeric_formatted;
    }


    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required',
            'date_started' => 'required',
            'time_started' => 'required',
            'ticket_number' => 'nullable',
            'technician' => 'required',
            'requesting_office' => 'nullable',
            'equipment_no' => 'nullable',
            'issue' => 'nullable',
            'action' => 'nullable',
            'recommendation' => 'nullable',
            'date_done' => 'required',
            'time_done' => 'required',
            'remarks' => 'nullable',
        ]);

        $service_id = $request->service_id;

        // Check if a ServiceReport with the given service_id exists
        $existingServiceReport = ServiceReport::where('service_id', $service_id)->first();

        // If a ServiceReport with the given service_id exists, update it
        if ($existingServiceReport) {
            $existingServiceReport->update([
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
            ]);
        } else {
            $serviceData = [
                'service_id' => $service_id,
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
        }

        $ticket = Ticket::where('ticket_number', $request->ticket_number)->first();
        $ticket->update([
            'sr_no' =>  $request->service_id,
            'remarks' => $request->remarks,
            'status' => 'Resolved',
            'resolved_at' => $request->date_done,
        ]);
        /*  $ticket->save(); */
        $user_id = auth()->id();
        $technician = Technician::where('user_id', $user_id)->with('user')->first();
        $technician->tickets_resolved = $technician->tickets_resolved + 1;
        $technician->tickets_assigned = $technician->tickets_assigned - 1;
        $technician->save();

        $employee = Employee::find($ticket->employee);
        $employee->user->notify(new UpdateTicketStatus($ticket));
        $admins = User::where('user_type', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(
                new ResolvedTicket($ticket)
            );
        }
        return redirect()->to('/technician/service-report')->with('success', 'Report Created');
    }

    public function assigned($id)
    {
        $assigned = AssignedTickets::where('ticket_number', $id)->with('technician.user')->get();
        return response()->json($assigned);
    }

    public function back(Request $request, $service_id)
    {

        // Find the existing ServiceReport with the given service_id
        $existingServiceReport = ServiceReport::where('service_id', $service_id)->first();

        if ($existingServiceReport) {
            // Find the related Ticket using the ticket_number from ServiceReport
            $ticket = Ticket::where('ticket_number', $existingServiceReport->ticket_number)->first();

            // Update Ticket values or set them to null as needed
            $ticket->update([
                'sr_no' => null, // Set sr_no to null or delete it from the ticket table
                'remarks' => null, // Set remarks to null or delete it from the ticket table
                'status' => 'Pending',
                'resolved_at' =>null,
            ]);

            // Delete the ServiceReport
            $existingServiceReport->delete();

            return redirect()->to('/technician/service-report');
        } else {
            return redirect()->to('/technician/service-report');
        }
    }


}
