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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
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
            return redirect()->route('dashboard.captain')->with('success', $message);
        }
        
        if (method_exists($user, 'isSecretary') && $user->isSecretary()) {
            return redirect()->route('dashboard.secretary')->with('success', $message);
        }
        
        if (method_exists($user, 'isTreasurer') && $user->isTreasurer()) {
            return redirect()->route('dashboard.treasurer')->with('success', $message);
        }
        
        if (method_exists($user, 'isKagawad') && $user->isKagawad()) {
            return redirect()->route('dashboard.kagawad')->with('success', $message);
        }
        
        if (method_exists($user, 'isHealthWorker') && $user->isHealthWorker()) {
            return redirect()->route('dashboard.health')->with('success', $message);
        }
        
        if (method_exists($user, 'isTanod') && $user->isTanod()) {
            return redirect()->route('dashboard.tanod')->with('success', $message);
        }
        
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