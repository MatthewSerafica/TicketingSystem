<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUsersController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->get();
        $users = User::with('employee', 'technician')->get();
        return inertia('Admin/Users/Index', [
            'users' => $users,
            'employees'=> $employees
        ]);
    }

    public function create()
    {
        // Add logic here if needed
        return inertia('Admin/Users/Create');
    }

    public function store(Request $request)
    {
        // Add logic here to store new users
    }

    public function edit($id)
    {
        // Add logic here to retrieve user data for editing
    }

    public function update(Request $request, $id)
    {
        // Add logic here to update user data
    }

    public function destroy($id)
    {
        // Add logic here to delete user
    }
}
