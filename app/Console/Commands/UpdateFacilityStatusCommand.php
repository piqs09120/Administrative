<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Facility;
use App\Models\FacilityReservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateFacilityStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facility:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update facility statuses based on active reservations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Running facility:update-status command.');
        $this->info('Updating facility statuses...');

        try {
            $now = Carbon::now();
            Log::info('Current time for status update', ['now' => $now->toDateTimeString()]);

            // Get all facilities
            $facilities = Facility::all();

            foreach ($facilities as $facility) {
                Log::info("Processing facility {$facility->name} (ID: {$facility->id})");

                // Check for an active reservation
                $activeReservation = FacilityReservation::where('facility_id', $facility->id)
                    ->where('status', 'approved')
                    ->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now)
                    ->first(); // Use first() to get the actual reservation object

                if ($activeReservation) {
                    Log::info("Active reservation found for {$facility->name}", [
                        'reservation_id' => $activeReservation->id,
                        'start_time' => $activeReservation->start_time->toDateTimeString(),
                        'end_time' => $activeReservation->end_time->toDateTimeString()
                    ]);
                    if ($facility->status !== 'occupied') {
                        $facility->status = 'occupied';
                        $facility->save();
                        $this->line("Facility '{$facility->name}' is now occupied.");
                        Log::info("Facility '{$facility->name}' is now occupied.");
                    }
                } else {
                    Log::info("No active reservation found for {$facility->name}. Current status: {$facility->status}");
                    if ($facility->status !== 'available') {
                        $facility->status = 'available';
                        $facility->save();
                        $this->line("Facility '{$facility->name}' is now available.");
                        Log::info("Facility '{$facility->name}' is now available.");
                    }
                }
            }

            $this->info('Facility statuses updated successfully.');
            Log::info('Facility statuses updated successfully.');
        } catch (\Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
            Log::error("Error in facility:update-status: " . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}
