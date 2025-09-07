<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Legal Cases Dashboard - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
  
  <style>
    /* CSS Variables for consistent styling */
    :root {
      --color-regal-navy: #1e3a8a;
      --color-charcoal-ink: #1f2937;
      --color-snow-mist: #f3f4f6;
      --color-white: #ffffff;
      --color-modern-teal: #0d9488;
      --color-golden-ember: #d97706;
      --color-danger-red: #dc2626;
    }
    
    /* Modal styling */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(4px);
    }
    
    .modal.modal-open {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .modal-box {
      background: white;
      border-radius: 12px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      max-height: 90vh;
      overflow-y: auto;
      animation: modalSlideIn 0.3s ease-out;
    }
    
    @keyframes modalSlideIn {
      from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
    
    /* Form styling */
    .form-control {
      margin-bottom: 1rem;
    }
    
    .label {
      margin-bottom: 0.5rem;
    }
    
    .label-text {
      font-weight: 600;
      color: #374151;
    }
    
    .input, .select, .textarea {
      border: 1px solid #d1d5db;
      border-radius: 6px;
      padding: 0.75rem;
      transition: border-color 0.2s ease;
    }
    
    .input:focus, .select:focus, .textarea:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Select element styling */
    .select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
      background-position: right 0.5rem center;
      background-repeat: no-repeat;
      background-size: 1.5em 1.5em;
      padding-right: 2.5rem;
    }
    
    /* File upload zone styling */
    #uploadZone {
      transition: all 0.2s ease;
    }
    
    #uploadZone:hover {
      border-color: #3b82f6;
      background-color: #eff6ff;
    }
    
    /* Loading spinner */
    .loading {
      display: inline-block;
      width: 1rem;
      height: 1rem;
      border: 2px solid #f3f3f3;
      border-top: 2px solid #3b82f6;
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

        <!-- Page Header with Stats -->
        <div class="mb-8">
          <div class="mb-6">
            <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-800 mb-2">Legal Cases</h1>
              <p class="text-gray-600">Manage and track all legal cases and proceedings</p>
              </div>
              @if(auth()->user()->role === 'Administrator')
              <button onclick="openAddCaseModal()" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Add New Case
              </button>
              @endif
            </div>
          </div>
          


        <!-- Status Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Cases -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-primary">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-primary text-primary-content rounded-full w-12 h-12">
                    <i data-lucide="briefcase" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-primary badge-outline">Total</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-primary justify-center mb-2">{{ $stats['total_cases'] ?? 0 }}</h2>
                <p class="text-base-content/70">All Cases</p>
              </div>
            </div>
          </div>

          <!-- Approved Cases -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-success">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-success text-success-content rounded-full w-12 h-12">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-success badge-outline">Approved</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-success justify-center mb-2">{{ $stats['approved_cases'] ?? 0 }}</h2>
                <p class="text-base-content/70">Completed</p>
              </div>
            </div>
          </div>

          <!-- Pending Cases -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-warning">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-warning text-warning-content rounded-full w-12 h-12">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-warning badge-outline">Pending</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-warning justify-center mb-2">{{ $stats['pending_cases'] ?? 0 }}</h2>
                <p class="text-base-content/70">Awaiting Review</p>
              </div>
            </div>
          </div>

          <!-- Declined Cases -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-error">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-error text-error-content rounded-full w-12 h-12">
                    <i data-lucide="x-circle" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-error badge-outline">Declined</div>
                </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-error justify-center mb-2">{{ $stats['declined_cases'] ?? 0 }}</h2>
                <p class="text-base-content/70">Not Approved</p>
              </div>
            </div>
          </div>
        </div>





        <!-- Cases Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Legal Cases</h3>
              <div class="text-sm text-gray-500">
                Showing {{ $cases->count() ?? 0 }} of {{ $stats['total_cases'] ?? 0 }} cases
              </div>
            </div>
            <div class="overflow-x-auto">
              <table class="table w-full">
                <thead>
                  <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-left font-semibold text-gray-700">Case Details</th>
                    <th class="px-6 py-4 text-center font-semibold text-gray-700">Type</th>
                    <th class="px-6 py-4 text-center font-semibold text-gray-700">Date</th>
                    <th class="px-6 py-4 text-center font-semibold text-gray-700">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($cases ?? [] as $case)
                    <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100">
                      <td class="px-6 py-4">
                        <div>
                          <h4 class="font-semibold text-gray-900 mb-1">{{ $case->case_title ?? 'Untitled Case' }}</h4>
                          <p class="text-sm text-gray-600 mb-2">{{ Str::limit($case->case_description ?? 'No description', 80) }}</p>
                          <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">#{{ $case->case_number ?? 'N/A' }}</span>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 text-center">
                        @if($case->case_type)
                          <span class="badge badge-outline badge-sm">{{ ucfirst($case->case_type) }}</span>
                        @else
                          <span class="text-gray-400 text-sm">N/A</span>
                        @endif
                      </td>
                      <td class="px-6 py-4 text-center">
                        <div class="text-sm text-gray-600">
                          {{ $case->created_at ? $case->created_at->format('M d, Y') : 'N/A' }}
                        </div>
                      </td>
                      <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                          @if($case->status === 'pending')
                            <button onclick="approveCase({{ $case->id ?? 1 }})" 
                                    class="btn btn-success btn-xs tooltip" 
                                    data-tip="Approve Case">
                              <i data-lucide="check" class="w-4 h-4 text-white"></i>
                            </button>
                            <button onclick="declineCase({{ $case->id ?? 1 }})" 
                                    class="btn btn-error btn-xs tooltip" 
                                    data-tip="Decline Case">
                              <i data-lucide="x" class="w-4 h-4 text-white"></i>
                            </button>
                          @endif
                          <button onclick="deleteCase({{ $case->id ?? 1 }})" 
                                  class="btn btn-error btn-xs tooltip" 
                                  data-tip="Delete Case">
                            <i data-lucide="trash-2" class="w-4 h-4 text-white"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="text-center py-12">
                        <div class="flex flex-col items-center">
                          <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="briefcase" class="w-10 h-10 text-gray-400"></i>
                  </div>
                          <h3 class="text-lg font-semibold text-gray-600 mb-2">No Legal Cases Found</h3>
                          <p class="text-gray-500 text-sm mb-4">Get started by creating your first legal case</p>
                          @if(auth()->user()->role === 'Administrator')
                          <button onclick="openAddCaseModal()" class="btn btn-primary">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Add New Case
                          </button>
                          @endif
                  </div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
                  </div>
            <!-- Pagination -->
            @if(isset($cases) && $cases->hasPages())
              <div class="flex justify-center p-6 border-t border-gray-200">
                {{ $cases->links() }}
              </div>
            @endif
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Add New Case Modal -->
  <div id="addCaseModal" class="modal">
    <div class="modal-box w-11/12 max-w-6xl bg-white text-gray-800 rounded-xl shadow-2xl" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Add New Legal Case</h2>
        <button onclick="closeAddCaseModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <form action="{{ route('legal.store') }}" method="POST" enctype="multipart/form-data" id="addCaseForm">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Left Column: Form Fields -->
          <div class="space-y-6">
            <!-- Case Title -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                Case Title*
              </label>
              <input type="text" name="case_title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                     value="{{ old('case_title') }}" placeholder="Enter case title" required 
                     style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                Enter a descriptive title for the legal case
              </p>
            </div>

            <!-- Case Description -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                Case Description
              </label>
              <textarea name="case_description" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                        rows="4" placeholder="Brief description of the legal case..." 
                        style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">{{ old('case_description') }}</textarea>
              <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                Provide a brief description of the case
              </p>
            </div>

            <!-- Case Type -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                Case Type*
              </label>
              <select name="case_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required
                      style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                <option value="">Select case type</option>
                <option value="contract_dispute" {{ old('case_type') == 'contract_dispute' ? 'selected' : '' }}>Contract Dispute</option>
                <option value="employment_law" {{ old('case_type') == 'employment_law' ? 'selected' : '' }}>Employment Law</option>
                <option value="intellectual_property" {{ old('case_type') == 'intellectual_property' ? 'selected' : '' }}>Intellectual Property</option>
                <option value="regulatory_compliance" {{ old('case_type') == 'regulatory_compliance' ? 'selected' : '' }}>Regulatory Compliance</option>
                <option value="litigation" {{ old('case_type') == 'litigation' ? 'selected' : '' }}>Litigation</option>
                <option value="other" {{ old('case_type') == 'other' ? 'selected' : '' }}>Other</option>
              </select>
              <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                Select the type of legal case
              </p>
            </div>

            <!-- Priority -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                Priority*
              </label>
              <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required
                      style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                <option value="">Select priority</option>
                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
              </select>
              <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                Set the priority level for this case
              </p>
            </div>

            <!-- Assigned To -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                Assigned To
              </label>
              <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                <option value="">Select assignee</option>
                <option value="legal_team" {{ old('assigned_to') == 'legal_team' ? 'selected' : '' }}>Legal Team</option>
                <option value="senior_counsel" {{ old('assigned_to') == 'senior_counsel' ? 'selected' : '' }}>Senior Counsel</option>
                <option value="external_counsel" {{ old('assigned_to') == 'external_counsel' ? 'selected' : '' }}>External Counsel</option>
              </select>
              <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                Assign the case to a team member
              </p>
            </div>
          </div>

          <!-- Right Column: Document Upload & AI Analysis -->
          <div class="space-y-6">
            <!-- Document File Section -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                Document File
              </label>
              <p class="text-sm text-gray-500 mb-3" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                PDF, Word, Excel, PPT, Text files (Max: 10MB)
              </p>
              
              <!-- File Upload Zone -->
              <div class="border-2 border-dashed border-blue-300 rounded-lg p-8 text-center transition-colors cursor-pointer bg-blue-50 hover:bg-blue-100"
                   onclick="document.getElementById('legal_document').click()" 
                   ondrop="handleDrop(event)" 
                   ondragover="handleDragOver(event)" 
                   ondragleave="handleDragLeave(event)"
                   id="uploadZone">
                
                <input type="file" name="legal_document" id="legal_document" class="hidden" 
                       accept=".pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx" required>
                
                <div class="space-y-4">
                  <div class="flex justify-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center bg-blue-100">
                      <i data-lucide="cloud-arrow-up" class="w-8 h-8 text-blue-600"></i>
                    </div>
                  </div>
                  <div>
                    <p class="text-lg font-medium text-gray-700" style="color: var(--color-charcoal-ink);">Click to select or drag file</p>
                    <p class="text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">Max file size: 10MB</p>
                  </div>
                  <p class="text-sm text-blue-600 font-medium">AI will automatically analyze and classify your document</p>
                </div>
              </div>
              
              <!-- File Preview -->
              <div id="filePreview" class="mt-4 hidden">
                <div class="rounded-lg p-4 border border-green-300 bg-green-50">
                  <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                    <div class="flex-1">
                      <p class="font-medium text-green-800" id="fileName"></p>
                      <p class="text-sm text-green-600" id="fileSize"></p>
                    </div>
                    <button type="button" onclick="removeFile()" class="text-green-600 hover:text-green-800">
                      <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- AI Analysis Complete Section -->
            <div id="aiAnalysis" class="hidden"></div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-8 pt-6 border-t border-gray-200">
          <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
            <i data-lucide="upload" class="w-5 h-5"></i>
            ADD CASE
          </button>
        </div>
      </form>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Role-based access control
    const userRole = '{{ auth()->user()->role }}';
    

    

    
    // Search and filter functionality
    function filterCases() {
      const searchTerm = document.getElementById('caseSearch')?.value?.toLowerCase() || '';
      const priorityFilter = document.getElementById('priorityFilter')?.value || '';
      
      const rows = document.querySelectorAll('tbody tr');
      
      rows.forEach(row => {
        let showRow = true;
        
        // Search filter
        if (searchTerm) {
          const title = row.querySelector('td:first-child h4')?.textContent?.toLowerCase() || '';
          const description = row.querySelector('td:first-child p')?.textContent?.toLowerCase() || '';
          if (!title.includes(searchTerm) && !description.includes(searchTerm)) {
            showRow = false;
          }
        }
        
        // Priority filter
        if (priorityFilter && showRow) {
          const priority = row.querySelector('td:nth-child(2) .badge')?.textContent?.toLowerCase() || '';
          if (!priority.includes(priorityFilter)) {
            showRow = false;
          }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
      });
    }
    
    function clearFilters() {
      const caseSearch = document.getElementById('caseSearch');
      const priorityFilter = document.getElementById('priorityFilter');
      
      if (caseSearch) caseSearch.value = '';
      if (priorityFilter) priorityFilter.value = '';
      
      // Show all rows
      const rows = document.querySelectorAll('tbody tr');
      rows.forEach(row => {
        row.style.display = '';
      });
    }
    
    // Case actions
    function deleteCase(caseId) {
      showDeleteModal(caseId);
    }

    // Show beautiful delete confirmation modal
    function showDeleteModal(caseId) {
      const modal = document.createElement('div');
      modal.className = 'modal modal-open';
      modal.innerHTML = `
        <div class="modal-box w-11/12 max-w-md bg-white text-gray-800 rounded-xl shadow-2xl" onclick="event.stopPropagation()">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <i data-lucide="trash-2" class="w-6 h-6 text-red-600"></i>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-800">Delete Legal Case</h3>
                <p class="text-sm text-gray-500">This action will permanently remove the case</p>
              </div>
            </div>
            <button onclick="closeDeleteModal()" class="btn btn-sm btn-circle btn-ghost">
              <i data-lucide="x" class="w-5 h-5"></i>
            </button>
          </div>

          <div class="mb-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                  <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                </div>
                <div>
                  <h4 class="font-semibold text-gray-800">Permanent Deletion</h4>
                  <p class="text-sm text-gray-600">Are you sure you want to delete this legal case? This action cannot be undone.</p>
                </div>
              </div>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
              <div class="flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-yellow-600"></i>
                <p class="text-sm text-yellow-700 font-medium">All case data will be permanently lost</p>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button onclick="closeDeleteModal()" class="btn btn-outline btn-sm hover:btn-primary transition-all duration-300 shadow-sm hover:shadow-md">
              <i data-lucide="x" class="w-4 h-4 mr-2"></i>
              Cancel
            </button>
            <button onclick="confirmDelete(${caseId})" class="btn btn-error btn-sm hover:btn-error-focus transition-all duration-300 shadow-sm hover:shadow-md transform hover:scale-105" id="confirmDeleteBtn">
              <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
              <span id="deleteBtnText">Delete Case</span>
            </button>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      lucide.createIcons();
      
      // Close modal when clicking outside
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          closeDeleteModal();
        }
      });
    }

    // Close delete modal
    function closeDeleteModal() {
      const modal = document.querySelector('.modal');
      if (modal) {
        modal.remove();
      }
    }

    // Confirm delete action
    function confirmDelete(caseId) {
      const confirmBtn = document.getElementById('confirmDeleteBtn');
      const btnText = document.getElementById('deleteBtnText');
      
      // Show loading state
      confirmBtn.disabled = true;
      btnText.textContent = 'Deleting...';
      confirmBtn.innerHTML = `
        <i class="loading loading-spinner loading-sm mr-2"></i>
        <span>Deleting...</span>
      `;
      
        fetch(`/legal/cases/${caseId}`, {
          method: 'DELETE',
          headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Close modal
          closeDeleteModal();
          
          // Show enhanced success notification
          showEnhancedToast('Legal case deleted successfully!', 'success', 'trash-2', 'Case has been permanently removed from the system.');
          
          // Reload page to update statistics and table
          setTimeout(() => window.location.reload(), 1500);
          } else {
          throw new Error(data.message || 'Failed to delete case');
          }
        })
        .catch(error => {
          console.error('Error:', error);
        
        // Reset button state
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = `
          <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
          <span>Delete Case</span>
        `;
        lucide.createIcons();
        
        // Show enhanced error notification
        showEnhancedToast('Error deleting case: ' + error.message, 'error', 'alert-circle', 'Please try again or contact support if the issue persists.');
      });
    }

    // Approve a legal case
    function approveCase(caseId) {
      showApprovalModal(caseId);
    }

    // Show beautiful approval confirmation modal
    function showApprovalModal(caseId) {
      const modal = document.createElement('div');
      modal.className = 'modal modal-open';
      modal.innerHTML = `
        <div class="modal-box w-11/12 max-w-md bg-white text-gray-800 rounded-xl shadow-2xl" onclick="event.stopPropagation()">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-800">Approve Legal Case</h3>
                <p class="text-sm text-gray-500">This action will mark the case as completed</p>
              </div>
            </div>
            <button onclick="closeApprovalModal()" class="btn btn-sm btn-circle btn-ghost">
              <i data-lucide="x" class="w-5 h-5"></i>
            </button>
          </div>

          <div class="mb-6">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                  <i data-lucide="gavel" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                  <h4 class="font-semibold text-gray-800">Case Approval</h4>
                  <p class="text-sm text-gray-600">Are you sure you want to approve this legal case?</p>
                </div>
              </div>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
              <div class="flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-yellow-600"></i>
                <p class="text-sm text-yellow-700 font-medium">This action cannot be undone</p>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button onclick="closeApprovalModal()" class="btn btn-outline btn-sm hover:btn-primary transition-all duration-300 shadow-sm hover:shadow-md">
              <i data-lucide="x" class="w-4 h-4 mr-2"></i>
              Cancel
            </button>
            <button onclick="confirmApproval(${caseId})" class="btn btn-success btn-sm hover:btn-success-focus transition-all duration-300 shadow-sm hover:shadow-md transform hover:scale-105" id="confirmApprovalBtn">
              <i data-lucide="check" class="w-4 h-4 mr-2"></i>
              <span id="approvalBtnText">Approve Case</span>
            </button>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      lucide.createIcons();
      
      // Close modal when clicking outside
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          closeApprovalModal();
        }
      });
    }

    // Close approval modal
    function closeApprovalModal() {
      const modal = document.querySelector('.modal');
      if (modal) {
        modal.remove();
      }
    }

    // Confirm approval action
    function confirmApproval(caseId) {
      const confirmBtn = document.getElementById('confirmApprovalBtn');
      const btnText = document.getElementById('approvalBtnText');
      
      // Show loading state
      confirmBtn.disabled = true;
      btnText.textContent = 'Approving...';
      confirmBtn.innerHTML = `
        <i class="loading loading-spinner loading-sm mr-2"></i>
        <span>Approving...</span>
      `;
      
      fetch(`/legal/cases/${caseId}/approve`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Close modal
          closeApprovalModal();
          
          // Show enhanced success notification
          showEnhancedToast('Legal case approved successfully!', 'success', 'check-circle', 'Case has been marked as completed and moved to approved status.');
          
          // Reload page to update statistics and table
          setTimeout(() => window.location.reload(), 1500);
        } else {
          throw new Error(data.message || 'Failed to approve case');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        
        // Reset button state
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = `
          <i data-lucide="check" class="w-4 h-4 mr-2"></i>
          <span>Approve Case</span>
        `;
        lucide.createIcons();
        
        // Show enhanced error notification
        showEnhancedToast('Error approving case: ' + error.message, 'error', 'alert-circle', 'Please try again or contact support if the issue persists.');
      });
    }

    // Decline a legal case
    function declineCase(caseId) {
      showDeclineModal(caseId);
    }

    // Show beautiful decline confirmation modal
    function showDeclineModal(caseId) {
      const modal = document.createElement('div');
      modal.className = 'modal modal-open';
      modal.innerHTML = `
        <div class="modal-box w-11/12 max-w-md bg-white text-gray-800 rounded-xl shadow-2xl" onclick="event.stopPropagation()">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-800">Decline Legal Case</h3>
                <p class="text-sm text-gray-500">This action will mark the case as rejected</p>
              </div>
            </div>
            <button onclick="closeDeclineModal()" class="btn btn-sm btn-circle btn-ghost">
              <i data-lucide="x" class="w-5 h-5"></i>
            </button>
          </div>

          <div class="mb-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                  <i data-lucide="gavel" class="w-5 h-5 text-red-600"></i>
                </div>
                <div>
                  <h4 class="font-semibold text-gray-800">Case Rejection</h4>
                  <p class="text-sm text-gray-600">Are you sure you want to decline this legal case?</p>
                </div>
              </div>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
              <div class="flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-yellow-600"></i>
                <p class="text-sm text-yellow-700 font-medium">This action cannot be undone</p>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button onclick="closeDeclineModal()" class="btn btn-outline btn-sm hover:btn-primary transition-all duration-300 shadow-sm hover:shadow-md">
              <i data-lucide="x" class="w-4 h-4 mr-2"></i>
              Cancel
            </button>
            <button onclick="confirmDecline(${caseId})" class="btn btn-error btn-sm hover:btn-error-focus transition-all duration-300 shadow-sm hover:shadow-md transform hover:scale-105" id="confirmDeclineBtn">
              <i data-lucide="x" class="w-4 h-4 mr-2"></i>
              <span id="declineBtnText">Decline Case</span>
            </button>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      lucide.createIcons();
      
      // Close modal when clicking outside
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          closeDeclineModal();
        }
      });
    }

    // Close decline modal
    function closeDeclineModal() {
      const modal = document.querySelector('.modal');
      if (modal) {
        modal.remove();
      }
    }

    // Confirm decline action
    function confirmDecline(caseId) {
      const confirmBtn = document.getElementById('confirmDeclineBtn');
      const btnText = document.getElementById('declineBtnText');
      
      // Show loading state
      confirmBtn.disabled = true;
      btnText.textContent = 'Declining...';
      confirmBtn.innerHTML = `
        <i class="loading loading-spinner loading-sm mr-2"></i>
        <span>Declining...</span>
      `;
      
      fetch(`/legal/cases/${caseId}/decline`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Close modal
          closeDeclineModal();
          
          // Show enhanced success notification
          showEnhancedToast('Legal case declined successfully!', 'success', 'x-circle', 'Case has been marked as rejected and moved to declined status.');
          
          // Reload page to update statistics and table
          setTimeout(() => window.location.reload(), 1500);
        } else {
          throw new Error(data.message || 'Failed to decline case');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        
        // Reset button state
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = `
          <i data-lucide="x" class="w-4 h-4 mr-2"></i>
          <span>Decline Case</span>
        `;
        lucide.createIcons();
        
        // Show enhanced error notification
        showEnhancedToast('Error declining case: ' + error.message, 'error', 'alert-circle', 'Please try again or contact support if the issue persists.');
      });
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Search and filter event listeners
      const caseSearch = document.getElementById('caseSearch');
      const priorityFilter = document.getElementById('priorityFilter');
      
      if (caseSearch) caseSearch.addEventListener('input', filterCases);
      if (priorityFilter) priorityFilter.addEventListener('change', filterCases);
      
      // File input change event listener
      const fileInput = document.getElementById('legal_document');
      if (fileInput) {
        fileInput.addEventListener('change', function(e) {
          if (e.target.files.length > 0) {
            updateFilePreview(e.target.files[0]);
            analyzeDocument(e.target.files[0]);
          }
        });
      }
      
      // Form submission handler
      const addCaseForm = document.getElementById('addCaseForm');
      if (addCaseForm) {
        addCaseForm.addEventListener('submit', function(e) {
          e.preventDefault();
          handleFormSubmission();
        });
      }
    });
    
    // Handle form submission
    function handleFormSubmission() {
      const form = document.getElementById('addCaseForm');
      const formData = new FormData(form);
      
      // Show loading state
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="loading loading-spinner"></i> Creating Case...';
      submitBtn.disabled = true;
      
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message
          showToast('Legal case created successfully!', 'success');
          // Close modal
          closeAddCaseModal();
          // Reload page to show new case
          setTimeout(() => window.location.reload(), 1000);
        } else {
          throw new Error(data.message || 'Failed to create case');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('Error creating case: ' + error.message, 'error');
        // Restore submit button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      });
    }
    
    // Enhanced toast notification function
    function showEnhancedToast(title, type = 'info', icon = 'info', description = '') {
      // Create toast container if it doesn't exist
      let toastContainer = document.getElementById('toastContainer');
      if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'fixed bottom-4 right-4 z-50 space-y-3';
        document.body.appendChild(toastContainer);
      }
      
      // Create toast element
      const toast = document.createElement('div');
      toast.className = `alert alert-${type} shadow-xl max-w-sm transform transition-all duration-500 translate-x-full opacity-0`;
      
      // Set icon based on type
      const iconMap = {
        'success': 'check-circle',
        'error': 'alert-circle',
        'warning': 'alert-triangle',
        'info': 'info'
      };
      
      const finalIcon = icon || iconMap[type] || 'info';
      
      toast.innerHTML = `
        <div class="flex items-start gap-3">
          <div class="flex-shrink-0">
            <i data-lucide="${finalIcon}" class="w-6 h-6"></i>
          </div>
          <div class="flex-1 min-w-0">
            <h4 class="font-semibold text-sm">${title}</h4>
            ${description ? `<p class="text-xs opacity-90 mt-1">${description}</p>` : ''}
          </div>
          <button onclick="this.parentElement.parentElement.remove()" class="btn btn-ghost btn-xs p-1">
            <i data-lucide="x" class="w-4 h-4"></i>
          </button>
        </div>
      `;
      
      // Add to container
      toastContainer.appendChild(toast);
      
      // Recreate Lucide icons
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
      
      // Animate in
      setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
      }, 100);
      
      // Auto remove after duration
      const duration = type === 'error' ? 6000 : 4000;
      setTimeout(() => {
        if (toast.parentNode) {
          toast.classList.add('translate-x-full', 'opacity-0');
          setTimeout(() => {
            if (toast.parentNode) {
              toast.parentNode.removeChild(toast);
            }
          }, 500);
        }
      }, duration);
    }

    // Legacy toast function for backward compatibility
    function showToast(message, type = 'info') {
      showEnhancedToast(message, type);
    }

    // Modal functions for Add New Case
    function openAddCaseModal() {
      const modal = document.getElementById('addCaseModal');
      modal.classList.add('modal-open');
      document.body.style.overflow = 'hidden';
      
      // Initialize Lucide icons in modal
      lucide.createIcons();
    }

    function closeAddCaseModal() {
      const modal = document.getElementById('addCaseModal');
      modal.classList.remove('modal-open');
      document.body.style.overflow = 'auto';
      
      // Reset form
      const form = document.getElementById('addCaseForm');
      form.reset();
      
      // Hide file preview and AI analysis
      document.getElementById('filePreview').classList.add('hidden');
      document.getElementById('aiAnalysis').classList.add('hidden');
    }

    // File upload handling functions
    function handleDrop(e) {
      e.preventDefault();
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        document.getElementById('legal_document').files = files;
        updateFilePreview(files[0]);
        analyzeDocument(files[0]);
      }
    }

    function handleDragOver(e) {
      e.preventDefault();
    }

    function handleDragLeave(e) {
      e.preventDefault();
    }

    function updateFilePreview(file) {
      const preview = document.getElementById('filePreview');
      const fileName = document.getElementById('fileName');
      const fileSize = document.getElementById('fileSize');
      
      fileName.textContent = file.name;
      fileSize.textContent = formatFileSize(file.size);
      preview.classList.remove('hidden');
    }

    function removeFile() {
      document.getElementById('legal_document').value = '';
      document.getElementById('filePreview').classList.add('hidden');
      document.getElementById('aiAnalysis').classList.add('hidden');
    }

    function formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // AI Document Analysis
    function analyzeDocument(file) {
      const formData = new FormData();
      formData.append('document_file', file);
      formData.append('_token', '{{ csrf_token() }}');

      // Show loading state
      const aiAnalysisPanel = document.getElementById('aiAnalysis');
      aiAnalysisPanel.classList.remove('hidden');
      aiAnalysisPanel.innerHTML = `
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="loader-2" class="w-5 h-5 animate-spin text-blue-500"></i>
            <h3 class="font-medium text-blue-800">Analyzing Document...</h3>
          </div>
          <p class="text-sm text-blue-600">AI is processing your document</p>
        </div>
      `;

      fetch('{{ route("document.analyzeUpload") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(async response => {
        const contentType = response.headers.get('content-type') || '';
        if (!response.ok) {
          const fallback = contentType.includes('application/json') ? await response.json() : { success: false, message: 'Server error' };
          return fallback;
        }
        if (!contentType.includes('application/json')) {
          return { success: false, message: 'Unexpected response from server' };
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          // Update AI analysis results
          const categoryDisplayNames = {
            'memorandum': 'Memorandum',
            'contract': 'Contract',
            'subpoena': 'Subpoena',
            'affidavit': 'Affidavit',
            'cease_desist': 'Cease & Desist',
            'legal_notice': 'Legal Notice',
            'policy': 'Policy',
            'legal_brief': 'Legal Brief',
            'financial': 'Financial Document',
            'compliance': 'Compliance Document',
            'report': 'Report',
            'general': 'Legal General'
          };

          const displayCategory = categoryDisplayNames[data.analysis.category] || 'Legal General';
          
          const summary = data.analysis.summary || '—';
          const compliance = data.analysis.compliance_status || 'review_required';
          const tags = data.analysis.tags ? (Array.isArray(data.analysis.tags) ? data.analysis.tags.join(', ') : data.analysis.tags) : '—';
          const risk = data.analysis.legal_risk_score || 'Low';
          const needsReview = (data.analysis.requires_legal_review ? 'Yes' : 'No');

          aiAnalysisPanel.innerHTML = `
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
              <div class="flex items-center gap-3 mb-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                <h3 class="font-medium text-green-800">AI Analysis Complete</h3>
              </div>
              <div class="space-y-2 text-sm">
                <div><strong>Category:</strong> <span class="font-semibold text-green-700">${displayCategory}</span></div>
                <div><strong>Summary:</strong> <span class="text-green-700">${summary}</span></div>
                <div><strong>Compliance:</strong> <span class="text-green-700">${compliance}</span></div>
                <div><strong>Tags:</strong> <span class="text-green-700">${tags}</span></div>
                <div><strong>Legal Risk:</strong> <span class="text-green-700">${risk}</span></div>
                <div><strong>Legal Review Required:</strong> <span class="text-green-700">${needsReview}</span></div>
              </div>
            </div>
          `;
          lucide.createIcons();
        } else {
          // Show error state
          aiAnalysisPanel.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
              <div class="flex items-center gap-3 mb-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                <h3 class="font-medium text-red-800">Analysis Failed</h3>
              </div>
              <p class="text-sm text-red-600">${data.message || 'Unable to analyze document'}</p>
            </div>
          `;
        }
      })
      .catch(error => {
        // Show error state
        aiAnalysisPanel.innerHTML = `
          <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center gap-3 mb-3">
              <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
              <h3 class="font-medium text-red-800">Analysis Failed</h3>
            </div>
            <p class="text-sm text-red-600">Network or server error</p>
          </div>
        `;
      });
    }

    // Event listeners for modal
    document.addEventListener('DOMContentLoaded', function() {
      // File input change event listener
      const fileInput = document.getElementById('legal_document');
      if (fileInput) {
        fileInput.addEventListener('change', function(e) {
          if (e.target.files.length > 0) {
            updateFilePreview(e.target.files[0]);
            analyzeDocument(e.target.files[0]);
          }
        });
      }
      
      // Form submission handler
      const addCaseForm = document.getElementById('addCaseForm');
      if (addCaseForm) {
        addCaseForm.addEventListener('submit', function(e) {
          e.preventDefault();
          handleFormSubmission();
        });
      }

      // Close modal when clicking outside
      const modal = document.getElementById('addCaseModal');
      if (modal) {
        modal.addEventListener('click', function(e) {
          if (e.target === modal) {
            closeAddCaseModal();
          }
        });
      }
    });
  </script>
</body>
</html>
