<?php

namespace App\Services;

use App\Models\FacilityReservation;
use Illuminate\Support\Facades\Log;

class VisitorService
{
    public function extractVisitorData(FacilityReservation $reservation, array $aiResult): void
    {
        $extractedVisitors = [];

        if ($this->requiresVisitorCoordination($aiResult)) {
            $namesFound = [];
            // Attempt to extract names from key_info first
            if (!empty($aiResult['key_info'])) {
                // Very basic regex to find potential names (e.g., capitalized words)
                preg_match_all('/[A-Z][a-z]+\s(?:[A-Z][a-z]+\s?)+/', $aiResult['key_info'], $matches);
                foreach ($matches[0] as $name) {
                    // Avoid single words that are not likely names
                    if (str_word_count($name) > 1) {
                        $namesFound[] = trim($name);
                    }
                }
            }

            // Fallback to entities if no names or if more generic extraction is needed
            if (empty($namesFound) && !empty($aiResult['entities'])) {
                foreach ($aiResult['entities'] as $entity) {
                    if (in_array($entity['type'] ?? '', ['PERSON', 'person', 'visitor'])) {
                        $namesFound[] = $entity['value'];
                    }
                }
            }

            // If no specific visitors found but coordination required, create placeholder
            if (empty($namesFound)) {
                $namesFound[] = 'Visitor (Name to be confirmed)';
            }

            // Log cross-check step
            $reservation->logWorkflowStep('visitor_cross_check_started', 'Cross-checking extracted visitors with existing records');

            foreach ($namesFound as $name) {
                $company = $this->extractCompanyForVisitor($name, $aiResult['entities'] ?? []);

                // Cross-check: try to find an existing pending/active visitor with same name/company
                $existing = \App\Models\Visitor::query()
                    ->where('name', $name)
                    ->when(!empty($company), function ($q) use ($company) { $q->where('company', $company); })
                    ->where('facility_id', $reservation->facility_id)
                    ->whereDate('time_in', '>=', now()->subDays(7)->toDateString())
                    ->orderByDesc('id')
                    ->first();

                if ($existing) {
                    // Link to this reservation if not already linked
                    if ($existing->facility_reservation_id !== $reservation->id) {
                        $existing->update(['facility_reservation_id' => $reservation->id]);
                    }
                    $extractedVisitors[] = $existing->toArray();
                } else {
                    // Create a new Visitor model for each extracted name
                    $visitor = \App\Models\Visitor::create([
                        'name' => $name,
                        'contact' => 'N/A',
                        'purpose' => $reservation->purpose ?? 'Facility Reservation',
                        'facility_id' => $reservation->facility_id,
                        'time_in' => $reservation->start_time,
                        'time_out' => null, // Set null initially
                        'company' => $company,
                        'host_employee' => $reservation->reserver->name ?? 'N/A',
                        'status' => 'pending_approval', // Initial status for extracted visitors
                        'facility_reservation_id' => $reservation->id, // Link to reservation
                    ]);
                    $extractedVisitors[] = $visitor->toArray();
                }
            }
            $reservation->logWorkflowStep('visitor_cross_check_completed', 'Cross-check completed', [ 'matched_or_created' => count($extractedVisitors) ]);
        }

        if (!empty($extractedVisitors)) {
            // Store a simplified record of extracted visitors on the reservation (e.g., just IDs or names) if needed for summary
            // For now, the actual Visitor models are the source of truth.
            $reservation->logWorkflowStep('visitor_data_extracted', 'Created individual visitor records from document', [
                'visitor_count' => count($extractedVisitors)
            ]);
        } else {
            $reservation->logWorkflowStep('visitor_data_extraction_skipped', 'No visitors found or extracted from document');
        }
    }

    private function requiresVisitorCoordination(array $aiResult): bool
    {
        $visitorKeywords = ['visitor', 'guest', 'attendee', 'participant', 'delegate'];
        $summary = strtolower($aiResult['summary'] ?? '');
        $keyInfo = strtolower($aiResult['key_info'] ?? '');
        
        foreach ($visitorKeywords as $keyword) {
            if (strpos($summary, $keyword) !== false || strpos($keyInfo, $keyword) !== false) {
                return true;
            }
        }
        
        // Also check for 'visitor' type entities explicitly
        foreach (($aiResult['entities'] ?? []) as $entity) {
            if (in_array($entity['type'] ?? '', ['PERSON', 'person']) && strpos(strtolower($entity['value']), 'visitor') !== false) {
                return true;
            }
        }
        
        return false;
    }

    private function extractCompanyForVisitor($visitorName, $entities)
    {
        // Try to find organization entities near the visitor name
        foreach ($entities as $entity) {
            if (in_array($entity['type'] ?? '', ['ORGANIZATION', 'organization', 'company'])) {
                return $entity['value'];
            }
        }
        return '';
    }

    public function approveVisitors(FacilityReservation $reservation): void
    {
        // Retrieve the actual Visitor models associated with this reservation
        $visitorsToApprove = \App\Models\Visitor::where('facility_reservation_id', $reservation->id)
                                                ->where('status', 'pending_approval')
                                                ->get();
        
        if ($visitorsToApprove->isEmpty()) {
            Log::warning('Attempted to approve visitors for a reservation with no pending visitor records.', [
                'reservation_id' => $reservation->id
            ]);
            return;
        }

        // Update status of individual Visitor models
        foreach ($visitorsToApprove as $visitor) {
            $visitor->update([
                'status' => 'approved',
                'time_in' => $visitor->time_in ?? now(), // Ensure check-in time is set if not already
                'host_employee' => $visitor->host_employee ?? $reservation->reserver->name ?? 'System', // Ensure host is set
            ]);
        }

        // Generate digital passes for these approved visitors
        // The GenerateDigitalPasses job will now query Visitor models directly
        \App\Jobs\GenerateDigitalPasses::dispatch($reservation->id);

        Log::info('Visitors approved and digital pass generation dispatched', [
            'reservation_id' => $reservation->id,
            'approved_count' => $visitorsToApprove->count()
        ]);
    }
}
