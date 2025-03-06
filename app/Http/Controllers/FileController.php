<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:edit files', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete files', ['only' => ['destroy']]);
    }

    public function index()
    {
        $files = File::whereNull('parent_id')->with('versions')->get();
        return view('files.index', compact('files'));
    }

    public function create()
    {
        return view('files.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,xls,xlsx,doc,docx|max:10240',
            'description' => 'nullable|string|max:1000'
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $name = Str::random(40) . '.' . $extension;
        $path = $file->storeAs('files', $name, 'public');
        
        // Create the file record
        File::create([
            'name' => $name,
            'original_name' => $originalName,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'description' => $request->description
        ]);

        return redirect()->route('files.index')->with('success', 'File uploaded successfully.');
    }

    public function show(string $id)
    {
        $file = File::with('versions')->findOrFail($id);
        $excelPreview = null;

        if ($file->isExcel()) {
            try {
                $filePath = Storage::path('public/' . $file->path);
                $spreadsheet = IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                $excelPreview = $data;
            } catch (\Exception $e) {
                \Log::error('Excel preview error: ' . $e->getMessage());
            }
        }

        return view('files.show', compact('file', 'excelPreview'));

    }

    public function edit(string $id)
    {
        $file = File::findOrFail($id);
        return view('files.edit', compact('file'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,xls,xlsx,doc,docx|max:10240',
            'description' => 'nullable|string|max:1000'
        ]);

        $oldFile = File::findOrFail($id);
        $file = $request->file('file');
        
        // Check if the file content matches any existing version
        $newContent = file_get_contents($file->getRealPath());
        $existingVersions = File::where('parent_id', $id)->get();
        
        foreach ($existingVersions as $version) {
            $versionContent = Storage::disk('public')->get($version->path);
            if ($newContent === $versionContent) {
                return back()->withErrors(['file' => 'This file content already exists in version created at ' . $version->created_at]);
            }
        }
        
        $extension = $file->getClientOriginalExtension();
        $name = Str::random(40) . '.' . $extension;
        $path = $file->storeAs('files', $name, 'public');

        // Create new version
        File::create([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'version' => $oldFile->version + 1,
            'parent_id' => $oldFile->parent_id ?? $oldFile->id,
            'description' => $request->description
        ]);

        return redirect()->route('files.index')->with('success', 'File updated successfully.');
    }

    public function destroy(string $id)
    {
        $file = File::findOrFail($id);
        
        // Delete the file from storage
        if (Storage::disk('public')->exists($file->path)) {
            Storage::disk('public')->delete($file->path);
        }
        
        // Delete all versions
        if (!$file->parent_id) {
            foreach ($file->versions as $version) {
                if (Storage::disk('public')->exists($version->path)) {
                    Storage::disk('public')->delete($version->path);
                }
                $version->delete();
            }
        }
        
        $file->delete();
        
        return redirect()->route('files.index')->with('success', 'File deleted successfully.');
    }

    public function history(string $id)
    {
        $file = File::with('versions')->findOrFail($id);
        $parentFile = $file;
        $versions = $file->parent_id ? File::where('id', $file->parent_id)->orWhere('parent_id', $file->parent_id)->get() : File::where('id', $file->id)->orWhere('parent_id', $file->id)->get();
        return view('files.history', compact('parentFile', 'versions', 'file'));
    }

    public function preview(File $file)
    {
        if ($file->isWord()) {
            $htmlContent = $file->getHtmlContent();
            return view('files.preview', compact('file', 'htmlContent'));
        }

        if ($file->isPdf()) {
            $pdfUrl = Storage::disk('public')->url($file->path);
            return view('files.preview', compact('file', 'pdfUrl'));
        }

        if ($file->isExcel()) {
            // Handle Excel files with existing functionality
            return view('files.preview', compact('file'));
        }

        abort(404, 'Preview not available for this file type');
    }
}
