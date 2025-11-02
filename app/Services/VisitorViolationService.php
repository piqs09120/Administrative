<?php

namespace App\Services;

use App\Models\VisitorViolation;
use App\Models\LegalCase;
use App\Models\VisitorViolationAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class VisitorViolationService
{
    /**
     * Critical violation types that auto-escalate
     */
    protected const CRITICAL_TYPES = [
        'ID_FORGERY',
        'IMPERSONATION',
        'UNAUTHORIZED_ACCESS',
        'THEFT',
        'PROHIBITED_ITEM',
        'VIOLENCE',
        'HARASSMENT',
        'IT_POLICY_BREACH',
        'DATA_PRIVACY_BREACH',
    ];

    /**
     * Property damage threshold for auto-escalation (in PHP)
     */
    protected const PROPERTY_DAMAGE_THRESHOLD = 10000;

    /**
     * Create a visitor violation and auto-escalate if needed
     */
    public function createViolation(array $data): VisitorViolation
    {
        return DB::transaction(function () use ($data) {
            // Create violation
            $violation = VisitorViolation::create($data);

            // Log creation
            VisitorViolationAuditLog::create([
                'violation_id' => $violation->id,
                'action' => 'created',
                'actor_id' => $data['reported_by'] ?? null,
                'actor_name' => $data['reported_by_name'] ?? null,
                'old_status' => null,
                'new_status' => 'OPEN',
                'notes' => 'Violation created',
            ]);

            // Check if auto-escalation is needed
            if ($this->shouldAutoEscalate($violation)) {
                $this->escalateToLegal($violation, $data['reported_by'] ?? null, $data['reported_by_name'] ?? null);
            }

            return $violation->fresh();
        });
    }

    /**
     * Update violation and check for escalation triggers
     */
    public function updateViolation(VisitorViolation $violation, array $data): VisitorViolation
    {
        return DB::transaction(function () use ($violation, $data) {
            $oldStatus = $violation->status;
            $oldSeverity = $violation->severity;
            $oldCost = $violation->estimated_cost;

            // Update violation
            $violation->update($data);

            $changes = [];
            if (isset($data['status']) && $data['status'] !== $oldStatus) {
                $changes['status'] = ['from' => $oldStatus, 'to' => $data['status']];
            }
            if (isset($data['severity']) && $data['severity'] !== $oldSeverity) {
                $changes['severity'] = ['from' => $oldSeverity, 'to' => $data['severity']];
            }
            if (isset($data['estimated_cost']) && $data['estimated_cost'] != $oldCost) {
                $changes['estimated_cost'] = ['from' => $oldCost, 'to' => $data['estimated_cost']];
            }

            // Log update
            if (!empty($changes)) {
                VisitorViolationAuditLog::create([
                    'violation_id' => $violation->id,
                    'action' => 'updated',
                    'actor_id' => auth()->id(),
                    'actor_name' => auth()->user()?->name,
                    'old_status' => $oldStatus,
                    'new_status' => $violation->status,
                    'notes' => 'Violation updated',
                    'changes' => $changes,
                ]);
            }

            // Check if escalation is needed after update
            if ($violation->status !== 'ESCALATED' && $this->shouldAutoEscalate($violation)) {
                $this->escalateToLegal($violation);
            }

            return $violation->fresh();
        });
    }

    /**
     * Check if violation should auto-escalate to Legal
     */
    public function shouldAutoEscalate(VisitorViolation $violation): bool
    {
        // Already escalated
        if ($violation->status === 'ESCALATED' || $violation->legal_case_ref) {
            return false;
        }

        // Check severity
        if ($violation->severity === 'HIGH' || $violation->severity === 'CRITICAL') {
            return true;
        }

        // Check critical types
        if (in_array($violation->violation_type, self::CRITICAL_TYPES)) {
            return true;
        }

        // Check property damage threshold
        if ($violation->violation_type === 'PROPERTY_DAMAGE' && 
            $violation->estimated_cost && 
            $violation->estimated_cost >= self::PROPERTY_DAMAGE_THRESHOLD) {
            return true;
        }

        return false;
    }

    /**
     * Escalate violation to Legal Case
     */
    public function escalateToLegal(VisitorViolation $violation, ?string $actorId = null, ?string $actorName = null): LegalCase
    {
        return DB::transaction(function () use ($violation, $actorId, $actorName) {
            // Create Legal Case
            $legalCase = LegalCase::create([
                'case_title' => "Visitor Violation: {$violation->violation_type} - {$violation->violation_ref}",
                'case_description' => $violation->description,
                'case_type' => 'visitor_violation',
                'priority' => $this->mapSeverityToPriority($violation->severity),
                'status' => 'pending',
                'created_by' => $actorId ?? auth()->id(),
                'source_module' => 'visitor',
                'source_id' => $violation->id,
                'source_ref' => $violation->violation_ref,
                'incident_date' => $violation->occurred_at,
                'metadata' => [
                    'violation_type' => $violation->violation_type,
                    'severity' => $violation->severity,
                    'visitor_id' => $violation->visitor_ref,
                    'visit_id' => $violation->visit_ref,
                ],
            ]);

            // Link violation to legal case
            $violation->escalateToLegal($legalCase, $actorId, $actorName);

            // Log escalation
            Log::info('Visitor violation escalated to Legal Case', [
                'violation_ref' => $violation->violation_ref,
                'legal_case_id' => $legalCase->id,
                'case_number' => $legalCase->case_number,
            ]);

            // TODO: Send notification to Legal role
            // $this->notifyLegalTeam($legalCase, $violation);

            return $legalCase;
        });
    }

    /**
     * Map violation severity to legal case priority
     */
    protected function mapSeverityToPriority(string $severity): string
    {
        return match($severity) {
            'CRITICAL' => 'urgent',
            'HIGH' => 'high',
            'MEDIUM' => 'medium',
            'LOW' => 'low',
            default => 'medium',
        };
    }

    /**
     * Get escalation rules configuration (for admin UI)
     */
    public function getEscalationRules(): array
    {
        return [
            'critical_types' => self::CRITICAL_TYPES,
            'severity_threshold' => ['HIGH', 'CRITICAL'],
            'property_damage_threshold' => self::PROPERTY_DAMAGE_THRESHOLD,
        ];
    }

    /**
     * Check and create violations from auto-rules
     */
    public function checkAutoRules(): void
    {
        // Overstay detection
        $this->checkOverstayViolations();

        // Unreturned pass detection
        $this->checkUnreturnedPassViolations();
    }

    /**
     * Check for overstay violations
     */
    protected function checkOverstayViolations(): void
    {
        // Visitors who are checked in but past their expected time out
        $visitors = \App\Models\Visitor::whereNotNull('time_in')
            ->whereNull('time_out')
            ->whereNotNull('expected_time_out')
            ->where('status', 'checked_in')
            ->where('expected_time_out', '<', now()->subHours(1)) // Grace period: 1 hour
            ->get();

        foreach ($visitors as $visitor) {
            // Check if violation already exists for this visit
            $existing = VisitorViolation::where('visit_ref', $visitor->id)
                ->where('violation_type', 'OVERSTAY')
                ->where('status', '!=', 'CLOSED')
                ->first();

            if (!$existing) {
                $this->createViolation([
                    'visit_ref' => $visitor->id,
                    'visitor_ref' => $visitor->id,
                    'violation_type' => 'OVERSTAY',
                    'severity' => 'MEDIUM',
                    'description' => "Visitor has overstayed beyond expected time out ({$visitor->expected_time_out})",
                    'occurred_at' => $visitor->expected_time_out,
                    'status' => 'OPEN',
                    'reported_by' => 'system',
                    'reported_by_name' => 'Auto-Rule System',
                    'metadata' => [
                        'auto_rule' => 'overstay_detection',
                        'expected_time_out' => $visitor->expected_time_out,
                        'current_time' => now(),
                    ],
                ]);
            }
        }
    }

    /**
     * Check for unreturned pass violations
     */
    protected function checkUnreturnedPassViolations(): void
    {
        // Visitors who are checked out but pass not returned
        $visitors = \App\Models\Visitor::whereNotNull('time_out')
            ->where('status', 'checked_out')
            ->whereNotNull('pass_id')
            ->whereHas('qrPass', function ($query) {
                $query->where('returned', false);
            })
            ->get();

        foreach ($visitors as $visitor) {
            // Check if violation already exists
            $existing = VisitorViolation::where('visit_ref', $visitor->id)
                ->where('violation_type', 'UNRETURNED_ASSET')
                ->where('status', '!=', 'CLOSED')
                ->first();

            if (!$existing) {
                $this->createViolation([
                    'visit_ref' => $visitor->id,
                    'visitor_ref' => $visitor->id,
                    'violation_type' => 'UNRETURNED_ASSET',
                    'severity' => 'LOW',
                    'description' => "Visitor pass not returned after checkout",
                    'occurred_at' => $visitor->time_out ?? now(),
                    'status' => 'OPEN',
                    'reported_by' => 'system',
                    'reported_by_name' => 'Auto-Rule System',
                    'metadata' => [
                        'auto_rule' => 'unreturned_pass_detection',
                        'pass_id' => $visitor->pass_id,
                        'checkout_time' => $visitor->time_out,
                    ],
                ]);
            }
        }
    }
}

