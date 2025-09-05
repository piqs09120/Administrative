<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Analytics - Facility Reservations - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @vite(['resources/css/soliera.css'])
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
        <!-- Back button and title -->
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center">
            <a href="{{ route('facility_reservations.index') }}" class="btn btn-ghost btn-sm mr-4">
              <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back to Reservations
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Admin Analytics Dashboard</h1>
          </div>
          <div class="flex items-center gap-3">
            <button onclick="exportAnalytics()" class="btn btn-outline btn-sm">
              <i data-lucide="download" class="w-4 h-4 mr-2"></i>
              Export Report
            </button>
            <button onclick="refreshAnalytics()" class="btn btn-primary btn-sm">
              <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
              Refresh
            </button>
          </div>
        </div>

        <!-- Overview Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Reservations -->
          <div class="card bg-white shadow-lg">
            <div class="card-body p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-600">Total Reservations</p>
                  <p class="text-3xl font-bold text-blue-600">{{ $analytics['overview']['total_reservations'] }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ $analytics['overview']['this_month_reservations'] }} this month</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                  <i data-lucide="calendar" class="w-6 h-6 text-blue-600"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Approval Rate -->
          <div class="card bg-white shadow-lg">
            <div class="card-body p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-600">Approval Rate</p>
                  <p class="text-3xl font-bold text-green-600">{{ number_format($analytics['overview']['approval_rate'], 1) }}%</p>
                  <p class="text-xs text-gray-500 mt-1">{{ $analytics['overview']['approved_reservations'] }} approved</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                  <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Pending Reviews -->
          <div class="card bg-white shadow-lg">
            <div class="card-body p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-600">Pending Reviews</p>
                  <p class="text-3xl font-bold text-yellow-600">{{ $analytics['overview']['pending_reservations'] }}</p>
                  <p class="text-xs text-gray-500 mt-1">Requires attention</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                  <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Active Users -->
          <div class="card bg-white shadow-lg">
            <div class="card-body p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-600">Active Users</p>
                  <p class="text-3xl font-bold text-purple-600">{{ $analytics['overview']['active_users'] }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ $analytics['overview']['total_facilities'] }} facilities</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                  <i data-lucide="users" class="w-6 h-6 text-purple-600"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Revenue Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
          <!-- Total Revenue -->
          <div class="card bg-white shadow-lg">
            <div class="card-body p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                  <p class="text-2xl font-bold text-green-600">${{ number_format($analytics['revenue_analytics']['total_revenue'], 2) }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                  <i data-lucide="dollar-sign" class="w-5 h-5 text-green-600"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Monthly Revenue -->
          <div class="card bg-white shadow-lg">
            <div class="card-body p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-600">This Month</p>
                  <p class="text-2xl font-bold text-blue-600">${{ number_format($analytics['revenue_analytics']['monthly_revenue'], 2) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                  <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Average Booking Value -->
          <div class="card bg-white shadow-lg">
            <div class="card-body p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-600">Avg Booking Value</p>
                  <p class="text-2xl font-bold text-purple-600">${{ number_format($analytics['revenue_analytics']['average_booking_value'], 2) }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                  <i data-lucide="bar-chart" class="w-5 h-5 text-purple-600"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Reservation Trends Chart -->
          <div class="card bg-white shadow-lg">
            <div class="card-body">
              <h3 class="card-title text-lg font-semibold mb-4">Reservation Trends (Last 12 Months)</h3>
              <canvas id="trendsChart" width="400" height="200"></canvas>
            </div>
          </div>

          <!-- Peak Hours Chart -->
          <div class="card bg-white shadow-lg">
            <div class="card-body">
              <h3 class="card-title text-lg font-semibold mb-4">Peak Booking Hours</h3>
              <canvas id="peakHoursChart" width="400" height="200"></canvas>
            </div>
          </div>
        </div>

        <!-- Facility Usage and User Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Top Facilities -->
          <div class="card bg-white shadow-lg">
            <div class="card-body">
              <h3 class="card-title text-lg font-semibold mb-4">Most Used Facilities</h3>
              <div class="space-y-3">
                @foreach($analytics['facility_usage'] as $facility)
                  <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i data-lucide="building" class="w-4 h-4 text-blue-600"></i>
                      </div>
                      <div>
                        <p class="font-medium">{{ $facility->name }}</p>
                        <p class="text-sm text-gray-500">{{ $facility->location }}</p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="font-bold text-blue-600">{{ $facility->reservations_count }}</p>
                      <p class="text-xs text-gray-500">bookings</p>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <!-- Top Users -->
          <div class="card bg-white shadow-lg">
            <div class="card-body">
              <h3 class="card-title text-lg font-semibold mb-4">Most Active Users</h3>
              <div class="space-y-3">
                @foreach($analytics['user_activity'] as $user)
                  <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4 text-green-600"></i>
                      </div>
                      <div>
                        <p class="font-medium">{{ $user->reserver->name ?? 'Unknown User' }}</p>
                        <p class="text-sm text-gray-500">User ID: {{ $user->reserved_by }}</p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="font-bold text-green-600">{{ $user->reservation_count }}</p>
                      <p class="text-xs text-gray-500">reservations</p>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Reservations -->
        @if($pendingReservations->count() > 0)
        <div class="card bg-white shadow-lg mb-8">
          <div class="card-body">
            <h3 class="card-title text-lg font-semibold mb-4">Pending Reservations Requiring Review</h3>
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th>Facility</th>
                    <th>User</th>
                    <th>Date & Time</th>
                    <th>Purpose</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pendingReservations as $reservation)
                    <tr>
                      <td>
                        <div class="flex items-center gap-3">
                          <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="building" class="w-4 h-4 text-gray-600"></i>
                          </div>
                          <div>
                            <div class="font-medium">{{ $reservation->facility->name }}</div>
                            <div class="text-sm text-gray-500">{{ $reservation->facility->location }}</div>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="font-medium">{{ $reservation->reserver->name ?? 'Unknown' }}</div>
                        <div class="text-sm text-gray-500">{{ $reservation->reserver->email ?? '' }}</div>
                      </td>
                      <td>
                        <div class="font-medium">{{ $reservation->start_time->format('M d, Y') }}</div>
                        <div class="text-sm text-gray-500">
                          {{ $reservation->start_time->format('g:i A') }} - {{ $reservation->end_time->format('g:i A') }}
                        </div>
                      </td>
                      <td>
                        <div class="max-w-xs truncate">
                          {{ $reservation->purpose ?: 'No purpose specified' }}
                        </div>
                      </td>
                      <td>
                        <div class="flex items-center gap-2">
                          <a href="{{ route('facility_reservations.show', $reservation->id) }}" 
                             class="btn btn-ghost btn-xs">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                          </a>
                          <form action="{{ route('facility_reservations.approve', $reservation->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-xs">
                              <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                          </form>
                          <form action="{{ route('facility_reservations.deny', $reservation->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-error btn-xs">
                              <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
        @endif

        <!-- Recent Reservations -->
        <div class="card bg-white shadow-lg">
          <div class="card-body">
            <h3 class="card-title text-lg font-semibold mb-4">Recent Reservations</h3>
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th>Facility</th>
                    <th>User</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($recentReservations as $reservation)
                    <tr>
                      <td>
                        <div class="flex items-center gap-3">
                          <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="building" class="w-4 h-4 text-gray-600"></i>
                          </div>
                          <div>
                            <div class="font-medium">{{ $reservation->facility->name }}</div>
                            <div class="text-sm text-gray-500">{{ $reservation->facility->location }}</div>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="font-medium">{{ $reservation->reserver->name ?? 'Unknown' }}</div>
                        <div class="text-sm text-gray-500">{{ $reservation->reserver->email ?? '' }}</div>
                      </td>
                      <td>
                        <div class="font-medium">{{ $reservation->start_time->format('M d, Y') }}</div>
                        <div class="text-sm text-gray-500">
                          {{ $reservation->start_time->format('g:i A') }} - {{ $reservation->end_time->format('g:i A') }}
                        </div>
                      </td>
                      <td>
                        @if($reservation->status === 'approved')
                          <span class="badge badge-success">Approved</span>
                        @elseif($reservation->status === 'pending')
                          <span class="badge badge-warning">Pending</span>
                        @elseif($reservation->status === 'denied')
                          <span class="badge badge-error">Denied</span>
                        @else
                          <span class="badge badge-neutral">{{ ucfirst($reservation->status) }}</span>
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('facility_reservations.show', $reservation->id) }}" 
                           class="btn btn-ghost btn-xs">
                          <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    // Initialize charts
    document.addEventListener('DOMContentLoaded', function() {
      // Reservation trends chart
      const trendsCtx = document.getElementById('trendsChart').getContext('2d');
      const trendsData = @json($analytics['reservation_trends']);
      
      new Chart(trendsCtx, {
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
              beginAtZero: true,
              ticks: {
                stepSize: 1
              }
            }
          }
        }
      });

      // Peak hours chart
      const peakHoursCtx = document.getElementById('peakHoursChart').getContext('2d');
      const peakHoursData = @json($analytics['peak_hours']);
      
      new Chart(peakHoursCtx, {
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
    });

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

    // Refresh analytics function
    function refreshAnalytics() {
      location.reload();
    }
  </script>
</body>
</html>
