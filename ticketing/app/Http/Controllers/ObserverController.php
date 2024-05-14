<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

use App\Models\Ticket;
use App\Models\ServiceReport;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Log;
use App\Models\Service;
use App\Models\ArchivedTicket;
use App\Models\Department;
use App\Models\Technician;

class ObserverController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('employee.user', 'assigned.technician.user')
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $yearly_data = $this->getYearlyData();
        $service = $this->getType();

        return inertia('Observer/Dashboard/Index', [
            'tickets' => $tickets,
            'yearly_data' => $yearly_data,
            'service' => $service,
        ]);
    }

    private function getYearlyData()
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $ticket_data = Ticket::whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(function ($ticket) {
                return Carbon::parse($ticket->created_at)->format('M');
            })
            ->map(function ($grouped_tickets) {
                return $grouped_tickets->count();
            });

        $archived_ticket_data = ArchivedTicket::whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(function ($ticket) {
                return Carbon::parse($ticket->created_at)->format('M');
            })
            ->map(function ($grouped_tickets) {
                return $grouped_tickets->count();
            });

        // Merge the data from both models
        $yearly_data = $ticket_data->mergeRecursive($archived_ticket_data);

        $ordered_data = collect([]);
        foreach ($months as $month) {
            $ordered_data[$month] = $yearly_data->get($month, 0);
        }
        return $ordered_data;
    }

    private function getType()
    {
        $ticket_types = Ticket::distinct('service')->pluck('service');
        $archived_ticket_types = ArchivedTicket::distinct('service')->pluck('service');

        $types = $ticket_types->merge($archived_ticket_types)->unique();

        $typeCounts = [];

        foreach ($types as $type) {
            $ticket_count = Ticket::where('service', $type)->count();
            $archived_ticket_count = ArchivedTicket::where('service', $type)->count();

            $total_count = $ticket_count + $archived_ticket_count;

            $typeCounts[$type] = $total_count;
        }

        return $typeCounts;
    }
    public function profile()
    {
        $auth = Auth::user();
        $user = User::where('id', $auth->id)->firstOrFail();
        $yearly = $this->getYearlyData();
        $service = $this->getType();
        return inertia('Admin/Profile/Index', [
            'users' => $user,
            'service' => $service,
            'yearly' => $yearly,
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
}
