<?php

namespace App\Http\Controllers;

use App\Models\Announcements;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DocumentType;
use App\Models\DocumentRequest;
use App\Models\Resident; 
use App\Models\Medicine; 
use App\Models\MedicineRequest; 
use App\Models\DocumentRequirement;
use App\Models\BlotterRecord;
use App\Models\HealthProgram; // Ensure this is imported
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; 
use Carbon\Carbon;

class ResidentController extends Controller
{
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

        // --- Announcement Stats ---
        $announcementsQuery = Announcements::forUser($user);
        
        $announcements = (clone $announcementsQuery)->latest()->paginate(6);

        $newCount = (clone $announcementsQuery)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
            
        $totalAvailable = $announcementsQuery->count();

        $stats = [
            'my_pending_documents' => $myRequests->clone()->whereIn('status', ['Pending', 'Processing', 'Verification Pending'])->count(),
            'my_completed_documents' => $myRequests->clone()->where('status', 'Completed')->count(),
            'my_household_members' => $householdMembers, 
            'new_announcements' => $newCount, 
            'unread_announcements' => $totalAvailable, 
        ];

        return view('dashboard.resident', compact('user', 'stats', 'announcements'));
    }

    /**
     * Display announcements.
     */
    public function announcements(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $query = Announcements::forUser($user)->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->paginate(9);

        return view('dashboard.resident-announcements', compact('user', 'announcements', 'search'));
    }

    // ============================================
    // DOCUMENT SERVICES
    // ============================================

    public function showDocumentServices(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'available');
        $statusFilter = $request->input('status');
        
        $residentId = $user->resident ? $user->resident->id : null;

        $allMyRequests = DocumentRequest::where('resident_id', $residentId);

        $stats = [
            'my_pending_requests' => $allMyRequests->clone()->whereIn('status', ['Pending', 'Processing', 'Ready for Pickup', 'Verification Pending'])->count(),
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

        } else { 
            // View = History
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
            'user', 'stats', 'view', 'documentTypes', 'documentRequests'
        ));
    }

    public function createDocumentRequest(Request $request)
    {
        $user = Auth::user();
        $selectedType = $request->query('type_id');
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.resident-document-create', compact('user', 'documentTypes', 'selectedType'));
    }

   public function storeDocumentRequest(Request $request)
    {
        $user = Auth::user();
        $residentId = $user->resident ? $user->resident->id : null;
        
        if (!$residentId) {
            return redirect()->back()->with('error', 'Could not find your resident profile.');
        }

        $docType = DocumentType::find($request->document_type_id);
        if (!$docType) {
            return redirect()->back()->with('error', 'Invalid document type.');
        }

        $rules = [
            'document_type_id' => 'required|exists:document_types,id',
            'purpose' => 'required|string|max:255',
            'requirements' => 'present|array',
            'requirements.*' => 'file|mimes:pdf,jpg,png,jpeg|max:2048',
            'payment_method' => $docType->price > 0 ? 'required|in:Cash,Online' : 'nullable',
            'payment_reference_number' => 'required_if:payment_method,Online|nullable|string|max:50',
            'payment_proof' => 'required_if:payment_method,Online|nullable|image|max:2048',
        ];

        if (!empty($docType->custom_fields)) {
            foreach ($docType->custom_fields as $field) {
                if (isset($field['required']) && $field['required']) {
                    $rules["custom_data.{$field['name']}"] = 'required'; 
                }
            }
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $paymentStatus = 'Unpaid';
            $paymentMethod = null;
            $referenceNumber = null;
            $proofPath = null;

            if ($docType->price == 0) {
                $paymentStatus = 'Waived'; 
                $paymentMethod = 'Free';
            } else {
                $paymentMethod = $validated['payment_method'];
                if ($paymentMethod === 'Online') {
                    $paymentStatus = 'Verification Pending'; 
                    $referenceNumber = $validated['payment_reference_number'];
                    if ($request->hasFile('payment_proof')) {
                        $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
                    }
                }
            }

            $documentRequest = DocumentRequest::create([
                'resident_id' => $residentId,
                'document_type' => $validated['document_type_id'],
                'purpose' => $validated['purpose'],
                'tracking_number' => 'BRGY-' . time() . '-' . $residentId, 
                'status' => 'Pending',
                'priority' => 'Normal',
                'price' => $docType->price,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'payment_reference_number' => $referenceNumber,
                'payment_proof' => $proofPath,
                'custom_data' => $request->input('custom_data'),
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
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function cancelDocumentRequest(Request $request, $id)
    {
        $user = Auth::user();
        $residentId = $user->resident ? $user->resident->id : null;

        $documentRequest = DocumentRequest::where('id', $id)
            ->where('resident_id', $residentId)
            ->firstOrFail();

        if (in_array($documentRequest->status, ['Pending', 'Processing', 'Verification Pending'])) {
            $documentRequest->status = 'Cancelled';
            $documentRequest->save();
            return redirect()->route('resident.document-services', ['view' => 'history'])
                ->with('success', 'Request ' . $documentRequest->tracking_number . ' has been cancelled.');
        }

        return redirect()->route('resident.document-services', ['view' => 'history'])
            ->with('error', 'This request is already being processed and cannot be cancelled.');
    }

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
    // HEALTH SERVICES (UPDATED WITH PROGRAMS)
    // ============================================

    public function showHealthServices(Request $request)
    {
        $user = Auth::user();
        $residentId = $user->resident ? $user->resident->id : null;
        $view = $request->input('view', 'available');

        // Stats Logic
        $myRequests = MedicineRequest::where('resident_id', $residentId);
        $stats = [
            'pending' => (clone $myRequests)->where('status', 'Pending')->count(),
            'approved' => (clone $myRequests)->where('status', 'Approved')->count(),
            'rejected' => (clone $myRequests)->where('status', 'Rejected')->count(),
        ];

        $medicines = null;
        $myRequestsPagination = null;
        $programs = null; 

        if ($view === 'available') {
            // View 1: Medicine Inventory
            $medicines = Medicine::where('quantity', '>', 0)
                ->whereDate('expiration_date', '>=', Carbon::now())
                ->orderBy('item_name')
                ->paginate(12);
        } 
        elseif ($view === 'programs') { 
            // View 2: Health Programs
            $programs = HealthProgram::where('status', 'Upcoming')
                ->orderBy('schedule_date', 'asc')
                ->paginate(9);
        } 
        else {
            // View 3: Request History (Default if view is not 'available' or 'programs')
            $myRequestsPagination = MedicineRequest::with('medicine')
                ->where('resident_id', $residentId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('dashboard.resident-health-services', compact('user', 'stats', 'view', 'medicines', 'myRequestsPagination', 'programs'));
    }

    public function storeMedicineRequest(Request $request)
    {
        $user = Auth::user();
        $residentId = $user->resident ? $user->resident->id : null;

        if (!$residentId) return back()->with('error', 'Resident profile not found.');

        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity_requested' => 'required|integer|min:1',
            'purpose' => 'nullable|string|max:255', 
        ]);

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

    // ============================================
    // INCIDENT & BLOTTER
    // ============================================

    public function showIncidents(Request $request)
    {
        $user = Auth::user();
        $residentId = $user->resident ? $user->resident->id : null;

        $stats = [
            'my_total_reports' => BlotterRecord::where('resident_id', $residentId)->count(),
            'open_cases' => BlotterRecord::where('resident_id', $residentId)
                            ->whereIn('status', ['Open', 'Under Investigation', 'For Mediation', 'Scheduled for Hearing'])->count(),
            'resolved_cases' => BlotterRecord::where('resident_id', $residentId)
                            ->where('status', 'Resolved')->count(),
        ];

        $query = BlotterRecord::where('resident_id', $residentId)->latest();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('incident_type', 'like', "%{$search}%")
                  ->orWhere('respondent', 'like', "%{$search}%");
            });
        }

        $myIncidents = $query->paginate(10);

        return view('dashboard.resident-incident-reports', compact('user', 'stats', 'myIncidents'));
    }

    public function storeIncident(Request $request)
    {
        $user = Auth::user();
        $residentId = $user->resident ? $user->resident->id : null;

        if (!$residentId) return back()->with('error', 'Resident profile not found.');

        $validated = $request->validate([
            'incident_type' => 'required|string',
            'date_reported' => 'required|date',
            'location' => 'required|string',
            'respondent' => 'nullable|string',
            'narrative' => 'required|string',
        ]);

        BlotterRecord::create([
            'case_number' => BlotterRecord::generateCaseNumber(),
            'resident_id' => $residentId,
            'complainant' => $user->first_name . ' ' . $user->last_name, 
            'incident_type' => $validated['incident_type'],
            'date_reported' => $validated['date_reported'],
            'location' => $validated['location'],
            'respondent' => $validated['respondent'],
            'narrative' => $validated['narrative'],
            'priority' => 'Low', 
            'status' => 'Open',
            'actions_taken' => "[" . now()->format('M d, Y h:i A') . "] Report submitted by resident.",
        ]);

        return redirect()->back()->with('success', 'Incident report submitted successfully.');
    }

    public function updateIncident(Request $request, $id)
    {
        $user = Auth::user();
        $residentId = $user->resident->id;

        $incident = BlotterRecord::where('id', $id)->where('resident_id', $residentId)->firstOrFail();

        if ($incident->status !== 'Open') {
            return back()->with('error', 'You cannot edit this report because it is already being processed.');
        }

        $validated = $request->validate([
            'incident_type' => 'required|string',
            'date_reported' => 'required|date',
            'location' => 'required|string',
            'narrative' => 'required|string',
        ]);

        $incident->update($validated);

        return back()->with('success', 'Report details updated successfully.');
    }

    public function cancelIncident(Request $request, $id)
    {
        $user = Auth::user();
        $residentId = $user->resident->id;

        $incident = BlotterRecord::where('id', $id)->where('resident_id', $residentId)->firstOrFail();

        if ($incident->status !== 'Open') {
            return back()->with('error', 'You cannot cancel this report as it is already under investigation or scheduled.');
        }

        $incident->status = 'Dismissed'; 
        $incident->actions_taken .= "\n[" . now()->format('M d, Y h:i A') . "] Case withdrawn/cancelled by resident.";
        $incident->save();

        return back()->with('success', 'Incident report has been cancelled.');
    }
}