<?php

namespace App\Services;

use App\Models\FacilityReservation;
use App\Models\ReservationTask;
use Illuminate\Support\Facades\Log;

class ReservationWorkflowService
{
    public function createDocumentClassificationTask(FacilityReservation $reservation, array $aiResult): ReservationTask
    {
        // Use the new AI analysis fields directly
        $requiresLegalReview = $aiResult['requires_legal_review'] ?? false;
        $requiresVisitorCoordination = $aiResult['requires_visitor_coordination'] ?? false;

        // Create the document classification task
        $documentTask = $reservation->tasks()->create([
            'task_type' => 'document_classification',
            'status' => 'completed',
            'assigned_to_module' => 'DM',
            'details' => [
                'ai_classification' => $aiResult,
                'requires_legal_review' => $requiresLegalReview,
                'requires_visitor_coordination' => $requiresVisitorCoordination,
            ],
            'completed_at' => now(),
            'completed_by' => auth()->id() // Assuming AI classification is 'completed' by the system/uploader
        ]);

        Log::info('Document classification task created and completed', [
            'reservation_id' => $reservation->id,
            'task_id' => $documentTask->id,
            'requires_legal_review' => $requiresLegalReview,
            'requires_visitor_coordination' => $requiresVisitorCoordination,
        ]);

        // Create follow-up tasks if required
        if ($requiresLegalReview) {
            $this->createLegalReviewTask($reservation);
        }
        if ($requiresVisitorCoordination) {
            $this->createVisitorCoordinationTask($reservation);
        }
        
        // Update overall workflow status based on generated tasks
        $this->updateReservationOverallStatus($reservation);

        return $documentTask;
    }

    public function createLegalReviewTask(FacilityReservation $reservation): ReservationTask
    {
        $legalTask = $reservation->tasks()->create([
            'task_type' => 'legal_review',
            'status' => 'pending',
            'assigned_to_module' => 'LM',
            'details' => null,
        ]);
        Log::info('Legal review task created', ['reservation_id' => $reservation->id, 'task_id' => $legalTask->id]);
        $this->updateReservationOverallStatus($reservation);
        return $legalTask;
    }

    public function createVisitorCoordinationTask(FacilityReservation $reservation): ReservationTask
    {
        $visitorTask = $reservation->tasks()->create([
            'task_type' => 'visitor_coordination',
            'status' => 'pending',
            'assigned_to_module' => 'VM',
            'details' => null,
        ]);
        Log::info('Visitor coordination task created', ['reservation_id' => $reservation->id, 'task_id' => $visitorTask->id]);
        $this->updateReservationOverallStatus($reservation);
        return $visitorTask;
    }

    public function updateTaskStatus(ReservationTask $task, string $status, ?string $comment = null): void
    {
        $task->update([
            'status' => $status,
            'completed_by' => auth()->id(),
            'completed_at' => now(),
            'details' => array_merge($task->details ?? [], ['comment' => $comment, 'old_status' => $task->status])
        ]);
        Log::info('Reservation task status updated', ['task_id' => $task->id, 'new_status' => $status]);

        // Handle specific actions based on task type and new status
        if ($task->task_type === 'visitor_coordination') {
            if ($status === 'in_progress') {
                // Retrieve AI result from document classification task
                $documentTask = $task->facilityReservation->tasks()->where('task_type', 'document_classification')->first();
                $aiResult = $documentTask->details['ai_classification'] ?? [];
                
                if (!empty($aiResult)) {
                    // Dispatch the job to extract visitor data
                    \App\Jobs\ProcessVisitorExtractionJob::dispatch($task->facilityReservation->id, $aiResult);
                    Log::info('Dispatched ProcessVisitorExtractionJob', ['reservation_id' => $task->facilityReservation->id]);
                } else {
                    Log::warning('No AI classification found for visitor extraction, skipping job dispatch.', ['reservation_id' => $task->facilityReservation->id]);
                }
            } elseif ($status === 'completed') {
                // Dispatch jobs for digital passes and security notification
                // These jobs will now operate on the Visitor models directly linked to the reservation
                \App\Jobs\GenerateDigitalPasses::dispatch($task->facilityReservation->id);
                Log::info('Dispatched GenerateDigitalPasses job', ['reservation_id' => $task->facilityReservation->id]);

                \App\Jobs\NotifySecurityTeam::dispatch($task->facilityReservation->id);
                Log::info('Dispatched NotifySecurityTeam job', ['reservation_id' => $task->facilityReservation->id]);
            }
        }

        $this->updateReservationOverallStatus($task->facilityReservation);
    }

    private function updateReservationOverallStatus(FacilityReservation $reservation): void
    {
        // Refresh the reservation's tasks relationship
        $reservation->load('tasks');

        $hasPendingLegal = $reservation->tasks()->where('task_type', 'legal_review')->where('status', 'pending')->exists();
        $hasPendingVisitor = $reservation->tasks()->where('task_type', 'visitor_coordination')->where('status', 'pending')->exists();

        $newStatus = $reservation->current_workflow_status;

        if ($reservation->status === 'denied') {
            $newStatus = 'denied';
        } elseif ($reservation->status === 'approved') {
            $newStatus = 'approved';
        } elseif ($hasPendingLegal) {
            $newStatus = 'pending_legal_review';
        } elseif ($hasPendingVisitor) {
            $newStatus = 'pending_visitor_coordination';
        } elseif ($reservation->areAllRequiredTasksComplete()) {
            $newStatus = 'ready_for_approval';
        } else {
            $newStatus = 'pending_document_processing'; // Default if no other state applies
        }
        
        if ($reservation->current_workflow_status !== $newStatus) {
            $reservation->update(['current_workflow_status' => $newStatus]);
            $reservation->logWorkflowStep('status_update', 'Overall workflow status changed', ['new_status' => $newStatus]);
            Log::info('Reservation overall workflow status updated', ['reservation_id' => $reservation->id, 'status' => $newStatus]);
        }

        // Trigger auto-approval if ready
        if ($newStatus === 'ready_for_approval' && $reservation->status === 'pending') {
             \App\Jobs\CheckAndAutoApproveReservation::dispatch($reservation->id);
             $reservation->logWorkflowStep('auto_approval_queued', 'Reservation ready and queued for auto-approval');
             $reservation->update(['current_workflow_status' => 'auto_approval_in_progress']);
        }
    }
}
