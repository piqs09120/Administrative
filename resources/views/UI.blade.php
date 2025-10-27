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
        
        <!-- Module Cards - Horizontal Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          
          <!-- Row 1 -->
          <!-- Legal Management Card -->
          <a href="{{ route('legal.case_deck') }}" class="bg-white rounded-2xl p-6 shadow-md border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 block">
            <div class="flex items-center justify-between">
              <!-- LEFT: Text Content -->
              <div class="flex-1">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">LEGAL MANAGEMENT</p>
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $legalCasesPending }}</p>
                <div class="flex items-center gap-1 text-orange-600 text-xs">
                  <i data-lucide="arrow-up" class="w-3 h-3"></i>
                  <span>Pending Cases</span>
                </div>
              </div>
              <!-- RIGHT: Blue Icon Box -->
              <div class="w-20 h-20 bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-4">
                <i data-lucide="scale" class="w-7 h-7 text-yellow-400"></i>
              </div>
            </div>
          </a>

          <!-- Document Management Card -->
          <a href="{{ route('document.index') }}" class="bg-white rounded-2xl p-6 shadow-md border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 block">
            <div class="flex items-center justify-between">
              <!-- LEFT: Text Content -->
              <div class="flex-1">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">DOCUMENT MANAGEMENT</p>
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $pendingLegalDocs }}</p>
                <div class="flex items-center gap-1 text-purple-600 text-xs">
                  <i data-lucide="arrow-up" class="w-3 h-3"></i>
                  <span>Documents Active</span>
                </div>
              </div>
              <!-- RIGHT: Blue Icon Box -->
              <div class="w-20 h-20 bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-4">
                <i data-lucide="file-text" class="w-7 h-7 text-yellow-400"></i>
              </div>
            </div>
          </a>

          <!-- Row 2 -->
          <!-- Visitor Management Card -->
          <a href="{{ route('visitor.index') }}" class="bg-white rounded-2xl p-6 shadow-md border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 block">
            <div class="flex items-center justify-between">
              <!-- LEFT: Text Content -->
              <div class="flex-1">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">VISITOR MANAGEMENT</p>
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $visitorsCheckedIn }}</p>
                <div class="flex items-center gap-1 text-green-600 text-xs">
                  <i data-lucide="arrow-up" class="w-3 h-3"></i>
                  <span>Currently In</span>
                </div>
              </div>
              <!-- RIGHT: Blue Icon Box -->
              <div class="w-20 h-20 bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-4">
                <i data-lucide="users" class="w-7 h-7 text-yellow-400"></i>
              </div>
            </div>
          </a>

          <!-- Facilities Reservations Card -->
          <a href="{{ route('facility_reservations.index') }}" class="bg-white rounded-2xl p-6 shadow-md border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 block">
            <div class="flex items-center justify-between">
              <!-- LEFT: Text Content -->
              <div class="flex-1">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">FACILITIES RESERVATIONS</p>
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $pendingReservations }}</p>
                <div class="flex items-center gap-1 text-blue-600 text-xs">
                  <i data-lucide="arrow-up" class="w-3 h-3"></i>
                  <span>Pending</span>
                </div>
              </div>
              <!-- RIGHT: Blue Icon Box -->
              <div class="w-20 h-20 bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-4">
                <i data-lucide="building" class="w-7 h-7 text-yellow-400"></i>
              </div>
            </div>
          </a>

        </div>

        <!-- Second Row - Full Width Cards -->
        <div class="grid grid-cols-1 gap-6 mb-8">
          
          <!-- User Management Card - Full Width -->
          <a href="{{ route('access.users') }}" class="bg-white rounded-2xl p-6 shadow-md border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 block">
            <div class="flex items-center justify-between">
              <!-- LEFT: Text Content -->
              <div class="flex-1">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">USER MANAGEMENT</p>
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $totalUsers }}</p>
                <div class="flex items-center gap-1 text-indigo-600 text-xs">
                  <i data-lucide="arrow-up" class="w-3 h-3"></i>
                  <span>Total Users</span>
                </div>
              </div>
              <!-- RIGHT: Blue Icon Box -->
              <div class="w-20 h-20 bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-4">
                <i data-lucide="shield" class="w-7 h-7 text-yellow-400"></i>
              </div>
            </div>
          </a>

        </div>

      </main>
    </div>
  </div>

