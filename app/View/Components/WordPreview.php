<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Services\WordPreviewService;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class WordPreview extends Component
{
    public $file;
    public $preview;
    public $error;

    public function __construct(File $file)
    {
        $this->file = $file;
        $this->preview = null;
        $this->error = null;

        if ($file->isWord()) {
            try {
                $previewService = new WordPreviewService();
                $filePath = Storage::path('public/' . $file->path);
                $this->preview = $previewService->generatePreview($filePath);
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
                \Log::error('Word preview error: ' . $e->getMessage());
            }
        }
    }

    public function render()
    {
        return view('components.word-preview');
    }
}