<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index() {
        $props = 'hello world';
        return inertia('Admin/Dashboard/Index', [
            'test' => $props,
        ]);
    }
}
