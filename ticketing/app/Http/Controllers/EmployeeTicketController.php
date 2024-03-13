<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketMade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeTicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();
        $ticket = Ticket::where('employee', $employee->employee_id)->with('employee.user', 'technician1.user', 'technician2.user', 'technician3.user')->paginate(10);
        return inertia('Employee/Index', [
            'tickets' => $ticket,
        ]);
    }
    public function create()
    {
        return inertia('Employee/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'employee' => 'required',
            'issue' => 'required',
        ]);

        $employee = Employee::where('user_id', $request->employee)->firstOrFail();
        if ($employee->made_ticket >= 5) {
            return redirect()->back()->with('error', 'You have already made the max number of tickets.');
        }

        $ticketData = [
            'employee' => $employee->employee_id,
            'issue' => $request->issue,
            'description' => $request->description,
            'status' => 'New',
        ];

        $ticket = Ticket::create($ticketData);
        $employee->update(['made_ticket' => $employee->made_ticket + 1]);
        $admins = User::where('user_type', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(
                new TicketMade($ticket)
            );
        }

        return redirect()->to('/employee')->with('success', 'Ticket Created');
    }

    public function password()
    {
        $user = Auth::user();
        return inertia('Employee/Change', [
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
                return redirect()->route('employee')->with('success', 'Password changed successfully');
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
}
