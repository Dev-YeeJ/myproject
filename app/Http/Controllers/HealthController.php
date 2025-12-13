<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\User;
use App\Models\MedicineRequest; // --- IMPORT ADDED ---
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Announcements;

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
     * Display the health and social services page (Inventory Dashboard)
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
            // Count pending requests for notification badge
            'pending_requests' => MedicineRequest::where('status', 'Pending')->count(),
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

        // Get the filtered results with pagination
        $medicines = $query->orderBy('item_name')->paginate(15);

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
    // MEDICINE INVENTORY CRUD
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
        
        // Determine initial status based on inputs
        $status = 'In Stock';
        if ($validated['quantity'] == 0) {
            $status = 'Out of Stock';
        } elseif ($validated['quantity'] <= $validated['low_stock_threshold']) {
            $status = 'Low Stock';
        }
        // Note: Expiration check usually happens via a scheduled task or accessor, 
        // but we can set it initially here if needed.
        if (Carbon::parse($validated['expiration_date'])->isPast()) {
            $status = 'Expired';
        }

        // Merge calculated status if your model fillable includes it, 
        // otherwise handle it via Model Observer or Accessor.
        // Assuming 'status' is a column:
        $medicine = new Medicine($validated);
        $medicine->status = $status; 
        $medicine->save();

       return redirect()->route('health.health-services') 
                        ->with('success', 'Medicine added to inventory successfully!');
    }

    /**
     * R: Show a single medicine's details
     */
    public function showMedicine(Medicine $medicine)
    {
        $user = Auth::user();
        return view('dashboard.health-medicine-show', compact('user', 'medicine'));
    }

    /**
     * U: Show form to edit an existing medicine
     */
    public function editMedicine(Medicine $medicine)
    {
        $user = Auth::user();
        return view('dashboard.health-medicine-edit', compact('user', 'medicine'));
    }

    /**
     * U: Update an existing medicine in the database
     */
    public function updateMedicine(Request $request, Medicine $medicine)
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
        
        $medicine->update($validated);

        // Re-evaluate status after update
        $status = 'In Stock';
        if ($medicine->quantity == 0) {
            $status = 'Out of Stock';
        } elseif ($medicine->quantity <= $medicine->low_stock_threshold) {
            $status = 'Low Stock';
        }
        if (Carbon::parse($medicine->expiration_date)->isPast()) {
            $status = 'Expired';
        }
        $medicine->update(['status' => $status]);

        return redirect()->route('health.health-services') 
                         ->with('success', 'Medicine updated successfully!');
    }

    /**
     * D: Delete a medicine from the database
     */
    public function destroyMedicine(Medicine $medicine)
    {
        $medicine->delete();

        return redirect()->route('health.health-services') 
                         ->with('success', 'Medicine deleted successfully!');
    }


    // ============================================
    // MEDICINE REQUESTS MANAGEMENT
    // ============================================

    /**
     * Show form to manage medicine requests
     */
    public function showMedicineRequests(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status', 'Pending');

        $requests = MedicineRequest::with(['resident.user', 'medicine'])
            ->where('status', $status)
            ->orderBy('created_at', $status == 'Pending' ? 'asc' : 'desc')
            ->paginate(10);

        // Counts for tabs
        $counts = [
            'Pending' => MedicineRequest::where('status', 'Pending')->count(),
            'Approved' => MedicineRequest::where('status', 'Approved')->count(),
            'Rejected' => MedicineRequest::where('status', 'Rejected')->count(),
        ];

        return view('dashboard.health-medicine-requests', compact('user', 'requests', 'status', 'counts'));
    }

    /**
     * Approve or Reject a request
     */
    public function updateRequestStatus(Request $request, $id)
    {
        $medicineRequest = MedicineRequest::findOrFail($id);
        
        // We validate remarks, but they might not be present in the request (e.g. Approve button)
        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'remarks' => 'nullable|string|max:255',
        ]);

        // Prevent double processing
        if ($medicineRequest->status !== 'Pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        // Handle Stock Deduction logic ONLY on Approval
        if ($validated['status'] === 'Approved') {
            $medicine = Medicine::find($medicineRequest->medicine_id);

            // Safety check: ensure medicine exists and has enough stock
            if (!$medicine || $medicine->quantity < $medicineRequest->quantity_requested) {
                return back()->with('error', 'Insufficient stock to approve this request.');
            }

            // Deduct Stock
            $medicine->decrement('quantity', $medicineRequest->quantity_requested);
            
            // Auto-update status based on new quantity
            if ($medicine->quantity == 0) {
                $medicine->update(['status' => 'Out of Stock']);
            } elseif ($medicine->quantity <= $medicine->low_stock_threshold) {
                $medicine->update(['status' => 'Low Stock']);
            }
        }

        // Update the request record
        // FIX: Use null coalescing operator (??) to handle missing remarks in Approval form
        $medicineRequest->update([
            'status' => $validated['status'],
            'remarks' => $validated['remarks'] ?? null 
        ]);

        return back()->with('success', 'Request ' . $validated['status'] . ' successfully.');
    }

    // ==========================================
    // HEALTH WORKER: VIEW ANNOUNCEMENTS
    // ==========================================

    public function healthAnnouncements(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        // Filter: Show announcements for 'All' or 'Barangay Officials' (Health workers are officials)
        $query = Announcements::where('is_published', true)
                              ->whereIn('audience', ['All', 'Barangay Officials']) 
                              ->with('user')
                              ->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->paginate(9);

        return view('dashboard.health-announcements', compact('user', 'announcements', 'search'));
    }
}