<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;

class IdValidationPipelineService
{
    private $client;
    private $geminiApiKey;
    private $googleVisionApiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->geminiApiKey = env('GEMINI_API_KEY');
        $this->googleVisionApiKey = env('GOOGLE_VISION_API_KEY');
    }

    /**
     * Main validation pipeline with layered checks
     */
    public function validateIdDocument($imagePath, $selectedIdType, $idNumber = null)
    {
        $validationResult = [
            'is_valid' => false,
            'confidence' => 0,
            'score' => 0,
            'status' => 'rejected',
            'validation_details' => [],
            'reasons' => [],
            'detected_text' => '',
            'predicted_id_type' => null,
            'extracted_fields' => [],
            'barcode_data' => null,
            'error_message' => null
        ];

        try {
            // Layer 1: Basic File Checks
            $basicChecks = $this->performBasicFileChecks($imagePath);
            $validationResult['validation_details']['basic_checks'] = $basicChecks;
            
            if (!$basicChecks['is_valid']) {
                $validationResult['reasons'] = array_merge($validationResult['reasons'], $basicChecks['reasons']);
                return $validationResult;
            }

            // Layer 2: OCR Text Extraction
            $ocrResult = $this->performOCR($imagePath);
            $validationResult['detected_text'] = $ocrResult['text'];
            $validationResult['validation_details']['ocr'] = $ocrResult;

            // Layer 3: ID Type Detection vs Selection
            $typeDetection = $this->detectIdTypeFromText($ocrResult['text']);
            $validationResult['predicted_id_type'] = $typeDetection['predicted_type'];
            $validationResult['validation_details']['type_detection'] = $typeDetection;

            // Layer 4: Field Extraction & Validation
            $fieldExtraction = $this->extractAndValidateFields($ocrResult['text'], $selectedIdType, $idNumber);
            $validationResult['extracted_fields'] = $fieldExtraction['fields'];
            $validationResult['validation_details']['field_extraction'] = $fieldExtraction;

            // Layer 5: Barcode/MRZ Parsing (if available)
            $barcodeResult = $this->parseBarcodeOrMRZ($imagePath);
            $validationResult['barcode_data'] = $barcodeResult['data'];
            $validationResult['validation_details']['barcode'] = $barcodeResult;

            // Layer 6: Scoring & Decision
            $scoringResult = $this->calculateScoreAndDecision([
                'type_match' => $typeDetection['matches_selected'],
                'id_number_format' => $fieldExtraction['id_number_valid'],
                'expiry_ok' => $fieldExtraction['expiry_valid'],
                'barcode_ok' => $barcodeResult['success'],
                'ocr_confidence' => $ocrResult['confidence'],
                'basic_checks' => $basicChecks['is_valid']
            ]);

            $validationResult['score'] = $scoringResult['score'];
            $validationResult['confidence'] = $scoringResult['confidence'];
            $validationResult['status'] = $scoringResult['status'];
            $validationResult['is_valid'] = $scoringResult['is_valid'];
            $validationResult['reasons'] = array_merge($validationResult['reasons'], $scoringResult['reasons']);

            return $validationResult;

        } catch (Exception $e) {
            Log::error('ID Validation Pipeline Error: ' . $e->getMessage());
            $validationResult['error_message'] = 'Validation service temporarily unavailable';
            $validationResult['reasons'][] = 'Service error: ' . $e->getMessage();
            return $validationResult;
        }
    }

    /**
     * Layer 1: Basic File Checks
     */
    private function performBasicFileChecks($imagePath)
    {
        $result = [
            'is_valid' => true,
            'reasons' => [],
            'file_type_ok' => false,
            'file_size_ok' => false,
            'aspect_ratio_ok' => false,
            'edges_detected' => false
        ];

        try {
            $imageData = Storage::get($imagePath);
            $imageInfo = getimagesizefromstring($imageData);
            
            if (!$imageInfo) {
                $result['reasons'][] = 'Invalid image format';
                $result['is_valid'] = false;
                return $result;
            }

            // Check file type
            $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
            if (in_array($imageInfo[2], $allowedTypes)) {
                $result['file_type_ok'] = true;
            } else {
                $result['reasons'][] = 'Unsupported file type';
                $result['is_valid'] = false;
            }

            // Check file size (5MB max)
            $fileSize = Storage::size($imagePath);
            if ($fileSize <= 5 * 1024 * 1024) {
                $result['file_size_ok'] = true;
            } else {
                $result['reasons'][] = 'File size exceeds 5MB limit';
                $result['is_valid'] = false;
            }

            // Check aspect ratio (approximately 1.58:1 for credit card size)
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $aspectRatio = $width / $height;
            $expectedRatio = 1.58;
            $tolerance = 0.2;

            if (abs($aspectRatio - $expectedRatio) <= $tolerance) {
                $result['aspect_ratio_ok'] = true;
            } else {
                $result['reasons'][] = 'Document aspect ratio does not match ID card format';
            }

            // Basic edge detection (simplified)
            $result['edges_detected'] = $this->detectEdges($imageData);

        } catch (Exception $e) {
            $result['reasons'][] = 'File processing error: ' . $e->getMessage();
            $result['is_valid'] = false;
        }

        return $result;
    }

    /**
     * Layer 2: OCR Text Extraction
     */
    private function performOCR($imagePath)
    {
        $result = [
            'text' => '',
            'confidence' => 0,
            'method' => 'none',
            'success' => false
        ];

        try {
            // Try Google Vision API first
            if ($this->googleVisionApiKey) {
                $visionResult = $this->performGoogleVisionOCR($imagePath);
                if ($visionResult['success']) {
                    return $visionResult;
                }
            }

            // Fallback to Gemini AI OCR
            $geminiResult = $this->performGeminiOCR($imagePath);
            if ($geminiResult['success']) {
                return $geminiResult;
            }

            // Final fallback to Tesseract (if available)
            $tesseractResult = $this->performTesseractOCR($imagePath);
            if ($tesseractResult['success']) {
                return $tesseractResult;
            }

        } catch (Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Google Vision API OCR
     */
    private function performGoogleVisionOCR($imagePath)
    {
        try {
            $imageData = Storage::get($imagePath);
            $imageBase64 = base64_encode($imageData);

            $response = $this->client->post('https://vision.googleapis.com/v1/images:annotate', [
                'query' => ['key' => $this->googleVisionApiKey],
                'json' => [
                    'requests' => [
                        [
                            'image' => ['content' => $imageBase64],
                            'features' => [
                                ['type' => 'TEXT_DETECTION'],
                                ['type' => 'DOCUMENT_TEXT_DETECTION']
                            ]
                        ]
                    ]
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            $textAnnotations = $result['responses'][0]['textAnnotations'] ?? [];

            if (!empty($textAnnotations)) {
                $fullText = $textAnnotations[0]['description'] ?? '';
                $confidence = $this->calculateOCRConfidence($textAnnotations);

                return [
                    'text' => $fullText,
                    'confidence' => $confidence,
                    'method' => 'google_vision',
                    'success' => true
                ];
            }

        } catch (Exception $e) {
            Log::error('Google Vision OCR Error: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Gemini AI OCR
     */
    private function performGeminiOCR($imagePath)
    {
        try {
            $imageData = Storage::get($imagePath);
            $imageBase64 = base64_encode($imageData);

            $response = $this->client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent', [
                'headers' => ['Content-Type' => 'application/json'],
                'query' => ['key' => $this->geminiApiKey],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'Extract ALL text from this image. Return only the text content, no explanations.'],
                                [
                                    'inline_data' => [
                                        'mime_type' => 'image/jpeg',
                                        'data' => $imageBase64
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'maxOutputTokens' => 2000
                    ]
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (!empty($text)) {
                return [
                    'text' => $text,
                    'confidence' => 0.8, // Gemini typically has good OCR accuracy
                    'method' => 'gemini',
                    'success' => true
                ];
            }

        } catch (Exception $e) {
            Log::error('Gemini OCR Error: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Tesseract OCR (fallback)
     */
    private function performTesseractOCR($imagePath)
    {
        try {
            $tesseractPath = env('TESSERACT_PATH');
            if (!$tesseractPath || !file_exists($tesseractPath)) {
                return ['success' => false];
            }

            $fullPath = Storage::path($imagePath);
            $command = "\"$tesseractPath\" \"$fullPath\" stdout -l eng";
            $output = shell_exec($command);

            if ($output && trim($output)) {
                return [
                    'text' => trim($output),
                    'confidence' => 0.6, // Lower confidence for Tesseract
                    'method' => 'tesseract',
                    'success' => true
                ];
            }

        } catch (Exception $e) {
            Log::error('Tesseract OCR Error: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Layer 3: ID Type Detection from OCR Text
     */
    private function detectIdTypeFromText($text)
    {
        $text = strtoupper($text);
        
        $idTypePatterns = [
            'philnational_id' => [
                'PHILIPPINE NATIONAL ID', 'PHILSYS', 'REPUBLIC OF THE PHILIPPINES',
                'NATIONAL ID', 'PHILIPPINE ID'
            ],
            'passport' => [
                'PHILIPPINE PASSPORT', 'PASSPORT', 'REPUBLIC OF THE PHILIPPINES',
                'PASSPORT NO', 'PASSPORT NUMBER'
            ],
            'drivers_license' => [
                'DRIVER\'S LICENSE', 'DRIVERS LICENSE', 'LAND TRANSPORTATION OFFICE',
                'LTO', 'DRIVING LICENSE'
            ],
            'umid' => [
                'UNIFIED MULTIPURPOSE ID', 'UMID', 'SSS/GSIS/PHILHEALTH',
                'MULTIPURPOSE ID'
            ],
            'postal_id' => [
                'POSTAL ID', 'PHILIPPINE POSTAL CORPORATION', 'POSTAL',
                'POSTAL CORPORATION'
            ],
            'voters_id' => [
                'VOTER\'S ID', 'VOTERS ID', 'COMMISSION ON ELECTIONS',
                'COMELEC', 'VOTER ID'
            ],
            'sss_id' => [
                'SOCIAL SECURITY SYSTEM', 'SSS', 'MEMBER ID',
                'SOCIAL SECURITY'
            ],
            'gsis_id' => [
                'GOVERNMENT SERVICE INSURANCE SYSTEM', 'GSIS', 'MEMBER ID',
                'GOVERNMENT SERVICE'
            ],
            'tin_id' => [
                'TAX IDENTIFICATION NUMBER', 'TIN', 'BUREAU OF INTERNAL REVENUE',
                'BIR', 'TAX ID'
            ],
            'prc_id' => [
                'PROFESSIONAL REGULATION COMMISSION', 'PRC', 'PROFESSIONAL ID',
                'REGULATION COMMISSION'
            ],
            'barangay_id' => [
                'BARANGAY ID', 'BARANGAY', 'LOCAL GOVERNMENT',
                'BARANGAY CERTIFICATE'
            ],
            'senior_citizen_id' => [
                'SENIOR CITIZEN ID', 'OSCA', 'OFFICE OF SENIOR CITIZENS AFFAIRS',
                'SENIOR CITIZEN'
            ],
            'pwd_id' => [
                'PERSONS WITH DISABILITY', 'PWD', 'DISABILITY ID',
                'PERSONS WITH DISABILITIES'
            ],
            'company_id' => [
                'COMPANY ID', 'EMPLOYEE ID', 'STAFF ID', 'CORPORATE ID',
                'EMPLOYEE CARD'
            ],
            'school_id' => [
                'SCHOOL ID', 'STUDENT ID', 'UNIVERSITY ID', 'COLLEGE ID',
                'STUDENT CARD'
            ]
        ];

        $matches = [];
        foreach ($idTypePatterns as $idType => $patterns) {
            $matchCount = 0;
            foreach ($patterns as $pattern) {
                if (strpos($text, $pattern) !== false) {
                    $matchCount++;
                }
            }
            if ($matchCount > 0) {
                $matches[$idType] = $matchCount;
            }
        }

        // Find the ID type with most matches
        $predictedType = null;
        $maxMatches = 0;
        foreach ($matches as $idType => $matchCount) {
            if ($matchCount > $maxMatches) {
                $maxMatches = $matchCount;
                $predictedType = $idType;
            }
        }

        return [
            'predicted_type' => $predictedType,
            'matches' => $matches,
            'confidence' => $maxMatches > 0 ? min(1.0, $maxMatches / 3) : 0,
            'matches_selected' => $predictedType !== null
        ];
    }

    /**
     * Layer 4: Field Extraction & Validation
     */
    private function extractAndValidateFields($text, $selectedIdType, $providedIdNumber)
    {
        $result = [
            'fields' => [],
            'id_number_valid' => false,
            'expiry_valid' => false,
            'name_valid' => false,
            'address_valid' => false
        ];

        // Extract ID Number
        $idNumberPatterns = [
            'philnational_id' => '/\b\d{4}-\d{4}-\d{4}\b/', // PHILSYS format
            'passport' => '/\b[A-Z]\d{7}\b/', // Passport format
            'umid' => '/\b\d{12}\b/', // UMID format
            'drivers_license' => '/\b[A-Z]\d{2}-\d{2}-\d{7}\b/', // DL format
            'sss_id' => '/\b\d{2}-\d{7}-\d{1}\b/', // SSS format
            'gsis_id' => '/\b\d{13}\b/', // GSIS format
            'tin_id' => '/\b\d{3}-\d{3}-\d{3}\b/', // TIN format
        ];

        $pattern = $idNumberPatterns[$selectedIdType] ?? '/\b[A-Z0-9\-]{8,20}\b/';
        preg_match($pattern, $text, $matches);
        
        if (!empty($matches)) {
            $result['fields']['id_number'] = $matches[0];
            $result['id_number_valid'] = true;
            
            // Check if it matches provided ID number
            if ($providedIdNumber && strtoupper($matches[0]) === strtoupper($providedIdNumber)) {
                $result['id_number_match'] = true;
            }
        }

        // Extract Name (look for common name patterns)
        $namePatterns = [
            '/(?:SURNAME|LAST NAME|FAMILY NAME)[:\s]+([A-Z\s,]+)/i',
            '/(?:GIVEN NAME|FIRST NAME)[:\s]+([A-Z\s,]+)/i',
            '/(?:NAME)[:\s]+([A-Z\s,]+)/i'
        ];

        foreach ($namePatterns as $pattern) {
            preg_match($pattern, $text, $matches);
            if (!empty($matches[1])) {
                $result['fields']['name'] = trim($matches[1]);
                $result['name_valid'] = true;
                break;
            }
        }

        // Extract Expiry Date
        $expiryPatterns = [
            '/(?:EXPIRY|EXPIRES|VALID UNTIL)[:\s]+(\d{2}\/\d{2}\/\d{4})/i',
            '/(?:EXPIRY|EXPIRES|VALID UNTIL)[:\s]+(\d{4}-\d{2}-\d{2})/i',
            '/(\d{2}\/\d{2}\/\d{4})/',
            '/(\d{4}-\d{2}-\d{2})/'
        ];

        foreach ($expiryPatterns as $pattern) {
            preg_match($pattern, $text, $matches);
            if (!empty($matches[1])) {
                $expiryDate = $this->parseDate($matches[1]);
                if ($expiryDate && $expiryDate > now()) {
                    $result['fields']['expiry_date'] = $expiryDate->format('Y-m-d');
                    $result['expiry_valid'] = true;
                }
                break;
            }
        }

        // Extract Address
        $addressPatterns = [
            '/(?:ADDRESS)[:\s]+([A-Z0-9\s,.-]+)/i',
            '/(?:RESIDENCE)[:\s]+([A-Z0-9\s,.-]+)/i'
        ];

        foreach ($addressPatterns as $pattern) {
            preg_match($pattern, $text, $matches);
            if (!empty($matches[1])) {
                $result['fields']['address'] = trim($matches[1]);
                $result['address_valid'] = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Layer 5: Barcode/MRZ Parsing
     */
    private function parseBarcodeOrMRZ($imagePath)
    {
        $result = [
            'data' => null,
            'success' => false,
            'type' => null
        ];

        try {
            // This would integrate with @zxing/library or similar
            // For now, we'll implement a basic QR code detection using PHP
            
            $imageData = Storage::get($imagePath);
            
            // Basic QR code detection (simplified)
            // In a real implementation, you'd use a proper QR code library
            $result['success'] = false; // Placeholder
            
        } catch (Exception $e) {
            Log::error('Barcode/MRZ Parsing Error: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Layer 6: Scoring & Decision
     */
    private function calculateScoreAndDecision($signals)
    {
        $score = 0;
        $reasons = [];

        // Type match: +0.4
        if ($signals['type_match']) {
            $score += 0.4;
        } else {
            $reasons[] = 'ID type mismatch detected';
        }

        // ID number format: +0.2
        if ($signals['id_number_format']) {
            $score += 0.2;
        } else {
            $reasons[] = 'Invalid ID number format';
        }

        // Expiry check: +0.1
        if ($signals['expiry_ok']) {
            $score += 0.1;
        } else {
            $reasons[] = 'Expired or invalid expiry date';
        }

        // Barcode/MRZ: +0.2
        if ($signals['barcode_ok']) {
            $score += 0.2;
        }

        // OCR confidence: +0.1
        if ($signals['ocr_confidence'] > 0.7) {
            $score += 0.1;
        } else {
            $reasons[] = 'Low OCR confidence';
        }

        // Determine status
        $status = 'rejected';
        $isValid = false;
        $confidence = $score * 100;

        if ($score >= 0.8) {
            $status = 'accepted';
            $isValid = true;
        } elseif ($score >= 0.5) {
            $status = 'review';
            $isValid = false;
            $reasons[] = 'Requires manual review';
        } else {
            $status = 'rejected';
            $isValid = false;
            $reasons[] = 'Validation failed';
        }

        return [
            'score' => $score,
            'confidence' => $confidence,
            'status' => $status,
            'is_valid' => $isValid,
            'reasons' => $reasons
        ];
    }

    /**
     * Helper Methods
     */
    private function detectEdges($imageData)
    {
        // Simplified edge detection
        // In a real implementation, you'd use proper image processing
        return true; // Placeholder
    }

    private function calculateOCRConfidence($textAnnotations)
    {
        // Calculate confidence based on text annotations
        if (empty($textAnnotations)) return 0;
        
        $totalConfidence = 0;
        $count = 0;
        
        foreach ($textAnnotations as $annotation) {
            if (isset($annotation['score'])) {
                $totalConfidence += $annotation['score'];
                $count++;
            }
        }
        
        return $count > 0 ? $totalConfidence / $count : 0;
    }

    private function parseDate($dateString)
    {
        try {
            // Try different date formats
            $formats = ['d/m/Y', 'Y-m-d', 'm/d/Y', 'd-m-Y'];
            
            foreach ($formats as $format) {
                $date = \Carbon\Carbon::createFromFormat($format, $dateString);
                if ($date) {
                    return $date;
                }
            }
        } catch (Exception $e) {
            // Date parsing failed
        }
        
        return null;
    }
}
