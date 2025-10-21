<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\CaptainController;

/*
|--------------------------------------------------------------------------
| Web Routes - Force Login First
|--------------------------------------------------------------------------
*/

// ============================================
// ROOT ROUTE "/" - FORCE LOGOUT and show login
// ============================================
Route::get('/', function () {
    // FORCE logout anyone who visits root
    if (Auth::check()) {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    
    // Then redirect to login
    return redirect()->route('login');
})->name('home');


// ============================================
// GUEST ROUTES - Only for users NOT logged in
// ============================================

Route::middleware('guest')->group(function () {
    
    // Show login form - FIRST page users see
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    
    // Handle login submission
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});


// ============================================
// AUTHENTICATED ROUTES - Only for logged-in users
// ============================================

Route::middleware('auth')->group(function () {
    
    // Logout route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard redirector
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ============================================
    // ROLE-SPECIFIC DASHBOARDS
    // ============================================
    
    Route::get('/dashboard/captain', [DashboardController::class, 'captain'])
        ->name('dashboard.captain')
        ->middleware(CheckRole::class . ':barangay_captain');
         Route::get('/captain/resident-profiling', [CaptainController::class, 'residentProfiling'])
        ->name('captain.resident-profiling');

    Route::get('/dashboard/secretary', [DashboardController::class, 'secretary'])
        ->name('dashboard.secretary')
        ->middleware(CheckRole::class . ':secretary');

    Route::get('/dashboard/treasurer', [DashboardController::class, 'treasurer'])
        ->name('dashboard.treasurer')
        ->middleware(CheckRole::class . ':treasurer');

    Route::get('/dashboard/kagawad', [DashboardController::class, 'kagawad'])
        ->name('dashboard.kagawad')
        ->middleware(CheckRole::class . ':kagawad');

    Route::get('/dashboard/health', [DashboardController::class, 'health'])
        ->name('dashboard.health')
        ->middleware(CheckRole::class . ':health_worker');

    Route::get('/dashboard/tanod', [DashboardController::class, 'tanod'])
        ->name('dashboard.tanod')
        ->middleware(CheckRole::class . ':tanod');
});