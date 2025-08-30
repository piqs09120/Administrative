<?php

namespace App\Services;

use App\Models\Document;
use App\Models\FacilityReservation;
use App\Models\User;
use App\Notifications\FacilityReservationStatusNotification;
use App\Jobs\ProcessReservationDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class DocumentWorkflowNotificationService
{
    /**
     * Auto-schedule facility and notify requestor based on TO BE diagram
     */
    public function autoScheduleFacilityAndNotify($document, $aiAnalysis)
    {
        try {
            // Extract reservation details from document if it's a facility reservation
            if ($this->isFacilityReservationDocument($document, $aiAnalysis)) {
                $reservationDetails = $this->extractReservationDetailsFromDocument($document, $aiAnalysis);
                
                if ($reservationDetails) {
                    // Create facility reservation
                    $reservation = $this->createFacilityReservationFromDocument($document, $reservationDetails);
                    
                    if ($reservation) {
                        // Update document with reservation link
                        $document->update([
                            'workflow_stage' => 'auto_scheduled',
                            'linked_reservation_id' => $reservation->id
                        ]);
                        
                        // Log the auto-scheduling
                        $document->logWorkflowStep('auto_scheduled', 'Facility automatically scheduled from document', [
                            'reservation_id' => $reservation->id,
                            'facility_id' => $reservation->facility_id,
                            'scheduled_time' => $reservation->start_time
                        ]);
                        
                        // Notify requestor
                        $this->notifyRequestorOfScheduling($document, $reservation);
                        
                        // Dispatch processing job for the new reservation
                        ProcessReservationDocument::dispatch($reservation->id);
                        
                        Log::info('Auto-scheduled facility reservation from document', [
                            'document_id' => $document->id,
                            'reservation_id' => $reservation->id
                        ]);
                        
                        return $reservation;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Auto-scheduling failed for document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            
            $document->logWorkflowStep('auto_scheduling_failed', 'Auto-scheduling failed', [
                'error' => $e->getMessage()
            ]);
        }
        
        return null;
    }

    /**
     * Check if document is related to facility reservations
     */
    private function isFacilityReservationDocument($document, $aiAnalysis)
    {
        // Check both AI analysis and document title for facility-related keywords
        $text = strtolower($aiAnalysis['summary'] ?? '') . ' ' . strtolower($aiAnalysis['key_info'] ?? '') . ' ' . strtolower($document->title ?? '');
        $facilityKeywords = ['facility', 'room', 'conference', 'meeting', 'reservation', 'booking', 'schedule', 'venue', 'facility_request'];
        
        foreach ($facilityKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Extract reservation details from AI analysis
     */
    private function extractReservationDetailsFromDocument($document, $aiAnalysis)
    {
        $summary = $aiAnalysis['summary'] ?? '';
        $keyInfo = $aiAnalysis['key_info'] ?? '';
        $text = $summary . ' ' . $keyInfo;
        
        // Use regex and AI analysis to extract potential reservation details
        $details = [
            'purpose' => $this->extractPurpose($text),
            'requested_facility' => $this->extractFacilityName($text),
            'estimated_start_time' => $this->extractDateTime($text, 'start'),
            'estimated_end_time' => $this->extractDateTime($text, 'end'),
            'requester_name' => $document->author ?? auth()->user()->name,
            'extracted_from_ai' => true
        ];
        
        // Only return if we have minimum required information
        if ($details['purpose'] || $details['requested_facility']) {
            return $details;
        }
        
        return null;
    }

    /**
     * Extract purpose from text
     */
    private function extractPurpose($text)
    {
        // Look for common purpose patterns
        $patterns = [
            '/purpose:?\s*([^\.]+)/i',
            '/meeting\s+for\s+([^\.]+)/i',
            '/event:?\s*([^\.]+)/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return trim($matches[1]);
            }
        }
        
        // Fallback to first sentence if no specific purpose found
        $sentences = explode('.', $text);
        return isset($sentences[0]) ? Str::limit(trim($sentences[0]), 100) : 'Auto-extracted from document';
    }

    /**
     * Extract facility name from text
     */
    private function extractFacilityName($text)
    {
        // Look for facility/room name patterns
        $patterns = [
            '/(?:room|facility|venue|hall):?\s*([a-zA-Z0-9\s]+)/i',
            '/in\s+(?:the\s+)?([a-zA-Z0-9\s]+\s+(?:room|hall|facility))/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return trim($matches[1]);
            }
        }
        
        return null;
    }

    /**
     * Extract date/time from text
     */
    private function extractDateTime($text, $type = 'start')
    {
        // For auto-scheduling, use default times if not extractable
        $now = now();
        
        if ($type === 'start') {
            // Default to next business day at 9 AM
            return $now->addWeekday()->setTime(9, 0);
        } else {
            // Default to 1 hour after start time
            return $now->addWeekday()->setTime(10, 0);
        }
    }

    /**
     * Create facility reservation from document
     */
    private function createFacilityReservationFromDocument($document, $details)
    {
        // Find a suitable facility (prefer first available or create default)
        $facility = \App\Models\Facility::where('status', 'available')->first();
        
        if (!$facility) {
            // Create a default facility if none exists
            $facility = \App\Models\Facility::create([
                'name' => 'General Purpose Room',
                'description' => 'Auto-created facility for document-based reservations',
                'location' => 'Main Building',
                'status' => 'available'
            ]);
        }

        $reservation = FacilityReservation::create([
            'facility_id' => $facility->id,
            'reserved_by' => $document->uploaded_by,
            'start_time' => $details['estimated_start_time'],
            'end_time' => $details['estimated_end_time'],
            'purpose' => $details['purpose'],
            'status' => 'pending',
            'requester_name' => $details['requester_name'],
            'requester_contact' => auth()->user()->email ?? 'system@soliera.com',
            'workflow_stage' => 'auto_created_from_document',
            'document_id' => $document->id,
            'auto_created' => true
        ]);

        $reservation->logWorkflowStep('auto_created', 'Automatically created from document analysis', [
            'source_document_id' => $document->id,
            'extracted_details' => $details
        ]);

        return $reservation;
    }

    /**
     * Notify requestor of auto-scheduling
     */
    private function notifyRequestorOfScheduling($document, $reservation)
    {
        try {
            $user = User::find($document->uploaded_by);
            if ($user) {
                $user->notify(new FacilityReservationStatusNotification($reservation));
                
                $document->logWorkflowStep('requestor_notified', 'Requestor notified of auto-scheduling', [
                    'user_id' => $user->id,
                    'reservation_id' => $reservation->id
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to notify requestor of auto-scheduling', [
                'document_id' => $document->id,
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
