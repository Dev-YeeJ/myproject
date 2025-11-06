<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;

class DocumentTypeController extends Controller
{
    /**
     * Show the form for creating a new document type.
     */
    public function create()
    {
        $user = Auth::user();
        // You'll need to create this view file next
        return view('dashboard.captain-document-type-create', compact('user'));
    }

    /**
     * Store a newly created document type in storage.
     */
    public function store(Request $request)
    {
        // Add '0' default for checkboxes if they aren't checked
        $request->merge([
            'requires_payment' => $request->input('requires_payment', 0),
            'is_active' => $request->input('is_active', 0),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:document_types',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'requires_payment' => 'boolean',
            'is_active' => 'boolean',
        ]);

        DocumentType::create($validated);

        return redirect()->route('captain.document-services', ['view' => 'types'])
                         ->with('success', 'Document Type added successfully!');
    }

    /**
     * Show the form for editing the specified document type.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $documentType = DocumentType::findOrFail($id);
        // You'll need to create this view file next
        return view('dashboard.captain-document-type-edit', compact('user', 'documentType'));
    }

    /**
     * Update the specified document type in storage.
     */
    public function update(Request $request, $id)
    {
        $documentType = DocumentType::findOrFail($id);

        // Add '0' default for checkboxes
        $request->merge([
            'requires_payment' => $request->input('requires_payment', 0),
            'is_active' => $request->input('is_active', 0),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:document_types,name,' . $id,
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'requires_payment' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $documentType->update($validated);

        return redirect()->route('captain.document-services', ['view' => 'types'])
                         ->with('success', 'Document Type updated successfully!');
    }

    /**
     * Remove the specified document type from storage.
     */
    public function destroy($id)
    {
        $documentType = DocumentType::findOrFail($id);
        
        // Add logic here to check if templates are attached
        // For now, we'll just delete it
        
        $documentType->delete();

        return redirect()->route('captain.document-services', ['view' => 'types'])
                         ->with('success', 'Document Type deleted successfully.');
    }
}