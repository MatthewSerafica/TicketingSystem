<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TechnicianDashboardController extends Controller
{
    public function index() {
        return inertia('Technician/Dashboard/Index');
    }
}
