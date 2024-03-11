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
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'department' => 'nullable',
            'office' => 'nullable',
            'assigned' => 'nullable',
        ]);

        try {
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
            ]);

            $user = User::where('id', $userId)->first();

            if (!Hash::check($request->oldPassword, $user->password)) {
                return redirect()->back()->with('error', 'Old Password does not match');
            }

            $newPassword = $request->newPassword;
            $user->password = $newPassword;
            $user->save();

            return redirect()->back()->with('success', 'Password changed successfully');

            if (session('error') === null) {
                return redirect()->route('admin')->with('success', 'Password changed successfully');
            }
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            return back()->with('error', 'Failed to update user.')->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while updating the user.')->withInput();
        }
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $request->file('file')->getRealPath();
        $file = fopen($path, 'r');

        fgetcsv($file);

        try {
            $employees = [];
            $technicians = [];
            $chunkSize = 1000;
            DB::beginTransaction();

            while (($line = fgetcsv($file)) !== false) {
                $hashed_default_password = Hash::make('user!test');

                $user = User::create([
                    'name' => $line[0],
                    'email' => $line[1],
                    'user_type' => $line[2],
                    'password' => $hashed_default_password
                ]);

                if ($line[2] == 'employee') {
                    $employees[] = [
                        'user_id' => $user->id,
                        'department' => $line[3],
                        'office' => $line[4],
                    ];
                    if (count($employees) >= $chunkSize) {
                        $this->insertChunk($employees);
                        $employees = [];
                    }
                } else if ($line[2] == 'technician') {
                    $technicians[] = [
                        'user_id' => $user->id,
                        'assigned_department' => $line[5],
                    ];
                    if (count($technicians) >= $chunkSize) {
                        $this->insertChunkt($technicians);
                    }
                }
            }
            $this->insertChunk($employees);
            $this->insertChunkt($technicians);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while creating the records. Some fields are maybe empty or invalid.');
        }
        return redirect()->to(route('admin.users'))->with('success', 'Users added successfully');
    }

    private function insertChunk($employees)
    {
        Employee::insert($employees);
        $userIds = User::whereIn('id', array_column($employees, 'user_id'))->pluck('id', 'id')->toArray();

        $employees = array_map(function ($employee) use ($userIds) {
            $employee['user_id'] = $userIds[$employee['user_id']];
            return $employee;
        }, $employees);
    }

    private function insertChunkt($technicians)
    {
        Technician::insert($technicians);
        $userIds = User::whereIn('id', array_column($technicians, 'user_id'))->pluck('id', 'id')->toArray();

        $technicians = array_map(function ($technician) use ($userIds) {
            $technician['user_id'] = $userIds[$technician['user_id']];
            return $technician;
        }, $technicians);
    }
}
