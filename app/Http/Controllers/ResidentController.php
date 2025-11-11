<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DocumentType;
use App\Models\DocumentRequest;
// Make sure to import your Resident model if it's separate from User
// use App\Models\Resident; 
use Illuminate\Support\Facades\Auth;

class ResidentController extends Controller
{
    /**
     * Create a new controller instance.
     * All methods require authentication.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the resident's main dashboard.
     * This corresponds to route('dashboard.resident')
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user(); 
        
        // --- Calculate Stats for the Resident Dashboard ---
        $myRequests = DocumentRequest::where('resident_id', $user->id);

        $stats = [
            'my_pending_documents' => $myRequests->clone()->whereIn('status', ['Pending', 'Processing'])->count(),
            'my_completed_documents' => $myRequests->clone()->where('status', 'Completed')->count(),
            // Assumes User model has a 'household' relationship
            'my_household_members' => $user->household ? $user->household->total_members : 1, 
            'new_announcements' => 0, // Placeholder
            'unread_announcements' => 0, // Placeholder
        ];

        return view('dashboard.resident', compact('user', 'stats'));
    }

    /**
     * Display the document services page.
     * This corresponds to route('resident.document-services')
     */
    public function showDocumentServices(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'available');
        $statusFilter = $request->input('status');

        // --- Calculate Stats for the Top Section ---
        $allMyRequests = DocumentRequest::where('resident_id', $user->id);

        $stats = [
            'my_pending_requests' => $allMyRequests->clone()->whereIn('status', ['Pending', 'Processing', 'Ready for Pickup'])->count(),
            'my_completed_requests' => $allMyRequests->clone()->where('status', 'Completed')->count(),
            'available_documents' => DocumentType::where('is_active', true)->count(),
            'total_requests' => $allMyRequests->clone()->count(),
        ];

        // --- Initialize data variables ---
        $documentTypes = null;
        $documentRequests = null;

        // --- Logic to fetch data based on the view ---
        if ($view === 'available') {
            // Get all ACTIVE document types, paginated (9 per page for a 3-col grid)
            $documentTypes = DocumentType::where('is_active', true)
                                ->orderBy('name')
                                ->paginate(9, ['*'], 'page')
                                ->appends($request->except('page'));

        } else { // $view === 'history'
            
            // Get THIS resident's requests, paginated
            $query = DocumentRequest::where('resident_id', $user->id)
                                    ->with('documentType') // Eager load for efficiency
                                    ->orderBy('created_at', 'desc');

            // Apply status filter if one is provided
            if ($statusFilter && $statusFilter !== 'All') {
                $query->where('status', $statusFilter);
            }

            $documentRequests = $query->paginate(10, ['*'], 'page')
                                         ->appends($request->except('page'));
        }

        // --- Return the view with all necessary data ---
        return view('dashboard.resident-document-services', compact(
            'user',
            'stats',
            'view',
            'documentTypes',
            'documentRequests'
        ));
    }

    /**
     * Show the form for creating a new document request.
     * This corresponds to route('resident.document.create')
     */
    public function createDocumentRequest(Request $request)
    {
        $user = Auth::user();
        $selectedType = $request->query('type_id');
        
        // Get all active document types for the dropdown
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();

        // You will need to create this Blade view:
        // 'resources/views/dashboard/resident-document-create.blade.php'
        return view('dashboard.resident-document-create', compact(
            'user', 
            'documentTypes', 
            'selectedType'
        ));
    }

    /**
     * Store a new document request.
     * (This would be the target of the form in 'resident-document-create')
     */
    public function storeDocumentRequest(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'purpose' => 'required|string|max:255',
            // Add any other fields you need, like 'priority'
        ]);

        $docType = DocumentType::find($validated['document_type_id']);

        // Create the request
        DocumentRequest::create([
            'resident_id' => $user->id,
            
            // ================================================================
            // !!! THIS IS THE FIX !!!
            // The database column is 'document_type', 
            // but your form variable is 'document_type_id'.
            // This line maps the form variable to the correct database column.
            'document_type' => $validated['document_type_id'],
            // ================================================================

            'purpose' => $validated['purpose'],
            'tracking_number' => 'BRGY-' . time() . '-' . $user->id, // Simple tracking number
            'status' => 'Pending',
            'payment_status' => $docType->price > 0 ? 'Unpaid' : 'Waived',
            'priority' => 'Normal', // Default priority
        ]);

        return redirect()->route('resident.document-services', ['view' => 'history'])
                         ->with('success', 'Your document request has been submitted successfully!');
    }


    /**
     * Cancel a pending document request.
     * This corresponds to the modal's form action.
     */
    public function cancelDocumentRequest(Request $request, $id)
    {
        $user = Auth::user();

        // Find the request, ensuring it belongs to the logged-in user
        $documentRequest = DocumentRequest::where('id', $id)
                                          ->where('resident_id', $user->id)
                                          ->firstOrFail(); // Fails if not found or not owned

        // Only allow cancellation if it's still pending
        if ($documentRequest->status === 'Pending') {
            $documentRequest->status = 'Cancelled';
            $documentRequest->save();
            
            return redirect()->route('resident.document-services', ['view' => 'history'])
                             ->with('success', 'Request ' . $documentRequest->tracking_number . ' has been cancelled.');
        }

        return redirect()->route('resident.document-services', ['view' => 'history'])
                         ->with('error', 'This request is already being processed and cannot be cancelled.');
    }
}