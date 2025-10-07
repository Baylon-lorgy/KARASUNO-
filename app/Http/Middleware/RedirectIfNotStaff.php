<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('staff')->check()) {
            return redirect('/staff/login');
        }

        $staffUser = Auth::guard('staff')->user();
        
        if ($staffUser->status !== 'active') {
            Auth::guard('staff')->logout();
            return redirect('/staff/login')->withErrors([
                'email' => 'Your account is not yet approved. Please contact your administrator.',
            ]);
        }

        return $next($request);
    }
} 