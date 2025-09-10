<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Visitor Logs & Analytics - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @vite(['resources/css/soliera.css'])
  <style>
    /* Responsive chart containers */
    .chart-container {
      position: relative;
      height: 250px;
      width: 100%;
    }
    
    @media (max-width: 1366px) {
      .chart-container {
        height: 200px;
      }
    }
    
    @media (max-width: 768px) {
      .chart-container {
        height: 180px;
      }
    }
    
    /* Ensure charts don't overflow */
    canvas {
      max-width: 100% !important;
      height: auto !important;
    }
    
    /* Duration badge styling - responsive and accessible */
    .duration-display, .live-duration {
      cursor: help;
      transition: all 0.2s ease;
      display: inline-flex;
      max-width: none;
      width: auto;
    }
    
    .duration-display:hover, .live-duration:hover {
      transform: scale(1.05);
    }
    
    /* Duration pill styling */
    .duration-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 9999px;
      padding: 0.125rem 0.625rem;
      font-size: 0.75rem;
      font-weight: 500;
      white-space: nowrap;
      font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
      font-variant-numeric: tabular-nums;
      border: 1px solid;
    }
    
    /* Duration pill color variants */
    .duration-pill--short {
      background-color: #f0fdf4;
      color: #166534;
      border-color: #bbf7d0;
    }
    
    .duration-pill--medium {
      background-color: #fffbeb;
      color: #92400e;
      border-color: #fed7aa;
    }
    
    .duration-pill--long {
      background-color: #fef2f2;
      color: #991b1b;
      border-color: #fecaca;
    }
    
    .duration-pill--error {
      background-color: #fef2f2;
      color: #991b1b;
      border-color: #fecaca;
    }
    
    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
      .duration-pill--short {
        background-color: #064e3b;
        color: #a7f3d0;
        border-color: #065f46;
      }
      
      .duration-pill--medium {
        background-color: #78350f;
        color: #fde68a;
        border-color: #92400e;
      }
      
      .duration-pill--long {
        background-color: #7f1d1d;
        color: #fecaca;
        border-color: #991b1b;
      }
      
      .duration-pill--error {
        background-color: #7f1d1d;
        color: #fecaca;
        border-color: #991b1b;
      }
    }
    
    /* Responsive sizing */
    .duration-pill {
      font-size: 0.625rem; /* 10px on mobile */
      padding: 0.125rem 0.5rem;
    }
    
    @media (min-width: 640px) {
      .duration-pill {
        font-size: 0.75rem; /* 12px on small screens */
        padding: 0.125rem 0.625rem;
      }
    }
    
    @media (min-width: 768px) {
      .duration-pill {
        font-size: 0.875rem; /* 14px on medium+ screens */
        padding: 0.125rem 0.75rem;
      }
    }
    
    /* Responsive text shortening for very small screens */
    @media (max-width: 480px) {
      .duration-pill {
        font-size: 0.625rem;
        padding: 0.125rem 0.375rem;
      }
    }
    
    /* Checkout time display styling */
    .checkout-time-display {
      cursor: help;
      transition: all 0.2s ease;
      font-weight: 500;
    }
    
    .checkout-time-display:hover {
      transform: scale(1.05);
      color: #3b82f6;
    }
    
    /* Still in badge styling */
    .badge-primary {
      background-color: #3b82f6 !important;
      color: white !important;
      font-weight: 500;
    }
    
    /* Table column sizing for duration */
    .duration-column {
      min-width: 90px;
      width: 110px;
      max-width: 160px;
    }
    
    /* Ensure duration cell doesn't truncate */
    .duration-cell {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      min-width: 0;
      overflow: visible;
    }
    
    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
      .duration-display, .live-duration {
        transition: none;
      }
      
      .duration-display:hover, .live-duration:hover {
        transform: none;
      }
    }

    /* Reports & Analytics Animations */
    .reports-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
    }

    .reports-card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .progress-bar {
      transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .progress-bar::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }

    .stat-number {
      transition: all 0.3s ease;
      display: inline-block;
    }

    .stat-number.updating {
      color: #3b82f6;
      transform: scale(1.1);
    }

    .department-item, .purpose-item {
      transition: all 0.3s ease;
      cursor: pointer;
      border-radius: 8px;
      padding: 8px;
    }

    .department-item:hover, .purpose-item:hover {
      background-color: #f8fafc;
      transform: translateX(4px);
    }

    .badge-pulse {
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
    }

    .chart-container {
      position: relative;
      overflow: hidden;
    }

    .chart-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
      animation: chartLoad 2s ease-in-out;
    }

    @keyframes chartLoad {
      0% { left: -100%; }
      100% { left: 100%; }
    }

    .summary-section {
      transition: all 0.3s ease;
    }

    .summary-section:hover {
      transform: translateY(-2px);
    }

    .highlight-item {
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .highlight-item:hover {
      background-color: rgba(34, 197, 94, 0.1);
      transform: translateX(4px);
    }

    .improvement-item {
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .improvement-item:hover {
      background-color: rgba(251, 146, 60, 0.1);
      transform: translateX(4px);
    }

    .recommendation-item {
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .recommendation-item:hover {
      background-color: rgba(59, 130, 246, 0.1);
      transform: translateX(4px);
    }
  </style>
</head>
<body class="bg-base-100">
  <div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    @include('partials.sidebarr')
    
    <!-- Main content -->
    <div class="flex flex-col flex-1 overflow-hidden">
      <!-- Header -->
      @include('partials.navbar')

      <!-- Main content area -->
      <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6">
        @if(session('success'))
          <div class="alert alert-success mb-6">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-error mb-6">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <span>{{ session('error') }}</span>
          </div>
        @endif

        <!-- Page Header -->
        <div class="mb-8">
          <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Visitor Logs & Analytics</h1>
            <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Comprehensive visitor tracking, analytics, and reporting</p>
          </div>

          <!-- Quick Stats Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Visitors Today -->
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-primary">
              <div class="card-body p-4">
                <div class="flex items-center justify-between mb-3">
                  <div class="avatar placeholder">
                    <div class="bg-primary text-primary-content rounded-full w-10 h-10">
                      <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                  </div>
                  <div class="badge badge-primary badge-outline text-xs">Today</div>
                </div>
                <div class="text-center">
                  <h2 class="card-title text-2xl sm:text-3xl font-bold text-primary justify-center mb-1" id="totalVisitorsToday">{{ $stats['today'] ?? 0 }}</h2>
                  <p class="text-sm text-base-content/70">Total Visitors</p>
                </div>
              </div>
            </div>

            <!-- Currently In Building -->
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-success">
              <div class="card-body p-4">
                <div class="flex items-center justify-between mb-3">
                  <div class="avatar placeholder">
                    <div class="bg-success text-success-content rounded-full w-10 h-10">
                      <i data-lucide="user-check" class="w-5 h-5"></i>
                    </div>
                  </div>
                  <div class="badge badge-success badge-outline text-xs">Active</div>
                </div>
                <div class="text-center">
                  <h2 class="card-title text-2xl sm:text-3xl font-bold text-success justify-center mb-1" id="currentlyInBuilding">{{ $stats['currently_in'] ?? 0 }}</h2>
                  <p class="text-sm text-base-content/70">In Building</p>
                </div>
              </div>
            </div>

            <!-- Average Visit Duration -->
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-warning">
              <div class="card-body p-4">
                <div class="flex items-center justify-between mb-3">
                  <div class="avatar placeholder">
                    <div class="bg-warning text-warning-content rounded-full w-10 h-10">
                      <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                  </div>
                  <div class="badge badge-warning badge-outline text-xs">Avg</div>
                </div>
                <div class="text-center">
                  <h2 class="card-title text-2xl sm:text-3xl font-bold text-warning justify-center mb-1" id="avgDuration">{{ $stats['avg_duration'] ?? '0m' }}</h2>
                  <p class="text-sm text-base-content/70">Average Duration</p>
                </div>
              </div>
            </div>

            <!-- Peak Hours -->
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-info">
              <div class="card-body p-4">
                <div class="flex items-center justify-between mb-3">
                  <div class="avatar placeholder">
                    <div class="bg-info text-info-content rounded-full w-10 h-10">
                      <i data-lucide="activity" class="w-5 h-5"></i>
                    </div>
                  </div>
                  <div class="badge badge-info badge-outline text-xs">Peak</div>
                </div>
                <div class="text-center">
                  <h2 class="card-title text-xl sm:text-2xl font-bold text-info justify-center mb-1" id="peakHours">{{ $stats['peak_hours'] ?? '—' }}</h2>
                  <p class="text-sm text-base-content/70">Busiest Hour</p>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Main Content Tabs -->
        <div class="bg-white rounded-xl shadow-lg">
          <!-- Tab Navigation -->
          <div class="border-b border-gray-200">
            <nav class="flex flex-wrap space-x-2 sm:space-x-8 px-4 sm:px-6" aria-label="Tabs">
              <button onclick="showTab('logs')" class="tab-btn active py-3 sm:py-4 px-1 border-b-2 border-blue-500 font-medium text-xs sm:text-sm text-blue-600" id="logs-tab">
                <i data-lucide="list" class="w-4 h-4 mr-1 sm:mr-2"></i>
                <span class="hidden sm:inline">Detailed Logs</span>
                <span class="sm:hidden">Logs</span>
              </button>
              <button onclick="showTab('reports')" class="tab-btn py-3 sm:py-4 px-1 border-b-2 border-transparent font-medium text-xs sm:text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" id="reports-tab">
                <i data-lucide="bar-chart-3" class="w-4 h-4 mr-1 sm:mr-2"></i>
                <span class="hidden sm:inline">Reports & Analytics</span>
                <span class="sm:hidden">Reports</span>
              </button>

            </nav>
          </div>

          <!-- Tab Content -->
          <div class="p-4 sm:p-6">
            <!-- Detailed Logs Tab -->
            <div id="logs-content" class="tab-content">
              <!-- Filters Bar -->
              <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-12 gap-3 md:gap-4 items-end">
                  <!-- From Date -->
                  <div class="col-span-12 md:col-span-6 xl:col-span-3 min-w-0">
                    <label for="logs-start-date" class="block text-xs font-medium text-slate-500 mb-1">From</label>
                    <div class="relative">
                      <i data-lucide="calendar" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                      <input type="date" id="logs-start-date" placeholder="mm/dd/yyyy" class="w-full h-10 md:h-11 text-sm px-3 pl-9 rounded-md border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                  </div>

                  <!-- To Date -->
                  <div class="col-span-12 md:col-span-6 xl:col-span-3 min-w-0">
                    <label for="logs-end-date" class="block text-xs font-medium text-slate-500 mb-1">To</label>
                    <div class="relative">
                      <i data-lucide="calendar" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                      <input type="date" id="logs-end-date" placeholder="mm/dd/yyyy" class="w-full h-10 md:h-11 text-sm px-3 pl-9 rounded-md border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                  </div>

                  <!-- Facility -->
                  <div class="col-span-12 md:col-span-6 xl:col-span-3 min-w-0 relative z-50">
                    <label for="facility-filter" class="block text-xs font-medium text-slate-500 mb-1">Facility</label>
                    <select id="facility-filter" class="w-full h-10 md:h-11 text-sm px-3 rounded-md border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 truncate" title="Select facility">
                      <option value="" title="All Facilities">All Facilities</option>
                      @foreach($facilities as $facility)
                        <option value="{{ $facility->id }}" title="{{ $facility->name }}">{{ $facility->name }}</option>
                      @endforeach
                    </select>
                  </div>


                </div>
              </div>

              <!-- Logs Table -->
              <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                  <thead>
                    <tr class="bg-gray-50">
                      <th class="text-left py-3 px-4 font-medium text-gray-700">Visitor Name</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Contact Number</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Purpose</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Facility</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Check In Date</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Check In Time</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Check Out Date</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Check Out Time</th>
                      <th class="text-right py-3 px-4 font-medium text-gray-700 duration-column">Duration</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">ID Number</th>
                    </tr>
                  </thead>
                  <tbody id="logs-table-body">
                    @forelse($visitors as $visitor)
                      <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4">
                          <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                              <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <div>
                              <div class="font-medium text-gray-900">{{ $visitor->name }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600">{{ $visitor->contact ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-center">
                          <span class="badge badge-outline badge-sm">{{ $visitor->purpose ?? 'N/A' }}</span>
                        </td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600">{{ $visitor->facility->name ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600">
                          @if($visitor->time_in)
                            {{ \Carbon\Carbon::parse($visitor->time_in)->format('M d, Y') }}
                          @else
                            N/A
                          @endif
                        </td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600">
                          @if($visitor->time_in)
                            {{ \Carbon\Carbon::parse($visitor->time_in)->format('h:i A') }}
                          @else
                            N/A
                          @endif
                        </td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600">
                          @if($visitor->time_out)
                            @php
                              $checkOut = \Carbon\Carbon::parse($visitor->time_out)->setTimezone('Asia/Manila');
                            @endphp
                            {{ $checkOut->format('M d, Y') }}
                          @else
                            —
                          @endif
                        </td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600">
                          @if($visitor->time_out)
                            @php
                              $checkOut = \Carbon\Carbon::parse($visitor->time_out)->setTimezone('Asia/Manila');
                            @endphp
                            <span class="checkout-time-display" 
                                  data-checkout="{{ $visitor->time_out }}"
                                  title="{{ $checkOut->format('Y-m-d H:i:s') }} (Server: {{ \Carbon\Carbon::parse($visitor->time_out)->format('Y-m-d H:i:s') }})">
                              {{ $checkOut->format('h:i A') }}
                            </span>
                          @else
                            <span class="badge badge-primary badge-sm" title="Visitor is still in the building">Still in</span>
                          @endif
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600 duration-cell">
                          @if($visitor->time_out)
                            @php
                              $checkIn = \Carbon\Carbon::parse($visitor->time_in);
                              $checkOut = \Carbon\Carbon::parse($visitor->time_out);
                              
                              // Check for data error (check out before check in)
                              if ($checkOut->lt($checkIn)) {
                                $durationMinutes = 0;
                                $isDataError = true;
                              } else {
                                $durationMinutes = $checkIn->diffInMinutes($checkOut);
                                $isDataError = false;
                              }
                              
                              // Calculate human-readable format
                              if ($isDataError) {
                                $displayText = '—';
                                $tooltipText = 'Data error: Check out before check in';
                                $colorClass = 'badge-error';
                              } else {
                                $days = floor($durationMinutes / (24 * 60));
                                $hours = floor(($durationMinutes % (24 * 60)) / 60);
                                $mins = $durationMinutes % 60;
                                
                                // Build compact display
                                $parts = [];
                                if ($days > 0) $parts[] = $days . 'd';
                                if ($hours > 0) $parts[] = $hours . 'h';
                                if ($mins > 0) $parts[] = $mins . 'm';
                                
                                $displayText = empty($parts) ? '0m' : implode(' ', $parts);
                                
                                // Build verbose tooltip
                                $tooltipParts = [];
                                if ($days > 0) $tooltipParts[] = $days . ' day' . ($days > 1 ? 's' : '');
                                if ($hours > 0) $tooltipParts[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
                                if ($mins > 0) $tooltipParts[] = $mins . ' minute' . ($mins > 1 ? 's' : '');
                                
                                $tooltipText = empty($tooltipParts) ? '0 minutes' : implode(', ', $tooltipParts);
                                
                                // Determine color class based on duration
                                if ($durationMinutes < 8 * 60) {
                                  $colorClass = 'badge-success'; // < 8h
                                } elseif ($durationMinutes < 72 * 60) {
                                  $colorClass = 'badge-warning'; // 8h-72h
                                } else {
                                  $colorClass = 'badge-error'; // > 72h
                                }
                              }
                            @endphp
                            <div class="duration-display" 
                                 data-duration-minutes="{{ $durationMinutes }}"
                                 data-tooltip="{{ $tooltipText }}"
                                 title="{{ $tooltipText }}"
                                 aria-label="{{ $tooltipText }}">
                              <span class="duration-pill duration-pill--{{ $durationMinutes < 480 ? 'short' : ($durationMinutes < 4320 ? 'medium' : 'long') }}">{{ $displayText }}</span>
                            </div>
                          @else
                            <div class="live-duration" 
                                 data-checkin="{{ $visitor->time_in }}" 
                                 data-visitor-id="{{ $visitor->id }}"
                                 data-duration-minutes="0"
                                 title="Still in building - duration being calculated"
                                 aria-label="Still in building - duration being calculated">
                              <span class="duration-pill duration-pill--error">Still in</span>
                            </div>
                          @endif
                        </td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600 font-mono">{{ $visitor->pass_id ?? 'N/A' }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="10" class="text-center py-12">
                          <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                              <i data-lucide="users" class="w-10 h-10 text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">No Visitor Logs Found</h3>
                            <p class="text-gray-500 text-sm">No visitor logs available for the selected criteria.</p>
                          </div>
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <div class="flex justify-center mt-6">
                <div class="btn-group">
                  <button class="btn btn-sm">Previous</button>
                  <button class="btn btn-sm btn-active">1</button>
                  <button class="btn btn-sm">2</button>
                  <button class="btn btn-sm">3</button>
                  <button class="btn btn-sm">Next</button>
                </div>
              </div>
            </div>



            <!-- Reports & Analytics Tab -->
            <div id="reports-content" class="tab-content hidden">
              <!-- Header with Export Button -->
              <div class="flex justify-between items-center mb-6">
                <div>
                  <h2 class="text-2xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Reports & Analytics</h2>
                  <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Comprehensive visitor management insights and statistics</p>
                  </div>
                <div class="flex items-center gap-3">
                  <div class="flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                    <select class="select select-bordered select-sm" id="analytics-time-range" onchange="refreshAnalytics()">
                      <option value="today">Today</option>
                      <option value="week">This Week</option>
                      <option value="month">This Month</option>
                      <option value="year">This Year</option>
                    </select>
                  </div>
                  <button onclick="refreshAnalytics()" class="btn btn-outline btn-sm">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-1"></i>
                    Refresh
                  </button>
                  <button onclick="exportReport()" class="btn btn-primary btn-sm">
                    <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                    Export Report
                  </button>
                </div>
              </div>

              <!-- Summary Statistics Row -->
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Visitors -->
                <div class="reports-card bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                      <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                  </div>
                    <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full badge-pulse">+12% from last week</span>
                  </div>
                  <h3 class="text-3xl font-bold text-gray-900 mb-1 stat-number">147</h3>
                  <p class="text-sm text-gray-600">Total Visitors</p>
                </div>

                <!-- Average Visit Duration -->
                <div class="reports-card bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                      <i data-lucide="clock" class="w-5 h-5 text-green-600"></i>
                  </div>
                    <span class="text-xs font-medium text-gray-500">Optimal for productivity</span>
                  </div>
                  <h3 class="text-3xl font-bold text-gray-900 mb-1 stat-number">2h 34m</h3>
                  <p class="text-sm text-gray-600">Avg. Visit Duration</p>
                </div>

                <!-- Peak Capacity -->
                <div class="reports-card bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                      <i data-lucide="bar-chart-3" class="w-5 h-5 text-orange-600"></i>
                  </div>
                    <span class="text-xs font-medium text-gray-500">Tuesday 3:00 PM</span>
                </div>
                  <h3 class="text-3xl font-bold text-gray-900 mb-1 stat-number">85%</h3>
                  <p class="text-sm text-gray-600">Peak Capacity</p>
              </div>

                <!-- Security Incidents -->
                <div class="reports-card bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                      <i data-lucide="shield-check" class="w-5 h-5 text-red-600"></i>
                </div>
                    <span class="text-xs font-medium text-green-600">All clear this week</span>
                </div>
                  <h3 class="text-3xl font-bold text-gray-900 mb-1 stat-number">0</h3>
                  <p class="text-sm text-gray-600">Security Incidents</p>
                </div>
              </div>

              <!-- Detailed Analytics Row -->
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Peak Visiting Hours -->
                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="clock" class="w-5 h-5 text-blue-600"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Peak Visiting Hours</h3>
                  </div>
                  <p class="text-sm text-gray-600 mb-4">Visitor traffic throughout the day</p>
                  <div id="peak-hours-content" class="space-y-3">
                    <!-- Dynamic content will be loaded here -->
                    <div class="text-center py-8 text-gray-500">
                      <i data-lucide="clock" class="w-8 h-8 mx-auto mb-2"></i>
                      <p>Loading peak hours data...</p>
                </div>
              </div>
            </div>

                <!-- Departments by visitor count -->
                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="building" class="w-5 h-5 text-blue-600"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Departments with most visitors</h3>
                  </div>
                  <p class="text-sm text-gray-600 mb-4">All departments ranked by visitor count</p>
                  <div id="hosts-departments-content" class="space-y-3">
                    <!-- Dynamic content will be loaded here -->
                    <div class="text-center py-8 text-gray-500">
                      <i data-lucide="building" class="w-8 h-8 mx-auto mb-2"></i>
                      <p>Loading departments data...</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Visit Purposes & Weekly Summary -->
              <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Visit Purposes -->
                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="target" class="w-5 h-5 text-blue-600"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Visit Purposes</h3>
                  </div>
                  <p class="text-sm text-gray-600 mb-4">Breakdown of visit reasons</p>
                  <div id="visitor-types-content" class="space-y-3">
                    <!-- Dynamic content will be loaded here -->
                    <div class="text-center py-8 text-gray-500">
                      <i data-lucide="target" class="w-8 h-8 mx-auto mb-2"></i>
                      <p>Loading visit purposes data...</p>
                    </div>
                  </div>
                </div>

                <!-- Weekly Activity Summary -->
                <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-blue-600"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Weekly Activity Summary</h3>
                  </div>
                  <p class="text-sm text-gray-600 mb-4">Key insights and recommendations for the past week</p>
                  
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Highlights -->
                    <div class="bg-green-50 rounded-lg p-4 summary-section">
                      <h4 class="text-sm font-semibold text-green-800 mb-3">Highlights</h4>
                      <ul class="space-y-2 text-sm text-green-700">
                        <li class="flex items-start gap-2 highlight-item">
                          <span class="text-green-500 mt-1">•</span>
                          <span>12% increase in visitor volume</span>
                        </li>
                        <li class="flex items-start gap-2 highlight-item">
                          <span class="text-green-500 mt-1">•</span>
                          <span>Zero security incidents reported</span>
                        </li>
                        <li class="flex items-start gap-2 highlight-item">
                          <span class="text-green-500 mt-1">•</span>
                          <span>100% badge return compliance</span>
                        </li>
                      </ul>
                    </div>

                    <!-- Areas for Improvement -->
                    <div class="bg-orange-50 rounded-lg p-4 summary-section">
                      <h4 class="text-sm font-semibold text-orange-800 mb-3">Areas for Improvement</h4>
                      <ul class="space-y-2 text-sm text-orange-700">
                        <li class="flex items-start gap-2 improvement-item">
                          <span class="text-orange-500 mt-1">•</span>
                          <span>15% visitors exceed expected duration</span>
                        </li>
                        <li class="flex items-start gap-2 improvement-item">
                          <span class="text-orange-500 mt-1">•</span>
                          <span>Peak hours causing capacity strain</span>
                        </li>
                        <li class="flex items-start gap-2 improvement-item">
                          <span class="text-orange-500 mt-1">•</span>
                          <span>Some hosts not promptly notified</span>
                        </li>
                      </ul>
                    </div>

                    <!-- Recommendations -->
                    <div class="bg-blue-50 rounded-lg p-4 summary-section">
                      <h4 class="text-sm font-semibold text-blue-800 mb-3">Recommendations</h4>
                      <ul class="space-y-2 text-sm text-blue-700">
                        <li class="flex items-start gap-2 recommendation-item">
                          <span class="text-blue-500 mt-1">•</span>
                          <span>Implement visit duration reminders</span>
                        </li>
                        <li class="flex items-start gap-2 recommendation-item">
                          <span class="text-blue-500 mt-1">•</span>
                          <span>Consider staggered appointment times</span>
                        </li>
                        <li class="flex items-start gap-2 recommendation-item">
                          <span class="text-blue-500 mt-1">•</span>
                          <span>Enhance host notification system</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Report Generation Section -->
              <div class="mt-8 bg-gray-50 rounded-xl p-6">
                <div class="flex items-center gap-2 mb-4">
                  <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                  <h3 class="text-lg font-semibold text-gray-800">Generate Custom Report</h3>
                </div>
                
                <form id="report-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Report Type:</label>
                      <select name="report_type" class="select select-bordered w-full">
                        <option value="daily">Daily Summary</option>
                        <option value="weekly">Weekly Summary</option>
                        <option value="monthly">Monthly Summary</option>
                        <option value="custom">Custom Report</option>
                      </select>
                    </div>
                    <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date:</label>
                    <input type="date" name="start_date" class="input input-bordered w-full" value="{{ now()->subDays(7)->format('Y-m-d') }}">
                    </div>
                    <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date:</label>
                    <input type="date" name="end_date" class="input input-bordered w-full" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Format:</label>
                      <select name="format" class="select select-bordered w-full">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                        <option value="csv">CSV</option>
                      </select>
                    </div>
                  <div class="md:col-span-2 lg:col-span-4 flex justify-end">
                    <button type="submit" class="btn btn-primary">
                      <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                      Generate Report
                    </button>
                  </div>
                  </form>
              </div>
            </div>


          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    // Global variables
    let currentTab = 'logs';
    let dailyTrendsChart = null;
    let visitorTypesChart = null;
    let peakHoursChart = null;

    // Tab functionality
    function showTab(tabName) {
      currentTab = tabName;
      
      // Hide all tab contents
      document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
      });
      
      // Remove active class from all tab buttons
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
      });
      
      // Show selected tab content
      document.getElementById(tabName + '-content').classList.remove('hidden');
      
      // Add active class to selected tab button
      const activeTab = document.getElementById(tabName + '-tab');
      activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
      activeTab.classList.remove('border-transparent', 'text-gray-500');
      
      // Load data for the selected tab
      loadTabData(tabName);
      
      // Initialize Reports & Analytics if switching to reports tab
      if (tabName === 'reports') {
        setTimeout(() => {
          loadAnalyticsData(); // Load data first
          initializeReportsAnalytics();
        }, 200);
      }
    }

    // Load data for specific tabs
    function loadTabData(tabName) {
      switch(tabName) {
        case 'logs':
          loadLogsData();
          break;
        case 'reports':
          loadReportsData();
          break;
      }
    }

    // Analytics functions
    function loadAnalyticsData() {
      // Always load analytics data - don't check if tab is visible
      console.log('Loading analytics data...');
      console.log('Route URL:', '{{ route("visitor.logs.analytics") }}');
      
      // Show loading state
      showAnalyticsLoading();
      
      // Load analytics data from backend
      fetch('{{ route("visitor.logs.analytics") }}?time_range=today', {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
        .then(response => {
          console.log('Analytics response status:', response.status);
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          console.log('Analytics data loaded:', data);
          console.log('Total visitors from API:', data.statistics?.total_visitors);
          // Create charts with real data only
          createDailyTrendsChart(data.daily_trends || []);
          createVisitorTypesChart(data.visitor_types || {});
          createHostsDepartmentsChart(data.hosts_departments || []);
          createPeakHoursChart(data.peak_hours || []);
          updateAnalyticsStats(data);
        })
        .catch(error => {
          console.error('Error loading analytics data:', error);
          showNotification('Error loading analytics data: ' + error.message, 'error');
          // Show empty state instead of static data
          showEmptyAnalyticsState();
        });
    }

    function showAnalyticsLoading() {
      // Show loading state for charts
      const chartContainers = document.querySelectorAll('.chart-container');
      chartContainers.forEach(container => {
        container.innerHTML = '<div class="flex items-center justify-center h-full"><div class="loading loading-spinner loading-md"></div></div>';
      });
    }

    function showEmptyAnalyticsState() {
      // Show empty state for analytics when data fails to load
      const statNumbers = document.querySelectorAll('#reports-content .stat-number');
      statNumbers.forEach((stat, index) => {
        stat.textContent = '0';
      });
      
      // Clear charts
      const chartContainers = document.querySelectorAll('.chart-container');
      chartContainers.forEach(container => {
        container.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500">No data available</div>';
      });
    }

    function refreshAnalytics() {
      // Get selected time range
      const timeRange = document.getElementById('analytics-time-range').value || 'today';
      
      // Show loading state
      showAnalyticsLoading();
      
      // Load fresh analytics data
      loadAnalyticsDataForRange(timeRange);
      
      showNotification('Analytics data refreshed!', 'success');
    }

    function createDailyTrendsChart(data = null) {
      const canvas = document.getElementById('dailyTrendsChart');
      if (!canvas) {
        console.error('Daily trends chart canvas not found');
        return;
      }
      
      const ctx = canvas.getContext('2d');
      
      if (dailyTrendsChart) {
        dailyTrendsChart.destroy();
      }
      
      // Use real data only - no fallback to static data
      let chartData = data || [];
      
      dailyTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: chartData.map(item => item.label || item.date),
          datasets: [{
            label: 'Visitors',
            data: chartData.map(item => item.count || 0),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          },
          layout: {
            padding: {
              top: 10,
              bottom: 10,
              left: 10,
              right: 10
            }
          }
        }
      });
    }

    // Update Visitor Types HTML content with real data
    function updateVisitorTypesHTML(data = null) {
      const container = document.getElementById('visitor-types-content');
      if (!container) return;
      
      if (!data || Object.keys(data).length === 0) {
        container.innerHTML = `
          <div class="text-center py-8 text-gray-500">
            <i data-lucide="building" class="w-8 h-8 mx-auto mb-2"></i>
            <p>No visitor types data available</p>
          </div>
        `;
        return;
      }
      
      // Calculate total visitors for percentage calculation
      const totalVisitors = Object.values(data).reduce((sum, count) => sum + count, 0);
      
      // Generate HTML for each visitor type
      const colors = ['bg-blue-500', 'bg-green-500', 'bg-orange-500', 'bg-purple-500', 'bg-red-500', 'bg-yellow-500', 'bg-indigo-500', 'bg-pink-500'];
      const textColors = ['text-blue-600', 'text-green-600', 'text-orange-600', 'text-purple-600', 'text-red-600', 'text-yellow-600', 'text-indigo-600', 'text-pink-600'];
      const bgColors = ['bg-blue-100', 'bg-green-100', 'bg-orange-100', 'bg-purple-100', 'bg-red-100', 'bg-yellow-100', 'bg-indigo-100', 'bg-pink-100'];
      
      const html = Object.entries(data).map(([purpose, count], index) => {
        const percentage = totalVisitors > 0 ? Math.round((count / totalVisitors) * 100) : 0;
        const colorClass = colors[index % colors.length];
        const textColorClass = textColors[index % textColors.length];
        const bgColorClass = bgColors[index % bgColors.length];
        
        return `
          <div class="flex items-center justify-between department-item">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 ${colorClass} rounded-full"></div>
              <span class="text-sm font-medium text-gray-700">${purpose}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">${count} visitor${count !== 1 ? 's' : ''}</span>
              <span class="text-xs font-medium ${textColorClass} ${bgColorClass} px-2 py-1 rounded-full">${percentage}%</span>
            </div>
          </div>
        `;
      }).join('');
      
      container.innerHTML = html;
      
      // Re-initialize Lucide icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

    // Update Hosts/Departments HTML content with real data
    function updateHostsDepartmentsHTML(data = null) {
      const container = document.getElementById('hosts-departments-content');
      if (!container) return;
      
      if (!data || data.length === 0) {
        container.innerHTML = `
          <div class="text-center py-8 text-gray-500">
            <i data-lucide="users" class="w-8 h-8 mx-auto mb-2"></i>
            <p>No hosts/departments data available</p>
          </div>
        `;
        return;
      }
      
      // Calculate total visitors for percentage calculation
      const totalVisitors = data.reduce((sum, item) => sum + item.count, 0);
      
      // Generate HTML for each host/department
      const colors = ['bg-blue-500', 'bg-green-500', 'bg-orange-500', 'bg-purple-500', 'bg-red-500', 'bg-yellow-500', 'bg-indigo-500', 'bg-pink-500'];
      const textColors = ['text-blue-600', 'text-green-600', 'text-orange-600', 'text-purple-600', 'text-red-600', 'text-yellow-600', 'text-indigo-600', 'text-pink-600'];
      const bgColors = ['bg-blue-100', 'bg-green-100', 'bg-orange-100', 'bg-purple-100', 'bg-red-100', 'bg-yellow-100', 'bg-indigo-100', 'bg-pink-100'];
      
      const html = data.map((item, index) => {
        const percentage = totalVisitors > 0 ? Math.round((item.count / totalVisitors) * 100) : 0;
        const colorClass = colors[index % colors.length];
        const textColorClass = textColors[index % textColors.length];
        const bgColorClass = bgColors[index % bgColors.length];
        const typeIcon = 'building';
        const typeLabel = 'Department';
        
        return `
          <div class="flex items-center justify-between department-item">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 ${colorClass} rounded-full"></div>
              <div class="flex items-center gap-1">
                <i data-lucide="${typeIcon}" class="w-3 h-3 text-gray-500"></i>
                <span class="text-sm font-medium text-gray-700">${item.name}</span>
                <span class="text-xs text-gray-500">(${typeLabel})</span>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">${item.count} visitor${item.count !== 1 ? 's' : ''}</span>
              <span class="text-xs font-medium ${textColorClass} ${bgColorClass} px-2 py-1 rounded-full">${percentage}%</span>
            </div>
          </div>
        `;
      }).join('');
      
      container.innerHTML = html;
      
      // Re-initialize Lucide icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

    function createHostsDepartmentsChart(data = null) {
      // Update the HTML content with real data
      updateHostsDepartmentsHTML(data);
      
      const canvas = document.getElementById('visitorTypesChart');
      if (!canvas) {
        console.error('Hosts/Departments chart canvas not found');
        return;
      }
      
      const ctx = canvas.getContext('2d');
      
      if (visitorTypesChart) {
        visitorTypesChart.destroy();
      }
      
      // Use real data only - no fallback to static data
      let chartData = data || [];
      
      const labels = chartData.map(item => `${item.name}`);
      const values = chartData.map(item => item.count);
      const colors = [
        'rgb(59, 130, 246)',
        'rgb(16, 185, 129)',
        'rgb(245, 158, 11)',
        'rgb(239, 68, 68)',
        'rgb(139, 92, 246)',
        'rgb(236, 72, 153)',
        'rgb(34, 197, 94)',
        'rgb(251, 146, 60)'
      ];
      
      visitorTypesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: values,
            backgroundColor: colors.slice(0, labels.length),
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 20,
                usePointStyle: true
              }
            }
          }
        }
      });
    }

    function createVisitorTypesChart(data = null) {
      // Update the HTML content with real data
      updateVisitorTypesHTML(data);
      
      const canvas = document.getElementById('visitorTypesChart');
      if (!canvas) {
        console.error('Visitor types chart canvas not found');
        return;
      }
      
      const ctx = canvas.getContext('2d');
      
      if (visitorTypesChart) {
        visitorTypesChart.destroy();
      }
      
      // Use real data only - no fallback to static data
      let chartData = data || {};
      
      const labels = Object.keys(chartData);
      const values = Object.values(chartData);
      const colors = [
        'rgb(59, 130, 246)',
        'rgb(16, 185, 129)',
        'rgb(245, 158, 11)',
        'rgb(239, 68, 68)',
        'rgb(139, 92, 246)',
        'rgb(236, 72, 153)',
        'rgb(34, 197, 94)',
        'rgb(251, 146, 60)'
      ];
      
      visitorTypesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: values,
            backgroundColor: colors.slice(0, labels.length)
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 10,
                usePointStyle: true
              }
            }
          },
          layout: {
            padding: {
              top: 10,
              bottom: 10,
              left: 10,
              right: 10
            }
          }
        }
      });
    }

    // Update Peak Hours HTML content with real data
    function updatePeakHoursHTML(data = null) {
      const container = document.getElementById('peak-hours-content');
      if (!container) return;
      
      if (!data || data.length === 0) {
        container.innerHTML = `
          <div class="text-center py-8 text-gray-500">
            <i data-lucide="clock" class="w-8 h-8 mx-auto mb-2"></i>
            <p>No peak hours data available</p>
          </div>
        `;
        return;
      }
      
      // Find the maximum count for percentage calculation
      const maxCount = Math.max(...data.map(item => item.count || 0));
      
      // Generate HTML for each hour
      const html = data.map(item => {
        const percentage = maxCount > 0 ? Math.round((item.count / maxCount) * 100) : 0;
        const hour = String(item.hour).padStart(2, '0');
        return `
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">${hour}:00</span>
            <div class="flex-1 mx-3">
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full progress-bar" style="width: ${percentage}%"></div>
              </div>
            </div>
            <span class="text-sm font-medium text-gray-700">${item.count || 0}</span>
          </div>
        `;
      }).join('');
      
      container.innerHTML = html;
      
      // Re-initialize Lucide icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

    function createPeakHoursChart(data = null) {
      // Update the HTML content with real data
      updatePeakHoursHTML(data);
      
      const canvas = document.getElementById('peakHoursChart');
      if (!canvas) {
        console.error('Peak hours chart canvas not found');
        return;
      }
      
      const ctx = canvas.getContext('2d');
      
      if (peakHoursChart) {
        peakHoursChart.destroy();
      }
      
      // Use real data only - no fallback to static data
      let chartData = data || [];
      
      peakHoursChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: chartData.map(item => `${item.hour}:00`),
          datasets: [{
            label: 'Visitors',
            data: chartData.map(item => item.count || 0),
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true
            },
            x: {
              ticks: {
                maxTicksLimit: 12
              }
            }
          }
        }
      });
    }

    function updateAnalyticsStats(data = null) {
      console.log('updateAnalyticsStats called with data:', data);
      // Update analytics statistics with real data
      if (data && data.statistics) {
        const stats = data.statistics;
        console.log('Updating stats with:', stats);
        
        // Update main statistics cards in Reports & Analytics section
        const statNumbers = document.querySelectorAll('#reports-content .stat-number');
        console.log('Found stat numbers elements:', statNumbers.length);
        if (statNumbers.length >= 4) {
          // Total Visitors
          statNumbers[0].textContent = stats.total_visitors || 0;
          console.log('Updated total visitors to:', stats.total_visitors || 0);
          // Avg. Visit Duration
          statNumbers[1].textContent = stats.average_duration || '0h';
          // Peak Capacity (calculate from current vs total)
          const peakCapacity = stats.total_visitors > 0 ? Math.round((stats.currently_in / stats.total_visitors) * 100) : 0;
          statNumbers[2].textContent = peakCapacity + '%';
          // Security Incidents (always 0 for now)
          statNumbers[3].textContent = '0';
        }
        
        // Update detailed analytics
        if (data.peak_hours && Array.isArray(data.peak_hours)) {
          // Find peak hour
          const peakHour = data.peak_hours.reduce((max, hour) => hour.count > max.count ? hour : max, {count: 0});
          if (peakHour.count > 0) {
            const nextHour = peakHour.hour + 1;
            const peakHoursText = `${peakHour.hour.toString().padStart(2, '0')}:00 - ${nextHour.toString().padStart(2, '0')}:00`;
            const peakHoursElement = document.getElementById('peakHoursDetail');
            if (peakHoursElement) {
              peakHoursElement.textContent = peakHoursText;
            }
          }
        }
        
        const mostVisitedElement = document.getElementById('mostVisitedFacility');
        if (mostVisitedElement) {
          mostVisitedElement.textContent = data.most_visited_facility || 'N/A';
        }
        
        const returnVisitorsElement = document.getElementById('returnVisitors');
        if (returnVisitorsElement) {
          returnVisitorsElement.textContent = (data.return_visitors || 0) + '%';
        }
      } else {
        // Show empty state for missing data
        const statNumbers = document.querySelectorAll('#reports-content .stat-number');
        statNumbers.forEach((stat, index) => {
          stat.textContent = '0';
        });
      }
    }

    // Time range functions
    function setTimeRange(range) {
      // Remove active class from all time range buttons
      document.querySelectorAll('.time-range-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      
      // Add active class to clicked button
      event.target.classList.add('active');
      
      // Show/hide custom date range
      const customRange = document.getElementById('custom-date-range');
      if (range === 'custom') {
        customRange.classList.remove('hidden');
        customRange.classList.add('flex', 'flex-wrap');
      } else {
        customRange.classList.add('hidden');
        customRange.classList.remove('flex', 'flex-wrap');
        // Load data for the selected time range
        loadAnalyticsDataForRange(range);
      }
    }

    function loadAnalyticsDataForRange(timeRange) {
      // Always load analytics data - don't check if tab is visible
      console.log('Loading analytics data for range:', timeRange);
      
      // Show loading state
      showAnalyticsLoading();
      
      // Load analytics data from backend with time range
      fetch(`{{ route("visitor.logs.analytics") }}?time_range=${timeRange}`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
        .then(response => {
          console.log('Analytics response status for range:', response.status);
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          console.log('Analytics data loaded for range:', timeRange, data);
          createDailyTrendsChart(data.daily_trends || []);
          createVisitorTypesChart(data.visitor_types || {});
          createHostsDepartmentsChart(data.hosts_departments || []);
          createPeakHoursChart(data.peak_hours || []);
          updateAnalyticsStats(data);
        })
        .catch(error => {
          console.error('Error loading analytics data:', error);
          showNotification('Error loading analytics data: ' + error.message, 'error');
          // Show empty state instead of static data
          showEmptyAnalyticsState();
        });
    }

    function applyCustomRange() {
      // Always load analytics data - don't check if tab is visible
      console.log('Applying custom range...');
      
      const startDate = document.getElementById('start-date').value;
      const endDate = document.getElementById('end-date').value;
      
      if (startDate && endDate) {
        // Show loading state
        showAnalyticsLoading();
        
        // Load analytics data from backend with custom date range
        fetch(`{{ route("visitor.logs.analytics") }}?time_range=custom&start_date=${startDate}&end_date=${endDate}`, {
          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        })
          .then(response => {
            console.log('Analytics response status for custom range:', response.status);
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            console.log('Analytics data loaded for custom range:', data);
            createDailyTrendsChart(data.daily_trends || []);
            createVisitorTypesChart(data.visitor_types || {});
            createHostsDepartmentsChart(data.hosts_departments || []);
            createPeakHoursChart(data.peak_hours || []);
            updateAnalyticsStats(data);
          })
          .catch(error => {
            console.error('Error loading analytics data:', error);
            showNotification('Error loading analytics data: ' + error.message, 'error');
            // Show empty state instead of static data
            showEmptyAnalyticsState();
          });
      } else {
        showNotification('Please select both start and end dates', 'error');
      }
    }

    // Logs functions
    function loadLogsData() {
      // Load logs data
      console.log('Loading logs data...');
    }

    function applyLogsFilters() {
      const startDate = document.getElementById('logs-start-date').value;
      const endDate = document.getElementById('logs-end-date').value;
      const facility = document.getElementById('facility-filter').value;
      const payload = { 
        from: startDate, 
        to: endDate, 
        facilityId: facility
      };
      
      // Apply filters and reload data
      console.log('Applying filters:', payload);
      if (typeof window.dispatchEvent === 'function') {
        window.dispatchEvent(new CustomEvent('visitorLogs:applyFilters', { detail: payload }));
      }
      loadLogsData();
    }

    // Reports functions
    function loadReportsData() {
      // Load reports data
      console.log('Loading reports data...');
      initializeReportsAnalytics();
    }

    // Initialize Reports & Analytics
    function initializeReportsAnalytics() {
      // Load real analytics data first
      loadAnalyticsData();
      
      // Animate statistics cards
      animateStatisticsCards();
      
      // Initialize peak hours chart
      initializePeakHoursChart();
      
      // Initialize department distribution
      initializeDepartmentChart();
      
      // Initialize visit purposes
      initializeVisitPurposes();
      
      // Start real-time updates
      startRealTimeUpdates();
      
      // Add click interactions
      addClickInteractions();
    }

    // Animate statistics cards
    function animateStatisticsCards() {
      const cards = document.querySelectorAll('#reports-content .bg-white.rounded-xl');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          card.style.transition = 'all 0.6s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 100);
      });
    }

    // Initialize peak hours chart with animation
    function initializePeakHoursChart() {
      const progressBars = document.querySelectorAll('#reports-content .bg-blue-500');
      progressBars.forEach((bar, index) => {
        const width = bar.style.width;
        bar.style.width = '0%';
        
        setTimeout(() => {
          bar.style.transition = 'width 1.5s ease-in-out';
          bar.style.width = width;
        }, index * 100 + 500);
      });
    }

    // Initialize department distribution with animation
    function initializeDepartmentChart() {
      const departmentItems = document.querySelectorAll('#reports-content .space-y-3 > div');
      departmentItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
          item.style.transition = 'all 0.5s ease';
          item.style.opacity = '1';
          item.style.transform = 'translateX(0)';
        }, index * 150 + 800);
      });
    }

    // Initialize visit purposes with animation
    function initializeVisitPurposes() {
      const purposeItems = document.querySelectorAll('#reports-content .space-y-3 > div');
      purposeItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
          item.style.transition = 'all 0.5s ease';
          item.style.opacity = '1';
          item.style.transform = 'translateX(0)';
        }, index * 150 + 1000);
      });
    }

    // Start real-time updates
    function startRealTimeUpdates() {
      // Update statistics every 30 seconds
      // Removed auto updates; manual refresh only
      
      // Update peak hours every minute
      // Removed auto updates; manual refresh only
      
      // Add hover effects
      addHoverEffects();
    }

    // Update statistics with animation
    function updateStatistics() {
      const statNumbers = document.querySelectorAll('#reports-content .text-3xl.font-bold');
      statNumbers.forEach(number => {
        const currentValue = parseInt(number.textContent);
        const newValue = currentValue + Math.floor(Math.random() * 3) - 1; // Random change -1 to +1
        
        if (newValue !== currentValue) {
          animateNumberChange(number, currentValue, newValue);
        }
      });
    }

    // Animate number changes
    function animateNumberChange(element, from, to) {
      const duration = 1000;
      const start = Date.now();
      
      function update() {
        const elapsed = Date.now() - start;
        const progress = Math.min(elapsed / duration, 1);
        const current = Math.round(from + (to - from) * progress);
        
        element.textContent = current;
        
        if (progress < 1) {
          requestAnimationFrame(update);
        }
      }
      
      requestAnimationFrame(update);
    }

    // Update peak hours with animation
    function updatePeakHours() {
      const progressBars = document.querySelectorAll('#reports-content .bg-blue-500');
      progressBars.forEach((bar, index) => {
        const currentWidth = parseInt(bar.style.width);
        const newWidth = Math.max(0, Math.min(100, currentWidth + (Math.random() - 0.5) * 10));
        
        bar.style.transition = 'width 0.8s ease';
        bar.style.width = newWidth + '%';
        
        // Update the count number
        const countElement = bar.closest('.flex').querySelector('.text-sm.font-medium:last-child');
        if (countElement) {
          const newCount = Math.round((newWidth / 100) * 30); // Assuming max 30 visitors
          countElement.textContent = newCount;
        }
      });
    }

    // Add hover effects
    function addHoverEffects() {
      // Card hover effects
      const cards = document.querySelectorAll('#reports-content .bg-white.rounded-xl');
      cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-5px)';
          this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0)';
          this.style.boxShadow = '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)';
        });
      });

      // Progress bar hover effects
      const progressBars = document.querySelectorAll('#reports-content .bg-blue-500');
      progressBars.forEach(bar => {
        bar.addEventListener('mouseenter', function() {
          this.style.height = '8px';
          this.style.transition = 'height 0.3s ease';
        });
        
        bar.addEventListener('mouseleave', function() {
          this.style.height = '8px';
        });
      });
    }

    // Add click interactions
    function addClickInteractions() {
      // Statistics cards click to drill down
      const statCards = document.querySelectorAll('#reports-content .bg-white.rounded-xl');
      statCards.forEach(card => {
        card.addEventListener('click', function() {
          this.style.transform = 'scale(0.98)';
          setTimeout(() => {
            this.style.transform = 'scale(1)';
          }, 150);
          
          // Show detailed view (placeholder)
          showNotification('Detailed view coming soon!', 'info');
        });
      });

      // Department items click to filter
      const departmentItems = document.querySelectorAll('#reports-content .space-y-3 > div');
      departmentItems.forEach(item => {
        item.addEventListener('click', function() {
          const department = this.querySelector('.text-sm.font-medium').textContent;
          showNotification(`Filtering by ${department}`, 'info');
        });
      });
    }

    // Export Report Function
    function exportReport() {
      const timeRange = document.querySelector('#reports-content select').value || 'This Week';
      
      // Show loading state
      const button = event.target.closest('button');
      const originalText = button.innerHTML;
      button.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-1 animate-spin"></i>Exporting...';
      button.disabled = true;
      
      // Re-initialize Lucide icons for spinner
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
      
      // Prepare export data
      const exportData = {
        timeRange: timeRange,
        timestamp: new Date().toISOString(),
        statistics: getCurrentStatistics(),
        analytics: getCurrentAnalytics()
      };
      
      // Call backend export endpoint
      fetch('{{ route("visitor.export.report") }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(exportData)
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.blob();
      })
      .then(blob => {
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `visitor-report-${timeRange.toLowerCase().replace(' ', '-')}-${new Date().toISOString().split('T')[0]}.xlsx`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        showNotification('Report exported successfully!', 'success');
      })
      .catch(error => {
        console.error('Export error:', error);
        showNotification('Error exporting report. Please try again.', 'error');
      })
      .finally(() => {
        // Reset button state
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
          lucide.createIcons();
        }
      });
    }

    // Get current statistics for export
    function getCurrentStatistics() {
      const stats = {};
      const statCards = document.querySelectorAll('#reports-content .stat-number');
      
      statCards.forEach((card, index) => {
        const label = card.closest('.bg-white').querySelector('p').textContent;
        const value = card.textContent;
        stats[label] = value;
      });
      
      return stats;
    }

    // Get current analytics for export
    function getCurrentAnalytics() {
      const analytics = {
        peakHours: [],
        departments: [],
        purposes: [],
        summary: {
          highlights: [],
          improvements: [],
          recommendations: []
        }
      };
      
      // Get peak hours data
      const peakHourItems = document.querySelectorAll('#reports-content .space-y-3 > div');
      peakHourItems.forEach(item => {
        const time = item.querySelector('.text-sm.font-medium:first-child')?.textContent;
        const count = item.querySelector('.text-sm.font-medium:last-child')?.textContent;
        if (time && count) {
          analytics.peakHours.push({ time, count: parseInt(count) });
        }
      });
      
      // Get department data
      const departmentItems = document.querySelectorAll('#reports-content .department-item');
      departmentItems.forEach(item => {
        const name = item.querySelector('.text-sm.font-medium').textContent;
        const count = item.querySelector('.text-sm.text-gray-600').textContent;
        const percentage = item.querySelector('.text-xs.font-medium').textContent;
        analytics.departments.push({ name, count, percentage });
      });
      
      // Get purpose data
      const purposeItems = document.querySelectorAll('#reports-content .purpose-item');
      purposeItems.forEach(item => {
        const name = item.querySelector('.text-sm.font-medium').textContent;
        const count = item.querySelector('.text-sm.text-gray-600').textContent;
        const percentage = item.querySelector('.text-xs.font-medium').textContent;
        analytics.purposes.push({ name, count, percentage });
      });
      
      // Get summary data
      const highlightItems = document.querySelectorAll('#reports-content .highlight-item');
      highlightItems.forEach(item => {
        analytics.summary.highlights.push(item.textContent.trim());
      });
      
      const improvementItems = document.querySelectorAll('#reports-content .improvement-item');
      improvementItems.forEach(item => {
        analytics.summary.improvements.push(item.textContent.trim());
      });
      
      const recommendationItems = document.querySelectorAll('#reports-content .recommendation-item');
      recommendationItems.forEach(item => {
        analytics.summary.recommendations.push(item.textContent.trim());
      });
      
      return analytics;
    }



    // Utility functions
    function viewVisitorDetails(visitorId) {
      // Open visitor details modal or redirect to details page
      console.log('Viewing visitor details:', visitorId);
    }

    function exportVisitorLog(visitorId) {
      // Export individual visitor log
      console.log('Exporting visitor log:', visitorId);
    }

    // Recent Reports Management
    function addToRecentReports(reportType, startDate, format) {
      const recentReportsContainer = document.getElementById('recent-reports');
      
      // Remove the "no reports" message if it exists
      const noReportsMsg = recentReportsContainer.querySelector('.text-center');
      if (noReportsMsg) {
        noReportsMsg.remove();
      }
      
      // Create new report entry
      const reportEntry = document.createElement('div');
      reportEntry.className = 'flex items-center justify-between p-3 bg-white rounded-lg';
      reportEntry.innerHTML = `
        <div>
          <p class="font-medium text-gray-900">${formatReportTitle(reportType, startDate)}</p>
          <p class="text-sm text-gray-500">Generated just now</p>
        </div>
        <div class="flex gap-2">
          <button class="btn btn-sm btn-outline" title="Download" onclick="downloadReport('${reportType}', '${startDate}', '${format}')">
            <i data-lucide="download" class="w-4 h-4"></i>
          </button>
          <button class="btn btn-sm btn-ghost text-red-600" title="Delete" onclick="deleteReport(this)">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </button>
        </div>
      `;
      
      // Add to the top of the list
      recentReportsContainer.insertBefore(reportEntry, recentReportsContainer.firstChild);
      
      // Limit to 5 recent reports
      const reports = recentReportsContainer.querySelectorAll('.flex.items-center.justify-between');
      if (reports.length > 5) {
        reports[reports.length - 1].remove();
      }
    }

    function formatReportTitle(reportType, startDate) {
      const date = new Date(startDate);
      const formattedDate = date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
      });
      
      switch(reportType) {
        case 'daily':
          return `Daily Summary - ${formattedDate}`;
        case 'weekly':
          return `Weekly Report - ${formattedDate}`;
        case 'monthly':
          return `Monthly Report - ${formattedDate}`;
        case 'custom':
          return `Custom Report - ${formattedDate}`;
        default:
          return `Report - ${formattedDate}`;
      }
    }

    function downloadReport(reportType, startDate, format) {
      // This would trigger a re-download of the report
      showNotification('Report download initiated', 'info');
    }

    function deleteReport(button) {
      if (confirm('Are you sure you want to delete this report?')) {
        button.closest('.flex.items-center.justify-between').remove();
        
        // Check if no reports left
        const recentReportsContainer = document.getElementById('recent-reports');
        const reports = recentReportsContainer.querySelectorAll('.flex.items-center.justify-between');
        
        if (reports.length === 0) {
          recentReportsContainer.innerHTML = `
            <div class="text-center py-8 text-gray-500">
              <i data-lucide="file-text" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
              <p>No recent reports generated yet</p>
              <p class="text-sm">Generate your first report using the form on the left</p>
            </div>
          `;
        }
        
        showNotification('Report deleted', 'success');
      }
    }

    // Auto-set date ranges based on report type
    document.querySelector('select[name="report_type"]').addEventListener('change', function() {
      const reportType = this.value;
      const startDateInput = document.querySelector('input[name="start_date"]');
      const endDateInput = document.querySelector('input[name="end_date"]');
      const today = new Date();
      
      switch(reportType) {
        case 'daily':
          startDateInput.value = today.toISOString().split('T')[0];
          endDateInput.value = today.toISOString().split('T')[0];
          break;
        case 'weekly':
          const weekAgo = new Date(today);
          weekAgo.setDate(today.getDate() - 7);
          startDateInput.value = weekAgo.toISOString().split('T')[0];
          endDateInput.value = today.toISOString().split('T')[0];
          break;
        case 'monthly':
          const monthAgo = new Date(today);
          monthAgo.setMonth(today.getMonth() - 1);
          startDateInput.value = monthAgo.toISOString().split('T')[0];
          endDateInput.value = today.toISOString().split('T')[0];
          break;
        case 'custom':
          // Don't auto-set for custom
          break;
      }
    });

    // Form submissions
    document.getElementById('report-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      
      // Validate form
      const reportType = formData.get('report_type');
      const startDate = formData.get('start_date');
      const endDate = formData.get('end_date');
      const format = formData.get('format');
      
      if (!reportType || !startDate || !endDate || !format) {
        showNotification('Please fill in all required fields', 'error');
        return;
      }
      
      if (new Date(startDate) > new Date(endDate)) {
        showNotification('Start date cannot be after end date', 'error');
        return;
      }
      
      // Show loading state
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Generating...';
      submitBtn.disabled = true;
      
      // Generate report
      fetch('{{ route("visitor.logs.generate-report") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        if (response.ok) {
          return response.blob();
        }
        throw new Error('Report generation failed');
      })
      .then(blob => {
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        
        // Get filename from response headers or create default
        const reportType = formData.get('report_type');
        const startDate = formData.get('start_date');
        const format = formData.get('format');
        const filename = `visitor_${reportType}_report_${startDate}.${format}`;
        a.download = filename;
        
        // Trigger download
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        showNotification('Report generated and downloaded successfully!', 'success');
        
        // Add to recent reports
        addToRecentReports(reportType, startDate, format);
      })
      .catch(error => {
        console.error('Error generating report:', error);
        showNotification('Error generating report: ' + error.message, 'error');
      })
      .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      });
    });





    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.className = `alert alert-${type === 'error' ? 'error' : type === 'success' ? 'success' : 'info'} fixed bottom-4 right-4 z-50 max-w-sm`;
      notification.innerHTML = `
        <i data-lucide="${type === 'error' ? 'alert-circle' : type === 'success' ? 'check-circle' : 'info'}" class="w-5 h-5"></i>
        <span>${message}</span>
      `;
      
      document.body.appendChild(notification);
      
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
      
      setTimeout(() => {
        notification.remove();
      }, 3000);
    }

    // Live duration calculation for visitors still in building
    function updateLiveDurations() {
      const liveDurationElements = document.querySelectorAll('.live-duration');
      
      liveDurationElements.forEach(element => {
        const checkInTime = element.getAttribute('data-checkin');
        if (checkInTime) {
          const checkIn = new Date(checkInTime);
          const now = new Date();
          const diffMs = now - checkIn;
          
          if (diffMs > 0) {
            const totalMinutes = Math.floor(diffMs / (1000 * 60));
            
            // Calculate days, hours, minutes
            const days = Math.floor(totalMinutes / (24 * 60));
            const hours = Math.floor((totalMinutes % (24 * 60)) / 60);
            const mins = totalMinutes % 60;
            
            // Build compact display
            const parts = [];
            if (days > 0) parts.push(`${days}d`);
            if (hours > 0) parts.push(`${hours}h`);
            if (mins > 0) parts.push(`${mins}m`);
            
            const displayText = parts.length > 0 ? parts.join(' ') : '0m';
            
            // Build verbose tooltip
            const tooltipParts = [];
            if (days > 0) tooltipParts.push(`${days} day${days > 1 ? 's' : ''}`);
            if (hours > 0) tooltipParts.push(`${hours} hour${hours > 1 ? 's' : ''}`);
            if (mins > 0) tooltipParts.push(`${mins} minute${mins > 1 ? 's' : ''}`);
            
            const tooltipText = tooltipParts.length > 0 ? tooltipParts.join(', ') : '0 minutes';
            
            // Determine color class based on duration
            let colorClass = 'badge-primary';
            if (totalMinutes < 8 * 60) {
              colorClass = 'badge-success'; // < 8h
            } else if (totalMinutes < 72 * 60) {
              colorClass = 'badge-warning'; // 8h-72h
            } else {
              colorClass = 'badge-error'; // > 72h
            }
            
            // Update element with new data
            element.setAttribute('data-duration-minutes', totalMinutes);
            element.setAttribute('data-tooltip', tooltipText);
            element.setAttribute('title', tooltipText);
            element.setAttribute('aria-label', tooltipText);
            
            // Determine pill class based on duration
            let pillClass = 'duration-pill--short'; // < 8h: green
            if (totalMinutes >= 480 && totalMinutes < 4320) { // 8h-72h: amber
              pillClass = 'duration-pill--medium';
            } else if (totalMinutes >= 4320) { // > 72h: red
              pillClass = 'duration-pill--long';
            }
            
            element.innerHTML = `<span class="duration-pill ${pillClass}">${displayText}</span>`;
          } else {
            element.innerHTML = '<span class="duration-pill duration-pill--error">Just arrived</span>';
          }
        }
      });
    }

    // Set up event listeners
    function setupEventListeners() {
      // Time range buttons
      document.querySelectorAll('.time-range-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          const range = this.dataset.range;
          setTimeRange(range);
        });
      });
      
      // Custom range apply button
      const applyCustomBtn = document.getElementById('apply-custom-range');
      if (applyCustomBtn) {
        applyCustomBtn.addEventListener('click', applyCustomRange);
      }
      
      // Report form submission
      const reportForm = document.getElementById('report-form');
      if (reportForm) {
        reportForm.addEventListener('submit', handleReportSubmission);
      }
      
      // Tab switching - load analytics when analytics tab is clicked
      const analyticsTabBtn = document.querySelector('[data-tab="analytics"]');
      if (analyticsTabBtn) {
        analyticsTabBtn.addEventListener('click', function() {
          // Small delay to ensure tab is visible before loading charts
          setTimeout(() => {
            loadAnalyticsData();
          }, 50);
        });
      }
    }

    // Check for reduced motion preference
    function shouldRespectReducedMotion() {
      return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      // Set up event listeners first
      setupEventListeners();
      
      // Load initial data with a small delay to ensure DOM is ready
      setTimeout(() => {
        loadLogsData();
        
        // Always load analytics data on page load (regardless of active tab)
        loadAnalyticsData();
      }, 100);
      
      // Also load analytics data after a longer delay to ensure it's loaded
      setTimeout(() => {
        loadAnalyticsData();
      }, 1000);
      
      // Initialize all Lucide icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }

      // Start live duration updates (respect reduced motion)
      updateLiveDurations();
      
      if (!shouldRespectReducedMotion()) {
        // Removed auto updates; manual refresh only
      } else {
        // For users with reduced motion preference, update less frequently
        // Removed auto updates; manual refresh only
      }
      
      // Add accessibility attributes to duration elements
      const durationElements = document.querySelectorAll('.duration-display, .live-duration');
      durationElements.forEach((element, index) => {
        const tooltipId = `duration-tooltip-${index}`;
        element.setAttribute('aria-describedby', tooltipId);
        element.setAttribute('role', 'text');
        element.setAttribute('aria-label', element.getAttribute('data-tooltip') || 'Duration information');
      });
      
      // Add accessibility attributes to checkout time elements
      const checkoutTimeElements = document.querySelectorAll('.checkout-time-display');
      checkoutTimeElements.forEach((element, index) => {
        const tooltipId = `checkout-tooltip-${index}`;
        element.setAttribute('aria-describedby', tooltipId);
        element.setAttribute('role', 'text');
        element.setAttribute('aria-label', element.getAttribute('title') || 'Check out time information');
      });
    });
  </script>
</body>
</html>
