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
        return inertia('Technician/Dashboard/Index', [
            'tickets' => $tickets,
        ]);
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

    public function profile($id)
    {
        $user = User::where('id', $id)->with('technician')->firstOrFail();
        $departments = Department::all();
        return inertia('Technician/Dashboard/Profile', [
            'users' => $user,
            'departments' => $departments,
        ]);
    }

    
    public function updateStatus($is_working)
    {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->firstOrFail();
        $technician->update(['is_working' => $is_working]);


        return redirect()->back()->with('success', 'Status updated successfully.');
    }
}
