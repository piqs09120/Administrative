<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Monthly Facility Reports - Soliera</title>
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

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Monthly Facility Reports</h1>
            <p class="text-gray-600 mt-1" style="color: var(--color-charcoal-ink); opacity: 0.7;">Generate and analyze monthly facility usage reports</p>
          </div>
          <div class="flex gap-3">
            <a href="{{ route('facility_reservations.admin_analytics') }}" class="btn btn-outline" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);" title="Back to Analytics">
              <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
          </div>
        </div>

        <!-- Report Generation Form -->
        <div class="card bg-white shadow-xl mb-6" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
          <div class="card-body">
            <h2 class="card-title text-xl mb-4 flex items-center" style="color: var(--color-charcoal-ink);">
              <i data-lucide="file-bar-chart" class="w-6 h-6 mr-2" style="color: var(--color-regal-navy);"></i>
              Generate Monthly Report
            </h2>
            
            <form id="reportForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
              @csrf
              <div class="form-control">
                <label class="label" style="color: var(--color-charcoal-ink);">
                  <span class="label-text font-medium">Month</span>
                </label>
                <select name="month" id="monthSelect" class="select select-bordered" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                  <option value="">Select Month</option>
                  @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $i == now()->month ? 'selected' : '' }}>
                      {{ \Carbon\Carbon::createFromDate(now()->year, $i)->format('F') }}
                    </option>
                  @endfor
                </select>
              </div>

              <div class="form-control">
                <label class="label" style="color: var(--color-charcoal-ink);">
                  <span class="label-text font-medium">Year</span>
                </label>
                <select name="year" id="yearSelect" class="select select-bordered" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                  <option value="">Select Year</option>
                  @for($i = now()->year; $i >= 2020; $i--)
                    <option value="{{ $i }}" {{ $i == now()->year ? 'selected' : '' }}>{{ $i }}</option>
                  @endfor
                </select>
              </div>

              <div class="form-control">
                <label class="label" style="color: var(--color-charcoal-ink);">
                  <span class="label-text font-medium">Facility (Optional)</span>
                </label>
                <select name="facility_id" id="facilitySelect" class="select select-bordered" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                  <option value="">All Facilities</option>
                  @foreach($facilities as $facility)
                    <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-control">
                <label class="label" style="color: var(--color-charcoal-ink);">
                  <span class="label-text font-medium">Format</span>
                </label>
                <select name="format" id="formatSelect" class="select select-bordered" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                  <option value="excel">Excel (.xlsx)</option>
                  <option value="pdf">PDF</option>
                  <option value="json">JSON</option>
                </select>
              </div>
            </form>

            <div class="flex gap-3 mt-6">
              <button type="button" id="generateReportBtn" class="btn" style="background-color: var(--color-modern-teal); color: var(--color-white); border-color: var(--color-modern-teal);">
                <i data-lucide="download" class="w-4 h-4 mr-2"></i>Generate Report
              </button>
              <button type="button" id="previewReportBtn" class="btn btn-outline" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">
                <i data-lucide="eye" class="w-4 h-4 mr-2"></i>Preview Data
              </button>
            </div>
          </div>
        </div>

        <!-- Quick Access to Available Reports -->
        @if($availablePeriods->count() > 0)
        <div class="card bg-white shadow-xl mb-6" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
          <div class="card-body">
            <h2 class="card-title text-xl mb-4 flex items-center" style="color: var(--color-charcoal-ink);">
              <i data-lucide="clock" class="w-6 h-6 mr-2" style="color: var(--color-regal-navy);"></i>
              Quick Access - Available Reports
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              @foreach($availablePeriods->take(6) as $period)
                <div class="card bg-base-100 border" style="border-color: var(--color-snow-mist);">
                  <div class="card-body p-4">
                    <h3 class="font-semibold" style="color: var(--color-charcoal-ink);">{{ $period['month_name'] }}</h3>
                    <div class="flex gap-2 mt-3">
                      <button class="btn btn-sm btn-outline" style="color: var(--color-modern-teal); border-color: var(--color-modern-teal);" 
                              onclick="quickGenerateReport({{ $period['month'] }}, {{ $period['year'] }}, 'excel')">
                        <i data-lucide="download" class="w-3 h-3 mr-1"></i>Excel
                      </button>
                      <button class="btn btn-sm btn-outline" style="color: var(--color-golden-ember); border-color: var(--color-golden-ember);" 
                              onclick="quickGenerateReport({{ $period['month'] }}, {{ $period['year'] }}, 'pdf')">
                        <i data-lucide="file-text" class="w-3 h-3 mr-1"></i>PDF
                      </button>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        @endif

        <!-- Report Preview Section -->
        <div id="reportPreview" class="hidden">
          <div class="card bg-white shadow-xl mb-6" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
            <div class="card-body">
              <h2 class="card-title text-xl mb-4 flex items-center" style="color: var(--color-charcoal-ink);">
                <i data-lucide="bar-chart-3" class="w-6 h-6 mr-2" style="color: var(--color-regal-navy);"></i>
                Report Preview
              </h2>
              
              <!-- Summary Cards -->
              <div id="summaryCards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Summary cards will be populated by JavaScript -->
              </div>

              <!-- Charts -->
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="card bg-base-100 border" style="border-color: var(--color-snow-mist);">
                  <div class="card-body">
                    <h3 class="font-semibold mb-4" style="color: var(--color-charcoal-ink);">Facility Usage</h3>
                    <canvas id="facilityUsageChart" width="400" height="200"></canvas>
                  </div>
                </div>
                
                <div class="card bg-base-100 border" style="border-color: var(--color-snow-mist);">
                  <div class="card-body">
                    <h3 class="font-semibold mb-4" style="color: var(--color-charcoal-ink);">Daily Usage Pattern</h3>
                    <canvas id="dailyUsageChart" width="400" height="200"></canvas>
                  </div>
                </div>
              </div>

              <!-- Reservations Table -->
              <div class="card bg-base-100 border" style="border-color: var(--color-snow-mist);">
                <div class="card-body">
                  <h3 class="font-semibold mb-4" style="color: var(--color-charcoal-ink);">Reservations Details</h3>
                  <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                      <thead>
                        <tr>
                          <th style="color: var(--color-charcoal-ink);">ID</th>
                          <th style="color: var(--color-charcoal-ink);">Facility</th>
                          <th style="color: var(--color-charcoal-ink);">Reserved By</th>
                          <th style="color: var(--color-charcoal-ink);">Start Time</th>
                          <th style="color: var(--color-charcoal-ink);">Duration</th>
                          <th style="color: var(--color-charcoal-ink);">Status</th>
                          <th style="color: var(--color-charcoal-ink);">Amount</th>
                        </tr>
                      </thead>
                      <tbody id="reservationsTableBody">
                        <!-- Table rows will be populated by JavaScript -->
                      </tbody>
                    </table>
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
    let facilityUsageChart = null;
    let dailyUsageChart = null;

    document.getElementById('generateReportBtn').addEventListener('click', function() {
      const form = document.getElementById('reportForm');
      const formData = new FormData(form);
      
      // Validate required fields
      if (!formData.get('month') || !formData.get('year') || !formData.get('format')) {
        alert('Please select month, year, and format');
        return;
      }

      // Create URL with parameters
      const params = new URLSearchParams();
      params.append('month', formData.get('month'));
      params.append('year', formData.get('year'));
      params.append('format', formData.get('format'));
      if (formData.get('facility_id')) {
        params.append('facility_id', formData.get('facility_id'));
      }

      // Download the report
      window.open(`{{ route('facility_reservations.generate_monthly_report') }}?${params.toString()}`, '_blank');
    });

    document.getElementById('previewReportBtn').addEventListener('click', function() {
      const form = document.getElementById('reportForm');
      const formData = new FormData(form);
      
      // Validate required fields
      if (!formData.get('month') || !formData.get('year')) {
        alert('Please select month and year');
        return;
      }

      // Show loading
      document.getElementById('reportPreview').classList.remove('hidden');
      document.getElementById('summaryCards').innerHTML = '<div class="col-span-full text-center py-8"><i data-lucide="loader-2" class="w-8 h-8 animate-spin mx-auto mb-4" style="color: var(--color-regal-navy);"></i><p>Loading report data...</p></div>';

      // Fetch report data
      fetch('{{ route("facility_reservations.monthly_report_summary") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          month: formData.get('month'),
          year: formData.get('year'),
          facility_id: formData.get('facility_id') || null
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          displayReportPreview(data.data);
        } else {
          alert('Error loading report data');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error loading report data');
      });
    });

    function quickGenerateReport(month, year, format) {
      const params = new URLSearchParams();
      params.append('month', month);
      params.append('year', year);
      params.append('format', format);
      
      window.open(`{{ route('facility_reservations.generate_monthly_report') }}?${params.toString()}`, '_blank');
    }

    function displayReportPreview(data) {
      // Update summary cards
      const summaryCards = document.getElementById('summaryCards');
      summaryCards.innerHTML = `
        <div class="stat bg-base-200" style="background-color: var(--color-snow-mist);">
          <div class="stat-figure text-primary" style="color: var(--color-regal-navy);">
            <i data-lucide="calendar" class="w-8 h-8"></i>
          </div>
          <div class="stat-title" style="color: var(--color-charcoal-ink);">Total Reservations</div>
          <div class="stat-value" style="color: var(--color-charcoal-ink);">${data.summary.total_reservations}</div>
        </div>
        
        <div class="stat bg-base-200" style="background-color: var(--color-snow-mist);">
          <div class="stat-figure text-secondary" style="color: var(--color-modern-teal);">
            <i data-lucide="check-circle" class="w-8 h-8"></i>
          </div>
          <div class="stat-title" style="color: var(--color-charcoal-ink);">Approved</div>
          <div class="stat-value" style="color: var(--color-charcoal-ink);">${data.summary.approved_reservations}</div>
          <div class="stat-desc" style="color: var(--color-charcoal-ink); opacity: 0.7;">${data.summary.approval_rate.toFixed(1)}% approval rate</div>
        </div>
        
        <div class="stat bg-base-200" style="background-color: var(--color-snow-mist);">
          <div class="stat-figure text-accent" style="color: var(--color-golden-ember);">
            <i data-lucide="clock" class="w-8 h-8"></i>
          </div>
          <div class="stat-title" style="color: var(--color-charcoal-ink);">Total Hours</div>
          <div class="stat-value" style="color: var(--color-charcoal-ink);">${data.summary.total_hours.toFixed(1)}</div>
          <div class="stat-desc" style="color: var(--color-charcoal-ink); opacity: 0.7;">${data.summary.average_booking_duration.toFixed(1)}h avg</div>
        </div>
        
        <div class="stat bg-base-200" style="background-color: var(--color-snow-mist);">
          <div class="stat-figure text-info" style="color: var(--color-regal-navy);">
            <i data-lucide="dollar-sign" class="w-8 h-8"></i>
          </div>
          <div class="stat-title" style="color: var(--color-charcoal-ink);">Revenue</div>
          <div class="stat-value" style="color: var(--color-charcoal-ink);">$${data.summary.total_revenue.toFixed(2)}</div>
        </div>
      `;

      // Update facility usage chart
      updateFacilityUsageChart(data.facility_usage);
      
      // Update daily usage chart
      updateDailyUsageChart(data.daily_usage);
      
      // Update reservations table
      updateReservationsTable(data.reservations);
    }

    function updateFacilityUsageChart(facilityUsage) {
      const ctx = document.getElementById('facilityUsageChart').getContext('2d');
      
      if (facilityUsageChart) {
        facilityUsageChart.destroy();
      }
      
      facilityUsageChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: facilityUsage.map(f => f.facility_name),
          datasets: [{
            label: 'Reservations',
            data: facilityUsage.map(f => f.reservation_count),
            backgroundColor: 'rgba(44, 62, 80, 0.8)',
            borderColor: 'rgba(44, 62, 80, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }

    function updateDailyUsageChart(dailyUsage) {
      const ctx = document.getElementById('dailyUsageChart').getContext('2d');
      
      if (dailyUsageChart) {
        dailyUsageChart.destroy();
      }
      
      dailyUsageChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: dailyUsage.map(d => d.date),
          datasets: [{
            label: 'Reservations',
            data: dailyUsage.map(d => d.reservation_count),
            borderColor: 'rgba(0, 123, 255, 1)',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.1
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }

    function updateReservationsTable(reservations) {
      const tbody = document.getElementById('reservationsTableBody');
      tbody.innerHTML = reservations.map(reservation => `
        <tr>
          <td style="color: var(--color-charcoal-ink);">${reservation.id}</td>
          <td style="color: var(--color-charcoal-ink);">${reservation.facility_name}</td>
          <td style="color: var(--color-charcoal-ink);">${reservation.reserved_by}</td>
          <td style="color: var(--color-charcoal-ink);">${reservation.start_time}</td>
          <td style="color: var(--color-charcoal-ink);">${reservation.duration_hours}h</td>
          <td>
            <span class="badge ${getStatusClass(reservation.status)}" style="color: var(--color-white);">
              ${reservation.status}
            </span>
          </td>
          <td style="color: var(--color-charcoal-ink);">$${reservation.payment_amount.toFixed(2)}</td>
        </tr>
      `).join('');
    }

    function getStatusClass(status) {
      const classes = {
        'approved': 'badge-success',
        'pending': 'badge-warning',
        'denied': 'badge-error'
      };
      return classes[status] || 'badge-info';
    }

    // Initialize Lucide icons
    lucide.createIcons();
  </script>
</body>
</html>
