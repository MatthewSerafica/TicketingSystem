<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class AdminDepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::paginate(8);
        return inertia('Admin/Department/Index', [
            'departments' => $departments,
        ]);
    }

    public function create()
    {
        $departments = Department::get();
        return inertia('Admin/Department/Create', [
            'departments' => $departments,
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'department' => 'required',
        ]);


        $departmentData = [
            'department' => $request->department,
        ];

        Department::create($departmentData);

        return redirect()->to('/admin/department')->with('success', 'Department Created!')->with('message', $request->department . ' is added to the departments!');
    }

    public function update(Request $request, $department_id)
    {
        $request->validate([
            'department' => 'required',
        ]);

        $department = Department::findOrFail($department_id);
        $old_department = $department->department;
        $department->department = $request->department;
        $department->save();

        return redirect()->back()->with('success', 'Department Updated!')->with('message', $old_department . ' is updated to ' . $request->department);
    }

}
