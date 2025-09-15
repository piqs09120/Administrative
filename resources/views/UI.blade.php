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
            <p class="text-sm text-gray-600">Revenue (Today)</p>
            <p class="text-2xl font-bold" id="mRevenue">—</p>
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
            <p class="text-sm text-gray-600">Inventory Alerts</p>
            <p class="text-2xl font-bold" id="mInventoryAlerts">—</p>
          </div></div>
        </div>

        <!-- Charts Grid -->
        <div class="mt-6 grid gap-4 grid-cols-1 md:grid-cols-5 md:auto-rows-[minmax(0,1fr)] md:h-[calc(100vh-320px)] md:overflow-hidden items-stretch">
          <!-- #7 Large: 2 col x 2 row -->
          <div class="card bg-base-100 shadow-lg rounded-2xl md:col-span-2 md:row-span-2">
            <div class="card-body p-4">
              <h3 class="text-lg font-semibold">Facility Reservations</h3>
              <p class="text-sm opacity-70">Administrative overview for hotel and restaurant</p>
              <canvas id="facilityChart" class="mt-4"></canvas>
            </div>
          </div>

          <!-- #8 Small (under #7) -->
          <div class="card bg-base-100 shadow-lg rounded-2xl md:col-start-1 md:col-span-2 md:row-start-3 md:row-span-2">
            <div class="card-body p-4">
              <h3 class="text-base font-semibold">Legal Management</h3>
              <p class="text-xs opacity-70">Compliance, cases, and policy tracking</p>
              <canvas id="legalChart" class="mt-4"></canvas>
            </div>
          </div>

          <!-- #9 Small (under #7, right of #8) -->
          <div class="card bg-base-100 shadow-lg rounded-2xl md:col-start-4 md:col-span-2 md:row-start-3 md:row-span-2">
            <div class="card-body p-4">
              <h3 class="text-base font-semibold">Document Management</h3>
              <p class="text-xs opacity-70">Documents, retention, and access logs</p>
              <canvas id="documentChart" class="mt-4"></canvas>
            </div>
          </div>

          <!-- #10 Tall: center column, stretch to bottom -->
          <div class="card bg-base-100 shadow-lg rounded-2xl md:col-start-3 md:row-span-3 h-full md:self-stretch">
            <div class="card-body p-4 h-full">
              <h3 class="text-lg font-semibold">Visitor Management</h3>
              <p class="text-sm opacity-70">Check-ins, passes, and visitor analytics</p>
              <canvas id="visitorChart" class="mt-4"></canvas>
            </div>
          </div>

          <!-- Combined block for #11-#14: same size behavior as #7 (2 cols x 2 rows) -->
          <div class="card bg-base-100 shadow-lg rounded-2xl md:col-start-4 md:col-span-2 md:row-span-2">
            <div class="card-body p-4">
              <h3 class="text-lg font-semibold">User Management</h3>
              <p class="text-sm opacity-70">Departments, roles, and account activity</p>
              <canvas id="userMgmtChart" class="mt-4"></canvas>
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
    documentSummary: `{{ route('document.monitoring.summary') }}`,
    legalSummary: `{{ route('legal.monitoring.summary') }}`,
    resRealtime: `{{ route('facility_reservations.realtime_stats') }}`,
  };

  function ensureChart(id, type, data, options){
    const el = document.getElementById(id);
    if (!el) return;
    if (charts[id]) { charts[id].data = data; charts[id].update(); return; }
    charts[id] = new Chart(el, { type, data, options });
  }

  async function refreshCharts(){
    try {
      const [facRes, userRes, visRes, docRes, legRes] = await Promise.all([
        fetch(endpoints.facility, { headers:{ 'Accept':'application/json' } }),
        fetch(endpoints.userMgmt, { headers:{ 'Accept':'application/json' } }),
        fetch(endpoints.visitorStats, { headers:{ 'Accept':'application/json' } }),
        fetch(endpoints.documentSummary, { headers:{ 'Accept':'application/json' } }),
        fetch(endpoints.legalSummary, { headers:{ 'Accept':'application/json' } }),
      ]);

      const [facJson, userJson, visJson, docJson, legJson] = await Promise.all([
        facRes.json(), userRes.json(), visRes.json(), docRes.json(), legRes.json()
      ]);

      if (facJson?.success) {
        ensureChart('facilityChart', 'line', {
          labels: facJson.labels,
          datasets: [{ label:'Reservations', data: facJson.data, borderColor:'#1f2937', backgroundColor:'rgba(31,41,55,0.25)', tension:0.3, fill:true }]
        }, { responsive:true, scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true } } });
      }

      if (userJson?.success) {
        ensureChart('userMgmtChart', 'bar', {
          labels: userJson.labels,
          datasets: [{ label:'New Users', data: userJson.registrations, backgroundColor:'rgba(31,41,55,0.8)' }]
        }, { responsive:true, scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true } } });
      }

      if (visJson) {
        const labels = ['Checked In','Scheduled','Today'];
        const values = [visJson.current_total ?? 0, visJson.scheduled_total ?? 0, visJson.today_total ?? 0];
        ensureChart('visitorChart', 'doughnut', {
          labels, datasets:[{ data: values, backgroundColor:['#10b981','#60a5fa','#f59e0b'] }]
        }, { responsive:true });
      }

      if (docJson?.success) {
        ensureChart('documentChart', 'bar', {
          labels: ['Total','Active','Archived','Expiring'],
          datasets: [{ label:'Documents', data:[docJson.data.total, docJson.data.active, docJson.data.archived, docJson.data.expiring], backgroundColor:'#4b5563' }]
        }, { responsive:true, scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true } } });
      }

      if (legJson?.success) {
        const l = ['Approved','Pending','Denied'];
        const d = [legJson.data?.approved ?? 0, legJson.data?.pending ?? 0, legJson.data?.denied ?? 0];
        ensureChart('legalChart', 'pie', { labels:l, datasets:[{ data:d, backgroundColor:['#34d399','#fbbf24','#f87171'] }] }, { responsive:true });
      }
    } catch (e) { console.warn('refreshCharts failed', e); }
  }

  async function refreshMetrics(){
    try {
      const [resRt, visRt, docRt] = await Promise.all([
        fetch(endpoints.resRealtime, { headers:{ 'Accept':'application/json' } }),
        fetch(endpoints.visitorStats, { headers:{ 'Accept':'application/json' } }),
        fetch(endpoints.documentSummary, { headers:{ 'Accept':'application/json' } }),
      ]);
      const [resJson, visJson, docJson] = await Promise.all([resRt.json(), visRt.json(), docRt.json()]);
      const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
      if (resJson) {
        setText('mReservations', resJson.today_reservations ?? '0');
        setText('mRevenue', resJson.today_revenue ? `₱${Number(resJson.today_revenue).toLocaleString()}` : '₱0');
        setText('mInventoryAlerts', resJson.inventory_alerts ?? '0');
      }
      if (visJson) setText('mVisitors', visJson.current_total ?? '0');
      if (docJson?.success) setText('mDocsActive', docJson.data?.active ?? '0');
      // Users active: leaving placeholder 0 unless you have an endpoint
      setText('mUsersActive', resJson?.active_users ?? '0');
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