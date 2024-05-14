<?php

namespace App\Http\Controllers;

use App\Events\DepartmentCreated;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use App\Models\Log;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminDepartmentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $request->validate([
                'search' => 'nullable|string|max:255',
            ]);

            $departments = Department::query()
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = $request->input('search');
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhere('department', 'like', '%' . $search . '%');
                })
                ->orderByDesc('id')
                ->paginate(10);
            $filters = $request->only(['search']);
            
            return inertia('Admin/Department/Index', [
                'departments' => $departments,
                'filters' => $filters,
            ]);
        } catch (ValidationException $e) {
            return inertia('Admin/Department/Index', [
                'error' => $e->validator->errors()->all(),
            ]);
        }
    }

    public function create()
    {
        return inertia('Admin/Department/Create');
    }

    public function store(DepartmentRequest $request)
    {

        $departmentData = $request->only('department');

        DB::beginTransaction();
        try {
            $department = Department::create($departmentData);
            event(new DepartmentCreated($department));
            $this->logAction($department->department, "Added a new department");

            DB::commit();
            return redirect()->to('/admin/department')->with('success', 'Department Created!')->with('message', $request->department . ' is added to the departments!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create department!')->with('message', $e->getMessage());
        }
    }

    public function update(DepartmentRequest $request, $department_id)
    {
        $department = Department::findOrFail($department_id);
        $old_department = $department->department;
        DB::beginTransaction();
        try {
            $department->update(['department' => $request->department]);
            $message = "Updated " . $old_department . " department to " . $request->department;
            $this->logAction($department->department, $message);

            DB::commit();
            return redirect()->back()->with('success', 'Department Updated!')->with('message', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update department!')->with('message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $department = Department::findOrFail($id);
            $name = $department->department;
            $department->delete();
            
            $message = "Deleted " . $name . " department";
            $this->logAction($department->department, $message);

            DB::commit();
            return redirect()->back()->with('success', 'Department Deleted!')->with('message', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update department!')->with('message', $e->getMessage());
        }
    }

    private function logAction($departmentName, $action)
    {
        $auth = Auth::user();
        if ($auth) {
            Log::create([
                'name' => $auth->name,
                'user_type' => $auth->user_type,
                'actions_taken' => $action . ': ' . $departmentName,
            ]);
        } else {
            Log::create([
                'name' => request()->ip(),
                'user_type' => request()->header('User-Agent'),
                'actions_taken' => $action . ': ' . $departmentName,
            ]);
            return redirect()->back()->with('error', 'Attempt to log action without authenticated user');
        }
    }
}
