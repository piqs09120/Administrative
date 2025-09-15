<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Visitor;
use App\Models\User;
use App\Notifications\VisitorCheckoutReminder;
use App\Notifications\AdminCheckoutAlert;
use App\Notifications\VisitorTimeoutSystemAlert;
use App\Notifications\VisitorExceededTimeAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckVisitorCheckoutTimes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $now = Carbon::now();
        
        // Get active visitors who haven't checked out yet
        $activeVisitors = Visitor::where('time_in', '!=', null)
            ->where('time_out', null)
            ->where('expected_time_out', '!=', null)
            ->with('facility')
            ->get();

        foreach ($activeVisitors as $visitor) {
            try {
                // Parse expected checkout time
                $expectedCheckout = $this->parseExpectedCheckoutTime($visitor);
                
                if (!$expectedCheckout) {
                    Log::warning("Could not parse checkout time for visitor: {$visitor->name} (ID: {$visitor->id})");
                    continue;
                }
                
                // Calculate minutes remaining (positive = future, negative = past)
                $minutesRemaining = $now->diffInMinutes($expectedCheckout, false);
                
                // Debug logging
                Log::info("Processing visitor: {$visitor->name}, minutes remaining: {$minutesRemaining}");
                
                // 10 minutes before timeout - Send email to visitor
                if ($minutesRemaining <= 10 && $minutesRemaining > 5) {
                    $this->sendVisitorEmailReminder($visitor, $minutesRemaining);
                }
                
                // 5 minutes before timeout - System alert to administrators
                if ($minutesRemaining <= 5 && $minutesRemaining > 0) {
                    $this->sendSystemAlert($visitor, $minutesRemaining);
                }
                
                // When exceeded time - Mark as pending exit and send critical alert
                if ($minutesRemaining < 0) {
                    $this->handleExceededTime($visitor, abs($minutesRemaining));
                }
                
            } catch (\Exception $e) {
                Log::error("Error processing checkout time for visitor {$visitor->id}: " . $e->getMessage());
            }
        }
    }
    
    private function parseExpectedCheckoutTime($visitor)
    {
        try {
            // If expected_time_out is already a full datetime, use it directly
            if (strpos($visitor->expected_time_out, ' ') !== false) {
                return Carbon::parse($visitor->expected_time_out);
            } else {
                // Just time, combine with date
                return Carbon::parse($visitor->expected_date_out . ' ' . $visitor->expected_time_out);
            }
        } catch (\Exception $e) {
            Log::error("Error parsing checkout time for visitor {$visitor->id}: " . $e->getMessage());
            return null;
        }
    }
    
    private function sendVisitorEmailReminder($visitor, $minutesRemaining)
    {
        $notificationKey = "visitor_email_10min_{$visitor->id}";
        if (!$this->hasNotificationBeenSent($visitor, $notificationKey)) {
            // Send email to visitor
            $visitor->notify(new VisitorCheckoutReminder($visitor, $minutesRemaining));
            $this->markNotificationSent($visitor, $notificationKey);
            
            Log::info("Sent 10-minute email reminder to visitor: {$visitor->name}");
        }
    }
    
    private function sendSystemAlert($visitor, $minutesRemaining)
    {
        $notificationKey = "system_alert_5min_{$visitor->id}";
        if (!$this->hasNotificationBeenSent($visitor, $notificationKey)) {
            // Send system alert to all administrators
            $administrators = User::where('role', 'administrator')->get();
            foreach ($administrators as $admin) {
                $admin->notify(new VisitorTimeoutSystemAlert($visitor, $minutesRemaining));
            }
            $this->markNotificationSent($visitor, $notificationKey);
            
            Log::warning("Sent 5-minute system alert for visitor: {$visitor->name}");
        }
    }
    
    private function handleExceededTime($visitor, $minutesOverdue)
    {
        // Flag as pending exit if not already flagged
        if (!$visitor->pending_exit) {
            $visitor->update([
                'pending_exit' => true,
                'pending_exit_at' => now()
            ]);
            
            Log::warning("Flagged visitor as pending exit: {$visitor->name} (ID: {$visitor->id})");
        }
        
        // Send critical alert to administrators (only once per day)
        $notificationKey = "exceeded_time_alert_{$visitor->id}_" . now()->format('Y-m-d');
        if (!$this->hasNotificationBeenSent($visitor, $notificationKey)) {
            $administrators = User::where('role', 'administrator')->get();
            foreach ($administrators as $admin) {
                $admin->notify(new VisitorExceededTimeAlert($visitor, $minutesOverdue));
            }
            $this->markNotificationSent($visitor, $notificationKey);
            
            Log::error("Sent exceeded time alert for visitor: {$visitor->name} (overdue by {$minutesOverdue} minutes)");
        }
    }
    
    private function hasNotificationBeenSent($visitor, $notificationKey)
    {
        // Check if notification metadata exists
        $metadata = $visitor->metadata ?? [];
        return isset($metadata['notifications'][$notificationKey]);
    }
    
    private function markNotificationSent($visitor, $notificationKey)
    {
        $metadata = $visitor->metadata ?? [];
        $metadata['notifications'][$notificationKey] = now()->toISOString();
        $visitor->update(['metadata' => $metadata]);
    }
}
