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
        // --- THIS IS THE FIX ---
        // We must explicitly tell Auth::attempt to use the 'username' column,
        // not the default 'email' column.
        // ================================================================
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
        // --- END OF FIX ---
            
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Your role-based redirect logic is great!
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('username'));
    }

    protected function redirectBasedOnRole($user)
    {
        $message = 'Welcome back, ' . $user->first_name . '!';

        if (method_exists($user, 'isBarangayCaptain') && $user->isBarangayCaptain()) {
            return redirect()->route('captain.dashboard')->with('success', $message);
        }
        
        if (method_exists($user, 'isSecretary') && $user->isSecretary()) {
            return redirect()->route('secretary.dashboard')->with('success', $message);
        }
        
        if (method_exists($user, 'isTreasurer') && $user->isTreasurer()) {
            return redirect()->route('treasurer.dashboard')->with('success', $message);
        }
        
        if (method_exists($user, 'isKagawad') && $user->isKagawad()) {
            return redirect()->route('kagawad.dashboard')->with('success', $message);
        }
        
        if (method_exists($user, 'isHealthWorker') && $user->isHealthWorker()) {
            return redirect()->route('health.dashboard')->with('success', $message);
        }
        
        if (method_exists($user, 'isTanod') && $user->isTanod()) {
            return redirect()->route('tanod.dashboard')->with('success', $message);
        } 
        if (method_exists($user, 'isResident') && $user->isResident()) {
            return redirect()->route('resident.dashboard')->with('success', $message);
        }
        if (method_exists($user, 'isSkofficial') && $user->isSkofficial()) {
            return redirect()->route('sk.dashboard')->with('success', $message);
        }
        
        // Fallback for any other role
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