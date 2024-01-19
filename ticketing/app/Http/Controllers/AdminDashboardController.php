<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index() {
        $user = User::all()->where('name', 'Test User');
        return inertia('Admin/Dashboard/Index', [
            'users' => $user,
        ]);
    }
}
