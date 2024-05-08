<?php

namespace App\Http\Controllers;

use App\Models\ArchivedTicket;
use App\Models\Department;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
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

        $technician = Technician::where('technician_id', $id)->first();
        $old = $technician->$field;
        $technician->$field = $request->$field;
        $technician->save();
        $input = ucfirst($field);

        return redirect()->back()->with('success', $input . ' Update!')->with('message', $old . ' is updated to ' . $request->$field);
    }
}
