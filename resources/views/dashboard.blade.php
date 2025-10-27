@extends('layouts.app')

@section('content')
@php
  $todayDate = now()->toDateString();
  $visitorsToday = \App\Models\Visitor::whereDate('time_in', $todayDate)->count();
  $visitorsCheckedIn = \App\Models\Visitor::whereNull('time_out')->count();
  $visitorsThisWeek = \App\Models\Visitor::whereBetween('time_in', [now()->startOfWeek(), now()->endOfWeek()])->count();
  $visitorsThisMonth = \App\Models\Visitor::whereMonth('time_in', now()->month)->whereYear('time_in', now()->year)->count();
  $pendingReservations = \App\Models\FacilityRequest::where('status', 'pending')->count();
  $upcomingReservations = \App\Models\FacilityRequest::where('status', 'approved')->where('requested_datetime', '>', now())->count();
  $pendingLegalDocs = \App\Models\Document::where('status', 'pending_review')->count();
  $legalCasesTotal = \App\Models\LegalCase::count();
  $legalCasesPending = \App\Models\LegalCase::where('status', 'pending')->count();
  $totalUsers = \App\Models\User::count();
  $activeFacilities = \App\Models\Facility::where('status', 'active')->count();
  $expiringDocuments = \App\Models\Document::whereNotNull('retention_until')->where('retention_until', '<=', now()->addDays(30))->where('retention_until', '>=', now())->count();
@endphp

<style>
  .metric-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
  }
  .metric-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
  }
</style>

<div class="p-6">
  <!-- Section Header -->
  <div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
  </div>

  <!-- Metric Cards -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Visitors Today -->
    <div class="metric-card">
      <div class="flex items-center justify-between">
        <!-- Left: Content -->
        <div class="flex-1">
          <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">VISITORS TODAY</p>
          <p class="text-3xl font-bold text-gray-900 mb-1">{{ $visitorsToday }}</p>
          <div class="flex items-center gap-1 text-green-600 text-xs">
            <span>↑ 0.0% vs last week</span>
          </div>
        </div>
        <!-- Right: Icon Box -->
        <div class="w-20 h-20 bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-4">
          <i data-lucide="users" class="w-7 h-7 text-yellow-400"></i>
        </div>
      </div>
    </div>

    <!-- Pending Reservations -->
    <div class="metric-card">
      <div class="flex items-center justify-between">
        <!-- Left: Content -->
        <div class="flex-1">
          <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">PENDING RESERVATIONS</p>
          <p class="text-3xl font-bold text-gray-900 mb-1">{{ $pendingReservations }}</p>
          <div class="flex items-center gap-1 text-blue-600 text-xs">
            <span>↑ 0.0% vs last month</span>
          </div>
        </div>
        <!-- Right: Icon Box -->
        <div class="w-20 h-20 bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-4">
          <i data-lucide="calendar-clock" class="w-7 h-7 text-yellow-400"></i>
        </div>
      </div>
    </div>

    <!-- Legal Cases -->
    <div class="metric-card">
      <div class="flex items-center justify-between">
        <!-- Left: Content -->
        <div class="flex-1">
          <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">LEGAL CASES</p>
          <p class="text-3xl font-bold text-gray-900 mb-1">{{ $legalCasesTotal }}</p>
          <div class="flex items-center gap-1 text-orange-600 text-xs">
            <span>{{ $legalCasesPending }} pending</span>
          </div>
        </div>
        <!-- Right: Icon Box -->
        <div class="w-20 h-20 bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-4">
          <i data-lucide="scale" class="w-7 h-7 text-yellow-400"></i>
        </div>
      </div>
    </div>

    <!-- Documents -->
    <div class="metric-card">
      <div class="flex items-center justify-between">
        <!-- Left: Content -->
        <div class="flex-1">
          <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">DOCUMENTS</p>
          <p class="text-3xl font-bold text-gray-900 mb-1">{{ $pendingLegalDocs }}</p>
          <div class="flex items-center gap-1 text-red-600 text-xs">
            <span>{{ $expiringDocuments }} expiring</span>
          </div>
        </div>
        <!-- Right: Icon Box -->
        <div class="w-20 h-20 bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-4">
          <i data-lucide="file-text" class="w-7 h-7 text-yellow-400"></i>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
  lucide.createIcons();
</script>
@endsection
