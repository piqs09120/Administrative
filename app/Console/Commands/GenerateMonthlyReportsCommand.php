<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FacilityReservation;
use App\Models\Facility;
use App\Exports\MonthlyFacilityReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate-monthly 
                            {--month= : Specific month (1-12)}
                            {--year= : Specific year}
                            {--email= : Email address to send reports to}
                            {--all-facilities : Generate reports for all facilities}
                            {--facility= : Specific facility ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly facility usage reports and optionally email them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting monthly report generation...');

        try {
            // Determine the month and year
            $month = $this->option('month') ?: now()->subMonth()->month;
            $year = $this->option('year') ?: now()->subMonth()->year;
            $email = $this->option('email');
            $allFacilities = $this->option('all-facilities');
            $facilityId = $this->option('facility');

            $monthName = Carbon::createFromDate($year, $month)->format('F');
            $this->info("Generating reports for {$monthName} {$year}");

            $reportsGenerated = [];

            if ($allFacilities) {
                // Generate reports for all facilities
                $facilities = Facility::where('status', 'available')->get();
                
                foreach ($facilities as $facility) {
                    $this->info("Generating report for facility: {$facility->name}");
                    $reportPath = $this->generateReportForFacility($facility->id, $month, $year, $facility->name);
                    if ($reportPath) {
                        $reportsGenerated[] = [
                            'facility_name' => $facility->name,
                            'path' => $reportPath
                        ];
                    }
                }

                // Also generate a combined report for all facilities
                $this->info("Generating combined report for all facilities");
                $reportPath = $this->generateReportForFacility(null, $month, $year, 'All Facilities');
                if ($reportPath) {
                    $reportsGenerated[] = [
                        'facility_name' => 'All Facilities',
                        'path' => $reportPath
                    ];
                }
            } elseif ($facilityId) {
                // Generate report for specific facility
                $facility = Facility::find($facilityId);
                if (!$facility) {
                    $this->error("Facility with ID {$facilityId} not found");
                    return 1;
                }

                $this->info("Generating report for facility: {$facility->name}");
                $reportPath = $this->generateReportForFacility($facilityId, $month, $year, $facility->name);
                if ($reportPath) {
                    $reportsGenerated[] = [
                        'facility_name' => $facility->name,
                        'path' => $reportPath
                    ];
                }
            } else {
                // Generate general report
                $this->info("Generating general monthly report");
                $reportPath = $this->generateReportForFacility(null, $month, $year, 'General');
                if ($reportPath) {
                    $reportsGenerated[] = [
                        'facility_name' => 'General',
                        'path' => $reportPath
                    ];
                }
            }

            if (empty($reportsGenerated)) {
                $this->error("No reports were generated");
                return 1;
            }

            $this->info("Successfully generated " . count($reportsGenerated) . " report(s)");

            // Email reports if email is provided
            if ($email) {
                $this->info("Sending reports to: {$email}");
                $this->emailReports($reportsGenerated, $email, $monthName, $year);
            }

            // Log the report generation
            Log::info('Monthly reports generated', [
                'month' => $month,
                'year' => $year,
                'reports_count' => count($reportsGenerated),
                'email_sent' => !empty($email),
                'email_address' => $email
            ]);

            $this->info('Monthly report generation completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error("Error generating reports: " . $e->getMessage());
            Log::error('Monthly report generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Generate report for a specific facility
     */
    private function generateReportForFacility($facilityId, $month, $year, $facilityName)
    {
        try {
            // Check if there are any reservations for this period
            $query = FacilityReservation::whereMonth('start_time', $month)
                ->whereYear('start_time', $year);

            if ($facilityId) {
                $query->where('facility_id', $facilityId);
            }

            $reservationCount = $query->count();

            if ($reservationCount === 0) {
                $this->warn("No reservations found for {$facilityName} in {$month}/{$year}");
                return null;
            }

            // Generate the Excel file
            $monthName = Carbon::createFromDate($year, $month)->format('F');
            $filename = "facility_usage_report_{$monthName}_{$year}" . 
                       ($facilityId ? "_facility_{$facilityId}" : '') . ".xlsx";
            
            $filePath = "reports/monthly/{$year}/{$month}/{$filename}";
            
            // Ensure directory exists
            Storage::disk('public')->makeDirectory(dirname($filePath));

            // Generate and store the report
            Excel::store(
                new MonthlyFacilityReportExport($month, $year, $facilityId),
                $filePath,
                'public'
            );

            $this->info("Report saved: {$filePath}");

            return $filePath;

        } catch (\Exception $e) {
            $this->error("Error generating report for {$facilityName}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Email the generated reports
     */
    private function emailReports($reports, $email, $monthName, $year)
    {
        try {
            $data = [
                'reports' => $reports,
                'monthName' => $monthName,
                'year' => $year,
                'generated_at' => now()->format('F j, Y \a\t g:i A')
            ];

            Mail::send('emails.monthly_reports', $data, function ($message) use ($email, $monthName, $year, $reports) {
                $message->to($email)
                    ->subject("Monthly Facility Usage Reports - {$monthName} {$year}")
                    ->from(config('mail.from.address'), config('mail.from.name'));

                // Attach each report file
                foreach ($reports as $report) {
                    $filePath = storage_path('app/public/' . $report['path']);
                    if (file_exists($filePath)) {
                        $message->attach($filePath, [
                            'as' => basename($report['path']),
                            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ]);
                    }
                }
            });

            $this->info("Reports sent successfully to {$email}");

        } catch (\Exception $e) {
            $this->error("Error sending email: " . $e->getMessage());
            Log::error('Failed to email monthly reports', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
        }
    }
}
