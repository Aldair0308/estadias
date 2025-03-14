<?php

namespace App\Services;

use Mwz\Mammoth\Converter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MammothWordPreviewService
{
    protected $converter;

    public function __construct()
    {
        $this->converter = new Converter();
    }

    public function generatePreview($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception('Word file not found at path: ' . $filePath);
        }

        if (!is_readable($filePath)) {
            throw new \Exception('Word file is not readable: ' . $filePath);
        }

        try {
            $result = $this->converter->convertToHtml($filePath);
            $html = $result->getValue();
            $warnings = $result->getWarnings();

            if (!empty($warnings)) {
                foreach ($warnings as $warning) {
                    Log::warning('Mammoth conversion warning: ' . $warning);
                }
            }

            if (empty($html)) {
                throw new \Exception('HTML conversion produced empty content');
            }

            return $this->enhanceHtmlOutput($html);
        } catch (\Exception $e) {
            Log::error('Failed to generate Word preview: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function enhanceHtmlOutput($html)
    {
        // Add Bootstrap classes for better styling
        $html = str_replace('<table>', '<table class="table table-bordered">', $html);
        $html = str_replace('<img ', '<img class="img-fluid" ', $html);

        return $html;
    }
}