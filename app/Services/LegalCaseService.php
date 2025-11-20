<?php

namespace App\Services;

use App\Models\LegalCase;
use App\Models\FacilityReservation;
use App\Models\User;
use App\Models\DeptAccount;
use Illuminate\Support\Facades\Log;

class LegalCaseService
{
    /**
     * Create a legal case from facility reservation damage
     * 
     * @param FacilityReservation $reservation
     * @return LegalCase
     */
    public function createFromReservationDamage(FacilityReservation $reservation): LegalCase
    {
        try {
            // Ensure relationships are loaded
            if (!$reservation->relationLoaded('facility')) {
                $reservation->load('facility');
            }
            if (!$reservation->relationLoaded('reserver')) {
                $reservation->load('reserver');
            }
            
            // Get facility name safely
            $facilityName = $reservation->facility->name ?? 'Unknown Facility';
            $facilityLocation = $reservation->facility->location ?? '';
            
            // Determine priority based on damage cost
            $priority = $this->determinePriority($reservation->damage_cost ?? 0);
            
            // Generate case title
            $caseTitle = "Facility Damage: {$facilityName} - Reservation #{$reservation->id}";
            
            // Build case description
            $caseDescription = $this->buildCaseDescription($reservation);
            
            // Get reserver name
            $reserverName = $reservation->reserver->name ?? $reservation->requester_name ?? 'Unknown';
            
            // Create legal case
            Log::info('Creating legal case', [
                'reservation_id' => $reservation->id,
                'case_type' => 'facility_damage',
                'facility_name' => $facilityName,
                'damage_cost' => $reservation->damage_cost ?? 0,
            ]);
            
            // Get created_by as Dept_no (string) - required by the schema
            // LegalCase.created_by expects Dept_no from department_accounts table, not user ID
            $createdByDeptNo = null;
            if ($reservation->inspected_by) {
                $inspector = \App\Models\User::find($reservation->inspected_by);
                if ($inspector) {
                    // Try to get Dept_no from User model if it exists
                    if (isset($inspector->Dept_no)) {
                        $createdByDeptNo = $inspector->Dept_no;
                    } elseif (isset($inspector->employee_id)) {
                        // If User has employee_id, find DeptAccount
                        $deptAccount = \App\Models\DeptAccount::where('employee_id', $inspector->employee_id)->first();
                        $createdByDeptNo = $deptAccount->Dept_no ?? null;
                    }
                }
            }
            if (!$createdByDeptNo && auth()->check()) {
                $user = auth()->user();
                if (isset($user->Dept_no)) {
                    $createdByDeptNo = $user->Dept_no;
                } elseif (isset($user->employee_id)) {
                    $deptAccount = \App\Models\DeptAccount::where('employee_id', $user->employee_id)->first();
                    $createdByDeptNo = $deptAccount->Dept_no ?? null;
                }
            }
            
            Log::info('Legal case creation - created_by info', [
                'inspected_by' => $reservation->inspected_by,
                'created_by_dept_no' => $createdByDeptNo,
                'auth_user_id' => auth()->id(),
            ]);
            
            $legalCase = LegalCase::create([
                'case_title' => $caseTitle,
                'case_description' => $caseDescription,
                'case_type' => 'facility_damage',
                'priority' => $priority,
                'status' => 'pending',
                'created_by' => $createdByDeptNo, // Must be Dept_no (string), not user ID
                'incident_date' => $reservation->returned_at ?? $reservation->end_time ?? now(),
                'incident_location' => $facilityName . ($facilityLocation ? ' - ' . $facilityLocation : ''),
                'metadata' => [
                    'facility_reservation_id' => $reservation->id,
                    'facility_request_id' => $reservation->facility_request_id,
                    'facility_id' => $reservation->facility_id,
                    'facility_name' => $facilityName,
                    'reserved_by' => $reservation->reserved_by,
                    'reserver_name' => $reserverName,
                    'damage_cost' => $reservation->damage_cost ?? 0,
                    'damage_flag' => $reservation->damage_flag ?? false,
                    'inspection_notes' => $reservation->inspection_notes,
                    'inspected_by' => $reservation->inspected_by,
                    'inspected_at' => $reservation->inspected_at?->toDateTimeString(),
                    'source' => 'facility_reservation',
                ],
            ]);
            
            // Refresh to get the case_number generated by boot method
            $legalCase->refresh();
            
            // Update reservation with legal case ID
            $reservation->update([
                'legal_case_id' => $legalCase->id,
            ]);
            
            // Verify the case was created with correct case_type
            $verifyCase = LegalCase::find($legalCase->id);
            if (!$verifyCase) {
                throw new \Exception("Legal case was not found after creation");
            }
            
            Log::info('Legal case created from facility damage', [
                'reservation_id' => $reservation->id,
                'legal_case_id' => $legalCase->id,
                'case_number' => $legalCase->case_number,
                'case_type' => $legalCase->case_type,
                'case_title' => $legalCase->case_title,
                'verified_case_type' => $verifyCase->case_type,
            ]);
            
            return $legalCase;
            
        } catch (\Exception $e) {
            Log::error('Failed to create legal case from facility damage', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
    
    /**
     * Determine priority based on damage cost
     * 
     * @param float $damageCost
     * @return string
     */
    private function determinePriority(float $damageCost): string
    {
        if ($damageCost >= 50000) {
            return 'urgent';
        } elseif ($damageCost >= 25000) {
            return 'high';
        } elseif ($damageCost >= 10000) {
            return 'high';
        } else {
            return 'medium';
        }
    }
    
    /**
     * Build case description from reservation details
     * 
     * @param FacilityReservation $reservation
     * @return string
     */
    private function buildCaseDescription(FacilityReservation $reservation): string
    {
        $facilityName = $reservation->facility->name ?? 'Unknown Facility';
        $reserverName = $reservation->reserver->name ?? $reservation->requester_name ?? 'Unknown';
        
        $description = "Facility Damage Case\n\n";
        $description .= "Facility: {$facilityName}\n";
        $description .= "Reservation ID: #{$reservation->id}\n";
        $description .= "Reserved By: {$reserverName}\n";
        
        if ($reservation->start_time && $reservation->end_time) {
            $description .= "Reservation Period: {$reservation->start_time->format('M d, Y h:i A')} to {$reservation->end_time->format('M d, Y h:i A')}\n";
        }
        
        $description .= "Purpose: " . ($reservation->purpose ?? 'N/A') . "\n\n";
        
        $description .= "Damage Information:\n";
        $description .= "Estimated Cost: ₱" . number_format($reservation->damage_cost ?? 0, 2) . "\n";
        
        if ($reservation->inspection_notes) {
            $description .= "Inspection Notes: {$reservation->inspection_notes}\n";
        }
        
        if ($reservation->inspected_by) {
            $inspector = \App\Models\User::find($reservation->inspected_by);
            $description .= "Inspected By: " . ($inspector->name ?? $inspector->employee_name ?? 'Unknown') . "\n";
        }
        
        if ($reservation->inspected_at) {
            $description .= "Inspection Date: {$reservation->inspected_at->format('M d, Y h:i A')}\n";
        }
        
        return $description;
    }
}
