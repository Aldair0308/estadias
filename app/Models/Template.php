<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Template extends Model
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
     * Get the parent template that this version belongs to
     */
    public function parent()
    {
        return $this->belongsTo(Template::class, 'parent_id');
    }

    /**
     * Get all versions of this template
     */
    public function versions()
    {
        return $this->hasMany(Template::class, 'parent_id');
    }

    /**
     * Check if the template is a PDF
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
        $matching_version = $this->versions()->where('content_hash', $content_hash)->first();
        
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
     * Check if the template is an Excel file
     */
    public function isExcel()
    {
        return in_array($this->mime_type, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }

    /**
     * Check if the template is a Word document
     */
    public function isWord()
    {
        return in_array($this->mime_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
    }

    /**
     * Extract and convert EMF images from a Word document
     * 
     * @param string $filePath Path to the Word document
     * @param string $outputDir Directory to save converted images
     * @return void
     */
    protected function extractAndConvertEmfImages($filePath, $outputDir)
    {
        try {
            \Log::debug('Extracting EMF images from Word document: ' . $filePath);
            
            // Create a temporary directory to extract the DOCX contents
            $tempDir = storage_path('app/temp_' . uniqid());
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            // Copy the DOCX file to the temp directory
            $tempFile = $tempDir . '/' . basename($filePath);
            copy($filePath, $tempFile);
            
            // Extract the DOCX (which is a ZIP file)
            $zip = new \ZipArchive();
            if ($zip->open($tempFile) === true) {
                $zip->extractTo($tempDir);
                $zip->close();
                
                // Look for EMF images in the word/media directory
                $mediaDir = $tempDir . '/word/media';
                if (file_exists($mediaDir)) {
                    $files = scandir($mediaDir);
                    foreach ($files as $file) {
                        if (pathinfo($file, PATHINFO_EXTENSION) === 'emf') {
                            $emfPath = $mediaDir . '/' . $file;
                            $imageHash = md5_file($emfPath);
                            $pngPath = $outputDir . '/' . $imageHash . '.png';
                            
                            // Convert EMF to PNG using GD or Imagick if available
                            if (extension_loaded('imagick')) {
                                $this->convertEmfWithImagick($emfPath, $pngPath);
                            } else {
                                // Fallback to a simple placeholder image using GD
                                $this->createPlaceholderImage($pngPath);
                            }
                            
                            \Log::debug('Converted EMF image: ' . $file . ' to ' . $pngPath);
                        }
                    }
                }
            }
            
            // Clean up
            $this->recursiveDelete($tempDir);
            
        } catch (\Exception $e) {
            \Log::error('Failed to extract EMF images: ' . $e->getMessage());
        }
    }
    
    /**
     * Convert EMF to PNG using ImageMagick
     * 
     * @param string $emfPath Path to EMF file
     * @param string $pngPath Path to save PNG file
     * @return bool Success status
     */
    protected function convertEmfWithImagick($emfPath, $pngPath)
    {
        try {
            $imagick = new \Imagick();
            $imagick->readImage($emfPath);
            $imagick->setImageFormat('png');
            $imagick->writeImage($pngPath);
            $imagick->clear();
            $imagick->destroy();
            return true;
        } catch (\Exception $e) {
            \Log::error('ImageMagick conversion failed: ' . $e->getMessage());
            // Fallback to placeholder
            $this->createPlaceholderImage($pngPath);
            return false;
        }
    }
    
    /**
     * Create a placeholder image for EMF files that can't be converted
     * 
     * @param string $outputPath Path to save placeholder image
     * @return bool Success status
     */
    protected function createPlaceholderImage($outputPath)
    {
        try {
            // Create a simple placeholder image with text
            $width = 400;
            $height = 300;
            $image = imagecreatetruecolor($width, $height);
            
            // Set background color (light gray)
            $bgColor = imagecolorallocate($image, 240, 240, 240);
            imagefill($image, 0, 0, $bgColor);
            
            // Set text color (dark gray)
            $textColor = imagecolorallocate($image, 80, 80, 80);
            
            // Add text
            $text = "EMF Image (Not Displayed)";
            $font = 5; // Built-in font
            
            // Center the text
            $textWidth = imagefontwidth($font) * strlen($text);
            $textHeight = imagefontheight($font);
            $x = ($width - $textWidth) / 2;
            $y = ($height - $textHeight) / 2;
            
            imagestring($image, $font, $x, $y, $text, $textColor);
            
            // Save as PNG
            imagepng($image, $outputPath);
            imagedestroy($image);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to create placeholder image: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Recursively delete a directory and its contents
     * 
     * @param string $dir Directory path
     * @return void
     */
    protected function recursiveDelete($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->recursiveDelete($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
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
                        \Log::warning('EMF image format detected which is not fully supported. Attempting EMF conversion.');
                        
                        // Extract and convert EMF images from the document
                        $this->extractAndConvertEmfImages($filePath, $tempImagesDir);
                        
                        // Use a custom configuration to handle EMF images
                        $phpWord = new \PhpOffice\PhpWord\PhpWord();
                        $reader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
                        
                        // Try to set reader options if the method exists
                        if (method_exists($reader, 'setImageProcessor')) {
                            $reader->setImageProcessor(function($imageData, $mimeType) use ($tempImagesDir) {
                                // Handle EMF images by replacing with converted PNG
                                if (strpos($mimeType, 'emf') !== false) {
                                    // Generate a hash of the image data to find its converted version
                                    $imageHash = md5($imageData);
                                    $convertedImagePath = $tempImagesDir . '/' . $imageHash . '.png';
                                    
                                    if (file_exists($convertedImagePath)) {
                                        \Log::debug('Using converted EMF image: ' . $convertedImagePath);
                                        return file_get_contents($convertedImagePath);
                                    }
                                    
                                    // If conversion failed, create a placeholder image on the fly
                                    \Log::warning('Converted EMF image not found: ' . $imageHash . '. Creating placeholder.');
                                    $this->createPlaceholderImage($convertedImagePath);
                                    if (file_exists($convertedImagePath)) {
                                        return file_get_contents($convertedImagePath);
                                    }
                                    return null;
                                }
                                return $imageData;
                            });
                        }
                        
                        try {
                            $phpWord = $reader->load($filePath);
                        } catch (\Exception $innerException) {
                            \Log::error('Still failed to load document after EMF conversion: ' . $innerException->getMessage());
                            // Create a new empty document as fallback
                            $phpWord = new \PhpOffice\PhpWord\PhpWord();
                            $section = $phpWord->addSection();
                            $section->addText('Este documento contiene imágenes en formato EMF que no pueden ser mostradas en la vista previa.');
                            $section->addText('Por favor, descargue el documento para verlo correctamente.');
                        }
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
                    $htmlWriter->setImagesFallback(function($image) use ($tempImagesDir) {
                        // Handle EMF images by using our converted PNG versions
                        if (isset($image['type']) && strpos($image['type'], 'emf') !== false) {
                            // Check if we have a converted version of this EMF image
                            if (isset($image['data'])) {
                                $imageHash = md5($image['data']);
                                $convertedImagePath = $tempImagesDir . '/' . $imageHash . '.png';
                                
                                if (file_exists($convertedImagePath)) {
                                    // Use the converted PNG image
                                    $imgData = file_get_contents($convertedImagePath);
                                    $base64 = base64_encode($imgData);
                                    return '<img src="data:image/png;base64,' . $base64 . '" alt="Converted EMF Image" />';
                                }
                            }
                            
                            // If no converted image is found, show a warning message
                            return '<div class="image-placeholder" style="border: 1px solid #ccc; padding: 10px; background-color: #f8f8f8; text-align: center;">' . 
                                   '<p style="color: #666;">Este documento contiene imágenes en formato EMF que no pueden ser mostradas en la vista previa.</p>' . 
                                   '<p style="color: #666;">Por favor, descargue el documento para verlo correctamente.</p>' . 
                                   '</div>';
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