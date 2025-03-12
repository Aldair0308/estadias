<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Template;
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
        $templates = Template::whereNull('parent_id')->with('versions')->get();
        return view('files.create', compact('templates'));
    }

    public function store(Request $request)
    {
        if ($request->has('create_from_template')) {
            $request->validate([
                'template_id' => 'required|exists:templates,id',
                'custom_title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000'
            ]);

            $template = Template::findOrFail($request->template_id);
            $templateContent = Storage::disk('public')->get($template->path);

            $extension = pathinfo($template->original_name, PATHINFO_EXTENSION);
            $name = Str::random(40) . '.' . $extension;
            $path = 'files/' . $name;

            Storage::disk('public')->put($path, $templateContent);

            File::create([
                'name' => $name,
                'original_name' => $request->custom_title . '.' . $extension,
                'path' => $path,
                'mime_type' => $template->mime_type,
                'size' => Storage::disk('public')->size($path),
                'description' => $request->description
            ]);

            return redirect()->route('files.index')->with('success', 'File created from template successfully.');
        }

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
        $wordPreview = null;
        $wordPreviewError = null;

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

        if ($file->isWord()) {
            try {
                \Log::debug('Starting Word preview generation for file: ' . $file->original_name);
                $wordPreview = $file->getHtmlContent();
                if ($wordPreview === null) {
                    \Log::error('Word preview generation returned null');
                    $wordPreviewError = 'Failed to generate Word preview';
                } else {
                    \Log::debug('Word preview generated successfully');
                }
            } catch (\Exception $e) {
                \Log::error('Word preview error in controller: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
                $wordPreviewError = 'Error generating Word preview: ' . $e->getMessage();
            }
        }

        return view('files.show', compact('file', 'excelPreview', 'wordPreview', 'wordPreviewError'));
    }

    public function edit(string $id)
    {
        $file = File::findOrFail($id);
        return view('files.edit', compact('file'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'file' => 'nullable|file|mimes:pdf,xls,xlsx,doc,docx|max:10240',
            'description' => 'nullable|string|max:1000',
            'observations' => 'nullable|string|max:2000'
        ]);

        $oldFile = File::findOrFail($id);

        // If a new file is uploaded
        if ($request->hasFile('file')) {
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
                'description' => $request->description,
                'observations' => $request->observations
            ]);

            return redirect()->route('files.index')->with('success', 'File updated successfully with new version.');
        } else {
            // Update only the metadata without creating a new version
            $oldFile->update([
                'description' => $request->description,
                'observations' => $request->observations
            ]);

            return redirect()->route('files.index')->with('success', 'File information updated successfully.');
        }
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
    
    public function write(string $id)
    {
        $file = File::findOrFail($id);
        
        if (!$file->isWord()) {
            return redirect()->route('files.show', $file->id)
                ->with('error', 'Solo los documentos Word pueden ser editados con esta herramienta.');
        }
        
        try {
            $wordContent = $file->getHtmlContent();
            return view('files.write', compact('file', 'wordContent'));
        } catch (\Exception $e) {
            \Log::error('Error loading Word content for editing: ' . $e->getMessage());
            return redirect()->route('files.show', $file->id)
                ->with('error', 'No se pudo cargar el contenido del documento para edición.');
        }
    }

    public function updateContent(Request $request, string $id)
    {
        $file = File::findOrFail($id);
        
        if (!$file->isWord()) {
            return response()->json(['error' => 'Only Word documents can be updated'], 400);
        }

        $content = $request->input('content');
        if (empty($content)) {
            return response()->json(['error' => 'Content cannot be empty'], 400);
        }

        try {
            // Create a new PHPWord instance
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            
            // Create a new section in the document
            $section = $phpWord->addSection();
            
            // HTML to Word conversion
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $content);

            // Create temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'word');
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempFile);

            // Read the content of the temp file
            $newContent = file_get_contents($tempFile);
            unlink($tempFile); // Clean up temp file

            // Check for duplicate content in versions
            $existingVersions = File::where('parent_id', $id)->get();
            foreach ($existingVersions as $version) {
                $versionContent = Storage::disk('public')->get($version->path);
                if ($newContent === $versionContent) {
                    return response()->json([
                        'error' => 'This content already exists in version created at ' . $version->created_at
                    ], 400);
                }
            }

            // Generate new filename
            $name = Str::random(40) . '.docx';
            $path = 'files/' . $name;

            // Save the new version
            Storage::disk('public')->put($path, $newContent);

            // Create new version in database
            File::create([
                'name' => $name,
                'original_name' => $file->original_name,
                'path' => $path,
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'size' => Storage::disk('public')->size($path),
                'version' => $file->version + 1,
                'parent_id' => $file->parent_id ?? $file->id,
                'description' => $file->description,
                'observations' => 'Content updated through editor'
            ]);

            return response()->json(['success' => true, 'message' => 'Document updated successfully']);

        } catch (\Exception $e) {
            \Log::error('Error updating document content: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update document content'], 500);
        }
    }
}
