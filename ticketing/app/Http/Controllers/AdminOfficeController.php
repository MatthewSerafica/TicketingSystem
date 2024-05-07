<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOfficeController extends Controller
{
    public function index(Request $request)
    {
        $offices = Office::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('office', 'like', '%' . $search . '%');
            })
            ->orderByDesc('id')
            ->paginate(10);
        $filters = $request->only(['search']);
        return inertia('Admin/Office/Index', [
            'offices' => $offices,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $offices = Office::get();
        return inertia('Admin/Office/Create', [
            'offices' => $offices,
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'office' => 'required',
        ]);


        $officeData = [
            'office' => $request->office,
        ];

        Office::create($officeData);

        $auth = Auth::user();
        $action_taken = "Added a new office " .  $request->office;
        $log_data = [
            'name' => $auth->name,
            'user_type' => $auth->user_type,
            'actions_taken' => $action_taken,
        ];
        Log::create($log_data);

        return redirect()->to('/admin/office')->with('success', 'Office Created!')->with('message', $request->office . ' is added to the offices!');
    }

    public function update(Request $request, $office_id)
    {
        $request->validate([
            'office' => 'required',
        ]);

        $office = Office::findOrFail($office_id);
        $old_office = $office->office;
        $office->office = $request->office;
        $office->save();

        $auth = Auth::user();
        $action_taken = "Updated an office from " . $old_office . " to " .  $request->office;
        $log_data = [
            'name' => $auth->name,
            'user_type' => $auth->user_type,
            'actions_taken' => $action_taken,
        ];
        Log::create($log_data);

        return redirect()->back()->with('success', 'Office Updated!')->with('message', $old_office . ' is updated to ' . $request->office);
    }

    public function destroy($id)
    {
        $office = Office::findOrFail($id);
        $name = $office->office;
        $office->delete();
        
        $auth = Auth::user();
        $action_taken = "Removed " . $name . " office";
        $log_data = [
            'name' => $auth->name,
            'user_type' => $auth->user_type,
            'actions_taken' => $action_taken,
        ];
        Log::create($log_data);

        return redirect()->back()->with('success', 'Office Deleted!')->with('message', $name . ' has been deleted from offices');
    }
}
