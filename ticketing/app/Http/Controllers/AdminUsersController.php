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
        // Fetch all users initially
        $query = User::query();
    
        // Filter users by user type if provided
        if ($request->has('user_type')) {
            $query->where('user_type', $request->user_type);
        }
    
        // Search for users by name or email if search query provided
        if ($request->has('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search);
            });
        }
    
        // Fetch the users based on the query
        $users = $query->with('technician', 'employee')->get();
    
        return inertia('Admin/Users/Index', [
            'users' => $users,
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
