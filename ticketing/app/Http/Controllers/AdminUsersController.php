<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


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
use Illuminate\Validation\Rule;

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
            'name' => ['required', 'regex:/^\S+(?: \S+)+$/'],
            'email' => ['required', Rule::unique('users')],
            'password' => 'required',
            'department' => 'nullable',
            'office' => 'nullable',
            'assigned' => 'nullable',
        ]);

        try {
            $nameParts = explode(' ', $request->name, 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            $user = User::create([
                'name' => $request->name,
                'first_name' => $firstName,
                'last_name' => $lastName,
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
            return redirect(route('admin.users'))->with('success', 'User created!');
        } catch (QueryException $e) {
            DB::rollback();
            if ($e->errorInfo[1] === 1062) {
                return redirect()->back()->withInput()->withErrors(['email' => 'The email has already been taken.']);
            }
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while creating the user.']);
        }
    }

    public function show($id)
    {
        $user = User::where('id', $id)->with('employee', 'technician')->firstOrFail();
        $departments = Department::all();
        $offices = Office::all();
        return inertia('Admin/Users/Show', [
            'user' => $user,
            'departments' => $departments,
            'offices' => $offices,
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

    public function email(Request $request, $id)
    {
        $request->validate([
            'email' => 'required',
        ]);

        $user = User::where('id', $id)->first();
        $old = $user->email;
        $user->email = $request->email;
        $user->save();
        return redirect()->back()->with('success', 'Name Update!')->with('message', $old . ' is updated to ' . $request->email);
    }

    public function update(Request $request, $id, $field)
    {
        $request->validate([
            $field => 'required',
        ]);

        $user = User::where('id', $id)->first();
        $old = $user->$field;
        $user->$field = $request->$field;
        $user->save();
        $input = ucfirst($field);

        return redirect()->back()->with('success', $input . ' Update!')->with('message', $old . ' is updated to ' . $request->$field);
    }

    public function employee(Request $request, $id, $field)
    {
        $request->validate([
            $field => 'required',
        ]);

        $employee = Employee::where('employee_id', $id)->first();
        $old = $employee->$field;
        $employee->$field = $request->$field;
        $employee->save();
        $input = ucfirst($field);

        return redirect()->back()->with('success', $input . ' Update!')->with('message', $old . ' is updated to ' . $request->$field);
    }

    public function technician(Request $request, $id, $field)
    {
        $request->validate([
            $field => 'required',
        ]);

        $technician = Technician::where('technician_id', $id)->first();
        $old = $technician->$field;
        $technician->$field = $request->$field;
        $technician->save();
        $input = ucfirst($field);

        return redirect()->back()->with('success', $input . ' Update!')->with('message', $old . ' is updated to ' . $request->$field);
    }

    public function destroy($id)
    {
        // Add logic here to delete user
    }

    public function password()
    {
        $user = Auth::user();
        return inertia('Admin/Users/Change', [
            'user' => $user,
        ]);
    }

    public function changePassword(Request $request, $userId)
    {
        try {
            $request->validate([
                'oldPassword' => 'required',
                'newPassword' => 'required|min:8',
                'confirmPassword' => 'required',
            ]);

            $user = User::where('id', $userId)->first();


            if (!Hash::check($request->oldPassword, $user->password)) {
                return redirect()->back()->with('error', 'Old Password does not match');
            }

            if ($request->newPassword !== $request->confirmPassword) {
                return redirect()->back()->with('error', 'New Password do not match');
            }

            $newPassword = $request->newPassword;

            $user->password = $newPassword;
            $user->save();

            return inertia('Admin/Users/Show', [
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            return back()->with('error', 'Failed to update user.')->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while updating the user.')->withInput();
        }
    }
}
