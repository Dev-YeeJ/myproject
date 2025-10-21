<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();
        
        if (!$user) {
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
            default => false,
        };
        
        if (!$hasRole) {
            abort(403, 'Unauthorized access.');
        }
        
        return $next($request);
    }
}