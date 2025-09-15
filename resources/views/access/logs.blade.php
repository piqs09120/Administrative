<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Account Logs - Soliera</title>
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
          <div class="mb-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Account Logs</h1>
            <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Monitor and track user account activities and system access</p>
          </div>
          <!-- underline divider (matches Visitor Management style) -->
          <div class="border-b border-gray-200 mb-6"></div>

          <!-- Log Entry Count -->
          @php
            $filteredLogs = $logs->filter(function($log){
              return in_array(strtolower($log->action), ['login','logout','otp']);
            });
          @endphp
          <div class="text-sm text-gray-500 mb-6">
            Total {{ $filteredLogs->count() }} log entries
          </div>
        </div>

        <!-- Account Logs Table Section -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <!-- Header with Search and Actions -->
          <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                  <i data-lucide="activity" class="w-5 h-5 text-blue-600"></i>
                  User Activity Logs
                </h3>
                <!-- Search Bar -->
                <div class="relative">
                  <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                  <input type="text" 
                         id="searchInput"
                         placeholder="Search logs..." 
                         class="input input-bordered input-sm w-64 pl-10 pr-4 bg-gray-50 border-gray-200 focus:bg-white focus:border-blue-300">
                </div>
              </div>
              
              <!-- Export Button -->
              <a href="{{ route('access.account_logs.export') }}" class="btn btn-outline btn-sm">
                <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                Export
              </a>
            </div>

            <!-- Filters Row -->
            <div class="flex items-center gap-4">
              <!-- Department Filter -->
              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Department:</label>
                <select id="departmentFilter" class="select select-bordered select-sm w-40">
                  <option value="">All Departments</option>
                  <option value="Soliera Restaurant">Soliera Restaurant</option>
                  <option value="Management">Management</option>
                  <option value="Reception">Reception</option>
                  <option value="Housekeeping">Housekeeping</option>
                  <option value="Restaurant">Restaurant</option>
                  <option value="Legal">Legal</option>
                  <option value="IT">IT</option>
                  <option value="Finance">Finance</option>
                </select>
              </div>

              <!-- Action Filter -->
              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Action:</label>
                <select id="actionFilter" class="select select-bordered select-sm w-40">
                  <option value="">All Actions</option>
                  <option value="Login">Login</option>
                  <option value="Logout">Logout</option>
                  <option value="OTP">OTP</option>
                </select>
              </div>

              <!-- Date Range Filter -->
              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Date:</label>
                <input type="date" id="dateFilter" class="input input-bordered input-sm w-32">
              </div>

              <!-- Clear Filters Button -->
              <button onclick="clearFilters()" class="btn btn-ghost btn-xs text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-3 h-3 mr-1"></i>
                Clear
              </button>
            </div>
          </div>

          <!-- Account Logs Table -->
          <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
              <thead>
                <tr class="bg-gray-50">
                  <th class="text-left py-3 px-4 font-medium text-gray-700">USER</th>
                  <th class="text-left py-3 px-4 font-medium text-gray-700">ACTION</th>
                  <th class="text-left py-3 px-4 font-medium text-gray-700">DESCRIPTION</th>
                  <th class="text-left py-3 px-4 font-medium text-gray-700">DATE</th>
                </tr>
              </thead>
              <tbody>
                @forelse($filteredLogs as $log)
                  <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-4">
                      <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                          <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div>
                          <div class="font-medium text-gray-900">{{ $log->user->employee_name ?? 'Unknown User' }}</div>
                          <div class="text-sm text-gray-500">{{ $log->user->dept_name ?? 'No Department' }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="py-3 px-4">
                      <div class="flex items-center gap-2">
                        <i data-lucide="activity" class="w-4 h-4 text-gray-500"></i>
                        <span class="text-sm text-gray-600">{{ $log->action }}</span>
                      </div>
                    </td>
                    <td class="py-3 px-4">
                      <span class="text-sm text-gray-600">{{ $log->description }}</span>
                    </td>
                    <td class="py-3 px-4">
                      <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y H:i:s') }}</span>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-12">
                      <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                          <i data-lucide="activity" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No Account Logs Found</h3>
                        <p class="text-gray-500 text-sm">No user activity logs available at the moment.</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination or Load More -->
          @if($logs->count() > 10)
            <div class="mt-6 flex justify-center">
              <button onclick="loadMoreLogs()" class="btn btn-outline btn-sm">
                <i data-lucide="chevron-down" class="w-4 h-4 mr-1"></i>
                Load More
              </button>
            </div>
          @endif
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Filtering functionality
    function filterLogs() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const departmentFilter = document.getElementById('departmentFilter').value;
      const actionFilter = document.getElementById('actionFilter').value;
      const dateFilter = document.getElementById('dateFilter').value;
      
      const rows = document.querySelectorAll('tbody tr');
      
      rows.forEach(row => {
        let showRow = true;
        
        // Search filter
        if (searchTerm) {
          const text = row.textContent.toLowerCase();
          if (!text.includes(searchTerm)) {
            showRow = false;
          }
        }
        
        // Department filter
        if (departmentFilter && showRow) {
          const department = row.querySelector('td:nth-child(2) .text-sm')?.textContent || '';
          if (!department.includes(departmentFilter)) {
            showRow = false;
          }
        }
        
        // Action filter
        if (actionFilter && showRow) {
          const action = row.querySelector('td:nth-child(3) .text-sm')?.textContent || '';
          if (action !== actionFilter) {
            showRow = false;
          }
        }
        
        // Date filter
        if (dateFilter && showRow) {
          const logDate = row.querySelector('td:last-child .text-sm')?.textContent || '';
          const rowDate = new Date(logDate);
          const filterDate = new Date(dateFilter);
          if (rowDate.toDateString() !== filterDate.toDateString()) {
            showRow = false;
          }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
      });
    }
    
    function clearFilters() {
      document.getElementById('searchInput').value = '';
      document.getElementById('departmentFilter').value = '';
      document.getElementById('actionFilter').value = '';
      document.getElementById('dateFilter').value = '';
      
      // Show all rows
      const rows = document.querySelectorAll('tbody tr');
      rows.forEach(row => {
        row.style.display = '';
      });
    }



    function loadMoreLogs() {
      // Implement pagination or load more functionality
      console.log('Loading more account logs...');
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchInput');
      const departmentFilter = document.getElementById('departmentFilter');
      const actionFilter = document.getElementById('actionFilter');
      const dateFilter = document.getElementById('dateFilter');
      
      if (searchInput) {
        searchInput.addEventListener('input', filterLogs);
      }
      if (departmentFilter) {
        departmentFilter.addEventListener('change', filterLogs);
      }
      if (actionFilter) {
        actionFilter.addEventListener('change', filterLogs);
      }
      if (dateFilter) {
        dateFilter.addEventListener('change', filterLogs);
      }
    });
  </script>
</body>
</html>


