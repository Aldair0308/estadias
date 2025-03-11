<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:edit templates', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete templates', ['only' => ['destroy']]);
    }

    public function index()
    {
        $templates = Template::whereNull('parent_id')->with('versions')->get();
        return view('templates.index', compact('templates'));
    }

    public function create()
    {
        return view('templates.create');
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
        $path = $file->storeAs('templates', $name, 'public');

        // Create content hash for version comparison
        $content_hash = md5_file($file->getRealPath());

        // Create the template record
        Template::create([
            'name' => $name,
            'original_name' => $originalName,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'description' => $request->description,
            'content_hash' => $content_hash
        ]);

        return redirect()->route('templates.index')->with('success', 'Template uploaded successfully.');
    }

    public function show(string $id)
    {
        $template = Template::with('versions')->findOrFail($id);
        $excelPreview = null;
        $wordPreview = null;

        if ($template->isExcel()) {
            try {
                $filePath = Storage::path('public/' . $template->path);
                $spreadsheet = IOFactory::load($filePath);
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

        return view('templates.show', compact('template', 'excelPreview', 'wordPreview'));
    }

    public function edit(string $id)
    {
        $template = Template::findOrFail($id);
        return view('templates.edit', compact('template'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'file' => 'nullable|file|mimes:pdf,xls,xlsx,doc,docx|max:10240',
            'description' => 'nullable|string|max:1000',
            'observations' => 'nullable|string|max:2000'
        ]);

        $oldTemplate = Template::findOrFail($id);

        // If a new file is uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Generate content hash for version comparison
            $content_hash = md5_file($file->getRealPath());
            
            // Check if the file content matches any existing version
            $matchingVersion = $oldTemplate->findMatchingVersion($content_hash);
            if ($matchingVersion) {
                return back()->withErrors(['file' => 'This template content already exists in version created at ' . $matchingVersion['date']]);
            }

            $extension = $file->getClientOriginalExtension();
            $name = Str::random(40) . '.' . $extension;
            $path = $file->storeAs('templates', $name, 'public');

            // Create new version
            Template::create([
                'name' => $name,
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'version' => $oldTemplate->version + 1,
                'parent_id' => $oldTemplate->parent_id ?? $oldTemplate->id,
                'description' => $request->description,
                'observations' => $request->observations,
                'content_hash' => $content_hash
            ]);

            return redirect()->route('templates.index')->with('success', 'Template updated successfully with new version.');
        } else {
            // Update only the metadata without creating a new version
            $oldTemplate->update([
                'description' => $request->description,
                'observations' => $request->observations
            ]);

            return redirect()->route('templates.index')->with('success', 'Template information updated successfully.');
        }
    }

    public function destroy(string $id)
    {
        $template = Template::findOrFail($id);

        // Delete the file from storage
        if (Storage::disk('public')->exists($template->path)) {
            Storage::disk('public')->delete($template->path);
        }

        // Delete all versions
        if (!$template->parent_id) {
            foreach ($template->versions as $version) {
                if (Storage::disk('public')->exists($version->path)) {
                    Storage::disk('public')->delete($version->path);
                }
                $version->delete();
            }
        }

        $template->delete();

        return redirect()->route('templates.index')->with('success', 'Template deleted successfully.');
    }

    public function history(string $id)
    {
        $template = Template::with('versions')->findOrFail($id);
        $parentTemplate = $template;
        $versions = $template->parent_id ? Template::where('id', $template->parent_id)->orWhere('parent_id', $template->parent_id)->get() : Template::where('id', $template->id)->orWhere('parent_id', $template->id)->get();
        return view('templates.history', compact('parentTemplate', 'versions', 'template'));
    }

    public function write(string $id)
    {
        $template = Template::findOrFail($id);
        
        if (!$template->isWord()) {
            return redirect()->route('templates.show', $template->id)
                ->with('error', 'Solo los documentos Word pueden ser editados con esta herramienta.');
        }
        
        try {
            $wordContent = $template->getHtmlContent();
            return view('templates.write', compact('template', 'wordContent'));
        } catch (\Exception $e) {
            \Log::error('Error loading Word content for editing: ' . $e->getMessage());
            return redirect()->route('templates.show', $template->id)
                ->with('error', 'No se pudo cargar el contenido del documento para edición.');
        }
    }

    public function updateContent(Request $request, string $id)
    {
        $template = Template::findOrFail($id);
        
        if (!$template->isWord()) {
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

            // Generate content hash for version comparison
            $content_hash = md5_file($tempFile);
            
            // Check for duplicate content in versions
            $matchingVersion = $template->findMatchingVersion($content_hash);
            if ($matchingVersion) {
                unlink($tempFile); // Clean up temp file
                return response()->json([
                    'error' => 'This content already exists in version created at ' . $matchingVersion['date']
                ], 400);
            }

            // Generate new filename
            $name = Str::random(40) . '.docx';
            $path = 'templates/' . $name;

            // Save the new version
            Storage::disk('public')->put($path, file_get_contents($tempFile));
            unlink($tempFile); // Clean up temp file

            // Create new version in database
            Template::create([
                'name' => $name,
                'original_name' => $template->original_name,
                'path' => $path,
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'size' => Storage::disk('public')->size($path),
                'version' => $template->version + 1,
                'parent_id' => $template->parent_id ?? $template->id,
                'description' => $template->description,
                'observations' => 'Content updated through editor',
                'content_hash' => $content_hash
            ]);

            return response()->json(['success' => true, 'message' => 'Document updated successfully']);

        } catch (\Exception $e) {
            \Log::error('Error updating document content: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update document content'], 500);
        }
    }
}