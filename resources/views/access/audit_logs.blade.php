<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Audit Trail & Transaction - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
  <style>
    /* Fix for module badges to ensure full text visibility */
    .module-badge {
      display: inline-block;
      white-space: nowrap;
      min-width: fit-content;
      padding: 0.25rem 0.75rem;
      font-size: 0.75rem;
      line-height: 1.2;
      border-radius: 0.375rem;
      background-color: #3b82f6;
      color: white;
      font-weight: 500;
    }
    
    /* Ensure table cell has enough space for the badge */
    .module-cell {
      min-width: 120px;
      max-width: 150px;
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
          <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Audit Trail & Transaction</h1>
            <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Monitor and track all system activities and user actions (excluding login/logout)</p>
          </div>

          <!-- Record Count -->
          <div class="text-sm text-gray-500 mb-6">
            Total {{ $logs->count() }} records
          </div>
        </div>

        <!-- Audit Logs Table -->
        <x-table-card :title="'Audit Logs'">
          @slot('headerAction')
            <a href="{{ route('access.audit_logs.export') }}" 
               class="btn btn-sm transition-all duration-200 cursor-pointer hover:scale-105"
               style="background: linear-gradient(135deg, #F7A923 0%, #E6940F 100%); color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25); border: none;"
               onmouseover="this.style.background='linear-gradient(135deg, #E6940F 0%, #D2840E 100%)'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'"
               onmouseout="this.style.background='linear-gradient(135deg, #F7A923 0%, #E6940F 100%)'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
              <i data-lucide="download" class="w-4 h-4 mr-1" style="fill: none;"></i>Export
            </a>
          @endslot
          
          <!-- Filters Row -->
          <div class="mb-6 flex items-center gap-4 flex-wrap">
            <!-- Search Bar -->
            <div class="relative flex-1 min-w-[200px]">
              <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
              <input type="text" 
                     id="searchInput"
                     placeholder="Search logs..." 
                     class="input input-bordered input-sm w-full pl-10 pr-4 bg-gray-50 border-gray-200 focus:bg-white focus:border-blue-300">
            </div>

            <!-- Department Filter -->
            <div class="flex items-center gap-2">
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
              <select id="actionFilter" class="select select-bordered select-sm w-40">
                <option value="">All Actions</option>
                <option value="save_legal_draft">Save Legal Draft</option>
                <option value="document_view">Document View</option>
                <option value="Document_uploaded">Document Uploaded</option>
                <option value="Access_control_check">Access Control Check</option>
                <option value="Profile_updated">Profile Updated</option>
                <option value="Table_added">Table Added</option>
                <option value="Facility_reserved">Facility Reserved</option>
                <option value="Visitor_registered">Visitor Registered</option>
                <option value="Report_generated">Report Generated</option>
                <option value="Settings_updated">Settings Updated</option>
                <option value="Data_exported">Data Exported</option>
                <option value="Notification_sent">Notification Sent</option>
                <option value="Backup_created">Backup Created</option>
                <option value="Permission_granted">Permission Granted</option>
                <option value="File_deleted">File Deleted</option>
              </select>
            </div>

            <!-- Date Range Filter -->
            <div class="flex items-center gap-2">
              <input type="date" id="dateFilter" class="input input-bordered input-sm w-32">
            </div>
          </div>
            <table class="table table-zebra w-full">
              <thead>
                <tr class="bg-gray-50">
                  <th class="text-left py-3 px-4 font-medium text-gray-700">LOG ID</th>
                  <th class="text-left py-3 px-4 font-medium text-gray-700">DEPARTMENT</th>
                  <th class="text-left py-3 px-4 font-medium text-gray-700">EMPLOYEE</th>
                  <th class="text-left py-3 px-4 font-medium text-gray-700" style="min-width: 120px;">MODULES</th>
                  <th class="text-left py-3 px-4 font-medium text-gray-700">ACTION</th>
                  <th class="text-left py-3 px-4 font-medium text-gray-700">ACTIVITY</th>
                  <th class="text-left py-3 px-4 font-medium text-gray-700">DATE</th>
                </tr>
              </thead>
              <tbody>
                @forelse($logs as $log)
                  <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-4">
                      <span class="font-mono text-sm text-gray-600">#{{ $log->id }}</span>
                    </td>
                    <td class="py-3 px-4">
                      <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">{{ $log->user->dept_name ?? 'Soliera Restaurant' }}</span>
                        <span class="text-xs text-gray-400">ID: {{ $log->user->Dept_no ?? '0' }}</span>
                      </div>
                    </td>
                    <td class="py-3 px-4">
                      <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-blue-900 flex items-center justify-center">
                          <i data-lucide="user" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                          <div class="font-medium text-gray-900">{{ $log->user->employee_name ?? 'Unknown User' }}</div>
                          <div class="text-sm text-gray-500">{{ $log->user->role ?? 'No role' }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="py-3 px-4 module-cell">
                      @php
                        $moduleMap = [
                          'save_legal_draft' => 'Legal Management',
                          'document_view' => 'Document Management',
                          'Document_uploaded' => 'Document Management',
                          'Access_control_check' => 'Security',
                          'Profile_updated' => 'User Management',
                          'Table_added' => 'Table Management',
                          'Facility_reserved' => 'Facility Management',
                          'Visitor_registered' => 'Visitor Management',
                          'Report_generated' => 'Reporting',
                          'Settings_updated' => 'System Administration',
                          'Data_exported' => 'Data Management',
                          'Notification_sent' => 'Communication',
                          'Backup_created' => 'System Administration',
                          'Permission_granted' => 'User Management',
                          'File_deleted' => 'File Management'
                        ];
                        $module = $moduleMap[$log->action] ?? 'System';
                      @endphp
                      <span class="module-badge">{{ $module }}</span>
                    </td>
                    <td class="py-3 px-4">
                      <div class="flex items-center gap-2">
                        <i data-lucide="trending-up" class="w-4 h-4 text-gray-500"></i>
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
                    <td colspan="7" class="text-center py-12">
                      <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                          <i data-lucide="activity" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No System Activity Logs Found</h3>
                        <p class="text-gray-500 text-sm">No system activity logs available at the moment (excluding login/logout).</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          
          <!-- Pagination or Load More -->
          @if($logs->count() > 10)
            <div class="mt-6 flex justify-center">
              <button onclick="loadMoreLogs()" 
                      class="btn btn-sm transition-all duration-200 cursor-pointer hover:scale-105"
                      style="background: linear-gradient(135deg, #F7A923 0%, #E6940F 100%); color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25); border: none;"
                      onmouseover="this.style.background='linear-gradient(135deg, #E6940F 0%, #D2840E 100%)'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'"
                      onmouseout="this.style.background='linear-gradient(135deg, #F7A923 0%, #E6940F 100%)'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                <i data-lucide="chevron-down" class="w-4 h-4 mr-1" style="fill: none;"></i>Load More
              </button>
            </div>
          @endif
        </x-table-card>
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
          const action = row.querySelector('td:nth-child(5) .text-sm')?.textContent || '';
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
      console.log('Loading more logs...');
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
