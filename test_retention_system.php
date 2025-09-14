<?php
/**
 * Test Script for Document Retention and Archival System
 * Run this script to test the retention policies and archival system
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

echo "Document Retention and Archival System Test\n";
echo "==========================================\n\n";

// Test 1: Create test documents with different retention periods
echo "1. Creating test documents with different retention periods...\n";
Artisan::call('documents:test-retention', ['--create-test-docs' => true]);
echo Artisan::output() . "\n";

// Test 2: Check document expiration monitoring
echo "2. Testing document expiration monitoring...\n";
Artisan::call('documents:monitor-expiration', ['--days' => 30]);
echo Artisan::output() . "\n";

// Test 3: Check expiration notifications
echo "3. Testing expiration notifications...\n";
Artisan::call('documents:check-expiration', ['--days' => 7]);
echo Artisan::output() . "\n";

// Test 4: Check 1-day expiration notifications
echo "4. Testing 1-day expiration notifications...\n";
Artisan::call('documents:check-expiration', ['--days' => 1]);
echo Artisan::output() . "\n";

echo "Retention system test completed!\n";
echo "Check the database to see the test documents and their status changes.\n";
