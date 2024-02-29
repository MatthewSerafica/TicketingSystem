<?php

namespace App\Http\Controllers;

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
    public function index() {
        $user = Auth::user();
        $technician = Technician::where('user_id', $user->id)->firstOrFail();
        $tickets = Ticket::where('technician', $technician->technician_id)->with('employee.user', 'technician.user')->whereDate('created_at',today())->orderByDesc('created_at')->take(3)->get();
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

            return redirect()->to('Technician/Dashboard/Change')->with('success', 'Password changed successfully');
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
