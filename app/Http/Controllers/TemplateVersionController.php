<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemplateVersionController extends Controller
{
    public function __construct()
    {   
        $this->middleware('auth');
        $this->middleware('permission:edit templates', ['only' => ['restore']]);
    }
    
    /**
     * Display the version history of a template
     */
    public function history($id)
    {   
        // Get the main template (parent) or the template itself if it's already a parent
        $template = Template::findOrFail($id);
        
        if ($template->parent_id) {
            // If this is a version, get its parent
            $parentTemplate = Template::findOrFail($template->parent_id);
            $versions = Template::where('parent_id', $parentTemplate->id)
                ->orWhere('id', $parentTemplate->id)
                ->orderBy('version', 'desc')
                ->get();
        } else {
            // This is already a parent template
            $parentTemplate = $template;
            $versions = Template::where('parent_id', $template->id)
                ->orWhere('id', $template->id)
                ->orderBy('version', 'desc')
                ->get();
        }
        
        return view('templates.history', compact('parentTemplate', 'versions', 'template'));
    }
    
    /**
     * Show a specific version of a template
     */
    public function show($id)
    {   
        $template = Template::findOrFail($id);
        $excelPreview = null;
        $wordPreview = null;

        if ($template->isExcel()) {
            try {
                $filePath = Storage::path('public/' . $template->path);
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                $excelPreview = $data;
            } catch (\Exception $e) {
                \Log::error('Excel preview error: ' . $e->getMessage());
            }
        }

        if ($template->isWord()) {
            try {
                $wordPreview = $template->getHtmlContent();
            } catch (\Exception $e) {
                \Log::error('Word preview error: ' . $e->getMessage());
            }
        }
        
        return view('templates.versions.show', compact('template', 'excelPreview', 'wordPreview'));
    }
    
    /**
     * Compare two versions of a template
     */
    public function compare(Request $request)
    {   
        $request->validate([
            'version1' => 'required|exists:templates,id',
            'version2' => 'required|exists:templates,id',
        ]);
        
        $version1 = Template::findOrFail($request->version1);
        $version2 = Template::findOrFail($request->version2);
        
        // Ensure both versions are related to the same template
        if ($version1->parent_id !== $version2->parent_id && 
            $version1->id !== $version2->parent_id && 
            $version2->id !== $version1->parent_id) {
            return back()->with('error', 'Cannot compare versions from different templates');
        }
        
        return view('templates.compare', compact('version1', 'version2'));
    }
    
    /**
     * Restore a previous version as the current version
     */
    public function restore($id)
    {   
        $oldVersion = Template::findOrFail($id);
        
        // Get the parent template
        $parentId = $oldVersion->parent_id;
        $parentTemplate = Template::findOrFail($parentId);
        
        // Create a new version based on the old version
        $newVersion = Template::create([
            'name' => Str::random(40) . '.' . pathinfo($oldVersion->name, PATHINFO_EXTENSION),
            'original_name' => $oldVersion->original_name,
            'mime_type' => $oldVersion->mime_type,
            'size' => $oldVersion->size,
            'version' => $parentTemplate->version + 1,
            'parent_id' => $parentId,
            'description' => $oldVersion->description,
            'observations' => 'Restored from version ' . $oldVersion->version,
            'content_hash' => $oldVersion->content_hash
        ]);
        
        // Copy the file from the old version to the new version
        $oldPath = Storage::disk('public')->path($oldVersion->path);
        $newPath = 'templates/' . $newVersion->name;
        Storage::disk('public')->put($newPath, file_get_contents($oldPath));
        
        // Update the path in the database
        $newVersion->update(['path' => $newPath]);
        
        return redirect()->route('templates.history', $parentId)
            ->with('success', 'Version restored successfully');
    }
}