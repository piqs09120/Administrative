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
          <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
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

            <!-- Average Duration -->
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-warning">
              <div class="card-body p-4">
                <div class="flex items-center justify-between mb-3">
                  <div class="avatar placeholder">
                    <div class="bg-warning text-warning-content rounded-full w-10 h-10">
                      <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                  </div>
                  <div class="badge badge-warning badge-outline text-xs">Average</div>
                </div>
                <div class="text-center">
                  @php
                    $avgRaw = $stats['avg_duration'] ?? '0h';
                    $avgDisplay = preg_replace('/h$/i', ' hr', $avgRaw);
                  @endphp
                  <h2 class="card-title text-2xl sm:text-3xl font-bold text-warning justify-center mb-1 font-mono tabular-nums" id="averageDuration">{{ $avgDisplay }}</h2>
                  <p class="text-sm text-base-content/70">Visit Duration</p>
                </div>
              </div>
            </div>

            <!-- Peak Hours -->
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-info">
              <div class="card-body p-4">
                <div class="flex items-center justify-between mb-3">
                  <div class="avatar placeholder">
                    <div class="bg-info text-info-content rounded-full w-10 h-10">
                      <i data-lucide="trending-up" class="w-5 h-5"></i>
                    </div>
                  </div>
                  <div class="badge badge-info badge-outline text-xs">Peak</div>
                </div>
                <div class="text-center">
                  <h2 class="card-title text-lg sm:text-xl font-bold text-info justify-center mb-1" id="peakHours">{{ $stats['peak_hours'] ?? '9-11 AM' }}</h2>
                  <p class="text-sm text-base-content/70">Busiest Time</p>
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
              <button onclick="showTab('analytics')" class="tab-btn active py-3 sm:py-4 px-1 border-b-2 border-blue-500 font-medium text-xs sm:text-sm text-blue-600" id="analytics-tab">
                <i data-lucide="bar-chart-3" class="w-4 h-4 mr-1 sm:mr-2"></i>
                <span class="hidden sm:inline">Analytics</span>
                <span class="sm:hidden">Analytics</span>
              </button>
              <button onclick="showTab('logs')" class="tab-btn py-3 sm:py-4 px-1 border-b-2 border-transparent font-medium text-xs sm:text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" id="logs-tab">
                <i data-lucide="list" class="w-4 h-4 mr-1 sm:mr-2"></i>
                <span class="hidden sm:inline">Detailed Logs</span>
                <span class="sm:hidden">Logs</span>
              </button>
              <button onclick="showTab('reports')" class="tab-btn py-3 sm:py-4 px-1 border-b-2 border-transparent font-medium text-xs sm:text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" id="reports-tab">
                <i data-lucide="file-text" class="w-4 h-4 mr-1 sm:mr-2"></i>
                <span class="hidden sm:inline">Reports</span>
                <span class="sm:hidden">Reports</span>
              </button>
              <button onclick="showTab('search')" class="tab-btn py-3 sm:py-4 px-1 border-b-2 border-transparent font-medium text-xs sm:text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" id="search-tab">
                <i data-lucide="search" class="w-4 h-4 mr-1 sm:mr-2"></i>
                <span class="hidden sm:inline">Advanced Search</span>
                <span class="sm:hidden">Search</span>
              </button>
            </nav>
          </div>

          <!-- Tab Content -->
          <div class="p-4 sm:p-6">
            <!-- Analytics Tab -->
            <div id="analytics-content" class="tab-content">
              <!-- Time Range Selector -->
              <div class="mb-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                  <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Time Range:</label>
                  <div class="flex flex-wrap gap-2">
                    <button onclick="setTimeRange('today')" class="time-range-btn active btn btn-sm btn-outline">Today</button>
                    <button onclick="setTimeRange('week')" class="time-range-btn btn btn-sm btn-outline">This Week</button>
                    <button onclick="setTimeRange('month')" class="time-range-btn btn btn-sm btn-outline">This Month</button>
                    <button onclick="setTimeRange('custom')" class="time-range-btn btn btn-sm btn-outline">Custom</button>
                  </div>
                  <div id="custom-date-range" class="hidden gap-2">
                    <input type="date" id="start-date" class="input input-sm input-bordered">
                    <input type="date" id="end-date" class="input input-sm input-bordered">
                    <button onclick="applyCustomRange()" class="btn btn-sm btn-primary">Apply</button>
                  </div>
                </div>
              </div>

              <!-- Charts Grid -->
              <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
                <!-- Daily Trends Chart -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h3 class="text-base font-semibold text-gray-800 mb-3">Daily Visitor Trends</h3>
                  <div class="chart-container">
                    <canvas id="dailyTrendsChart"></canvas>
                  </div>
                </div>

                <!-- Visitor Types Chart -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h3 class="text-base font-semibold text-gray-800 mb-3">Visitor Types</h3>
                  <div class="chart-container">
                    <canvas id="visitorTypesChart"></canvas>
                  </div>
                </div>
              </div>

              <!-- Statistics Row -->
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                  <h4 class="text-sm font-semibold text-blue-800 mb-2">Peak Hours</h4>
                  <p class="text-lg font-bold text-blue-900" id="peakHoursDetail">9:00 AM - 11:00 AM</p>
                  <p class="text-xs text-blue-600">Most active time period</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                  <h4 class="text-sm font-semibold text-green-800 mb-2">Most Visited Facility</h4>
                  <p class="text-lg font-bold text-green-900" id="mostVisitedFacility">Conference Room A</p>
                  <p class="text-xs text-green-600">Highest visitor count</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                  <h4 class="text-sm font-semibold text-purple-800 mb-2">Return Visitors</h4>
                  <p class="text-lg font-bold text-purple-900" id="returnVisitors">15%</p>
                  <p class="text-xs text-purple-600">Percentage of repeat visitors</p>
                </div>
              </div>
            </div>

            <!-- Detailed Logs Tab -->
            <div id="logs-content" class="tab-content hidden">
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

                  <!-- Status -->
                  <div class="col-span-12 md:col-span-6 xl:col-span-2 min-w-0 relative z-50">
                    <label for="status-filter" class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                    <select id="status-filter" class="w-full h-10 md:h-11 text-sm px-3 rounded-md border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 truncate" title="Select status">
                      <option value="" title="All Status">All Status</option>
                      <option value="checked_in" title="Currently In">Currently In</option>
                      <option value="checked_out" title="Checked Out">Checked Out</option>
                    </select>
                  </div>

                  <!-- Apply Button -->
                  <div class="col-span-12 xl:col-span-1 min-w-0">
                    <button id="apply-logs-filters" aria-label="Apply filters" class="btn btn-primary w-full h-10 md:h-11 px-4 text-sm">
                      <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                      Apply
                    </button>
                  </div>
                </div>
              </div>

              <!-- Logs Table -->
              <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                  <thead>
                    <tr class="bg-gray-50">
                      <th class="text-left py-3 px-4 font-medium text-gray-700">Visitor Name</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Company</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Purpose</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Facility</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Check In</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Check Out</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Duration</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Host</th>
                      <th class="text-center py-3 px-4 font-medium text-gray-700">Actions</th>
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
                        <td class="py-3 px-4 text-center text-sm text-gray-600">{{ $visitor->company ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-center">
                          <span class="badge badge-outline badge-sm">{{ $visitor->purpose ?? 'N/A' }}</span>
                        </td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600">{{ $visitor->facility->name ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600">{{ $visitor->time_in ? \Carbon\Carbon::parse($visitor->time_in)->format('M d, Y h:i A') : 'N/A' }}</td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600">{{ $visitor->time_out ? \Carbon\Carbon::parse($visitor->time_out)->format('M d, Y h:i A') : 'Still in' }}</td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600">
                          @if($visitor->time_out)
                            {{ \Carbon\Carbon::parse($visitor->time_in)->diffForHumans(\Carbon\Carbon::parse($visitor->time_out), true) }}
                          @else
                            <span class="badge badge-primary badge-sm">Active</span>
                          @endif
                        </td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600">{{ $visitor->host_employee ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-center">
                          <div class="flex items-center justify-center gap-1">
                            <button onclick="viewVisitorDetails({{ $visitor->id }})" class="btn btn-ghost btn-xs tooltip" data-tip="View Details">
                              <i data-lucide="eye" class="w-4 h-4 text-blue-600"></i>
                            </button>
                            <button onclick="exportVisitorLog({{ $visitor->id }})" class="btn btn-ghost btn-xs tooltip" data-tip="Export">
                              <i data-lucide="download" class="w-4 h-4 text-green-600"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="9" class="text-center py-12">
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

            <!-- Reports Tab -->
            <div id="reports-content" class="tab-content hidden">
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Report Generator -->
                <div class="bg-gray-50 rounded-lg p-6">
                  <h3 class="text-lg font-semibold text-gray-800 mb-4">Generate Report</h3>
                  <form id="report-form" class="space-y-4">
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
                      <label class="block text-sm font-medium text-gray-700 mb-2">Date Range:</label>
                      <div class="flex gap-2">
                        <input type="date" name="start_date" class="input input-bordered flex-1">
                        <input type="date" name="end_date" class="input input-bordered flex-1">
                      </div>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Include:</label>
                      <div class="space-y-2">
                        <label class="flex items-center">
                          <input type="checkbox" name="include_details" checked class="checkbox checkbox-sm mr-2">
                          <span class="text-sm">Visitor Details</span>
                        </label>
                        <label class="flex items-center">
                          <input type="checkbox" name="include_statistics" checked class="checkbox checkbox-sm mr-2">
                          <span class="text-sm">Statistics</span>
                        </label>
                        <label class="flex items-center">
                          <input type="checkbox" name="include_charts" class="checkbox checkbox-sm mr-2">
                          <span class="text-sm">Charts</span>
                        </label>
                      </div>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Format:</label>
                      <select name="format" class="select select-bordered w-full">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                        <option value="csv">CSV</option>
                      </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-full">
                      <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                      Generate Report
                    </button>
                  </form>
                </div>

                <!-- Recent Reports -->
                <div class="bg-gray-50 rounded-lg p-6">
                  <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Reports</h3>
                  <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                      <div>
                        <p class="font-medium text-gray-900">Daily Summary - Dec 15, 2024</p>
                        <p class="text-sm text-gray-500">Generated 2 hours ago</p>
                      </div>
                      <div class="flex gap-2">
                        <button class="btn btn-sm btn-outline">Download</button>
                        <button class="btn btn-sm btn-ghost text-red-600">Delete</button>
                      </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                      <div>
                        <p class="font-medium text-gray-900">Weekly Report - Dec 9-15, 2024</p>
                        <p class="text-sm text-gray-500">Generated 1 day ago</p>
                      </div>
                      <div class="flex gap-2">
                        <button class="btn btn-sm btn-outline">Download</button>
                        <button class="btn btn-sm btn-ghost text-red-600">Delete</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Advanced Search Tab -->
            <div id="search-content" class="tab-content hidden">
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Search Form -->
                <div class="bg-gray-50 rounded-lg p-6">
                  <h3 class="text-lg font-semibold text-gray-800 mb-4">Advanced Search</h3>
                  <form id="search-form" class="space-y-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Visitor Name:</label>
                      <input type="text" name="visitor_name" placeholder="Enter visitor name" class="input input-bordered w-full">
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Company:</label>
                      <input type="text" name="company" placeholder="Enter company name" class="input input-bordered w-full">
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Host Employee:</label>
                      <input type="text" name="host_employee" placeholder="Enter host name" class="input input-bordered w-full">
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Purpose:</label>
                      <select name="purpose" class="select select-bordered w-full">
                        <option value="">All Purposes</option>
                        <option value="Meeting">Meeting</option>
                        <option value="Interview">Interview</option>
                        <option value="Delivery">Delivery</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Consultation">Consultation</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Visit Duration:</label>
                      <select name="duration" class="select select-bordered w-full">
                        <option value="">Any Duration</option>
                        <option value="less_than_1h">Less than 1 hour</option>
                        <option value="1h_to_2h">1-2 hours</option>
                        <option value="2h_to_4h">2-4 hours</option>
                        <option value="more_than_4h">More than 4 hours</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Time Range:</label>
                      <div class="flex gap-2">
                        <input type="datetime-local" name="start_time" class="input input-bordered flex-1">
                        <input type="datetime-local" name="end_time" class="input input-bordered flex-1">
                      </div>
                    </div>
                    <div class="flex gap-2">
                      <button type="submit" class="btn btn-primary flex-1">
                        <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                        Search
                      </button>
                      <button type="button" onclick="clearSearch()" class="btn btn-outline">
                        Clear
                      </button>
                    </div>
                  </form>
                </div>

                <!-- Search Results -->
                <div class="bg-gray-50 rounded-lg p-6">
                  <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Search Results</h3>
                    <span class="text-sm text-gray-500" id="search-results-count">0 results</span>
                  </div>
                  <div id="search-results" class="space-y-3">
                    <div class="text-center py-8 text-gray-500">
                      <i data-lucide="search" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                      <p>Enter search criteria to find visitors</p>
                    </div>
                  </div>
                </div>
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
    let currentTab = 'analytics';
    let dailyTrendsChart = null;
    let visitorTypesChart = null;

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
    }

    // Load data for specific tabs
    function loadTabData(tabName) {
      switch(tabName) {
        case 'analytics':
          loadAnalyticsData();
          break;
        case 'logs':
          loadLogsData();
          break;
        case 'reports':
          loadReportsData();
          break;
        case 'search':
          // Search form is static, no data loading needed
          break;
      }
    }

    // Analytics functions
    function loadAnalyticsData() {
      // Load analytics data and create charts
      createDailyTrendsChart();
      createVisitorTypesChart();
      updateAnalyticsStats();
    }

    function createDailyTrendsChart() {
      const ctx = document.getElementById('dailyTrendsChart').getContext('2d');
      
      if (dailyTrendsChart) {
        dailyTrendsChart.destroy();
      }
      
      dailyTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['6 AM', '8 AM', '10 AM', '12 PM', '2 PM', '4 PM', '6 PM', '8 PM'],
          datasets: [{
            label: 'Visitors',
            data: [2, 8, 15, 12, 18, 10, 6, 2],
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

    function createVisitorTypesChart() {
      const ctx = document.getElementById('visitorTypesChart').getContext('2d');
      
      if (visitorTypesChart) {
        visitorTypesChart.destroy();
      }
      
      visitorTypesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Meeting', 'Interview', 'Delivery', 'Maintenance', 'Other'],
          datasets: [{
            data: [35, 20, 15, 10, 20],
            backgroundColor: [
              'rgb(59, 130, 246)',
              'rgb(16, 185, 129)',
              'rgb(245, 158, 11)',
              'rgb(239, 68, 68)',
              'rgb(139, 92, 246)'
            ]
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

    function updateAnalyticsStats() {
      // Update analytics statistics
      // This would typically fetch data from the server
      document.getElementById('peakHoursDetail').textContent = '9:00 AM - 11:00 AM';
      document.getElementById('mostVisitedFacility').textContent = 'Conference Room A';
      document.getElementById('returnVisitors').textContent = '15%';
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
      }
      
      // Load data for the selected time range
      loadAnalyticsData();
    }

    function applyCustomRange() {
      const startDate = document.getElementById('start-date').value;
      const endDate = document.getElementById('end-date').value;
      
      if (startDate && endDate) {
        loadAnalyticsData();
      } else {
        alert('Please select both start and end dates');
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
      const status = document.getElementById('status-filter').value;
      const payload = { from: startDate, to: endDate, facilityId: facility, status };
      
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
    }

    // Search functions
    function clearSearch() {
      document.getElementById('search-form').reset();
      document.getElementById('search-results').innerHTML = `
        <div class="text-center py-8 text-gray-500">
          <i data-lucide="search" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
          <p>Enter search criteria to find visitors</p>
        </div>
      `;
      document.getElementById('search-results-count').textContent = '0 results';
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

    // Form submissions
    document.getElementById('report-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      
      // Generate report
      console.log('Generating report:', Object.fromEntries(formData));
      
      // Show success message
      showNotification('Report generated successfully!', 'success');
    });

    document.getElementById('search-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      
      // Perform search
      console.log('Searching with criteria:', Object.fromEntries(formData));
      
      // Mock search results
      const mockResults = [
        { name: 'John Doe', company: 'ABC Corp', purpose: 'Meeting', checkIn: '2024-12-15 09:00' },
        { name: 'Jane Smith', company: 'XYZ Ltd', purpose: 'Interview', checkIn: '2024-12-15 10:30' }
      ];
      
      displaySearchResults(mockResults);
    });

    function displaySearchResults(results) {
      const resultsContainer = document.getElementById('search-results');
      const countElement = document.getElementById('search-results-count');
      
      countElement.textContent = `${results.length} results`;
      
      if (results.length === 0) {
        resultsContainer.innerHTML = `
          <div class="text-center py-8 text-gray-500">
            <i data-lucide="search" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
            <p>No visitors found matching your criteria</p>
          </div>
        `;
      } else {
        resultsContainer.innerHTML = results.map(result => `
          <div class="flex items-center justify-between p-3 bg-white rounded-lg">
            <div>
              <p class="font-medium text-gray-900">${result.name}</p>
              <p class="text-sm text-gray-500">${result.company} • ${result.purpose}</p>
            </div>
            <div class="text-sm text-gray-500">
              ${result.checkIn}
            </div>
          </div>
        `).join('');
      }
      
      // Recreate icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

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

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      // Load initial data
      loadAnalyticsData();
      
      // Initialize all Lucide icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }

      // Debounce Apply Filters button to prevent double submits
      const applyBtn = document.getElementById('apply-logs-filters');
      if (applyBtn) {
        let lastClick = 0;
        applyBtn.addEventListener('click', function(e) {
          const now = Date.now();
          if (now - lastClick < 300) {
            e.preventDefault();
            return;
          }
          lastClick = now;
          e.preventDefault();
          applyLogsFilters();
        });
      }
    });
  </script>
</body>
</html>
