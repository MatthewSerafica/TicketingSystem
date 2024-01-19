<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TechnicianTicketController extends Controller
{
    public function index() {
        return inertia('Technician/Tickets/Index');
    }
    
    public function create() {
        return inertia('Technician/Tickets/Create');
    }
}
