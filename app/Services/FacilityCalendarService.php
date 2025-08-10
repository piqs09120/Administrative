<?php

namespace App\Services;

use App\Models\FacilityReservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FacilityCalendarService
{
    /**
     * Check facility availability for a given time period
     */
    public function checkAvailability($facilityId, $startTime, $endTime, $excludeReservationId = null)
    {
        $startTime = Carbon::parse($startTime);
        $endTime = Carbon::parse($endTime);
        
        // Find conflicting reservations
        $conflicts = $this->findConflictingReservations($facilityId, $startTime, $endTime, $excludeReservationId);
        
        return [
            'available' => $conflicts->isEmpty(),
            'conflicts' => $conflicts->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'purpose' => $reservation->purpose,
                    'reserver' => $reservation->reserver->name,
                    'start_time' => $reservation->start_time->format('Y-m-d H:i:s'),
                    'end_time' => $reservation->end_time->format('Y-m-d H:i:s'),
                    'status' => $reservation->status
                ];
            })->toArray(),
            'conflict_details' => $this->generateConflictDetails($conflicts),
            'suggested_times' => $this->suggestAlternativeTimes($facilityId, $startTime, $endTime)
        ];
    }

    /**
     * Find conflicting reservations for a facility and time period
     */
    private function findConflictingReservations($facilityId, $startTime, $endTime, $excludeReservationId = null)
    {
        $query = FacilityReservation::where('facility_id', $facilityId)
            ->whereIn('status', ['approved', 'pending']) // Don't consider denied reservations
            ->where(function ($query) use ($startTime, $endTime) {
                // Check for overlapping time periods
                $query->where(function ($q) use ($startTime, $endTime) {
                    // Case 1: New reservation starts during existing reservation
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // Case 2: New reservation ends during existing reservation  
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // Case 3: New reservation completely contains existing reservation
                    $q->where('start_time', '>=', $startTime)
                      ->where('end_time', '<=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // Case 4: Existing reservation completely contains new reservation
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>=', $endTime);
                });
            });

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return $query->with(['reserver', 'facility'])->get();
    }

    /**
     * Generate detailed conflict descriptions
     */
    private function generateConflictDetails($conflicts)
    {
        if ($conflicts->isEmpty()) {
            return null;
        }

        $details = [];
        foreach ($conflicts as $conflict) {
            $details[] = sprintf(
                "Conflict with reservation #%d by %s from %s to %s for '%s'",
                $conflict->id,
                $conflict->reserver->name,
                $conflict->start_time->format('M j, Y g:i A'),
                $conflict->end_time->format('M j, Y g:i A'),
                $conflict->purpose ?: 'No purpose specified'
            );
        }

        return implode('; ', $details);
    }

    /**
     * Suggest alternative available time slots
     */
    private function suggestAlternativeTimes($facilityId, $requestedStart, $requestedEnd)
    {
        $duration = $requestedEnd->diffInMinutes($requestedStart);
        $suggestions = [];
        
        // Look for slots in the same day
        $dayStart = $requestedStart->copy()->startOfDay()->addHours(8); // 8 AM
        $dayEnd = $requestedStart->copy()->startOfDay()->addHours(18); // 6 PM
        
        $currentSlot = $dayStart->copy();
        
        while ($currentSlot->addMinutes($duration)->lte($dayEnd) && count($suggestions) < 3) {
            $slotEnd = $currentSlot->copy()->addMinutes($duration);
            
            $availability = $this->checkAvailability($facilityId, $currentSlot, $slotEnd);
            
            if ($availability['available']) {
                $suggestions[] = [
                    'start_time' => $currentSlot->format('Y-m-d H:i:s'),
                    'end_time' => $slotEnd->format('Y-m-d H:i:s'),
                    'formatted' => $currentSlot->format('M j, Y g:i A') . ' - ' . $slotEnd->format('g:i A')
                ];
            }
            
            $currentSlot->addMinutes(30); // Check every 30 minutes
        }
        
        return $suggestions;
    }

    /**
     * Generate a calendar view for a facility
     */
    public function getFacilityCalendar($facilityId, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfMonth();
        
        $reservations = FacilityReservation::where('facility_id', $facilityId)
            ->whereIn('status', ['approved', 'pending'])
            ->whereBetween('start_time', [$startDate, $endDate])
            ->with(['reserver'])
            ->orderBy('start_time')
            ->get();

        $calendar = [];
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            $dayReservations = $reservations->filter(function ($reservation) use ($current) {
                return $reservation->start_time->isSameDay($current);
            });
            
            $calendar[$current->format('Y-m-d')] = [
                'date' => $current->format('Y-m-d'),
                'formatted_date' => $current->format('M j, Y'),
                'day_of_week' => $current->format('l'),
                'reservations' => $dayReservations->map(function ($reservation) {
                    return [
                        'id' => $reservation->id,
                        'purpose' => $reservation->purpose,
                        'reserver' => $reservation->reserver->name,
                        'start_time' => $reservation->start_time->format('g:i A'),
                        'end_time' => $reservation->end_time->format('g:i A'),
                        'status' => $reservation->status,
                        'duration_hours' => $reservation->start_time->diffInHours($reservation->end_time)
                    ];
                })->toArray(),
                'is_available' => $dayReservations->isEmpty(),
                'utilization_hours' => $dayReservations->sum(function ($reservation) {
                    return $reservation->start_time->diffInHours($reservation->end_time);
                })
            ];
            
            $current->addDay();
        }
        
        return $calendar;
    }

    /**
     * Check if a facility is available for a specific duration pattern
     */
    public function checkRecurringAvailability($facilityId, $startTime, $endTime, $pattern = 'weekly', $occurrences = 4)
    {
        $conflicts = [];
        $currentStart = Carbon::parse($startTime);
        $currentEnd = Carbon::parse($endTime);
        
        for ($i = 0; $i < $occurrences; $i++) {
            $availability = $this->checkAvailability($facilityId, $currentStart, $currentEnd);
            
            if (!$availability['available']) {
                $conflicts[] = [
                    'occurrence' => $i + 1,
                    'start_time' => $currentStart->format('Y-m-d H:i:s'),
                    'end_time' => $currentEnd->format('Y-m-d H:i:s'),
                    'conflicts' => $availability['conflicts']
                ];
            }
            
            // Move to next occurrence based on pattern
            switch ($pattern) {
                case 'daily':
                    $currentStart->addDay();
                    $currentEnd->addDay();
                    break;
                case 'weekly':
                    $currentStart->addWeek();
                    $currentEnd->addWeek();
                    break;
                case 'monthly':
                    $currentStart->addMonth();
                    $currentEnd->addMonth();
                    break;
            }
        }
        
        return [
            'all_available' => empty($conflicts),
            'conflicts' => $conflicts,
            'available_occurrences' => $occurrences - count($conflicts)
        ];
    }
}
