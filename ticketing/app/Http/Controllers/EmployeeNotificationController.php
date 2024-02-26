<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeNotificationController extends Controller
{
    public function index(Request $request) 
    {
        $notifications = $request->user()->Notifications()->get();
        return response()->json(['notifications' => $notifications]);
    }

    public function update()
    {
        $user = Auth::user();
        $user->notifications->markAsRead();
    }
}
