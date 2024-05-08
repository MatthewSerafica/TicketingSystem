<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TechnicianMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $current_user = User::findOrFail($user->id);

            if ($user->user_type !== 'technician' && $current_user->user_type === 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/')->with('message', 'You were logged out because your account type changed.');
            }
            if (auth()->check() && $current_user->user_type === 'technician') {
                return $next($request);
            }
        }
        return redirect()->back()->with('error', 'Invalid Request')->with('message', 'You cannot access this page!');
    }
}
