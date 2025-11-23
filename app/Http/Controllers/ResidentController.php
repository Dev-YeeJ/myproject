<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DocumentType;
use App\Models\DocumentRequest;
use App\Models\Resident; 
use App\Models\Medicine; // Import Medicine
use App\Models\MedicineRequest; // Import MedicineRequest
use App\Models\DocumentRequirement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user(); 
        
        $residentId = $user->resident ? $user->resident->id : null;
        $myRequests = DocumentRequest::where('resident_id', $residentId);

        $householdMembers = 0;
        if ($user->resident && $user->resident->household) {
            $householdMembers = $user->resident->household->total_members;
        }

        $stats = [
            'my_pending_documents' => $myRequests->clone()->whereIn('status', ['Pending', 'Processing'])->count(),
            'my_completed_documents' => $myRequests->clone()->where('status', 'Completed')->count(),
            'my_household_members' => $householdMembers, 
            'new_announcements' => 0, // Placeholder
            'unread_announcements' => 0, // Placeholder
        ];

        return view('dashboard.resident', compact('user', 'stats'));
    }

    /**
     * Display the document services page.
     */
    public function showDocumentServices(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'available');
        $statusFilter = $request->input('status');
        
        $residentId = $user->resident ? $user->resident->id : null;

        $allMyRequests = DocumentRequest::where('resident_id', $residentId);

        $stats = [
            'my_pending_requests' => $allMyRequests->clone()->whereIn('status', ['Pending', 'Processing', 'Ready for Pickup'])->count(),
            'my_completed_requests' => $allMyRequests->clone()->where('status', 'Completed')->count(),
            'available_documents' => DocumentType::where('is_active', true)->count(),
            'total_requests' => $allMyRequests->clone()->count(),
        ];

        $documentTypes = null;
        $documentRequests = null;

        if ($view === 'available') {
            $documentTypes = DocumentType::where('is_active', true)
                                         ->orderBy('name')
                                         ->paginate(9, ['*'], 'page')
                                         ->appends($request->except('page'));

        } else { // $view === 'history'
            
            $query = DocumentRequest::where('resident_id', $residentId)
                                    ->with(['documentType', 'requirements']) 
                                    ->orderBy('created_at', 'desc');

            if ($statusFilter && $statusFilter !== 'All') {
                $query->where('status', $statusFilter);
            }

            $documentRequests = $query->paginate(10, ['*'], 'page')
                                      ->appends($request->except('page'));
        }

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
     */
    public function createDocumentRequest(Request $request)
    {
        $user = Auth::user();
        $selectedType = $request->query('type_id');
        
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.resident-document-create', compact(
            'user', 
            'documentTypes', 
            'selectedType'
        ));
    }

    /**
     * Store a new document request.
     */
    public function storeDocumentRequest(Request $request)
    {
        $user = Auth::user();
        
        $residentId = $user->resident ? $user->resident->id : null;
        if (!$residentId) {
            return redirect()->back()->with('error', 'Could not find your resident profile.');
        }
        
        $validated = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'purpose' => 'required|string|max:255',
            'requirements' => 'present|array', 
            'requirements.*' => 'file|mimes:pdf,jpg,png,jpeg|max:2048'
        ]);

        $docType = DocumentType::find($validated['document_type_id']);

        DB::beginTransaction();
        try {
            $documentRequest = DocumentRequest::create([
                'resident_id' => $residentId,
                'document_type' => $validated['document_type_id'],
                'purpose' => $validated['purpose'],
                'tracking_number' => 'BRGY-' . time() . '-' . $residentId, 
                'status' => 'Pending',
                'payment_status' => $docType->price > 0 ? 'Unpaid' : 'Waived',
                'priority' => 'Normal',
                'price' => $docType->price,
            ]);

            if ($request->hasFile('requirements')) {
                foreach ($request->file('requirements') as $file) {
                    $filePath = $file->store('requirements', 'public');
                    
                    DocumentRequirement::create([
                        'document_request_id' => $documentRequest->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                    ]);
                }
            }
            
            DB::commit();

            return redirect()->route('resident.document-services', ['view' => 'history'])
                             ->with('success', 'Your document request has been submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Document request failed: ' . $e->getMessage());

            return redirect()->back()
                             ->with('error', 'An error occurred while submitting your request. Please try again.')
                             ->withInput();
        }
    }


    /**
     * Cancel a pending document request.
     */
    public function cancelDocumentRequest(Request $request, $id)
    {
        $user = Auth::user();
        $residentId = $user->resident ? $user->resident->id : null;

        $documentRequest = DocumentRequest::where('id', $id)
                                          ->where('resident_id', $residentId)
                                          ->firstOrFail();

        if (in_array($documentRequest->status, ['Pending', 'Processing'])) {
            $documentRequest->status = 'Cancelled';
            $documentRequest->save();
            
            return redirect()->route('resident.document-services', ['view' => 'history'])
                             ->with('success', 'Request ' . $documentRequest->tracking_number . ' has been cancelled.');
        }

        return redirect()->route('resident.document-services', ['view' => 'history'])
                         ->with('error', 'This request is already being processed and cannot be cancelled.');
    }

    /**
     * Allow resident to download the file generated by the captain.
     */
    public function downloadGeneratedDocument($id)
    {
        $user = Auth::user();
        $residentId = $user->resident ? $user->resident->id : null;

        $documentRequest = DocumentRequest::where('id', $id)
                                          ->where('resident_id', $residentId)
                                          ->firstOrFail();

        if ($documentRequest->generated_file_path && Storage::disk('public')->exists($documentRequest->generated_file_path)) {
            return Storage::disk('public')->download($documentRequest->generated_file_path);
        }

        return redirect()->back()->with('error', 'No generated document found for this request.');
    }

    // ============================================
    // HEALTH SERVICES (Medicine Requests)
    // ============================================

    /**
     * Display Health Services for Resident
     */
    public function showHealthServices(Request $request)
    {
        $user = Auth::user();
        $residentId = $user->resident ? $user->resident->id : null;
        $view = $request->input('view', 'available');

        // Stats
        $myRequests = MedicineRequest::where('resident_id', $residentId);
        // Note: We use clone() to avoid modifying the query builder for subsequent counts
        $stats = [
            'pending' => (clone $myRequests)->where('status', 'Pending')->count(),
            'approved' => (clone $myRequests)->where('status', 'Approved')->count(),
            'rejected' => (clone $myRequests)->where('status', 'Rejected')->count(),
        ];

        $medicines = null;
        $myRequestsPagination = null; // Renamed variable to avoid conflict

        if ($view === 'available') {
            // FIX: Use whereDate on expiration_date instead of status column
            $medicines = Medicine::where('quantity', '>', 0)
                                 ->whereDate('expiration_date', '>=', Carbon::now()) // Only show items not expired
                                 ->orderBy('item_name')
                                 ->paginate(12);
        } else {
            // Show history
            $myRequestsPagination = MedicineRequest::with('medicine')
                                         ->where('resident_id', $residentId)
                                         ->orderBy('created_at', 'desc')
                                         ->paginate(10);
        }

        return view('dashboard.resident-health-services', compact(
            'user', 
            'stats', 
            'view', 
            'medicines', 
            'myRequestsPagination' // Pass this to the view
        ));
    }

    /**
     * Store a medicine request from a resident
     */
    public function storeMedicineRequest(Request $request)
    {
        $user = Auth::user();
        $residentId = $user->resident ? $user->resident->id : null;

        if (!$residentId) {
            return back()->with('error', 'Resident profile not found.');
        }

        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity_requested' => 'required|integer|min:1',
            'purpose' => 'nullable|string|max:255', 
        ]);

        // Check stock availability
        $medicine = Medicine::find($validated['medicine_id']);
        
        if ($medicine->quantity < $validated['quantity_requested']) {
            return back()->with('error', 'Requested quantity exceeds available stock.');
        }

        MedicineRequest::create([
            'resident_id' => $residentId,
            'medicine_id' => $validated['medicine_id'],
            'quantity_requested' => $validated['quantity_requested'],
            'remarks' => $validated['purpose'],
            'status' => 'Pending',
        ]);

        return redirect()->route('resident.health-services', ['view' => 'history'])
                         ->with('success', 'Medicine request submitted successfully.');
    }
}