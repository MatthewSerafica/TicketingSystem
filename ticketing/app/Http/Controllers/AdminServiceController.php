<?php

namespace App\Http\Controllers;

use App\Events\ServiceCreated;
use App\Http\Requests\ServiceRequest;
use App\Models\Log;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminServiceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $request->validate([
                'search' => 'nullable|string|max:255',
            ]);

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
        } catch (ValidationException $e) {
            return inertia('Admin/Services/Index', [
                'error' => $e->validator->errors()->all(),
            ]);
        }
    }

    public function create()
    {
        return inertia('Admin/Services/Create');
    }

    public function store(ServiceRequest $request)
    {

        $serviceData = $request->only('service');

        DB::beginTransaction();
        try {
            $service = Service::create($serviceData);
            event(new ServiceCreated($service));
            $this->logAction($service->service, "Added a new service");
            return redirect()->to('/admin/services')->with('success', 'Services Created!')->with('message', $request->service . ' is added to the Services!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create service!')->with('message', $e->getMessage());
        }
    }

    public function update(ServiceRequest $request, $service_id)
    {

        $service = Service::findOrFail($service_id);
        $old_service = $service->service;

        DB::beginTransaction();
        try {

            $service->service = $request->service;
            $service->save();
            $message = "Updated " . $old_service . " service to " . $request->service;
            $this->logAction($service->service, $message);

            return redirect()->back()->with('success', 'Service Updated!')->with('message', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create service!')->with('message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $service = Service::findOrFail($id);
            $name = $service->service;
            $service->delete();

            $message = "Deleted " . $name . " service";
            $this->logAction($service->service, $message);

            return redirect()->back()->with('success', 'Service Deleted!')->with('message', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create service!')->with('message', $e->getMessage());
        }
    }

    private function logAction($service, $action)
    {
        $auth = Auth::user();
        if ($auth) {
            Log::create([
                'name' => $auth->name,
                'user_type' => $auth->user_type,
                'actions_taken' => $action . ': ' . $service,
            ]);
        } else {
            Log::create([
                'name' => request()->ip(),
                'user_type' => request()->header('User-Agent'),
                'actions_taken' => $action . ': ' . $service,
            ]);
            return redirect()->back()->with('error', 'Attempt to log action without authenticated user');
        }
    }
}
