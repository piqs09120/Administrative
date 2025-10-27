<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;

class IdValidationService
{
    private $client;
    private $geminiApiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->geminiApiKey = env('GEMINI_API_KEY');
    }

    /**
     * Validate if uploaded image matches the selected ID type
     */
    public function validateIdDocument($imagePath, $selectedIdType, $idNumber = null)
    {
        try {
            // Read the image file
            $imageData = Storage::get($imagePath);
            $imageBase64 = base64_encode($imageData);
            
            // Get ID type specific validation criteria
            $validationCriteria = $this->getIdValidationCriteria($selectedIdType);
            
            // Use Gemini AI to analyze the image
            $analysisResult = $this->analyzeImageWithAI($imageBase64, $validationCriteria, $idNumber);
            
            return [
                'is_valid' => $analysisResult['is_valid'],
                'confidence' => $analysisResult['confidence'],
                'detected_text' => $analysisResult['detected_text'],
                'validation_details' => $analysisResult['validation_details'],
                'error_message' => $analysisResult['error_message'] ?? null
            ];
            
        } catch (Exception $e) {
            Log::error('ID Validation Error: ' . $e->getMessage());
            return [
                'is_valid' => false,
                'confidence' => 0,
                'detected_text' => '',
                'validation_details' => [],
                'error_message' => 'Validation service temporarily unavailable'
            ];
        }
    }

    /**
     * Get validation criteria for specific Philippine ID types
     */
    private function getIdValidationCriteria($idType)
    {
        $criteria = [
            'philnational_id' => [
                'name' => 'Philippine National ID (PhilSys)',
                'required_text' => ['PHILIPPINE NATIONAL ID', 'PHILSYS', 'REPUBLIC OF THE PHILIPPINES'],
                'format_indicators' => ['QR code', 'barcode', 'national id number'],
                'layout_features' => ['blue background', 'yellow accents', 'republic seal'],
                'size_format' => 'credit card size',
                'security_features' => ['hologram', 'microprinting', 'watermark']
            ],
            'passport' => [
                'name' => 'Philippine Passport',
                'required_text' => ['PHILIPPINE PASSPORT', 'REPUBLIC OF THE PHILIPPINES', 'PASSPORT'],
                'format_indicators' => ['passport number', 'machine readable zone', 'biometric chip'],
                'layout_features' => ['dark blue cover', 'golden eagle', 'passport photo'],
                'size_format' => 'passport size',
                'security_features' => ['biometric chip', 'security thread', 'hologram']
            ],
            'drivers_license' => [
                'name' => 'Driver\'s License',
                'required_text' => ['DRIVER\'S LICENSE', 'LAND TRANSPORTATION OFFICE', 'LTO'],
                'format_indicators' => ['license number', 'expiration date', 'restrictions'],
                'layout_features' => ['driver photo', 'signature', 'license categories'],
                'size_format' => 'credit card size',
                'security_features' => ['hologram', 'microprinting']
            ],
            'umid' => [
                'name' => 'Unified Multipurpose ID (UMID)',
                'required_text' => ['UNIFIED MULTIPURPOSE ID', 'UMID', 'SSS/GSIS/PHILHEALTH'],
                'format_indicators' => ['umid number', 'sss number', 'gsis number'],
                'layout_features' => ['photo', 'signature', 'member information'],
                'size_format' => 'credit card size',
                'security_features' => ['chip', 'hologram']
            ],
            'postal_id' => [
                'name' => 'Postal ID',
                'required_text' => ['POSTAL ID', 'PHILIPPINE POSTAL CORPORATION', 'POSTAL'],
                'format_indicators' => ['postal id number', 'postal code', 'address'],
                'layout_features' => ['photo', 'address', 'postal logo'],
                'size_format' => 'credit card size',
                'security_features' => ['hologram']
            ],
            'voters_id' => [
                'name' => 'Voter\'s ID',
                'required_text' => ['VOTER\'S ID', 'COMMISSION ON ELECTIONS', 'COMELEC'],
                'format_indicators' => ['voter\'s number', 'precinct number', 'barangay'],
                'layout_features' => ['photo', 'signature', 'voting precinct'],
                'size_format' => 'credit card size',
                'security_features' => ['hologram']
            ],
            'sss_id' => [
                'name' => 'SSS ID',
                'required_text' => ['SOCIAL SECURITY SYSTEM', 'SSS', 'MEMBER ID'],
                'format_indicators' => ['sss number', 'member name', 'employer'],
                'layout_features' => ['photo', 'sss logo', 'member information'],
                'size_format' => 'credit card size',
                'security_features' => ['hologram']
            ],
            'gsis_id' => [
                'name' => 'GSIS ID',
                'required_text' => ['GOVERNMENT SERVICE INSURANCE SYSTEM', 'GSIS', 'MEMBER ID'],
                'format_indicators' => ['gsis number', 'member name', 'agency'],
                'layout_features' => ['photo', 'gsis logo', 'member information'],
                'size_format' => 'credit card size',
                'security_features' => ['hologram']
            ],
            'tin_id' => [
                'name' => 'TIN ID',
                'required_text' => ['TAX IDENTIFICATION NUMBER', 'TIN', 'BUREAU OF INTERNAL REVENUE'],
                'format_indicators' => ['tin number', 'taxpayer name', 'bir'],
                'layout_features' => ['photo', 'bir logo', 'taxpayer information'],
                'size_format' => 'credit card size',
                'security_features' => ['hologram']
            ],
            'prc_id' => [
                'name' => 'Professional Regulation Commission (PRC)',
                'required_text' => ['PROFESSIONAL REGULATION COMMISSION', 'PRC', 'PROFESSIONAL ID'],
                'format_indicators' => ['prc number', 'profession', 'license number'],
                'layout_features' => ['photo', 'prc logo', 'professional information'],
                'size_format' => 'credit card size',
                'security_features' => ['hologram']
            ],
            'barangay_id' => [
                'name' => 'Barangay ID',
                'required_text' => ['BARANGAY ID', 'BARANGAY', 'LOCAL GOVERNMENT'],
                'format_indicators' => ['barangay name', 'resident number', 'address'],
                'layout_features' => ['photo', 'barangay seal', 'resident information'],
                'size_format' => 'credit card size',
                'security_features' => ['barangay seal']
            ],
            'senior_citizen_id' => [
                'name' => 'Senior Citizen ID',
                'required_text' => ['SENIOR CITIZEN ID', 'OSCA', 'OFFICE OF SENIOR CITIZENS AFFAIRS'],
                'format_indicators' => ['senior citizen number', 'age', 'benefits'],
                'layout_features' => ['photo', 'osca logo', 'senior information'],
                'size_format' => 'credit card size',
                'security_features' => ['osca seal']
            ],
            'pwd_id' => [
                'name' => 'PWD ID',
                'required_text' => ['PERSONS WITH DISABILITY', 'PWD', 'DISABILITY ID'],
                'format_indicators' => ['pwd number', 'disability type', 'benefits'],
                'layout_features' => ['photo', 'pwd logo', 'disability information'],
                'size_format' => 'credit card size',
                'security_features' => ['pwd seal']
            ],
            'company_id' => [
                'name' => 'Company ID',
                'required_text' => ['COMPANY ID', 'EMPLOYEE ID', 'STAFF ID'],
                'format_indicators' => ['employee number', 'department', 'position'],
                'layout_features' => ['photo', 'company logo', 'employee information'],
                'size_format' => 'credit card size',
                'security_features' => ['company logo']
            ],
            'school_id' => [
                'name' => 'School ID',
                'required_text' => ['SCHOOL ID', 'STUDENT ID', 'UNIVERSITY ID'],
                'format_indicators' => ['student number', 'course', 'year level'],
                'layout_features' => ['photo', 'school logo', 'student information'],
                'size_format' => 'credit card size',
                'security_features' => ['school seal']
            ],
            'other_id' => [
                'name' => 'Other Valid ID',
                'required_text' => ['ID', 'IDENTIFICATION', 'CARD'],
                'format_indicators' => ['id number', 'name', 'photo'],
                'layout_features' => ['photo', 'issuing authority', 'id information'],
                'size_format' => 'credit card size',
                'security_features' => ['issuing authority seal']
            ]
        ];

        return $criteria[$idType] ?? $criteria['other_id'];
    }

    /**
     * Analyze image using Gemini AI
     */
    private function analyzeImageWithAI($imageBase64, $criteria, $idNumber = null)
    {
        try {
            $prompt = $this->buildValidationPrompt($criteria, $idNumber);
            
            $response = $this->client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'key' => $this->geminiApiKey
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ],
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
                        'maxOutputTokens' => 1000
                    ]
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            $analysisText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            return $this->parseAnalysisResult($analysisText, $criteria);
            
        } catch (Exception $e) {
            Log::error('Gemini AI Analysis Error: ' . $e->getMessage());
            return [
                'is_valid' => false,
                'confidence' => 0,
                'detected_text' => '',
                'validation_details' => [],
                'error_message' => 'AI analysis failed'
            ];
        }
    }

    /**
     * Build validation prompt for Gemini AI
     */
    private function buildValidationPrompt($criteria, $idNumber = null)
    {
        $prompt = "You are an expert in Philippine ID document validation. Analyze this image and determine if it is a valid {$criteria['name']}.\n\n";
        
        $prompt .= "REQUIRED VALIDATION CRITERIA:\n";
        $prompt .= "1. Required Text Elements: " . implode(', ', $criteria['required_text']) . "\n";
        $prompt .= "2. Format Indicators: " . implode(', ', $criteria['format_indicators']) . "\n";
        $prompt .= "3. Layout Features: " . implode(', ', $criteria['layout_features']) . "\n";
        $prompt .= "4. Size Format: {$criteria['size_format']}\n";
        $prompt .= "5. Security Features: " . implode(', ', $criteria['security_features']) . "\n\n";
        
        if ($idNumber) {
            $prompt .= "6. ID Number Match: Look for ID number '{$idNumber}' in the document\n\n";
        }
        
        $prompt .= "ANALYSIS INSTRUCTIONS:\n";
        $prompt .= "1. Extract ALL visible text from the image\n";
        $prompt .= "2. Check if the image contains the required text elements\n";
        $prompt .= "3. Verify the document layout matches the expected format\n";
        $prompt .= "4. Assess if this appears to be a legitimate government-issued ID\n";
        $prompt .= "5. Check for signs of tampering or forgery\n";
        $prompt .= "6. Determine confidence level (0-100%)\n\n";
        
        $prompt .= "RESPONSE FORMAT (JSON):\n";
        $prompt .= "{\n";
        $prompt .= "  \"is_valid\": true/false,\n";
        $prompt .= "  \"confidence\": 0-100,\n";
        $prompt .= "  \"detected_text\": \"all text found in image\",\n";
        $prompt .= "  \"validation_details\": {\n";
        $prompt .= "    \"required_text_found\": [\"list of found required text\"],\n";
        $prompt .= "    \"missing_elements\": [\"list of missing required elements\"],\n";
        $prompt .= "    \"layout_match\": true/false,\n";
        $prompt .= "    \"security_features_detected\": [\"list of detected security features\"],\n";
        $prompt .= "    \"id_number_match\": true/false,\n";
        $prompt .= "    \"tampering_signs\": [\"list of any tampering indicators\"]\n";
        $prompt .= "  },\n";
        $prompt .= "  \"reasoning\": \"detailed explanation of validation decision\"\n";
        $prompt .= "}\n\n";
        
        $prompt .= "IMPORTANT: Only return valid JSON. Do not include any other text or formatting.";
        
        return $prompt;
    }

    /**
     * Parse AI analysis result
     */
    private function parseAnalysisResult($analysisText, $criteria)
    {
        try {
            // Extract JSON from the response
            $jsonStart = strpos($analysisText, '{');
            $jsonEnd = strrpos($analysisText, '}');
            
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonString = substr($analysisText, $jsonStart, $jsonEnd - $jsonStart + 1);
                $result = json_decode($jsonString, true);
                
                if ($result) {
                    return [
                        'is_valid' => $result['is_valid'] ?? false,
                        'confidence' => $result['confidence'] ?? 0,
                        'detected_text' => $result['detected_text'] ?? '',
                        'validation_details' => $result['validation_details'] ?? [],
                        'reasoning' => $result['reasoning'] ?? 'Analysis completed'
                    ];
                }
            }
            
            // Fallback parsing if JSON extraction fails
            return $this->fallbackParsing($analysisText, $criteria);
            
        } catch (Exception $e) {
            Log::error('Analysis Result Parsing Error: ' . $e->getMessage());
            return [
                'is_valid' => false,
                'confidence' => 0,
                'detected_text' => '',
                'validation_details' => [],
                'error_message' => 'Failed to parse analysis result'
            ];
        }
    }

    /**
     * Fallback parsing method
     */
    private function fallbackParsing($analysisText, $criteria)
    {
        $isValid = false;
        $confidence = 0;
        
        // Simple keyword matching as fallback
        $requiredText = strtolower(implode(' ', $criteria['required_text']));
        $analysisLower = strtolower($analysisText);
        
        $matches = 0;
        foreach ($criteria['required_text'] as $text) {
            if (strpos($analysisLower, strtolower($text)) !== false) {
                $matches++;
            }
        }
        
        if ($matches >= 2) {
            $isValid = true;
            $confidence = min(80, $matches * 20);
        }
        
        return [
            'is_valid' => $isValid,
            'confidence' => $confidence,
            'detected_text' => $analysisText,
            'validation_details' => [
                'required_text_found' => $matches,
                'missing_elements' => [],
                'layout_match' => false,
                'security_features_detected' => [],
                'id_number_match' => false,
                'tampering_signs' => []
            ],
            'reasoning' => 'Fallback analysis based on keyword matching'
        ];
    }
}


