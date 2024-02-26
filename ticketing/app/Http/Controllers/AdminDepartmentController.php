<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Office;
use Illuminate\Http\Request;

class AdminDepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::paginate(8);
        $offices = Office::paginate(8);
        return inertia('Admin/Department/Index', [
            'departments' => $departments,
            'offices' => $offices,
        ]);
    }
}
