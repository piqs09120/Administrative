<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Analytics - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @vite(['resources/css/soliera.css'])
</head>
<body class="bg-gray-50">
  <div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    @include('partials.sidebarr')
    <!-- Main content -->
    <div class="flex flex-col flex-1 overflow-hidden">
      <!-- Header -->
      @include('partials.navbar')

      <!-- Main content area -->
      <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
        <!-- Page Header -->
        <div class="pb-5 mb-6 animate-fadeIn">
          <div class="border-b-2 border-gray-500 w-full"></div>
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
              <a href="{{ route('document.index') }}" class="btn btn-ghost btn-sm" title="Back to Documents">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
              </a>
              <div>
                <h1 class="text-2xl font-semibold bg-white bg-clip-text text-[#191970]" style="color: var(--color-charcoal-ink);">Document Analytics</h1>
                <p class="text-gray-600">Comprehensive document management analytics and insights</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <a href="{{ route('document.reports') }}" class="btn btn-outline">
                <i data-lucide="bar-chart" class="w-4 h-4 mr-2"></i>Basic Reports
              </a>
              <button onclick="refreshAnalytics()" class="btn btn-primary">
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>Refresh
              </button>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
          <h3 class="text-lg font-semibold mb-4">Filters</h3>
          <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="label">
                <span class="label-text">Date Range</span>
              </label>
              <select name="date_range" class="select select-bordered w-full">
                <option value="7" {{ $dateRange == '7' ? 'selected' : '' }}>Last 7 days</option>
                <option value="30" {{ $dateRange == '30' ? 'selected' : '' }}>Last 30 days</option>
                <option value="90" {{ $dateRange == '90' ? 'selected' : '' }}>Last 90 days</option>
                <option value="365" {{ $dateRange == '365' ? 'selected' : '' }}>Last year</option>
              </select>
            </div>
            <div>
              <label class="label">
                <span class="label-text">Department</span>
              </label>
              <select name="department" class="select select-bordered w-full">
                <option value="">All Departments</option>
                @foreach($analytics['department_breakdown'] as $dept)
                  <option value="{{ $dept->department }}" {{ $department == $dept->department ? 'selected' : '' }}>
                    {{ $dept->department }} ({{ $dept->count }})
                  </option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="label">
                <span class="label-text">Confidentiality</span>
              </label>
              <select name="confidentiality" class="select select-bordered w-full">
                <option value="">All Levels</option>
                <option value="public" {{ $confidentiality == 'public' ? 'selected' : '' }}>Public</option>
                <option value="internal" {{ $confidentiality == 'internal' ? 'selected' : '' }}>Internal</option>
                <option value="restricted" {{ $confidentiality == 'restricted' ? 'selected' : '' }}>Restricted</option>
              </select>
            </div>
            <div class="flex items-end">
              <button type="submit" class="btn btn-primary w-full">
                <i data-lucide="filter" class="w-4 h-4 mr-2"></i>Apply Filters
              </button>
            </div>
          </form>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Total Documents</p>
                <p class="text-2xl font-bold text-gray-900">{{ $analytics['overview']['total_documents'] }}</p>
              </div>
              <div class="p-3 bg-blue-100 rounded-full">
                <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Active Documents</p>
                <p class="text-2xl font-bold text-green-600">{{ $analytics['overview']['active_documents'] }}</p>
              </div>
              <div class="p-3 bg-green-100 rounded-full">
                <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Total Accesses</p>
                <p class="text-2xl font-bold text-purple-600">{{ $analytics['access_stats']['total_accesses'] }}</p>
              </div>
              <div class="p-3 bg-purple-100 rounded-full">
                <i data-lucide="eye" class="w-6 h-6 text-purple-600"></i>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Unique Users</p>
                <p class="text-2xl font-bold text-orange-600">{{ $analytics['access_stats']['unique_users'] }}</p>
              </div>
              <div class="p-3 bg-orange-100 rounded-full">
                <i data-lucide="users" class="w-6 h-6 text-orange-600"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <!-- Department Breakdown -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Documents by Department</h3>
            <canvas id="departmentChart" width="400" height="200"></canvas>
          </div>

          <!-- Confidentiality Breakdown -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Documents by Confidentiality</h3>
            <canvas id="confidentialityChart" width="400" height="200"></canvas>
          </div>
        </div>

        <!-- Recent Activity and Expiring Documents -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Recent Activity -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
            <div class="space-y-3">
              @forelse($analytics['recent_activity'] as $activity)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 rounded-full">
                      <i data-lucide="{{ $activity->action == 'download' ? 'download' : 'eye' }}" class="w-4 h-4 text-blue-600"></i>
                    </div>
                    <div>
                      <p class="text-sm font-medium">{{ $activity->document->title ?? 'Unknown Document' }}</p>
                      <p class="text-xs text-gray-500">{{ ucfirst($activity->action) }} by User {{ $activity->user_id }}</p>
                    </div>
                  </div>
                  <span class="text-xs text-gray-500">{{ $activity->accessed_at->diffForHumans() }}</span>
                </div>
              @empty
                <p class="text-gray-500 text-center py-4">No recent activity</p>
              @endforelse
            </div>
          </div>

          <!-- Expiring Documents -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Expiring Documents</h3>
            <div class="space-y-3">
              @forelse($analytics['expiring_documents'] as $document)
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                  <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 rounded-full">
                      <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600"></i>
                    </div>
                    <div>
                      <p class="text-sm font-medium">{{ $document->title }}</p>
                      <p class="text-xs text-gray-500">{{ $document->department }} • {{ ucfirst($document->confidentiality) }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-xs text-red-600 font-medium">{{ $document->retention_until->diffForHumans() }}</p>
                    <p class="text-xs text-gray-500">{{ $document->retention_until->format('M d, Y') }}</p>
                  </div>
                </div>
              @empty
                <p class="text-gray-500 text-center py-4">No documents expiring soon</p>
              @endforelse
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Department Chart
    const departmentCtx = document.getElementById('departmentChart').getContext('2d');
    const departmentData = @json($analytics['department_breakdown']);
    
    new Chart(departmentCtx, {
      type: 'doughnut',
      data: {
        labels: departmentData.map(item => item.department),
        datasets: [{
          data: departmentData.map(item => item.count),
          backgroundColor: [
            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });

    // Confidentiality Chart
    const confidentialityCtx = document.getElementById('confidentialityChart').getContext('2d');
    const confidentialityData = @json($analytics['confidentiality_breakdown']);
    
    new Chart(confidentialityCtx, {
      type: 'pie',
      data: {
        labels: confidentialityData.map(item => item.confidentiality),
        datasets: [{
          data: confidentialityData.map(item => item.count),
          backgroundColor: [
            '#10B981', '#F59E0B', '#EF4444'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });

    function refreshAnalytics() {
      window.location.reload();
    }
  </script>
</body>
</html>
