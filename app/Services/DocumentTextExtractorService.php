<?php

namespace App\Services;

class DocumentTextExtractorService
{
    public function extractText($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        
        switch (strtolower($extension)) {
            case 'txt':
                return $this->extractFromTxt($filePath);
            case 'pdf':
                return $this->extractFromPdf($filePath);
            case 'doc':
            case 'docx':
                return $this->extractFromWord($filePath);
            case 'jpg':
            case 'jpeg':
            case 'png':
                return $this->extractFromImage($filePath);
            default:
                return "Unknown document type: $extension";
        }
    }

    private function extractFromTxt($filePath)
    {
        if (!file_exists($filePath)) {
            return "File not found";
        }
        
        $content = file_get_contents($filePath);
        return $content ?: "Empty or unreadable text file";
    }

    private function extractFromPdf($filePath)
    {
        // Try to parse PDF text; if parser not available, return best-effort hint text
        try {
            if (class_exists(\Smalot\PdfParser\Parser::class)) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filePath);
                $text = trim($pdf->getText());
                if ($text !== '') {
                    return $text;
                }
            }
        } catch (\Throwable $e) {
            // fallback to simple hint below
        }
        return 'general document content (pdf)';
    }

    private function extractFromWord($filePath)
    {
        // For Word document extraction, you would typically use PhpOffice\PhpWord
        // For now, return a placeholder
        return "Word document content - requires Word parser library installation.
                Document: " . basename($filePath) . "
                Size: " . filesize($filePath) . " bytes
                Type: Microsoft Word document";
    }

    private function extractFromImage($filePath)
    {
        // For image OCR, you would typically use a library like Tesseract
        // For now, return a placeholder
        return "Image document - requires OCR processing.
                Document: " . basename($filePath) . "
                Size: " . filesize($filePath) . " bytes
                Type: Image file";
    }
} 