<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\HTML;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class WordPreviewService
{
    protected $tempImagesDir;
    protected $currentFilePath;

    public function __construct()
    {   
        $this->tempImagesDir = storage_path('app/public/temp_images');
        $this->ensureTempDirectoryExists();
    }

    public function generatePreview($filePath)
    {   
        $this->currentFilePath = $filePath;

        if (!file_exists($filePath)) {
            throw new \Exception('Word file not found at path: ' . $filePath);
        }

        if (!is_readable($filePath)) {
            throw new \Exception('Word file is not readable: ' . $filePath);
        }

        try {
            $phpWord = $this->loadDocument($filePath);
            return $this->convertToHtml($phpWord);
        } catch (\Exception $e) {
            Log::error('Failed to generate Word preview: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function loadDocument($filePath)
    {
        $attempts = [
            'text_only' => function() use ($filePath) {
                $reader = IOFactory::createReader('Word2007');
                if (method_exists($reader, 'setReadDataOnly')) {
                    $reader->setReadDataOnly(true);
                }
                if (method_exists($reader, 'setStrictParsingMode')) {
                    $reader->setStrictParsingMode(false);
                }
                // Additional settings to maximize text extraction
                if (method_exists($reader, 'setLoadSheetsOnly')) {
                    $reader->setLoadSheetsOnly(true);
                }
                return $reader->load($filePath);
            },
            'ignore_images' => function() use ($filePath) {
                $reader = IOFactory::createReader('Word2007');
                if (method_exists($reader, 'setReadDataOnly')) {
                    $reader->setReadDataOnly(true);
                }
                return $reader->load($filePath);
            },
            'compatibility_mode' => function() use ($filePath) {
                $reader = IOFactory::createReader('Word2007');
                if (method_exists($reader, 'setStrictParsingMode')) {
                    $reader->setStrictParsingMode(false);
                }
                return $reader->load($filePath);
            },
            'default' => function() use ($filePath) {
                $reader = IOFactory::createReader('Word2007');
                return $reader->load($filePath);
            }
        ];

        $lastError = null;
        foreach ($attempts as $method => $attempt) {
            try {
                Log::info("Attempting to load document using {$method} method");
                return $attempt();
            } catch (\Exception $e) {
                $lastError = $e;
                Log::warning("Failed to load document using {$method} method: " . $e->getMessage());
                continue;
            }
        }

        // If we've exhausted all attempts, throw a user-friendly error
        Log::error('All document loading attempts failed. Last error: ' . $lastError->getMessage());
        throw new \Exception('Unable to open this document. The file might be corrupted or in an unsupported format.');
    }

    protected function convertToHtml($phpWord)
    {
        // Skip image processing for better text extraction
        $writer = new HTML($phpWord);
        
        // Configure HTML writer for better text output
        if (method_exists($writer, 'setHtmlBlockElements')) {
            $writer->setHtmlBlockElements(['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div']);
        }

        // Disable image handling to focus on text
        if (method_exists($writer, 'setImagesHandling')) {
            $writer->setImagesHandling('omit');
        }

        ob_start();
        try {
            $writer->save('php://output');
            $htmlContent = ob_get_clean();

            if (empty($htmlContent)) {
                throw new \Exception('HTML conversion produced empty content');
            }

            // Add Bootstrap classes and styling
            $htmlContent = $this->enhanceHtmlOutput($htmlContent);

            return $htmlContent;
        } catch (\Exception $e) {
            ob_end_clean();
            throw new \Exception('Failed to convert Word to HTML: ' . $e->getMessage());
        }
    }

    protected function processEmfImages($phpWord)
    {
        try {
            foreach ($phpWord->getSections() as $section) {
                $this->processSection($section);
            }
        } catch (\Exception $e) {
            // Log warning but continue with preview
            Log::warning('EMF image processing warning: ' . $e->getMessage());
            // Don't throw the exception - allow preview to continue
        }
    }

    protected function processSection($section)
    {
        try {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getElements')) {
                    $this->processElements($element->getElements());
                }
            }
        } catch (\Exception $e) {
            Log::warning('Section processing warning: ' . $e->getMessage());
        }
    }

    protected function processElements($elements)
    {
        foreach ($elements as $element) {
            try {
                if (method_exists($element, 'getImage')) {
                    $this->handleImageElement($element);
                } elseif (method_exists($element, 'getElements')) {
                    $this->processElements($element->getElements());
                }
            } catch (\Exception $e) {
                Log::warning('Element processing warning: ' . $e->getMessage());
            }
        }
    }

    protected function handleImageElement($element)
    {   
        try {
            $image = $element->getImage();
            if (!$image) return;

            $imageSrc = $image->getImageSrc();
            if (!$imageSrc) return;

            $extension = strtolower(pathinfo($imageSrc, PATHINFO_EXTENSION));
            
            // Remove EMF images and other unsupported formats
            if ($extension === 'emf' || !$this->isValidImageType($extension)) {
                $element->setImage(null);
                if ($extension === 'emf') {
                    Log::info('Removed EMF image for better document compatibility');
                }
                return;
            }
        } catch (\Exception $e) {
            // Handle any image processing errors
            $element->setImage(null);
            Log::warning('Image processing error: ' . $e->getMessage());
        }
    }

    protected function isValidImageType($extension)
    {
        $validTypes = ['png', 'jpg', 'jpeg', 'gif', 'bmp'];
        return in_array(strtolower($extension), $validTypes);
    }

    protected function convertEmfToPng($emfPath)
    {
        try {
            // Generate a unique filename for the PNG
            $pngFilename = uniqid('converted_', true) . '.png';
            $pngPath = $this->tempImagesDir . '/' . $pngFilename;

            // Convert EMF to PNG using Intervention Image
            $image = Image::make($emfPath);
            $image->save($pngPath);

            return $pngPath;
        } catch (\Exception $e) {
            Log::error('Failed to convert EMF to PNG: ' . $e->getMessage());
            return null;
        }
    }

    protected function enhanceHtmlOutput($html)
    {
        // Add Bootstrap classes to tables
        $html = preg_replace('/<table>/', '<table class="table table-bordered table-striped">', $html);

        // Add responsive wrapper for tables
        $html = preg_replace('/(<table[^>]*>)/', '<div class="table-responsive">$1', $html);
        $html = preg_replace('/(<\/table>)/', '$1</div>', $html);

        // Add Bootstrap classes to images
        $html = preg_replace('/<img/', '<img class="img-fluid"', $html);

        // Add custom styles for better readability
        $styles = '<style>
            .document-preview { max-width: 100%; padding: 20px; }
            .document-preview img { max-width: 100%; height: auto; margin: 10px 0; }
            .document-preview p { margin-bottom: 1rem; line-height: 1.5; }
            .document-preview h1, .document-preview h2, .document-preview h3, 
            .document-preview h4, .document-preview h5, .document-preview h6 { 
                margin-top: 1.5rem; margin-bottom: 1rem; 
            }
        </style>';

        return $styles . '<div class="document-preview">' . $html . '</div>';
    }

    protected function ensureTempDirectoryExists()
    {
        if (!file_exists($this->tempImagesDir)) {
            if (!mkdir($this->tempImagesDir, 0755, true)) {
                throw new \Exception('Failed to create temp images directory');
            }
        }

        if (!is_writable($this->tempImagesDir)) {
            throw new \Exception('Temp images directory is not writable');
        }
    }
}