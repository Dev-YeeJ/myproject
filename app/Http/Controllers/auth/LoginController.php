<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // ================================================================
        // UPDATE: Check for 'is_active' status
        // ================================================================
        // We add 'is_active' => 1 to the attempt credentials. 
        // This prevents users who have been "soft deleted" or deactivated 
        // (like former SK officials) from logging in.
        
        $loginAttempt = Auth::attempt([
            'username' => $credentials['username'], 
            'password' => $credentials['password'],
            'is_active' => true 
        ]);

        if ($loginAttempt) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            return $this->redirectBasedOnRole($user);
        }

        // Custom error message if account exists but is inactive
        // (Optional: You can keep the generic message for security if preferred)
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records or the account is inactive.',
        ])->withInput($request->only('username'));
    }

    protected function redirectBasedOnRole($user)
    {
        $message = 'Welcome back, ' . $user->first_name . '!';

        // 1. Check Highest Authority
        if (method_exists($user, 'isBarangayCaptain') && $user->isBarangayCaptain()) {
            return redirect()->route('captain.dashboard')->with('success', $message);
        }
        
        // 2. Check Key Officials
        if (method_exists($user, 'isSecretary') && $user->isSecretary()) {
            return redirect()->route('secretary.dashboard')->with('success', $message);
        }
        
        if (method_exists($user, 'isTreasurer') && $user->isTreasurer()) {
            return redirect()->route('treasurer.dashboard')->with('success', $message);
        }

        // 3. Check Council Members (Kagawad & SK)
        if (method_exists($user, 'isKagawad') && $user->isKagawad()) {
            return redirect()->route('kagawad.dashboard')->with('success', $message);
        }

        // MOVED UP: Check SK Official before Resident
        // This ensures SK officials go to their specific dashboard, 
        // even if they technically qualify as "residents" too.
        if (method_exists($user, 'isSkofficial') && $user->isSkofficial()) {
            return redirect()->route('sk.dashboard')->with('success', $message);
        }
        
        // 4. Check Staff / Workers
        if (method_exists($user, 'isHealthWorker') && $user->isHealthWorker()) {
            return redirect()->route('health.dashboard')->with('success', $message);
        }
        
        if (method_exists($user, 'isTanod') && $user->isTanod()) {
            return redirect()->route('tanod.dashboard')->with('success', $message);
        } 

        // 5. General Resident (Lowest Priority Check)
        if (method_exists($user, 'isResident') && $user->isResident()) {
            return redirect()->route('resident.dashboard')->with('success', $message);
        }
        
        // Fallback
        return redirect()->route('dashboard')->with('success', $message);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}