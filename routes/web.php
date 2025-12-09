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
use App\Http\Controllers\TreasurerController;
use App\Http\Controllers\KagawadController; // Added Kagawad Controller import

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ROOT
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// GUEST
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

// AUTHENTICATED
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ============================================
    // CAPTAIN ROUTES
    // ============================================
    Route::middleware(CheckRole::class . ':barangay_captain')->prefix('captain')->name('captain.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'captain'])->name('dashboard');
        
        // Resident
        Route::get('/resident-profiling', [CaptainController::class, 'residentProfiling'])->name('resident-profiling');
        Route::get('/resident/create', [CaptainController::class, 'createResident'])->name('resident.create');
        Route::post('/resident', [CaptainController::class, 'storeResident'])->name('resident.store');
        Route::get('/resident/{id}', [CaptainController::class, 'showResident'])->name('resident.show')->where('id', '[0-9]+');
        Route::get('/resident/{id}/edit', [CaptainController::class, 'editResident'])->name('resident.edit')->where('id', '[0-9]+');
        Route::put('/resident/{id}', [CaptainController::class, 'updateResident'])->name('resident.update')->where('id', '[0-9]+');
        Route::delete('/resident/{id}', [CaptainController::class, 'destroyResident'])->name('resident.destroy')->where('id', '[0-9]+');
        Route::post('/resident/{resident}/reset-password', [CaptainController::class, 'resetPassword'])->name('resident.reset-password');

        // Household
        Route::get('/household/create', [CaptainController::class, 'createHousehold'])->name('household.create');
        Route::post('/household', [CaptainController::class, 'storeHousehold'])->name('household.store');
        Route::get('/household/{id}/edit', [CaptainController::class, 'editHousehold'])->name('household.edit')->where('id', '[0-9]+');
        Route::put('/household/{id}', [CaptainController::class, 'updateHousehold'])->name('household.update')->where('id', '[0-9]+');
        Route::delete('/household/{id}', [CaptainController::class, 'destroyHousehold'])->name('household.destroy')->where('id', '[0-9]+');
        Route::get('/household/{id}/show', [CaptainController::class, 'showHousehold'])->name('household.show');

        // Health
        Route::get('/health-services', [CaptainController::class, 'healthAndSocialServices'])->name('health-services');
        
        // Documents
        Route::get('/document-services', [CaptainController::class, 'documentServices'])->name('document-services');
        Route::get('/document-request/{id}', [CaptainController::class, 'showDocumentRequest'])->name('document.show');
        Route::put('/document-request/{id}', [CaptainController::class, 'updateDocumentRequest'])->name('document.update');
        Route::get('/requirement/{id}/download', [CaptainController::class, 'downloadRequirement'])->name('requirement.download');
        Route::get('/financial/sync-documents', [CaptainController::class, 'syncDocumentTransactions'])->name('financial.sync');
        
        // Doc Types & Templates
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
    
        // Financial Management
        Route::get('/financial-management', [CaptainController::class, 'financialManagement'])->name('financial');
        Route::post('/transaction', [CaptainController::class, 'storeTransaction'])->name('transaction.store');
        Route::put('/transaction/{id}', [CaptainController::class, 'updateTransaction'])->name('transaction.update');
        Route::delete('/transaction/{id}', [CaptainController::class, 'destroyTransaction'])->name('transaction.destroy');
        Route::put('/transaction/{id}/status', [CaptainController::class, 'updateTransactionStatus'])->name('transaction.updateStatus');
        Route::post('/budget/update', [CaptainController::class, 'updateBudget'])->name('budget.update');
        Route::get('/financial/export', [CaptainController::class, 'exportReports'])->name('export');

        // Project Monitoring
        Route::get('/project-monitoring', [CaptainController::class, 'projectMonitoring'])->name('project.monitoring');
        Route::post('/project', [CaptainController::class, 'storeProject'])->name('project.store');
        Route::put('/project/{id}', [CaptainController::class, 'updateProjectProgress'])->name('project.update');
        Route::delete('/project/{id}', [CaptainController::class, 'destroyProject'])->name('project.destroy');

        // Announcements
        Route::get('/announcements', [CaptainController::class, 'announcements'])->name('announcements.index');
        Route::get('/announcements/create', [CaptainController::class, 'createAnnouncement'])->name('announcements.create');
        Route::post('/announcements', [CaptainController::class, 'storeAnnouncement'])->name('announcements.store');
        Route::get('/announcements/{id}/edit', [CaptainController::class, 'editAnnouncement'])->name('announcements.edit');
        Route::put('/announcements/{id}', [CaptainController::class, 'updateAnnouncement'])->name('announcements.update');
        Route::delete('/announcements/{id}', [CaptainController::class, 'destroyAnnouncement'])->name('announcements.destroy');

        // ============================================
        // INCIDENT & BLOTTER MANAGEMENT (UPDATED)
        // ============================================
        // View Table
        Route::get('/incident-blotter', [CaptainController::class, 'incidentAndBlotter'])->name('incident.index');
        
        // Create
        Route::post('/incident-blotter/store', [CaptainController::class, 'storeIncident'])->name('incident.store');
        
        // Process (Workflow: Status/Hearing)
        Route::put('/incident/{id}/process', [CaptainController::class, 'processIncident'])->name('incident.process');
        
        // Edit Details (Correction)
        Route::put('/incident/{id}/update', [CaptainController::class, 'updateIncidentDetails'])->name('incident.update_details');
        
        // Delete Record
        Route::delete('/incident/{id}', [CaptainController::class, 'destroyIncident'])->name('incident.destroy');
    });

    // ============================================
    // SECRETARY ROUTES
    // ============================================
    Route::middleware(CheckRole::class . ':secretary')->prefix('secretary')->name('secretary.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'secretary'])->name('dashboard');
        
        // Resident
        Route::get('/resident-profiling', [SecretaryController::class, 'residentProfiling'])->name('resident-profiling');
        Route::get('/resident/create', [SecretaryController::class, 'createResident'])->name('resident.create');
        Route::post('/resident', [SecretaryController::class, 'storeResident'])->name('resident.store');
        Route::get('/resident/{id}', [SecretaryController::class, 'showResident'])->name('resident.show')->where('id', '[0-9]+');
        Route::get('/resident/{id}/edit', [SecretaryController::class, 'editResident'])->name('resident.edit')->where('id', '[0-9]+');
        Route::put('/resident/{id}', [SecretaryController::class, 'updateResident'])->name('resident.update')->where('id', '[0-9]+');
        Route::delete('/resident/{id}', [SecretaryController::class, 'destroyResident'])->name('resident.destroy')->where('id', '[0-9]+');
        Route::post('/resident/{resident}/reset-password', [SecretaryController::class, 'resetPassword'])->name('resident.reset-password');
            
        // Household
        Route::get('/household/create', [SecretaryController::class, 'createHousehold'])->name('household.create');
        Route::get('/household/{id}/edit', [SecretaryController::class, 'editHousehold'])->name('household.edit')->where('id', '[0-9]+');
        Route::put('/household/{id}', [SecretaryController::class, 'updateHousehold'])->name('household.update')->where('id', '[0-9]+');
        Route::post('/household', [SecretaryController::class, 'storeHousehold'])->name('household.store');
        Route::delete('/household/{id}', [SecretaryController::class, 'destroyHousehold'])->name('household.destroy')->where('id', '[0-9]+');
        Route::get('/household/{id}/show', [SecretaryController::class, 'showHousehold'])->name('household.show');

        // Documents
        Route::get('/document-services', [SecretaryController::class, 'documentServices'])->name('document-services');
        Route::get('/document-request/{id}', [SecretaryController::class, 'showDocumentRequest'])->name('document.show');
        Route::put('/document-request/{id}', [SecretaryController::class, 'updateDocumentRequest'])->name('document.update');
        Route::get('/requirement/{id}/download', [SecretaryController::class, 'downloadRequirement'])->name('requirement.download');
        
        // Doc Types & Templates
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

        // Financial Management (SECRETARY)
        Route::get('/financial-management', [SecretaryController::class, 'financialManagement'])->name('financial-management');
        Route::post('/financial-transaction', [SecretaryController::class, 'storeFinancialTransaction'])->name('financial.store');
        Route::put('/financial-transaction/{id}/status', [SecretaryController::class, 'updateTransactionStatus'])->name('financial.status');
        Route::get('/financial/export', [SecretaryController::class, 'exportFinancialReports'])->name('financial.export'); 

        // Announcements
        Route::get('/announcements', [SecretaryController::class, 'announcements'])->name('announcements.index');
        Route::get('/announcements/create', [SecretaryController::class, 'createAnnouncement'])->name('announcements.create');
        Route::post('/announcements', [SecretaryController::class, 'storeAnnouncement'])->name('announcements.store');
        Route::get('/announcements/{id}/edit', [SecretaryController::class, 'editAnnouncement'])->name('announcements.edit');
        Route::put('/announcements/{id}', [SecretaryController::class, 'updateAnnouncement'])->name('announcements.update');
        Route::delete('/announcements/{id}', [SecretaryController::class, 'destroyAnnouncement'])->name('announcements.destroy');
    });

    // ============================================
    // TREASURER ROUTES
    // ============================================
    Route::middleware(CheckRole::class . ':treasurer')->prefix('treasurer')->name('treasurer.')->group(function () {
        
        Route::get('/dashboard', [TreasurerController::class, 'index'])->name('dashboard');
        
        Route::get('/financial-management', [TreasurerController::class, 'financialManagement'])->name('financial');
        
        Route::post('/transaction', [TreasurerController::class, 'storeTransaction'])->name('transaction.store');
        Route::put('/transaction/{id}', [TreasurerController::class, 'updateTransaction'])->name('transaction.update');
        Route::delete('/transaction/{id}', [TreasurerController::class, 'destroyTransaction'])->name('transaction.destroy');

        Route::put('/transaction/{id}/status', [TreasurerController::class, 'updateTransactionStatus'])->name('transaction.updateStatus');
        Route::post('/budget/update', [TreasurerController::class, 'updateBudget'])->name('budget.update');
        Route::get('/export', [TreasurerController::class, 'exportReports'])->name('export');

        Route::get('/announcements', [TreasurerController::class, 'announcements'])->name('announcements.index');
    });

    // ============================================
    // HEALTH & RESIDENT ROUTES
    // ============================================
    Route::middleware(CheckRole::class . ':health_worker')->prefix('health')->name('health.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'health'])->name('dashboard');
        Route::get('/health-services', [HealthController::class, 'showHealthServices'])->name('health-services');
        Route::get('/medicine/create', [HealthController::class, 'createMedicine'])->name('medicine.create');
        Route::post('/medicine', [HealthController::class, 'storeMedicine'])->name('medicine.store');
        Route::get('/medicine/requests', [HealthController::class, 'showMedicineRequests'])->name('medicine.requests');
        Route::put('/medicine/requests/{id}', [HealthController::class, 'updateRequestStatus'])->name('medicine.request.update');
        Route::get('/medicine/{medicine}', [HealthController::class, 'showMedicine'])->name('medicine.show');
        Route::get('/medicine/{medicine}/edit', [HealthController::class, 'editMedicine'])->name('medicine.edit');
        Route::put('/medicine/{medicine}', [HealthController::class, 'updateMedicine'])->name('medicine.update');
        Route::delete('/medicine/{medicine}', [HealthController::class, 'destroyMedicine'])->name('medicine.destroy'); 
    });

    Route::middleware(CheckRole::class . ':resident')->prefix('resident')->name('resident.')->group(function () {
        Route::get('/dashboard', [ResidentController::class, 'dashboard'])->name('dashboard');
        Route::get('/document-services', [ResidentController::class, 'showDocumentServices'])->name('document-services');
        Route::get('/document/create', [ResidentController::class, 'createDocumentRequest'])->name('document.create');
        Route::post('/document/store', [ResidentController::class, 'storeDocumentRequest'])->name('document.store');
        Route::delete('/document-request/{id}/cancel', [ResidentController::class, 'cancelDocumentRequest'])->name('document.cancel');
        Route::get('/document-request/{id}/download', [ResidentController::class, 'downloadGeneratedDocument'])->name('document.download');
        Route::get('/health-services', [ResidentController::class, 'showHealthServices'])->name('health-services');
        Route::post('/health-services/request', [ResidentController::class, 'storeMedicineRequest'])->name('health.request.store');
        Route::get('/announcements', [ResidentController::class, 'announcements'])->name('announcements.index');
        
        // Incident Routes (Resident)
        Route::get('/incidents', [ResidentController::class, 'showIncidents'])->name('incidents.index');
        Route::post('/incidents', [ResidentController::class, 'storeIncident'])->name('incidents.store');
        Route::put('/incidents/{id}', [ResidentController::class, 'updateIncident'])->name('incidents.update');
        Route::put('/incidents/{id}/cancel', [ResidentController::class, 'cancelIncident'])->name('incidents.cancel');
    });

    Route::get('/dashboard/treasurer', [DashboardController::class, 'treasurer'])->name('dashboard.treasurer')->middleware(CheckRole::class . ':treasurer');
    Route::get('/dashboard/kagawad', [DashboardController::class, 'kagawad'])->name('dashboard.kagawad')->middleware(CheckRole::class . ':kagawad');
    Route::get('/dashboard/tanod', [DashboardController::class, 'tanod'])->name('dashboard.tanod')->middleware(CheckRole::class . ':tanod');
});

// Kagawad Routes
Route::middleware(['auth', CheckRole::class . ':kagawad'])->prefix('kagawad')->name('kagawad.')->group(function () {
    Route::get('/dashboard', [KagawadController::class, 'index'])->name('dashboard');
    Route::get('/residents', [KagawadController::class, 'residents'])->name('residents');
});