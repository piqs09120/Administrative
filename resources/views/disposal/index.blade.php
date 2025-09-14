<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Disposal History - Soliera</title>
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

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h1 class="text-3xl font-bold" style="color: var(--color-charcoal-ink);">Disposal History</h1>
            <p class="text-gray-600 mt-1">View all disposed documents and disposal audit trail</p>
          </div>
          <div class="flex gap-3">
            <a href="{{ route('disposal.export') }}" 
               class="btn btn-outline" 
               style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">
              <i data-lucide="download" class="w-4 h-4 mr-2"></i>
              Export CSV
            </a>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
          <!-- Total Disposed -->
          <div class="card bg-white shadow-xl">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-red-100 text-red-800 rounded-full w-12 h-12">
                    <i data-lucide="trash-2" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-error badge-outline">Total</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-red-600 justify-center mb-2">{{ $stats['total_disposed'] }}</h2>
                <p class="text-base-content/70">Documents Disposed</p>
              </div>
            </div>
          </div>

          <!-- Auto Expired -->
          <div class="card bg-white shadow-xl">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-orange-100 text-orange-800 rounded-full w-12 h-12">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-warning badge-outline">Auto</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-orange-600 justify-center mb-2">{{ $stats['auto_expired'] }}</h2>
                <p class="text-base-content/70">Auto Expired</p>
              </div>
            </div>
          </div>

          <!-- Manually Disposed -->
          <div class="card bg-white shadow-xl">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-blue-100 text-blue-800 rounded-full w-12 h-12">
                    <i data-lucide="user-x" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-info badge-outline">Manual</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-blue-600 justify-center mb-2">{{ $stats['manually_disposed'] }}</h2>
                <p class="text-base-content/70">Manually Disposed</p>
              </div>
            </div>
          </div>

          <!-- This Month -->
          <div class="card bg-white shadow-xl">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-green-100 text-green-800 rounded-full w-12 h-12">
                    <i data-lucide="calendar" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-success badge-outline">Month</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-green-600 justify-center mb-2">{{ $stats['this_month'] }}</h2>
                <p class="text-base-content/70">This Month</p>
              </div>
            </div>
          </div>

          <!-- This Week -->
          <div class="card bg-white shadow-xl">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-purple-100 text-purple-800 rounded-full w-12 h-12">
                    <i data-lucide="calendar-days" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-secondary badge-outline">Week</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-purple-600 justify-center mb-2">{{ $stats['this_week'] }}</h2>
                <p class="text-base-content/70">This Week</p>
              </div>
            </div>
          </div>
        </div>


        <!-- Disposed Documents Table -->
        <div class="card bg-white shadow-xl">
          <div class="card-body">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Disposed Documents</h3>
              <div class="text-sm text-gray-500">
                Showing {{ $disposedDocuments->count() }} of {{ $disposedDocuments->total() }} documents
              </div>
            </div>
            
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr class="bg-gray-50">
                    <th class="text-left py-4 px-4 font-semibold text-gray-700">Document</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Department</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Confidentiality</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Disposal Reason</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Disposed At</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Disposed By</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($disposedDocuments as $doc)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                      <!-- Document Column -->
                      <td class="py-4 px-4">
                        <div class="flex items-center space-x-3">
                          <div class="avatar placeholder">
                            <div class="bg-red-100 text-red-800 rounded-full w-10 h-10 flex items-center justify-center">
                              <i data-lucide="file-text" class="w-5 h-5"></i>
                            </div>
                          </div>
                          <div>
                            <h4 class="font-semibold text-gray-900">{{ $doc->document_title }}</h4>
                            <p class="text-sm text-gray-500">{{ $doc->file_name ?? 'Unknown file' }}</p>
                            <p class="text-xs text-gray-400">{{ $doc->formatted_file_size }}</p>
                          </div>
                        </div>
                      </td>
                      
                      <!-- Department Column -->
                      <td class="py-4 px-4 text-center">
                        <span class="text-sm font-medium text-gray-700">{{ $doc->document_department ?? 'N/A' }}</span>
                      </td>
                      
                      <!-- Confidentiality Column -->
                      <td class="py-4 px-4 text-center">
                        @if($doc->confidentiality_level)
                          <span class="badge badge-sm {{ $doc->confidentiality_badge_class }}">
                            {{ ucfirst($doc->confidentiality_level) }}
                          </span>
                        @else
                          <span class="text-sm text-gray-400">N/A</span>
                        @endif
                      </td>
                      
                      <!-- Disposal Reason Column -->
                      <td class="py-4 px-4 text-center">
                        <span class="badge badge-sm {{ $doc->disposal_reason === 'auto_expired' ? 'badge-warning' : 'badge-info' }}">
                          {{ $doc->disposal_reason_display }}
                        </span>
                      </td>
                      
                      <!-- Disposed At Column -->
                      <td class="py-4 px-4 text-center">
                        <span class="text-sm text-gray-600">{{ $doc->disposed_at->format('M d, Y H:i') }}</span>
                      </td>
                      
                      <!-- Disposed By Column -->
                      <td class="py-4 px-4 text-center">
                        <span class="text-sm text-gray-600">{{ $doc->disposer?->name ?? 'System' }}</span>
                      </td>
                      
                      <!-- Actions Column -->
                      <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center space-x-2">
                          <a href="{{ route('disposal.show', $doc->id) }}" 
                             class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200" 
                             title="View Details">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="7" class="py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                          <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="trash-2" class="w-10 h-10 text-gray-400"></i>
                          </div>
                          <h3 class="text-lg font-semibold text-gray-600 mb-2">No Disposed Documents Found</h3>
                          <p class="text-gray-500 text-sm">No documents have been disposed yet or match your current filters.</p>
                        </div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            
            <!-- Pagination -->
            @if($disposedDocuments->hasPages())
              <div class="flex justify-center p-6 border-t border-gray-200">
                {{ $disposedDocuments->links() }}
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
  </script>
</body>
</html>
