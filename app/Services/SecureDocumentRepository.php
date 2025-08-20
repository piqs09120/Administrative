<?php

namespace App\Services;

use App\Models\FacilityReservation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class SecureDocumentRepository
{
    /**
     * Store and index uploaded document in secure repository
     */
    public function storeDocument(UploadedFile $file, $reservationId, $classificationData = null)
    {
        try {
            // Generate secure filename
            $filename = $this->generateSecureFilename($file, $reservationId);
            
            // Store file in secure directory
            $path = $file->storeAs('secure_documents/facility_reservations', $filename, 'private');
            
            // Create document index entry
            $documentIndex = [
                'original_filename' => $file->getClientOriginalName(),
                'secure_filename' => $filename,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now()->toISOString(),
                'reservation_id' => $reservationId,
                'classification_data' => $classificationData,
                'security_hash' => hash_file('sha256', $file->getRealPath()),
                'status' => 'stored'
            ];
            
            // Log the document storage
            $this->logDocumentStorage($documentIndex);
            
            return [
                'success' => true,
                'path' => $path,
                'index' => $documentIndex
            ];
            
        } catch (\Exception $e) {
            Log::error('SecureDocumentRepository: Failed to store document', [
                'reservation_id' => $reservationId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Log classification metadata from Gemini AI
     */
    public function logClassificationMetadata($reservationId, $classificationData)
    {
        try {
            $metadata = [
                'reservation_id' => $reservationId,
                'classification_timestamp' => now()->toISOString(),
                'ai_engine' => 'gemini',
                'classification_results' => $classificationData,
                'document_category' => $classificationData['category'] ?? 'unknown',
                'confidence_score' => $classificationData['confidence'] ?? null,
                'extracted_entities' => $classificationData['entities'] ?? [],
                'summary' => $classificationData['summary'] ?? null
            ];
            
            // Store metadata in database or file system
            $this->storeClassificationMetadata($metadata);
            
            Log::info('SecureDocumentRepository: Classification metadata logged', [
                'reservation_id' => $reservationId,
                'category' => $metadata['document_category'],
            ]);
            
            return $metadata;
            
        } catch (\Exception $e) {
            Log::error('SecureDocumentRepository: Failed to log classification metadata', [
                'reservation_id' => $reservationId,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Retrieve document from secure repository
     */
    public function retrieveDocument($reservationId, $filename = null)
    {
        try {
            $reservation = FacilityReservation::find($reservationId);
            if (!$reservation || !$reservation->document_path) {
                return null;
            }
            
            $path = $reservation->document_path;
            
            if (Storage::disk('private')->exists($path)) {
                return [
                    'path' => $path,
                    'content' => Storage::disk('private')->get($path),
                    'exists' => true
                ];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('SecureDocumentRepository: Failed to retrieve document', [
                'reservation_id' => $reservationId,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Generate secure filename
     */
    private function generateSecureFilename(UploadedFile $file, $reservationId)
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $hash = substr(hash('sha256', $file->getClientOriginalName() . $reservationId . microtime()), 0, 8);
        
        return "res_{$reservationId}_{$timestamp}_{$hash}.{$extension}";
    }

    /**
     * Log document storage activity
     */
    private function logDocumentStorage($documentIndex)
    {
        // Create audit log entry
        Log::info('SecureDocumentRepository: Document stored', [
            'reservation_id' => $documentIndex['reservation_id'],
            'original_filename' => $documentIndex['original_filename'],
            'secure_filename' => $documentIndex['secure_filename'],
            'file_size' => $documentIndex['file_size'],
            'security_hash' => $documentIndex['security_hash']
        ]);
    }

    /**
     * Store classification metadata
     */
    private function storeClassificationMetadata($metadata)
    {
        // Store as JSON file in secure location
        $filename = "classification_metadata_res_{$metadata['reservation_id']}.json";
        $path = "secure_documents/metadata/{$filename}";
        
        Storage::disk('private')->put($path, json_encode($metadata, JSON_PRETTY_PRINT));
        
        return $path;
    }

    /**
     * Get document repository statistics
     */
    public function getRepositoryStats()
    {
        try {
            $documentsPath = 'secure_documents/facility_reservations';
            $metadataPath = 'secure_documents/metadata';
            
            $documentFiles = Storage::disk('private')->files($documentsPath);
            $metadataFiles = Storage::disk('private')->files($metadataPath);
            
            $totalSize = 0;
            foreach ($documentFiles as $file) {
                $totalSize += Storage::disk('private')->size($file);
            }
            
            return [
                'total_documents' => count($documentFiles),
                'total_metadata_files' => count($metadataFiles),
                'total_size_bytes' => $totalSize,
                'total_size_mb' => round($totalSize / 1024 / 1024, 2)
            ];
            
        } catch (\Exception $e) {
            Log::error('SecureDocumentRepository: Failed to get repository stats', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'total_documents' => 0,
                'total_metadata_files' => 0,
                'total_size_bytes' => 0,
                'total_size_mb' => 0
            ];
        }
    }
}
