<?php

namespace App\View\Components;

use Illuminate\View\Component;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

class EditorWord extends Component
{
    public $documentContent;
    public $documentId;

    public function __construct($documentContent, $documentId)
    {
        $this->documentContent = $documentContent;
        $this->documentId = $documentId;
    }

    public function render()
    {
        return view('components.editor-word', [
            'documentContent' => $this->documentContent,
            'documentId' => $this->documentId
        ]);
    }

    public static function convertWordToHtml($filePath)
    {
        try {
            Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));
            Settings::setPdfRendererName('DomPDF');

            $phpWord = IOFactory::load($filePath);
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
            
            ob_start();
            $htmlWriter->save('php://output');
            $content = ob_get_clean();
            
            // Preserve styles and formatting
            $content = preg_replace('/<!DOCTYPE[^>]*>/i', '', $content);
            $content = preg_replace('/<\/?html[^>]*>/i', '', $content);
            $content = preg_replace('/<head>.*?<\/head>/is', '', $content);
            $content = preg_replace('/<\/?body[^>]*>/i', '', $content);
            
            // Extract and preserve style information
            if (preg_match('/<style[^>]*>(.*?)<\/style>/is', $content, $matches)) {
                $styles = $matches[1];
                $content = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $content);
                $content = '<style>' . $styles . '</style>' . $content;
            }
            
            // Clean up unnecessary tags while preserving content structure
            $content = preg_replace('/<\/?div[^>]*>/i', '', $content);
            $content = str_replace('\n', '', $content);
            $content = preg_replace('/\s+/', ' ', $content);
            
            return $content;
        } catch (\Exception $e) {
            return null;
        }
    }
}