@include('partials.soliera_js')
<!-- Chart.js and dashboard charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
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

    function ensureChart(id, type, data, options){
      const el = document.getElementById(id);
      if (!el) return;
      if (charts[id]) { charts[id].data = data; charts[id].update(); return; }
      charts[id] = new Chart(el, { type, data, options });
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

        if (facJson?.success) {
          ensureChart('facilityChart', 'line', {
            labels: facJson.labels,
            datasets: [{ label:'Reservations', data: facJson.data, borderColor:'#1f2937', backgroundColor:'rgba(31,41,55,0.25)', tension:0.3, fill:true }]
          }, { responsive:true, maintainAspectRatio:false, scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true } } });
        }

        if (userJson?.success) {
          const roleLabels = (userJson.roles || []).map(r => r.role);
          const roleCounts = (userJson.roles || []).map(r => r.count);
          if (roleLabels.length > 0) {
            ensureChart('userMgmtChart', 'doughnut', {
              labels: roleLabels,
              datasets: [{ data: roleCounts, backgroundColor:['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f472b6'] }]
            }, { responsive:true, maintainAspectRatio:false });
          } else {
            ensureChart('userMgmtChart', 'bar', {
              labels: userJson.labels,
              datasets: [{ label:'New Users', data: userJson.registrations, backgroundColor:'rgba(31,41,55,0.8)' }]
            }, { responsive:true, maintainAspectRatio:false, scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true } } });
          }
        } else {
          ensureChart('userMgmtChart', 'bar', { labels: [], datasets:[{ label:'New Users', data: [] }] }, { responsive:true, maintainAspectRatio:false });
        }

        if (visJson) {
          const labels = ['Checked In','Scheduled','Today'];
          const checkedIn = visJson.currentlyIn ?? visJson.current_total ?? 0;
          const scheduled = Array.isArray(scheduledJson) ? scheduledJson.length : (scheduledJson?.length ?? 0);
          const today = visJson.todayVisitors ?? visJson.today_total ?? 0;
          const values = [checkedIn, scheduled, today];
          ensureChart('visitorChart', 'doughnut', {
            labels, datasets:[{ data: values, backgroundColor:['#10b981','#60a5fa','#f59e0b'] }]
          }, { responsive:true, maintainAspectRatio:false });
        }

        if (docJson?.success) {
          const docVals = [docJson.data.total, docJson.data.active, docJson.data.archived, docJson.data.expiring].map(v=>Number(v)||0);
          const maxVal = Math.max(...docVals);
          const suggestedMax = Math.max(1, Math.ceil(maxVal * 1.25));
          ensureChart('documentChart', 'bar', {
            labels: ['Total','Active','Archived','Expiring'],
            datasets: [{ label:'Documents', data: docVals, backgroundColor:'#4b5563' }]
          }, {
            responsive:true,
            maintainAspectRatio:false,
            scales:{
              x:{ grid:{ display:false } },
              y:{ beginAtZero:true, suggestedMax }
            },
            layout:{ padding:{ top:0, right:8, bottom:8, left:8 } }
          });
        }

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

        const totals = [approved, pending, denied];
        const hasData = totals.some(v => Number(v) > 0);
        const displayData = hasData ? totals : [1,1,1]; // placeholder arcs so chart is visible

        ensureChart('legalChart', 'doughnut', {
          labels:['Approved','Pending','Denied'],
          datasets:[{
            data: displayData,
            backgroundColor:['#34d399','#fbbf24','#f87171'],
            borderColor:'#ffffff',
            borderWidth:4,
            hoverOffset:8
          }]
        }, {
          responsive:true,
          maintainAspectRatio:false,
          cutout:'55%',
          plugins:{ legend:{ position:'top', align:'start', labels:{ usePointStyle:true, boxWidth:10, padding:12 } } },
          layout:{ padding:{ top:8, right:8, bottom:8, left:8 } }
        });

        // Compact mode: no extra KPIs/list
      } catch (e) { console.warn('refreshCharts failed', e); }
    }

    async function refreshMetrics(){
      try {
        const [resRt, visRt, docRt, usersRt, currRt] = await Promise.all([
          fetch(endpoints.resRealtime, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.visitorStats, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.documentSummary, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.activeUsers, { headers:{ 'Accept':'application/json' } }),
          fetch(endpoints.visitorCurrent, { headers:{ 'Accept':'application/json' } }),
        ]);
        const [resJson, visJson, docJson, usersJson, currJson] = await Promise.all([resRt.json(), visRt.json(), docRt.json(), usersRt.json(), currRt.json()]);
        const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
        if (resJson) {
          setText('mReservations', resJson.today_reservations ?? '0');
        }
        if (visJson) {
          let active = visJson.currentlyIn ?? visJson.current_total;
          if (active == null && Array.isArray(currJson)) active = currJson.length;
          setText('mVisitors', (active ?? 0).toString());
        }
        if (docJson?.success) {
          setText('mDocsActive', docJson.data?.active ?? '0');
          setText('mDocsExpiring', docJson.data?.expiring ?? '0');
        }
        // Legal pending count from monitoring summary (if available)
        fetch(endpoints.legalSummary, { headers:{ 'Accept':'application/json' }})
          .then(r=>r.json())
          .then(j=>{ const v = j?.data?.pending ?? 0; const el = document.getElementById('mLegalPending'); if (el) el.textContent = v; })
          .catch(()=>{});
        setText('mUsersActive', usersJson?.active_users ?? '0');
      } catch (e) { console.warn('refreshMetrics failed', e); }
    }


    // Add loading animation to metric cards
    function addLoadingAnimation() {
      const metricCards = document.querySelectorAll('#metricCards .card');
      metricCards.forEach(card => {
        card.classList.add('animate-pulse');
        setTimeout(() => {
          card.classList.remove('animate-pulse');
        }, 2000);
      });
    }

    // Initialize dashboard
    refreshCharts();
    refreshMetrics();
    addLoadingAnimation();
    
    // Set up intervals
    setInterval(refreshCharts, 15000); // 15s
    setInterval(refreshMetrics, 10000); // 10s
});
</script>
</body>
</html>