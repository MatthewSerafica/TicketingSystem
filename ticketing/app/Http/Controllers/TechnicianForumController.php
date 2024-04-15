<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TechnicianForumController extends Controller
{
    public function index(Request $request)
    {
        return inertia('Technician/Forum/Index', []);
    }
}
