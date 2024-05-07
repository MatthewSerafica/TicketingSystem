<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('service', 'like', '%' . $search . '%');
            })
            ->orderByDesc('id')
            ->paginate(10);
        $filters = $request->only(['search']);
        return inertia('Admin/Services/Index', [
            'services' => $services,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $services = Service::get();
        return inertia('Admin/Services/Create', [
            'services' => $services,
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'service' => 'required',
        ]);


        $serviceData = [
            'service' => $request->service,
        ];

        Service::create($serviceData);

        $auth = Auth::user();
        $action_taken = "Added a new service " .  $request->service;
        $log_data = [
            'name' => $auth->name,
            'user_type' => $auth->user_type,
            'actions_taken' => $action_taken,
        ];
        Log::create($log_data);

        return redirect()->to('/admin/services')->with('success', 'Services Created!')->with('message', $request->service . ' is added to the Services!');
    }

    public function update(Request $request, $service_id)
    {
        $request->validate([
            'service' => 'required',
        ]);

        $service = Service::findOrFail($service_id);
        $old_service = $service->service;
        $service->service = $request->service;
        $service->save();

        $auth = Auth::user();
        $action_taken = "Updated a service from " . $old_service . " to " .  $request->service;
        $log_data = [
            'name' => $auth->name,
            'user_type' => $auth->user_type,
            'actions_taken' => $action_taken,
        ];
        Log::create($log_data);

        return redirect()->back()->with('success', 'Service Updated!')->with('message', $old_service . ' is updated to ' . $request->service);
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $name = $service->service;
        $service->delete();

        $auth = Auth::user();
        $action_taken = "Removed " . $name . " service";
        $log_data = [
            'name' => $auth->name,
            'user_type' => $auth->user_type,
            'actions_taken' => $action_taken,
        ];
        Log::create($log_data);

        return redirect()->back()->with('success', 'Service Deleted!')->with('message', $name . ' has been deleted from services');
    }
}