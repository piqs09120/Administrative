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
        
        // Debug: Check if API key is being read
        if (empty($this->apiKey)) {
            throw new \Exception('GEMINI_API_KEY is not set in environment variables');
        }
    }

    public function analyzeDocument($text)
    {
        try {
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
                                    'text' => "You are an expert legal document classifier and analyzer. Analyze this document and provide a detailed, structured response in the following format:

CATEGORY: [Choose one from: memorandum, contract, subpoena, affidavit, cease_desist, legal_notice, policy, legal_brief, financial, compliance, report, general]

DETAILED CLASSIFICATION RULES:
1. MEMORANDUM: Documents containing 'memorandum', 'memoranda', 'memo', 'memorandum of agreement', 'MOA', or informal agreements between parties
2. CONTRACT: Formal legal agreements with terms, conditions, parties, signatures, and binding obligations
3. SUBPOENA: Court orders requiring appearance or document production, containing 'subpoena', 'subpoenas', 'court order'
4. AFFIDAVIT: Sworn statements or declarations made under oath, containing 'affidavit', 'affidavits', 'sworn', 'under oath', 'declare', 'declaration'
5. CEASE_DESIST: Legal notices demanding cessation of specific activities, containing 'cease', 'desist', 'stop', 'discontinue'
6. LEGAL_NOTICE: Official legal communications, warnings, or notifications
7. POLICY: Organizational rules, procedures, guidelines, or standards (NOT memorandums)
8. LEGAL_BRIEF: Legal analysis, case summaries, or legal arguments, containing 'brief', 'legal brief', 'case brief'
9. FINANCIAL: Financial reports, budgets, statements, or monetary documents, containing 'financial', 'budget', 'money', 'financial statement'
10. COMPLIANCE: Regulatory compliance documents, audit reports, or regulatory filings, containing 'compliance', 'regulation', 'regulatory'
11. REPORT: Analysis reports, assessments, or investigative documents, containing 'report', 'analysis', 'assessment'
12. GENERAL: Any document that doesn't fit the above categories

CRITICAL CLASSIFICATION PRIORITY:
- Check for specific document type keywords FIRST before general terms
- If document contains 'affidavit', 'sworn', 'under oath', 'declare' - classify as AFFIDAVIT
- If document contains 'subpoena', 'court order' - classify as SUBPOENA
- If document contains 'memorandum', 'memo', 'MOA' - classify as MEMORANDUM
- If document contains 'cease', 'desist' - classify as CEASE_DESIST
- If document contains 'brief', 'legal brief' - classify as LEGAL_BRIEF
- If document contains 'financial', 'budget', 'money' - classify as FINANCIAL
- If document contains 'compliance', 'regulation' - classify as COMPLIANCE
- If document contains 'report', 'analysis' - classify as REPORT
- Only use GENERAL if the document truly doesn't fit any other category

SUMMARY: [Provide a comprehensive 3-4 sentence summary of the document's content, purpose, and key elements]
KEY_INFO: [List important details such as dates, names, amounts, parties involved, deadlines, references, and any critical information]
LEGAL_IMPLICATIONS: [Explain any legal consequences, risks, obligations, rights, or implications if applicable]
COMPLIANCE_STATUS: [Determine if the document indicates: compliant, non-compliant, or review_required based on relevant standards]
TAGS: [Provide 5-7 relevant tags separated by commas, focusing on document themes, types, and key concepts]

IMPORTANT GUIDELINES:
- Be thorough and professional in your analysis
- Focus on practical significance and implications
- Consider regulatory compliance and business risks
- Provide actionable insights for document management
- If no legal implications exist, state 'No specific legal implications identified'
- Choose the most specific and accurate category
- Look for specific document type indicators FIRST before general classification

Document text to analyze: " . $text
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            // Parse the response to extract structured data
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $analysisText = $result['candidates'][0]['content']['parts'][0]['text'];
                return $this->parseAnalysisResponse($analysisText);
            }
            
            return [
                'error' => true,
                'message' => 'Invalid response format from Gemini API'
            ];
            
        } catch (RequestException $e) {
            // Check if it's a quota exceeded error
            if (strpos($e->getMessage(), '429') !== false || strpos($e->getMessage(), 'quota') !== false) {
                // Return fallback analysis based on text content
                return $this->fallbackAnalysis($text);
            }
            
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    private function fallbackAnalysis($text)
    {
        // Enhanced keyword-based analysis as fallback
        $text = strtolower($text);
        
        // Determine category based on keywords with better priority
        $category = 'general';
        
        // Check for specific legal document types first - PRIORITY ORDER
        if (strpos($text, 'affidavit') !== false || strpos($text, 'affidavits') !== false || strpos($text, 'sworn') !== false || strpos($text, 'under oath') !== false || strpos($text, 'declare') !== false || strpos($text, 'declaration') !== false) {
            $category = 'affidavit';
        } elseif (strpos($text, 'subpoena') !== false || strpos($text, 'subpoenas') !== false || strpos($text, 'court order') !== false) {
            $category = 'subpoena';
        } elseif (strpos($text, 'memorandum') !== false || strpos($text, 'memoranda') !== false || strpos($text, 'memo') !== false || strpos($text, 'moa') !== false) {
            $category = 'memorandum';
        } elseif (strpos($text, 'cease') !== false && strpos($text, 'desist') !== false) {
            $category = 'cease_desist';
        } elseif (strpos($text, 'brief') !== false || strpos($text, 'legal brief') !== false || strpos($text, 'case brief') !== false) {
            $category = 'legal_brief';
        } elseif (strpos($text, 'financial') !== false || strpos($text, 'budget') !== false || strpos($text, 'money') !== false || strpos($text, 'financial statement') !== false) {
            $category = 'financial';
        } elseif (strpos($text, 'compliance') !== false || strpos($text, 'regulation') !== false || strpos($text, 'regulatory') !== false) {
            $category = 'compliance';
        } elseif (strpos($text, 'report') !== false || strpos($text, 'analysis') !== false || strpos($text, 'assessment') !== false) {
            $category = 'report';
        } elseif (strpos($text, 'agreement') !== false) {
            // If it contains 'agreement' but not explicitly a formal contract, it's likely a memorandum
            if (strpos($text, 'parties') !== false && strpos($text, 'hereby') !== false && strpos($text, 'terms and conditions') !== false && strpos($text, 'binding') !== false) {
                $category = 'contract';
            } else {
                $category = 'memorandum';
            }
        } elseif (strpos($text, 'contract') !== false || strpos($text, 'lease') !== false || strpos($text, 'employment') !== false) {
            $category = 'contract';
        } elseif (strpos($text, 'legal notice') !== false || strpos($text, 'legal notices') !== false) {
            $category = 'legal_notice';
        } elseif (strpos($text, 'policy') !== false || strpos($text, 'procedure') !== false || strpos($text, 'guidelines') !== false) {
            $category = 'policy';
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
            'fallback' => true
        ];
    }

    private function parseAnalysisResponse($text)
    {
        $lines = explode("\n", $text);
        $analysis = [
            'error' => false,
            'category' => 'general',
            'summary' => '',
            'key_info' => '',
            'legal_implications' => '',
            'compliance_status' => 'review_required',
            'tags' => []
        ];

        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, 'CATEGORY:') === 0) {
                $analysis['category'] = trim(str_replace('CATEGORY:', '', $line));
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
            }
        }

        return $analysis;
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

**Document to Classify:**
" . $text
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
            $result = json_decode($response->getBody(), true);
            return $result;
        } catch (RequestException $e) {
            // Error handling
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }
}