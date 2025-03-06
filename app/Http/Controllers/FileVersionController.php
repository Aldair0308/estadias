<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileVersionController extends Controller
{
    public function __construct()
    {   
        $this->middleware('auth');
        $this->middleware('permission:edit files', ['only' => ['restore']]);
    }
    
    /**
     * Display the version history of a file
     */
    public function history($id)
    {   
        // Get the main file (parent) or the file itself if it's already a parent
        $file = File::findOrFail($id);
        
        if ($file->parent_id) {
            // If this is a version, get its parent
            $parentFile = File::findOrFail($file->parent_id);
            $versions = File::where('parent_id', $parentFile->id)
                ->orWhere('id', $parentFile->id)
                ->orderBy('version', 'desc')
                ->get();
        } else {
            // This is already a parent file
            $parentFile = $file;
            $versions = File::where('parent_id', $file->id)
                ->orWhere('id', $file->id)
                ->orderBy('version', 'desc')
                ->get();
        }
        
        return view('files.history', compact('parentFile', 'versions', 'file'));
    }
    
    /**
     * Show a specific version of a file
     */
    public function show($id)
    {   
        $file = File::findOrFail($id);
        $parentFile = $file->parent_id ? File::findOrFail($file->parent_id) : $file;
        
        // Get all versions for the sidebar
        $versions = File::where('parent_id', $parentFile->id)
            ->orWhere('id', $parentFile->id)
            ->orderBy('version', 'desc')
            ->get();
            
        return view('files.version', compact('file', 'versions', 'parentFile'));
    }
    
    /**
     * Compare two versions of a file
     */
    public function compare(Request $request)
    {   
        $request->validate([
            'version1' => 'required|exists:files,id',
            'version2' => 'required|exists:files,id',
        ]);
        
        $version1 = File::findOrFail($request->version1);
        $version2 = File::findOrFail($request->version2);
        
        // Ensure both files are related (same parent or one is parent of the other)
        if ($version1->parent_id !== $version2->parent_id && 
            $version1->id !== $version2->parent_id && 
            $version2->id !== $version1->parent_id) {
            return redirect()->back()->with('error', 'Cannot compare unrelated files');
        }
        
        $parentFile = $version1->parent_id ? File::findOrFail($version1->parent_id) : $version1;
        
        return view('files.compare', compact('version1', 'version2', 'parentFile'));
    }
    
    /**
     * Restore a previous version (make it the current version)
     */
    public function restore($id)
    {   
        $versionToRestore = File::findOrFail($id);
        
        // Get the parent file
        $parentFile = $versionToRestore->parent_id ? 
            File::findOrFail($versionToRestore->parent_id) : 
            $versionToRestore;
        
        // Get the highest version number
        $highestVersion = File::where('parent_id', $parentFile->id)
            ->max('version') ?? $parentFile->version;
        
        // Create a new version based on the version being restored
        $newVersion = new File([
            'name' => Str::random(40) . '.' . pathinfo($versionToRestore->name, PATHINFO_EXTENSION),
            'original_name' => $versionToRestore->original_name,
            'mime_type' => $versionToRestore->mime_type,
            'size' => $versionToRestore->size,
            'version' => $highestVersion + 1,
            'parent_id' => $parentFile->id,
            'description' => $versionToRestore->description . ' (Restored from version ' . $versionToRestore->version . ')'
        ]);
        
        // Copy the file content
        if (Storage::disk('public')->exists($versionToRestore->path)) {
            $content = Storage::disk('public')->get($versionToRestore->path);
            $newPath = 'files/' . $newVersion->name;
            Storage::disk('public')->put($newPath, $content);
            $newVersion->path = $newPath;
            $newVersion->save();
            
            return redirect()->route('files.history', $parentFile->id)
                ->with('success', 'Version ' . $versionToRestore->version . ' has been restored as the latest version.');
        }
        
        return redirect()->back()->with('error', 'Could not restore the file version.');
    }
}