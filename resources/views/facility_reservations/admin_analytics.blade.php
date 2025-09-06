<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Analytics Dashboard - Soliera</title>
  <link rel="icon" href="swt.jpg" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @vite(['resources/css/soliera.css'])
  <style>
    .chart-container {
      height: 200px;
      width: 100%;
    }
    @media (max-width: 1024px) {
      .chart-container {
        height: 180px;
      }
    }
    @media (max-width: 768px) {
      .chart-container {
        height: 160px;
      }
    }
    
    /* Chart control buttons */
    .trend-btn.active,
    .peak-btn.active {
      background-color: var(--color-regal-navy) !important;
      color: white !important;
      border-color: var(--color-regal-navy) !important;
    }
    
    .trend-btn:hover,
    .peak-btn:hover {
      background-color: var(--color-modern-teal) !important;
      color: white !important;
      border-color: var(--color-modern-teal) !important;
    }
    
    /* Chart animations */
    .chart-container canvas {
      transition: all 0.3s ease;
    }
    
    /* Enhanced tooltips */
    .chart-tooltip {
      background: rgba(0, 0, 0, 0.8);
      color: white;
      padding: 8px 12px;
      border-radius: 6px;
      font-size: 12px;
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
      <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
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
          <div class="flex items-center mb-4">
            <a href="{{ route('facility_reservations.index') }}" class="btn btn-ghost btn-sm mr-4" style="color: var(--color-regal-navy);" title="Back to Facility Reservations">
              <i data-lucide="arrow-left" class="w-5 h-5" style="color: var(--color-regal-navy);"></i>
            </a>
            <div>
              <h1 class="text-3xl font-bold text-gray-800 mb-2">Analytics Dashboard</h1>
              <p class="text-gray-600">Monitor and analyze facility reservation data</p>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Reservations -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-primary cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                  <div class="bg-primary text-primary-content rounded-full w-12 h-12">
                    <i data-lucide="calendar" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-primary badge-outline group-hover:badge-primary transition-colors duration-300">Reservations</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-primary justify-center mb-2 group-hover:text-primary-focus transition-colors duration-300">{{ $analytics['overview']['total_reservations'] }}</h2>
                <p class="text-base-content/70 group-hover:text-base-content transition-colors duration-300">Total Reservations</p>
              </div>
            </div>
          </div>

          <!-- Approval Rate -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-success cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                  <div class="bg-success text-success-content rounded-full w-12 h-12">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-success badge-outline group-hover:badge-success transition-colors duration-300">Approved</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-success justify-center mb-2 group-hover:text-success-focus transition-colors duration-300">{{ number_format($analytics['overview']['approval_rate'], 1) }}%</h2>
                <p class="text-base-content/70 group-hover:text-base-content transition-colors duration-300">Approval Rate</p>
              </div>
            </div>
          </div>

          <!-- Pending Reviews -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-info cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                  <div class="bg-info text-info-content rounded-full w-12 h-12">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-info badge-outline group-hover:badge-info transition-colors duration-300">Pending</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-info justify-center mb-2 group-hover:text-info-focus transition-colors duration-300">{{ $analytics['overview']['pending_reservations'] }}</h2>
                <p class="text-base-content/70 group-hover:text-base-content transition-colors duration-300">Pending Reviews</p>
              </div>
            </div>
          </div>

          <!-- Active Users -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-neutral cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                  <div class="bg-neutral text-neutral-content rounded-full w-12 h-12">
                    <i data-lucide="users" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-neutral badge-outline group-hover:badge-neutral transition-colors duration-300">Users</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-neutral justify-center mb-2 group-hover:text-neutral-focus transition-colors duration-300">{{ $analytics['overview']['active_users'] }}</h2>
                <p class="text-base-content/70 group-hover:text-base-content transition-colors duration-300">Active Users</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Analytics Content -->
        <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200">
          <div class="p-6">
            <!-- Revenue Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
              <!-- Total Revenue -->
              <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-success cursor-pointer group">
                <div class="card-body p-6">
                  <div class="flex items-center justify-between mb-4">
                    <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                      <div class="bg-success text-success-content rounded-full w-12 h-12">
                        <i data-lucide="dollar-sign" class="w-6 h-6"></i>
                      </div>
                    </div>
                    <div class="badge badge-success badge-outline group-hover:badge-success transition-colors duration-300">Revenue</div>
                  </div>
                  <div class="text-center">
                    <h2 class="card-title text-4xl font-bold text-success justify-center mb-2 group-hover:text-success-focus transition-colors duration-300">${{ number_format($analytics['revenue_analytics']['total_revenue'], 2) }}</h2>
                    <p class="text-base-content/70 group-hover:text-base-content transition-colors duration-300">Total Revenue</p>
                  </div>
                </div>
              </div>

              <!-- Monthly Revenue -->
              <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-primary cursor-pointer group">
                <div class="card-body p-6">
                  <div class="flex items-center justify-between mb-4">
                    <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                      <div class="bg-primary text-primary-content rounded-full w-12 h-12">
                        <i data-lucide="trending-up" class="w-6 h-6"></i>
                      </div>
                    </div>
                    <div class="badge badge-primary badge-outline group-hover:badge-primary transition-colors duration-300">This Month</div>
                  </div>
                  <div class="text-center">
                    <h2 class="card-title text-4xl font-bold text-primary justify-center mb-2 group-hover:text-primary-focus transition-colors duration-300">${{ number_format($analytics['revenue_analytics']['monthly_revenue'], 2) }}</h2>
                    <p class="text-base-content/70 group-hover:text-base-content transition-colors duration-300">This Month Revenue</p>
                  </div>
                </div>
              </div>

              <!-- Average Booking Value -->
              <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-info cursor-pointer group">
                <div class="card-body p-6">
                  <div class="flex items-center justify-between mb-4">
                    <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                      <div class="bg-info text-info-content rounded-full w-12 h-12">
                        <i data-lucide="bar-chart" class="w-6 h-6"></i>
                      </div>
                    </div>
                    <div class="badge badge-info badge-outline group-hover:badge-info transition-colors duration-300">Average</div>
                  </div>
                  <div class="text-center">
                    <h2 class="card-title text-4xl font-bold text-info justify-center mb-2 group-hover:text-info-focus transition-colors duration-300">${{ number_format($analytics['revenue_analytics']['average_booking_value'], 2) }}</h2>
                    <p class="text-base-content/70 group-hover:text-base-content transition-colors duration-300">Avg Booking Value</p>
                  </div>
                </div>
              </div>
            </div>

        <!-- Monthly Reports Quick Access -->
        <div class="bg-white rounded-lg shadow-sm p-3 mb-4">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-md font-semibold text-gray-800">Monthly Reports</h3>
            <a href="{{ route('facility_reservations.monthly_reports') }}" class="btn btn-outline btn-sm">
              <i data-lucide="external-link" class="w-4 h-4 mr-1"></i>
              View All
            </a>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3 min-w-0">
            <!-- Current Month Quick Report -->
            <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
              <div class="text-center">
                <div class="w-8 h-8 bg-blue-200 rounded-full flex items-center justify-center mx-auto mb-2">
                  <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                </div>
                <h4 class="font-semibold text-sm text-blue-800">{{ now()->format('M Y') }}</h4>
                <p class="text-xs text-blue-600 mb-2">{{ $analytics['overview']['this_month_reservations'] }} reservations</p>
                <div class="flex gap-1">
                  <a href="{{ route('facility_reservations.generate_monthly_report', ['month' => now()->month, 'year' => now()->year, 'format' => 'excel']) }}" 
                     class="btn btn-xs btn-outline">
                    <i data-lucide="download" class="w-3 h-3 mr-1"></i>
                    Excel
                  </a>
                  <a href="{{ route('facility_reservations.generate_monthly_report', ['month' => now()->month, 'year' => now()->year, 'format' => 'pdf']) }}" 
                     class="btn btn-xs btn-primary">
                    <i data-lucide="file-text" class="w-3 h-3 mr-1"></i>
                    PDF
                  </a>
                </div>
              </div>
            </div>

            <!-- Last Month Quick Report -->
            <div class="bg-green-50 rounded-lg p-3 border border-green-200">
              <div class="text-center">
                <div class="w-8 h-8 bg-green-200 rounded-full flex items-center justify-center mx-auto mb-2">
                  <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                </div>
                <h4 class="font-semibold text-sm text-green-800">{{ now()->subMonth()->format('M Y') }}</h4>
                <p class="text-xs text-green-600 mb-2">Complete data</p>
                <div class="flex gap-1">
                  <a href="{{ route('facility_reservations.generate_monthly_report', ['month' => now()->subMonth()->month, 'year' => now()->subMonth()->year, 'format' => 'excel']) }}" 
                     class="btn btn-xs btn-outline">
                    <i data-lucide="download" class="w-3 h-3 mr-1"></i>
                    Excel
                  </a>
                  <a href="{{ route('facility_reservations.generate_monthly_report', ['month' => now()->subMonth()->month, 'year' => now()->subMonth()->year, 'format' => 'pdf']) }}" 
                     class="btn btn-xs btn-primary">
                    <i data-lucide="file-text" class="w-3 h-3 mr-1"></i>
                    PDF
                  </a>
                </div>
              </div>
            </div>

            <!-- Custom Report -->
            <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
              <div class="text-center">
                <div class="w-8 h-8 bg-purple-200 rounded-full flex items-center justify-center mx-auto mb-2">
                  <i data-lucide="settings" class="w-4 h-4 text-purple-600"></i>
                </div>
                <h4 class="font-semibold text-sm text-purple-800">Custom</h4>
                <p class="text-xs text-purple-600 mb-2">Any period</p>
                <a href="{{ route('facility_reservations.monthly_reports') }}" 
                   class="btn btn-xs btn-primary">
                  <i data-lucide="cog" class="w-3 h-3 mr-1"></i>
                  Create
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4 min-w-0">
          <!-- Reservation Trends Over Time -->
          <div class="bg-white rounded-lg shadow-sm p-3">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-md font-semibold text-gray-800">Reservation Trends Over Time</h3>
              <div class="flex gap-2">
                <button onclick="toggleTrendChart('monthly')" class="btn btn-xs btn-outline trend-btn active" data-period="monthly">Monthly</button>
                <button onclick="toggleTrendChart('weekly')" class="btn btn-xs btn-outline trend-btn" data-period="weekly">Weekly</button>
                <button onclick="toggleTrendChart('daily')" class="btn btn-xs btn-outline trend-btn" data-period="daily">Daily</button>
              </div>
            </div>
            <div class="chart-container">
              <canvas id="trendsChart"></canvas>
            </div>
            <div class="mt-2 text-xs text-gray-600">
              <span id="trendSummary">Showing monthly reservation trends</span>
            </div>
          </div>

          <!-- Peak Hours Analysis -->
          <div class="bg-white rounded-lg shadow-sm p-3">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-md font-semibold text-gray-800">Peak Hours Analysis</h3>
              <div class="flex gap-2">
                <button onclick="togglePeakChart('bar')" class="btn btn-xs btn-outline peak-btn active" data-type="bar">Bar</button>
                <button onclick="togglePeakChart('line')" class="btn btn-xs btn-outline peak-btn" data-type="line">Line</button>
                <button onclick="togglePeakChart('doughnut')" class="btn btn-xs btn-outline peak-btn" data-type="doughnut">Pie</button>
              </div>
            </div>
            <div class="chart-container">
              <canvas id="peakHoursChart"></canvas>
            </div>
            <div class="mt-2 text-xs text-gray-600">
              <span id="peakSummary">Most active booking hours</span>
            </div>
          </div>
        </div>

        <!-- Additional Trend Analysis Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-4 min-w-0">
          <!-- Facility Usage Trends -->
          <div class="bg-white rounded-lg shadow-sm p-3">
            <h3 class="text-md font-semibold mb-3 text-gray-800">Facility Usage Trends</h3>
            <div class="chart-container" style="height: 150px;">
              <canvas id="facilityTrendsChart"></canvas>
            </div>
          </div>

          <!-- Status Distribution -->
          <div class="bg-white rounded-lg shadow-sm p-3">
            <h3 class="text-md font-semibold mb-3 text-gray-800">Status Distribution</h3>
            <div class="chart-container" style="height: 150px;">
              <canvas id="statusDistributionChart"></canvas>
            </div>
          </div>

          <!-- Revenue Trends -->
          <div class="bg-white rounded-lg shadow-sm p-3">
            <h3 class="text-md font-semibold mb-3 text-gray-800">Revenue Trends</h3>
            <div class="chart-container" style="height: 150px;">
              <canvas id="revenueTrendsChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Facility Usage and User Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4 min-w-0">
          <!-- Top Facilities -->
          <div class="bg-white rounded-lg shadow-sm p-3">
            <h3 class="text-md font-semibold mb-3 text-gray-800">Top Facilities</h3>
            <div class="space-y-3">
              @if($analytics['facility_usage']->count() > 0)
                @foreach($analytics['facility_usage'] as $facility)
                  <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i data-lucide="building" class="w-4 h-4 text-blue-600"></i>
                      </div>
                      <div>
                        <p class="font-medium text-sm">{{ $facility->name }}</p>
                        <p class="text-xs text-gray-500">{{ $facility->location }}</p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="font-bold text-blue-600 text-sm">{{ $facility->reservations_count }}</p>
                    </div>
                  </div>
                @endforeach
              @else
                <p class="text-gray-500 text-sm text-center py-4">No data available</p>
              @endif
            </div>
          </div>

          <!-- Top Users -->
          <div class="bg-white rounded-lg shadow-sm p-3">
            <h3 class="text-md font-semibold mb-3 text-gray-800">Top Users</h3>
            <div class="space-y-3">
              @if($analytics['user_activity']->count() > 0)
                @foreach($analytics['user_activity'] as $user)
                  <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4 text-green-600"></i>
                      </div>
                      <div>
                        <p class="font-medium text-sm">{{ $user->reserver->name ?? 'Unknown User' }}</p>
                        <p class="text-xs text-gray-500">ID: {{ $user->reserved_by }}</p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="font-bold text-green-600 text-sm">{{ $user->reservation_count }}</p>
                    </div>
                  </div>
                @endforeach
              @else
                <p class="text-gray-500 text-sm text-center py-4">No data available</p>
              @endif
            </div>
          </div>
        </div>

        <!-- Pending Reservations -->
        @if($pendingReservations->count() > 0)
          <div class="bg-white rounded-lg shadow-sm p-3 mb-4">
            <h3 class="text-md font-semibold mb-3 text-gray-800">Pending Reservations</h3>
          <div class="space-y-3">
            @foreach($pendingReservations as $reservation)
              <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="building" class="w-4 h-4 text-gray-600"></i>
                    <span class="font-medium text-sm">{{ $reservation->facility->name }}</span>
                  </div>
                  <div class="text-sm text-gray-600">
                    {{ $reservation->reserver->name ?? 'Unknown' }} • 
                    {{ $reservation->start_time->format('M d, g:i A') }}
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <a href="{{ route('facility_reservations.show', $reservation->id) }}" 
                     class="btn btn-sm btn-outline">
                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                    View
                  </a>
                  <form action="{{ route('facility_reservations.approve', $reservation->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">
                      <i data-lucide="check" class="w-4 h-4 mr-1"></i>
                      Approve
                    </button>
                  </form>
                  <form action="{{ route('facility_reservations.deny', $reservation->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-error">
                      <i data-lucide="x" class="w-4 h-4 mr-1"></i>
                      Deny
                    </button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>
        </div>
        @endif

        <!-- Recent Reservations -->
        <div class="bg-white rounded-lg shadow-sm p-3">
          <h3 class="text-md font-semibold mb-3 text-gray-800">Recent Reservations</h3>
          <div class="space-y-3">
            @foreach($recentReservations as $reservation)
              <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="building" class="w-4 h-4 text-gray-600"></i>
                    <span class="font-medium text-sm">{{ $reservation->facility->name }}</span>
                    @if($reservation->status === 'approved')
                      <span class="badge badge-success">Approved</span>
                    @elseif($reservation->status === 'pending')
                      <span class="badge badge-warning">Pending</span>
                    @elseif($reservation->status === 'denied')
                      <span class="badge badge-error">Denied</span>
                    @else
                      <span class="badge badge-neutral">{{ ucfirst($reservation->status) }}</span>
                    @endif
                  </div>
                  <div class="text-sm text-gray-600">
                    {{ $reservation->reserver->name ?? 'Unknown' }} • 
                    {{ $reservation->start_time->format('M d, g:i A') }}
                  </div>
                </div>
                <a href="{{ route('facility_reservations.show', $reservation->id) }}" 
                   class="btn btn-sm btn-outline">
                  <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                  View
                </a>
              </div>
            @endforeach
          </div>
        </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    // Hide loading indicator on page load
    window.addEventListener('load', function() {
      const loadingIndicator = document.getElementById('loadingIndicator');
      if (loadingIndicator) {
        loadingIndicator.classList.add('hidden');
        loadingIndicator.classList.remove('flex');
      }
    });

    // Also hide loading indicator when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
      const loadingIndicator = document.getElementById('loadingIndicator');
      if (loadingIndicator) {
        loadingIndicator.classList.add('hidden');
        loadingIndicator.classList.remove('flex');
      }
      
      // Ensure main content is visible
      const mainContent = document.querySelector('.analytics-container');
      if (mainContent) {
        mainContent.style.display = 'block';
        mainContent.style.visibility = 'visible';
      }
      
      // Debug: Log that content should be visible
      console.log('Analytics page loaded, content should be visible');
    });

    // Show loading indicator when refreshing
    function refreshAnalytics() {
      const loadingIndicator = document.getElementById('loadingIndicator');
      if (loadingIndicator) {
        loadingIndicator.classList.remove('hidden');
        loadingIndicator.classList.add('flex');
      }
      location.reload();
    }

    // Global chart instances
    let trendsChart = null;
    let peakHoursChart = null;
    let facilityTrendsChart = null;
    let statusDistributionChart = null;
    let revenueTrendsChart = null;

    // Initialize charts
    document.addEventListener('DOMContentLoaded', function() {
      try {
        initializeTrendsChart();
        initializePeakHoursChart();
        initializeFacilityTrendsChart();
        initializeStatusDistributionChart();
        initializeRevenueTrendsChart();
      } catch (error) {
        console.error('Error initializing charts:', error);
      }
    });

    function initializeTrendsChart() {
      const trendsCtx = document.getElementById('trendsChart');
      if (trendsCtx) {
        const trendsData = @json($analytics['reservation_trends']);
        
        trendsChart = new Chart(trendsCtx.getContext('2d'), {
          type: 'line',
          data: {
            labels: trendsData.map(item => {
              const date = new Date(item.month + '-01');
              return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
              label: 'Reservations',
              data: trendsData.map(item => item.count),
              borderColor: 'rgb(59, 130, 246)',
              backgroundColor: 'rgba(59, 130, 246, 0.1)',
              tension: 0.4,
              fill: true,
              pointRadius: 4,
              pointHoverRadius: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                mode: 'index',
                intersect: false,
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  stepSize: 1
                }
              },
              x: {
                grid: {
                  display: false
                }
              }
            },
            interaction: {
              mode: 'nearest',
              axis: 'x',
              intersect: false
            }
          }
        });
      }
    }

    function initializePeakHoursChart() {
      const peakHoursCtx = document.getElementById('peakHoursChart');
      if (peakHoursCtx) {
        const peakHoursData = @json($analytics['peak_hours']);
        
        peakHoursChart = new Chart(peakHoursCtx.getContext('2d'), {
          type: 'bar',
          data: {
            labels: peakHoursData.map(item => {
              const hour = item.hour;
              return hour === 0 ? '12 AM' : 
                     hour < 12 ? hour + ' AM' : 
                     hour === 12 ? '12 PM' : 
                     (hour - 12) + ' PM';
            }),
            datasets: [{
              label: 'Bookings',
              data: peakHoursData.map(item => item.count),
              backgroundColor: 'rgba(16, 185, 129, 0.8)',
              borderColor: 'rgb(16, 185, 129)',
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
                beginAtZero: true,
                ticks: {
                  stepSize: 1
                }
              }
            }
          }
        });
      }
    }

    function initializeFacilityTrendsChart() {
      const facilityTrendsCtx = document.getElementById('facilityTrendsChart');
      if (facilityTrendsCtx) {
        const facilityData = @json($analytics['facility_usage']);
        
        facilityTrendsChart = new Chart(facilityTrendsCtx.getContext('2d'), {
          type: 'doughnut',
          data: {
            labels: facilityData.map(f => f.name),
            datasets: [{
              data: facilityData.map(f => f.reservations_count),
              backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(139, 92, 246, 0.8)'
              ],
              borderWidth: 2,
              borderColor: '#fff'
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
            }
          }
        });
      }
    }

    function initializeStatusDistributionChart() {
      const statusCtx = document.getElementById('statusDistributionChart');
      if (statusCtx) {
        const statusData = {
          approved: @json($analytics['overview']['approved_reservations']),
          pending: @json($analytics['overview']['pending_reservations']),
          denied: @json($analytics['overview']['denied_reservations'])
        };
        
        statusDistributionChart = new Chart(statusCtx.getContext('2d'), {
          type: 'pie',
          data: {
            labels: ['Approved', 'Pending', 'Denied'],
            datasets: [{
              data: [statusData.approved, statusData.pending, statusData.denied],
              backgroundColor: [
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)'
              ],
              borderWidth: 2,
              borderColor: '#fff'
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
            }
          }
        });
      }
    }

    function initializeRevenueTrendsChart() {
      const revenueCtx = document.getElementById('revenueTrendsChart');
      if (revenueCtx) {
        const revenueData = @json($analytics['revenue_analytics']);
        
        revenueTrendsChart = new Chart(revenueCtx.getContext('2d'), {
          type: 'line',
          data: {
            labels: ['Total Revenue', 'Monthly Revenue', 'Avg Booking Value'],
            datasets: [{
              label: 'Revenue ($)',
              data: [revenueData.total_revenue, revenueData.monthly_revenue, revenueData.average_booking_value],
              borderColor: 'rgb(34, 197, 94)',
              backgroundColor: 'rgba(34, 197, 94, 0.1)',
              tension: 0.4,
              fill: true,
              pointRadius: 5,
              pointHoverRadius: 7
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
                beginAtZero: true,
                ticks: {
                  callback: function(value) {
                    return '$' + value.toFixed(2);
                  }
                }
              }
            }
          }
        });
      }
    }

    // Chart toggle functions
    function toggleTrendChart(period) {
      // Update button states
      document.querySelectorAll('.trend-btn').forEach(btn => btn.classList.remove('active', 'btn-primary'));
      document.querySelector(`[data-period="${period}"]`).classList.add('active', 'btn-primary');
      
      // Update summary text
      const summary = document.getElementById('trendSummary');
      summary.textContent = `Showing ${period} reservation trends`;
      
      // Here you would typically fetch new data based on the period
      // For now, we'll just show the current data
      console.log(`Switched to ${period} trend view`);
    }

    function togglePeakChart(type) {
      if (!peakHoursChart) return;
      
      // Update button states
      document.querySelectorAll('.peak-btn').forEach(btn => btn.classList.remove('active', 'btn-primary'));
      document.querySelector(`[data-type="${type}"]`).classList.add('active', 'btn-primary');
      
      // Update chart type
      peakHoursChart.config.type = type;
      peakHoursChart.update();
      
      // Update summary text
      const summary = document.getElementById('peakSummary');
      summary.textContent = `Most active booking hours (${type} view)`;
    }

    // Export analytics function
    function exportAnalytics() {
      // Create comprehensive report data
      const reportData = {
        overview: @json($analytics['overview']),
        revenue: @json($analytics['revenue_analytics']),
        facility_usage: @json($analytics['facility_usage']),
        user_activity: @json($analytics['user_activity']),
        trends: @json($analytics['reservation_trends']),
        peak_hours: @json($analytics['peak_hours']),
        generated_at: new Date().toISOString()
      };

      // Create CSV content
      const csvContent = [
        ['Metric', 'Value'],
        ['Total Reservations', reportData.overview.total_reservations],
        ['Approval Rate', reportData.overview.approval_rate + '%'],
        ['Total Revenue', '$' + reportData.revenue.total_revenue],
        ['Monthly Revenue', '$' + reportData.revenue.monthly_revenue],
        ['Average Booking Value', '$' + reportData.revenue.average_booking_value],
        ['Active Users', reportData.overview.active_users],
        ['Total Facilities', reportData.overview.total_facilities]
      ].map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');

      // Download CSV
      const blob = new Blob([csvContent], { type: 'text/csv' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `admin-analytics-${new Date().toISOString().split('T')[0]}.csv`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
    }

  </script>
</body>
</html>
image.png