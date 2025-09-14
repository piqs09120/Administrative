<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>View Documents - Soliera</title>
  <link rel="icon" href="swt.jpg" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
          <div class="pb-5 border-b border-base-300 mb-6">
            <h1 class="text-2xl font-semibold bg-white bg-clip-text text-[#191970]" style="color: var(--color-charcoal-ink);">Document Management System - All Documents</h1>
            <p class="text-gray-600 mt-2">View and manage all documents in the system</p>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
          <!-- Total Documents -->
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-primary">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-primary text-primary-content rounded-full w-10 h-10">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-primary badge-outline text-xs">Total</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-primary justify-center mb-1">{{ $documents->total() }}</h2>
                <p class="text-sm text-base-content/70">Documents</p>
              </div>
            </div>
          </div>

          <!-- Received Today -->
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-success">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-success text-success-content rounded-full w-10 h-10">
                    <i data-lucide="calendar-plus" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-success badge-outline text-xs">Today</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-success justify-center mb-1">{{ $documents->where('created_at', '>=', now()->startOfDay())->count() }}</h2>
                <p class="text-sm text-base-content/70">Received Today</p>
              </div>
            </div>
          </div>

          <!-- Released Documents -->
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-info">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-info text-info-content rounded-full w-10 h-10">
                    <i data-lucide="send" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-info badge-outline text-xs">Released</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-info justify-center mb-1">{{ $documents->where('status', 'released')->count() }}</h2>
                <p class="text-sm text-base-content/70">Released Documents</p>
              </div>
            </div>
          </div>

          <!-- Archived Documents -->
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-neutral">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-neutral text-neutral-content rounded-full w-10 h-10">
                    <i data-lucide="archive" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-neutral badge-outline text-xs">Archived</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-neutral justify-center mb-1">{{ $documents->where('status', 'archived')->count() }}</h2>
                <p class="text-sm text-base-content/70">Archived Documents</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Document Library Section -->
        <div class="card bg-white shadow-xl">
          <div class="card-body">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Document Library</h3>
              <div class="text-sm text-gray-500">
                Total: {{ $documents->total() }} documents
              </div>
            </div>
            
            <!-- Search and Filter Controls -->
            <div class="flex flex-col sm:flex-row gap-4 mb-6">
              <div class="flex-1">
                <input type="text" placeholder="Search documents..." class="input input-bordered w-full" id="searchInput">
              </div>
              <div class="flex gap-2">
                <select class="select select-bordered" id="statusFilter">
                  <option value="">All Status</option>
                  <option value="active">Active</option>
                  <option value="archived">Archived</option>
                  <option value="expired">Expired</option>
                  <option value="disposed">Disposed</option>
                </select>
                <button class="btn btn-primary" onclick="filterDocuments()">
                  <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                  Search
                </button>
                <button class="btn btn-outline" onclick="clearFilters()">
                  <i data-lucide="x" class="w-4 h-4"></i>
                </button>
              </div>
            </div>

            <!-- Documents Table -->
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full" id="documentsTable">
                <thead>
                  <tr class="bg-gray-50">
                    <th class="text-left py-4 px-4 font-semibold text-gray-700">Document Name</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Document Type</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Department</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Date Created</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Confidentiality Level</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Retention Period</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Status</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Expiration Date</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($documents as $index => $document)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                      <!-- Document Name Column -->
                      <td class="py-4 px-4">
                        <div class="flex items-center space-x-3">
                          <div class="avatar placeholder">
                            <div class="bg-blue-100 text-blue-800 rounded-full w-10 h-10 flex items-center justify-center">
                              <span class="text-sm font-semibold">
                                {{ substr($document->title ?? 'UC', 0, 2) }}
                              </span>
                            </div>
                          </div>
                          <div>
                            <h4 class="font-semibold text-gray-900">{{ $document->title }}</h4>
                            <p class="text-sm text-gray-500">#{{ $document->id }}</p>
                          </div>
                        </div>
                      </td>
                      
                      <!-- Document Type Column -->
                      <td class="py-4 px-4 text-center">
                        <span class="text-sm font-medium text-gray-700">{{ ucfirst($document->category ?: 'Document') }}</span>
                      </td>
                      
                      <!-- Department Column -->
                      <td class="py-4 px-4 text-center">
                        <span class="text-sm font-medium text-gray-700">{{ $document->department ?: 'Not Set' }}</span>
                      </td>
                      
                      <!-- Date Created Column -->
                      <td class="py-4 px-4 text-center">
                        <span class="text-sm text-gray-600">{{ $document->created_at->format('M d, Y') }}</span>
                      </td>
                      
                      <!-- Confidentiality Level Column -->
                      <td class="py-4 px-4 text-center">
                        @php
                          $confidentialityConfig = [
                            'public' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Public'],
                            'internal' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Internal'],
                            'confidential' => ['class' => 'bg-orange-100 text-orange-800', 'text' => 'Confidential'],
                            'restricted' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Restricted']
                          ];
                          $confInfo = $confidentialityConfig[$document->confidentiality] ?? ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Not Set'];
                        @endphp
                        <span class="text-xs font-medium {{ $confInfo['class'] }} px-2 py-1 rounded-full">{{ $confInfo['text'] }}</span>
                      </td>
                      
                      <!-- Retention Period Column -->
                      <td class="py-4 px-4 text-center">
                        <span class="text-sm font-medium text-gray-700">{{ $document->retention_policy ?: 'Not Set' }}</span>
                      </td>
                      
                      <!-- Status Column -->
                      <td class="py-4 px-4 text-center">
                        @php
                          $statusConfig = [
                            'active' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Active'],
                            'archived' => ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Archived'],
                            'disposed' => ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Disposed'],
                            'expired' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Expired'],
                            'pending_release' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Pending Release']
                          ];
                          $status = $statusConfig[$document->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($document->status)];
                        @endphp
                        <span class="text-xs font-medium {{ $status['class'] }} px-2 py-1 rounded-full">{{ $status['text'] }}</span>
                      </td>
                      
                      <!-- Expiration Date Column -->
                      <td class="py-4 px-4 text-center">
                        @if($document->retention_until)
                          <span class="text-sm text-gray-600">{{ $document->retention_until->format('M d, Y') }}</span>
                        @else
                          <span class="text-sm text-gray-400">Not Set</span>
                        @endif
                      </td>
                      
                      <!-- Actions Column -->
                      <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center space-x-2">
                          <!-- View Button -->
                          <a href="{{ route('document.show', $document->id) }}" 
                             class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200" 
                             title="View Document">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                          </a>
                          
                          <!-- Download Button -->
                          <a href="{{ route('document.download', $document->id) }}" 
                             class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200" 
                             title="Download Document">
                            <i data-lucide="download" class="w-4 h-4"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="9" class="py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                          <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="file-text" class="w-10 h-10 text-gray-400"></i>
                          </div>
                          <h3 class="text-lg font-semibold text-gray-600 mb-2">No Documents Found</h3>
                          <p class="text-gray-500 text-sm">No documents match your current filters or no documents have been uploaded yet.</p>
                        </div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            
            <!-- Pagination -->
            @if($documents->hasPages())
              <div class="flex justify-center p-6 border-t border-gray-200">
                {{ $documents->links() }}
              </div>
            @endif
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Search and filter functionality
    function filterDocuments() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const statusFilter = document.getElementById('statusFilter').value;
      
      const rows = document.querySelectorAll('#documentsTable tbody tr');
      
      rows.forEach(row => {
        let showRow = true;
        
        // Search filter
        if (searchTerm) {
          const title = row.querySelector('td:nth-child(2) h4')?.textContent?.toLowerCase() || '';
          const description = row.querySelector('td:nth-child(2) p')?.textContent?.toLowerCase() || '';
          if (!title.includes(searchTerm) && !description.includes(searchTerm)) {
            showRow = false;
          }
        }
        
        // Status filter
        if (statusFilter && showRow) {
          const status = row.querySelector('td:nth-child(5) .badge')?.textContent?.toLowerCase() || '';
          if (!status.includes(statusFilter)) {
            showRow = false;
          }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
      });
    }
    
    function clearFilters() {
      document.getElementById('searchInput').value = '';
      document.getElementById('statusFilter').value = '';
      
      // Show all rows
      const rows = document.querySelectorAll('#documentsTable tbody tr');
      rows.forEach(row => {
        row.style.display = '';
      });
    }

    // Add event listeners
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        filterDocuments();
      }
    });
  </script>
</body>
</html>
