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
        $query = File::whereNull('parent_id');
        
        if (!auth()->user()->hasRole('tutor')) {
            $query->where('responsible_email', auth()->user()->email);
        }
        
        $files = $query->with(['versions' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->get();
        
        // Transform each file to include version count and latest version data
        $files = $files->map(function($file) {
            $latestVersion = $file->versions->first();
            if ($latestVersion) {
                $latestVersion->versions = $file->versions;
                return $latestVersion;
            }
            return $file;
        });
        
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
                'description' => $request->description,
                'responsible_email' => auth()->user()->email
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
            'description' => $request->description,
            'responsible_email' => auth()->user()->email
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
                    $wordPreviewError = 'No se pudo generar la vista previa del documento';
                } else {
                    // Check for EMF images and set warning if found
                    if (strpos($wordPreview, 'EMF') !== false) {
                        $wordPreviewError = 'El documento contiene imágenes EMF que no se pueden mostrar en la vista previa. El documento se mostrará sin estas imágenes.';
                    }
                    \Log::debug('Word preview generated successfully');
                }
            } catch (\Exception $e) {
                \Log::error('Word preview error in controller: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
                if (strpos($e->getMessage(), 'EMF') !== false) {
                    $wordPreviewError = 'El documento contiene imágenes EMF que no se pueden mostrar en la vista previa. El documento se mostrará sin estas imágenes.';
                } else {
                    $wordPreviewError = 'Error al generar la vista previa: ' . $e->getMessage();
                }
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
        $parentFile = $file->parent_id ? File::findOrFail($file->parent_id) : $file;
        $versions = File::where('id', $parentFile->id)
            ->orWhere('parent_id', $parentFile->id)
            ->orderBy('version', 'desc')
            ->get();
        return view('files.history', compact('parentFile', 'versions', 'file'));
    }

    public function review()
    {
        $query = File::query();
        
        if (!auth()->user()->hasRole('tutor')) {
            $query->where('responsible_email', auth()->user()->email);
        }
        
        // Get both parent files and their versions
        $files = $query->with(['responsible', 'parent'])
            ->where(function($q) {
                $q->whereNull('parent_id')
                  ->orWhereHas('parent');
            })
            ->get()
            ->map(function($file) {
                // Add version number information
                if ($file->parent_id) {
                    $file->version_display = 'Versión ' . $file->version;
                    $file->original_name = $file->original_name . ' (Versión ' . $file->version . ')';
                } else {
                    $file->version_display = 'Original';
                }
                return $file;
            });
        
        return view('files.review', compact('files'));
    }

    public function markReviewed(string $id)
    {
        $file = File::findOrFail($id);
        $file->update(['checked' => !$file->checked]);
        
        return back()->with('success', 'Estado de revisión actualizado exitosamente.');
    }

    public function updateObservations(Request $request, string $id)
    {
        $request->validate([
            'observations' => 'required|string|max:2000'
        ]);

        $file = File::findOrFail($id);
        $file->update(['observations' => $request->observations]);

        return back()->with('success', 'Observaciones actualizadas exitosamente.');
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
            // Sanitize HTML content
            $content = $this->sanitizeHtmlContent($content);

            // Create a new PHPWord instance with settings
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->setDefaultFontName('Arial');
            $phpWord->setDefaultFontSize(11);
            
            // Create a new section with margins
            $section = $phpWord->addSection([
                'marginLeft' => 1440,  // 1 inch in twips
                'marginRight' => 1440,
                'marginTop' => 1440,
                'marginBottom' => 1440
            ]);
            
            // HTML to Word conversion with error handling
            try {
                \PhpOffice\PhpWord\Shared\Html::addHtml($section, $content, false, false);
            } catch (\Exception $e) {
                \Log::error('HTML conversion error: ' . $e->getMessage());
                return response()->json(['error' => 'Error al convertir el contenido HTML a formato Word'], 400);
            }

            // Create temporary file with proper extension
            $tempFile = tempnam(sys_get_temp_dir(), 'word') . '.docx';
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            
            try {
                $objWriter->save($tempFile);
            } catch (\Exception $e) {
                \Log::error('Error saving Word file: ' . $e->getMessage());
                return response()->json(['error' => 'Error al guardar el documento Word'], 500);
            }

            // Verify the file was created and is readable
            if (!file_exists($tempFile) || !is_readable($tempFile)) {
                \Log::error('Temporary file creation failed or is not readable');
                return response()->json(['error' => 'Error al crear el archivo temporal'], 500);
            }

            // Read the content of the temp file
            $newContent = file_get_contents($tempFile);
            if ($newContent === false) {
                \Log::error('Failed to read temporary file content');
                return response()->json(['error' => 'Error al leer el contenido del archivo temporal'], 500);
            }

            unlink($tempFile); // Clean up temp file

            // Check for duplicate content in versions
            $existingVersions = File::where('parent_id', $id)->get();
            foreach ($existingVersions as $version) {
                $versionContent = Storage::disk('public')->get($version->path);
                if ($newContent === $versionContent) {
                    return response()->json([
                        'error' => 'Este contenido ya existe en la versión creada el ' . $version->created_at->format('d/m/Y H:i')
                    ], 400);
                }
            }

            // Generate new filename
            $name = Str::random(40) . '.docx';
            $path = 'files/' . $name;

            // Save the new version
            try {
                if (!Storage::disk('public')->put($path, $newContent)) {
                    throw new \Exception('Failed to save file to storage');
                }
            } catch (\Exception $e) {
                \Log::error('Storage error: ' . $e->getMessage());
                return response()->json(['error' => 'Error al guardar el archivo en el almacenamiento'], 500);
            }

            // Create new version in database
            try {
                File::create([
                    'name' => $name,
                    'original_name' => $file->original_name,
                    'path' => $path,
                    'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'size' => Storage::disk('public')->size($path),
                    'version' => $file->version + 1,
                    'parent_id' => $file->parent_id ?? $file->id,
                    'description' => $file->description,
                    'observations' => 'Sin observaciones.',
                    'responsible_email' => auth()->user()->email,
                    'checked' => false
                ]);
            } catch (\Exception $e) {
                \Log::error('Database error: ' . $e->getMessage());
                Storage::disk('public')->delete($path); // Clean up file if database insert fails
                return response()->json(['error' => 'Error al guardar la información en la base de datos'], 500);
            }

            return response()->json(['success' => true, 'message' => 'Documento actualizado exitosamente']);

        } catch (\Exception $e) {
            \Log::error('Error updating document content: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el contenido del documento'], 500);
        }
    }

    protected function sanitizeHtmlContent($content)
    {
        // Remove potentially problematic HTML elements and attributes
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
        $content = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $content);
        $content = preg_replace('/on\w+="[^"]*"/i', '', $content);
        
        // Ensure proper encoding
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
        
        return $content;
    }
}
