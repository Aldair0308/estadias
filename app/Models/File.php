<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'original_name',
        'path',
        'mime_type',
        'size',
        'version',
        'parent_id',
        'description',
        'content_hash',
        'observations'
    ];

    /**
     * Get the parent file that this version belongs to
     */
    public function parent()
    {
        return $this->belongsTo(File::class, 'parent_id');
    }

    /**
     * Get all versions of this file
     */
    public function versions()
    {
        return $this->hasMany(File::class, 'parent_id');
    }

    /**
     * Check if the file is a PDF
     */
    public function isPdf()
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Check if content matches any previous version
     *
     * @param string $content_hash
     * @return array|null Returns matching version info or null if no match found
     */
    public function findMatchingVersion(string $content_hash)
    {
        $matching_version = $this->versions()
            ->where('content_hash', $content_hash)
            ->first();

        if ($matching_version) {
            return [
                'version' => $matching_version->version,
                'date' => $matching_version->created_at->format('Y-m-d'),
                'id' => $matching_version->id
            ];
        }

        return null;
    }

    /**
     * Check if the file is an Excel file
     */
    public function isExcel()
    {
        return in_array($this->mime_type, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }

    /**
     * Check if the file is a Word document
     */
    public function isWord()
    {
        return in_array($this->mime_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
    }

    /**
     * Convert Word document to HTML for preview
     */
    public function getHtmlContent()
    {
        if (!$this->isWord()) {
            \Log::debug('File is not a Word document');
            throw new \Exception('This file is not a Word document');
        }

        try {
            $filePath = Storage::path('public/' . $this->path);
            \Log::debug('Starting Word preview generation for file: ' . $this->original_name);
            \Log::debug('Attempting to convert Word file: ' . $filePath);
            
            // Verify file exists
            if (!file_exists($filePath)) {
                throw new \Exception('Word file not found at path: ' . $filePath);
            }

            // Verify file is readable
            if (!is_readable($filePath)) {
                throw new \Exception('Word file is not readable: ' . $filePath);
            }

            // Ensure temp images directory exists and is writable
            $tempImagesDir = storage_path('app/public/temp_images');
            if (!file_exists($tempImagesDir)) {
                if (!mkdir($tempImagesDir, 0755, true)) {
                    throw new \Exception('Failed to create temp images directory');
                }
            }
            
            if (!is_writable($tempImagesDir)) {
                throw new \Exception('Temp images directory is not writable');
            }

            \Log::debug('Loading Word document...');
            try {
                // Try to load the document with special handling for EMF images
                try {
                    $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
                } catch (\Exception $e) {
                    // Check if the error is related to EMF images
                    if (strpos($e->getMessage(), 'Invalid image') !== false && strpos($e->getMessage(), '.emf') !== false) {
                        \Log::warning('EMF image format detected which is not fully supported. Attempting alternative loading method.');
                        
                        // Use a custom configuration to ignore problematic images
                        $phpWord = new \PhpOffice\PhpWord\PhpWord();
                        $reader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
                        
                        // Try to set reader options if the method exists
                        if (method_exists($reader, 'setImageProcessor')) {
                            $reader->setImageProcessor(function($imageData, $mimeType) {
                                // Skip EMF images
                                if (strpos($mimeType, 'emf') !== false) {
                                    return null;
                                }
                                return $imageData;
                            });
                        }
                        
                        $phpWord = $reader->load($filePath);
                    } else {
                        // If it's not an EMF issue, rethrow the exception
                        throw $e;
                    }
                }
            } catch (\Exception $e) {
                throw new \Exception('Failed to load Word document: ' . $e->getMessage());
            }
            \Log::debug('Successfully loaded Word document');

            // Configure HTML writer settings
            try {
                $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
                
                // Set options to handle problematic images if methods exist
                if (method_exists($htmlWriter, 'setImagesHandling')) {
                    $htmlWriter->setImagesHandling('base64');
                }
                
                if (method_exists($htmlWriter, 'setImagesFallback')) {
                    $htmlWriter->setImagesFallback(function($image) {
                        // Return placeholder for EMF images
                        if (isset($image['type']) && strpos($image['type'], 'emf') !== false) {
                            return '<div class="image-placeholder">[Image not supported]</div>';
                        }
                        return null; // Process normally
                    });
                }
            } catch (\Exception $e) {
                throw new \Exception('Failed to configure HTML writer: ' . $e->getMessage());
            }

            \Log::debug('Converting Word to HTML...');
            $htmlContent = '';
            ob_start();
            try {
                $htmlWriter->save('php://output');
                $htmlContent = ob_get_clean();
                if (empty($htmlContent)) {
                    throw new \Exception('HTML conversion produced empty content');
                }
                \Log::debug('Successfully converted Word to HTML');
            } catch (\Exception $e) {
                ob_end_clean();
                throw new \Exception('Failed to convert Word to HTML: ' . $e->getMessage());
            }

            return $htmlContent;
        } catch (\Exception $e) {
            \Log::error('Word preview error for file ' . $this->original_name . ': ' . $e->getMessage());
            \Log::debug('Error trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
}
