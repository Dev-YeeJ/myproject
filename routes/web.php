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
use App\Http\Controllers\ResidentController;

/*
|--------------------------------------------------------------------------
| Web Routes - Force Login First
|--------------------------------------------------------------------------
*/

// ============================================
// ROOT ROUTE "/"
// ============================================
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');


// ============================================
// GUEST ROUTES - Only for users NOT logged in
// ============================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});


// ============================================
// AUTHENTICATED ROUTES - Only for logged-in users
// ============================================
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ============================================
    // ROLE-SPECIFIC DASHBOARDS & ROUTES
    // ============================================

    // --- Captain Routes ---
    Route::middleware(CheckRole::class . ':barangay_captain')->prefix('captain')->name('captain.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'captain'])->name('dashboard');
        
        // Resident Profiling
        Route::get('/resident-profiling', [CaptainController::class, 'residentProfiling'])->name('resident-profiling');
        Route::get('/resident/create', [CaptainController::class, 'createResident'])->name('resident.create');
        Route::post('/resident', [CaptainController::class, 'storeResident'])->name('resident.store');
        Route::get('/resident/{id}', [CaptainController::class, 'showResident'])->name('resident.show')->where('id', '[0-9]+');
        Route::get('/resident/{id}/edit', [CaptainController::class, 'editResident'])->name('resident.edit')->where('id', '[0-9]+');
        Route::put('/resident/{id}', [CaptainController::class, 'updateResident'])->name('resident.update')->where('id', '[0-9]+');
        Route::delete('/resident/{id}', [CaptainController::class, 'destroyResident'])->name('resident.destroy')->where('id', '[0-9]+');
        Route::post('/resident/{resident}/reset-password', [CaptainController::class, 'resetPassword'])->name('resident.reset-password');

        // Household Management
        Route::get('/household/create', [CaptainController::class, 'createHousehold'])->name('household.create');
        Route::post('/household', [CaptainController::class, 'storeHousehold'])->name('household.store');
        Route::get('/household/{id}/edit', [CaptainController::class, 'editHousehold'])->name('household.edit')->where('id', '[0-9]+');
        Route::put('/household/{id}', [CaptainController::class, 'updateHousehold'])->name('household.update')->where('id', '[0-9]+');
        Route::delete('/household/{id}', [CaptainController::class, 'destroyHousehold'])->name('household.destroy')->where('id', '[0-9]+');
        Route::get('/household/{id}/show', [CaptainController::class, 'showHousehold'])->name('household.show');

        // Health Services (View-Only)
        Route::get('/health-services', [CaptainController::class, 'healthAndSocialServices'])->name('health-services');
        Route::get('/medicine/create', [CaptainController::class, 'createMedicine'])->name('medicine.create');
        Route::post('/medicine', [CaptainController::class, 'storeMedicine'])->name('medicine.store');
                
        // Document Services
        Route::get('/document-services', [CaptainController::class, 'documentServices'])->name('document-services');

        // Document Request Management Routes
        Route::get('/document-request/{id}', [CaptainController::class, 'showDocumentRequest'])->name('document.show');
        Route::put('/document-request/{id}', [CaptainController::class, 'updateDocumentRequest'])->name('document.update');
        Route::get('/requirement/{id}/download', [CaptainController::class, 'downloadRequirement'])->name('requirement.download');
        
        // Document Type & Template CRUD
        Route::get('/document-type/create', [DocumentTypeController::class, 'create'])->name('document-type.create');
        Route::post('/document-type', [DocumentTypeController::class, 'store'])->name('document-type.store');
        Route::get('/document-type/{id}/edit', [DocumentTypeController::class, 'edit'])->name('document-type.edit');
        Route::put('/document-type/{id}', [DocumentTypeController::class, 'update'])->name('document-type.update');
        Route::delete('/document-type/{id}', [DocumentTypeController::class, 'destroy'])->name('document-type.destroy');
        Route::get('/template/create', [TemplateController::class, 'create'])->name('template.create');
        Route::post('/template', [TemplateController::class, 'store'])->name('template.store');
        Route::get('/template/{id}/edit', [TemplateController::class, 'edit'])->name('template.edit');
        Route::put('/template/{id}', [TemplateController::class, 'update'])->name('template.update');
        Route::delete('/template/{id}', [TemplateController::class, 'destroy'])->name('template.destroy');
    });

    // --- Secretary Routes ---
    Route::middleware(CheckRole::class . ':secretary')->prefix('secretary')->name('secretary.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'secretary'])->name('dashboard');
        
        // Resident Profiling
        Route::get('/resident-profiling', [SecretaryController::class, 'residentProfiling'])->name('resident-profiling');
        Route::get('/resident/create', [SecretaryController::class, 'createResident'])->name('resident.create');
        Route::post('/resident', [SecretaryController::class, 'storeResident'])->name('resident.store');
        Route::get('/resident/{id}', [SecretaryController::class, 'showResident'])->name('resident.show')->where('id', '[0-9]+');
        Route::get('/resident/{id}/edit', [SecretaryController::class, 'editResident'])->name('resident.edit')->where('id', '[0-9]+');
        Route::put('/resident/{id}', [SecretaryController::class, 'updateResident'])->name('resident.update')->where('id', '[0-9]+');
        Route::delete('/resident/{id}', [SecretaryController::class, 'destroyResident'])->name('resident.destroy')->where('id', '[0-9]+');
        Route::post('/resident/{resident}/reset-password', [SecretaryController::class, 'resetPassword'])->name('resident.reset-password');
            
        // Household Management
        Route::get('/household/create', [SecretaryController::class, 'createHousehold'])->name('household.create');
        Route::get('/household/{id}/edit', [SecretaryController::class, 'editHousehold'])->name('household.edit')->where('id', '[0-9]+');
        Route::put('/household/{id}', [SecretaryController::class, 'updateHousehold'])->name('household.update')->where('id', '[0-9]+');
        Route::post('/household', [SecretaryController::class, 'storeHousehold'])->name('household.store');
        Route::delete('/household/{id}', [SecretaryController::class, 'destroyHousehold'])->name('household.destroy')->where('id', '[0-9]+');
        Route::get('/household/{id}/show', [SecretaryController::class, 'showHousehold'])->name('household.show');
    });

    // --- Health Worker (BHW) Routes ---
    Route::middleware(CheckRole::class . ':health_worker')->prefix('health')->name('health.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'health'])->name('dashboard');
        
        // Medicine Inventory CRUD
        Route::get('/health-services', [HealthController::class, 'showHealthServices'])->name('health-services');
        Route::get('/medicine/create', [HealthController::class, 'createMedicine'])->name('medicine.create');
        Route::post('/medicine', [HealthController::class, 'storeMedicine'])->name('medicine.store');
        
        // --- CRITICAL: Request Management MUST BE BEFORE Wildcard Routes ---
        Route::get('/medicine/requests', [HealthController::class, 'showMedicineRequests'])->name('medicine.requests');
        Route::put('/medicine/requests/{id}', [HealthController::class, 'updateRequestStatus'])->name('medicine.request.update');

        // Medicine Specific Routes (Wildcards)
        Route::get('/medicine/{medicine}', [HealthController::class, 'showMedicine'])->name('medicine.show');
        Route::get('/medicine/{medicine}/edit', [HealthController::class, 'editMedicine'])->name('medicine.edit');
        Route::put('/medicine/{medicine}', [HealthController::class, 'updateMedicine'])->name('medicine.update');
        Route::delete('/medicine/{medicine}', [HealthController::class, 'destroyMedicine'])->name('medicine.destroy'); 
    });

    // --- Resident Routes ---
    Route::middleware(CheckRole::class . ':resident')->prefix('resident')->name('resident.')->group(function () {
        Route::get('/dashboard', [ResidentController::class, 'dashboard'])->name('dashboard');
        
        // Document Services
        Route::get('/document-services', [ResidentController::class, 'showDocumentServices'])->name('document-services');
        Route::get('/document/create', [ResidentController::class, 'createDocumentRequest'])->name('document.create');
        Route::post('/document/store', [ResidentController::class, 'storeDocumentRequest'])->name('document.store');
        Route::delete('/document-request/{id}/cancel', [ResidentController::class, 'cancelDocumentRequest'])->name('document.cancel');
        Route::get('/document-request/{id}/download', [ResidentController::class, 'downloadGeneratedDocument'])->name('document.download');
        
        // Health & Social Services
        Route::get('/health-services', [ResidentController::class, 'showHealthServices'])->name('health-services');
        Route::post('/health-services/request', [ResidentController::class, 'storeMedicineRequest'])->name('health.request.store');
    });

    // --- Other Role Dashboards ---
    Route::get('/dashboard/treasurer', [DashboardController::class, 'treasurer'])
        ->name('dashboard.treasurer')
        ->middleware(CheckRole::class . ':treasurer');

    Route::get('/dashboard/kagawad', [DashboardController::class, 'kagawad'])
        ->name('dashboard.kagawad')
        ->middleware(CheckRole::class . ':kagawad');

    Route::get('/dashboard/tanod', [DashboardController::class, 'tanod'])
        ->name('dashboard.tanod')
        ->middleware(CheckRole::class . ':tanod');

});