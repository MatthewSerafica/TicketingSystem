<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Employee;

class TechnicianNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->where('marked_at', 0)->get();

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
