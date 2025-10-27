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
            :value="$documents->count()" 
            icon="fa-file-text" 
            iconColor="text-yellow-400" 
            bgColor="bg-blue-900" />
          
          <!-- Received Today -->
          <x-stat-card 
            title="Received Today" 
            :value="$documents->where('created_at', '>=', now()->startOfDay())->count()" 
            icon="fa-calendar" 
            iconColor="text-yellow-400" 
            bgColor="bg-blue-900" />
          
          <!-- Expired Documents -->
          <x-stat-card 
            title="Expired Documents" 
            :value="$documents->where('status', 'expired')->count()" 
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

        <!-- Advanced Filters Section -->
        <div class="card bg-white shadow-xl mb-6 {{ $activeTab==='list' ? '' : 'hidden' }}">
          <div class="card-body">
            <h3 class="card-title text-lg mb-4">
              <i data-lucide="search" class="w-5 h-5 text-blue-600"></i>
              Advanced Document Search & Filtering
            </h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <!-- Search -->
              <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       class="input input-bordered w-full input-sm"
                       placeholder="Title, description, author...">
              </div>

              <!-- Category -->
              <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" id="category" class="select select-bordered w-full select-sm">
                  <option value="">All Categories</option>
                  <option value="contract" {{ request('category') == 'contract' ? 'selected' : '' }}>Contract</option>
                  <option value="policy" {{ request('category') == 'policy' ? 'selected' : '' }}>Policy</option>
                  <option value="legal_case" {{ request('category') == 'legal_case' ? 'selected' : '' }}>Legal Case</option>
                  <option value="compliance" {{ request('category') == 'compliance' ? 'selected' : '' }}>Compliance</option>
                  <option value="financial" {{ request('category') == 'financial' ? 'selected' : '' }}>Financial</option>
                </select>
              </div>

              <!-- Author -->
              <div>
                <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                <input type="text" name="author" id="author" value="{{ request('author') }}" 
                       class="input input-bordered w-full input-sm"
                       placeholder="Author name...">
              </div>

              <!-- Department -->
              <div>
                <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department" id="department" class="select select-bordered w-full select-sm">
                  <option value="">All Departments</option>
                  <option value="Legal" {{ request('department') == 'Legal' ? 'selected' : '' }}>Legal</option>
                  <option value="HR" {{ request('department') == 'HR' ? 'selected' : '' }}>HR</option>
                  <option value="Finance" {{ request('department') == 'Finance' ? 'selected' : '' }}>Finance</option>
                  <option value="Operations" {{ request('department') == 'Operations' ? 'selected' : '' }}>Operations</option>
                </select>
              </div>

              <!-- Date Range -->
              <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                       class="input input-bordered w-full input-sm">
              </div>

              <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                       class="input input-bordered w-full input-sm">
              </div>

              <!-- Confidentiality -->
              <div>
                <label for="confidentiality" class="block text-sm font-medium text-gray-700 mb-1">Confidentiality</label>
                <select name="confidentiality" id="confidentiality" class="select select-bordered w-full select-sm">
                  <option value="">All Levels</option>
                  <option value="public" {{ request('confidentiality') == 'public' ? 'selected' : '' }}>Public</option>
                  <option value="internal" {{ request('confidentiality') == 'internal' ? 'selected' : '' }}>Internal</option>
                  <option value="confidential" {{ request('confidentiality') == 'confidential' ? 'selected' : '' }}>Confidential</option>
                  <option value="restricted" {{ request('confidentiality') == 'restricted' ? 'selected' : '' }}>Restricted</option>
                </select>
              </div>

              <!-- Sort By -->
              <div>
                <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select name="sort_by" id="sort_by" class="select select-bordered w-full select-sm">
                  <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                  <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Title</option>
                  <option value="author" {{ request('sort_by') == 'author' ? 'selected' : '' }}>Author</option>
                  <option value="view_count" {{ request('sort_by') == 'view_count' ? 'selected' : '' }}>Most Viewed</option>
                  <option value="download_count" {{ request('sort_by') == 'download_count' ? 'selected' : '' }}>Most Downloaded</option>
                  <option value="last_edited_at" {{ request('sort_by') == 'last_edited_at' ? 'selected' : '' }}>Recently Edited</option>
                </select>
              </div>

              <!-- Sort Order -->
              <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                <select name="sort_order" id="sort_order" class="select select-bordered w-full select-sm">
                  <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                  <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>
              </div>

              <!-- Filter Button -->
              <div class="flex items-end">
                <button type="submit" class="btn btn-primary btn-sm">
                  <i data-lucide="search" class="w-4 h-4 mr-2"></i>Filter & Search
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Complete Archived Documents Table -->
        <div id="archived-documents-tab" class="card bg-white shadow-xl {{ $activeTab==='list' ? '' : 'hidden' }}">
          <div class="card-body">
            <div class="flex items-center justify-between mb-6">
              <div></div>
              <div class="flex items-center gap-3">
                <div class="text-sm text-gray-600">
                  Total: <span class="font-semibold">{{ $documents->count() }}</span> archived documents
                </div>
                <div class="flex space-x-2">
                  <button onclick="refreshDocuments()" class="btn btn-outline btn-sm">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-1"></i>Refresh
                  </button>
                </div>
              </div>
            </div>
            
            <x-table-card :title="'Archived Documents'" :pagination="$documents->links('pagination::tailwind')">
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
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Stats</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Actions</th>
                  </tr>
                </thead>
                <tbody>
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
                      
                      <!-- Stats Column -->
                      <td class="py-4 px-4 text-center">
                        <div class="flex flex-col space-y-1 text-xs">
                          <div class="flex items-center justify-center space-x-2">
                            <span title="Views" class="flex items-center text-gray-600">
                              <i data-lucide="eye" class="w-3 h-3 mr-1"></i>{{ $document->view_count ?? 0 }}
                            </span>
                            <span title="Downloads" class="flex items-center text-gray-600">
                              <i data-lucide="download" class="w-3 h-3 mr-1"></i>{{ $document->download_count ?? 0 }}
                            </span>
                          </div>
                          @if($document->last_edited_at)
                            <div class="text-gray-500 text-xs" title="Last edited">
                              <i data-lucide="edit" class="w-3 h-3 inline mr-1"></i>{{ $document->last_edited_at->format('M d') }}
                            </div>
                          @endif
                        </div>
                      </td>

                      <!-- Actions Column -->
                      <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center space-x-2">
                          <button onclick="viewDocument({{ $document->id }})" 
                                  class="p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110" 
                                  style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                  title="View Document"
                                  type="button">
                            <i data-lucide="eye" class="w-4 h-4" style="fill: none;"></i>
                          </button>
                          <button onclick="downloadDocument({{ $document->id }})" 
                                  class="p-2 rounded-lg transition-all duration-200 hover:scale-110"
                                  style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                  title="Download Document">
                            <i data-lucide="download" class="w-4 h-4" style="fill: none;"></i>
                          </button>
                          <button onclick="showDocumentHistory({{ $document->id }})" 
                                  class="p-2 rounded-lg transition-all duration-200 hover:scale-110"
                                  style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                  title="Document History & Tracking">
                            <i data-lucide="history" class="w-4 h-4" style="fill: none;"></i>
                          </button>
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
                      <td colspan="9" class="py-12 text-center">
                        <div class="flex flex-col items-center">
                          <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="archive" class="w-10 h-10 text-gray-400"></i>
                          </div>
                          <h3 class="text-lg font-semibold text-gray-600 mb-2">No Archived Documents Found</h3>
                          <p class="text-gray-500 text-sm mb-4">No documents have been archived yet.</p>
                        </div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </x-table-card>
          </div>
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
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
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

    // View document details in modal
    function viewDocumentDetails(documentId) {
      // Show loading state
      Swal.fire({
        title: 'Loading Document Details...',
        text: 'Please wait while we fetch the document information.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

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
          
          // Format dates
          const formatDate = (dateString) => {
            if (!dateString) return 'Not Set';
            return new Date(dateString).toLocaleDateString('en-US', {
              year: 'numeric',
              month: 'short',
              day: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            });
          };

          // Get status badge class
          const getStatusClass = (status) => {
            const statusConfig = {
              'active': 'bg-green-100 text-green-800',
              'archived': 'bg-gray-100 text-gray-800',
              'disposed': 'bg-gray-100 text-gray-800',
              'expired': 'bg-red-100 text-red-800',
              'pending_release': 'bg-yellow-100 text-yellow-800'
            };
            return statusConfig[status] || 'bg-gray-100 text-gray-800';
          };

          // Get confidentiality badge class
          const getConfidentialityClass = (confidentiality) => {
            const confConfig = {
              'public': 'bg-green-100 text-green-800',
              'internal': 'bg-yellow-100 text-yellow-800',
              'confidential': 'bg-orange-100 text-orange-800',
              'restricted': 'bg-red-100 text-red-800'
            };
            return confConfig[confidentiality] || 'bg-gray-100 text-gray-800';
          };

          // Create modal content
          const modalContent = `
            <div class="text-left">
              <!-- Document Header -->
              <div class="flex items-start justify-between mb-6">
                <div class="flex-1">
                  <h2 class="text-2xl font-bold text-gray-900 mb-2">${doc.title || 'Untitled Document'}</h2>
                  <p class="text-gray-600">Document ID: #${doc.id}</p>
                </div>
                <span class="text-xs font-medium px-3 py-1 rounded-full ${getStatusClass(doc.status)}">
                  ${doc.status ? doc.status.replace('_', ' ').toUpperCase() : 'ACTIVE'}
                </span>
              </div>

              ${doc.description ? `
                <div class="mb-6">
                  <h3 class="font-semibold text-gray-700 mb-2">Description</h3>
                  <p class="text-gray-600">${doc.description}</p>
                </div>
              ` : ''}

              <!-- Document Details Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                  <h4 class="font-semibold text-gray-700 mb-2">Document Information</h4>
                  <div class="space-y-2">
                    <div class="flex justify-between">
                      <span class="text-gray-600">Type:</span>
                      <span class="font-medium">${doc.type || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Category:</span>
                      <span class="font-medium">${doc.category || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Department:</span>
                      <span class="font-medium">${doc.department || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Confidentiality:</span>
                      <span class="px-2 py-1 text-xs rounded-full ${getConfidentialityClass(doc.confidentiality)}">
                        ${doc.confidentiality ? doc.confidentiality.toUpperCase() : 'N/A'}
                      </span>
                    </div>
                  </div>
                </div>
                
                <div>
                  <h4 class="font-semibold text-gray-700 mb-2">Timeline</h4>
                  <div class="space-y-2">
                    <div class="flex justify-between">
                      <span class="text-gray-600">Created:</span>
                      <span class="font-medium">${formatDate(doc.created_at)}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Updated:</span>
                      <span class="font-medium">${formatDate(doc.updated_at)}</span>
                    </div>
                    ${doc.retention_until ? `
                      <div class="flex justify-between">
                        <span class="text-gray-600">Retention Until:</span>
                        <span class="font-medium">${formatDate(doc.retention_until)}</span>
                      </div>
                    ` : ''}
                  </div>
                </div>
              </div>


            </div>
          `;

          // Show the modal
          Swal.fire({
            title: 'Document Details',
            html: modalContent,
            width: '800px',
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: 'Close',
            customClass: {
              popup: 'text-left'
            },
            didOpen: () => {
              // Re-initialize Lucide icons in the modal
              lucide.createIcons();
            }
          });
        } else {
          throw new Error('Invalid response format');
        }
      })
      .catch(error => {
        console.error('Error fetching document details:', error);
        console.error('Error details:', {
          message: error.message,
          stack: error.stack,
          response: error.response
        });
        Swal.fire({
          title: 'Error',
          text: `Failed to load document details: ${error.message}`,
          icon: 'error',
          confirmButtonText: 'OK'
        });
      });
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

    // Filter functions
    function clearFilters() {
      document.getElementById('searchInput').value = '';
      document.getElementById('categoryFilter').value = '';
      document.getElementById('sourceFilter').value = '';
      filterDocuments();
    }

    function filterDocuments() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const categoryFilter = document.getElementById('categoryFilter').value;
      const sourceFilter = document.getElementById('sourceFilter').value;
      
      const rows = document.querySelectorAll('tbody tr');
      
      rows.forEach(row => {
        let showRow = true;
        
        // Search filter
        if (searchTerm) {
          const title = row.querySelector('td:first-child .font-medium')?.textContent?.toLowerCase() || '';
          const description = row.querySelector('td:first-child .text-sm')?.textContent?.toLowerCase() || '';
          if (!title.includes(searchTerm) && !description.includes(searchTerm)) {
            showRow = false;
          }
        }
        
        // Category filter
        if (categoryFilter && showRow) {
          const category = row.querySelector('td:nth-child(2) .badge')?.textContent?.toLowerCase() || '';
          if (category !== categoryFilter.replace('_', ' ')) {
            showRow = false;
          }
        }
        
        // Source filter
        if (sourceFilter && showRow) {
          const source = row.querySelector('td:nth-child(3) .badge')?.textContent?.toLowerCase() || '';
          if (source !== sourceFilter.replace('_', ' ')) {
            showRow = false;
          }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
      });
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
            timer: 2000
          });
          // Reload collaborators list
          loadCollaborators(documentId);
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
      document.getElementById('searchInput').addEventListener('input', filterDocuments);
      document.getElementById('categoryFilter').addEventListener('change', filterDocuments);
      document.getElementById('sourceFilter').addEventListener('change', filterDocuments);
      
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
