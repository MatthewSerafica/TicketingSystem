<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


use App\Models\Department;
use App\Models\Employee;
use App\Models\Office;
use App\Models\ServiceReport;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminUsersController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query()->with('technician', 'employee')
            ->whereNot('user_type', 'admin');

        // Retrieve filter state from query parameters
        $filter = $request->only(['search', 'filterUsers', 'all', 'employee', 'technician']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('user_type', 'like', '%' . $search . '%')
                    ->orWhereHas('employee', function ($subquery) use ($search) {
                        $subquery->where('department', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('filterUsers')) {
            // Apply user type filter
            $userFilter = $request->input('filterUsers');
            if ($userFilter === 'employee' || $userFilter === 'technician') {
                $query->where('user_type', $userFilter);
            }
        }

        $users = $query->paginate(10);

        $users->appends($filter);

        // Save the filter in the session to maintain state across page requests
        $request->session()->put('filter', $filter);

        return inertia('Admin/Users/Index', [
            'users' => $users,
            'filter' => $filter, // Pass the filter state to the view
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
                return redirect()->back()->with('error', 'Error!')->with('message', 'Email already exists!');
            }
            return redirect()->back()->with('error', 'Error!')->with('message', 'An error occured while creating the user!');
        }
    }

    public function show($id)
    {
        $user = User::where('id', $id)->with('employee', 'technician')->firstOrFail();
        $departments = Department::all();
        $offices = Office::all();
        $yearly = $this->getYearlyData($user);
        $service = $this->getType($user);
        $assigned_today = $this->getAssignedToday($user);
        $resolved_today = $this->getResolvedToday($user);
        $time = $this->getAverageResolutionTime($user);
        $complexity = $this->getComplexityCounts($user);
        return inertia('Admin/Users/Show', [
            'users' => $user,
            'departments' => $departments,
            'offices' => $offices,
            'yearly' => $yearly,
            'service' => $service,
            'assigned_today' => $assigned_today,
            'resolved_today' => $resolved_today,
            'time' => $time,
            'complexity' => $complexity,
        ]);
    }

    private function getAssignedToday($user)
    {
        if ($user->user_type == 'technician') {
            $todayStart = Carbon::today();
            $todayEnd = Carbon::today()->endOfDay();
            $ticket = Ticket::with('assigned.technician.user')
                ->whereHas('assigned.technician.user', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->whereIn('status', ['New', 'Pending', 'Ongoing'])
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->count();
            return $ticket;
        }
    }
    private function getResolvedToday($user)
    {
        if ($user->user_type == 'technician') {
            $todayStart = Carbon::today();
            $todayEnd = Carbon::today()->endOfDay();
            $ticket = Ticket::with('assigned.technician.user')
                ->whereHas('assigned.technician.user', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->where('status', 'resolved')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->count();
            return $ticket;
        }
    }

    private function getComplexityCounts($user)
    {
        $ticketCounts = Ticket::with('assigned.technician.user')
            ->whereHas('assigned.technician.user', function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->selectRaw('complexity, COUNT(*) as count')
            ->groupBy('complexity')
            ->get();

        $counts = [
            'simple' => 0,
            'complex' => 0
        ];

        foreach ($ticketCounts as $ticket) {
            if ($ticket->complexity === 'Simple') {
                $counts['simple'] = $ticket->count;
            } elseif ($ticket->complexity === 'Complex') {
                $counts['complex'] = $ticket->count;
            }
        }

        return $counts;
    }

    private function getAverageResolutionTime($user)
    {
        $time = ServiceReport::where('technician', 'like', '%' . $user->name . '%')
            ->whereNotNull('date_done')
            ->whereNotNull('time_done')
            ->whereNotNull('date_started')
            ->whereNotNull('time_started')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, CONCAT(date_started, " ", time_started), CONCAT(date_done, " ", time_done))) AS average_resolution_time')
            ->value('average_resolution_time');

        $timeInHours = $time / 3600;

        $timeInDecimal = round($timeInHours, 2);

        return $timeInDecimal;
    }


    private function getYearlyData($user)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        if ($user->user_type == 'employee') {
            $yearly_data = Ticket::whereYear('created_at', Carbon::now()->year)
                ->where('employee', $user->employee->employee_id)
                ->get()
                ->groupBy(function ($ticket) {
                    return Carbon::parse($ticket->created_at)->format('M');
                })
                ->map(function ($grouped_tickets) {
                    return $grouped_tickets->count();
                });
        } else if ($user->user_type == 'technician') {
            $yearly_data = Ticket::whereYear('created_at', Carbon::now()->year)
                ->with('assigned.technician.user')
                ->whereHas('assigned.technician.user', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->get()
                ->groupBy(function ($ticket) {
                    return Carbon::parse($ticket->created_at)->format('M');
                })
                ->map(function ($grouped_tickets) {
                    return $grouped_tickets->count();
                });
        }
        $ordered_data = collect([]);
        foreach ($months as $month) {
            $ordered_data[$month] = $yearly_data->get($month, 0);
        }
        return $ordered_data;
    }

    private function getType($user)
    {
        if ($user->user_type == 'employee') {
            $types = Ticket::distinct('service')->where('employee', $user->employee->employee_id)->pluck('service');
            $typeCounts = [];

            foreach ($types as $type) {
                $count = Ticket::where('service', $type)->where('employee', $user->employee->employee_id)->count(); // Count tickets for each type
                $typeCounts[$type] = $count;
            }
        } else if ($user->user_type == 'technician') {
            $types = Ticket::distinct('service')
                ->with('assigned.technician.user')
                ->whereHas('assigned.technician.user', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->pluck('service');
            $typeCounts = [];

            foreach ($types as $type) {
                $count = Ticket::where('service', $type)
                    ->with('assigned.technician.user')
                    ->whereHas('assigned.technician.user', function ($query) use ($user) {
                        $query->where('id', $user->id);
                    })
                    ->count(); // Count tickets for each type
                $typeCounts[$type] = $count;
            }
        }

        return $typeCounts;
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
