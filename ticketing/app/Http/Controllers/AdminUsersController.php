<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUsersController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()->with('technician', 'employee')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('user_type', 'like', '%' . $search . '%')
                    ->orWhereHas('employee', function ($subquery) use ($search) {
                        $subquery->where('department', 'like', '%' . $search . '%');
                    });
            })
            ->when($request->filled('filterUsers'), function ($query) use ($request) {
                $userFilter = $request->input('filterUsers');
                if ($userFilter === 'employee') {
                    $query->where('user_type', 'like', '%' . $userFilter . '%');
                } elseif ($userFilter === 'technician') {
                    $query->where('user_type', 'like', '%' . $userFilter . '%');
                }
            })
            ->get();

        $filters = $request->only(['search']);

        return inertia('Admin/Users/Index', [
            'users' => $users,
            'filter' => $filters,
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
