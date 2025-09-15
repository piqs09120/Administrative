<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Running checkout notification job...\n";

// Create and run the job
$job = new App\Jobs\CheckVisitorCheckoutTimes();
$job->handle();

echo "Job completed.\n";
