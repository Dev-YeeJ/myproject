<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\CaptainController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\SecretaryController;

/*
|--------------------------------------------------------------------------
| Web Routes - Force Login First
|--------------------------------------------------------------------------
*/

// ============================================
// ROOT ROUTE "/" - (IMPROVED VERSION)
// ============================================
Route::get('/', function () {
    // Check if the user is already logged in
    if (Auth::check()) {
        // If they are, redirect them to the main dashboard
        // (which will then redirect them to their role-specific dashboard)
        return redirect()->route('dashboard');
    }

    // If they are not logged in, show them the login page
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
    // ROLE-SPECIFIC DASHBOARDS & ROUTES
    // ============================================

    // --- Captain Routes ---
    Route::middleware(CheckRole::class . ':barangay_captain')->group(function() {
        // Captain Dashboard
        Route::get('/dashboard/captain', [DashboardController::class, 'captain'])
            ->name('dashboard.captain');

        // Resident Profiling Routes
        Route::get('/captain/resident-profiling', [CaptainController::class, 'residentProfiling'])
            ->name('captain.resident-profiling');
        Route::get('/captain/resident/create', [CaptainController::class, 'createResident'])
            ->name('captain.resident.create');
        Route::post('/captain/resident', [CaptainController::class, 'storeResident'])
            ->name('captain.resident.store');
        Route::get('/captain/resident/{id}', [CaptainController::class, 'showResident'])
            ->name('captain.resident.show')->where('id', '[0-9]+');
       Route::get('/captain/resident/{id}/edit', [CaptainController::class, 'editResident'])
 ->name('captain.resident.edit')->where('id', '[0-9]+');
        Route::put('/captain/resident/{id}', [CaptainController::class, 'updateResident'])
            ->name('captain.resident.update')->where('id', '[0-9]+');
        Route::delete('/captain/resident/{id}', [CaptainController::class, 'destroyResident'])
            ->name('captain.resident.destroy')->where('id', '[0-9]+');

        // Household Management Routes
        Route::get('/captain/household/create', [CaptainController::class, 'createHousehold'])
            ->name('captain.household.create');
        Route::post('/captain/household', [CaptainController::class, 'storeHousehold'])
            ->name('captain.household.store');
        Route::get('/captain/household/{id}/edit', [CaptainController::class, 'editHousehold'])
            ->name('captain.household.edit')->where('id', '[0-9]+');
        Route::put('/captain/household/{id}', [CaptainController::class, 'updateHousehold'])
            ->name('captain.household.update')->where('id', '[0-9]+');
        Route::delete('/captain/household/{id}', [CaptainController::class, 'destroyHousehold'])
            ->name('captain.household.destroy')->where('id', '[0-9]+');
        Route::get('/captain/household/{id}/show', [CaptainController::class, 'showHousehold'])->name('captain.household.show');

        // Health & Social Services Route
        Route::get('/captain/health-services', [CaptainController::class, 'healthAndSocialServices'])
                ->name('captain.health-services');

        // --- NEW MEDICINE ROUTES ---
        // Show form to add medicine
        Route::get('/captain/medicine/create', [CaptainController::class, 'createMedicine'])
                ->name('captain.medicine.create');
        // Store new medicine
        Route::post('/captain/medicine', [CaptainController::class, 'storeMedicine'])
                ->name('captain.medicine.store');
                
        //Document Services
        Route::get('/captain/document-services', [CaptainController::class, 'documentServices'])
            ->name('captain.document-services');

        // --- ADDED: Routes for Document Types ---
        Route::get('/captain/document-type/create', [DocumentTypeController::class, 'create'])->name('captain.document-type.create');
        Route::post('/captain/document-type', [DocumentTypeController::class, 'store'])->name('captain.document-type.store');
        Route::get('/captain/document-type/{id}/edit', [DocumentTypeController::class, 'edit'])->name('captain.document-type.edit');
        Route::put('/captain/document-type/{id}', [DocumentTypeController::class, 'update'])->name('captain.document-type.update');
        Route::delete('/captain/document-type/{id}', [DocumentTypeController::class, 'destroy'])->name('captain.document-type.destroy');

        // --- ADDED: Routes for Templates ---
        Route::get('/captain/template/create', [TemplateController::class, 'create'])->name('captain.template.create');
        Route::post('/captain/template', [TemplateController::class, 'store'])->name('captain.template.store');
        Route::get('/captain/template/{id}/edit', [TemplateController::class, 'edit'])->name('captain.template.edit');
        Route::put('/captain/template/{id}', [TemplateController::class, 'update'])->name('captain.template.update');
        Route::delete('/captain/template/{id}', [TemplateController::class, 'destroy'])->name('captain.template.destroy');


    }); // End Captain Middleware Group

    
    // ============================================
    // --- NEW: Secretary Routes ---
    // ============================================
    Route::middleware(CheckRole::class . ':secretary')->group(function() {
        
        // Secretary Dashboard
        Route::get('/dashboard/secretary', [DashboardController::class, 'secretary'])
            ->name('dashboard.secretary');

        // Resident Profiling Routes
        Route::get('/secretary/resident-profiling', [SecretaryController::class, 'residentProfiling'])
            ->name('secretary.resident-profiling');
        Route::get('/secretary/resident/create', [SecretaryController::class, 'createResident'])
            ->name('secretary.resident.create');
        Route::post('/secretary/resident', [SecretaryController::class, 'storeResident'])
            ->name('secretary.resident.store');
        Route::get('/secretary/resident/{id}', [SecretaryController::class, 'showResident'])
            ->name('secretary.resident.show')->where('id', '[0-9]+');
        Route::get('/secretary/resident/{id}/edit', [SecretaryController::class, 'editResident'])
            ->name('secretary.resident.edit')->where('id', '[0-9]+');
        Route::put('/secretary/resident/{id}', [SecretaryController::class, 'updateResident'])
            ->name('secretary.resident.update')->where('id', '[0-9]+');
        Route::delete('/secretary/resident/{id}', [SecretaryController::class, 'destroyResident'])
            ->name('secretary.resident.destroy')->where('id', '[0-9]+');
            

        // Household Management Routes
        Route::get('/secretary/household/create', [SecretaryController::class, 'createHousehold'])
        ->name('secretary.household.create');
        
        Route::get('/secretary/household/{id}/edit', [SecretaryController::class, 'editHousehold'])
            ->name('secretary.household.edit')->where('id', '[0-9]+');

         Route::put('/secretary/household/{id}', [SecretaryController::class, 'updateHousehold'])
            ->name('secretary.household.update')->where('id', '[0-9]+');

         Route::post('/secretary/household', [SecretaryController::class, 'storeHousehold'])
            ->name('secretary.household.store');
            


        // Add other secretary routes here (e.g., documents, settings)
        
    }); // End Secretary Middleware Group


    // --- Health Worker (BHW) Routes ---
    // This group handles all BHW-specific actions *except* the main dashboard
    Route::middleware(CheckRole::class . ':health_worker')->prefix('health')->group(function () {
        
        // Health Services Page (Medicine Inventory)
        Route::get('/health-services', [HealthController::class, 'showHealthServices'])
             ->name('health.health-services');
    
        // Show form to add medicine
        Route::get('/medicine/create', [HealthController::class, 'createMedicine'])
             ->name('health.medicine.create');
    
        // Store new medicine
        Route::post('/medicine', [HealthController::class, 'storeMedicine'])
             ->name('health.medicine.store');
    
        // "Manage Requests" button
        Route::get('/medicine/requests', [HealthController::class, 'showMedicineRequests'])
             ->name('health.medicine.requests');

        // You will also need routes for edit, update, delete
        // Route::get('/medicine/{id}/edit', [HealthController::class, 'editMedicine'])->name('bhw.medicine.edit');
        // Route::put('/medicine/{id}', [HealthController::class, 'updateMedicine'])->name('bhw.medicine.update');
        // Route::delete('/medicine/{id}', [HealthController::class, 'destroyMedicine'])->name('bhw.medicine.destroy');

    }); // End Health Worker Middleware Group


    // --- Other Role Dashboards ---

    // Note: The '/dashboard/secretary' route is now inside the new secretary group above
    
    Route::get('/dashboard/treasurer', [DashboardController::class, 'treasurer'])
        ->name('dashboard.treasurer')
        ->middleware(CheckRole::class . ':treasurer');

    Route::get('/dashboard/kagawad', [DashboardController::class, 'kagawad'])
        ->name('dashboard.kagawad')
        ->middleware(CheckRole::class . ':kagawad');

    // This is the main BHW dashboard route
    Route::get('/dashboard/health', [DashboardController::class, 'health'])
        ->name('dashboard.health')
        ->middleware(CheckRole::class . ':health_worker');

    Route::get('/dashboard/tanod', [DashboardController::class, 'tanod'])
        ->name('dashboard.tanod')
        ->middleware(CheckRole::class . ':tanod');

    // *** THIS IS THE NEW ROUTE ***
    // --- NEW: Resident Dashboard Route ---
    Route::get('/dashboard/resident', [DashboardController::class, 'resident'])
        ->name('dashboard.resident')
        ->middleware(CheckRole::class . ':resident');

    // --- SK Official Routes --- (Uncomment and adjust if needed)
    // Route::get('/dashboard/sk', [DashboardController::class, 'sk_official']) // Create sk_official method if needed
    //     ->name('dashboard.sk')
    //     ->middleware(CheckRole::class . ':sk_official');
    // Route::middleware(CheckRole::class . ':sk_official')->prefix('sk')->name('sk.')->group(function () {
    //     // Add SK-specific routes here
    // });

}); // End Auth Middleware Group