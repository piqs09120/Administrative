<?php

namespace App\Services;

use App\Models\Document;
use App\Models\FacilityReservation;
use App\Models\LegalAuditLog;
use App\Models\VisitorCheckinLog;
use Illuminate\Support\Collection;

class DashboardActivityService
{
    /**
     * Gather recent activity items across core modules for the dashboard feed.
     */
    public function recent(int $limit = 10): Collection
    {
        $visitorActivities = VisitorCheckinLog::with(['visitor', 'checkedInBy'])
            ->latest('action_time')
            ->limit($limit)
            ->get()
            ->map(function (VisitorCheckinLog $log) {
                $visitorName = $log->visitor?->name ?? 'Visitor #' . $log->visitor_id;
                $actionLabel = match ($log->action) {
                    'checkin' => 'checked in',
                    'checkout' => 'checked out',
                    'register' => 'registered',
                    default => $log->action,
                };

                return [
                    'module' => 'Visitor Management',
                    'title' => "{$visitorName} {$actionLabel}",
                    'description' => $log->notes ?? ($log->visitor?->host_employee ? 'Host: ' . $log->visitor->host_employee : 'Managed via kiosk'),
                    'actor' => $log->checkedInBy?->name ?? 'System',
                    'icon' => match ($log->action) {
                        'checkin' => 'user-plus',
                        'checkout' => 'user-minus',
                        'register' => 'user-check',
                        default => 'user',
                    },
                    'icon_color' => match ($log->action) {
                        'checkin' => 'bg-emerald-100 text-emerald-600',
                        'checkout' => 'bg-rose-100 text-rose-600',
                        'register' => 'bg-sky-100 text-sky-600',
                        default => 'bg-slate-100 text-slate-600',
                    },
                    'timestamp' => $log->action_time,
                    'url' => route('visitor.index'),
                ];
            });

        $legalActivities = LegalAuditLog::latest('timestamp')
            ->limit($limit)
            ->get()
            ->map(function (LegalAuditLog $log) {
                return [
                    'module' => 'Legal Management',
                    'title' => $log->description ?? ucfirst(str_replace('_', ' ', $log->action_type)),
                    'description' => $log->metadata['violation_type'] ?? $log->metadata['complaint_type'] ?? $log->metadata['document_title'] ?? 'Legal workflow update',
                    'actor' => $log->user_name ?? 'System',
                    'icon' => match ($log->action_type) {
                        'violation_reported' => 'alert-triangle',
                        'complaint_filed' => 'file-warning',
                        'ai_analysis', 'ai_violation_detection', 'ai_compliance_check' => 'cpu',
                        'document_upload' => 'file-text',
                        default => 'gavel',
                    },
                    'icon_color' => match ($log->action_type) {
                        'violation_reported' => 'bg-amber-100 text-amber-600',
                        'complaint_filed' => 'bg-orange-100 text-orange-600',
                        'ai_analysis', 'ai_violation_detection', 'ai_compliance_check' => 'bg-purple-100 text-purple-600',
                        'document_upload' => 'bg-blue-100 text-blue-600',
                        default => 'bg-indigo-100 text-indigo-600',
                    },
                    'timestamp' => $log->timestamp,
                    'url' => route('legal.legal_cases'),
                ];
            });

        $reservationActivities = FacilityReservation::with(['facility', 'reserver'])
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(function (FacilityReservation $reservation) {
                $facilityName = $reservation->facility?->name ?? 'Facility #' . $reservation->facility_id;
                $statusLabel = ucfirst($reservation->status ?? 'updated');

                return [
                    'module' => 'Facilities Reservations',
                    'title' => "{$facilityName} {$statusLabel}",
                    'description' => $reservation->purpose
                        ? "{$reservation->purpose} • " . optional($reservation->start_time)->format('M d, g:i A')
                        : 'Reservation workflow update',
                    'actor' => $reservation->reserver?->name ?? 'System',
                    'icon' => 'calendar-check',
                    'icon_color' => 'bg-amber-100 text-amber-600',
                    'timestamp' => $reservation->updated_at,
                    'url' => route('facility_reservations.index'),
                ];
            });

        $documentActivities = Document::with('uploader')
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(function (Document $document) {
                return [
                    'module' => 'Document Management',
                    'title' => $document->title ?? 'Untitled document',
                    'description' => 'Status: ' . ucfirst($document->status ?? 'updated'),
                    'actor' => $document->uploader_name ?? 'System',
                    'icon' => 'file-text',
                    'icon_color' => 'bg-sky-100 text-sky-600',
                    'timestamp' => $document->updated_at,
                    'url' => route('document.index'),
                ];
            });

        return collect()
            ->merge($visitorActivities)
            ->merge($legalActivities)
            ->merge($reservationActivities)
            ->merge($documentActivities)
            ->filter(fn ($item) => !empty($item['timestamp']))
            ->sortByDesc('timestamp')
            ->take($limit)
            ->values()
            ->map(function (array $item) {
                $timestamp = $item['timestamp'];
                $item['time_ago'] = $timestamp?->diffForHumans() ?? '';
                $item['timestamp_iso'] = $timestamp?->toIso8601String();
                $item['timestamp_label'] = $timestamp?->format('M d, Y g:i A') ?? '';

                unset($item['timestamp']);

                return $item;
            });
    }
}


