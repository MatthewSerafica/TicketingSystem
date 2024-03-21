<?php

namespace App\Http\Controllers;

use App\Models\Department;
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
        $user = User::where('id', $auth->id)->with('technician')->firstOrFail();
        $departments = Department::all();
        $yearly = $this->getYearlyData($user);
        $service = $this->getType($user);
        return inertia('Technician/Dashboard/Profile', [
            'users' => $user,
            'departments' => $departments,
            'service' => $service,
            'yearly' => $yearly,
        ]);
    }

    
    public function updateStatus($is_working)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->firstOrFail();
        $technician->update(['is_working' => $is_working]);


        return redirect()->back()->with('success', 'Status updated successfully.');
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

    

}
