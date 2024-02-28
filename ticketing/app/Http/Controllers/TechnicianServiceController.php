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
                $query->where('service_id', 'like', '%' . $search . '%')
                ->orWhereHas('technician.user', function ($subquery) use ($search) {
                    $subquery->where('name', 'like', '%' . $search . '%');
                });
            })
        
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderBy('ticket_number')
            ->get();
        $technicians = Technician::with('user')->get();
        return inertia('Technician/ServiceReports/Index', [
            'service_report' => $service_report,
            'technicians' => $technicians,
        ]);
    }
    
    public function create()
    {
        $latest_report = ServiceReport::orderBy('created_at', 'desc')->first();
        $new_service_id = $latest_report ? $this->incrementServiceId($latest_report->service_id) : 'SR-B 0001';
        $technicians = Technician::all();
        return inertia('Technician/ServiceReports/Create', [
            'technicians' => $technicians,
            'new_service_id' => $new_service_id,
        ]);
    }

    private function incrementServiceId($currentServiceId)
    {
        $numericPart = (int)substr($currentServiceId, 5);
        $newNumericPart = $numericPart + 1;
        $newNumericPartFormatted = sprintf('%04d', $newNumericPart);
        $newServiceId = 'SR-B ' . $newNumericPartFormatted;
        return $newServiceId;
    }




    public function store(Request $request) 
    {
        $request->validate([
            'service_id' => 'required',
            'date_started' => 'required',
            'time_started' => 'required',
            'ticket_number' => 'required',
            'technician_name' => 'required',
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
            'service_id' => $request -> service_id,
            'date_started' => $request -> date_started,
            'time_started' => $request -> time_started,
            'ticket_number' => $request -> ticket_number,
            'technician_name' => $request -> technician_name,
            'requesting_office' => $request -> requesting_office,
            'equipment_no' => $request -> equipment_no,
            'issue' => $request -> issue,
            'action' => $request -> action,
            'recommendation' => $request -> recommendation,
            'date_done' => $request -> date_done,
            'time_done' => $request -> time_done,
            'remarks' => $request -> remarks,
        ];

        ServiceReport::create($serviceData);

        return redirect()->to('/technician/service-report')->with('success', 'Report Created');
    }

    public function check_service_id(Request $request, $service_id)
    {
        // Check if the service_id already exists in the database
        $exists = ServiceReport::where('service_id', $service_id)->exists();

        return response()->json(['exists' => $exists]);
    }
}
