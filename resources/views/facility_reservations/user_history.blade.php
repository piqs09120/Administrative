<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Reservation History - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @vite(['resources/css/soliera.css'])
  <style>
    .chart-container {
      position: relative;
      height: 300px;
      width: 100%;
      min-height: 250px;
    }
    .card {
      min-height: auto;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    .table th, .table td {
      padding: 0.75rem;
      font-size: 0.875rem;
    }
    .badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
    }
    .btn {
      font-size: 0.875rem;
      padding: 0.5rem 1rem;
    }
    @media (max-width: 768px) {
      .grid-cols-1 {
        gap: 1rem;
      }
      .card-body {
        padding: 1rem;
      }
      .text-3xl {
        font-size: 1.875rem;
      }
      .chart-container {
        height: 250px;
      }
      .table th, .table td {
        padding: 0.5rem;
        font-size: 0.8rem;
      }
    }
    @media (max-width: 480px) {
      .text-3xl {
        font-size: 1.5rem;
      }
      .chart-container {
        height: 200px;
      }
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
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
              <a href="{{ route('facility_reservations.index') }}" class="btn btn-ghost btn-sm" title="Back to Reservations">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
              </a>
              <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">My Reservation History</h1>
                <p class="text-gray-600">View and manage your facility reservation history</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <button onclick="exportHistory()" class="btn btn-outline">
                <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                Export
              </button>
              <a href="{{ route('facility_reservations.create') }}" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                New Reservation
              </a>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Reservations -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-primary cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-base-content/70">Total Reservations</p>
                  <p class="text-3xl font-bold text-primary">{{ $analytics['total_reservations'] }}</p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                  <i data-lucide="calendar" class="w-6 h-6 text-primary"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Approved Reservations -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-success cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-base-content/70">Approved</p>
                  <p class="text-3xl font-bold text-success">{{ $analytics['approved_reservations'] }}</p>
                </div>
                <div class="w-12 h-12 bg-success/10 rounded-full flex items-center justify-center group-hover:bg-success/20 transition-colors">
                  <i data-lucide="check-circle" class="w-6 h-6 text-success"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Pending Reservations -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-warning cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-base-content/70">Pending</p>
                  <p class="text-3xl font-bold text-warning">{{ $analytics['pending_reservations'] }}</p>
                </div>
                <div class="w-12 h-12 bg-warning/10 rounded-full flex items-center justify-center group-hover:bg-warning/20 transition-colors">
                  <i data-lucide="clock" class="w-6 h-6 text-warning"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Upcoming Reservations -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-secondary cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-base-content/70">Upcoming</p>
                  <p class="text-3xl font-bold text-secondary">{{ $analytics['upcoming_reservations'] }}</p>
                </div>
                <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center group-hover:bg-secondary/20 transition-colors">
                  <i data-lucide="calendar-days" class="w-6 h-6 text-secondary"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Monthly Trends Chart -->
          <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
              <h3 class="card-title text-lg font-semibold mb-4">Reservation Trends (Last 6 Months)</h3>
              <div class="chart-container">
                <canvas id="monthlyChart"></canvas>
              </div>
            </div>
          </div>

          <!-- Peak Booking Times Chart -->
          <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
              <h3 class="card-title text-lg font-semibold mb-4">Peak Booking Times</h3>
              <div class="chart-container">
                <canvas id="peakTimesChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Most Used Facility -->
        @if($analytics['most_used_facility'])
        <div class="card bg-base-100 shadow-xl mb-8">
          <div class="card-body">
            <h3 class="card-title text-lg font-semibold mb-4">Most Used Facility</h3>
            <div class="flex items-center justify-between p-4 bg-primary/10 rounded-lg">
              <div>
                <h4 class="font-semibold text-primary">{{ $analytics['most_used_facility']->name }}</h4>
                <p class="text-sm text-primary/70">{{ $analytics['most_used_facility']->usage_count }} reservations</p>
              </div>
              <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center">
                <i data-lucide="trophy" class="w-8 h-8 text-primary"></i>
              </div>
            </div>
          </div>
        </div>
        @endif

        <!-- Reservation History Table -->
        <div class="card bg-base-100 shadow-xl">
          <div class="card-body">
            <h3 class="card-title text-lg font-semibold mb-6">Reservation History</h3>
            
            @if($reservations->count() > 0)
              <div class="table-responsive overflow-x-auto">
                <table class="table table-zebra w-full min-w-full">
                  <thead>
                    <tr>
                      <th>Facility</th>
                      <th>Date & Time</th>
                      <th>Purpose</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($reservations as $reservation)
                      <tr>
                        <td>
                          <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                              <i data-lucide="building" class="w-5 h-5 text-gray-600"></i>
                            </div>
                            <div>
                              <div class="font-semibold">{{ $reservation->facility->name }}</div>
                              <div class="text-sm text-gray-500">{{ $reservation->facility->location }}</div>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div>
                            <div class="font-medium">{{ $reservation->start_time->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-500">
                              {{ $reservation->start_time->format('g:i A') }} - {{ $reservation->end_time->format('g:i A') }}
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="max-w-xs truncate">
                            {{ $reservation->purpose ?: 'No purpose specified' }}
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
                          <div class="flex items-center gap-2">
                            <a href="{{ route('facility_reservations.show', $reservation->id) }}" 
                               class="btn btn-ghost btn-xs">
                              <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            @if($reservation->status === 'pending' && $reservation->start_time > now())
                              <button onclick="cancelReservation({{ $reservation->id }})" 
                                      class="btn btn-ghost btn-xs text-red-600">
                                <i data-lucide="x" class="w-4 h-4"></i>
                              </button>
                            @endif
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <div class="mt-6">
                {{ $reservations->links() }}
              </div>
            @else
              <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <i data-lucide="calendar-x" class="w-10 h-10 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-600 mb-2">No Reservations Found</h3>
                <p class="text-gray-500 text-sm mb-4">You haven't made any facility reservations yet.</p>
                <a href="{{ route('facility_reservations.create') }}" class="btn btn-primary">
                  <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                  Make Your First Reservation
                </a>
              </div>
            @endif
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    // Initialize charts
    document.addEventListener('DOMContentLoaded', function() {
      // Monthly trends chart
      const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
      const monthlyData = @json($analytics['monthly_stats']);
      
      new Chart(monthlyCtx, {
        type: 'line',
        data: {
          labels: monthlyData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
          }),
          datasets: [{
            label: 'Reservations',
            data: monthlyData.map(item => item.count),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          aspectRatio: 2,
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

      // Peak booking times chart
      const peakTimesCtx = document.getElementById('peakTimesChart').getContext('2d');
      const peakTimesData = @json($analytics['peak_booking_times']);
      
      new Chart(peakTimesCtx, {
        type: 'bar',
        data: {
          labels: peakTimesData.map(item => {
            const hour = item.hour;
            return hour === 0 ? '12 AM' : 
                   hour < 12 ? hour + ' AM' : 
                   hour === 12 ? '12 PM' : 
                   (hour - 12) + ' PM';
          }),
          datasets: [{
            label: 'Bookings',
            data: peakTimesData.map(item => item.count),
            backgroundColor: 'rgba(16, 185, 129, 0.8)',
            borderColor: 'rgb(16, 185, 129)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          aspectRatio: 2,
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

    // Export history function
    function exportHistory() {
      // Create CSV data
      const reservations = @json($reservations->items());
      const csvContent = [
        ['Facility', 'Date', 'Start Time', 'End Time', 'Purpose', 'Status'],
        ...reservations.map(r => [
          r.facility.name,
          r.start_time,
          r.start_time,
          r.end_time,
          r.purpose || '',
          r.status
        ])
      ].map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');

      // Download CSV
      const blob = new Blob([csvContent], { type: 'text/csv' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `reservation-history-${new Date().toISOString().split('T')[0]}.csv`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
    }

    // Cancel reservation function
    function cancelReservation(reservationId) {
      if (confirm('Are you sure you want to cancel this reservation?')) {
        // Implement cancellation logic
        fetch(`/facility_reservations/${reservationId}/cancel`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Error canceling reservation: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error canceling reservation. Please try again.');
        });
      }
    }
  </script>

  @include('partials.soliera_js')
</body>
</html>
