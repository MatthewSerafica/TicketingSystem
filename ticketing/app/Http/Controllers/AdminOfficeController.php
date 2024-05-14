<?php

namespace App\Http\Controllers;

use App\Events\OfficeCreated;
use App\Http\Requests\OfficeRequest;
use App\Models\Log;
use App\Models\Office;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminOfficeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $request->validate([
                'search' => 'nullable|string|max:255',
            ]);

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
        } catch (ValidationException $e) {
            return inertia('Admin/Office/Index', [
                'error' => $e->validator->errors()->all(),
            ]);
        }
    }

    public function create()
    {
        return inertia('Admin/Office/Create');
    }

    public function store(OfficeRequest $request)
    {
        $officeData = $request->only('office');

        DB::beginTransaction();
        try {
            $office = Office::create($officeData);
            event(new OfficeCreated($office));
            $this->logAction($office->office, "Added a new office");

            DB::commit();
            return redirect()->to('/admin/office')->with('success', 'Office Created!')->with('message', $request->office . ' is added to the offices!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create office!')->with('message', $e->getMessage());
        }
    }

    public function update(OfficeRequest $request, $office_id)
    {
        $office = Office::findOrFail($office_id);
        $old_office = $office->office;

        DB::beginTransaction();
        try {
            $office->office = $request->office;
            $office->save();
            $message = "Updated " . $old_office . " office to " . $request->office;
            $this->logAction($office->office, $message);

            DB::commit();
            return redirect()->back()->with('success', 'Office Updated!')->with('message', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update office!')->with('message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $office = Office::findOrFail($id);
            $name = $office->office;
            $office->delete();

            $message = "Deleted " . $name . " office";
            $this->logAction($office->office, $message);

            DB::commit();
            return redirect()->back()->with('success', 'Office Deleted!')->with('message', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update office!')->with('message', $e->getMessage());
        }
    }
    private function logAction($office, $action)
    {
        $auth = Auth::user();
        if ($auth) {
            Log::create([
                'name' => $auth->name,
                'user_type' => $auth->user_type,
                'actions_taken' => $action . ': ' . $office,
            ]);
        } else {
            Log::create([
                'name' => request()->ip(),
                'user_type' => request()->header('User-Agent'),
                'actions_taken' => $action . ': ' . $office,
            ]);
            return redirect()->back()->with('error', 'Attempt to log action without authenticated user');
        }
    }
}
