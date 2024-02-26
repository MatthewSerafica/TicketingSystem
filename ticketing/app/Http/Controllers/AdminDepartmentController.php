<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Technician;
use App\Models\Office;
use Illuminate\Http\Request;

class AdminDepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::all();
        $offices = Office::all();
        return inertia('Admin/Department/Index', [
            'departments' => $departments,
            'offices' => $offices,
        ]);
    }
}
