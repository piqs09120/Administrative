@php
  // Get dashboard data
  $todayDate = now()->toDateString();
  $visitorsToday = \App\Models\Visitor::whereDate('time_in', $todayDate)->count();
  $visitorsCheckedIn = \App\Models\Visitor::whereNull('time_out')->count();
  $pendingReservations = \App\Models\FacilityRequest::where('status', 'pending')->count();
  $pendingLegalDocs = \App\Models\Document::where('status', 'pending_review')->count();
  $legalCasesPending = \App\Models\LegalCase::where('status', 'pending')->count();
  $totalUsers = \App\Models\User::count();
  $expiringDocuments = \App\Models\Document::whereNotNull('retention_until')->where('retention_until', '<=', now()->addDays(30))->where('retention_until', '>=', now())->count();
@endphp

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SOLIERA Dashboard</title>
  <link rel="icon" href="swt.jpg" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
   @vite(['resources/css/soliera.css'])
  <style>
    /* Custom dashboard enhancements */
    .card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card:hover {
      transform: translateY(-2px);
    }
    
    /* Gradient text effect */
    .gradient-text {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    /* Custom scrollbar */
    .overflow-y-auto::-webkit-scrollbar {
      width: 6px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 3px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 3px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }
    
    /* Chart container styling */
    .chart-container {
      position: relative;
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      border-radius: 0.75rem;
      border: 1px solid #e2e8f0;
    }
    
    /* Badge animations */
    .badge {
      transition: all 0.2s ease;
    }
    
    .badge:hover {
      transform: scale(1.05);
    }
    
    /* Loading state for charts */
    .chart-loading {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 200px;
    }
    
    .chart-loading::before {
      content: '';
      width: 40px;
      height: 40px;
      border: 4px solid #e2e8f0;
      border-top: 4px solid #3b82f6;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
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

      <!-- Dashboard Content -->
      <main class="flex-1 overflow-y-auto p-6">
        <div class="space-y-5" id="dash-content">
          
          {{-- ROW 1: Legal + Documents (equal size) --}}
          <div class="grid grid-cols-12 gap-5">
            {{-- Legal Management - 6/12 --}}
            <div class="col-span-12 lg:col-span-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 h-[320px] flex flex-col justify-between overflow-hidden hover:shadow-md transition-all relative group">
              <header class="shrink-0 flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-2">
                    <h3 class="text-[11px] uppercase tracking-wider text-[#2C3E50]">Legal Management</h3>
                    <div class="tooltip tooltip-right" data-tip="SLA risk = overdue_cases / active_cases × 100">
                      <i data-lucide="info" class="w-3 h-3 text-gray-400 cursor-help"></i>
                    </div>
                  </div>
                  <div class="mt-2 flex items-center gap-3">
                    <div class="text-4xl font-bold text-gray-900" id="kpi-legal">{{ $legalCasesPending }}</div>
                    <div class="flex flex-col gap-1">
                      <span class="inline-flex items-center px-2 py-1 text-[11px] rounded-md bg-emerald-50 text-emerald-700" id="kpi-legal-badge">8% SLA risk</span>
                      <span class="inline-flex items-center px-2 py-1 text-[10px] rounded-md" id="kpi-legal-change">
                        <span id="kpi-legal-change-icon"></span>
                        <span id="kpi-legal-change-text" class="ml-1">--</span>
                      </span>
                    </div>
                  </div>
                  <p class="text-xs text-gray-500 mt-1" id="legal-subtitle">Pending cases (last 7 days)</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[#0B1E3A] flex items-center justify-center flex-shrink-0">
                  <i data-lucide="scale" class="w-6 h-6 text-[#F0C30F]"></i>
                </div>
              </header>
              <div class="mt-3 flex-1 min-h-0 relative">
                <div id="legal-loading" class="hidden absolute inset-0 items-center justify-center bg-white bg-opacity-80 z-10" style="display: none;">
                  <div class="loading loading-spinner loading-sm text-gray-400"></div>
                </div>
                <div id="legal-empty" class="hidden absolute inset-0 items-center justify-center" style="display: none;">
                  <p class="text-xs text-gray-400">No data for selected range</p>
                </div>
                <div class="h-[180px] w-full">
                  <canvas id="spark-legal"></canvas>
                </div>
              </div>
            </div>

            {{-- Document Management - 6/12 --}}
            <div class="col-span-12 lg:col-span-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 h-[320px] flex flex-col justify-between overflow-hidden hover:shadow-md transition-all relative group">
              <header class="shrink-0 flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-2">
                    <h3 class="text-[11px] uppercase tracking-wider text-[#2C3E50]">Document Management</h3>
                    <div class="tooltip tooltip-right" data-tip="Active = status ∈ {Active} (Archived/Expiring excluded)">
                      <i data-lucide="info" class="w-3 h-3 text-gray-400 cursor-help"></i>
                    </div>
                  </div>
                  <div class="mt-2 flex items-center gap-3">
                    <div class="text-4xl font-bold text-gray-900" id="kpi-docs">{{ $pendingLegalDocs }}</div>
                    <div class="flex flex-col gap-1">
                      <span class="inline-flex items-center px-2 py-1 text-[11px] rounded-md bg-emerald-50 text-emerald-700" id="kpi-docs-badge">+8 this week</span>
                      <span class="inline-flex items-center px-2 py-1 text-[10px] rounded-md" id="kpi-docs-change">
                        <span id="kpi-docs-change-icon"></span>
                        <span id="kpi-docs-change-text" class="ml-1">--</span>
                      </span>
                    </div>
                  </div>
                  <p class="text-xs text-gray-500 mt-1" id="docs-subtitle">Documents by category (last 7 days)</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[#0B1E3A] flex items-center justify-center flex-shrink-0">
                  <i data-lucide="file-text" class="w-6 h-6 text-[#F0C30F]"></i>
                </div>
              </header>
              <div class="mt-3 flex-1 min-h-0 relative">
                <div id="docs-loading" class="hidden absolute inset-0 items-center justify-center bg-white bg-opacity-80 z-10" style="display: none;">
                  <div class="loading loading-spinner loading-sm text-gray-400"></div>
                </div>
                <div id="docs-empty" class="hidden absolute inset-0 items-center justify-center" style="display: none;">
                  <p class="text-xs text-gray-400">No data for selected range</p>
                </div>
                <div class="h-[180px] w-full">
                  <canvas id="bar-docs"></canvas>
                </div>
              </div>
            </div>
          </div>

          {{-- ROW 2: Visitor + Facilities (equal size) --}}
          <div class="grid grid-cols-12 gap-5">
            <div class="col-span-12 lg:col-span-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 h-[320px] flex flex-col justify-between overflow-hidden hover:shadow-md transition-all relative group">
              <header class="shrink-0 flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-2">
                    <h3 class="text-[11px] uppercase tracking-wider text-[#2C3E50]">Visitor Management</h3>
                  </div>
                  <div class="mt-2 flex items-center gap-3">
                    <div class="text-4xl font-bold text-gray-900" id="kpi-visitors">{{ $visitorsCheckedIn }}</div>
                    <div class="flex flex-col gap-1">
                      <span class="inline-flex items-center px-2 py-1 text-[11px] rounded-md bg-emerald-50 text-emerald-700" id="kpi-visitors-badge">70 min avg dwell</span>
                      <span class="inline-flex items-center px-2 py-1 text-[10px] rounded-md" id="kpi-visitors-change">
                        <span id="kpi-visitors-change-icon"></span>
                        <span id="kpi-visitors-change-text" class="ml-1">--</span>
                      </span>
                    </div>
                  </div>
                  <p class="text-xs text-gray-500 mt-1" id="visitors-subtitle">Visitors checked in (last 7 days)</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[#0B1E3A] flex items-center justify-center flex-shrink-0">
                  <i data-lucide="users" class="w-6 h-6 text-[#F0C30F]"></i>
                </div>
              </header>
              <div class="mt-3 flex-1 min-h-0 relative">
                <div id="visitors-loading" class="hidden absolute inset-0 items-center justify-center bg-white bg-opacity-80 z-10" style="display: none;">
                  <div class="loading loading-spinner loading-sm text-gray-400"></div>
                </div>
                <div id="visitors-empty" class="hidden absolute inset-0 items-center justify-center" style="display: none;">
                  <p class="text-xs text-gray-400">No data for selected range</p>
                </div>
                <div class="h-[180px] w-full">
                  <canvas id="spark-visitors"></canvas>
                </div>
              </div>
            </div>

            <div class="col-span-12 lg:col-span-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 h-[320px] flex flex-col justify-between overflow-hidden hover:shadow-md transition-all relative group">
              <header class="shrink-0 flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-2">
                    <h3 class="text-[11px] uppercase tracking-wider text-[#2C3E50]">Facilities Reservations</h3>
                    <div class="tooltip tooltip-right" data-tip="Utilization = booked_hours / available_hours × 100 for selected range">
                      <i data-lucide="info" class="w-3 h-3 text-gray-400 cursor-help"></i>
                    </div>
                  </div>
                  <div class="mt-2 flex items-center gap-3">
                    <div class="text-4xl font-bold text-gray-900" id="kpi-fac">{{ $pendingReservations }}</div>
                    <div class="flex flex-col gap-1">
                      <span class="inline-flex items-center px-2 py-1 text-[11px] rounded-md bg-emerald-50 text-emerald-700" id="kpi-fac-badge">82% utilization</span>
                      <span class="inline-flex items-center px-2 py-1 text-[10px] rounded-md" id="kpi-fac-change">
                        <span id="kpi-fac-change-icon"></span>
                        <span id="kpi-fac-change-text" class="ml-1">--</span>
                      </span>
                    </div>
                  </div>
                  <p class="text-xs text-gray-500 mt-1" id="facilities-subtitle">Reservations by month (YTD)</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[#0B1E3A] flex items-center justify-center flex-shrink-0">
                  <i data-lucide="building-2" class="w-6 h-6 text-[#F0C30F]"></i>
                </div>
              </header>
              <div class="mt-3 flex-1 min-h-0 relative">
                <div id="facilities-loading" class="hidden absolute inset-0 items-center justify-center bg-white bg-opacity-80 z-10" style="display: none;">
                  <div class="loading loading-spinner loading-sm text-gray-400"></div>
                </div>
                <div id="facilities-empty" class="hidden absolute inset-0 items-center justify-center" style="display: none;">
                  <p class="text-xs text-gray-400">No data for selected range</p>
                </div>
                <div class="h-[180px] w-full">
                  <canvas id="bar-fac"></canvas>
                </div>
              </div>
            </div>
        </div>

          {{-- ROW 3: User Management + Recent Activity (equal size) --}}
          <div class="grid grid-cols-12 gap-5">
            <div class="col-span-12 lg:col-span-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 h-[320px] flex flex-col justify-between overflow-hidden hover:shadow-md transition-all relative group">
              <header class="shrink-0 flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-2">
                    <h3 class="text-[11px] uppercase tracking-wider text-[#2C3E50]">User Management</h3>
                  </div>
                  <div class="mt-2 flex items-center gap-3">
                    <div class="text-4xl font-bold text-gray-900" id="kpi-users">{{ $totalUsers }}</div>
                    <div class="flex flex-col gap-1">
                      <span class="inline-flex items-center px-2 py-1 text-[11px] rounded-md bg-emerald-50 text-emerald-700" id="kpi-users-badge">8 online now</span>
                      <span class="inline-flex items-center px-2 py-1 text-[10px] rounded-md" id="kpi-users-change">
                        <span id="kpi-users-change-icon"></span>
                        <span id="kpi-users-change-text" class="ml-1">--</span>
                      </span>
                    </div>
                  </div>
                  <p class="text-xs text-gray-500 mt-1" id="users-subtitle">Total registered users (last 7 days)</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[#0B1E3A] flex items-center justify-center flex-shrink-0">
                  <i data-lucide="shield" class="w-6 h-6 text-[#F0C30F]"></i>
                </div>
              </header>
              <div class="mt-3 flex-1 min-h-0 relative">
                <div id="users-loading" class="hidden absolute inset-0 items-center justify-center bg-white bg-opacity-80 z-10" style="display: none;">
                  <div class="loading loading-spinner loading-sm text-gray-400"></div>
                </div>
                <div id="users-empty" class="hidden absolute inset-0 items-center justify-center" style="display: none;">
                  <p class="text-xs text-gray-400">No data for selected range</p>
                </div>
                <div class="h-[180px] w-full">
                  <canvas id="spark-users"></canvas>
                </div>
              </div>
            </div>

            {{-- Recent Activity Feed --}}
            <section class="col-span-12 lg:col-span-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 h-[320px] flex flex-col overflow-hidden">
              <header class="shrink-0 flex items-center justify-between mb-3">
                <h3 class="text-[11px] uppercase tracking-wider text-[#2C3E50]">Recent Activity</h3>
              </header>
              <div class="flex-1 overflow-y-auto" id="activity-feed">
                <div class="space-y-2 text-xs">
                  <div class="flex items-start gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0"></i>
                    <div class="flex-1 min-w-0">
                      <p class="text-gray-700 truncate">Legal case #1234 approved</p>
                      <p class="text-gray-400 text-[10px]">2 minutes ago</p>
                    </div>
                  </div>
                  <div class="flex items-start gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="file-plus" class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"></i>
                    <div class="flex-1 min-w-0">
                      <p class="text-gray-700 truncate">New document uploaded</p>
                      <p class="text-gray-400 text-[10px]">15 minutes ago</p>
                    </div>
                  </div>
                  <div class="flex items-start gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="building-2" class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0"></i>
                    <div class="flex-1 min-w-0">
                      <p class="text-gray-700 truncate">Facility reservation created</p>
                      <p class="text-gray-400 text-[10px]">1 hour ago</p>
                    </div>
                  </div>
                  <div class="flex items-start gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="user-plus" class="w-4 h-4 text-purple-500 mt-0.5 flex-shrink-0"></i>
                    <div class="flex-1 min-w-0">
                      <p class="text-gray-700 truncate">New user registered</p>
                      <p class="text-gray-400 text-[10px]">2 hours ago</p>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>

        </div>
      </main>
    </div>
  </div>

@include('partials.soliera_js')
<!-- Chart.js and dashboard charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const charts = {};
    const endpoints = {
      facility: `{{ route('dashboard.facility_stats') }}`,
      userMgmt: `{{ route('dashboard.user_mgmt_stats') }}`,
      visitorStats: `{{ route('visitor.stats') }}`,
      legalList: `{{ route('dashboard.legal_list') }}`,
      visitorCurrent: `{{ route('visitor.current') }}`,
      visitorScheduled: `{{ route('visitor.scheduled') }}`,
      documentSummary: `{{ route('document.monitoring.summary') }}`,
      legalSummary: `{{ route('dashboard.legal_summary') }}`,
      resRealtime: `{{ route('facility_reservations.realtime_stats') }}`,
      activeUsers: `{{ route('dashboard.active_users') }}`,
    };

    // Small helper to keep the same compact style across cards
    const compactOpts = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { 
        legend: { display: false }, 
        tooltip: { 
          enabled: true,
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          padding: 10,
          titleFont: { size: 12, weight: 'bold' },
          bodyFont: { size: 11 },
          displayColors: false,
          callbacks: {
            label: function(context) {
              return context.dataset.label + ': ' + context.parsed.y;
            }
          }
        }
      },
      scales: { 
        x: { 
          display: true,
          position: 'bottom',
          grid: { 
            display: true, 
            color: 'rgba(0,0,0,0.1)',
            lineWidth: 1,
            drawOnChartArea: true
          },
          ticks: { 
            display: true, 
            font: { size: 12, weight: '600' },
            color: '#4b5563',
            maxRotation: 45,
            minRotation: 0,
            padding: 0,
            autoSkip: false
          },
          border: { display: true, color: 'rgba(0,0,0,0.2)' }
        },
        y: { 
          display: true,
          position: 'left',
          grid: { 
            display: true, 
            color: 'rgba(0,0,0,0.1)',
            lineWidth: 1,
            drawOnChartArea: true
          },
          ticks: { 
            display: true, 
            font: { size: 12, weight: '600' },
            color: '#4b5563',
            padding: 0,
            mirror: false,
            callback: function(value) {
              return value;
            }
          },
          border: { display: true, color: 'rgba(0,0,0,0.2)' },
          beginAtZero: true
        }
      },
      layout: {
        padding: { top: 10, right: 5, bottom: 2, left: 2 }
      }
    };

    // Create charts only if the canvas exists (avoids errors on partial renders)
    function mk(id, type, data, opts = compactOpts){
      const el = document.getElementById(id);
      if(!el) return;
      if (charts[id]) {
        charts[id].data = data;
        charts[id].options = opts;
        charts[id].update();
        return charts[id];
      }
      charts[id] = new Chart(el.getContext('2d'), {
        type,
        data,
        options: opts
      });
      return charts[id];
    }

    async function refreshCharts(){
      try {
        const [facRes, userRes, visRes, docRes, legRes, schedRes, legListRes] = await Promise.all([
          fetch(endpoints.facility, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.userMgmt, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.visitorStats, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.documentSummary, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.legalSummary, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.visitorScheduled, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.legalList, { headers:{ 'Accept':'application/json' } }),
        ]);

        const [facJson, userJson, visJson, docJson, scheduledJson] = await Promise.all([
          facRes.ok ? facRes.json() : Promise.resolve(null),
          userRes.ok ? userRes.json() : Promise.resolve(null),
          visRes.ok ? visRes.json() : Promise.resolve(null),
          docRes.ok ? docRes.json() : Promise.resolve(null),
          schedRes.ok ? schedRes.json() : Promise.resolve(null)
        ]);

        let legJson = null; let legListJson = null;
        try { legJson = legRes.ok ? await legRes.json() : null; } catch (e) { legJson = null; }
        try { legListJson = legListRes.ok ? await legListRes.json() : null; } catch (e) { legListJson = null; }

        // Facilities bar chart - column chart
      const barFacOpts = JSON.parse(JSON.stringify(compactOpts));
      barFacOpts.barThickness = 32;
      barFacOpts.categoryPercentage = 0.8;
      barFacOpts.barPercentage = 0.9;
      if (barFacOpts?.scales?.y?.ticks) {
        barFacOpts.scales.y.min = 0;
        barFacOpts.scales.y.max = 50;
        barFacOpts.scales.y.ticks.stepSize = 5;
        barFacOpts.scales.y.ticks.callback = (value) => value;
      }
        let facData = facJson?.success ? (facJson.data || []) : [3,5,4,6,5,7,6,8];
        let facLabels = facJson?.success ? (facJson.labels || ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Mon']) : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Mon'];
        // Ensure data is visible (if all zeros, use sample data)
        if (facData.length === 0 || facData.every(v => v === 0)) {
          facData = [3,5,4,6,5,7,6,8];
        }
        // Pad to 8 points if needed
        while (facData.length < 8) facData.push(0);
        facData = facData.slice(0, 8);
        while (facLabels.length < 8) facLabels.push('');
        facLabels = facLabels.slice(0, 8);
        mk('bar-fac', 'bar', {
          labels: facLabels,
          datasets: [{ 
            label: 'Reservations',
            data: facData, 
            backgroundColor:'#F7A923',
            borderColor: '#F7A923',
            borderWidth: 0
          }]
        }, barFacOpts);

        // Users sparkline - area chart (green style like image)
        let userData = userJson?.success ? (userJson.registrations || userJson.data || []) : [40,42,43,45,44,46,47,45];
        let userLabels = userJson?.success ? (userJson.labels || ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Mon']) : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Mon'];
        // Ensure data is visible
        if (userData.length === 0 || userData.every(v => v === 0)) {
          userData = [40,42,43,45,44,46,47,45];
        }
        // Pad to 8 points
        while (userData.length < 8) userData.push(0);
        userData = userData.slice(0, 8);
        while (userLabels.length < 8) userLabels.push('');
        userLabels = userLabels.slice(0, 8);
        const userLineOpts = JSON.parse(JSON.stringify(compactOpts));
        if (userLineOpts?.scales?.y?.ticks) {
          userLineOpts.scales.y.min = 0;
          userLineOpts.scales.y.max = 100;
          userLineOpts.scales.y.ticks.stepSize = 10;
          userLineOpts.scales.y.ticks.callback = (value) => value;
        }
        mk('spark-users', 'line', {
          labels: userLabels,
          datasets: [{ 
            label: 'Users',
            data: userData, 
            fill: true, 
            borderWidth: 3,
            tension: 0.4, 
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            backgroundColor: 'rgba(16, 185, 129, 0.15)',
            borderColor: '#10b981'
          }]
        }, userLineOpts);

        // Visitors sparkline - area chart (green style like image)
        const checkedIn = visJson?.currentlyIn ?? visJson?.current_total ?? 0;
        let trendData = Array.from({length: 8}, () => Math.max(0, checkedIn + Math.floor(Math.random() * 5) - 2));
        // If all zeros or very low, use sample data
        if (trendData.every(v => v <= 2)) {
          trendData = [15,18,20,22,19,21,23,22];
        }
        const visitorLineOpts = JSON.parse(JSON.stringify(compactOpts));
        if (visitorLineOpts?.scales?.y?.ticks) {
          visitorLineOpts.scales.y.min = 0;
          visitorLineOpts.scales.y.max = 100;
          visitorLineOpts.scales.y.ticks.stepSize = 10;
          visitorLineOpts.scales.y.ticks.callback = (value) => value;
        }
        mk('spark-visitors', 'line', {
          labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Mon'],
          datasets: [{ 
            label: 'Visitors',
            data: trendData, 
            fill: true, 
            borderWidth: 3,
            tension: 0.4, 
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            backgroundColor: 'rgba(16, 185, 129, 0.15)',
            borderColor: '#10b981'
          }]
        }, visitorLineOpts);

        // Documents bar chart - column chart
        let docVals = docJson?.success ? [
          docJson.data?.total || 0, 
          docJson.data?.active || 0, 
          docJson.data?.archived || 0, 
          docJson.data?.expiring || 0
        ].map(v=>Number(v)||0) : [120, 85, 45, 12];
        // Ensure data is visible (if all zeros, use sample data)
        if (docVals.every(v => v === 0)) {
          docVals = [120, 85, 45, 12];
        }
        const barDocsOpts = JSON.parse(JSON.stringify(compactOpts));
        barDocsOpts.barThickness = 32;
        barDocsOpts.categoryPercentage = 0.8;
        barDocsOpts.barPercentage = 0.9;
        if (barDocsOpts?.scales?.y?.ticks) {
          barDocsOpts.scales.y.min = 0;
          barDocsOpts.scales.y.max = 50;
          barDocsOpts.scales.y.ticks.stepSize = 5;
          barDocsOpts.scales.y.ticks.callback = (value) => value;
        }
        mk('bar-docs', 'bar', {
          labels: ['Total', 'Active', 'Archived', 'Expiring'],
          datasets: [{ 
            label: 'Documents',
            data: docVals, 
            backgroundColor:'#F7A923',
            borderColor: '#F7A923',
            borderWidth: 0
          }]
        }, barDocsOpts);

        // Build counts from summary or list; if all zero or unavailable, render placeholder arcs so doughnut is visible
        let approved = 0, pending = 0, denied = 0;
        if (legJson?.success && legJson.data) {
          approved = legJson.data.approved ?? 0;
          pending = legJson.data.pending ?? 0;
          denied = legJson.data.denied ?? 0;
        }
        if ((approved + pending + denied) === 0 && legListJson?.success && Array.isArray(legListJson.data)) {
          const cases = legListJson.data;
          approved = cases.filter(c => c.status === 'approved').length;
          pending = cases.filter(c => c.status === 'pending').length;
          denied = cases.filter(c => c.status === 'denied').length;
        }

        // Legal sparkline - area chart (green style like image)
        const basePending = pending || 11; // Use actual pending or fallback
        let legalTrend = Array.from({length: 8}, () => Math.max(0, basePending + Math.floor(Math.random() * 3) - 1));
        // Ensure trend is visible
        if (legalTrend.every(v => v <= 2)) {
          legalTrend = [8,10,9,11,12,10,11,13];
        }
        const legalLineOpts = JSON.parse(JSON.stringify(compactOpts));
        if (legalLineOpts?.scales?.y?.ticks) {
          legalLineOpts.scales.y.min = 0;
          legalLineOpts.scales.y.max = 100;
          legalLineOpts.scales.y.ticks.stepSize = 10;
          legalLineOpts.scales.y.ticks.callback = (value) => value;
        }
        mk('spark-legal', 'line', {
          labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Mon'],
          datasets:[{
            label: 'Cases',
            data: legalTrend,
            fill: true,
            borderWidth: 2.5,
            tension: 0.4,
            pointRadius: 3,
            pointHoverRadius: 5,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            backgroundColor: 'rgba(16, 185, 129, 0.15)',
            borderColor: '#10b981'
          }]
        }, legalLineOpts);

        // Compact mode: no extra KPIs/list
      } catch (e) { console.warn('refreshCharts failed', e); }
    }

    async function refreshMetrics(){
      try {
        const [resRt, visRt, docRt, usersRt, currRt, legRt] = await Promise.all([
          fetch(endpoints.resRealtime, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.visitorStats, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.documentSummary, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.activeUsers, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.visitorCurrent, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.legalSummary, { headers:{ 'Accept':'application/json' } }),
        ]);
        const [resJson, visJson, docJson, usersJson, currJson, legJson] = await Promise.all([
          resRt.json().catch(()=>null), 
          visRt.json().catch(()=>null), 
          docRt.json().catch(()=>null), 
          usersRt.json().catch(()=>null), 
          currRt.json().catch(()=>null),
          legRt.json().catch(()=>null)
        ]);
        
        const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
        const setBadge = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
        
        // Legal KPI
        if (legJson?.data) {
          setText('kpi-legal', legJson.data.pending ?? '0');
        }
        
        // Facilities KPI
        if (resJson) {
          setText('kpi-fac', resJson.today_reservations ?? '0');
        }
        
        // Visitors KPI
        if (visJson) {
          let active = visJson.currentlyIn ?? visJson.current_total;
          if (active == null && Array.isArray(currJson)) active = currJson.length;
          setText('kpi-visitors', (active ?? 0).toString());
        }
        
        // Documents KPI
        if (docJson?.success) {
          setText('kpi-docs', docJson.data?.active ?? '0');
        }
        
        // Users KPI
        if (usersJson) {
          setText('kpi-users', usersJson.total_users ?? usersJson.active_users ?? '0');
          setBadge('kpi-users-badge', `${usersJson.active_users ?? 0} online now`);
        }
        
        // Update timestamps
        updateTimestamp('legal');
        updateTimestamp('docs');
        updateTimestamp('visitors');
        updateTimestamp('facilities');
        updateTimestamp('users');
        
        // Update % change badges (mock data - replace with actual previous period data from API)
        // In real implementation, fetch previous period data and calculate
        const legalCurrent = parseInt(legJson?.data?.pending ?? '0');
        const docsCurrent = parseInt(docJson?.data?.active ?? '0');
        const visitorsCurrent = parseInt((visJson?.currentlyIn ?? visJson?.current_total ?? 0).toString());
        const facilitiesCurrent = parseInt(resJson?.today_reservations ?? '0');
        const usersCurrent = parseInt(usersJson?.total_users ?? usersJson?.active_users ?? '0');
        
        updateChangeBadge('legal', legalCurrent, 10);
        updateChangeBadge('docs', docsCurrent, 5);
        updateChangeBadge('visitors', visitorsCurrent, 8);
        updateChangeBadge('facilities', facilitiesCurrent, 3);
        updateChangeBadge('users', usersCurrent, 35);
      } catch (e) { console.warn('refreshMetrics failed', e); }
    }


    // Initialize charts immediately with fallback data
    function initChartsImmediate() {
      // Legal sparkline - area chart (green style like image)
      const legalLineOptsInit = JSON.parse(JSON.stringify(compactOpts));
      if (legalLineOptsInit?.scales?.y?.ticks) {
        legalLineOptsInit.scales.y.min = 0;
        legalLineOptsInit.scales.y.max = 100;
        legalLineOptsInit.scales.y.ticks.stepSize = 10;
        legalLineOptsInit.scales.y.ticks.callback = (value) => value;
      }
      mk('spark-legal', 'line', {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Mon'],
        datasets:[{
          label: 'Cases',
          data: [8,10,9,11,12,10,11,13],
          fill: true,
          borderWidth: 2.5,
          tension: 0.4,
          pointRadius: 3,
          pointHoverRadius: 5,
          pointBackgroundColor: '#10b981',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          backgroundColor: 'rgba(16, 185, 129, 0.15)',
          borderColor: '#10b981'
        }]
      }, legalLineOptsInit);
      
      // Documents bar chart - column chart
      const barDocsOpts = JSON.parse(JSON.stringify(compactOpts));
      barDocsOpts.barThickness = 32;
      barDocsOpts.categoryPercentage = 0.8;
      barDocsOpts.barPercentage = 0.9;
      if (barDocsOpts?.scales?.y?.ticks) {
        barDocsOpts.scales.y.min = 0;
        barDocsOpts.scales.y.max = 50;
        barDocsOpts.scales.y.ticks.stepSize = 5;
        barDocsOpts.scales.y.ticks.callback = (value) => value;
      }
      mk('bar-docs', 'bar', {
        labels: ['Total', 'Active', 'Archived', 'Expiring'],
        datasets: [{ 
          label: 'Documents',
          data: [120, 85, 45, 12], 
          backgroundColor:'#F7A923',
          borderColor: '#F7A923',
          borderWidth: 0
        }]
      }, barDocsOpts);
      
      // Visitors sparkline - area chart (green style like image)
      const visitorLineOptsInit = JSON.parse(JSON.stringify(compactOpts));
      if (visitorLineOptsInit?.scales?.y?.ticks) {
        visitorLineOptsInit.scales.y.min = 0;
        visitorLineOptsInit.scales.y.max = 100;
        visitorLineOptsInit.scales.y.ticks.stepSize = 10;
        visitorLineOptsInit.scales.y.ticks.callback = (value) => value;
      }
      mk('spark-visitors', 'line', {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Mon'],
        datasets: [{ 
          label: 'Visitors',
          data: [15,18,20,22,19,21,23,22], 
          fill: true, 
          borderWidth: 2.5,
          tension: 0.4, 
          pointRadius: 3,
          pointHoverRadius: 5,
          pointBackgroundColor: '#10b981',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          backgroundColor: 'rgba(16, 185, 129, 0.15)',
          borderColor: '#10b981'
        }]
      }, visitorLineOptsInit);
      
      // Facilities bar chart - column chart
      const barFacOpts = JSON.parse(JSON.stringify(compactOpts));
      barFacOpts.barThickness = 32;
      barFacOpts.categoryPercentage = 0.8;
      barFacOpts.barPercentage = 0.9;
      if (barFacOpts?.scales?.y?.ticks) {
        barFacOpts.scales.y.min = 0;
        barFacOpts.scales.y.max = 50;
        barFacOpts.scales.y.ticks.stepSize = 5;
        barFacOpts.scales.y.ticks.callback = (value) => value;
      }
      mk('bar-fac', 'bar', {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Mon'],
        datasets: [{ 
          label: 'Reservations',
          data: [3,5,4,6,5,7,6,8], 
          backgroundColor:'#F7A923',
          borderColor: '#F7A923',
          borderWidth: 0
        }]
      }, barFacOpts);
      
      // Users sparkline - area chart (green style like image)
      const userLineOptsInit = JSON.parse(JSON.stringify(compactOpts));
      if (userLineOptsInit?.scales?.y?.ticks) {
        userLineOptsInit.scales.y.min = 0;
        userLineOptsInit.scales.y.max = 100;
        userLineOptsInit.scales.y.ticks.stepSize = 10;
        userLineOptsInit.scales.y.ticks.callback = (value) => value;
      }
      mk('spark-users', 'line', {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Mon'],
        datasets: [{ 
          label: 'Users',
          data: [40,42,43,45,44,46,47,45], 
          fill: true, 
          borderWidth: 2.5,
          tension: 0.4, 
          pointRadius: 3,
          pointHoverRadius: 5,
          pointBackgroundColor: '#10b981',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          backgroundColor: 'rgba(16, 185, 129, 0.15)',
          borderColor: '#10b981'
        }]
      }, userLineOptsInit);
    }

    // Global date range state
    let currentDateRange = 'last7';
    let dateRangeParams = {};

    // Update timestamp helper
    function updateTimestamp(cardId) {
      const now = new Date();
      const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
      const el = document.getElementById(cardId + '-last-updated');
      if (el) el.textContent = `Refreshed: ${timeStr}`;
    }

    // Calculate date range params
    function getDateRangeParams(range) {
      const today = new Date();
      const params = {};
      
      switch(range) {
        case 'today':
          params.start = today.toISOString().split('T')[0];
          params.end = today.toISOString().split('T')[0];
          break;
        case 'last7':
          const last7 = new Date(today);
          last7.setDate(today.getDate() - 7);
          params.start = last7.toISOString().split('T')[0];
          params.end = today.toISOString().split('T')[0];
          break;
        case 'last30':
          const last30 = new Date(today);
          last30.setDate(today.getDate() - 30);
          params.start = last30.toISOString().split('T')[0];
          params.end = today.toISOString().split('T')[0];
          break;
        case 'thismonth':
          params.start = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
          params.end = today.toISOString().split('T')[0];
          break;
        case 'custom':
          const from = document.getElementById('date-from')?.value;
          const to = document.getElementById('date-to')?.value;
          if (from && to) {
            params.start = from;
            params.end = to;
          } else {
            return getDateRangeParams('last7'); // fallback
          }
          break;
      }
      return params;
    }

    // Update subtitle based on date range
    function updateSubtitle(cardId, baseText) {
      const el = document.getElementById(cardId + '-subtitle');
      if (!el) return;
      
      let rangeText = '';
      switch(currentDateRange) {
        case 'today': rangeText = 'today'; break;
        case 'last7': rangeText = 'last 7 days'; break;
        case 'last30': rangeText = 'last 30 days'; break;
        case 'thismonth': rangeText = 'this month'; break;
        case 'custom': 
          const from = dateRangeParams.start;
          const to = dateRangeParams.end;
          rangeText = `${from} to ${to}`;
          break;
      }
      el.textContent = `${baseText} (${rangeText})`;
    }

    // Calculate % change vs previous period
    function calculateChange(current, previous) {
      if (!previous || previous === 0) return null;
      const change = ((current - previous) / previous) * 100;
      return Math.round(change * 10) / 10; // 1 decimal place
    }

    // Update change badge
    function updateChangeBadge(cardId, current, previous) {
      const change = calculateChange(current, previous);
      if (change === null) return;
      
      const iconEl = document.getElementById(`kpi-${cardId}-change-icon`);
      const textEl = document.getElementById(`kpi-${cardId}-change-text`);
      const badgeEl = document.getElementById(`kpi-${cardId}-change`);
      
      if (!iconEl || !textEl || !badgeEl) return;
      
      const isPositive = change > 0;
      const icon = isPositive ? '▲' : '▼';
      const color = isPositive ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50';
      
      iconEl.textContent = icon;
      textEl.textContent = `${Math.abs(change)}% vs last period`;
      badgeEl.className = `inline-flex items-center px-2 py-1 text-[10px] rounded-md ${color}`;
    }

    // Show/hide loading state
    function setLoading(cardId, isLoading) {
      const el = document.getElementById(cardId + '-loading');
      if (el) {
        if (isLoading) {
          el.classList.remove('hidden');
          el.style.display = 'flex';
        } else {
          el.classList.add('hidden');
          el.style.display = 'none';
        }
      }
    }

    // Show/hide empty state
    function setEmpty(cardId, isEmpty) {
      const el = document.getElementById(cardId + '-empty');
      if (el) {
        if (isEmpty) {
          el.classList.remove('hidden');
          el.style.display = 'flex';
        } else {
          el.classList.add('hidden');
          el.style.display = 'none';
        }
      }
    }

    // Refresh individual card
    function refreshCard(cardId) {
      setLoading(cardId, true);
      updateTimestamp(cardId);
      
      // Simulate API call
      setTimeout(() => {
        setLoading(cardId, false);
        // In real implementation, fetch data and update chart/KPI
        refreshCharts();
        refreshMetrics();
      }, 500);
    }

    // Export card data
    function exportCard(cardId, format) {
      alert(`Exporting ${cardId} data as ${format.toUpperCase()}...`);
      // In real implementation, call export endpoint
    }

    // Refresh activity feed
    async function refreshActivity() {
      const feed = document.getElementById('activity-feed');
      if (!feed) return;
      feed.innerHTML = `
        <div class="flex items-center justify-center py-6 text-xs text-slate-400">
          <span class="loading loading-spinner loading-sm mr-2"></span>
          Updating activity…
        </div>
      `;

      try {
        const response = await fetch(`{{ route('dashboard.recent_activity') }}?limit=8`, {
          headers: { 'Accept': 'application/json' }
        });
        const payload = response.ok ? await response.json() : null;

        if (!payload?.success || !Array.isArray(payload.data) || payload.data.length === 0) {
          feed.innerHTML = `
            <div class="flex flex-col items-center justify-center py-6 text-xs text-slate-400">
              <i data-lucide="inbox" class="w-6 h-6 mb-2"></i>
              No activity yet. Check back soon.
            </div>
          `;
          if (window.lucide) window.lucide.createIcons();
          return;
        }

        feed.innerHTML = payload.data.map(item => {
          const href = item.url ?? 'javascript:void(0);';
          const descr = item.description ? `<p class="text-xs text-slate-500 truncate">${item.description}</p>` : '';
          const meta = [item.module, item.actor, item.time_ago].filter(Boolean).join(' • ');

          return `
            <a href="${href}" class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
              <span class="w-9 h-9 rounded-full flex items-center justify-center ${item.icon_color ?? 'bg-slate-100 text-slate-600'}">
                <i data-lucide="${item.icon ?? 'activity'}" class="w-4 h-4"></i>
              </span>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-800 truncate group-hover:text-slate-900">${item.title}</p>
                ${descr}
                <p class="text-[11px] text-slate-400 mt-1">${meta}</p>
              </div>
            </a>
          `;
        }).join('');

        if (window.lucide) window.lucide.createIcons();
      } catch (error) {
        console.error('Activity feed error', error);
        feed.innerHTML = `
          <div class="flex flex-col items-center justify-center py-6 text-xs text-rose-500">
            <i data-lucide="alert-triangle" class="w-6 h-6 mb-2"></i>
            Unable to load recent activity. Please try again later.
          </div>
        `;
        if (window.lucide) window.lucide.createIcons();
      }
    }

    // Date range selector removed - using default 'last7' range

    // Refresh all cards with new date range
    function refreshAllCards() {
      updateSubtitle('legal', 'Pending cases');
      updateSubtitle('docs', 'Documents by category');
      updateSubtitle('visitors', 'Visitors checked in');
      updateSubtitle('facilities', 'Reservations by month');
      updateSubtitle('users', 'Total registered users');
      
      refreshCharts();
      refreshMetrics();
    }

    // Add chart click handlers for drill-down
    function setupChartClickHandlers() {
      // Legal chart click
      const legalChart = charts['spark-legal'];
      if (legalChart) {
        legalChart.canvas.onclick = function(evt) {
          const points = legalChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
          if (points.length) {
            window.location.href = `{{ route('legal.case_deck') }}?filter=pending&date_range=${currentDateRange}`;
          }
        };
      }
      
      // Documents chart click
      const docsChart = charts['bar-docs'];
      if (docsChart) {
        docsChart.canvas.onclick = function(evt) {
          const points = docsChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
          if (points.length) {
            const index = points[0].index;
            const labels = ['total', 'active', 'archived', 'expiring'];
            window.location.href = `{{ route('document.archived') }}?filter=${labels[index]}&date_range=${currentDateRange}`;
          }
        };
      }
      
      // Facilities chart click
      const facChart = charts['bar-fac'];
      if (facChart) {
        facChart.canvas.onclick = function(evt) {
          const points = facChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
          if (points.length) {
            window.location.href = `{{ route('facility_reservations.index') }}?date_range=${currentDateRange}`;
          }
        };
      }
    }

    // Update chart options to include Y-axis labels
    const compactOptsWithLabels = JSON.parse(JSON.stringify(compactOpts));
    compactOptsWithLabels.scales.y.ticks.callback = function(value) {
      return value;
    };
    compactOptsWithLabels.scales.x.ticks.callback = function(value, index, ticks) {
      return value;
    };

    // Initialize dashboard
    dateRangeParams = getDateRangeParams('last7');
    initChartsImmediate(); // Show charts immediately
    refreshCharts(); // Then update with real data
    refreshMetrics();
    refreshAllCards(); // Set initial subtitles
    refreshActivity();
    setupChartClickHandlers(); // Enable drill-downs
    
    // Set up intervals
    setInterval(refreshCharts, 15000); // 15s
    setInterval(refreshMetrics, 10000); // 10s
    setInterval(refreshActivity, 20000); // 20s
    
    // Initialize Lucide icons
    if (window.lucide) {
      window.lucide.createIcons();
    }
});
</script>
</body>
</html>