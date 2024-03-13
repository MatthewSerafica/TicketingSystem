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
        $user_id = auth()->id();
        $technician = Technician::where('user_id', $user_id)->first();

        $service_report = ServiceReport::query() 
            ->with('technician.user')
            ->where('technician', $technician->technician_id)
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
            ->paginate(10);
            
        $filters = $request->only(['search']);
        return inertia('Technician/ServiceReports/Index', [
            'service_report' => $service_report,
            'filters' => $filters,
        ]);
    }
    
    public function create()
    {
        $latest_report = ServiceReport::orderBy('created_at', 'desc')->first();
        $new_service_id = $latest_report ? $this->incrementServiceId($latest_report->service_id) : '0001';
        $tickets = Ticket::with('employee.user')->get();
        $technicians = Technician::all();
        return inertia('Technician/ServiceReports/Create', [
            'technicians' => $technicians,
            'new_service_id' => $new_service_id,
            'tickets' => $tickets,
        ]);
    }

    private function incrementServiceId($currentServiceId)
    {
        // Check if a service report with the given service_id exists
        $exists = ServiceReport::where('service_id', $currentServiceId)->exists();
        
        // If a service report with the given service_id exists and date_started is not null
        if ($exists) {
            $latestReport = ServiceReport::where('service_id', $currentServiceId)->latest()->first();
            if ($latestReport->date_started !== null) {
                $numericPart = (int)$currentServiceId;
                $newNumericPart = $numericPart + 1;
                $newNumericPartFormatted = sprintf('%04d', $newNumericPart);
            } else {
                $newNumericPartFormatted = $currentServiceId;
            }
        } else {
            // If no service report with the given service_id exists, maintain the same service_id
            $newNumericPartFormatted = $currentServiceId;
        }
        
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
            // Create a new ServiceReport if the service_id does not exist
            $technician = Technician::where('user_id', $request->technician)->firstOrFail();
    
            $serviceData = [
                'service_id' => $service_id,
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
        }
    
        return redirect()->to('/technician/service-report')->with('success', 'Report Created');
    }
    


}
