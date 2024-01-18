<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    public function index() {
        return inertia('Admin/Tickets/Index');
    }
    public function create() {
        return inertia('Admin/Tickets/Create');
    }
}
