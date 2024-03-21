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
use Carbon\Carbon;
use App\Notifications\TicketMade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeTicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();
        $query = Ticket::query()->where('employee', $employee->employee_id)->with('employee.user', 'assigned.technician.user');

        $filter = $request->only(['search', 'filterTickets', 'all', 'new', 'pending', 'ongoing', 'resolved']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('ticket_number', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%')
                ->orWhere('rr_no', 'like', '%' . $search . '%')
                ->orWhere('ms_no', 'like', '%' . $search . '%')
                ->orWhere('rs_no', 'like', '%' . $search . '%')
                ->orWhere('sr_no', 'like', '%' . $search . '%')
                ->orWhere('service', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('remarks', 'like', '%' . $search . '%')
                ->orWhereHas('employee.user', function ($subquery) use ($search) {
                    $subquery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('employee', function ($subquery) use ($search) {
                    $subquery->where('department', 'like', '%' . $search . '%');
                })
                ->orWhereHas('employee', function ($subquery) use ($search) {
                    $subquery->where('office', 'like', '%' . $search . '%');
                })
                ->orWhereHas('assigned.technician.user', function ($subquery) use ($search) {
                    $subquery->where('name', 'like', '%' . $search . '%');
                });
        }

        if ($request->filled('filterTickets')) {
            $ticketFilter = $request->input('filterTickets');
            if ($ticketFilter === 'new') {
                $query->where('status', 'like', '%' . $ticketFilter . '%');
            } elseif ($ticketFilter === 'resolved') {
                $query->where('status', 'like', '%' . $ticketFilter . '%');
            } elseif ($ticketFilter === 'pending') {
                $query->where('status', 'like', '%' . $ticketFilter . '%');
            } elseif ($ticketFilter === 'ongoing') {
                $query->where('status', 'like', '%' . $ticketFilter . '%');
            }
        }

        $tickets = $query->paginate(10);
        $tickets->appends($filter);
        $request->session()->put('filter', $filter);
        return inertia('Employee/Index', [
            'tickets' => $tickets,
            'filters' => $filter,
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
            return redirect()->back()->with('error', 'Error Creating Ticket')->with('message', 'Employee has already met ticket limit!');
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

    public function profile($id)
    {
        $user = User::where('id', $id)->with('employee')->firstOrFail();
        $departments = Department::all();
        $yearly = $this->getYearlyData($user);
        $service = $this->getType($user);
        return inertia('Employee/Profile', [
            'users' => $user,
            'departments' => $departments,
            'yearly' => $yearly,
            'service' => $service,
        ]);
    }

    private function getYearlyData($user)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $yearly_data = Ticket::whereYear('created_at', Carbon::now()->year)
            ->where('employee', $user->employee->employee_id)
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
        $types = Ticket::distinct('service')->where('employee', $user->employee->employee_id)->pluck('service');
        $typeCounts = [];

        foreach ($types as $type) {
            $count = Ticket::where('service', $type)->where('employee', $user->employee->employee_id)->count(); // Count tickets for each type
            $typeCounts[$type] = $count;
        }

        return $typeCounts;
    }
}
