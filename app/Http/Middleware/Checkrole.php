<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();
        
        if (!$user) {
            // User is not logged in, send them to the login page.
            return redirect()->route('login');
        }
        
        // Check if user has the required role
        $hasRole = match($role) {
            'barangay_captain' => $user->isBarangayCaptain(),
            'secretary' => $user->isSecretary(),
            'treasurer' => $user->isTreasurer(),
            'kagawad' => $user->isKagawad(),
            'health_worker' => $user->isHealthWorker(),
            'tanod' => $user->isTanod(),
            'resident' => $user->isResident(),
            default => false,
        };
        
        if (!$hasRole) {
            // --- THIS IS THE MODIFIED PART ---
            // Instead of aborting, redirect to the main dashboard
            // with an error flash message.
            return redirect()->route('dashboard')->with('error', 'You are not authorized to access that page.');
        }
        
        return $next($request);
    }
}