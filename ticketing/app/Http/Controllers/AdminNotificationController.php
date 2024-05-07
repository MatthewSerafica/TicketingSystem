<?php

namespace App\Http\Controllers;

use App\Models\AssignedTickets;
use App\Models\Employee;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->where('marked_at', 0)->get();

        /* $notifications_with_user = [];

    foreach ($notifications as $notification) {
        $data = $notification->data;

        $employee = Employee::where('employee_id', $data['employee'])->with('user')->first();

        $technicians = collect([]);
        $assigned = AssignedTickets::where('ticket_number', $data['ticket_number'])->get();

        foreach ($assigned as $assign) {
            $technicians = $technicians->merge(Technician::where('technician_id', $assign->technician)->with('user')->get());
        }

        $notifications_with_user[] = [
            'notification' => $notification,
            'name' => $employee->user->name,
            'technicians' => $technicians,
            'department' => $employee->department,
            'office' => $employee->office,
        ];
    } */

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    public function marked($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->update(['marked_at' => 1]);
    }
    
    public function update()
    {
        $user = Auth::user();
        $user->notifications->markAsRead();
    }
}
