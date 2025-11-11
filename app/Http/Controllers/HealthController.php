<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
     */
    public function showHealthServices(Request $request)
    {
        $user = Auth::user();

        // --- Get Filter Inputs ---
        $selectedCategory = $request->query('category');
        $searchQuery = $request->query('search');

        // --- Query Real Data (Stats) ---
        $allMedicines = Medicine::all(); 
        $stats = [
            'total_medicines' => $allMedicines->count(),
            'low_stock_medicines' => $allMedicines->where('status', 'Low Stock')->count(),
            'expired_medicines' => $allMedicines->where('status', 'Expired')->count(),
            'total_categories' => $allMedicines->whereNotNull('category')->unique('category')->count(),
            'pending_requests' => 0, // Placeholder
        ];

        // --- Get Medicine Data for Table (Filtered) ---
        $query = Medicine::query();

        // 1. Apply category filter
        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }

        // 2. Apply search filter
        if ($searchQuery) {
            $query->where(function($q) use ($searchQuery) {
                $q->where('item_name', 'like', '%' . $searchQuery . '%')
                  ->orWhere('brand_name', 'like', '%' . $searchQuery . '%')
                  ->orWhere('category', 'like', '%' . $searchQuery . '%');
            });
        }

        // Get the filtered results
        $medicines = $query->orderBy('item_name')->paginate(15); // <-- Added pagination

        // --- Get Category List for Dropdown ---
        $categories = Medicine::select('category')
                            ->whereNotNull('category')
                            ->distinct()
                            ->orderBy('category')
                            ->pluck('category');

        return view('dashboard.health-health-services', compact(
            'user',
            'stats',
            'medicines',
            'categories',
            'selectedCategory',
            'searchQuery'
        ));
    }


    // ============================================
    // MEDICINE (HEALTH SERVICES) CRUD
    // ============================================

    /**
     * C: Show form to add new medicine
     */
    public function createMedicine()
    {
        $user = Auth::user();
        return view('dashboard.health-medicine-create', compact('user'));
    }

    /**
     * C: Store new medicine
     */
    public function storeMedicine(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'dosage' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'expiration_date' => 'required|date',
        ]);
        
        Medicine::create($validated);

       return redirect()->route('health.health-services') 
                        ->with('success', 'Medicine added to inventory successfully!');
    }

    /**
     * R: Show a single medicine's details
     */
    public function showMedicine(Medicine $medicine)
    {
        $user = Auth::user();
        // Uses route-model binding to automatically find the medicine by ID
        return view('dashboard.health-medicine-show', compact('user', 'medicine'));
    }

    /**
     * U: Show form to edit an existing medicine
     */
    public function editMedicine(Medicine $medicine)
    {
        $user = Auth::user();
        // Uses route-model binding
        return view('dashboard.health-medicine-edit', compact('user', 'medicine'));
    }

    /**
     * U: Update an existing medicine in the database
     */
    public function updateMedicine(Request $request, Medicine $medicine)
    {
        // Use the same validation as store
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'dosage' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'expiration_date' => 'required|date',
        ]);
        
        // Update the medicine
        $medicine->update($validated);

        return redirect()->route('health.health-services') 
                         ->with('success', 'Medicine updated successfully!');
    }

    /**
     * D: Delete a medicine from the database
     */
    public function destroyMedicine(Medicine $medicine)
    {
        // Delete the medicine
        $medicine->delete();

        return redirect()->route('health.health-services') 
                         ->with('success', 'Medicine deleted successfully!');
    }


    /**
     * Show form to manage medicine requests (placeholder)
     */
    public function showMedicineRequests()
    {
        $user = Auth::user();
        return view('dashboard.health-medicine-requests', compact('user'));
    }
}