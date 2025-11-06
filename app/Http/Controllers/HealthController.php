<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine; // Import the Medicine model
use App\Models\User;     // Import the User model
use Illuminate\Support\Facades\Auth; // Import Auth
use Carbon\Carbon;      // Import Carbon for date logic

class HealthController extends Controller
{
    /**
     * Require authentication for all methods in this controller
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the health and social services page (health-health-services.blade.php)
     * (Logic copied from CaptainController->healthAndSocialServices)
     */
    public function showHealthServices(Request $request)
    {
        $user = Auth::user();

        // --- Query Real Data ---
        // Get all medicines, and we will use the Accessor for status
        $allMedicines = Medicine::all();

        $stats = [
            'total_medicines' => $allMedicines->count(),
            // Use collection 'where' to filter by the 'status' Accessor
            'low_stock_medicines' => $allMedicines->where('status', 'Low Stock')->count(),
            'expired_medicines' => $allMedicines->where('status', 'Expired')->count(),
            'pending_requests' => 0, // Placeholder
        ];

        // --- Get Medicine Data for Table ---
        $medicines = Medicine::orderBy('item_name')->get();

        // The 'status' attribute is now automatically handled by the Accessor in the Medicine model

        // --- MODIFIED: Return the BHW view ---
        return view('dashboard.health-health-services', compact(
            'user',
            'stats',
            'medicines'
        ));
    }


    // ============================================
    // MEDICINE (HEALTH SERVICES) CRUD
    // ============================================

    /**
     * Show form to add new medicine
     * (Logic copied from CaptainController->createMedicine)
     */
    public function createMedicine()
    {
        $user = Auth::user();
        
        // --- MODIFIED: Return the BHW-specific view ---
        // NOTE: You will need to create this view file:
        // 'resources/views/dashboard/health-medicine-create.blade.php'
        // You can copy 'captain-medicine-create.blade.php' to create it.
        return view('dashboard.health-medicine-create', compact('user'));
    }

    /**
     * Store new medicine
     * (Logic copied from CaptainController->storeMedicine)
     */
    public function storeMedicine(Request $request)
    {
        // Validates based on your migration file
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'dosage' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'expiration_date' => 'required|date',
        ]);
        
        Medicine::create($validated);

        // --- MODIFIED: Redirect back to the BHW health services route ---
       return redirect()->route('health.health-services') 
                         ->with('success', 'Medicine added to inventory successfully!');
    }

    /**
     * Show form to manage medicine requests (placeholder)
     */
    public function showMedicineRequests()
    {
        $user = Auth::user();
        // --- MODIFIED: Return the BHW-specific view ---
        // NOTE: You will need to create this view file:
        // 'resources/views/dashboard/health-medicine-requests.blade.php'
        
        // For now, just returning a placeholder view
        return view('dashboard.health-medicine-requests', compact('user'));
    }
}