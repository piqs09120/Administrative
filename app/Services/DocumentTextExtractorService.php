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
            case 'xls':
            case 'xlsx':
                return $this->extractFromSpreadsheet($filePath);
            case 'ppt':
            case 'pptx':
                return $this->extractFromPresentation($filePath);
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
        // Include file name to help downstream keyword classification (e.g., memo, contract)
        return 'pdf file (likely scanned or image-based) - filename: ' . basename($filePath);
    }

    private function extractFromWord($filePath)
    {
        try {
            // Handle only DOCX reliably; DOC is legacy and often unsupported
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if ($extension === 'docx' && class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
                $text = '';
                foreach ($phpWord->getSections() as $section) {
                    $elements = $section->getElements();
                    foreach ($elements as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText() . " \n";
                        }
                    }
                }
                $text = trim($text);
                if ($text !== '') {
                    return $text;
                }
            }
        } catch (\Throwable $e) {
            // fall through to placeholder
        }

        return 'word document - filename: ' . basename($filePath);
    }

    private function extractFromImage($filePath)
    {
        // Try OCR via `tesseract` CLI if available; otherwise return descriptive placeholder
        try {
            // Detect if tesseract is available
            $which = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where' : 'which';
            $hasTesseract = trim((string) @shell_exec($which . ' tesseract')) !== '';
            if ($hasTesseract) {
                $tempTxt = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ocr_' . uniqid();
                // Execute: tesseract input outputbase -l eng
                @shell_exec('tesseract ' . escapeshellarg($filePath) . ' ' . escapeshellarg($tempTxt) . ' -l eng 2> NUL');
                $txtPath = $tempTxt . '.txt';
                if (file_exists($txtPath)) {
                    $text = trim((string) @file_get_contents($txtPath));
                    @unlink($txtPath);
                    if ($text !== '') {
                        return $text;
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore and fallback
        }
        return "Image document - OCR not available or produced no text. Document: " . basename($filePath) .
               " | Size: " . @filesize($filePath) . " bytes | Type: Image file";
    }

    private function extractFromSpreadsheet($filePath)
    {
        try {
            if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $text = '';
                foreach ($spreadsheet->getAllSheets() as $sheet) {
                    $rows = $sheet->toArray(null, true, true, true);
                    foreach ($rows as $row) {
                        $line = array_filter(array_map('trim', array_values($row)), function ($v) { return $v !== null && $v !== ''; });
                        if (!empty($line)) {
                            $text .= implode(' | ', $line) . "\n";
                        }
                    }
                }
                $text = trim($text);
                if ($text !== '') {
                    return $text;
                }
            }
        } catch (\Throwable $e) {
            // ignore and fallback
        }
        return 'spreadsheet file - filename: ' . basename($filePath);
    }

    private function extractFromPresentation($filePath)
    {
        // PHPPresentation is not installed; provide descriptive placeholder
        return 'presentation file - filename: ' . basename($filePath);
    }
} 