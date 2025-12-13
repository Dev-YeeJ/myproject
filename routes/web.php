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
use App\Http\Controllers\SkController;

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
        // Project Approvals
        Route::put('/projects/{id}/approve', [CaptainController::class, 'approveProjectProposal'])->name('projects.approve');
        Route::delete('/projects/{id}/reject', [CaptainController::class, 'rejectProjectProposal'])->name('projects.reject');

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
        // Inside your existing Captain Routes group:
// Route::prefix('captain')...

        Route::get('/sk-oversight', [App\Http\Controllers\CaptainController::class, 'skOverview'])
        ->name('sk.overview');
    });

    

    // ============================================
    // SECRETARY ROUTES
    // ============================================
    Route::middleware(CheckRole::class . ':secretary')->prefix('secretary')->name('secretary.')->group(function () {
       // --- 1. Main Dashboard (Handled by DashboardController) ---
    // Ensure this route name matches what you use in redirects: 'secretary.dashboard'
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'secretary'])->name('dashboard');

    // --- 2. Resident Profiling ---
    Route::get('/residents', [SecretaryController::class, 'residentProfiling'])->name('resident-profiling');
    Route::get('/residents/create', [SecretaryController::class, 'createResident'])->name('resident.create');
    Route::post('/residents', [SecretaryController::class, 'storeResident'])->name('resident.store');
    Route::get('/residents/{id}', [SecretaryController::class, 'showResident'])->name('resident.show');
    Route::get('/residents/{id}/edit', [SecretaryController::class, 'editResident'])->name('resident.edit');
    Route::put('/residents/{id}', [SecretaryController::class, 'updateResident'])->name('resident.update');
    Route::delete('/residents/{id}', [SecretaryController::class, 'destroyResident'])->name('resident.destroy');
    Route::post('/residents/{resident}/reset-password', [SecretaryController::class, 'resetPassword'])->name('resident.reset-password');

    // --- 3. Household Management ---
    Route::get('/households/create', [SecretaryController::class, 'createHousehold'])->name('household.create');
    Route::post('/households', [SecretaryController::class, 'storeHousehold'])->name('household.store');
    Route::get('/households/{id}', [SecretaryController::class, 'showHousehold'])->name('household.show');
    Route::get('/households/{id}/edit', [SecretaryController::class, 'editHousehold'])->name('household.edit');
    Route::put('/households/{id}', [SecretaryController::class, 'updateHousehold'])->name('household.update');
    Route::delete('/households/{id}', [SecretaryController::class, 'destroyHousehold'])->name('household.destroy');

    // --- 4. Health & Social Services ---
    Route::get('/health-services', [SecretaryController::class, 'healthAndSocialServices'])->name('health-services');
    Route::get('/medicine/create', [SecretaryController::class, 'createMedicine'])->name('medicine.create');
    Route::post('/medicine', [SecretaryController::class, 'storeMedicine'])->name('medicine.store');

    // --- 5. Document Services ---
    Route::get('/documents', [SecretaryController::class, 'documentServices'])->name('document-services');
    Route::get('/documents/{id}', [SecretaryController::class, 'showDocumentRequest'])->name('document.show');
    Route::put('/documents/{id}', [SecretaryController::class, 'updateDocumentRequest'])->name('document.update');
    Route::get('/requirements/{id}/download', [SecretaryController::class, 'downloadRequirement'])->name('requirement.download');

    // --- 6. Announcements ---
    Route::get('/announcements', [SecretaryController::class, 'announcements'])->name('announcements.index');
    Route::get('/announcements/create', [SecretaryController::class, 'createAnnouncement'])->name('announcements.create');
    Route::post('/announcements', [SecretaryController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::get('/announcements/{id}/edit', [SecretaryController::class, 'editAnnouncement'])->name('announcements.edit');
    Route::put('/announcements/{id}', [SecretaryController::class, 'updateAnnouncement'])->name('announcements.update');
    Route::delete('/announcements/{id}', [SecretaryController::class, 'destroyAnnouncement'])->name('announcements.destroy');

    // --- 7. Financial Management (Restricted Logic) ---
    Route::get('/financial-management', [SecretaryController::class, 'financialManagement'])->name('financial-management');
    Route::post('/financial/store', [SecretaryController::class, 'storeFinancialTransaction'])->name('financial.store');
    Route::get('/financial/export', [SecretaryController::class, 'exportFinancialReports'])->name('financial.export');

    // --- 8. Project Monitoring ---
    Route::get('/projects', [SecretaryController::class, 'projectMonitoring'])->name('project-monitoring');
    Route::post('/projects', [SecretaryController::class, 'storeProject'])->name('project.store');
    Route::put('/projects/{id}/progress', [SecretaryController::class, 'updateProjectProgress'])->name('project.update-progress');
    Route::delete('/projects/{id}', [SecretaryController::class, 'destroyProject'])->name('project.destroy');

    // --- 9. Incident & Blotter (Mediation) ---
    Route::get('/incidents', [SecretaryController::class, 'incidentAndBlotter'])->name('incident-blotter');
    Route::post('/incidents', [SecretaryController::class, 'storeIncident'])->name('incident.store');
    Route::post('/incidents/{id}/process', [SecretaryController::class, 'processIncident'])->name('incident.process');
    Route::put('/incidents/{id}/details', [SecretaryController::class, 'updateIncidentDetails'])->name('incident.update-details');
    Route::delete('/incidents/{id}', [SecretaryController::class, 'destroyIncident'])->name('incident.destroy');

    // --- 10. SK Overview ---
    Route::get('/sk-overview', [SecretaryController::class, 'skOverview'])->name('sk-overview');
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
        Route::get('/health/announcements', [HealthController::class, 'healthAnnouncements'])->name('announcements');
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
    // Project Monitoring
    Route::get('/projects', [KagawadController::class, 'projects'])->name('projects');
    Route::post('/projects/store', [KagawadController::class, 'storeProjectProposal'])->name('projects.store');
    Route::put('/projects/{id}/progress', [KagawadController::class, 'updateProjectProgress'])->name('projects.progress');
    Route::post('/projects/expense', [KagawadController::class, 'storeProjectExpense'])->name('projects.expense');
    // ==========================================
    // NEW: INCIDENT & BLOTTER ROUTES
    // ==========================================
    
    // 1. The Main View (Table & Statistics)
    Route::get('/incidents', [KagawadController::class, 'incidents'])->name('incidents');

    // 2. Store a Walk-in Incident (Create)
    Route::post('/incidents/store', [KagawadController::class, 'storeIncident'])->name('incidents.store');

    // 3. Process/Update an Incident (Mediation, Status Change, Investigation Log)
    Route::post('/incidents/update/{id}', [KagawadController::class, 'updateIncident'])->name('incidents.update');
    // ===========================================
    // ANNOUNCEMENTS ROUTES
    // ===========================================
    
    // List all announcements
    Route::get('/announcements', [KagawadController::class, 'announcements'])->name('announcements.index');

    // Show create form
    Route::get('/announcements/create', [KagawadController::class, 'createAnnouncement'])->name('announcements.create');

    // Store new announcement
    Route::post('/announcements', [KagawadController::class, 'storeAnnouncement'])->name('announcements.store');

    // Show edit form
    Route::get('/announcements/{id}/edit', [KagawadController::class, 'editAnnouncement'])->name('announcements.edit');

    // Update announcement
    Route::put('/announcements/{id}', [KagawadController::class, 'updateAnnouncement'])->name('announcements.update');

    // Delete announcement
    Route::delete('/announcements/{id}', [KagawadController::class, 'destroyAnnouncement'])->name('announcements.destroy');
});


// routes/web.php







Route::prefix('sk')->name('sk.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [SkController::class, 'index'])->name('dashboard');
    
    // Profiling
    Route::get('/youth-profiling', [SkController::class, 'youthProfiling'])->name('youth-profiling');
    Route::get('/youth-profiling/print', [SkController::class, 'printYouthList'])->name('youth-profiling.print');
    
    // Projects
    Route::get('/projects', [SkController::class, 'projects'])->name('projects');
    Route::post('/projects', [SkController::class, 'storeProject'])->name('projects.store');
    Route::put('/projects/{id}', [SkController::class, 'updateProject'])->name('projects.update'); // Note the URL param
    Route::delete('/projects/{id}', [SkController::class, 'destroyProject'])->name('projects.destroy');
    
    // Officials
    Route::get('/officials', [SkController::class, 'manageOfficials'])->name('officials');
    Route::post('/officials', [SkController::class, 'storeOfficial'])->name('officials.store');
    Route::put('/officials/{id}', [SkController::class, 'updateOfficial'])->name('officials.update');
    Route::delete('/officials/{id}', [SkController::class, 'destroyOfficial'])->name('officials.destroy');
});