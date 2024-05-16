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
        $credentials = $this->validateLogin($request);

        if (!Auth::attempt($credentials, true)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed')
            ]);
        }

        $request->session()->regenerate();

        return $this->authenticated($request, auth()->user()->user_type);
        /* $userType = auth()->user()->user_type;
        $redirectRoute = '';

        switch ($userType) {
            case 'super':
                $redirectRoute = '/admin';
                break;
            case 'admin':
                $redirectRoute = '/admin';
                break;
            case 'employee':
                $redirectRoute = '/employee';
                break;
            case 'technician':
                $redirectRoute = '/technician';
                break;
            case 'observer':
                $redirectRoute = '/observer';
                break;
            case 'default':
                $redirectRoute = '';
                break;
        }

        return redirect()->intended($redirectRoute); */
    }

    protected function validateLogin(Request $request)
    {
        return $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    }

    protected function authenticated(Request $request, $user_type)
    {
        $redirectRoute = match ($user_type) {
            'admin' => route('admin'),
            'super' => route('admin'),
            'employee' => route('employee'),
            'technician' => route('technician'),
            'observer' => route('observer'),
            default => route('login'),
        };

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
