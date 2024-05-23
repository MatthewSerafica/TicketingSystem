<?php

namespace App\Http\Controllers;

use App\Models\AssignedDepartment;
use App\Models\Department;
use App\Models\Log;
use App\Models\ServiceReport;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Technician;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TechnicianDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->firstOrFail();
        $tickets = Ticket::with('employee.user', 'assigned.technician.user')
            ->whereHas('assigned.technician.user', function ($query) use ($technician) {
                $query->where('id', $technician->user->id);
            })
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->take(3)
            ->get();
        $yearly_data = $this->getYearly();
        $service = $this->getService();
        return inertia('Technician/Dashboard/Index', [
            'tickets' => $tickets,
            'yearly_data' => $yearly_data,
            'service' => $service,
        ]);
    }

    private function getYearly()
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $yearly_data = Ticket::whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(function ($ticket) {
                return Carbon::parse($ticket->created_at)->format('M');
            })
            ->map(function ($grouped_tickets) {
                return $grouped_tickets->count();
            });
        $ordered_data = collect([]);
        foreach ($months as $month) {
            $ordered_data[$month] = $yearly_data->get($month, 0);
        }
        return $ordered_data;
    }

    private function getService()
    {
        $types = Ticket::distinct('service')->pluck('service');
        $typeCounts = [];

        foreach ($types as $type) {
            $count = Ticket::where('service', $type)->count(); // Count tickets for each type
            $typeCounts[$type] = $count;
        }

        return $typeCounts;
    }
    public function password()
    {
        $user = Auth::user();
        return inertia('Technician/Dashboard/Change', [
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
                return redirect()->route('technician')->with('success', 'Password changed successfully');
            }
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

    public function profile()
    {
        $auth = Auth::user();
        $user = User::where('id', $auth->id)->with('technician.departments.departments')->firstOrFail();
        $departments = Department::all();
        $yearly = $this->getYearlyData($user);
        $service = $this->getType($user);
        $assigned_today = $this->getAssignedToday($user);
        $resolved_today = $this->getResolvedToday($user);
        $time = $this->getAverageResolutionTime($user);
        $complexity = $this->getComplexityCounts($user);
        return inertia('Technician/Dashboard/Profile', [
            'users' => $user,
            'departments' => $departments,
            'service' => $service,
            'yearly' => $yearly,
            'assigned_today' => $assigned_today,
            'resolved_today' => $resolved_today,
            'time' => $time,
            'complexity' => $complexity,
        ]);
    }

    public function avatar(Request $request, $id)
    {
        if ($request->hasFile('avatar')) {
            $previousPath = $request->user()->getRawOriginal('avatar');
            $link = Storage::put('/profile', $request->file('avatar'));

            $request->user()->update(['avatar' => $link]);

            if ($previousPath) {
                Storage::delete($previousPath);
            }

            return redirect()->back()
                ->with('success', 'Profile Updated')
                ->with('message', 'File uploaded successfully. Path: ' . $link);
        }

        return redirect()->back()
            ->with('error', 'No File Uploaded');
    }



    public function updateStatus($is_working)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->firstOrFail();
        $technician->update(['is_working' => $is_working]);


        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    private function getAssignedToday($user)
    {
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
    private function getResolvedToday($user)
    {
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

        $ordered_data = collect([]);
        foreach ($months as $month) {
            $ordered_data[$month] = $yearly_data->get($month, 0);
        }
        return $ordered_data;
    }

    private function getType($user)
    {
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
        return $typeCounts;
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
    public function technician(Request $request, $id, $field)
    {
        $request->validate([
            $field => 'required',
        ]);

        if ($field !== 'department_id') {
            $technician = Technician::where('technician_id', $id)->first();
            $old = $technician->$field;
            $technician->$field = $request->$field;
            $technician->save();
            $input = ucfirst($field);
            $action_taken = "Updated profile detail from " . $old . " to " .  $request->$field;
        } else {
            $assign = [
                'department_id' => $request->$field,
                'technician' => $id,
            ];
            $action_taken = "Updated department assignment";
            AssignedDepartment::create($assign);
            $input = ucfirst($field);
        }

        $auth = Auth::user();
        $log_data = [
            'name' => $auth->name,
            'user_type' => $auth->user_type,
            'actions_taken' => $action_taken,
        ];
        Log::create($log_data);

        return redirect()->back()->with('success', $input . ' Update!')->with('message', $action_taken);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'department_id' => 'required',
            'technician' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $assigned = AssignedDepartment::where(['department_id' => $request->department_id, 'technician' => $request->technician])->first();

            if ($assigned) {
                $assigned->delete();

                $auth = Auth::user();
                $action_taken = "Removed a department from  technician";
                $log_data = [
                    'name' => $auth->name,
                    'user_type' => $auth->user_type,
                    'actions_taken' => $action_taken,
                ];
                Log::create($log_data);

                DB::commit();
                return redirect()->back()->with('success', 'Department removed!')->with('message', 'Department successfully removed from technician');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred!')->with('message', $e->getMessage());
        }
    }

    public function replaceDepartment(Request $request)
    {
        $request->validate([
            'department_id' => 'required',
            'technician' => 'required',
            'old' => 'required',
            'assignId' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $assigned = AssignedDepartment::where(['department_id' => $request->old, 'technician' => $request->technician])->firstOrFail();

            if ($assigned) {
                $assigned->department_id = $request->department_id;
                $assigned->save();

                $auth = Auth::user();
                $action_taken = "Replaced a department for technician";
                $log_data = [
                    'name' => $auth->name,
                    'user_type' => $auth->user_type,
                    'actions_taken' => $action_taken,
                ];
                Log::create($log_data);

                DB::commit();
                return redirect()->back()->with('success', 'Department Updated!')->with('message', 'Department has been replaced!');
            }
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred!')->with('message', $e->getMessage());
        }
    }
}
