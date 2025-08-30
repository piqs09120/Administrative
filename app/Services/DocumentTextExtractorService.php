<?php

namespace App\Services;

class DocumentTextExtractorService
{
    public function extractText($filePath)
    {
        // Handle temporary files by detecting actual file type
        $extension = $this->detectActualFileExtension($filePath);
        
        \Log::info('DocumentTextExtractor: Starting text extraction', [
            'file_path' => $filePath,
            'detected_extension' => $extension,
            'file_exists' => file_exists($filePath),
            'file_size' => file_exists($filePath) ? filesize($filePath) : 'N/A'
        ]);
        
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
            \Log::error('DocumentTextExtractor: Text file not found', ['file_path' => $filePath]);
            return "File not found";
        }
        
        $content = file_get_contents($filePath);
        $extractedText = $content ?: "Empty or unreadable text file";
        
        // Sanitize the extracted text
        $extractedText = $this->sanitizeText($extractedText);
        
        \Log::info('DocumentTextExtractor: Text extraction completed', [
            'file_path' => $filePath,
            'text_length' => strlen($extractedText),
            'text_preview' => substr($extractedText, 0, 200)
        ]);
        
        return $extractedText;
    }

    private function extractFromPdf($filePath)
    {
        \Log::info('DocumentTextExtractor: Starting PDF text extraction', [
            'file_path' => $filePath,
            'file_size' => filesize($filePath)
        ]);
        
        // Method 1: Try Smalot PDF Parser (most reliable for text-based PDFs)
        try {
            if (class_exists(\Smalot\PdfParser\Parser::class)) {
                \Log::info('DocumentTextExtractor: Using Smalot PDF parser');
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filePath);
                $text = trim($pdf->getText());
                
                if ($text !== '' && strlen($text) > 50) { // Ensure we have substantial text
                    $text = $this->sanitizeText($text); // Sanitize the extracted text
                    \Log::info('DocumentTextExtractor: Smalot parser successful', [
                        'text_length' => strlen($text),
                        'text_preview' => substr($text, 0, 200),
                        'extraction_method' => 'smalot_parser'
                    ]);
                    return $text;
                } else {
                    \Log::warning('DocumentTextExtractor: Smalot parser returned insufficient text', [
                        'text_length' => strlen($text),
                        'text_preview' => substr($text, 0, 100)
                    ]);
                }
            } else {
                \Log::info('DocumentTextExtractor: Smalot PDF parser not available');
            }
        } catch (\Throwable $e) {
            \Log::warning('DocumentTextExtractor: Smalot parser failed', [
                'error' => $e->getMessage(),
                'falling_back_to_pdftotext' => true
            ]);
        }
        
        // Method 2: Try pdftotext (poppler-utils) - excellent for most PDFs
        try {
            $pdftotext = $this->getExecutablePath('pdftotext', [
                'C:\\Program Files\\poppler\\bin\\pdftotext.exe',
                'C:\\Program Files (x86)\\poppler\\bin\\pdftotext.exe',
                '/usr/bin/pdftotext',
                '/usr/local/bin/pdftotext'
            ]);
            
            if (!empty($pdftotext)) {
                \Log::info('DocumentTextExtractor: Using pdftotext', ['path' => $pdftotext]);
                
                // Try with layout preservation first
                $cmd = escapeshellarg($pdftotext) . ' -layout -nopgbrk ' . escapeshellarg($filePath) . ' -';
                $out = @shell_exec($cmd);
                $out = is_string($out) ? trim($out) : '';
                
                if ($out !== '' && strlen($out) > 50) {
                    $out = $this->sanitizeText($out); // Sanitize the extracted text
                    \Log::info('DocumentTextExtractor: pdftotext successful with layout', [
                        'text_length' => strlen($out),
                        'text_preview' => substr($out, 0, 200),
                        'extraction_method' => 'pdftotext_layout'
                    ]);
                    return $out;
                }
                
                // Try without layout if layout failed
                $cmd = escapeshellarg($pdftotext) . ' ' . escapeshellarg($filePath) . ' -';
                $out = @shell_exec($cmd);
                $out = is_string($out) ? trim($out) : '';
                
                if ($out !== '' && strlen($out) > 50) {
                    $out = $this->sanitizeText($out); // Sanitize the extracted text
                    \Log::info('DocumentTextExtractor: pdftotext successful without layout', [
                        'text_length' => strlen($out),
                        'text_preview' => substr($out, 0, 200),
                        'extraction_method' => 'pdftotext_basic'
                    ]);
                    return $out;
                } else {
                    \Log::warning('DocumentTextExtractor: pdftotext returned insufficient output', [
                        'text_length' => strlen($out),
                        'text_preview' => substr($out, 0, 100)
                    ]);
                }
            } else {
                \Log::info('DocumentTextExtractor: pdftotext not available');
            }
        } catch (\Throwable $e) {
            \Log::warning('DocumentTextExtractor: pdftotext failed', [
                'error' => $e->getMessage(),
                'falling_back_to_tesseract' => true
            ]);
        }
        
        // Method 3: Tesseract OCR (for scanned PDFs and images)
        try {
            $tesseract = $this->getExecutablePath('tesseract', [
                'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
                'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe',
                '/usr/bin/tesseract',
                '/usr/local/bin/tesseract'
            ], env('TESSERACT_PATH'));
            
            if (!empty($tesseract)) {
                \Log::info('DocumentTextExtractor: Using Tesseract OCR for PDF', ['path' => $tesseract]);
                
                // For PDFs, we need to convert to image first using pdftoppm
                $pdftoppm = $this->getExecutablePath('pdftoppm', [
                    'C:\\Program Files\\poppler\\bin\\pdftoppm.exe',
                    'C:\\Program Files (x86)\\poppler\\bin\\pdftoppm.exe',
                    '/usr/bin/pdftoppm',
                    '/usr/local/bin/pdftoppm'
                ]);
                
                if (!empty($pdftoppm)) {
                    // Convert first page to PNG for OCR
                    $tempDir = sys_get_temp_dir();
                    $tempImage = $tempDir . '/pdf_page_' . uniqid() . '.png';
                    
                    $cmd = escapeshellarg($pdftoppm) . ' -png -singlefile ' . escapeshellarg($filePath) . ' ' . escapeshellarg($tempImage);
                    @shell_exec($cmd);
                    
                    if (file_exists($tempImage)) {
                        // OCR the image
                        $cmd = escapeshellarg($tesseract) . ' ' . escapeshellarg($tempImage) . ' stdout -l eng --psm 6';
                        $ocr = @shell_exec($cmd);
                        $ocr = is_string($ocr) ? trim($ocr) : '';
                        
                        // Clean up temp file
                        @unlink($tempImage);
                        
                        if ($ocr !== '' && strlen($ocr) > 30) {
                            $ocr = $this->sanitizeText($ocr); // Sanitize the extracted text
                            \Log::info('DocumentTextExtractor: Tesseract OCR successful for PDF', [
                                'text_length' => strlen($ocr),
                                'text_preview' => substr($ocr, 0, 200),
                                'extraction_method' => 'tesseract_pdf_ocr'
                            ]);
                            return $ocr;
                        }
                    }
                }
                
                // If pdftoppm not available, try direct OCR (may not work for PDFs)
                $cmd = escapeshellarg($tesseract) . ' ' . escapeshellarg($filePath) . ' stdout -l eng';
                $ocr = @shell_exec($cmd);
                $ocr = is_string($ocr) ? trim($ocr) : '';
                
                if ($ocr !== '' && strlen($ocr) > 30) {
                    $ocr = $this->sanitizeText($ocr); // Sanitize the extracted text
                    \Log::info('DocumentTextExtractor: Tesseract direct OCR successful for PDF', [
                        'text_length' => strlen($ocr),
                        'text_preview' => substr($ocr, 0, 200),
                        'extraction_method' => 'tesseract_direct'
                    ]);
                    return $ocr;
                } else {
                    \Log::warning('DocumentTextExtractor: Tesseract OCR returned insufficient output for PDF', [
                        'text_length' => strlen($ocr),
                        'text_preview' => substr($ocr, 0, 100)
                    ]);
                }
            } else {
                \Log::warning('DocumentTextExtractor: Tesseract not available for PDF OCR');
            }
        } catch (\Throwable $e) {
            \Log::warning('DocumentTextExtractor: Tesseract failed for PDF', [
                'error' => $e->getMessage()
            ]);
        }

        // All extraction methods failed - provide detailed logging
        \Log::error('DocumentTextExtractor: All PDF text extraction methods failed', [
            'file_path' => $filePath,
            'file_size' => filesize($filePath),
            'methods_tried' => ['smalot_parser', 'pdftotext', 'tesseract_ocr'],
            'fallback_reason' => 'No text extraction method succeeded'
        ]);

        // Return a more descriptive fallback message
        $filename = basename($filePath);
        return "PDF text extraction failed - document may be scanned or image-based. Filename: $filename. Manual review recommended.";
    }

    private function extractFromWord($filePath)
    {
        \Log::info('DocumentTextExtractor: Starting Word document text extraction', [
            'file_path' => $filePath
        ]);
        
        try {
            // Handle only DOCX reliably; DOC is legacy and often unsupported
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if ($extension === 'docx' && class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
                \Log::info('DocumentTextExtractor: Using PhpWord for DOCX');
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
                $text = '';
                
                // Extract text from all sections and elements
                foreach ($phpWord->getSections() as $section) {
                    $elements = $section->getElements();
                    foreach ($elements as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText() . " \n";
                        } elseif (method_exists($element, 'getElements')) {
                            // Handle nested elements (like tables)
                            foreach ($element->getElements() as $nestedElement) {
                                if (method_exists($nestedElement, 'getText')) {
                                    $text .= $nestedElement->getText() . " ";
                                }
                            }
                            $text .= "\n";
                        }
                    }
                }
                
                $text = trim($text);
                
                // Validate extracted text quality
                if ($text !== '' && $text !== 'tmp' && strlen($text) > 20) {
                    $text = $this->sanitizeText($text); // Sanitize the extracted text
                    \Log::info('DocumentTextExtractor: PhpWord extraction successful', [
                        'text_length' => strlen($text),
                        'text_preview' => substr($text, 0, 200),
                        'extraction_method' => 'phpword'
                    ]);
                    return $text;
                } else {
                    \Log::warning('DocumentTextExtractor: PhpWord returned insufficient text', [
                        'text_length' => strlen($text),
                        'text_preview' => substr($text, 0, 100),
                        'text_quality' => 'insufficient'
                    ]);
                }
            } else {
                \Log::info('DocumentTextExtractor: PhpWord not available or unsupported format', [
                    'extension' => $extension,
                    'phpword_available' => class_exists(\PhpOffice\PhpWord\IOFactory::class)
                ]);
            }
        } catch (\Throwable $e) {
            \Log::warning('DocumentTextExtractor: PhpWord extraction failed', [
                'error' => $e->getMessage(),
                'falling_back_to_filename' => true
            ]);
        }

        // Enhanced fallback for Word documents with better filename analysis
        $filename = basename($filePath);
        $lowerFilename = strtolower($filename);
        
        \Log::info('DocumentTextExtractor: Using enhanced filename-based fallback for Word document', [
            'filename' => $filename,
            'fallback_reason' => 'PhpWord extraction failed or insufficient'
        ]);
        
        // Check filename for document type indicators with better mapping
        $documentTypeMap = [
            'memo' => 'memorandum',
            'memorandum' => 'memorandum',
            'contract' => 'contract',
            'agreement' => 'contract',
            'policy' => 'policy',
            'privacy' => 'policy',
            'terms' => 'policy',
            'report' => 'report',
            'analysis' => 'report',
            'assessment' => 'report',
            'invoice' => 'financial',
            'receipt' => 'financial',
            'budget' => 'financial',
            'affidavit' => 'legal',
            'subpoena' => 'legal',
            'legal' => 'legal',
            'compliance' => 'compliance',
            'regulation' => 'compliance'
        ];
        
        foreach ($documentTypeMap as $indicator => $category) {
            if (strpos($lowerFilename, $indicator) !== false) {
                \Log::info('DocumentTextExtractor: Determined category from filename', [
                    'filename' => $filename,
                    'indicator' => $indicator,
                    'category' => $category
                ]);
                return "$category document - filename indicates $category type: $filename";
            }
        }
        
        // Default fallback
        return 'word document - filename: ' . $filename;
    }

    private function extractFromImage($filePath)
    {
        \Log::info('DocumentTextExtractor: Starting image OCR extraction', [
            'file_path' => $filePath
        ]);
        
        // Attempt OCR via system tesseract if available
        try {
            $tesseract = $this->getExecutablePath('tesseract', [
                'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
                'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe',
                '/usr/bin/tesseract',
                '/usr/local/bin/tesseract'
            ], env('TESSERACT_PATH'));
            
            if (!empty($tesseract)) {
                \Log::info('DocumentTextExtractor: Using Tesseract for image OCR', ['path' => $tesseract]);
                
                // Try different PSM modes for better OCR results
                $psmModes = [6, 8, 3]; // 6=uniform block, 8=single word, 3=fully automatic
                $bestResult = '';
                $bestPsm = 0;
                
                foreach ($psmModes as $psm) {
                    $cmd = escapeshellarg($tesseract) . ' ' . escapeshellarg($filePath) . ' stdout -l eng --psm ' . $psm;
                    $text = @shell_exec($cmd);
                    $text = is_string($text) ? trim($text) : '';
                    
                    if ($text !== '' && strlen($text) > strlen($bestResult)) {
                        $bestResult = $text;
                        $bestPsm = $psm;
                    }
                }
                
                if ($bestResult !== '' && strlen($bestResult) > 20) {
                    $bestResult = $this->sanitizeText($bestResult); // Sanitize the extracted text
                    \Log::info('DocumentTextExtractor: Image OCR successful', [
                        'text_length' => strlen($bestResult),
                        'text_preview' => substr($bestResult, 0, 200),
                        'extraction_method' => 'tesseract_ocr',
                        'best_psm_mode' => $bestPsm
                    ]);
                    return $bestResult;
                } else {
                    \Log::warning('DocumentTextExtractor: Image OCR returned insufficient text', [
                        'text_length' => strlen($bestResult),
                        'text_preview' => substr($bestResult, 0, 100),
                        'psm_modes_tried' => $psmModes,
                        'best_psm' => $bestPsm
                    ]);
                }
            } else {
                \Log::warning('DocumentTextExtractor: Tesseract not available for image OCR');
            }
        } catch (\Throwable $e) {
            \Log::warning('DocumentTextExtractor: Image OCR failed', [
                'error' => $e->getMessage(),
                'falling_back_to_filename' => true
            ]);
        }
        
        // Enhanced fallback for images with filename analysis
        $filename = basename($filePath);
        $lowerFilename = strtolower($filename);
        
        \Log::warning('DocumentTextExtractor: Image OCR failed, using enhanced filename fallback', [
            'filename' => $filename,
            'fallback_reason' => 'OCR extraction failed or insufficient'
        ]);
        
        // Check filename for document type indicators
        $imageTypeMap = [
            'policy' => 'policy',
            'privacy' => 'policy',
            'contract' => 'contract',
            'agreement' => 'contract',
            'memo' => 'memorandum',
            'memorandum' => 'memorandum',
            'report' => 'report',
            'invoice' => 'financial',
            'receipt' => 'financial',
            'id' => 'identification',
            'passport' => 'identification',
            'license' => 'identification'
        ];
        
        foreach ($imageTypeMap as $indicator => $category) {
            if (strpos($lowerFilename, $indicator) !== false) {
                \Log::info('DocumentTextExtractor: Determined image category from filename', [
                    'filename' => $filename,
                    'indicator' => $indicator,
                    'category' => $category
                ]);
                return "$category document image - filename indicates $category type: $filename";
            }
        }
        
        return 'image file - filename: ' . $filename;
    }

    private function extractFromSpreadsheet($filePath)
    {
        \Log::info('DocumentTextExtractor: Starting spreadsheet text extraction', [
            'file_path' => $filePath
        ]);
        
        try {
            if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                \Log::info('DocumentTextExtractor: Using PhpSpreadsheet');
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
                    $text = $this->sanitizeText($text); // Sanitize the extracted text
                    \Log::info('DocumentTextExtractor: Spreadsheet extraction successful', [
                        'text_length' => strlen($text),
                        'text_preview' => substr($text, 0, 200)
                    ]);
                    return $text;
                } else {
                    \Log::warning('DocumentTextExtractor: Spreadsheet extraction returned empty text');
                }
            } else {
                \Log::info('DocumentTextExtractor: PhpSpreadsheet not available');
            }
        } catch (\Throwable $e) {
            \Log::warning('DocumentTextExtractor: Spreadsheet extraction failed', [
                'error' => $e->getMessage()
            ]);
        }
        
        \Log::warning('DocumentTextExtractor: Spreadsheet extraction failed, using fallback');
        return 'spreadsheet file - filename: ' . basename($filePath);
    }

    private function extractFromPresentation($filePath)
    {
        \Log::info('DocumentTextExtractor: Presentation file detected', [
            'file_path' => $filePath
        ]);
        
        // PHPPresentation is not installed; provide descriptive placeholder
        return 'presentation file - filename: ' . basename($filePath);
    }

    /**
     * Cross-platform executable resolver.
     * Tries env override, then Windows 'where', then Linux 'which', then common Windows paths.
     */
    private function getExecutablePath($binary, array $windowsCommonPaths = [], $envOverride = null)
    {
        // 1) Environment override (e.g., TESSERACT_PATH)
        if (!empty($envOverride) && is_string($envOverride) && file_exists($envOverride)) {
            \Log::info('DocumentTextExtractor: Using environment override for ' . $binary, ['path' => $envOverride]);
            return $envOverride;
        }

        // 2) Windows: use 'where'
        if (stripos(PHP_OS_FAMILY, 'Windows') !== false) {
            try {
                $out = @shell_exec('where ' . escapeshellarg($binary) . ' 2>nul');
                if (is_string($out)) {
                    $lines = array_filter(array_map('trim', preg_split('/\r?\n/', $out)));
                    if (!empty($lines)) {
                        $candidate = reset($lines);
                        if (file_exists($candidate)) {
                            \Log::info('DocumentTextExtractor: Found ' . $binary . ' via where command', ['path' => $candidate]);
                            return $candidate;
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('DocumentTextExtractor: where command failed for ' . $binary, ['error' => $e->getMessage()]);
            }
            
            // 3) Check common install paths
            foreach ($windowsCommonPaths as $path) {
                if (file_exists($path)) {
                    \Log::info('DocumentTextExtractor: Found ' . $binary . ' in common path', ['path' => $path]);
                    return $path;
                }
            }
            
            // 4) Check WinGet installed paths for Poppler tools
            $wingetPopplerPaths = [
                $envOverride ?? getenv('LOCALAPPDATA') . '\\Microsoft\\WinGet\\Packages\\oschwartz10612.Poppler_Microsoft.Winget.Source_8wekyb3d8bbwe\\poppler-24.08.0\\Library\\bin\\' . $binary . '.exe',
                getenv('LOCALAPPDATA') . '\\Microsoft\\WinGet\\Packages\\oschwartz10612.Poppler_Microsoft.Winget.Source_8wekyb3d8bbwe\\poppler-24.08.0\\Library\\bin\\' . $binary . '.exe'
            ];
            
            foreach ($wingetPopplerPaths as $path) {
                if (file_exists($path)) {
                    \Log::info('DocumentTextExtractor: Found ' . $binary . ' in WinGet path', ['path' => $path]);
                    return $path;
                }
            }
            
            \Log::warning('DocumentTextExtractor: ' . $binary . ' not found in common Windows paths');
            return null;
        }

        // 5) POSIX: use 'which'
        try {
            $out = @shell_exec('which ' . escapeshellarg($binary) . ' 2>/dev/null');
            $out = is_string($out) ? trim($out) : '';
            if ($out !== '') {
                \Log::info('DocumentTextExtractor: Found ' . $binary . ' via which command', ['path' => $out]);
                return $out;
            }
        } catch (\Throwable $e) {
            \Log::warning('DocumentTextExtractor: which command failed for ' . $binary, ['error' => $e->getMessage()]);
        }
        
        \Log::warning('DocumentTextExtractor: ' . $binary . ' not found via any method');
        return null;
    }

    /**
     * Test method to verify file extension detection
     */
    public function testFileExtensionDetection($filePath)
    {
        $extension = $this->detectActualFileExtension($filePath);
        
        \Log::info('DocumentTextExtractor: File extension detection test', [
            'file_path' => $filePath,
            'detected_extension' => $extension
        ]);
        
        return $extension;
    }

    /**
     * Detect actual file extension from file content and MIME type
     */
    private function detectActualFileExtension($filePath)
    {
        // First try to get MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        
        \Log::info('DocumentTextExtractor: MIME type detection', [
            'file_path' => $filePath,
            'mime_type' => $mimeType
        ]);
        
        // Map MIME types to extensions
        $mimeToExtension = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'text/plain' => 'txt',
            'image/jpeg' => 'jpg',
            'image/png' => 'png'
        ];
        
        if (isset($mimeToExtension[$mimeType])) {
            \Log::info('DocumentTextExtractor: Extension detected from MIME type', [
                'mime_type' => $mimeType,
                'extension' => $mimeToExtension[$mimeType]
            ]);
            return $mimeToExtension[$mimeType];
        }
        
        // Fallback: try to detect from file content
        $fileHandle = fopen($filePath, 'rb');
        if ($fileHandle) {
            $header = fread($fileHandle, 8);
            fclose($fileHandle);
            
            // Check for PDF magic number
            if (substr($header, 0, 4) === '%PDF') {
                \Log::info('DocumentTextExtractor: Extension detected from file content as PDF');
                return 'pdf';
            }
            
            // Check for ZIP-based formats (DOCX, XLSX, PPTX)
            if (substr($header, 0, 2) === 'PK') {
                // This could be DOCX, XLSX, or PPTX - we'll need to check the content
                // For now, assume DOCX as it's most common
                \Log::info('DocumentTextExtractor: Extension detected from file content as DOCX (ZIP-based)');
                return 'docx';
            }
        }
        
        // Last resort: use pathinfo but log the issue
        $fallbackExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        \Log::warning('DocumentTextExtractor: Using fallback extension detection', [
            'file_path' => $filePath,
            'fallback_extension' => $fallbackExtension,
            'mime_type' => $mimeType
        ]);
        
        return $fallbackExtension;
    }

    /**
     * Sanitize text to handle UTF-8 encoding issues
     */
    private function sanitizeText($text)
    {
        if (empty($text)) {
            return $text;
        }
        
        // Remove null bytes and other problematic characters
        $text = str_replace(["\x00", "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07", "\x08", "\x0B", "\x0C", "\x0E", "\x0F"], '', $text);
        
        // Try to fix UTF-8 encoding issues
        if (!mb_check_encoding($text, 'UTF-8')) {
            // Convert to UTF-8 if possible
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
            
            // If still invalid, try to clean it up
            if (!mb_check_encoding($text, 'UTF-8')) {
                // Remove invalid UTF-8 sequences
                $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
                $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);
            }
        }
        
        // Remove any remaining control characters
        $text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);
        
        return $text;
    }
} 