<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GeminiService
{
    private $client;
    private $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GEMINI_API_KEY');
        // Do not throw here; allow graceful fallback inside analyzeDocument
        if (empty($this->apiKey)) {
            \Log::warning('GEMINI_API_KEY is not set; GeminiService will use fallback analysis.');
        }
    }

    public function analyzeDocument($text)
    {
        // Validate input text before processing
        if (empty(trim($text))) {
            \Log::error('GeminiService: Empty or invalid text provided for analysis', [
                'text' => $text,
                'text_length' => strlen($text)
            ]);
            return [
                'error' => true,
                'message' => 'No text content provided for analysis',
                'category' => 'general',
                'fallback' => true
            ];
        }

        // Check for common OCR errors and fallback messages
        $lowercaseText = strtolower($text);
        $isFallbackText = str_contains($lowercaseText, 'unknown document type') || 
            str_contains($lowercaseText, 'document not found') ||
            str_contains($lowercaseText, 'tmp') ||
            str_contains($lowercaseText, 'file not found') ||
            str_contains($lowercaseText, 'pdf text extraction failed') ||
            str_contains($lowercaseText, 'likely scanned') ||
            str_contains($lowercaseText, 'image file') ||
            str_contains($lowercaseText, 'pdf file') ||
            str_contains($lowercaseText, 'manual review recommended');
            
        if ($isFallbackText) {
            \Log::warning('GeminiService: Fallback text detected, using enhanced filename analysis', [
                'text' => $text,
                'text_length' => strlen($text),
                'contains_fallback_indicators' => true
            ]);
            return $this->enhancedFallbackAnalysis($text);
        }

        try {
            // If API key is missing, fall back to keyword-based analysis
            if (empty($this->apiKey)) {
                \Log::warning('GEMINI_API_KEY is not set, using enhanced fallback analysis');
                return $this->enhancedFallbackAnalysis($text);
            }
            
            \Log::info('Starting Gemini AI analysis', [
                'api_key_set' => !empty($this->apiKey),
                'text_length' => strlen($text),
                'text_preview' => substr($text, 0, 100),
                'text_validation_passed' => true,
                'is_fallback_text' => false
            ]);
            
            $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=' . $this->apiKey;
            
            $response = $this->client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => "You are a legal document classifier and analyzer. Analyze the FULL text content and provide accurate classification and analysis.

IMPORTANT: This is actual document content, not a filename or fallback text. Analyze the real content thoroughly.

Primary task: Categorize the document into ONE of these categories ONLY:
[Policy, Contract, Legal Notice, Compliance, Financial, Report, Memorandum, Affidavit, Subpoena, Cease & Desist, Legal Brief, General]

Classification rules (in order of priority):
1. POLICY: Contains 'Privacy Policy', 'Data Protection', 'Terms of Service', 'Terms and Conditions', 'Acceptable Use Policy', 'Data Privacy Policy'
2. CONTRACT: Contains 'contract', 'agreement', 'parties', 'obligations', 'signatures', 'binding terms', 'lease', 'employment agreement'
3. MEMORANDUM: Contains 'memorandum', 'memo', 'MOA', 'internal communication', 'staff notice'
4. LEGAL NOTICE: Contains 'legal notice', 'cease and desist', 'demand letter', 'court notice'
5. COMPLIANCE: Contains 'compliance', 'regulation', 'regulatory', 'audit', 'standards'
6. FINANCIAL: Contains 'invoice', 'receipt', 'financial statement', 'budget', 'expense report'
7. REPORT: Contains 'report', 'analysis', 'assessment', 'evaluation', 'findings'
8. AFFIDAVIT: Contains 'affidavit', 'sworn statement', 'declaration', 'under oath'
9. SUBPOENA: Contains 'subpoena', 'court order', 'summons'
10. LEGAL BRIEF: Contains 'legal brief', 'case brief', 'legal argument'
11. GENERAL: Only if absolutely none of the above match

Return a structured response in this exact format:

CATEGORY: <one of the allowed categories above>
CONFIDENCE: <0.0-1.0>

SUMMARY: <2-4 sentences based on the actual document content>
KEY_INFO: <concise bullet-like info extracted from content>
LEGAL_IMPLICATIONS: <short text or 'None' based on content>
COMPLIANCE_STATUS: <compliant | non-compliant | review_required>
TAGS: <5-7 relevant tags, comma-separated>

Document text to analyze: " . $text . "

IMPORTANT: Also determine if the document requires legal review based on its content (answer YES/NO).
IMPORTANT: Also determine if the document requires visitor coordination (answer YES/NO), especially if it mentions visitors, attendees, or guest lists.
IMPORTANT: Provide a LEGAL_RISK_SCORE: [Low, Medium, High] based on potential legal issues or implications.

LEGAL_REVIEW_REQUIRED: [YES/NO]
VISITOR_COORDINATION_REQUIRED: [YES/NO]
LEGAL_RISK_SCORE: [Low/Medium/High]"
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            \Log::info('Gemini API response received', [
                'response_keys' => array_keys($result),
                'has_candidates' => isset($result['candidates']),
                'candidates_count' => isset($result['candidates']) ? count($result['candidates']) : 0
            ]);
            
            // Parse the response to extract structured data
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $analysisText = $result['candidates'][0]['content']['parts'][0]['text'];
                \Log::info('Gemini analysis text extracted', [
                    'text_length' => strlen($analysisText),
                    'text_preview' => substr($analysisText, 0, 200)
                ]);
                return $this->parseAnalysisResponse($analysisText);
            }
            
            \Log::warning('Invalid Gemini API response format', [
                'result' => $result
            ]);
            
            return [
                'error' => true,
                'message' => 'Invalid response format from Gemini API'
            ];
            
        } catch (RequestException $e) {
            // On ANY API/network error, gracefully fallback to local analysis
            \Log::error('Gemini API request failed', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response',
                'falling_back_to_local' => true
            ]);
            return $this->enhancedFallbackAnalysis($text);
        } catch (\Throwable $e) {
            // Any other unexpected error, still fallback to ensure classification
            \Log::error('Unexpected error in Gemini analysis', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'falling_back_to_local' => true
            ]);
            return $this->enhancedFallbackAnalysis($text);
        }
    }

    public function enhancedFallbackAnalysis($text)
    {
        // Enhanced keyword-based analysis as fallback
        $text = strtolower($text);
        
        \Log::info('GeminiService: Using enhanced fallback analysis', [
            'text' => $text,
            'text_length' => strlen($text)
        ]);
        
        // Check if this is a fallback message indicating extraction failure
        if (str_contains($text, 'unknown document type') || 
            str_contains($text, 'document not found') ||
            str_contains($text, 'tmp') ||
            str_contains($text, 'file not found') ||
            str_contains($text, 'likely scanned') ||
            str_contains($text, 'image file') ||
            str_contains($text, 'pdf file') ||
            str_contains($text, 'pdf text extraction failed') ||
            str_contains($text, 'manual review recommended')) {
            
            \Log::warning('GeminiService: Enhanced fallback analysis detected extraction failure', [
                'text' => $text,
                'using_filename_analysis' => true
            ]);
            
            // Try to extract meaningful information from the fallback text
            $category = $this->extractCategoryFromFallbackText($text);
            $requiresLegalReview = $this->determineLegalReviewFromFallback($text);
            $legalRiskScore = $this->determineRiskFromFallback($text);
            
            return [
                'error' => false,
                'category' => $category,
                'summary' => 'Document analysis completed using enhanced fallback methods. Text extraction was limited, but document type was determined from available information.',
                'key_info' => 'Document processed using enhanced fallback analysis due to text extraction limitations.',
                'legal_implications' => 'Limited analysis available - document may require manual review.',
                'compliance_status' => 'review_required',
                'tags' => ['enhanced_fallback_analysis', 'limited_text', 'manual_review_recommended'],
                'fallback' => true,
                'requires_legal_review' => $requiresLegalReview,
                'requires_visitor_coordination' => false,
                'legal_risk_score' => $legalRiskScore,
                'extraction_quality' => 'low'
            ];
        }
        
        // Initialize variables
        $category = 'general';
        $requiresLegalReview = false;
        $requiresVisitorCoordination = false;
        $legalRiskScore = 'Low';
        
        // Enhanced document type detection with better keyword mapping
        $documentTypeMap = [
            // Policy documents
            'privacy policy' => ['category' => 'policy', 'risk' => 'Medium', 'review' => true],
            'data protection' => ['category' => 'policy', 'risk' => 'Medium', 'review' => true],
            'terms of service' => ['category' => 'policy', 'risk' => 'Medium', 'review' => true],
            'terms and conditions' => ['category' => 'policy', 'risk' => 'Medium', 'review' => true],
            'acceptable use' => ['category' => 'policy', 'risk' => 'Medium', 'review' => true],
            'data privacy' => ['category' => 'policy', 'risk' => 'Medium', 'review' => true],
            
            // Contract documents
            'contract' => ['category' => 'contract', 'risk' => 'Medium', 'review' => true],
            'agreement' => ['category' => 'contract', 'risk' => 'Medium', 'review' => true],
            'lease' => ['category' => 'contract', 'risk' => 'Medium', 'review' => true],
            'employment' => ['category' => 'contract', 'risk' => 'Medium', 'review' => true],
            'parties' => ['category' => 'contract', 'risk' => 'Medium', 'review' => true],
            'obligations' => ['category' => 'contract', 'risk' => 'Medium', 'review' => true],
            'signatures' => ['category' => 'contract', 'risk' => 'Medium', 'review' => true],
            'binding' => ['category' => 'contract', 'risk' => 'Medium', 'review' => true],
            
            // Memorandum documents
            'memorandum' => ['category' => 'memorandum', 'risk' => 'Low', 'review' => false],
            'memo' => ['category' => 'memorandum', 'risk' => 'Low', 'review' => false],
            'moa' => ['category' => 'memorandum', 'risk' => 'Low', 'review' => false],
            'internal communication' => ['category' => 'memorandum', 'risk' => 'Low', 'review' => false],
            'staff notice' => ['category' => 'memorandum', 'risk' => 'Low', 'review' => false],
            
            // Legal notice documents
            'legal notice' => ['category' => 'legal_notice', 'risk' => 'High', 'review' => true],
            'cease and desist' => ['category' => 'legal_notice', 'risk' => 'High', 'review' => true],
            'demand letter' => ['category' => 'legal_notice', 'risk' => 'High', 'review' => true],
            'court notice' => ['category' => 'legal_notice', 'risk' => 'High', 'review' => true],
            
            // Compliance documents
            'compliance' => ['category' => 'compliance', 'risk' => 'Medium', 'review' => true],
            'regulation' => ['category' => 'compliance', 'risk' => 'Medium', 'review' => true],
            'regulatory' => ['category' => 'compliance', 'risk' => 'Medium', 'review' => true],
            'audit' => ['category' => 'compliance', 'risk' => 'Medium', 'review' => true],
            'standards' => ['category' => 'compliance', 'risk' => 'Medium', 'review' => true],
            
            // Financial documents
            'invoice' => ['category' => 'financial', 'risk' => 'Low', 'review' => false],
            'receipt' => ['category' => 'financial', 'risk' => 'Low', 'review' => false],
            'financial statement' => ['category' => 'financial', 'risk' => 'Low', 'review' => false],
            'budget' => ['category' => 'financial', 'risk' => 'Low', 'review' => false],
            'expense report' => ['category' => 'financial', 'risk' => 'Low', 'review' => false],
            
            // Report documents
            'report' => ['category' => 'report', 'risk' => 'Low', 'review' => false],
            'analysis' => ['category' => 'report', 'risk' => 'Low', 'review' => false],
            'assessment' => ['category' => 'report', 'risk' => 'Low', 'review' => false],
            'evaluation' => ['category' => 'report', 'risk' => 'Low', 'review' => false],
            'findings' => ['category' => 'report', 'risk' => 'Low', 'review' => false],
            
            // Legal documents
            'affidavit' => ['category' => 'affidavit', 'risk' => 'Medium', 'review' => true],
            'sworn statement' => ['category' => 'affidavit', 'risk' => 'Medium', 'review' => true],
            'declaration' => ['category' => 'affidavit', 'risk' => 'Medium', 'review' => true],
            'under oath' => ['category' => 'affidavit', 'risk' => 'Medium', 'review' => true],
            
            'subpoena' => ['category' => 'subpoena', 'risk' => 'High', 'review' => true],
            'court order' => ['category' => 'subpoena', 'risk' => 'High', 'review' => true],
            'summons' => ['category' => 'subpoena', 'risk' => 'High', 'review' => true],
            
            'legal brief' => ['category' => 'legal_brief', 'risk' => 'Medium', 'review' => true],
            'case brief' => ['category' => 'legal_brief', 'risk' => 'Medium', 'review' => true],
            'legal argument' => ['category' => 'legal_brief', 'risk' => 'Medium', 'review' => true]
        ];
        
        // Check for document type indicators
        foreach ($documentTypeMap as $indicator => $config) {
            if (strpos($text, $indicator) !== false) {
                $category = $config['category'];
                $legalRiskScore = $config['risk'];
                $requiresLegalReview = $config['review'];
                break;
            }
        }
        
        // Determine if visitor coordination is required
        if (strpos($text, 'visitor') !== false || strpos($text, 'attendee') !== false || 
            strpos($text, 'guest list') !== false || strpos($text, 'guests') !== false ||
            strpos($text, 'meeting') !== false || strpos($text, 'conference') !== false) {
            $requiresVisitorCoordination = true;
        }

        // Generate meaningful summary based on detected category
        $summary = $this->generateSummaryFromCategory($category, $text);
        
        // Generate tags based on content and category
        $tags = $this->generateTagsFromContent($text, $category);
        
        return [
            'error' => false,
            'category' => $category,
            'summary' => $summary,
            'key_info' => 'Document classified using enhanced fallback analysis based on content keywords.',
            'legal_implications' => $this->getLegalImplications($category),
            'compliance_status' => 'review_required',
            'tags' => $tags,
            'fallback' => true,
            'requires_legal_review' => $requiresLegalReview,
            'requires_visitor_coordination' => $requiresVisitorCoordination,
            'legal_risk_score' => $legalRiskScore,
            'extraction_quality' => 'medium'
        ];
    }

    /**
     * Generate summary based on detected category
     */
    private function generateSummaryFromCategory($category, $text)
    {
        $textPreview = substr($text, 0, 150);
        
        switch ($category) {
            case 'policy':
                return "This appears to be a policy document, likely containing rules, guidelines, or standards. Content preview: $textPreview...";
            case 'contract':
                return "This appears to be a contract or agreement document, likely containing terms, conditions, and obligations. Content preview: $textPreview...";
            case 'memorandum':
                return "This appears to be a memorandum or internal communication document. Content preview: $textPreview...";
            case 'legal_notice':
                return "This appears to be a legal notice or formal legal communication. Content preview: $textPreview...";
            case 'compliance':
                return "This appears to be a compliance or regulatory document. Content preview: $textPreview...";
            case 'financial':
                return "This appears to be a financial document, likely containing monetary or budgetary information. Content preview: $textPreview...";
            case 'report':
                return "This appears to be a report or analysis document. Content preview: $textPreview...";
            case 'affidavit':
                return "This appears to be an affidavit or sworn statement document. Content preview: $textPreview...";
            case 'subpoena':
                return "This appears to be a subpoena or court order document. Content preview: $textPreview...";
            case 'legal_brief':
                return "This appears to be a legal brief or case analysis document. Content preview: $textPreview...";
            default:
                return "This appears to be a general document. Content preview: $textPreview...";
        }
    }

    /**
     * Generate tags based on content and category
     */
    private function generateTagsFromContent($text, $category)
    {
        $tags = [$category, 'fallback_analysis'];
        
        // Add content-based tags
        if (strpos($text, 'legal') !== false) $tags[] = 'legal';
        if (strpos($text, 'business') !== false) $tags[] = 'business';
        if (strpos($text, 'compliance') !== false) $tags[] = 'compliance';
        if (strpos($text, 'financial') !== false) $tags[] = 'financial';
        if (strpos($text, 'policy') !== false) $tags[] = 'policy';
        if (strpos($text, 'contract') !== false) $tags[] = 'contract';
        if (strpos($text, 'memo') !== false) $tags[] = 'memo';
        if (strpos($text, 'report') !== false) $tags[] = 'report';
        
        // Ensure we have 5-7 tags
        $tags = array_unique($tags);
        if (count($tags) < 5) {
            $tags[] = 'document';
            $tags[] = 'analysis';
        }
        
        return array_slice($tags, 0, 7);
    }

    /**
     * Get legal implications based on category
     */
    private function getLegalImplications($category)
    {
        switch ($category) {
            case 'policy':
                return 'May have legal implications for organizational compliance and employee conduct';
            case 'contract':
                return 'Legally binding document with potential legal consequences';
            case 'legal_notice':
                return 'Formal legal communication with potential legal implications';
            case 'compliance':
                return 'May have regulatory and legal compliance implications';
            case 'affidavit':
                return 'Sworn statement with legal implications';
            case 'subpoena':
                return 'Court order with significant legal implications';
            case 'legal_brief':
                return 'Legal analysis with potential legal implications';
            default:
                return 'Limited legal implications identified';
        }
    }

    public function fallbackAnalysis($text)
    {
        // Enhanced keyword-based analysis as fallback
        $text = strtolower($text);
        
        // Check if this is a fallback message indicating extraction failure
        if (str_contains($text, 'unknown document type') || 
            str_contains($text, 'document not found') ||
            str_contains($text, 'tmp') ||
            str_contains($text, 'file not found') ||
            str_contains($text, 'likely scanned') ||
            str_contains($text, 'image file') ||
            str_contains($text, 'pdf file')) {
            
            \Log::warning('GeminiService: Fallback analysis detected extraction failure', [
                'text' => $text,
                'using_filename_analysis' => true
            ]);
            
            // Try to extract meaningful information from the fallback text
            $category = $this->extractCategoryFromFallbackText($text);
            $requiresLegalReview = $this->determineLegalReviewFromFallback($text);
            $legalRiskScore = $this->determineRiskFromFallback($text);
            
            return [
                'error' => false,
                'category' => $category,
                'summary' => 'Document analysis completed using fallback methods. Text extraction was limited, but document type was determined from available information.',
                'key_info' => 'Document processed using fallback analysis due to text extraction limitations.',
                'legal_implications' => 'Limited analysis available - document may require manual review.',
                'compliance_status' => 'review_required',
                'tags' => ['fallback_analysis', 'limited_text', 'manual_review_recommended'],
                'fallback' => true,
                'requires_legal_review' => $requiresLegalReview,
                'requires_visitor_coordination' => false,
                'legal_risk_score' => $legalRiskScore,
                'extraction_quality' => 'low'
            ];
        }
        
        // Initialize variables
        $category = 'general';
        $requiresLegalReview = false;
        $requiresVisitorCoordination = false;
        $legalRiskScore = 'Low';
        
        // Check if text contains filename-based indicators (HIGHEST PRIORITY)
        $filenameIndicators = [
            'memo' => 'memorandum',
            'memorandum' => 'memorandum',
            'contract' => 'contract',
            'policy' => 'policy',
            'report' => 'report',
            'affidavit' => 'affidavit',
            'subpoena' => 'subpoena'
        ];
        
        foreach ($filenameIndicators as $indicator => $categoryType) {
            if (strpos($text, $indicator) !== false) {
                // If we find a filename indicator, prioritize it
                $category = $categoryType;
                break;
            }
        }
        
        // If no filename indicator found, proceed with content analysis
        if ($category === 'general') {
            // Check for specific legal document types first - PRIORITY ORDER
            // MEMORANDUM DETECTION (HIGHEST PRIORITY - most common business document type)
            if (strpos($text, 'memorandum') !== false || strpos($text, 'memoranda') !== false || strpos($text, 'memo') !== false || strpos($text, 'moa') !== false) {
                $category = 'memorandum';
                $requiresLegalReview = false; // Memorandums are usually low risk
                $legalRiskScore = 'Low';
            } elseif (strpos($text, 'affidavit') !== false || strpos($text, 'affidavits') !== false || strpos($text, 'sworn') !== false || strpos($text, 'under oath') !== false || strpos($text, 'declare') !== false || strpos($text, 'declaration') !== false) {
                $category = 'affidavit';
                $requiresLegalReview = true;
                $legalRiskScore = 'Medium';
            } elseif (strpos($text, 'subpoena') !== false || strpos($text, 'court order') !== false) {
                $category = 'subpoena';
                $requiresLegalReview = true;
                $legalRiskScore = 'High';
            } elseif (strpos($text, 'cease') !== false && strpos($text, 'desist') !== false) {
                $category = 'cease_desist';
                $requiresLegalReview = true;
                $legalRiskScore = 'High';
            } elseif (strpos($text, 'brief') !== false || strpos($text, 'legal brief') !== false || strpos($text, 'case brief') !== false) {
                $category = 'legal_brief';
                $requiresLegalReview = true;
                $legalRiskScore = 'Medium';
            } elseif (strpos($text, 'financial') !== false || strpos($text, 'budget') !== false || strpos($text, 'money') !== false || strpos($text, 'financial statement') !== false) {
                $category = 'financial';
            } elseif (strpos($text, 'compliance') !== false || strpos($text, 'regulation') !== false || strpos($text, 'regulatory') !== false) {
                $category = 'compliance';
                $requiresLegalReview = true;
                $legalRiskScore = 'Medium';
            } elseif (strpos($text, 'report') !== false || strpos($text, 'analysis') !== false || strpos($text, 'assessment') !== false) {
                $category = 'report';
            } elseif (strpos($text, 'agreement') !== false) {
                // If it contains 'agreement' but not explicitly a formal contract, it's likely a memorandum
                if (strpos($text, 'parties') !== false && strpos($text, 'hereby') !== false && strpos($text, 'terms and conditions') !== false && strpos($text, 'binding') !== false) {
                    $category = 'contract';
                    $requiresLegalReview = true;
                    $legalRiskScore = 'Medium';
                } else {
                    $category = 'memorandum';
                }
            } elseif (strpos($text, 'contract') !== false || strpos($text, 'lease') !== false || strpos($text, 'employment') !== false) {
                $category = 'contract';
                $requiresLegalReview = true;
                $legalRiskScore = 'Medium';
            } elseif (strpos($text, 'legal notice') !== false || strpos($text, 'legal notices') !== false) {
                $category = 'legal_notice';
                $requiresLegalReview = true;
                $legalRiskScore = 'Medium';
            } elseif (strpos($text, 'policy') !== false || strpos($text, 'procedure') !== false || strpos($text, 'guidelines') !== false) {
                $category = 'policy';
            }
        }
        
        // Determine if visitor coordination is required
        if (strpos($text, 'visitor') !== false || strpos($text, 'attendee') !== false || strpos($text, 'guest list') !== false || strpos($text, 'guests') !== false) {
            $requiresVisitorCoordination = true;
        }

        // Extract first 200 characters as summary
        $summary = substr($text, 0, 200) . '...';
        
        // Generate tags based on common words
        $words = str_word_count($text, 1);
        $wordCount = array_count_values($words);
        arsort($wordCount);
        $tags = array_slice(array_keys($wordCount), 0, 5);
        
        return [
            'error' => false,
            'category' => $category,
            'summary' => $summary,
            'key_info' => 'Key information extracted from document content.',
            'legal_implications' => 'No specific legal implications identified',
            'compliance_status' => 'review_required',
            'tags' => $tags,
            'fallback' => true,
            'requires_legal_review' => $requiresLegalReview,
            'requires_visitor_coordination' => $requiresVisitorCoordination,
            'legal_risk_score' => $legalRiskScore
        ];
    }

    private function parseAnalysisResponse($text)
    {
        $lines = explode("\n", $text);
        $analysis = [
            'error' => false,
            'category' => 'general',
            'confidence' => 0.5,
            'summary' => '',
            'key_info' => '',
            'legal_implications' => '',
            'compliance_status' => 'review_required',
            'tags' => [],
            'requires_legal_review' => false,
            'requires_visitor_coordination' => false,
            'legal_risk_score' => 'Low'
        ];

        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, 'CATEGORY:') === 0) {
                $analysis['category'] = trim(str_replace('CATEGORY:', '', $line));
            } elseif (strpos($line, 'CONFIDENCE:') === 0) {
                $conf = (float) trim(str_replace('CONFIDENCE:', '', $line));
                if ($conf >= 0 && $conf <= 1) {
                    $analysis['confidence'] = $conf;
                }
            } elseif (strpos($line, 'SUMMARY:') === 0) {
                $analysis['summary'] = trim(str_replace('SUMMARY:', '', $line));
            } elseif (strpos($line, 'KEY_INFO:') === 0) {
                $analysis['key_info'] = trim(str_replace('KEY_INFO:', '', $line));
            } elseif (strpos($line, 'LEGAL_IMPLICATIONS:') === 0) {
                $analysis['legal_implications'] = trim(str_replace('LEGAL_IMPLICATIONS:', '', $line));
            } elseif (strpos($line, 'COMPLIANCE_STATUS:') === 0) {
                $analysis['compliance_status'] = trim(str_replace('COMPLIANCE_STATUS:', '', $line));
            } elseif (strpos($line, 'TAGS:') === 0) {
                $tagsText = trim(str_replace('TAGS:', '', $line));
                $analysis['tags'] = array_map('trim', explode(',', $tagsText));
            } elseif (strpos($line, 'LEGAL_REVIEW_REQUIRED:') === 0) {
                $analysis['requires_legal_review'] = (trim(str_replace('LEGAL_REVIEW_REQUIRED:', '', $line)) === 'YES');
            } elseif (strpos($line, 'VISITOR_COORDINATION_REQUIRED:') === 0) {
                $analysis['requires_visitor_coordination'] = (trim(str_replace('VISITOR_COORDINATION_REQUIRED:', '', $line)) === 'YES');
            } elseif (strpos($line, 'LEGAL_RISK_SCORE:') === 0) {
                $analysis['legal_risk_score'] = trim(str_replace('LEGAL_RISK_SCORE:', '', $line));
            }
        }

        return $analysis;
    }

    /**
     * Extract category from fallback text when OCR fails
     */
    private function extractCategoryFromFallbackText($text)
    {
        $text = strtolower($text);
        
        // Check for specific document types in fallback text
        if (str_contains($text, 'policy') || str_contains($text, 'privacy')) {
            return 'policy';
        }
        if (str_contains($text, 'contract') || str_contains($text, 'agreement')) {
            return 'contract';
        }
        if (str_contains($text, 'memo') || str_contains($text, 'memorandum')) {
            return 'memorandum';
        }
        if (str_contains($text, 'report')) {
            return 'report';
        }
        if (str_contains($text, 'financial') || str_contains($text, 'invoice')) {
            return 'financial';
        }
        if (str_contains($text, 'legal') || str_contains($text, 'affidavit')) {
            return 'legal';
        }
        
        return 'general';
    }

    /**
     * Determine if legal review is required from fallback text
     */
    private function determineLegalReviewFromFallback($text)
    {
        $text = strtolower($text);
        
        // High-risk document types that typically require legal review
        if (str_contains($text, 'contract') || 
            str_contains($text, 'agreement') ||
            str_contains($text, 'legal') ||
            str_contains($text, 'policy') ||
            str_contains($text, 'compliance')) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine risk score from fallback text
     */
    private function determineRiskFromFallback($text)
    {
        $text = strtolower($text);
        
        // High-risk indicators
        if (str_contains($text, 'contract') || 
            str_contains($text, 'legal') ||
            str_contains($text, 'policy') ||
            str_contains($text, 'compliance')) {
            return 'Medium';
        }
        
        // Low-risk indicators
        if (str_contains($text, 'memo') || 
            str_contains($text, 'report') ||
            str_contains($text, 'general')) {
            return 'Low';
        }
        
        return 'Low';
    }

    public function analyzeLegalDocument($text)
    {
        try {
            // Use the correct API endpoint with gemini-1.5-flash model
            $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=' . $this->apiKey;
            
            // Debug: Log the URL (remove in production)
            \Log::info('Gemini Legal API URL: ' . $url);
            
            $response = $this->client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => "You are a legal document classification system. Your task is to analyze the text of a legal document and categorize it into one of the predefined types.

Here are the possible categories:
- Memorandum
- Contract
- Complaint
- Deed of Sale
- Last Will and Testament
- Affidavit
- Power of Attorney
- Other Legal Document

Your response should ONLY contain the category name. Do not include any additional text, explanation, or punctuation. The goal is for the output to be a single, clean category name that can be directly used by a system.

IMPORTANT: Also provide a LEGAL_RISK_SCORE: [Low, Medium, High] based on potential legal issues or implications.

**Document to Classify:**
" . $text . "
LEGAL_RISK_SCORE: [Low/Medium/High]"
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
            $result = json_decode($response->getBody(), true);

            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $analysisText = $result['candidates'][0]['content']['parts'][0]['text'];
                $lines = explode("\n", $analysisText);
                $category = 'Other Legal Document'; // Default
                $legalRiskScore = 'Low';

                foreach ($lines as $line) {
                    $line = trim($line);
                    if (strpos($line, 'LEGAL_RISK_SCORE:') === 0) {
                        $legalRiskScore = trim(str_replace('LEGAL_RISK_SCORE:', '', $line));
                    } else {
                        // Assume the first line that is not a risk score is the category
                        if (!empty($line)) {
                            $category = $line;
                        }
                    }
                }

                return [
                    'error' => false,
                    'category' => $category,
                    'legal_risk_score' => $legalRiskScore,
                    'requires_legal_review' => ($legalRiskScore === 'High' || $legalRiskScore === 'Medium')
                ];
            }

            return [
                'error' => true,
                'message' => 'Invalid response format from Gemini API'
            ];
        } catch (RequestException $e) {
            // Error handling, provide a basic fallback
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'category' => 'Other Legal Document',
                'legal_risk_score' => 'Medium', // Default to Medium risk on error
                'requires_legal_review' => true, // Assume review is required on error
                'fallback' => true
            ];
        }
    }

    /**
     * Generate content using Gemini AI
     */
    public function generateContent($prompt)
    {
        try {
            // If API key is missing, return fallback content
            if (empty($this->apiKey)) {
                \Log::warning('GEMINI_API_KEY is not set, using fallback content generation');
                return $this->generateFallbackContent($prompt);
            }
            
            $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=' . $this->apiKey;
            
            $response = $this->client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                return [
                    'content' => $result['candidates'][0]['content']['parts'][0]['text']
                ];
            }
            
            return [
                'error' => true,
                'message' => 'Invalid response format from Gemini API'
            ];
            
        } catch (RequestException $e) {
            \Log::error('Gemini content generation failed', [
                'error' => $e->getMessage(),
                'falling_back_to_local' => true
            ]);
            return $this->generateFallbackContent($prompt);
        } catch (\Throwable $e) {
            \Log::error('Unexpected error in Gemini content generation', [
                'error' => $e->getMessage(),
                'falling_back_to_local' => true
            ]);
            return $this->generateFallbackContent($prompt);
        }
    }

    /**
     * Generate fallback content when AI is unavailable
     */
    private function generateFallbackContent($prompt)
    {
        // Extract document type from prompt
        $documentType = 'general';
        if (strpos($prompt, 'employment contract') !== false) {
            $documentType = 'employment_contract';
        } elseif (strpos($prompt, 'service contract') !== false) {
            $documentType = 'service_contract';
        } elseif (strpos($prompt, 'guest agreement') !== false) {
            $documentType = 'guest_agreement';
        } elseif (strpos($prompt, 'vendor agreement') !== false) {
            $documentType = 'vendor_agreement';
        } elseif (strpos($prompt, 'hr policy') !== false) {
            $documentType = 'hr_policy';
        }

        // Generate appropriate fallback content based on document type
        switch ($documentType) {
            case 'employment_contract':
                return [
                    'content' => "This section outlines the terms and conditions of employment. The employee agrees to perform duties as assigned and follow company policies. Compensation will be provided as agreed upon in the employment terms. Both parties acknowledge their rights and responsibilities under this agreement."
                ];
            case 'service_contract':
                return [
                    'content' => "This section defines the scope of services to be provided. The service provider agrees to deliver services according to the specifications outlined. Payment terms and performance standards are established to ensure quality service delivery. Both parties commit to fulfilling their obligations under this agreement."
                ];
            case 'guest_agreement':
                return [
                    'content' => "This section establishes the terms for guest accommodation and facility access. Guests agree to follow facility rules and regulations during their stay. The host provides appropriate accommodations and maintains facility standards. Both parties acknowledge their respective responsibilities for a safe and comfortable experience."
                ];
            case 'vendor_agreement':
                return [
                    'content' => "This section outlines the supply terms and conditions for vendor services. The vendor agrees to provide goods or services according to specified requirements. Quality standards and delivery schedules are established to ensure consistent performance. Both parties commit to maintaining professional business relationships."
                ];
            case 'hr_policy':
                return [
                    'content' => "This section establishes organizational policies and procedures for employee conduct and operations. All employees are expected to comply with these guidelines to maintain workplace standards. The policy outlines expectations, procedures, and consequences for non-compliance. Regular review and updates ensure the policy remains current and effective."
                ];
            default:
                return [
                    'content' => "This section contains important information relevant to the document. Please review the content carefully and ensure all details are accurate and complete. Additional information may be required based on specific circumstances and requirements."
                ];
        }
    }
}