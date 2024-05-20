<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AdminNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = Cache::remember('user-notifications:' . $user->id, now()->addMinutes(5), function () use ($user) {
            return $user->notifications()->where('marked_at', 0)->get();
        });

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    public function marked($notificationId)
    {
        $user = Auth::user();
    
        try {
            $user->notifications()->where('id', $notificationId)->update(['marked_at' => 1]);
            return response()->json(['message' => 'Notification marked successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Notification not found.'], 404);
        }
    }
    
    public function update()
    {
        $user = Auth::user();
        $user->notifications->markAsRead();
    }
}
