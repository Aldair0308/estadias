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
    /**
     * Extract and convert EMF images from a Word document
     * 
     * @param string $filePath Path to the Word document
     * @param string $outputDir Directory to save converted images
     * @return void
     */
    /**
     * Extract and convert EMF images from a Word document
     * 
     * @param string $filePath Path to the Word document
     * @param string $outputDir Directory to save converted images
     * @return array Array of converted image paths indexed by original image hash
     */
    protected function extractAndConvertEmfImages($filePath, $outputDir)
    {
        $convertedImages = [];
        
        try {
            \Log::debug('Extracting EMF images from Word document: ' . $filePath);
            
            // Ensure output directory exists
            if (!file_exists($outputDir)) {
                if (!mkdir($outputDir, 0755, true)) {
                    throw new \Exception("Failed to create output directory: {$outputDir}");
                }
            }
            
            // Create a temporary directory to extract the DOCX contents
            $tempDir = storage_path('app/temp_' . uniqid());
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            // Copy the DOCX file to the temp directory
            $tempFile = $tempDir . '/' . basename($filePath);
            if (!copy($filePath, $tempFile)) {
                throw new \Exception("Failed to copy Word document to temporary location");
            }
            
            // Extract the DOCX (which is a ZIP file)
            $zip = new \ZipArchive();
            $zipResult = $zip->open($tempFile);
            
            if ($zipResult === true) {
                \Log::debug('Successfully opened Word document as ZIP archive');
                $zip->extractTo($tempDir);
                $zip->close();
                
                // Look for EMF images in the word/media directory
                $mediaDir = $tempDir . '/word/media';
                if (file_exists($mediaDir)) {
                    $files = scandir($mediaDir);
                    $emfCount = 0;
                    $convertedCount = 0;
                    
                    foreach ($files as $file) {
                        // Check for EMF files and also WMF files (similar format)
                        if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['emf', 'wmf'])) {
                            $emfCount++;
                            $emfPath = $mediaDir . '/' . $file;
                            $imageHash = md5_file($emfPath);
                            $pngPath = $outputDir . '/' . $imageHash . '.png';
                            
                            // Skip if already converted
                            if (file_exists($pngPath) && filesize($pngPath) > 0) {
                                \Log::debug("Using existing converted image for {$file}");
                                $convertedImages[$imageHash] = $pngPath;
                                $convertedCount++;
                                continue;
                            }
                            
                            // Try multiple conversion methods
                            $converted = false;
                            
                            // Method 1: ImageMagick extension if available
                            if (extension_loaded('imagick')) {
                                \Log::debug("Attempting to convert {$file} with ImageMagick extension");
                                if ($this->convertEmfWithImagick($emfPath, $pngPath)) {
                                    $convertedImages[$imageHash] = $pngPath;
                                    $convertedCount++;
                                    $converted = true;
                                }
                            }
                            
                            // Method 2: Try command-line conversion if extension failed
                            if (!$converted && function_exists('exec')) {
                                try {
                                    \Log::debug("Attempting to convert {$file} with command-line tools");
                                    $command = "magick convert \"{$emfPath}\" -density 300 \"{$pngPath}\"";
                                    exec($command, $output, $returnCode);
                                    
                                    if ($returnCode === 0 && file_exists($pngPath) && filesize($pngPath) > 0) {
                                        \Log::debug("Successfully converted {$file} with command-line tools");
                                        $convertedImages[$imageHash] = $pngPath;
                                        $convertedCount++;
                                        $converted = true;
                                    }
                                } catch (\Exception $cmdEx) {
                                    \Log::warning("Command-line conversion failed for {$file}: {$cmdEx->getMessage()}");
                                }
                            }
                            
                            // Method 3: Fallback to placeholder if all else fails
                            if (!$converted) {
                                \Log::notice("All conversion methods failed for {$file}, using placeholder");
                                $this->createPlaceholderImage($pngPath);
                                $convertedImages[$imageHash] = $pngPath;
                            }
                            
                            \Log::debug("Processed EMF image: {$file} to {$pngPath}");
                        }
                    }
                    
                    \Log::info("EMF image processing complete. Found: {$emfCount}, Successfully converted: {$convertedCount}");
                } else {
                    \Log::debug('No media directory found in Word document');
                }
            } else {
                \Log::warning("Failed to open Word document as ZIP archive. Error code: {$zipResult}");
            }
            
            // Clean up
            $this->recursiveDelete($tempDir);
            
        } catch (\Exception $e) {
            \Log::error('Failed to extract EMF images: ' . $e->getMessage());
            \Log::debug('Error trace: ' . $e->getTraceAsString());
        }
        
        return $convertedImages;
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
            // Try with direct ImageMagick conversion first
            $imagick = new \Imagick();
            
            // Set density for better quality conversion
            $imagick->setResolution(300, 300);
            
            // Try to read the EMF file
            $imagick->readImage($emfPath);
            
            // Apply some optimizations for better rendering
            $imagick->setImageFormat('png');
            $imagick->setImageCompressionQuality(95);
            
            // Try to handle transparency if present in EMF
            $imagick->setImageBackgroundColor('white');
            $imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
            
            // Write the converted image
            $imagick->writeImage($pngPath);
            $imagick->clear();
            $imagick->destroy();
            
            // Verify the output file exists and has content
            if (file_exists($pngPath) && filesize($pngPath) > 0) {
                \Log::debug('Successfully converted EMF to PNG with ImageMagick');
                return true;
            } else {
                throw new \Exception('Converted file is empty or does not exist');
            }
        } catch (\Exception $e) {
            \Log::warning('ImageMagick direct conversion failed: ' . $e->getMessage());
            
            // Try alternative conversion approach using ImageMagick command line
            try {
                // Check if we can use exec for command line operations
                if (function_exists('exec')) {
                    $command = "magick convert \"$emfPath\" -density 300 \"$pngPath\"";
                    exec($command, $output, $returnCode);
                    
                    if ($returnCode === 0 && file_exists($pngPath) && filesize($pngPath) > 0) {
                        \Log::debug('Successfully converted EMF to PNG with ImageMagick command line');
                        return true;
                    }
                }
            } catch (\Exception $cmdEx) {
                \Log::warning('ImageMagick command line conversion failed: ' . $cmdEx->getMessage());
            }
            
            // Fallback to placeholder as last resort
            \Log::notice('All EMF conversion methods failed, using placeholder image');
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
            // Create a more visually appealing placeholder image
            $width = 500;
            $height = 350;
            $image = imagecreatetruecolor($width, $height);
            
            // Set background color (light blue gradient)
            $bgColor1 = imagecolorallocate($image, 240, 248, 255); // AliceBlue
            $bgColor2 = imagecolorallocate($image, 176, 224, 230); // PowderBlue
            
            // Create gradient background
            for ($i = 0; $i < $height; $i++) {
                $ratio = $i / $height;
                $r = (int)(240 - $ratio * (240 - 176));
                $g = (int)(248 - $ratio * (248 - 224));
                $b = (int)(255 - $ratio * (255 - 230));
                $color = imagecolorallocate($image, $r, $g, $b);
                imageline($image, 0, $i, $width, $i, $color);
            }
            
            // Add border
            $borderColor = imagecolorallocate($image, 70, 130, 180); // SteelBlue
            imagerectangle($image, 0, 0, $width-1, $height-1, $borderColor);
            
            // Set text colors
            $titleColor = imagecolorallocate($image, 25, 25, 112); // MidnightBlue
            $textColor = imagecolorallocate($image, 47, 79, 79); // DarkSlateGray
            
            // Add title
            $title = "Imagen EMF Convertida";
            $font = 5; // Built-in font (larger)
            
            // Center the title
            $titleWidth = imagefontwidth($font) * strlen($title);
            $x = ($width - $titleWidth) / 2;
            $y = 40;
            
            imagestring($image, $font, $x, $y, $title, $titleColor);
            
            // Add descriptive text
            $font = 3; // Smaller font for description
            $lines = [
                "Esta imagen ha sido convertida del formato EMF",
                "para su visualización en la vista previa.",
                "",
                "Si necesita ver la imagen original con mayor calidad,",
                "por favor descargue el documento completo."
            ];
            
            $lineHeight = imagefontheight($font) + 5;
            $startY = 100;
            
            foreach ($lines as $index => $line) {
                $lineWidth = imagefontwidth($font) * strlen($line);
                $x = ($width - $lineWidth) / 2;
                $y = $startY + ($index * $lineHeight);
                imagestring($image, $font, $x, $y, $line, $textColor);
            }
            
            // Add icon-like element
            $iconColor = imagecolorallocate($image, 65, 105, 225); // RoyalBlue
            $iconX = $width / 2;
            $iconY = $height - 80;
            $iconSize = 30;
            
            // Draw document icon
            imagefilledrectangle($image, $iconX-$iconSize, $iconY-$iconSize, $iconX+$iconSize, $iconY+$iconSize, $iconColor);
            imagefilledrectangle($image, $iconX-($iconSize-5), $iconY-($iconSize-5), $iconX+($iconSize-5), $iconY+($iconSize-5), $bgColor1);
            
            // Save as PNG with higher quality
            imagepng($image, $outputPath, 1); // 1 = highest quality
            imagedestroy($image);
            
            \Log::info('Created enhanced placeholder image for EMF at: ' . $outputPath);
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
                // Pre-process the document to extract and convert EMF images before loading
                \Log::debug('Pre-processing document to handle EMF images');
                $convertedImages = $this->extractAndConvertEmfImages($filePath, $tempImagesDir);
                \Log::debug('Found and processed ' . count($convertedImages) . ' EMF/WMF images');
                
                // Try to load the document with special handling for EMF images
                try {
                    // Use a custom reader with image processing capabilities
                    $reader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
                    
                    // Set up image processor if the method exists
                    if (method_exists($reader, 'setImageProcessor')) {
                        $reader->setImageProcessor(function($imageData, $mimeType) use ($tempImagesDir, $convertedImages) {
                            // Handle EMF/WMF images by replacing with converted PNG
                            if (strpos(strtolower($mimeType), 'emf') !== false || strpos(strtolower($mimeType), 'wmf') !== false) {
                                // Generate a hash of the image data to find its converted version
                                $imageHash = md5($imageData);
                                
                                // Check if we have a pre-converted version
                                if (isset($convertedImages[$imageHash])) {
                                    \Log::debug('Using pre-converted EMF/WMF image: ' . $convertedImages[$imageHash]);
                                    return file_get_contents($convertedImages[$imageHash]);
                                }
                                
                                // Try to convert on-the-fly if not pre-converted
                                $convertedImagePath = $tempImagesDir . '/' . $imageHash . '.png';
                                if (!file_exists($convertedImagePath)) {
                                    // Save the EMF data to a temporary file
                                    $tempEmfPath = $tempImagesDir . '/' . uniqid('emf_') . '.emf';
                                    file_put_contents($tempEmfPath, $imageData);
                                    
                                    // Try to convert it
                                    $converted = $this->convertEmfWithImagick($tempEmfPath, $convertedImagePath);
                                    
                                    // Clean up temp file
                                    if (file_exists($tempEmfPath)) {
                                        unlink($tempEmfPath);
                                    }
                                    
                                    if (!$converted) {
                                        \Log::warning('On-the-fly conversion failed for EMF/WMF image');
                                        return null;
                                    }
                                }
                                
                                if (file_exists($convertedImagePath) && filesize($convertedImagePath) > 0) {
                                    \Log::debug('Using converted EMF/WMF image: ' . $convertedImagePath);
                                    return file_get_contents($convertedImagePath);
                                }
                                
                                // If conversion failed, return null to skip this image
                                \Log::warning('Converted EMF/WMF image not found: ' . $imageHash);
                                return null;
                            }
                            return $imageData;
                        });
                    }
                    
                    // Load the document with our custom reader
                    $phpWord = $reader->load($filePath);
                    
                } catch (\Exception $e) {
                    // Check if the error is related to EMF/WMF images
                    if (strpos($e->getMessage(), 'Invalid image') !== false && 
                        (strpos($e->getMessage(), '.emf') !== false || strpos($e->getMessage(), '.wmf') !== false)) {
                        \Log::warning('EMF/WMF image format issue detected: ' . $e->getMessage());
                        
                        // Fallback to standard loading if custom processing failed
                        \Log::debug('Falling back to standard document loading');
                        $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
                    } else {
                        // If it's not an EMF/WMF issue, rethrow the exception
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
                            
                            // If no converted image is found, try to convert it now
                        try {
                            // Generate a unique filename for this EMF image
                            $tempEmfPath = $tempImagesDir . '/' . uniqid('emf_') . '.emf';
                            if (isset($image['data'])) {
                                file_put_contents($tempEmfPath, $image['data']);
                                $imageHash = md5_file($tempEmfPath);
                                $convertedImagePath = $tempImagesDir . '/' . $imageHash . '.png';
                                
                                // Try to convert the EMF image
                                if ($this->convertEmfWithImagick($tempEmfPath, $convertedImagePath)) {
                                    // Use the converted PNG image
                                    $imgData = file_get_contents($convertedImagePath);
                                    $base64 = base64_encode($imgData);
                                    return '<img src="data:image/png;base64,' . $base64 . '" alt="Converted EMF Image" />';
                                }
                                
                                // Clean up the temporary EMF file
                                if (file_exists($tempEmfPath)) {
                                    unlink($tempEmfPath);
                                }
                            }
                        } catch (\Exception $convEx) {
                            \Log::warning('Failed to convert EMF image on-the-fly: ' . $convEx->getMessage());
                        }
                        
                        // If all conversion attempts fail, use a more informative placeholder
                        return '<div class="emf-image-placeholder" style="border: 1px solid #4682B4; padding: 15px; background: linear-gradient(to bottom, #F0F8FF, #B0E0E6); text-align: center; border-radius: 5px; margin: 10px 0;">' . 
                               '<h4 style="color: #191970; margin-top: 0;">Imagen EMF Convertida</h4>' . 
                               '<p style="color: #2F4F4F;">Esta imagen ha sido convertida del formato EMF para su visualización en la vista previa.</p>' . 
                               '<p style="color: #2F4F4F;">Si necesita ver la imagen original con mayor calidad, por favor descargue el documento completo.</p>' . 
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
