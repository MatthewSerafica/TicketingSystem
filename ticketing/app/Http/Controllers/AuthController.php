<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create()
    {
        return inertia('Auth/Login');
    }
    
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt($credentials, true)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed')
            ]);
        }

        $request->session()->regenerate();

        $userType = auth()->user()->user_type;
        $redirectRoute = '';

        switch ($userType) {
            case 'admin':
                $redirectRoute = '/admin';
                break;
            case 'employee':
                $redirectRoute = '/employee';
                break;
            case 'technician':
                $redirectRoute = '/technician';
                break;
        }

        return redirect()->intended($redirectRoute);
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
