<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Needed for file handling

class TemplateController extends Controller
{
    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        $user = Auth::user();
        // Get all active document types for the dropdown menu
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        
        // You'll need to create this view file next
        return view('dashboard.captain-template-create', compact('user', 'documentTypes'));
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type_id' => 'required|exists:document_types,id',
            // Add file validation if you are uploading files
            // 'template_file' => 'required|file|mimes:doc,docx' 
        ]);

        // --- Basic File Upload Logic ---
        // if ($request->hasFile('template_file')) {
        //    $path = $request->file('template_file')->store('templates', 'public');
        //    $validated['file_path'] = $path;
        // }

        Template::create($validated);

        return redirect()->route('captain.document-services', ['view' => 'templates'])
                         ->with('success', 'Template added successfully!');
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $template = Template::findOrFail($id);
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();

        // You'll need to create this view file next
        return view('dashboard.captain-template-edit', compact('user', 'template', 'documentTypes'));
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, $id)
    {
        $template = Template::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type_id' => 'required|exists:document_types,id',
            // 'template_file' => 'nullable|file|mimes:doc,docx'
        ]);

        // --- Logic to update file (if a new one is uploaded) ---
        // if ($request->hasFile('template_file')) {
        //    // Delete old file
        //    if ($template->file_path) {
        //        Storage::disk('public')->delete($template->file_path);
        //    }
        //    // Store new file
        //    $path = $request->file('template_file')->store('templates', 'public');
        //    $validated['file_path'] = $path;
        // }

        $template->update($validated);

        return redirect()->route('captain.document-services', ['view' => 'templates'])
                         ->with('success', 'Template updated successfully!');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy($id)
    {
        $template = Template::findOrFail($id);

        // --- Delete the file from storage ---
        // if ($template->file_path) {
        //     Storage::disk('public')->delete($template->file_path);
        // }
        
        $template->delete();

        return redirect()->route('captain.document-services', ['view' => 'templates'])
                         ->with('success', 'Template deleted successfully.');
    }
}