<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\IdValidationPipelineService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class IdValidationController extends Controller
{
    protected $idValidationPipelineService;

    public function __construct(IdValidationPipelineService $idValidationPipelineService)
    {
        $this->idValidationPipelineService = $idValidationPipelineService;
    }

    /**
     * Validate ID document via API
     */
    public function validateDocument(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validated = $request->validate([
                'id_document' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
                'id_type' => 'required|string|in:philnational_id,passport,drivers_license,umid,postal_id,voters_id,sss_id,gsis_id,tin_id,prc_id,barangay_id,senior_citizen_id,pwd_id,company_id,school_id,other_id',
                'id_number' => 'nullable|string|max:255'
            ]);

            // Store the uploaded file temporarily
            $file = $request->file('id_document');
            $filename = 'temp_validation_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('temp_validation', $filename, 'public');

            // Perform layered validation pipeline
            $validationResult = $this->idValidationPipelineService->validateIdDocument(
                $path,
                $validated['id_type'],
                $validated['id_number']
            );

            // Clean up temporary file
            Storage::disk('public')->delete($path);

            // Get ID type name for response
            $idTypeNames = [
                'philnational_id' => 'Philippine National ID (PhilSys)',
                'passport' => 'Philippine Passport',
                'drivers_license' => 'Driver\'s License',
                'umid' => 'Unified Multipurpose ID (UMID)',
                'postal_id' => 'Postal ID',
                'voters_id' => 'Voter\'s ID',
                'sss_id' => 'SSS ID',
                'gsis_id' => 'GSIS ID',
                'tin_id' => 'TIN ID',
                'prc_id' => 'Professional Regulation Commission (PRC)',
                'barangay_id' => 'Barangay ID',
                'senior_citizen_id' => 'Senior Citizen ID',
                'pwd_id' => 'PWD ID',
                'company_id' => 'Company ID',
                'school_id' => 'School ID',
                'other_id' => 'Other Valid ID'
            ];

            return response()->json([
                'success' => true,
                'validation' => [
                    'is_valid' => $validationResult['is_valid'],
                    'confidence' => $validationResult['confidence'],
                    'score' => $validationResult['score'],
                    'status' => $validationResult['status'],
                    'id_type_name' => $idTypeNames[$validated['id_type']],
                    'predicted_id_type' => $validationResult['predicted_id_type'],
                    'detected_text' => $validationResult['detected_text'],
                    'extracted_fields' => $validationResult['extracted_fields'],
                    'validation_details' => $validationResult['validation_details'],
                    'reasons' => $validationResult['reasons'],
                    'barcode_data' => $validationResult['barcode_data']
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('ID Validation API Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Validation service temporarily unavailable',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}