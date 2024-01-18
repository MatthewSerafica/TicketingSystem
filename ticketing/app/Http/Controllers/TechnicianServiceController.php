<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TechnicianServiceController extends Controller
{
    public function index()
    {
        return inertia('Technician/ServiceReports/Index');
    }
    
    public function create()
    {
        return inertia('Technician/ServiceReports/Create');
    }
}
