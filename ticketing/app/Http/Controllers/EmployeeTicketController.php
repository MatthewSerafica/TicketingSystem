<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeTicketController extends Controller
{
    public function index() {
        return inertia('/Employee/Index');
    }
    
    public function create() {
        
    }
}
