<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sub - System</title>
  <link rel="icon" href="swt.jpg" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
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

      <!-- Dashboard Content -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow bg-gray-50">
          <!-- Metrics row: realtime small cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6" id="metricCards">
            <div class="card bg-base-200 shadow-lg rounded-2xl"><div class="card-body p-6">
              <p class="text-sm text-gray-600">Reservations (Today)</p>
              <p class="text-2xl font-bold" id="mReservations">—</p>
            </div></div>
            <div class="card bg-base-200 shadow-lg rounded-2xl"><div class="card-body p-6">
              <p class="text-sm text-gray-600">Legal Pending Cases</p>
              <p class="text-2xl font-bold" id="mLegalPending">—</p>
            </div></div>
            <div class="card bg-base-200 shadow-lg rounded-2xl"><div class="card-body p-6">
              <p class="text-sm text-gray-600">Active Visitors</p>
              <p class="text-2xl font-bold" id="mVisitors">—</p>
            </div></div>
            <div class="card bg-base-200 shadow-lg rounded-2xl"><div class="card-body p-6">
              <p class="text-sm text-gray-600">Documents (Active)</p>
              <p class="text-2xl font-bold" id="mDocsActive">—</p>
            </div></div>
            <div class="card bg-base-200 shadow-lg rounded-2xl"><div class="card-body p-6">
              <p class="text-sm text-gray-600">Users (Active)</p>
              <p class="text-2xl font-bold" id="mUsersActive">—</p>
            </div></div>
            <div class="card bg-base-200 shadow-lg rounded-2xl"><div class="card-body p-6">
              <p class="text-sm text-gray-600">Documents Expiring (60d)</p>
              <p class="text-2xl font-bold" id="mDocsExpiring">—</p>
            </div></div>
        </div>

        <!-- Charts Grid -->
        <div class="mt-6 grid gap-4 grid-cols-1 md:grid-cols-5 md:auto-rows-[minmax(280px,1fr)] md:h-[calc(100vh-320px)] md:overflow-visible items-stretch">
          <!-- #7 Large: 2 col x 2 row -->
            <div class="card bg-base-100 shadow-lg rounded-2xl md:col-span-2 md:row-span-2 h-full">
              <div class="card-body p-4 flex flex-col h-full">
              <h3 class="text-lg font-semibold">Facility Reservations</h3>
              <p class="text-sm opacity-70">Administrative overview for hotel and restaurant</p>
                <div class="mt-4 flex-1 min-h-[320px]">
                  <canvas id="facilityChart" class="w-full h-full"></canvas>
                </div>
            </div>
          </div>

          <!-- #8 Legal Management: compact chart only -->
          <div class="card bg-base-100 shadow-lg rounded-2xl md:col-start-1 md:col-span-2 md:row-start-3 md:row-span-2 h-full">
            <div class="card-body p-4 flex flex-col h-full">
              <div class="flex items-start justify-between">
                <div>
              <h3 class="text-base font-semibold">Legal Management</h3>
              <p class="text-xs opacity-70">Compliance, cases, and policy tracking</p>
                </div>
                <div class="text-xs opacity-70 hidden md:block">Last 15s</div>
              </div>
              <div class="mt-4 flex-1 min-h-[320px]">
                <canvas id="legalChart" class="w-full h-full"></canvas>
              </div>
            </div>
          </div>

          <!-- #9 Small (under #7, right of #8) -->
            <div class="card bg-base-100 shadow-lg rounded-2xl md:col-start-4 md:col-span-2 md:row-start-3 md:row-span-2 h-full">
              <div class="card-body p-4 flex flex-col h-full">
              <h3 class="text-base font-semibold">Document Management</h3>
              <p class="text-xs opacity-70">Documents, retention, and access logs</p>
                <div class="mt-4 flex-1 min-h-[320px]">
                  <canvas id="documentChart" class="w-full h-full"></canvas>
                </div>
            </div>
          </div>

          <!-- #10 Visitor Management: compact, moved to center top (row-span-2) -->
          <div class="card bg-base-100 shadow-lg rounded-2xl md:col-start-3 md:row-span-2 h-full md:self-stretch">
            <div class="card-body p-4 h-full flex flex-col">
              <h3 class="text-lg font-semibold">Visitor Management</h3>
              <p class="text-sm opacity-70">Check-ins, passes, and visitor analytics</p>
              <div class="mt-4 flex-1 min-h-[320px]">
                <canvas id="visitorChart" class="w-full h-full"></canvas>
              </div>
            </div>
          </div>

          <!-- Combined block for #11-#14: same size behavior as #7 (2 cols x 2 rows) -->
            <div class="card bg-base-100 shadow-lg rounded-2xl md:col-start-4 md:col-span-2 md:row-span-2 h-full">
              <div class="card-body p-4 flex flex-col h-full">
              <h3 class="text-lg font-semibold">User Management</h3>
              <p class="text-sm opacity-70">Departments, roles, and account activity</p>
                <div class="mt-4 flex-1 min-h-[320px]">
                  <canvas id="userMgmtChart" class="w-full h-full"></canvas>
                </div>
            </div>
          </div>
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

    refreshCharts();
    refreshMetrics();
    setInterval(refreshCharts, 15000); // 15s
    setInterval(refreshMetrics, 10000); // 10s
});
</script>
</body>
</html>