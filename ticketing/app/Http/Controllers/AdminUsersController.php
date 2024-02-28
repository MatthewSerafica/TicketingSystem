<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Office;
use App\Models\Technician;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminUsersController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()->with('technician', 'employee')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('user_type', 'like', '%' . $search . '%')
                    ->orWhereHas('employee', function ($subquery) use ($search) {
                        $subquery->where('department', 'like', '%' . $search . '%');
                    });
            })
            ->when($request->filled('filterUsers'), function ($query) use ($request) {
                $userFilter = $request->input('filterUsers');
                if ($userFilter === 'employee') {
                    $query->where('user_type', 'like', '%' . $userFilter . '%');
                } elseif ($userFilter === 'technician') {
                    $query->where('user_type', 'like', '%' . $userFilter . '%');
                }
            })
            ->paginate(10);

        $filters = $request->only(['search']);

        return inertia('Admin/Users/Index', [
            'users' => $users,
            'filter' => $filters,
        ]);
    }


    public function create()
    {
        $departments = Department::all();
        $offices = Office::all();
        return inertia('Admin/Users/Create', [
            'departments' => $departments,
            'offices' => $offices,
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $request->validate([
            'user_type' => 'required',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'department' => 'nullable',
            'office' => 'nullable',
            'assigned' => 'nullable',
        ]);

        $user = User::create([
            'name' => $request->name,
            'user_type' => $request->user_type,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($request->user_type == 'employee') {
            Employee::create([
                'user_id' => $user->id,
                'department' => $request->department,
                'office' => $request->office,
            ]);
        } else {
            Technician::create([
                'user_id' => $user->id,
                'assigned_department' => $request->assigned
            ]);
        }
        DB::commit();
        return redirect(route('admin.users'))->with('success', 'User created')->with('message', $request->user_type . ' ' . $request->name . ' account has been created!');
    }

    public function show($id)
    {
        $user = User::where('id', $id)->with('employee', 'technician')->firstOrFail();;
        return inertia('Admin/Users/Show', [
            'user' => $user,
        ]);
    }

    public function name(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $user = User::where('id', $id)->first();
        $old_name = $user->name;
        $user->name = $request->name;
        $user->save();
        return redirect()->back()->with('success', 'Name Update!')->with('message', $old_name . ' is updated to ' . $request->name);
    }

    public function destroy($id)
    {
        // Add logic here to delete user
    }
}
