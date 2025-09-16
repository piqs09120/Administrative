<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find and fix duplicate case numbers
        $duplicates = \DB::table('legal_cases')
            ->select('case_number', \DB::raw('COUNT(*) as count'))
            ->groupBy('case_number')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            // Get all cases with this duplicate case number, ordered by created_at
            $cases = \DB::table('legal_cases')
                ->where('case_number', $duplicate->case_number)
                ->orderBy('created_at', 'asc')
                ->get();

            // Keep the first case, update the rest with new case numbers
            $keepFirst = true;
            foreach ($cases as $index => $case) {
                if ($keepFirst) {
                    $keepFirst = false;
                    continue; // Keep the first case as is
                }

                // Generate a new unique case number for this duplicate
                $year = date('Y', strtotime($case->created_at));
                $newCaseNumber = \App\Models\LegalCase::generateCaseNumber();

                // Update the case with the new case number
                \DB::table('legal_cases')
                    ->where('id', $case->id)
                    ->update(['case_number' => $newCaseNumber]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
