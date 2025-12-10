<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsSkOfficial
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in AND has the 'sk_official' role
        if (Auth::check() && Auth::user()->role === 'sk_official') {
            return $next($request);
        }

        // If not, redirect them away
        return redirect()->route('login')->with('error', 'Unauthorized access.');
    }
}