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

    // --- Resident Profiling Routes (Captain) ---
        // Apply captain middleware directly to these routes or group them
        Route::middleware(CheckRole::class . ':barangay_captain')->group(function() {
            Route::get('/captain/resident-profiling', [CaptainController::class, 'residentProfiling'])
                ->name('captain.resident-profiling');

            // Create Resident
            Route::get('/captain/resident/create', [CaptainController::class, 'createResident'])
                ->name('captain.resident.create');

            // Store Resident
            Route::post('/captain/resident', [CaptainController::class, 'storeResident'])
                ->name('captain.resident.store');

            // View Resident
            Route::get('/captain/resident/{id}', [CaptainController::class, 'showResident'])
                ->name('captain.resident.show')->where('id', '[0-9]+'); // Added where clause for ID

            // Edit Resident
            Route::get('/captain/resident/{id}/edit', [CaptainController::class, 'editResident'])
                ->name('captain.resident.edit')->where('id', '[0-9]+'); // Added where clause for ID

            // Update Resident
            Route::put('/captain/resident/{id}', [CaptainController::class, 'updateResident'])
                ->name('captain.resident.update')->where('id', '[0-9]+'); // Added where clause for ID

            // Delete Resident
            Route::delete('/captain/resident/{id}', [CaptainController::class, 'destroyResident'])
                ->name('captain.resident.destroy')->where('id', '[0-9]+'); // Added where clause for ID

            // --- Household Management Routes (Captain) ---

            // Create Household
            Route::get('/captain/household/create', [CaptainController::class, 'createHousehold'])
                ->name('captain.household.create');

            // Store Household
            Route::post('/captain/household', [CaptainController::class, 'storeHousehold'])
                ->name('captain.household.store');

            // Edit Household
            Route::get('/captain/household/{id}/edit', [CaptainController::class, 'editHousehold'])
                ->name('captain.household.edit')->where('id', '[0-9]+'); // Added where clause for ID

            // Update Household
            Route::put('/captain/household/{id}', [CaptainController::class, 'updateHousehold'])
                ->name('captain.household.update')->where('id', '[0-9]+'); // Added where clause for ID

            // Delete Household
            Route::delete('/captain/household/{id}', [CaptainController::class, 'destroyHousehold'])
                ->name('captain.household.destroy')->where('id', '[0-9]+'); // Added where clause for ID
        }); // End Captain Middleware Group


    // --- Other Role Dashboards ---

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

     // --- SK Official Routes --- (Uncomment and adjust if needed)
     // Route::get('/dashboard/sk', [DashboardController::class, 'sk_official']) // Create sk_official method if needed
     //     ->name('dashboard.sk')
     //     ->middleware(CheckRole::class . ':sk_official');
     // Route::middleware(CheckRole::class . ':sk_official')->prefix('sk')->name('sk.')->group(function () {
     //     // Add SK-specific routes here
     // });

}); // End Auth Middleware Group

