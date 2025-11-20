<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Archived Documents - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @vite(['resources/css/soliera.css'])
  @php
    use Illuminate\Support\Facades\Storage;
  @endphp
</head>
<body class="bg-base-100">
  <div class="flex h-screen overflow-hidden">
    @include('partials.sidebarr')
    <div class="flex flex-col flex-1 overflow-hidden">
      @include('partials.navbar')
      <main class="flex-1 overflow-y-auto p-8">
        <!-- Success Message for Archived Document -->
        @if(request('archived'))
          <div class="alert alert-success mb-6 shadow-lg">
            <i data-lucide="check-circle" class="w-6 h-6"></i>
            <span>Document has been successfully archived and is now displayed in the table below.</span>
          </div>
        @endif
        
        @if(session('success'))
          <div class="alert alert-success mb-6 shadow-lg">
            <i data-lucide="check-circle" class="w-6 h-6"></i>
            <span>{{ session('success') }}</span>
          </div>
        @endif
        
        <div class="mb-4">
          <h1 class="text-3xl font-bold" style="color: var(--color-charcoal-ink);">Archived Documents</h1>
        </div>
        <!-- underline divider (matches other modules) -->
        <div class="border-b border-gray-200 mb-6"></div>

        <!-- Status Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
          <!-- Total Documents -->
          <x-stat-card 
            title="Total Documents" 
            :value="$totalCount ?? $documents->total()" 
            icon="fa-file-text" 
            iconColor="text-yellow-400" 
            bgColor="bg-blue-900" />
          
          <!-- Received Today -->
          <x-stat-card 
            title="Received Today" 
            :value="$receivedToday ?? 0" 
            icon="fa-calendar" 
            iconColor="text-yellow-400" 
            bgColor="bg-blue-900" />
          
          <!-- Expired Documents -->
          <x-stat-card 
            title="Expired Documents" 
            :value="$expiredCount ?? 0" 
            icon="fa-check-circle" 
            iconColor="text-yellow-400" 
            bgColor="bg-blue-900" />
        </div>

        <!-- Tabs: List | Reports & Analytics -->
        @php 
          $validTabs = ['list','reports'];
          $tabParam = request('tab');
          $activeTab = in_array($tabParam, $validTabs) ? $tabParam : 'list';
        @endphp

        <div class="mb-3">
          <nav class="flex flex-wrap items-center gap-1 sm:gap-2 text-xs sm:text-sm">
            <button id="nav-docs" class="px-2 py-1 rounded {{ $activeTab==='list' ? 'text-blue-800 font-semibold bg-blue-50' : 'text-gray-600 hover:text-blue-600' }}" onclick="switchArchivedTab('list')">
              Documents
            </button>
            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300"></i>
            <button id="nav-reports" class="px-2 py-1 rounded {{ $activeTab==='reports' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-600 hover:text-blue-600' }}" onclick="switchArchivedTab('reports')">
              Reports & Analytics
            </button>
          </nav>
        </div>

        <!-- Complete Archived Documents Table -->
        <div id="archived-documents-tab" class="card bg-white shadow-xl {{ $activeTab==='list' ? '' : 'hidden' }}">
          <div class="card-body">
            <div class="flex items-center justify-between mb-6">
              <div></div>
              <div class="flex items-center gap-3">
                <!-- Search Field -->
                <div class="relative">
                  <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                  <input type="text" 
                         id="search-input" 
                         value="{{ request('search') }}" 
                         class="input input-bordered input-sm w-64 pl-10 pr-4 bg-white border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                         placeholder="Search documents..."
                         aria-label="Search archived documents"
                         autocomplete="off">
                </div>
                
                <!-- Filters Button with Badge -->
                <button type="button" 
                        id="filters-button"
                        onclick="openFiltersModal()"
                        class="btn btn-sm inline-flex items-center gap-2 whitespace-nowrap"
                        style="background: linear-gradient(135deg, #F7A923 0%, #E6940F 100%); color: #2C3E50; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25); border: none;"
                        onmouseover="this.style.background='linear-gradient(135deg, #E6940F 0%, #D2840E 100%)'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'"
                        onmouseout="this.style.background='linear-gradient(135deg, #F7A923 0%, #E6940F 100%)'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'"
                        aria-label="Open filters"
                        aria-expanded="false"
                        aria-controls="filters-modal">
                  <i data-lucide="filter" class="w-4 h-4"></i>
                  <span>Filters</span>
                  <span id="filter-badge" class="badge badge-primary badge-sm hidden">0</span>
                </button>
              </div>
            </div>
            
            <!-- Filter Chips Container -->
            <div id="filter-chips-container" class="mb-4 flex flex-wrap gap-2 hidden">
              <!-- Filter chips will be dynamically inserted here -->
            </div>
            
            <x-table-card :title="'Archived Documents'">
              <!-- Table -->
              <table class="table w-full">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="text-left py-4 px-4 font-semibold text-gray-700">Document Profile</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Type</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Department</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Created</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Confidentiality</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Retention</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Status</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    // Debug: Log what we have
                    \Log::info('Archived view rendering', [
                      'documents_count' => $documents->count(),
                      'documents_total' => $documents->total(),
                      'totalCount' => $totalCount ?? 0,
                      'receivedToday' => $receivedToday ?? 0,
                      'current_page' => $documents->currentPage(),
                      'has_items' => $documents->count() > 0
                    ]);
                  @endphp
                  @forelse($documents as $index => $document)
                    <tr class="hover:bg-gray-50 transition">
                      <!-- Document Profile Column -->
                      <td class="py-4 px-4">
                        <div class="flex items-center gap-3">
                          <div class="rounded-full w-12 h-12 bg-blue-900 flex items-center justify-center">
                            <span class="text-sm font-bold text-white">
                              {{ strtoupper(substr($document->title ?? 'UN', 0, 2)) }}
                            </span>
                          </div>
                          <div>
                            <div class="font-bold">{{ $document->title ?: 'Untitled Document' }}</div>
                            <div class="text-sm opacity-50">#{{ $document->id }}</div>
                          </div>
                        </div>
                      </td>
                      
                      <!-- Type Column -->
                      <td class="py-4 px-4 text-center">
                        <div class="text-sm opacity-80">{{ ucfirst($document->category ?: 'Unknown') }}</div>
                      </td>
                      
                      <!-- Department Column -->
                      <td class="py-4 px-4 text-center">
                        <div class="text-sm opacity-80">{{ $document->department ?: 'Unassigned' }}</div>
                      </td>
                      
                      <!-- Created Column -->
                      <td class="py-4 px-4 text-center">
                        <div class="text-sm opacity-80">{{ $document->created_at->format('M d, Y') }}</div>
                      </td>
                      
                      <!-- Confidentiality Column -->
                      <td class="py-4 px-4 text-center">
                        @php
                          $confidentialityLevel = $document->confidentiality_level ?? 'internal';
                          $confidentialityClass = match($confidentialityLevel) {
                            'restricted' => 'bg-red-500 text-white',
                            'confidential' => 'bg-orange-500 text-white',
                            'internal' => 'bg-green-500 text-white',
                            'public' => 'bg-green-500 text-white',
                            default => 'bg-gray-500 text-white'
                          };
                        @endphp
                        <span class="px-2 py-1 rounded-full text-white text-xs {{ $confidentialityClass }}">
                          {{ ucfirst($confidentialityLevel) }}
                        </span>
                      </td>
                      
                      <!-- Retention Column -->
                      <td class="py-4 px-4 text-center">
                        @php
                          $retentionPeriod = $document->retention_period ?? match($document->category) {
                            'contract' => '7 Years',
                            'legal' => '10 Years',
                            'policy' => '5 Years',
                            'report' => '3 Years',
                            default => '2 Years'
                          };
                        @endphp
                        <div class="text-sm opacity-80">{{ $retentionPeriod }}</div>
                      </td>
                      
                      <!-- Status Column -->
                      <td class="py-4 px-4 text-center">
                        @php
                          $status = $document->status ?? 'active';
                          $statusClass = match($status) {
                            'expired' => 'bg-red-500 text-white',
                            'expiring_soon' => 'bg-orange-500 text-white',
                            'active' => 'bg-green-500 text-white',
                            'archived' => 'bg-green-500 text-white',
                            'disposed' => 'bg-gray-500 text-white',
                            default => 'bg-gray-500 text-white'
                          };
                        @endphp
                        <span class="px-2 py-1 rounded-full text-white text-xs {{ $statusClass }}">
                          {{ ucfirst($status) }}
                        </span>
                      </td>

                      <!-- Actions Column -->
                      <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center space-x-2">
                          <!-- Kebab Menu Button -->
                          <div class="dropdown dropdown-end">
                            <button type="button" 
                                    class="p-2 rounded-lg transition-all duration-200 hover:scale-110"
                                    style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                    title="More options"
                                    tabindex="0">
                              <i data-lucide="more-vertical" class="w-4 h-4" style="fill: none;"></i>
                            </button>
                            <ul class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow-lg border border-gray-200" tabindex="0">
                              <li>
                                <a onclick="viewDocument({{ $document->id }})" class="flex items-center gap-2">
                                  <i data-lucide="info" class="w-4 h-4"></i>
                                  <span>View details</span>
                                </a>
                              </li>
                              <li>
                                <a onclick="downloadDocument({{ $document->id }})" class="flex items-center gap-2">
                                  <i data-lucide="download" class="w-4 h-4"></i>
                                  <span>Download</span>
                                </a>
                              </li>
                              <li>
                                <a onclick="showShareDialog({{ $document->id }})" class="flex items-center gap-2">
                                  <i data-lucide="user-plus" class="w-4 h-4"></i>
                                  <span>Share</span>
                                </a>
                              </li>
                              <li>
                                <a onclick="showVersionHistory({{ $document->id }})" class="flex items-center gap-2">
                                  <i data-lucide="clock" class="w-4 h-4"></i>
                                  <span>Version History</span>
                                </a>
                              </li>
                            </ul>
                          </div>
                          
                          @if($document->status === 'expired')
                            <button onclick="disposeDocument({{ $document->id }})" 
                                    class="p-2 rounded-lg transition-all duration-200 hover:scale-110"
                                    style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                    title="Dispose Document">
                              <i data-lucide="trash-2" class="w-4 h-4" style="fill: none;"></i>
                            </button>
                          @endif
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="8" class="py-12 text-center">
                        <div class="flex flex-col items-center">
                          <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="archive" class="w-10 h-10 text-gray-400"></i>
                          </div>
                          <h3 class="text-lg font-semibold text-gray-600 mb-2">No Archived Documents Found</h3>
                          @if(($totalCount ?? 0) > 0 || ($receivedToday ?? 0) > 0)
                            <p class="text-gray-500 text-sm mb-4">
                              Documents may be filtered out. Try clearing your filters or search.
                            </p>
                            <button onclick="FilterState.clearAll(); FilterState.applyFilters();" 
                                    class="btn btn-primary btn-sm mt-2">
                              <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                              Clear All Filters
                            </button>
                          @else
                            <p class="text-gray-500 text-sm mb-4">No documents have been archived yet.</p>
                          @endif
                        </div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </x-table-card>
            
            <!-- Compact Pagination -->
            @if($documents->hasPages())
              <div class="mt-4 flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200">
                <div class="flex items-center gap-2">
                  <span class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $documents->firstItem() }}</span> to 
                    <span class="font-medium">{{ $documents->lastItem() }}</span> of 
                    <span class="font-medium">{{ $documents->total() }}</span> results
                  </span>
                </div>
                
                <nav class="flex items-center gap-1" aria-label="Pagination">
                  <!-- Previous Button -->
                  @if($documents->onFirstPage())
                    <span class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">
                      Previous
                    </span>
                  @else
                    <a href="{{ $documents->previousPageUrl() }}" 
                       class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-colors">
                      Previous
                    </a>
                  @endif
                  
                  <!-- Page Numbers -->
                  @php
                    $currentPage = $documents->currentPage();
                    $lastPage = $documents->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                    
                    // Adjust if we're near the start
                    if ($currentPage <= 3) {
                      $endPage = min(5, $lastPage);
                    }
                    // Adjust if we're near the end
                    if ($currentPage >= $lastPage - 2) {
                      $startPage = max(1, $lastPage - 4);
                    }
                  @endphp
                  
                  @if($startPage > 1)
                    <a href="{{ $documents->url(1) }}" 
                       class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-colors">
                      1
                    </a>
                    @if($startPage > 2)
                      <span class="px-2 py-1.5 text-sm text-gray-500">…</span>
                    @endif
                  @endif
                  
                  @for($i = $startPage; $i <= $endPage; $i++)
                    @if($i == $currentPage)
                      <span class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md cursor-default">
                        {{ $i }}
                      </span>
                    @else
                      <a href="{{ $documents->url($i) }}" 
                         class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-colors">
                        {{ $i }}
                      </a>
                    @endif
                  @endfor
                  
                  @if($endPage < $lastPage)
                    @if($endPage < $lastPage - 1)
                      <span class="px-2 py-1.5 text-sm text-gray-500">…</span>
                    @endif
                    <a href="{{ $documents->url($lastPage) }}" 
                       class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-colors">
                      {{ $lastPage }}
                    </a>
                  @endif
                  
                  <!-- Next Button -->
                  @if($documents->hasMorePages())
                    <a href="{{ $documents->nextPageUrl() }}" 
                       class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-colors">
                      Next
                    </a>
                  @else
                    <span class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">
                      Next
                    </span>
                  @endif
                </nav>
              </div>
            @endif
          </div>
        </div>

        <!-- Document Details Panel (Google Drive Style) -->
        <div id="document-details-panel" class="fixed top-0 right-0 h-full w-96 bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto">
          <div class="sticky top-0 bg-white border-b border-gray-200 z-10">
            <!-- Panel Header -->
            <div class="flex items-center justify-between p-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-900 flex items-center justify-center flex-shrink-0">
                  <span class="text-sm font-bold text-white" id="details-doc-initials">--</span>
                </div>
                <div>
                  <h3 id="details-doc-title" class="font-semibold text-gray-900 text-sm">Document</h3>
                  <p id="details-doc-id" class="text-xs text-gray-500">#--</p>
                </div>
              </div>
              <button onclick="closeDocumentDetailsPanel()" 
                      class="btn btn-sm btn-circle btn-ghost"
                      title="Hide details">
                <i data-lucide="x" class="w-4 h-4"></i>
              </button>
            </div>
            
            <!-- Tabs -->
            <div class="flex border-b border-gray-200">
              <button id="details-tab-btn" 
                      onclick="switchDetailsTab('details')"
                      class="flex-1 px-4 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-blue-50">
                Details
              </button>
              <button id="activity-tab-btn" 
                      onclick="switchDetailsTab('activity')"
                      class="flex-1 px-4 py-3 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 hover:border-gray-300">
                Activity
              </button>
            </div>
          </div>

          <!-- Details Tab Content -->
          <div id="details-tab-content" class="p-4 space-y-6">
            <!-- Document Information -->
            <div>
              <h4 class="text-sm font-semibold text-gray-700 mb-3">Document Information</h4>
              <div class="space-y-3">
                <div>
                  <label class="text-xs text-gray-500">Title</label>
                  <p id="details-title" class="text-sm text-gray-900 mt-1">--</p>
                </div>
                <div>
                  <label class="text-xs text-gray-500">Description</label>
                  <p id="details-description" class="text-sm text-gray-900 mt-1">--</p>
                </div>
                <div>
                  <label class="text-xs text-gray-500">Category</label>
                  <p id="details-category" class="text-sm text-gray-900 mt-1">--</p>
                </div>
                <div>
                  <label class="text-xs text-gray-500">Department</label>
                  <p id="details-department" class="text-sm text-gray-900 mt-1">--</p>
                </div>
                <div>
                  <label class="text-xs text-gray-500">Status</label>
                  <p id="details-status" class="text-sm text-gray-900 mt-1">--</p>
                </div>
              </div>
            </div>

            <!-- Dates -->
            <div>
              <h4 class="text-sm font-semibold text-gray-700 mb-3">Dates</h4>
              <div class="space-y-3">
                <div>
                  <label class="text-xs text-gray-500">Created</label>
                  <p id="details-created" class="text-sm text-gray-900 mt-1">--</p>
                </div>
                <div>
                  <label class="text-xs text-gray-500">Last Modified</label>
                  <p id="details-modified" class="text-sm text-gray-900 mt-1">--</p>
                </div>
                <div>
                  <label class="text-xs text-gray-500">Archived Date</label>
                  <p id="details-archived" class="text-sm text-gray-900 mt-1">--</p>
                </div>
                <div>
                  <label class="text-xs text-gray-500">Disposal Date</label>
                  <p id="details-disposal" class="text-sm text-gray-900 mt-1">--</p>
                </div>
              </div>
            </div>

            <!-- Retention & Confidentiality -->
            <div>
              <h4 class="text-sm font-semibold text-gray-700 mb-3">Retention & Confidentiality</h4>
              <div class="space-y-3">
                <div>
                  <label class="text-xs text-gray-500">Retention Period</label>
                  <p id="details-retention" class="text-sm text-gray-900 mt-1">--</p>
                </div>
                <div>
                  <label class="text-xs text-gray-500">Confidentiality Level</label>
                  <p id="details-confidentiality" class="text-sm text-gray-900 mt-1">--</p>
                </div>
              </div>
            </div>

            <!-- Who has access -->
            <div>
              <h4 class="text-sm font-semibold text-gray-700 mb-3">Who has access</h4>
              <div id="details-access-list" class="space-y-2">
                <!-- Access list will be populated here -->
              </div>
              <button onclick="showManageAccess()" class="btn btn-sm btn-outline mt-3 w-full">
                <i data-lucide="users" class="w-4 h-4 mr-2"></i>
                Manage access
              </button>
            </div>

            <!-- Security Limitations -->
            <div>
              <h4 class="text-sm font-semibold text-gray-700 mb-3">Security limitations</h4>
              <p class="text-sm text-gray-500">No limitations applied</p>
              <p class="text-xs text-gray-400 mt-1">If any are applied, they will appear here</p>
            </div>
          </div>

          <!-- Activity Tab Content -->
          <div id="activity-tab-content" class="p-4 space-y-4 hidden">
            <div id="activity-list" class="space-y-4">
              <!-- Activity items will be populated here -->
              <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <i data-lucide="activity" class="w-8 h-8 text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-500">Loading activity...</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Overlay for details panel -->
        <div id="details-panel-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeDocumentDetailsPanel()"></div>

        <!-- Filters Modal/Drawer -->
        <div id="filters-modal" 
             class="modal lg:modal-bottom lg:modal-middle"
             role="dialog"
             aria-labelledby="filters-modal-title"
             aria-modal="true">
          <div class="modal-box lg:max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
              <h3 id="filters-modal-title" class="text-xl font-bold flex items-center gap-2">
                <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i>
                Advanced Filters
              </h3>
              <button type="button" 
                      onclick="closeFiltersModal()" 
                      class="btn btn-sm btn-circle btn-ghost"
                      aria-label="Close filters">
                <i data-lucide="x" class="w-5 h-5"></i>
              </button>
            </div>
            
            <form id="filters-form" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Category -->
                <div class="form-control">
                  <label for="filter-category" class="label">
                    <span class="label-text font-medium">Category</span>
                  </label>
                  <select id="filter-category" name="category" class="select select-bordered w-full">
                    <option value="">All Categories</option>
                    <option value="contract" {{ request('category') == 'contract' ? 'selected' : '' }}>Contract</option>
                    <option value="policy" {{ request('category') == 'policy' ? 'selected' : '' }}>Policy</option>
                    <option value="legal_case" {{ request('category') == 'legal_case' ? 'selected' : '' }}>Legal Case</option>
                    <option value="compliance" {{ request('category') == 'compliance' ? 'selected' : '' }}>Compliance</option>
                    <option value="financial" {{ request('category') == 'financial' ? 'selected' : '' }}>Financial</option>
                  </select>
                </div>

                <!-- Department -->
                <div class="form-control">
                  <label for="filter-department" class="label">
                    <span class="label-text font-medium">Department</span>
                  </label>
                  <select id="filter-department" name="department" class="select select-bordered w-full">
                    <option value="">All Departments</option>
                    <option value="Legal" {{ request('department') == 'Legal' ? 'selected' : '' }}>Legal</option>
                    <option value="HR" {{ request('department') == 'HR' ? 'selected' : '' }}>HR</option>
                    <option value="Finance" {{ request('department') == 'Finance' ? 'selected' : '' }}>Finance</option>
                    <option value="Operations" {{ request('department') == 'Operations' ? 'selected' : '' }}>Operations</option>
                  </select>
                </div>

                <!-- Author -->
                <div class="form-control">
                  <label for="filter-author" class="label">
                    <span class="label-text font-medium">Author</span>
                  </label>
                  <input type="text" 
                         id="filter-author" 
                         name="author" 
                         value="{{ request('author') }}"
                         class="input input-bordered w-full"
                         placeholder="Author name...">
                </div>

                <!-- Confidentiality -->
                <div class="form-control">
                  <label for="filter-confidentiality" class="label">
                    <span class="label-text font-medium">Confidentiality</span>
                  </label>
                  <select id="filter-confidentiality" name="confidentiality" class="select select-bordered w-full">
                    <option value="">All Levels</option>
                    <option value="public" {{ request('confidentiality') == 'public' ? 'selected' : '' }}>Public</option>
                    <option value="internal" {{ request('confidentiality') == 'internal' ? 'selected' : '' }}>Internal</option>
                    <option value="confidential" {{ request('confidentiality') == 'confidential' ? 'selected' : '' }}>Confidential</option>
                    <option value="restricted" {{ request('confidentiality') == 'restricted' ? 'selected' : '' }}>Restricted</option>
                  </select>
                </div>

                <!-- Date From -->
                <div class="form-control">
                  <label for="filter-date-from" class="label">
                    <span class="label-text font-medium">From Date</span>
                  </label>
                  <input type="date" 
                         id="filter-date-from" 
                         name="date_from" 
                         value="{{ request('date_from') }}"
                         class="input input-bordered w-full"
                         aria-describedby="date-from-error">
                  <div id="date-from-error" class="label-text-alt text-error hidden"></div>
                </div>

                <!-- Date To -->
                <div class="form-control">
                  <label for="filter-date-to" class="label">
                    <span class="label-text font-medium">To Date</span>
                  </label>
                  <input type="date" 
                         id="filter-date-to" 
                         name="date_to" 
                         value="{{ request('date_to') }}"
                         class="input input-bordered w-full"
                         aria-describedby="date-to-error">
                  <div id="date-to-error" class="label-text-alt text-error hidden"></div>
                </div>

                <!-- Sort By -->
                <div class="form-control">
                  <label for="filter-sort-by" class="label">
                    <span class="label-text font-medium">Sort By</span>
                  </label>
                  <select id="filter-sort-by" name="sort_by" class="select select-bordered w-full">
                    <option value="created_at" {{ request('sort_by') == 'created_at' || !request('sort_by') ? 'selected' : '' }}>Date Created</option>
                    <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Title</option>
                    <option value="author" {{ request('sort_by') == 'author' ? 'selected' : '' }}>Author</option>
                    <option value="view_count" {{ request('sort_by') == 'view_count' ? 'selected' : '' }}>Most Viewed</option>
                    <option value="download_count" {{ request('sort_by') == 'download_count' ? 'selected' : '' }}>Most Downloaded</option>
                    <option value="last_edited_at" {{ request('sort_by') == 'last_edited_at' ? 'selected' : '' }}>Recently Edited</option>
                  </select>
                </div>

                <!-- Sort Order -->
                <div class="form-control">
                  <label for="filter-sort-order" class="label">
                    <span class="label-text font-medium">Order</span>
                  </label>
                  <select id="filter-sort-order" name="sort_order" class="select select-bordered w-full">
                    <option value="desc" {{ request('sort_order') == 'desc' || !request('sort_order') ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                  </select>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                <button type="button" 
                        onclick="clearAllFilters()" 
                        class="btn btn-ghost btn-sm sm:btn-md flex-1"
                        aria-label="Clear all filters">
                  <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                  Clear All
                </button>
                <button type="button" 
                        onclick="resetFilters()" 
                        class="btn btn-outline btn-sm sm:btn-md flex-1"
                        aria-label="Reset filters to default">
                  <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                  Reset
                </button>
                <button type="button" 
                        onclick="applyFilters()" 
                        class="btn btn-primary btn-sm sm:btn-md flex-1"
                        aria-label="Apply filters">
                  <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                  Apply Filters
                </button>
              </div>
            </form>
          </div>
          <div class="modal-backdrop" onclick="closeFiltersModal()" aria-label="Close filters"></div>
        </div>

        <!-- Reports & Analytics Tab -->
        <div id="archived-reports-tab" class="card bg-white shadow-xl {{ $activeTab==='reports' ? '' : 'hidden' }}">
          <div class="card-body">
            <div class="flex items-center justify-between mb-6">
              <h3 class="card-title text-xl flex items-center gap-2">
                <i data-lucide="bar-chart" class="w-6 h-6 text-emerald-600"></i>
                Reports & Analytics
              </h3>
              <div class="flex items-center gap-2">
                <select id="rep-range" class="select select-bordered select-sm">
                  <option value="30">Last 30 days</option>
                  <option value="90">Last 90 days</option>
                  <option value="365">Last 12 months</option>
                </select>
              </div>
            </div>

            <!-- KPI cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
              <div class="card bg-base-100 border-l-4 border-l-primary"><div class="card-body p-4"><div class="text-sm text-gray-500">Total Archived</div><div id="rep-total" class="text-2xl font-bold">0</div></div></div>
              <div class="card bg-base-100 border-l-4 border-l-success"><div class="card-body p-4"><div class="text-sm text-gray-500">Active</div><div id="rep-active" class="text-2xl font-bold">0</div></div></div>
              <div class="card bg-base-100 border-l-4 border-l-warning"><div class="card-body p-4"><div class="text-sm text-gray-500">Expiring Soon</div><div id="rep-expiring" class="text-2xl font-bold">0</div></div></div>
              <div class="card bg-base-100 border-l-4 border-l-error"><div class="card-body p-4"><div class="text-sm text-gray-500">Expired</div><div id="rep-expired" class="text-2xl font-bold">0</div></div></div>
            </div>

            <!-- Simple charts container (can be wired to Chart.js later) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div class="p-4 rounded-lg border">
                <div class="text-sm font-semibold mb-2">Archived by Department</div>
                <div id="rep-by-dept" class="text-sm text-gray-600">Loading…</div>
              </div>
              <div class="p-4 rounded-lg border">
                <div class="text-sm font-semibold mb-2">Archived by Type</div>
                <div id="rep-by-type" class="text-sm text-gray-600">Loading…</div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    function switchArchivedTab(tab){
      // Get tab elements
      const docsTab = document.getElementById('archived-documents-tab');
      const reportsTab = document.getElementById('archived-reports-tab');
      const navDocs = document.getElementById('nav-docs');
      const navReports = document.getElementById('nav-reports');

      if (tab === 'reports') {
        // Show reports tab, hide documents tab
        if (docsTab) docsTab.classList.add('hidden');
        if (reportsTab) reportsTab.classList.remove('hidden');
        
        // Update navigation buttons
        if (navDocs) {
          navDocs.classList.remove('text-blue-800', 'font-semibold', 'bg-blue-50');
          navDocs.classList.add('text-gray-600');
        }
        if (navReports) {
          navReports.classList.remove('text-gray-600');
          navReports.classList.add('text-blue-600', 'font-semibold', 'bg-blue-50');
        }
        
        // Update URL without page refresh
        try {
          const url = new URL(window.location.href);
          url.searchParams.set('tab', 'reports');
          window.history.replaceState({}, '', url);
        } catch(e) {
          // Fallback
          window.history.replaceState({}, '', '?tab=reports');
        }
      } else {
        // Show documents tab, hide reports tab
        if (docsTab) docsTab.classList.remove('hidden');
        if (reportsTab) reportsTab.classList.add('hidden');
        
        // Update navigation buttons
        if (navDocs) {
          navDocs.classList.remove('text-gray-600');
          navDocs.classList.add('text-blue-800', 'font-semibold', 'bg-blue-50');
        }
        if (navReports) {
          navReports.classList.remove('text-blue-600', 'font-semibold', 'bg-blue-50');
          navReports.classList.add('text-gray-600');
        }
        
        // Update URL without page refresh
        try {
          const url = new URL(window.location.href);
          url.searchParams.set('tab', 'list');
          window.history.replaceState({}, '', url);
        } catch(e) {
          // Fallback
          window.history.replaceState({}, '', '?tab=list');
        }
      }
      
      // Recreate icons after tab switch
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

    document.addEventListener('DOMContentLoaded', function(){
      // Initialize tab state based on URL parameter
      const urlParams = new URLSearchParams(window.location.search);
      const activeTab = urlParams.get('tab') || 'list';
      
      // Set initial tab visibility
      const docsTab = document.getElementById('archived-documents-tab');
      const reportsTab = document.getElementById('archived-reports-tab');
      const navDocs = document.getElementById('nav-docs');
      const navReports = document.getElementById('nav-reports');
      
      if (activeTab === 'reports') {
        if (docsTab) docsTab.classList.add('hidden');
        if (reportsTab) reportsTab.classList.remove('hidden');
        if (navDocs) {
          navDocs.classList.remove('text-blue-800', 'font-semibold', 'bg-blue-50');
          navDocs.classList.add('text-gray-600');
        }
        if (navReports) {
          navReports.classList.remove('text-gray-600');
          navReports.classList.add('text-blue-600', 'font-semibold', 'bg-blue-50');
        }
      } else {
        if (docsTab) docsTab.classList.remove('hidden');
        if (reportsTab) reportsTab.classList.add('hidden');
        if (navDocs) {
          navDocs.classList.remove('text-gray-600');
          navDocs.classList.add('text-blue-800', 'font-semibold', 'bg-blue-50');
        }
        if (navReports) {
          navReports.classList.remove('text-blue-600', 'font-semibold', 'bg-blue-50');
          navReports.classList.add('text-gray-600');
        }
      }
      
      // Lightweight client-side analytics using the existing table rows
      try{
        const rows = Array.from(document.querySelectorAll('table tbody tr'));
        const data = rows.map(r => ({
          dept: (r.querySelector('td:nth-child(3)')?.textContent || '').trim(),
          type: (r.querySelector('td:nth-child(2)')?.textContent || '').trim(),
          status: (r.querySelector('td:nth-child(7) span')?.textContent || '').trim().toLowerCase()
        }));

        const total = data.length;
        const active = data.filter(d => d.status === 'active').length;
        const expiring = data.filter(d => d.status === 'expiring soon').length;
        const expired = data.filter(d => d.status === 'expired').length;

        const byDept = {};
        const byType = {};
        data.forEach(d => { byDept[d.dept] = (byDept[d.dept]||0)+1; byType[d.type]=(byType[d.type]||0)+1; });

        const el = id => document.getElementById(id);
        if(el('rep-total')) el('rep-total').textContent = total;
        if(el('rep-active')) el('rep-active').textContent = active;
        if(el('rep-expiring')) el('rep-expiring').textContent = expiring;
        if(el('rep-expired')) el('rep-expired').textContent = expired;

        if(el('rep-by-dept')) el('rep-by-dept').innerHTML = Object.keys(byDept).length
          ? Object.entries(byDept).map(([k,v])=>`<div class=\"flex justify-between py-1\"><span>${k||'—'}</span><span class=\"font-semibold\">${v}</span></div>`).join('')
          : '<span class="text-gray-400">No data</span>';
        if(el('rep-by-type')) el('rep-by-type').innerHTML = Object.keys(byType).length
          ? Object.entries(byType).map(([k,v])=>`<div class=\"flex justify-between py-1\"><span>${k||'—'}</span><span class=\"font-semibold\">${v}</span></div>`).join('')
          : '<span class="text-gray-400">No data</span>';
      }catch(e){}
    });
  </script>
  <!-- Archive Confirmation Modal -->
  <div id="archiveModal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Confirm Archive</h3>
      <p class="py-4">Are you sure you want to archive this document? You can restore it anytime from the Archived Documents section.</p>
      <div class="modal-action">
        <button class="btn btn-ghost" onclick="closeArchiveModal()">Cancel</button>
        <button class="btn btn-warning" onclick="confirmArchive()">Archive Document</button>
      </div>
    </div>
  </div>

  <!-- Unarchive Confirmation Modal -->
  <div id="unarchiveModal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Confirm Unarchive</h3>
      <p class="py-4">Are you sure you want to restore this document? It will be moved back to the active documents list.</p>
      
      <!-- Password Input Field - Only for Administrators -->
      @if(auth()->user()->role === 'Administrator')
        <div class="form-control w-full mb-4">
          <label class="label">
            <span class="label-text font-semibold">Enter Administrator Password to Confirm</span>
          </label>
          <input type="password" id="unarchivePassword" class="input input-bordered w-full" placeholder="Enter administrator password" />
          <div class="label">
            <span class="label-text-alt">Administrator password required to restore archived documents</span>
          </div>
        </div>
        
        <!-- Error Message -->
        <div id="passwordError" class="alert alert-error mb-4 hidden">
          <i data-lucide="alert-circle" class="w-4 h-4"></i>
          <span id="errorMessage">Incorrect password. Please try again.</span>
        </div>
      @else
        <!-- Non-Administrator Message -->
        <div class="alert alert-warning mb-4">
          <i data-lucide="shield-x" class="w-4 h-4"></i>
          <span>Only administrators can restore archived documents. Please contact your system administrator.</span>
        </div>
      @endif
      
      <div class="modal-action">
        <button class="btn btn-ghost" onclick="closeUnarchiveModal()">Cancel</button>
        @if(auth()->user()->role === 'Administrator')
          <button class="btn btn-success" onclick="confirmUnarchive()">Restore Document</button>
        @else
          <button class="btn btn-disabled" disabled>Restore Document</button>
        @endif
      </div>
    </div>
  </div>

  <!-- Permanent Delete Confirmation Modal -->
  <div id="permanentDeleteModal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Confirm Permanent Deletion</h3>
      <p class="py-4 text-red-600 font-semibold">⚠️ WARNING: This action cannot be undone!</p>
      <p class="py-4">Are you absolutely sure you want to permanently delete this archived document? This will remove it from the system completely.</p>
      <div class="modal-action">
        <button class="btn btn-ghost" onclick="closePermanentDeleteModal()">Cancel</button>
        <button class="btn btn-error" onclick="confirmPermanentDelete()">Permanently Delete</button>
      </div>
    </div>
  </div>

  <!-- Document History Modal -->
  <div id="historyModal" class="modal">
    <div class="modal-box max-w-4xl">
      <h3 class="font-bold text-lg mb-4">Document History & Tracking</h3>
      <div id="historyContent" class="max-h-96 overflow-y-auto">
        <!-- History content will be loaded here -->
      </div>
      <div class="modal-action">
        <button class="btn btn-ghost" onclick="closeHistoryModal()">Close</button>
      </div>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <style>
    /* Responsive Modal Styles */
    @media (max-width: 1023px) {
      /* Mobile: Bottom sheet style */
      #filters-modal.modal {
        align-items: flex-end;
      }
      #filters-modal .modal-box {
        border-radius: 1rem 1rem 0 0;
        max-height: 85vh;
        margin-bottom: 0;
      }
    }
    
    @media (min-width: 1024px) {
      /* Desktop: Centered popover */
      #filters-modal.modal {
        align-items: center;
      }
      #filters-modal .modal-box {
        border-radius: 0.5rem;
        max-height: 90vh;
      }
    }
    
    /* Filter chip animations */
    #filter-chips-container .badge {
      animation: slideIn 0.2s ease-out;
    }
    
    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    /* Focus styles for accessibility */
    #filters-modal input:focus,
    #filters-modal select:focus {
      outline: 2px solid #3b82f6;
      outline-offset: 2px;
    }
    
    /* Loading state for search */
    #search-input:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }
  </style>
  
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Filter State Management
    const FilterState = {
      search: '',
      category: '',
      author: '',
      department: '',
      date_from: '',
      date_to: '',
      confidentiality: '',
      sort_by: 'created_at',
      sort_order: 'desc',
      
      // Initialize from URL params
      init() {
        const params = new URLSearchParams(window.location.search);
        this.search = params.get('search') || '';
        this.category = params.get('category') || '';
        this.author = params.get('author') || '';
        this.department = params.get('department') || '';
        this.date_from = params.get('date_from') || '';
        this.date_to = params.get('date_to') || '';
        this.confidentiality = params.get('confidentiality') || '';
        this.sort_by = params.get('sort_by') || 'created_at';
        this.sort_order = params.get('sort_order') || 'desc';
        
        // Populate form fields
        this.populateForm();
        this.updateFilterBadge();
        this.renderFilterChips();
      },
      
      // Populate filter form from state
      populateForm() {
        document.getElementById('search-input').value = this.search;
        document.getElementById('filter-category').value = this.category;
        document.getElementById('filter-author').value = this.author;
        document.getElementById('filter-department').value = this.department;
        document.getElementById('filter-date-from').value = this.date_from;
        document.getElementById('filter-date-to').value = this.date_to;
        document.getElementById('filter-confidentiality').value = this.confidentiality;
        document.getElementById('filter-sort-by').value = this.sort_by;
        document.getElementById('filter-sort-order').value = this.sort_order;
      },
      
      // Get active filter count
      getActiveCount() {
        let count = 0;
        if (this.category) count++;
        if (this.author) count++;
        if (this.department) count++;
        if (this.date_from) count++;
        if (this.date_to) count++;
        if (this.confidentiality) count++;
        if (this.sort_by !== 'created_at') count++;
        if (this.sort_order !== 'desc') count++;
        return count;
      },
      
      // Update filter badge
      updateFilterBadge() {
        const badge = document.getElementById('filter-badge');
        const count = this.getActiveCount();
        if (count > 0) {
          badge.textContent = count;
          badge.classList.remove('hidden');
        } else {
          badge.classList.add('hidden');
        }
      },
      
      // Render filter chips
      renderFilterChips() {
        const container = document.getElementById('filter-chips-container');
        const chips = [];
        
        if (this.category) {
          chips.push({ key: 'category', label: 'Category', value: this.getCategoryLabel(this.category) });
        }
        if (this.author) {
          chips.push({ key: 'author', label: 'Author', value: this.author });
        }
        if (this.department) {
          chips.push({ key: 'department', label: 'Department', value: this.department });
        }
        if (this.date_from || this.date_to) {
          const dateRange = [this.date_from, this.date_to].filter(Boolean).join(' to ');
          chips.push({ key: 'date', label: 'Date Range', value: dateRange });
        }
        if (this.confidentiality) {
          chips.push({ key: 'confidentiality', label: 'Confidentiality', value: this.getConfidentialityLabel(this.confidentiality) });
        }
        if (this.sort_by !== 'created_at') {
          chips.push({ key: 'sort_by', label: 'Sort By', value: this.getSortByLabel(this.sort_by) });
        }
        if (this.sort_order !== 'desc') {
          chips.push({ key: 'sort_order', label: 'Order', value: this.sort_order === 'asc' ? 'Ascending' : 'Descending' });
        }
        
        if (chips.length > 0) {
          container.innerHTML = chips.map(chip => `
            <div class="badge badge-primary badge-lg gap-2 px-3 py-2">
              <span class="font-medium">${chip.label}:</span>
              <span>${chip.value}</span>
              <button type="button" 
                      onclick="removeFilterChip('${chip.key}')" 
                      class="ml-1 hover:bg-primary-focus rounded-full p-0.5"
                      aria-label="Remove ${chip.label} filter">
                <i data-lucide="x" class="w-3 h-3"></i>
              </button>
            </div>
          `).join('');
          container.classList.remove('hidden');
          lucide.createIcons();
        } else {
          container.classList.add('hidden');
          container.innerHTML = '';
        }
      },
      
      // Get label helpers
      getCategoryLabel(value) {
        const labels = {
          'contract': 'Contract',
          'policy': 'Policy',
          'legal_case': 'Legal Case',
          'compliance': 'Compliance',
          'financial': 'Financial'
        };
        return labels[value] || value;
      },
      
      getConfidentialityLabel(value) {
        return value.charAt(0).toUpperCase() + value.slice(1);
      },
      
      getSortByLabel(value) {
        const labels = {
          'created_at': 'Date Created',
          'title': 'Title',
          'author': 'Author',
          'view_count': 'Most Viewed',
          'download_count': 'Most Downloaded',
          'last_edited_at': 'Recently Edited'
        };
        return labels[value] || value;
      },
      
      // Update URL and reload
      applyFilters() {
        const params = new URLSearchParams();
        
        if (this.search) params.set('search', this.search);
        if (this.category) params.set('category', this.category);
        if (this.author) params.set('author', this.author);
        if (this.department) params.set('department', this.department);
        if (this.date_from) params.set('date_from', this.date_from);
        if (this.date_to) params.set('date_to', this.date_to);
        if (this.confidentiality) params.set('confidentiality', this.confidentiality);
        if (this.sort_by !== 'created_at') params.set('sort_by', this.sort_by);
        if (this.sort_order !== 'desc') params.set('sort_order', this.sort_order);
        
        // Preserve tab parameter
        const currentTab = new URLSearchParams(window.location.search).get('tab');
        if (currentTab) params.set('tab', currentTab);
        
        window.location.search = params.toString();
      },
      
      // Clear all filters
      clearAll() {
        this.search = '';
        this.category = '';
        this.author = '';
        this.department = '';
        this.date_from = '';
        this.date_to = '';
        this.confidentiality = '';
        this.sort_by = 'created_at';
        this.sort_order = 'desc';
        this.populateForm();
        this.updateFilterBadge();
        this.renderFilterChips();
      },
      
      // Remove specific filter
      removeFilter(key) {
        switch(key) {
          case 'category': this.category = ''; break;
          case 'author': this.author = ''; break;
          case 'department': this.department = ''; break;
          case 'date': this.date_from = ''; this.date_to = ''; break;
          case 'confidentiality': this.confidentiality = ''; break;
          case 'sort_by': this.sort_by = 'created_at'; break;
          case 'sort_order': this.sort_order = 'desc'; break;
        }
        this.populateForm();
        this.updateFilterBadge();
        this.renderFilterChips();
        this.applyFilters();
      }
    };
    
    // Debounced search function
    let searchTimeout;
    function debounceSearch() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        FilterState.search = document.getElementById('search-input').value.trim();
        FilterState.applyFilters();
      }, 500);
    }
    
    // Modal functions
    function openFiltersModal() {
      const modal = document.getElementById('filters-modal');
      const button = document.getElementById('filters-button');
      modal.classList.add('modal-open');
      button.setAttribute('aria-expanded', 'true');
      
      // Focus first input for keyboard navigation
      setTimeout(() => {
        document.getElementById('filter-category').focus();
      }, 100);
    }
    
    function closeFiltersModal() {
      const modal = document.getElementById('filters-modal');
      const button = document.getElementById('filters-button');
      modal.classList.remove('modal-open');
      button.setAttribute('aria-expanded', 'false');
    }
    
    // Validate date range
    function validateDateRange() {
      const dateFrom = document.getElementById('filter-date-from').value;
      const dateTo = document.getElementById('filter-date-to').value;
      const fromError = document.getElementById('date-from-error');
      const toError = document.getElementById('date-to-error');
      
      let isValid = true;
      
      // Clear previous errors
      fromError.classList.add('hidden');
      toError.classList.add('hidden');
      fromError.textContent = '';
      toError.textContent = '';
      
      if (dateFrom && dateTo) {
        if (new Date(dateFrom) > new Date(dateTo)) {
          toError.textContent = 'To date must be after From date';
          toError.classList.remove('hidden');
          isValid = false;
        }
      }
      
      return isValid;
    }
    
    // Apply filters
    function applyFilters() {
      if (!validateDateRange()) {
        return;
      }
      
      // Update state from form
      FilterState.category = document.getElementById('filter-category').value;
      FilterState.author = document.getElementById('filter-author').value.trim();
      FilterState.department = document.getElementById('filter-department').value;
      FilterState.date_from = document.getElementById('filter-date-from').value;
      FilterState.date_to = document.getElementById('filter-date-to').value;
      FilterState.confidentiality = document.getElementById('filter-confidentiality').value;
      FilterState.sort_by = document.getElementById('filter-sort-by').value;
      FilterState.sort_order = document.getElementById('filter-sort-order').value;
      
      closeFiltersModal();
      FilterState.updateFilterBadge();
      FilterState.renderFilterChips();
      FilterState.applyFilters();
    }
    
    // Reset filters to defaults
    function resetFilters() {
      document.getElementById('filter-category').value = '';
      document.getElementById('filter-author').value = '';
      document.getElementById('filter-department').value = '';
      document.getElementById('filter-date-from').value = '';
      document.getElementById('filter-date-to').value = '';
      document.getElementById('filter-confidentiality').value = '';
      document.getElementById('filter-sort-by').value = 'created_at';
      document.getElementById('filter-sort-order').value = 'desc';
      
      // Clear errors
      document.getElementById('date-from-error').classList.add('hidden');
      document.getElementById('date-to-error').classList.add('hidden');
    }
    
    // Clear all filters
    function clearAllFilters() {
      FilterState.clearAll();
      resetFilters();
      closeFiltersModal();
      FilterState.applyFilters();
    }
    
    // Remove filter chip
    function removeFilterChip(key) {
      FilterState.removeFilter(key);
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      // Only initialize filters if there are actual filter parameters in URL
      // Don't apply filters on initial load if coming from archive redirect
      const urlParams = new URLSearchParams(window.location.search);
      const hasFilterParams = urlParams.has('search') || urlParams.has('category') || 
                              urlParams.has('author') || urlParams.has('department') ||
                              urlParams.has('date_from') || urlParams.has('date_to') ||
                              urlParams.has('confidentiality') || urlParams.has('sort_by');
      
      // Only init filters if there are actual filter params (not just archived redirect params)
      if (hasFilterParams) {
        FilterState.init();
      } else {
        // Just populate form fields without applying filters
        FilterState.populateForm();
        FilterState.updateFilterBadge();
        FilterState.renderFilterChips();
      }
      
      // Check if we just archived a document and highlight it if found
      const archivedDocId = urlParams.get('archived');
      if (archivedDocId) {
        // Wait a bit for the page to fully load, then check if document appears
        setTimeout(() => {
          const tableRows = document.querySelectorAll('tbody tr');
          let documentFound = false;
          
          tableRows.forEach(row => {
            const docIdText = row.textContent;
            const rowDocId = row.getAttribute('data-document-id');
            
            // Check if the row contains the document ID or title
            if (docIdText.includes(archivedDocId) || rowDocId === archivedDocId) {
              documentFound = true;
              // Highlight the row
              row.classList.add('bg-green-50', 'border-l-4', 'border-l-green-500');
              // Scroll to the row
              row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
          });
        }, 1000);
      }
      
      // Debounced search input
      const searchInput = document.getElementById('search-input');
      if (searchInput) {
        searchInput.addEventListener('input', debounceSearch);
        searchInput.addEventListener('keydown', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            FilterState.search = this.value.trim();
            FilterState.applyFilters();
          }
        });
      }
      
      // Date validation on change
      document.getElementById('filter-date-from')?.addEventListener('change', validateDateRange);
      document.getElementById('filter-date-to')?.addEventListener('change', validateDateRange);
      
      // Keyboard shortcuts
      document.addEventListener('keydown', function(e) {
        // Close modal on Escape
        if (e.key === 'Escape') {
          const modal = document.getElementById('filters-modal');
          if (modal && modal.classList.contains('modal-open')) {
            closeFiltersModal();
          }
        }
      });
    });
    
    // Modal state variables
    let documentToArchive = null;
    let documentToUnarchive = null;
    let documentToDelete = null;
    
    // Predefined password for unarchiving documents
    const UNARCHIVE_PASSWORD = 'admin123'; // You can change this to your desired password
    
    // User role for conditional access control
    const userRole = '{{ auth()->user()->role }}';
    const isAdministrator = userRole === 'Administrator';

    // Archive functions
    function archiveDocument(documentId) {
      documentToArchive = documentId;
      document.getElementById('archiveModal').classList.add('modal-open');
    }

    function closeArchiveModal() {
      document.getElementById('archiveModal').classList.remove('modal-open');
      documentToArchive = null;
    }

    function confirmArchive() {
      if (!documentToArchive) return;
      
      fetch(`/document/${documentToArchive}/archive`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message and reload page
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while archiving the document');
      })
      .finally(() => {
        closeArchiveModal();
      });
    }

    // Unarchive functions
    function unarchiveDocument(documentId) {
      documentToUnarchive = documentId;
      document.getElementById('unarchiveModal').classList.add('modal-open');
    }

    function closeUnarchiveModal() {
      document.getElementById('unarchiveModal').classList.remove('modal-open');
      documentToUnarchive = null;
      
      // Clear password field and hide error message (only for administrators)
      if (isAdministrator) {
        const passwordField = document.getElementById('unarchivePassword');
        const errorElement = document.getElementById('passwordError');
        
        if (passwordField) passwordField.value = '';
        if (errorElement) errorElement.classList.add('hidden');
      }
    }

    function confirmUnarchive() {
      if (!documentToUnarchive) return;
      
      // Check if user is administrator
      if (!isAdministrator) {
        showPasswordError('Access denied. Only administrators can restore archived documents.');
        return;
      }
      
      // Get password from input field
      const password = document.getElementById('unarchivePassword').value.trim();
      
      // Validate password
      if (!password) {
        showPasswordError('Please enter the administrator password');
        return;
      }
      
      if (password !== UNARCHIVE_PASSWORD) {
        showPasswordError('Incorrect administrator password. Please try again.');
        return;
      }
      
      // Hide any previous error messages
      document.getElementById('passwordError').classList.add('hidden');
      
      // Proceed with unarchive request
      fetch(`/document/${documentToUnarchive}/unarchive`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message and reload page
          location.reload();
        } else {
          showPasswordError('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showPasswordError('An error occurred while unarchiving the document');
      });
    }
    
    // Function to show password error message
    function showPasswordError(message) {
      const errorElement = document.getElementById('passwordError');
      const errorMessageElement = document.getElementById('errorMessage');
      
      if (errorElement && errorMessageElement) {
        errorMessageElement.textContent = message;
        errorElement.classList.remove('hidden');
        
        // Focus on password field for better UX (only for administrators)
        if (isAdministrator) {
          const passwordField = document.getElementById('unarchivePassword');
          if (passwordField) passwordField.focus();
        }
      }
    }

    // Permanent delete functions
    function permanentlyDeleteDocument(documentId) {
      documentToDelete = documentId;
      document.getElementById('permanentDeleteModal').classList.add('modal-open');
    }

    function closePermanentDeleteModal() {
      document.getElementById('permanentDeleteModal').classList.remove('modal-open');
      documentToDelete = null;
    }

    function confirmPermanentDelete() {
      if (!documentToDelete) return;
      
      fetch(`/document/${documentToDelete}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message and reload page
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the document');
      })
      .finally(() => {
        closePermanentDeleteModal();
      });
    }

    // Document actions
    function viewDocument(documentId) {
      console.log('viewDocument called with ID:', documentId);
      
      // Log access
      fetch(`/legal/documents/${documentId}/view`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        }
      }).catch(error => console.log('Access logging failed:', error));
      
      viewDocumentDetails(documentId);
    }

    // View document details in sidebar panel (Google Drive style)
    function viewDocumentDetails(documentId) {
      // Open the sidebar panel
      openDocumentDetailsPanel();
      
      // Show loading state
      document.getElementById('details-tab-content').innerHTML = `
        <div class="text-center py-12">
          <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-gray-400"></i>
          </div>
          <p class="text-sm text-gray-500">Loading document details...</p>
        </div>
      `;
      lucide.createIcons();

      // Fetch document details
      fetch(`/document/${documentId}`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: Failed to fetch document details`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success && data.document) {
          const doc = data.document;
          populateDocumentDetails(doc);
          loadDocumentActivity(documentId);
        } else {
          throw new Error('Invalid response format');
        }
      })
      .catch(error => {
        console.error('Error fetching document details:', error);
        document.getElementById('details-tab-content').innerHTML = `
          <div class="text-center py-12">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <i data-lucide="alert-circle" class="w-8 h-8 text-red-500"></i>
            </div>
            <p class="text-sm text-red-600">Failed to load document details</p>
            <p class="text-xs text-gray-500 mt-2">${error.message}</p>
          </div>
        `;
        lucide.createIcons();
      });
    }

    // Open document details panel
    function openDocumentDetailsPanel() {
      const panel = document.getElementById('document-details-panel');
      const overlay = document.getElementById('details-panel-overlay');
      if (panel) {
        panel.classList.remove('translate-x-full');
        panel.classList.add('translate-x-0');
      }
      if (overlay) {
        overlay.classList.remove('hidden');
      }
      // Switch to Details tab by default
      switchDetailsTab('details');
    }

    // Close document details panel
    function closeDocumentDetailsPanel() {
      const panel = document.getElementById('document-details-panel');
      const overlay = document.getElementById('details-panel-overlay');
      if (panel) {
        panel.classList.remove('translate-x-0');
        panel.classList.add('translate-x-full');
      }
      if (overlay) {
        overlay.classList.add('hidden');
      }
    }

    // Switch between Details and Activity tabs
    function switchDetailsTab(tab) {
      const detailsBtn = document.getElementById('details-tab-btn');
      const activityBtn = document.getElementById('activity-tab-btn');
      const detailsContent = document.getElementById('details-tab-content');
      const activityContent = document.getElementById('activity-tab-content');

      if (tab === 'details') {
        detailsBtn.classList.add('text-blue-600', 'border-blue-600', 'bg-blue-50');
        detailsBtn.classList.remove('text-gray-600', 'border-transparent');
        activityBtn.classList.remove('text-blue-600', 'border-blue-600', 'bg-blue-50');
        activityBtn.classList.add('text-gray-600', 'border-transparent');
        detailsContent.classList.remove('hidden');
        activityContent.classList.add('hidden');
      } else {
        activityBtn.classList.add('text-blue-600', 'border-blue-600', 'bg-blue-50');
        activityBtn.classList.remove('text-gray-600', 'border-transparent');
        detailsBtn.classList.remove('text-blue-600', 'border-blue-600', 'bg-blue-50');
        detailsBtn.classList.add('text-gray-600', 'border-transparent');
        activityContent.classList.remove('hidden');
        detailsContent.classList.add('hidden');
      }
    }

    // Populate document details in the panel
    function populateDocumentDetails(doc) {
      // Format dates
      const formatDate = (dateString) => {
        if (!dateString) return 'Not Set';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });
      };

      const formatDateShort = (dateString) => {
        if (!dateString) return 'Not Set';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
      };

      // Get document initials
      const title = doc.title || 'Untitled Document';
      const initials = title.substring(0, 2).toUpperCase();

      // Populate header
      document.getElementById('details-doc-initials').textContent = initials;
      document.getElementById('details-doc-title').textContent = title;
      document.getElementById('details-doc-id').textContent = `#${doc.id}`;

      // Populate Details tab
      document.getElementById('details-title').textContent = title;
      document.getElementById('details-description').textContent = doc.description || 'No description';
      document.getElementById('details-category').textContent = doc.category ? doc.category.replace('_', ' ').charAt(0).toUpperCase() + doc.category.replace('_', ' ').slice(1) : 'Unknown';
      document.getElementById('details-department').textContent = doc.department || 'Unassigned';
      document.getElementById('details-status').textContent = doc.status ? doc.status.replace('_', ' ').charAt(0).toUpperCase() + doc.status.replace('_', ' ').slice(1) : 'Active';
      
      document.getElementById('details-created').textContent = formatDateShort(doc.created_at);
      document.getElementById('details-modified').textContent = formatDateShort(doc.updated_at || doc.created_at);
      document.getElementById('details-archived').textContent = doc.archived_at ? formatDateShort(doc.archived_at) : 'Not archived';
      document.getElementById('details-disposal').textContent = doc.disposal_date ? formatDateShort(doc.disposal_date) : 'Not set';

      // Retention period
      const retentionPeriod = doc.retention_period || (doc.retention_years ? `${doc.retention_years} Years` : 'Not set');
      document.getElementById('details-retention').textContent = retentionPeriod;

      // Confidentiality
      const confidentiality = doc.confidentiality_level || 'internal';
      document.getElementById('details-confidentiality').textContent = confidentiality.charAt(0).toUpperCase() + confidentiality.slice(1);

      // Who has access
      const accessList = document.getElementById('details-access-list');
      accessList.innerHTML = `
        <div class="flex items-center gap-2 mb-2">
          <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-semibold">
            ${(doc.uploader_name || 'U').substring(0, 1).toUpperCase()}
          </div>
          <div class="flex-1">
            <p class="text-sm font-medium text-gray-900">${doc.uploader_name || 'You'}</p>
            <p class="text-xs text-gray-500">Owner</p>
          </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">Owned by ${doc.uploader_name || 'you'}. ${doc.collaborators && doc.collaborators.length > 0 ? `Shared with ${doc.collaborators.length} other${doc.collaborators.length > 1 ? 's' : ''}.` : 'Not shared.'}</p>
      `;

      // Re-initialize icons
      lucide.createIcons();
    }

    // Load document activity
    function loadDocumentActivity(documentId) {
      const activityList = document.getElementById('activity-list');
      activityList.innerHTML = `
        <div class="text-center py-8">
          <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-gray-400"></i>
          </div>
          <p class="text-sm text-gray-500">Loading activity...</p>
        </div>
      `;
      lucide.createIcons();

      // Fetch activity logs
      fetch(`/legal/documents/${documentId}/activity-tracking`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success && data.activity_log) {
          const activities = data.activity_log;
          if (activities.length === 0) {
            activityList.innerHTML = `
              <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <i data-lucide="activity" class="w-8 h-8 text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-500">No activity recorded</p>
              </div>
            `;
          } else {
            activityList.innerHTML = activities.map(activity => `
              <div class="flex items-start gap-3 pb-4 border-b border-gray-200 last:border-0">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                  <i data-lucide="${getActivityIcon(activity.action)}" class="w-4 h-4 text-blue-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-gray-900">${activity.description || activity.action}</p>
                  <p class="text-xs text-gray-500 mt-1">${activity.user_name || 'System'} • ${activity.formatted_date || 'Unknown date'}</p>
                </div>
              </div>
            `).join('');
          }
        } else {
          activityList.innerHTML = `
            <div class="text-center py-8">
              <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="activity" class="w-8 h-8 text-gray-400"></i>
              </div>
              <p class="text-sm text-gray-500">No activity recorded</p>
            </div>
          `;
        }
        lucide.createIcons();
      })
      .catch(error => {
        console.error('Error loading activity:', error);
        activityList.innerHTML = `
          <div class="text-center py-8">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <i data-lucide="activity" class="w-8 h-8 text-gray-400"></i>
            </div>
            <p class="text-sm text-gray-500">No activity recorded</p>
          </div>
        `;
        lucide.createIcons();
      });
    }

    // Get activity icon based on action
    function getActivityIcon(action) {
      const iconMap = {
        'created': 'file-plus',
        'updated': 'edit',
        'archived': 'archive',
        'viewed': 'eye',
        'downloaded': 'download',
        'shared': 'share-2',
        'deleted': 'trash-2'
      };
      return iconMap[action?.toLowerCase()] || 'circle';
    }

    function downloadDocument(documentId) {
      // Log download access
      fetch(`/legal/documents/${documentId}/download`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        }
      }).catch(error => console.log('Download logging failed:', error));
      
      window.location.href = `/document/${documentId}/download`;
    }

    function disposeDocument(documentId) {
      if (confirm('Are you sure you want to permanently dispose of this document? This action cannot be undone.')) {
        fetch(`/document/${documentId}/dispose`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Document disposed successfully');
            window.location.reload();
          } else {
            alert('Error disposing document: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error disposing document');
        });
      }
    }

    function refreshTable() {
      location.reload();
    }

    function refreshDocuments() {
      location.reload();
    }

    function showVersionHistory(documentId) {
      // Fetch document history/versions
      fetch(`/legal/documents/${documentId}/history`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success && data.editing_history && data.editing_history.length > 0) {
          const versions = data.editing_history;
          Swal.fire({
            title: 'Version History',
            html: `
              <div class="text-left">
                <p class="text-gray-600 mb-4">View all versions of this document.</p>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                  ${versions.map((version, index) => `
                    <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                      <div class="flex items-start justify-between">
                        <div class="flex-1">
                          <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-semibold text-gray-900">Version ${versions.length - index}</span>
                            <span class="text-xs text-gray-500">${version.action || 'Updated'}</span>
                          </div>
                          <p class="text-sm text-gray-600 mb-1">${version.description || 'No description'}</p>
                          <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span>${version.user_name || 'System'}</span>
                            <span>•</span>
                            <span>${new Date(version.timestamp).toLocaleString()}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  `).join('')}
                </div>
              </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Close',
            width: '600px'
          });
        } else {
          Swal.fire({
            title: 'Version History',
            html: `
              <div class="text-left">
                <p class="text-gray-600 mb-4">View all versions of archived documents.</p>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                  <div class="text-gray-500 text-sm text-center py-4">No version history available for this document.</div>
                </div>
              </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Close',
            width: '600px'
          });
        }
      })
      .catch(error => {
        console.error('Error loading version history:', error);
        Swal.fire({
          title: 'Version History',
          html: `
            <div class="text-left">
              <p class="text-gray-600 mb-4">View all versions of archived documents.</p>
              <div class="space-y-2 max-h-64 overflow-y-auto">
                <div class="text-gray-500 text-sm text-center py-4">Version history feature coming soon...</div>
              </div>
            </div>
          `,
          showConfirmButton: true,
          confirmButtonText: 'Close',
          width: '600px'
        });
      });
    }

    function showCollaborators() {
      Swal.fire({
        title: 'Collaborators',
        html: `
          <div class="text-left">
            <p class="text-gray-600 mb-4">Manage collaborators for archived documents.</p>
            <div class="space-y-2 max-h-64 overflow-y-auto">
              <div class="text-gray-500 text-sm text-center py-4">Collaborator management for archived documents...</div>
            </div>
          </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'Close',
        width: '600px'
      });
    }

    function showManageAccess() {
      Swal.fire({
        title: 'Manage Access',
        html: `
          <div class="text-left">
            <p class="text-gray-600 mb-4">Manage who has access to this document.</p>
            <div class="space-y-2 max-h-64 overflow-y-auto">
              <div class="text-gray-500 text-sm text-center py-4">Access management feature coming soon...</div>
            </div>
          </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'Close',
        width: '600px'
      });
    }

    function showShareDialog(documentId) {
      // Fetch document details and collaborators
      Promise.all([
        fetch(`/document/${documentId}`, {
          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).then(r => r.json()),
        fetch(`/legal/documents/${documentId}/collaborators`, {
          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).then(r => r.json())
      ])
      .then(([docData, collabData]) => {
        const doc = docData.success ? docData.document : null;
        const collaborators = collabData.success ? collabData.collaborators : [];
        const currentUser = { name: '{{ auth()->user()->name }}', email: '{{ auth()->user()->email }}' };
        const shareUrl = `${window.location.origin}/document/${documentId}`;
        
        // Get document title
        const docTitle = doc ? doc.title : 'Document';
        
        Swal.fire({
          title: '',
          html: `
            <div class="text-left" style="max-width: 520px; padding: 24px;">
              <!-- Header with title and icons -->
              <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                <h3 class="text-lg font-normal text-gray-900">Share '${docTitle}'</h3>
                <div class="flex items-center gap-2">
                  <button class="p-1.5 hover:bg-gray-100 rounded-full transition-colors" title="Help">
                    <i data-lucide="help-circle" class="w-5 h-5 text-gray-600"></i>
                  </button>
                  <button class="p-1.5 hover:bg-gray-100 rounded-full transition-colors" title="Settings">
                    <i data-lucide="settings" class="w-5 h-5 text-gray-600"></i>
                  </button>
                </div>
              </div>

              <!-- Add people input -->
              <div class="mb-6">
                <input type="text" 
                       id="share-add-people" 
                       placeholder="Add people, groups, spaces and calendar events"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
              </div>

              <!-- People with access -->
              <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">People with access</h4>
                <div id="share-people-list" class="space-y-1">
                  <!-- Owner -->
                  <div class="relative flex items-center justify-between py-2 px-2 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer group user-tooltip-trigger"
                       data-user-name="${currentUser.name || 'You'}"
                       data-user-email="${currentUser.email || ''}"
                       data-user-role="Owner"
                       data-user-initial="${currentUser.name ? currentUser.name.charAt(0).toUpperCase() : 'U'}"
                       data-avatar-color="#8B4513">
                    <div class="flex items-center gap-3 flex-1">
                      <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-semibold" style="background-color: #8B4513;">
                        ${currentUser.name ? currentUser.name.charAt(0).toUpperCase() : 'U'}
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900 truncate">${currentUser.name || 'You'} ${currentUser.name ? '(you)' : ''}</p>
                        <p class="text-xs text-gray-500 truncate">${currentUser.email || ''}</p>
                      </div>
                    </div>
                    <span class="text-sm text-gray-500">Owner</span>
                  </div>
                  
                  <!-- Collaborators -->
                  ${collaborators.map(collab => `
                    <div class="relative flex items-center justify-between py-2 px-2 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer group user-tooltip-trigger"
                         data-user-name="${collab.user_name || 'Unknown User'}"
                         data-user-email="${collab.email || ''}"
                         data-user-role="${collab.role || 'Collaborator'}"
                         data-user-initial="${collab.user_name ? collab.user_name.charAt(0).toUpperCase() : 'U'}"
                         data-avatar-color="#9CA3AF">
                      <div class="flex items-center gap-3 flex-1">
                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-white text-xs font-semibold">
                          ${collab.user_name ? collab.user_name.charAt(0).toUpperCase() : 'U'}
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="text-sm text-gray-900 truncate">${collab.user_name || 'Unknown User'}</p>
                          <p class="text-xs text-gray-500 truncate">${collab.email || ''}</p>
                        </div>
                      </div>
                      <div class="flex items-center gap-2">
                        <select class="text-sm border-0 bg-transparent text-gray-600 focus:outline-none focus:ring-0 cursor-pointer hover:bg-gray-50 rounded px-1 py-0.5" 
                                onchange="updateCollaboratorRole(${documentId}, ${collab.user_id}, this.value)">
                          <option value="viewer" ${collab.role === 'viewer' ? 'selected' : ''}>Viewer</option>
                          <option value="editor" ${collab.role === 'editor' ? 'selected' : ''}>Editor</option>
                          <option value="reviewer" ${collab.role === 'reviewer' ? 'selected' : ''}>Reviewer</option>
                        </select>
                        <button onclick="removeCollaboratorFromShare(${documentId}, ${collab.user_id})" 
                                class="p-1 hover:bg-gray-200 rounded-full transition-colors opacity-0 group-hover:opacity-100"
                                title="Remove">
                          <i data-lucide="x" class="w-4 h-4 text-gray-500"></i>
                        </button>
                      </div>
                    </div>
                  `).join('')}
                </div>
              </div>

              <!-- Action buttons at bottom -->
              <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                <button onclick="Swal.close()" 
                        class="px-6 py-2 text-sm font-medium text-white rounded-lg transition-colors" 
                        style="background-color: #4285f4;">
                  Done
                </button>
              </div>
            </div>
          `,
          showConfirmButton: false,
          showCancelButton: false,
          width: '520px',
          customClass: {
            popup: 'text-left p-0',
            htmlContainer: 'p-0'
          },
          didOpen: () => {
            lucide.createIcons();
            
            // Add event listener for adding people
            const addPeopleInput = document.getElementById('share-add-people');
            if (addPeopleInput) {
              addPeopleInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && this.value.trim()) {
                  addPersonToShare(documentId, this.value.trim());
                  this.value = '';
                }
              });
            }
            
            // Attach tooltip handlers after modal opens - Use event delegation
            setTimeout(() => {
              // Use event delegation on the share-people-list container
              const peopleList = document.getElementById('share-people-list');
              if (peopleList) {
                console.log('Attaching tooltip handlers via event delegation');
                
                // Remove old listeners by cloning
                const newPeopleList = peopleList.cloneNode(true);
                peopleList.parentNode.replaceChild(newPeopleList, peopleList);
                
                // Attach event delegation
                newPeopleList.addEventListener('mouseenter', function(e) {
                  const trigger = e.target.closest('.user-tooltip-trigger');
                  if (trigger) {
                    // Initialize mouse position
                    window.tooltipMouseX = e.clientX;
                    window.tooltipMouseY = e.clientY;
                    
                    const userName = trigger.getAttribute('data-user-name');
                    const userEmail = trigger.getAttribute('data-user-email');
                    const userRole = trigger.getAttribute('data-user-role');
                    const userInitial = trigger.getAttribute('data-user-initial');
                    const avatarColor = trigger.getAttribute('data-avatar-color');
                    
                    console.log('Mouse entered tooltip trigger');
                    console.log('Tooltip data:', { userName, userEmail, userRole, userInitial, avatarColor });
                    
                    if (window.showUserTooltip) {
                      window.showUserTooltip(e, userName, userEmail, userRole, userInitial, avatarColor);
                    } else {
                      console.error('showUserTooltip function not found');
                    }
                  }
                }, true);
                
                // Update mouse position while moving
                newPeopleList.addEventListener('mousemove', function(e) {
                  const trigger = e.target.closest('.user-tooltip-trigger');
                  if (trigger) {
                    // Update mouse position while moving
                    window.tooltipMouseX = e.clientX;
                    window.tooltipMouseY = e.clientY;
                  }
                }, true);
                
                newPeopleList.addEventListener('mouseleave', function(e) {
                  const trigger = e.target.closest('.user-tooltip-trigger');
                  if (trigger) {
                    console.log('Mouse left tooltip trigger');
                    
                    // Only hide if tooltip is already shown
                    const tooltip = document.getElementById('user-tooltip');
                    if (tooltip) {
                      // Tooltip is already visible, delay hiding
                      if (window.hideTooltipTimeout) {
                        clearTimeout(window.hideTooltipTimeout);
                      }
                      window.hideTooltipTimeout = setTimeout(() => {
                        // Check if mouse moved to tooltip
                        const stillExists = document.getElementById('user-tooltip');
                        if (!stillExists || !stillExists.matches(':hover')) {
                          if (window.hideUserTooltip) {
                            window.hideUserTooltip();
                          }
                          // Clear everything after hiding
                          window.tooltipTarget = null;
                          window.tooltipTargetId = null;
                          window.tooltipData = null;
                          window.tooltipMouseX = null;
                          window.tooltipMouseY = null;
                        }
                      }, 150);
                    } else {
                      // Tooltip not shown yet, cancel the show timeout
                      if (window.tooltipTimeout) {
                        clearTimeout(window.tooltipTimeout);
                        window.tooltipTimeout = null;
                      }
                      // Clear everything after a short delay
                      setTimeout(() => {
                        window.tooltipTarget = null;
                        window.tooltipTargetId = null;
                        window.tooltipData = null;
                        window.tooltipMouseX = null;
                        window.tooltipMouseY = null;
                      }, 100);
                    }
                  }
                }, true);
              }
            }, 300);
          }
        });
      })
      .catch(error => {
        console.error('Error loading share dialog:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to load share settings',
          icon: 'error'
        });
      });
    }

    function copyShareLinkFromDialog(shareUrl) {
      navigator.clipboard.writeText(shareUrl).then(() => {
        Swal.fire({
          title: 'Copied!',
          text: 'Link copied to clipboard',
          icon: 'success',
          timer: 1500,
          showConfirmButton: false
        });
      }).catch(() => {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = shareUrl;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        Swal.fire({
          title: 'Copied!',
          text: 'Link copied to clipboard',
          icon: 'success',
          timer: 1500,
          showConfirmButton: false
        });
      });
    }

    function shareViaEmailFromDialog(docTitle, shareUrl) {
      const subject = encodeURIComponent(`Shared: ${docTitle}`);
      const body = encodeURIComponent(`I'm sharing this document with you:\n\n${shareUrl}`);
      window.location.href = `mailto:?subject=${subject}&body=${body}`;
    }

    function updateLinkAccess(shareUrl) {
      const access = document.getElementById('share-link-access').value;
      console.log('Link access updated to:', access);
      // You can implement actual API call here to update link access settings
    }

    function updateLinkRole() {
      const role = document.getElementById('share-link-role').value;
      console.log('Link role updated to:', role);
      // You can implement actual API call here to update link role settings
    }

    function updateCollaboratorRole(documentId, userId, newRole) {
      // You can implement actual API call here to update collaborator role
      console.log('Updating collaborator role:', { documentId, userId, newRole });
      // For now, just show a success message
      Swal.fire({
        title: 'Updated',
        text: 'Collaborator role updated',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
      });
    }

    function removeCollaboratorFromShare(documentId, userId) {
      Swal.fire({
        title: 'Remove access?',
        text: 'This person will lose access to this document.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Remove',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/legal/documents/${documentId}/collaborators/${userId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                title: 'Removed',
                text: 'Access removed successfully',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
              }).then(() => {
                // Reload the share dialog
                showShareDialog(documentId);
              });
            } else {
              Swal.fire({
                title: 'Error',
                text: data.message || 'Failed to remove access',
                icon: 'error'
              });
            }
          })
          .catch(error => {
            console.error('Error removing collaborator:', error);
            Swal.fire({
              title: 'Error',
              text: 'Failed to remove access',
              icon: 'error'
            });
          });
        }
      });
    }

    function addPersonToShare(documentId, emailOrName) {
      // Close current share dialog and show add collaborator modal
      Swal.close();
      showAddCollaboratorModal(documentId);
    }

    // User tooltip with 2-second delay - Make functions global
    window.tooltipTimeout = null;
    window.hideTooltipTimeout = null;
    window.currentTooltip = null;
    window.tooltipTarget = null;
    window.tooltipData = null;
    window.tooltipMouseX = null;
    window.tooltipMouseY = null;

    window.showUserTooltip = function(event, userName, userEmail, userRole, userInitial, avatarColor) {
      console.log('showUserTooltip called', { userName, userEmail, userRole, userInitial, avatarColor });
      
      // Find the trigger element from the event
      const trigger = event.target.closest('.user-tooltip-trigger');
      if (!trigger) {
        console.log('No trigger element found');
        return;
      }
      
      // Clear any hide timeout
      if (window.hideTooltipTimeout) {
        clearTimeout(window.hideTooltipTimeout);
        window.hideTooltipTimeout = null;
      }
      
      // Store the target element and data with a unique identifier
      const triggerId = trigger.getAttribute('data-user-name') + '_' + Date.now();
      window.tooltipTarget = trigger;
      window.tooltipTargetId = triggerId;
      window.tooltipData = { userName, userEmail, userRole, userInitial, avatarColor };
      
      // Store mouse position from event if available
      if (event.clientX && event.clientY) {
        window.tooltipMouseX = event.clientX;
        window.tooltipMouseY = event.clientY;
      }
      
      // Clear any existing show timeout
      if (window.tooltipTimeout) {
        clearTimeout(window.tooltipTimeout);
        window.tooltipTimeout = null;
      }
      
      // If tooltip already exists and is for the same element, don't recreate
      if (window.currentTooltip && window.currentTooltip.dataset.targetId === userName) {
        console.log('Tooltip already exists for this element');
        return;
      }
      
      // Hide any existing tooltip for different element (but don't clear target/data)
      if (window.currentTooltip) {
        const existingTooltip = document.getElementById('user-tooltip');
        if (existingTooltip) {
          existingTooltip.remove();
        }
        window.currentTooltip = null;
      }
      
      // Set timeout for 2 seconds
      window.tooltipTimeout = setTimeout(() => {
        // Check if still hovering over the same element (compare by triggerId)
        if (!window.tooltipTarget || !window.tooltipData || window.tooltipTargetId !== triggerId) {
          console.log('Tooltip target or data lost or changed');
          return;
        }
        
        // Double check the element is still in the DOM
        if (!document.body.contains(window.tooltipTarget)) {
          console.log('Tooltip target no longer in DOM');
          return;
        }
        
        console.log('Creating tooltip after 2 seconds');
        
        const tooltip = document.createElement('div');
        tooltip.id = 'user-tooltip';
        tooltip.className = 'bg-white rounded-lg shadow-2xl border border-gray-200 p-4';
        tooltip.style.cssText = `
          position: fixed;
          z-index: 999999;
          display: block;
          pointer-events: auto;
          min-width: 280px;
          max-width: 320px;
        `;
        
        // Position tooltip near the user entry
        const rect = window.tooltipTarget.getBoundingClientRect();
        const scrollX = window.pageXOffset || document.documentElement.scrollLeft;
        const scrollY = window.pageYOffset || document.documentElement.scrollTop;
        
        // Position tooltip near the cursor position
        // Use stored mouse position if available, otherwise use element position
        let tooltipX, tooltipY;
        
        if (window.tooltipMouseX && window.tooltipMouseY) {
          // Position relative to cursor (offset to the right and slightly down)
          tooltipX = window.tooltipMouseX + 15;
          tooltipY = window.tooltipMouseY + 15;
        } else {
          // Fallback to element position
          tooltipX = rect.right + 12;
          tooltipY = rect.top;
        }
        
        tooltip.style.left = `${tooltipX}px`;
        tooltip.style.top = `${tooltipY}px`;
        
        tooltip.innerHTML = `
          <div class="flex items-start gap-3">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-base font-semibold flex-shrink-0" style="background-color: ${window.tooltipData.avatarColor};">
              ${window.tooltipData.userInitial}
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-semibold text-gray-900 mb-1">${window.tooltipData.userName}</h4>
              <p class="text-xs text-gray-500 mb-2">${window.tooltipData.userEmail || 'No email'}</p>
              <div class="flex items-center gap-2">
                <span class="text-xs px-2 py-1 rounded-full ${window.tooltipData.userRole === 'Owner' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'}">
                  ${window.tooltipData.userRole}
                </span>
              </div>
            </div>
          </div>
        `;
        
        tooltip.dataset.targetId = window.tooltipData.userName;
        
        // Keep tooltip visible when hovering over it
        tooltip.addEventListener('mouseenter', () => {
          // Cancel any hide timeout
          if (window.hideTooltipTimeout) {
            clearTimeout(window.hideTooltipTimeout);
            window.hideTooltipTimeout = null;
          }
        });
        
        tooltip.addEventListener('mouseleave', () => {
          // Delay hiding to allow moving from trigger to tooltip
          window.hideTooltipTimeout = setTimeout(() => {
            window.hideUserTooltip();
          }, 100);
        });
        
        document.body.appendChild(tooltip);
        window.currentTooltip = tooltip;
        
        // Adjust position if tooltip goes off screen
        setTimeout(() => {
          const tooltipRect = tooltip.getBoundingClientRect();
          const viewportWidth = window.innerWidth;
          const viewportHeight = window.innerHeight;
          
          // Adjust horizontal position
          if (tooltipRect.right > viewportWidth) {
            tooltip.style.left = `${rect.left - tooltipRect.width - 12}px`;
          }
          if (parseInt(tooltip.style.left) < 10) {
            tooltip.style.left = '10px';
          }
          
          // Adjust vertical position
          if (tooltipRect.bottom > viewportHeight) {
            tooltip.style.top = `${viewportHeight - tooltipRect.height - 10}px`;
          }
          if (parseInt(tooltip.style.top) < 10) {
            tooltip.style.top = '10px';
          }
        }, 10);
        
        // Re-initialize icons
        if (window.lucide && window.lucide.createIcons) {
          window.lucide.createIcons();
        }
      }, 2000); // 2 second delay
    };

    window.hideUserTooltip = function() {
      console.log('hideUserTooltip called');
      
      // Clear hide timeout
      if (window.hideTooltipTimeout) {
        clearTimeout(window.hideTooltipTimeout);
        window.hideTooltipTimeout = null;
      }
      
      // Remove tooltip if it exists (only if it's already shown)
      if (window.currentTooltip) {
        window.currentTooltip.remove();
        window.currentTooltip = null;
      }
      
      // Also remove by ID in case
      const existingTooltip = document.getElementById('user-tooltip');
      if (existingTooltip) {
        existingTooltip.remove();
      }
      
      // DON'T clear the show timeout or target/data here
      // Let the show timeout complete even if mouseleave fires
      // Only clear if tooltip is actually being hidden after it's shown
    };

    // Document History Functions
    function showDocumentHistory(documentId) {
      // Store the current document ID for use in the history modal
      window.currentDocumentId = documentId;
      
      document.getElementById('historyModal').classList.add('modal-open');
      document.getElementById('historyContent').innerHTML = '<div class="text-center py-4">Loading history...</div>';
      
      // Load document history via AJAX - try multiple endpoints
      const historyEndpoints = [
        `/legal/documents/${documentId}/history`,
        `/document/${documentId}/history`,
        `/legal/documents/${documentId}`
      ];
      
      let currentEndpointIndex = 0;
      
      function tryNextEndpoint() {
        if (currentEndpointIndex >= historyEndpoints.length) {
          // All endpoints failed, show basic history from access logs
          showBasicHistory(documentId);
          return;
        }
        
        const endpoint = historyEndpoints[currentEndpointIndex];
        currentEndpointIndex++;
        
        fetch(endpoint, {
          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          if (data.success) {
            displayHistoryData(data);
          } else {
            throw new Error(data.message || 'Invalid response');
          }
        })
        .catch(error => {
          console.log(`Endpoint ${endpoint} failed:`, error);
          tryNextEndpoint();
        });
      }
      
      function showBasicHistory(documentId) {
        // Get basic history from access logs
        fetch(`/legal/documents/${documentId}/collaborators`, {
          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          const collaborators = data.success ? data.collaborators : [];
          displayBasicHistory(collaborators);
        })
        .catch(error => {
          console.error('Error loading basic history:', error);
          displayBasicHistory([]);
        });
      }
      
      function displayHistoryData(data) {
        // Get the document ID from the current document being viewed
        const currentDocumentId = window.currentDocumentId || 'unknown';
        
            document.getElementById('historyContent').innerHTML = `
              <div class="space-y-6">
                <div>
              <h4 class="font-semibold text-gray-900 mb-3">Document Activity</h4>
                  <div class="space-y-2">
                    ${data.editing_history && data.editing_history.length > 0 ? data.editing_history.map(entry => `
                      <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="flex justify-between items-start">
                          <div>
                            <span class="text-sm font-medium text-gray-900">${entry.action}</span>
                            <p class="text-sm text-gray-600 mt-1">${entry.description}</p>
                            <p class="text-xs text-gray-500 mt-1">by ${entry.user_name}</p>
                          </div>
                          <span class="text-xs text-gray-500">${new Date(entry.timestamp).toLocaleString()}</span>
                        </div>
                      </div>
                `).join('') : '<p class="text-gray-500 text-center py-4">No activity history available</p>'}
                  </div>
                </div>
                
                <div>
                  <h4 class="font-semibold text-gray-900 mb-3">Access Log</h4>
                  <div class="space-y-2">
                    ${data.access_log && data.access_log.length > 0 ? data.access_log.slice(0, 10).map(entry => `
                      <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="flex justify-between items-start">
                          <div>
                            <span class="text-sm font-medium text-gray-900">${entry.action}</span>
                            <p class="text-xs text-gray-500 mt-1">by ${entry.user_name} from ${entry.ip_address}</p>
                          </div>
                          <span class="text-xs text-gray-500">${new Date(entry.timestamp).toLocaleString()}</span>
                        </div>
                      </div>
                    `).join('') : '<p class="text-gray-500 text-center py-4">No access log available</p>'}
                  </div>
                </div>
                
                <div>
                  <h4 class="font-semibold text-gray-900 mb-3">Collaborators</h4>
              <div id="collaborators-list-${currentDocumentId}" class="space-y-2">
                <div class="text-gray-500 text-sm">Loading collaborators...</div>
                          </div>
              <div class="mt-3 flex gap-2">
                <button onclick="showAddCollaboratorModal(${currentDocumentId})" class="btn btn-outline btn-sm">
                  <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                  Add Collaborator
                </button>
                  </div>
                </div>

                <div>
                  <h4 class="font-semibold text-gray-900 mb-3">Document Activity Tracking</h4>
                  <div class="mb-4">
                    <div class="flex flex-wrap gap-2 mb-3">
                      <select id="activity-user-filter" class="select select-bordered select-sm">
                        <option value="">All Users</option>
                      </select>
                      <select id="activity-action-filter" class="select select-bordered select-sm">
                        <option value="">All Actions</option>
                        <option value="uploaded">Uploaded</option>
                        <option value="edited">Edited</option>
                        <option value="viewed">Viewed</option>
                        <option value="downloaded">Downloaded</option>
                        <option value="archived">Archived</option>
                        <option value="collaborator">Collaborator</option>
                      </select>
                      <input type="date" id="activity-date-from" class="input input-bordered input-sm" placeholder="From Date">
                      <input type="date" id="activity-date-to" class="input input-bordered input-sm" placeholder="To Date">
                      <button onclick="loadActivityTracking(${currentDocumentId})" class="btn btn-primary btn-sm">
                        <i data-lucide="search" class="w-4 h-4 mr-1"></i>Filter
                      </button>
                    </div>
                  </div>
                  <div id="activity-log-container-${currentDocumentId}" class="space-y-2 max-h-64 overflow-y-auto">
                    <div class="text-center py-4">
                      <div class="loading loading-spinner loading-md"></div>
                      <p class="text-gray-500 mt-2">Loading activity log...</p>
                    </div>
                  </div>
                  <div id="activity-pagination-${currentDocumentId}" class="flex justify-center mt-4">
                    <!-- Pagination will be loaded here -->
                  </div>
                </div>

                <div>
                  <h4 class="font-semibold text-gray-900 mb-3">Document Statistics</h4>
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-3 rounded-lg text-center">
                      <div class="text-2xl font-bold text-blue-600">${data.stats?.view_count || 0}</div>
                      <div class="text-sm text-blue-600">Views</div>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg text-center">
                      <div class="text-2xl font-bold text-green-600">${data.stats?.download_count || 0}</div>
                      <div class="text-sm text-green-600">Downloads</div>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg text-center">
                      <div class="text-2xl font-bold text-purple-600">${data.stats?.version || 1}</div>
                      <div class="text-sm text-purple-600">Version</div>
                    </div>
                    <div class="bg-orange-50 p-3 rounded-lg text-center">
                      <div class="text-2xl font-bold text-orange-600">${data.stats?.collaborators_count || 0}</div>
                      <div class="text-sm text-orange-600">Collaborators</div>
                    </div>
                  </div>
                </div>
              </div>
            `;
      }
      
      function displayBasicHistory(collaborators) {
        document.getElementById('historyContent').innerHTML = `
          <div class="space-y-6">
            <div>
              <h4 class="font-semibold text-gray-900 mb-3">Document Information</h4>
              <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">This is an archived document. Limited history information is available.</p>
                <p class="text-xs text-gray-500 mt-2">Document was archived and moved to this section for long-term storage.</p>
              </div>
            </div>
            
            <div>
              <h4 class="font-semibold text-gray-900 mb-3">Collaborators</h4>
              <div id="collaborators-list-${window.currentDocumentId || 'unknown'}" class="space-y-2">
                <div class="text-gray-500 text-sm">Loading collaborators...</div>
              </div>
              <div class="mt-3 flex gap-2">
                <button onclick="showAddCollaboratorModal(${window.currentDocumentId || 'unknown'})" class="btn btn-outline btn-sm">
                  <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                  Add Collaborator
                </button>
              </div>
            </div>
            
            <div>
              <h4 class="font-semibold text-gray-900 mb-3">Document Activity Tracking</h4>
              <div class="mb-4">
                <div class="flex flex-wrap gap-2 mb-3">
                  <select id="activity-user-filter-basic" class="select select-bordered select-sm">
                    <option value="">All Users</option>
                  </select>
                  <select id="activity-action-filter-basic" class="select select-bordered select-sm">
                    <option value="">All Actions</option>
                    <option value="uploaded">Uploaded</option>
                    <option value="edited">Edited</option>
                    <option value="viewed">Viewed</option>
                    <option value="downloaded">Downloaded</option>
                    <option value="archived">Archived</option>
                    <option value="collaborator">Collaborator</option>
                  </select>
                  <input type="date" id="activity-date-from-basic" class="input input-bordered input-sm" placeholder="From Date">
                  <input type="date" id="activity-date-to-basic" class="input input-bordered input-sm" placeholder="To Date">
                  <button onclick="loadActivityTracking(${window.currentDocumentId || 'unknown'})" class="btn btn-primary btn-sm">
                    <i data-lucide="search" class="w-4 h-4 mr-1"></i>Filter
                  </button>
                </div>
              </div>
              <div id="activity-log-container-${window.currentDocumentId || 'unknown'}" class="space-y-2 max-h-64 overflow-y-auto">
                <div class="text-center py-4">
                  <div class="loading loading-spinner loading-md"></div>
                  <p class="text-gray-500 mt-2">Loading activity log...</p>
                </div>
              </div>
              <div id="activity-pagination-${window.currentDocumentId || 'unknown'}" class="flex justify-center mt-4">
                <!-- Pagination will be loaded here -->
              </div>
            </div>
            
            <div>
              <h4 class="font-semibold text-gray-900 mb-3">Document Status</h4>
              <div class="bg-blue-50 p-4 rounded-lg">
                <div class="flex items-center">
                  <i data-lucide="archive" class="w-5 h-5 text-blue-600 mr-2"></i>
                  <span class="text-sm font-medium text-blue-800">Archived Document</span>
                </div>
                <p class="text-xs text-blue-600 mt-1">This document is stored in the archive for reference purposes.</p>
              </div>
            </div>
          </div>
        `;
        
        // Re-initialize Lucide icons
        if (window.lucide && window.lucide.createIcons) {
          window.lucide.createIcons();
        }
        
        // Load collaborators for the current document
        if (currentDocumentId && currentDocumentId !== 'unknown') {
          loadCollaborators(currentDocumentId);
          loadActivityTracking(currentDocumentId);
        }
      }
      
      // Start trying endpoints
      tryNextEndpoint();
    }

    function closeHistoryModal() {
      document.getElementById('historyModal').classList.remove('modal-open');
    }


    // Collaborator Management Functions
    function loadCollaborators(documentId) {
      console.log('Loading collaborators for document:', documentId);
      
      fetch(`/legal/documents/${documentId}/collaborators`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        console.log('Collaborators API response:', response);
        return response.json();
      })
      .then(data => {
        console.log('=== COLLABORATORS DEBUG ===');
        console.log('Full response:', data);
        console.log('Success:', data.success);
        console.log('Collaborators array:', data.collaborators);
        console.log('Collaborators count:', data.collaborators ? data.collaborators.length : 'undefined');
        console.log('========================');
        
        const collaboratorsList = document.getElementById(`collaborators-list-${documentId}`);
        if (collaboratorsList) {
          if (data.success && data.collaborators && data.collaborators.length > 0) {
            console.log('Found collaborators:', data.collaborators);
            collaboratorsList.innerHTML = data.collaborators.map(collaborator => `
              <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                    ${collaborator.user_name ? collaborator.user_name.charAt(0).toUpperCase() : 'U'}
                  </div>
                  <div>
                    <div class="font-medium text-sm">${collaborator.user_name || 'Unknown User'}</div>
                    <div class="text-xs text-gray-500">${collaborator.role || 'Collaborator'}</div>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="text-xs text-gray-500">Added ${new Date(collaborator.added_at).toLocaleDateString()}</span>
                  <button onclick="removeCollaborator(${documentId}, ${collaborator.user_id})" 
                          class="p-1 text-red-500 hover:bg-red-50 rounded" 
                          title="Remove Collaborator">
                    <i data-lucide="x" class="w-4 h-4"></i>
                  </button>
                </div>
              </div>
            `).join('');
          } else {
            console.log('No collaborators found for this document');
            collaboratorsList.innerHTML = '<div class="text-gray-500 text-sm">No collaborators added yet.</div>';
          }
        }
        // Re-initialize Lucide icons
        if (window.lucide && window.lucide.createIcons) {
          window.lucide.createIcons();
        }
        
        // Load collaborators and activity tracking for the current document
        const currentDocumentId = window.currentDocumentId;
        if (currentDocumentId && currentDocumentId !== 'unknown') {
          loadCollaborators(currentDocumentId);
          loadActivityTracking(currentDocumentId);
        }
      })
      .catch(error => {
        console.error('Error loading collaborators:', error);
        const collaboratorsList = document.getElementById(`collaborators-list-${documentId}`);
        if (collaboratorsList) {
          collaboratorsList.innerHTML = '<div class="text-red-500 text-sm">Error loading collaborators.</div>';
        }
      });
    }

    function showAddCollaboratorModal(documentId) {
      // Get list of users for dropdown
      fetch('/users/list', {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        console.log('Users API response:', response);
        return response.json();
      })
      .then(data => {
        console.log('Users data:', data);
        
        if (!data.success) {
          Swal.fire({
            title: 'Error',
            text: data.message || 'Failed to load users',
            icon: 'error'
          });
          return;
        }
        
        const users = data.users || [];
        
        if (users.length === 0) {
          Swal.fire({
            title: 'No Users Available',
            text: 'No other users found in the system. You cannot add yourself as a collaborator.',
            icon: 'info'
          });
          return;
        }
        
        const userOptions = users.map(user => 
          `<option value="${user.id}">${user.name} (${user.email})</option>`
        ).join('');

        Swal.fire({
          title: 'Add Collaborator',
          html: `
            <div class="text-left">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select User</label>
                <select id="collaborator-user" class="w-full p-2 border border-gray-300 rounded-md">
                  <option value="">Choose a user...</option>
                  ${userOptions}
                </select>
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <select id="collaborator-role" class="w-full p-2 border border-gray-300 rounded-md">
                  <option value="viewer">Viewer</option>
                  <option value="editor">Editor</option>
                  <option value="reviewer">Reviewer</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: 'Add Collaborator',
          cancelButtonText: 'Cancel',
          preConfirm: () => {
            const userId = document.getElementById('collaborator-user').value;
            const role = document.getElementById('collaborator-role').value;
            
            if (!userId) {
              Swal.showValidationMessage('Please select a user');
              return false;
            }
            
            return { userId, role };
          }
        }).then((result) => {
          if (result.isConfirmed) {
            addCollaborator(documentId, result.value.userId, result.value.role);
          }
        });
      })
      .catch(error => {
        console.error('Error loading users:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to load users. Please try again.',
          icon: 'error'
        });
      });
    }

    function addCollaborator(documentId, userId, role) {
      fetch(`/legal/documents/${documentId}/collaborators`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          user_id: userId,
          role: role
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            title: 'Success',
            text: 'Collaborator added successfully!',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
          }).then(() => {
            // Reopen share dialog to show updated collaborators
            showShareDialog(documentId);
          });
        } else {
          Swal.fire({
            title: 'Error',
            text: data.message || 'Failed to add collaborator',
            icon: 'error'
          });
        }
      })
      .catch(error => {
        console.error('Error adding collaborator:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to add collaborator. Please try again.',
          icon: 'error'
        });
      });
    }

    function removeCollaborator(documentId, userId) {
      Swal.fire({
        title: 'Remove Collaborator',
        text: 'Are you sure you want to remove this collaborator?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/legal/documents/${documentId}/collaborators/${userId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                title: 'Success',
                text: 'Collaborator removed successfully!',
                icon: 'success',
                timer: 2000
              });
              // Reload collaborators list
              loadCollaborators(documentId);
            } else {
              Swal.fire({
                title: 'Error',
                text: data.message || 'Failed to remove collaborator',
                icon: 'error'
              });
            }
          })
          .catch(error => {
            console.error('Error removing collaborator:', error);
            Swal.fire({
              title: 'Error',
              text: 'Failed to remove collaborator. Please try again.',
              icon: 'error'
            });
          });
        }
      });
    }


    // Document Activity Tracking Functions
    function loadActivityTracking(documentId, page = 1) {
      console.log('Loading activity tracking for document:', documentId);
      
      // Get filter values
      const userFilter = document.getElementById('activity-user-filter')?.value || 
                        document.getElementById('activity-user-filter-basic')?.value || '';
      const actionFilter = document.getElementById('activity-action-filter')?.value || 
                          document.getElementById('activity-action-filter-basic')?.value || '';
      const dateFrom = document.getElementById('activity-date-from')?.value || 
                      document.getElementById('activity-date-from-basic')?.value || '';
      const dateTo = document.getElementById('activity-date-to')?.value || 
                    document.getElementById('activity-date-to-basic')?.value || '';
      
      // Build query parameters
      const params = new URLSearchParams({
        page: page,
        per_page: 10,
        user: userFilter,
        action: actionFilter,
        date_from: dateFrom,
        date_to: dateTo
      });
      
      fetch(`/legal/documents/${documentId}/activity-tracking?${params}`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        console.log('Activity tracking API response:', response);
        return response.json();
      })
      .then(data => {
        console.log('Activity tracking data:', data);
        
        const container = document.getElementById(`activity-log-container-${documentId}`);
        const paginationContainer = document.getElementById(`activity-pagination-${documentId}`);
        
        if (container) {
          if (data.success && data.activity_log && data.activity_log.length > 0) {
            console.log('Found activity log entries:', data.activity_log);
            container.innerHTML = data.activity_log.map(entry => `
              <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                    ${entry.user_name ? entry.user_name.charAt(0).toUpperCase() : 'U'}
                  </div>
                  <div>
                    <div class="font-medium text-sm">${entry.user_name || 'Unknown User'}</div>
                    <div class="text-xs text-gray-500">${entry.action || 'Action'}</div>
                    <div class="text-xs text-gray-400">${entry.description || ''}</div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-xs text-gray-500">${entry.formatted_date || ''}</div>
                  <div class="text-xs text-gray-400">${entry.ip_address || ''}</div>
                </div>
              </div>
            `).join('');
          } else {
            console.log('No activity log entries found');
            container.innerHTML = '<div class="text-gray-500 text-sm text-center py-4">No activity log entries found.</div>';
          }
        }
        
        // Update pagination
        if (paginationContainer && data.pagination) {
          const pagination = data.pagination;
          let paginationHTML = '';
          
          if (pagination.last_page > 1) {
            paginationHTML = '<div class="join">';
            
            // Previous button
            if (pagination.current_page > 1) {
              paginationHTML += `<button class="join-item btn btn-sm" onclick="loadActivityTracking(${documentId}, ${pagination.current_page - 1})">Previous</button>`;
            }
            
            // Page numbers
            for (let i = 1; i <= pagination.last_page; i++) {
              const isActive = i === pagination.current_page ? 'btn-active' : '';
              paginationHTML += `<button class="join-item btn btn-sm ${isActive}" onclick="loadActivityTracking(${documentId}, ${i})">${i}</button>`;
            }
            
            // Next button
            if (pagination.current_page < pagination.last_page) {
              paginationHTML += `<button class="join-item btn btn-sm" onclick="loadActivityTracking(${documentId}, ${pagination.current_page + 1})">Next</button>`;
            }
            
            paginationHTML += '</div>';
          }
          
          paginationContainer.innerHTML = paginationHTML;
        }
        
        // Re-initialize Lucide icons
        if (window.lucide && window.lucide.createIcons) {
          window.lucide.createIcons();
        }
      })
      .catch(error => {
        console.error('Error loading activity tracking:', error);
        const container = document.getElementById(`activity-log-container-${documentId}`);
        if (container) {
          container.innerHTML = '<div class="text-red-500 text-sm text-center py-4">Error loading activity log.</div>';
        }
      });
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Add Enter key support for password field (only for administrators)
      if (isAdministrator) {
        const passwordField = document.getElementById('unarchivePassword');
        if (passwordField) {
          passwordField.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
              confirmUnarchive();
            }
          });
        }
      }
    });
  </script>
</body>
</html>
