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
          <div class="flex items-center justify-between mb-6">
            <div>
              <h1 class="text-3xl font-bold text-gray-800 mb-2">Legal Cases</h1>
              <p class="text-gray-600">Manage and track all legal cases and proceedings</p>
            </div>
            <button onclick="openAddCaseModal()" class="btn btn-primary btn-lg">
              <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
              Add New Case
            </button>
          </div>
          
          <!-- Quick Stats Row -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                  <i data-lucide="briefcase" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Total Cases</p>
                  <p class="text-2xl font-bold text-gray-900">{{ $stats['total_cases'] ?? 0 }}</p>
                </div>
              </div>
        </div>

            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-yellow-100 rounded-lg">
                  <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Pending Review</p>
                  <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_cases'] ?? 0 }}</p>
                </div>
              </div>
            </div>
            
            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 rounded-lg">
                  <i data-lucide="activity" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Active Cases</p>
                  <p class="text-2xl font-bold text-gray-900">{{ $stats['active_cases'] ?? 0 }}</p>
                </div>
                </div>
              </div>
            </div>
          </div>

        <!-- Status Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Pending Cases -->
          <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 overflow-hidden">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-yellow-100 rounded-xl">
                  <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-xs font-medium text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full">Pending</span>
              </div>
              <div class="text-center">
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['pending_cases'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">Awaiting Review</p>
              </div>
            </div>
          </div>

          <!-- Ongoing Cases -->
          <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 overflow-hidden">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-100 rounded-xl">
                  <i data-lucide="gavel" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Active</span>
              </div>
              <div class="text-center">
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['ongoing_cases'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">In Progress</p>
              </div>
            </div>
          </div>

          <!-- Completed Cases -->
          <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 overflow-hidden">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-100 rounded-xl">
                  <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">Completed</span>
              </div>
              <div class="text-center">
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['completed_cases'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">Resolved</p>
              </div>
            </div>
          </div>

          <!-- Rejected Cases -->
          <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 overflow-hidden">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-red-100 rounded-xl">
                  <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                </div>
                <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-full">Rejected</span>
                </div>
              <div class="text-center">
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['rejected_cases'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">Not Approved</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Search and Filters Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Search & Filters</h3>
              <button onclick="clearFilters()" class="btn btn-outline btn-sm">
                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                Clear All
              </button>
                  </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- Search Bar -->
              <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" 
                       id="caseSearch"
                       placeholder="Search cases..." 
                       class="input input-bordered w-full pl-10 bg-gray-50 border-gray-200 focus:bg-white focus:border-blue-300">
                </div>

              <!-- Status Filter -->
              <div>
                <select id="statusFilter" class="select select-bordered w-full bg-gray-50 border-gray-200 focus:bg-white focus:border-blue-300">
                  <option value="">All Statuses</option>
                  <option value="pending">Pending</option>
                  <option value="ongoing">Ongoing</option>
                  <option value="completed">Completed</option>
                  <option value="rejected">Rejected</option>
                </select>
                </div>

              <!-- Priority Filter -->
              <div>
                <select id="priorityFilter" class="select select-bordered w-full bg-gray-50 border-gray-200 focus:bg-white focus:border-blue-300">
                  <option value="">All Priorities</option>
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                  <option value="urgent">Urgent</option>
                </select>
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
                    <th class="px-6 py-4 text-center font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-4 text-center font-semibold text-gray-700">Priority</th>
                    <th class="px-6 py-4 text-center font-semibold text-gray-700">Assigned To</th>
                    <th class="px-6 py-4 text-center font-semibold text-gray-700">Created</th>
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
                            @if($case->case_type)
                              <span class="badge badge-outline badge-xs">{{ ucfirst($case->case_type) }}</span>
                            @endif
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 text-center">
                        @php
                          $statusConfig = [
                            'pending' => ['class' => 'badge-warning', 'icon' => 'clock'],
                            'ongoing' => ['class' => 'badge-info', 'icon' => 'gavel'],
                            'completed' => ['class' => 'badge-success', 'icon' => 'check-circle'],
                            'rejected' => ['class' => 'badge-error', 'icon' => 'x-circle']
                          ];
                          $status = $case->status ?? 'pending';
                          $config = $statusConfig[$status] ?? $statusConfig['pending'];
                        @endphp
                        <span class="badge {{ $config['class'] }} gap-1">
                          <i data-lucide="{{ $config['icon'] }}" class="w-3 h-3"></i>
                          {{ ucfirst($status) }}
                        </span>
                      </td>
                      <td class="px-6 py-4 text-center">
                        @php
                          $priorityConfig = [
                            'low' => 'badge-neutral',
                            'medium' => 'badge-warning',
                            'high' => 'badge-error',
                            'urgent' => 'badge-error badge-outline'
                          ];
                          $priority = $case->priority ?? 'medium';
                          $priorityClass = $priorityConfig[$priority] ?? 'badge-neutral';
                        @endphp
                        <span class="badge {{ $priorityClass }}">{{ ucfirst($priority) }}</span>
                      </td>
                      <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center">
                          @if($case->assigned_to)
                            <div class="avatar placeholder">
                              <div class="bg-blue-100 text-blue-800 rounded-full w-8">
                                <span class="text-xs">{{ substr($case->assigned_to, 0, 2) }}</span>
                              </div>
                            </div>
                            <span class="ml-2 text-sm text-gray-700">{{ $case->assigned_to }}</span>
                          @else
                            <span class="text-gray-400 text-sm">Unassigned</span>
                          @endif
                        </div>
                      </td>
                      <td class="px-6 py-4 text-center">
                        <div class="text-sm text-gray-600">
                          {{ $case->created_at ? $case->created_at->format('M d, Y') : 'N/A' }}
                        </div>
                      </td>
                      <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                          <button onclick="viewCase({{ $case->id ?? 1 }})" 
                                  class="btn btn-ghost btn-xs tooltip" 
                                  data-tip="View Case">
                            <i data-lucide="eye" class="w-4 h-4 text-blue-600"></i>
                          </button>
                          <button onclick="editCase({{ $case->id ?? 1 }})" 
                                  class="btn btn-ghost btn-xs tooltip" 
                                  data-tip="Edit Case">
                            <i data-lucide="edit" class="w-4 h-4 text-green-600"></i>
                          </button>
                          <button onclick="deleteCase({{ $case->id ?? 1 }})" 
                                  class="btn btn-ghost btn-xs tooltip" 
                                  data-tip="Delete Case">
                            <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                          </button>
                      </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center py-12">
                        <div class="flex flex-col items-center">
                          <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="briefcase" class="w-10 h-10 text-gray-400"></i>
                  </div>
                          <h3 class="text-lg font-semibold text-gray-600 mb-2">No Legal Cases Found</h3>
                          <p class="text-gray-500 text-sm mb-4">Get started by creating your first legal case</p>
                          <a href="{{ route('legal.create') }}" class="btn btn-primary">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Add New Case
                    </a>
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
    <div class="modal-box w-11/12 max-w-4xl">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="plus" class="w-8 h-8 text-blue-500"></i>
          Add New Legal Case
        </h3>
        <button onclick="closeAddCaseModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <form id="addCaseForm" action="{{ route('legal.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          
          <!-- Left Column: Case Details -->
          <div class="space-y-6">
            <!-- Case Title -->
            <div class="form-control mb-6">
              <label class="label">
                <span class="label-text font-semibold" style="color: var(--color-charcoal-ink);">Case Title *</span>
              </label>
              <input type="text" name="case_title" id="case_title" class="input input-bordered w-full" 
                     placeholder="Enter case title" required style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
            </div>

            <!-- Case Description -->
            <div class="form-control mb-6">
              <label class="label">
                <span class="label-text font-semibold" style="color: var(--color-charcoal-ink);">Case Description</span>
              </label>
              <textarea name="case_description" id="case_description" class="textarea textarea-bordered w-full h-32" 
                        placeholder="Enter case description" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);"></textarea>
            </div>

            <!-- Case Type -->
            <div class="form-control mb-6">
              <label class="label">
                <span class="label-text font-semibold" style="color: var(--color-charcoal-ink);">Case Type *</span>
              </label>
              <select name="case_type" id="case_type" class="select select-bordered w-full" required style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                <option value="">Select case type</option>
                <option value="contract">Contract</option>
                <option value="litigation">Litigation</option>
                <option value="compliance">Compliance</option>
                <option value="policy">Policy</option>
                <option value="employment">Employment</option>
                <option value="intellectual_property">Intellectual Property</option>
                <option value="real_estate">Real Estate</option>
                <option value="other">Other</option>
              </select>
            </div>

            <!-- Priority -->
            <div class="form-control mb-6">
              <label class="label">
                <span class="label-text font-semibold" style="color: var(--color-charcoal-ink);">Priority *</span>
              </label>
              <select name="priority" id="priority" class="select select-bordered w-full" required style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                <option value="">Select priority</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>

            <!-- Assigned To -->
            <div class="form-control mb-6">
              <label class="label">
                <span class="label-text font-semibold" style="color: var(--color-charcoal-ink);">Assigned To</span>
              </label>
              <select name="assigned_to" id="assigned_to" class="select select-bordered w-full" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                <option value="">Select assignee</option>
                @foreach(\App\Models\DeptAccount::all() as $dept)
                  <option value="{{ $dept->Dept_no }}">{{ $dept->employee_name }} ({{ $dept->dept_name }})</option>
                @endforeach
              </select>
            </div>

            <!-- File Upload Section -->
            <div class="form-control mb-6">
              <label class="label">
                <span class="label-text font-semibold" style="color: var(--color-charcoal-ink);">Upload Legal Document *</span>
              </label>
              
              <!-- File Upload Zone -->
              <div id="uploadZone" class="border-2 border-dashed rounded-xl p-8 text-center transition-colors cursor-pointer"
                   onclick="triggerFileSelection()" 
                   ondrop="handleDrop(event)" 
                   ondragover="handleDragOver(event)" 
                   ondragleave="handleDragLeave(event)"
                   style="border-color: var(--color-regal-navy); background-color: var(--color-white);">
                
                <input type="file" name="legal_document" id="legal_document" class="hidden" 
                       accept=".pdf,.doc,.docx,.txt" required>
                
                <div class="space-y-4">
                  <div class="flex justify-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%);">
                      <i data-lucide="cloud-arrow-up" class="w-8 h-8" style="color: var(--color-regal-navy);"></i>
                    </div>
                  </div>
                  <div>
                    <p class="text-lg font-medium text-gray-700" style="color: var(--color-charcoal-ink);">Drop your legal document here</p>
                    <p class="text-sm text-gray-500 mt-2" style="color: var(--color-charcoal-ink); opacity: 0.7;">or click to browse files</p>
                  </div>
                  <button type="button" class="btn btn-outline btn-primary">
                    <i data-lucide="file" class="w-4 h-4 mr-2"></i>
                    CHOOSE FILE
                  </button>
                </div>
              </div>
              
              <!-- File Info -->
              <div class="mt-4">
                <p class="text-sm text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Accepted: PDF, DOC, DOCX, TXT (max 10MB)</p>
              </div>

              <!-- File Preview -->
              <div id="filePreview" class="mt-4 hidden">
                <div class="rounded-lg p-4 border" style="background-color: color-mix(in srgb, var(--color-modern-teal), white 90%); border-color: var(--color-modern-teal);">
                  <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5" style="color: var(--color-modern-teal);"></i>
                    <div>
                      <p class="font-medium text-green-800" id="fileName" style="color: var(--color-charcoal-ink);"></p>
                      <p class="text-sm text-green-600" id="fileSize" style="color: var(--color-charcoal-ink);"></p>
                    </div>
                    <button type="button" onclick="removeFile()" class="btn btn-ghost btn-sm">
                      <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: AI Classification Preview -->
          <div class="space-y-6">
            <div class="flex items-center mb-6">
              <i data-lucide="brain" class="w-6 h-6 mr-3" style="color: var(--color-regal-navy);"></i>
              <h2 class="text-xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">AI Classification Preview</h2>
            </div>

            <!-- AI Preview Content -->
            <div id="aiPreview" class="text-center py-12">
              <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%);">
                <i data-lucide="file-text" class="w-12 h-12" style="color: var(--color-regal-navy);"></i>
              </div>
              <h3 class="text-lg font-semibold text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">Upload a Document</h3>
              <p class="text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">AI will automatically classify your legal document and show the preview here.</p>
            </div>

            <!-- AI Analysis Results -->
            <div id="aiAnalysis" class="hidden space-y-4">
              <!-- AI Classification -->
              <div class="rounded-lg p-4 border" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 90%); border-color: var(--color-regal-navy);">
                <div class="flex items-center gap-2 mb-2">
                  <i data-lucide="wrench" class="w-4 h-4" style="color: var(--color-regal-navy);"></i>
                  <span class="text-sm font-medium" style="color: var(--color-charcoal-ink);">Document Type:</span>
                </div>
                <div class="text-lg font-bold text-blue-900 mb-1" id="aiCategory" style="color: var(--color-charcoal-ink);">Legal General</div>
                <div class="text-sm text-blue-700" id="aiConfidence" style="color: var(--color-regal-navy); opacity: 0.8;">AI Confidence: High (95%)</div>
              </div>

              <!-- AI Summary -->
              <div class="rounded-lg p-4 border" style="background-color: color-mix(in srgb, var(--color-snow-mist), black 5%); border-color: color-mix(in srgb, var(--color-snow-mist), black 10%);">
                <h4 class="font-semibold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">AI Summary</h4>
                <p class="text-gray-700 text-sm" id="aiSummary" style="color: var(--color-charcoal-ink);">This document has been analyzed by AI and classified as a general legal document.</p>
              </div>

              <!-- Key Information -->
              <div class="rounded-lg p-4 border" style="background-color: color-mix(in srgb, var(--color-modern-teal), white 90%); border-color: var(--color-modern-teal);">
                <h4 class="font-semibold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Key Information</h4>
                <p class="text-gray-700 text-sm" id="aiKeyInfo" style="color: var(--color-charcoal-ink);">Key information will be extracted during processing.</p>
              </div>

              <!-- Legal Implications -->
              <div class="rounded-lg p-4 border" style="background-color: color-mix(in srgb, var(--color-golden-ember), white 90%); border-color: var(--color-golden-ember);">
                <h4 class="font-semibold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Legal Implications</h4>
                <p class="text-gray-700 text-sm" id="aiLegalImplications" style="color: var(--color-charcoal-ink);">Legal implications will be determined based on document content.</p>
              </div>

              <!-- Tags / Compliance / Risk / Review Required -->
              <div class="rounded-lg p-4 border" style="background-color: color-mix(in srgb, var(--color-snow-mist), black 5%); border-color: color-mix(in srgb, var(--color-snow-mist), black 10%);">
                <div class="space-y-1 text-sm" style="color: var(--color-charcoal-ink);">
                  <div><strong>Compliance:</strong> <span id="aiCompliance">—</span></div>
                  <div><strong>Tags:</strong> <span id="aiTags">—</span></div>
                  <div><strong>Legal Risk:</strong> <span id="aiRisk">—</span></div>
                  <div><strong>Legal Review Required:</strong> <span id="aiReview">—</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
          <button type="button" onclick="closeAddCaseModal()" class="btn btn-outline">
            Cancel
          </button>
          <button type="submit" class="btn btn-warning btn-lg" style="background-color: var(--color-golden-ember); color: var(--color-white); border-color: var(--color-golden-ember);">
            <i data-lucide="arrow-up" class="w-5 h-5 mr-2"></i>
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
    
    // Modal functions
    function openAddCaseModal() {
      document.getElementById('addCaseModal').classList.add('modal-open');
      // Reset form when opening
      document.getElementById('addCaseForm').reset();
      document.getElementById('filePreview').classList.add('hidden');
      // Recreate icons after modal opens
      setTimeout(() => lucide.createIcons(), 100);
      
      // Add click outside to close functionality
      document.getElementById('addCaseModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeAddCaseModal();
        }
      });
      
      // Add escape key to close functionality
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeAddCaseModal();
        }
      });
    }
    
    function closeAddCaseModal() {
      document.getElementById('addCaseModal').classList.remove('modal-open');
      // Remove event listeners
      document.removeEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeAddCaseModal();
        }
      });
    }
    
    // File upload handling
    function triggerFileSelection() {
      document.getElementById('legal_document').click();
    }
    
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
      e.currentTarget.classList.add('border-blue-500', 'bg-blue-50');
    }

    function handleDragLeave(e) {
      e.preventDefault();
      e.currentTarget.classList.remove('border-blue-500', 'bg-blue-50');
    }

    function updateFilePreview(file) {
      const preview = document.getElementById('filePreview');
      const fileName = document.getElementById('fileName');
      const fileSize = document.getElementById('fileSize');
      
      fileName.textContent = file.name;
      fileSize.textContent = formatFileSize(file.size);
      preview.classList.remove('hidden');
    }

    function formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function removeFile() {
      document.getElementById('legal_document').value = '';
      document.getElementById('filePreview').classList.add('hidden');
      
      // Reset AI preview
      document.getElementById('aiAnalysis').classList.add('hidden');
      document.getElementById('aiPreview').classList.remove('hidden');
    }
    
    // AI Document Analysis
    function analyzeDocument(file) {
      const formData = new FormData();
      formData.append('document_file', file);
      formData.append('_token', '{{ csrf_token() }}');

      // Show loading state
      const aiPreview = document.getElementById('aiPreview');
      const aiAnalysis = document.getElementById('aiAnalysis');
      
      aiPreview.innerHTML = `
        <div class="flex items-center justify-center py-12">
          <div class="text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%);">
              <i data-lucide="loader-2" class="w-8 h-8 animate-spin" style="color: var(--color-regal-navy);"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2" style="color: var(--color-charcoal-ink);">Analyzing Document...</h3>
            <p style="color: var(--color-charcoal-ink); opacity: 0.8;">AI is classifying your legal document</p>
          </div>
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
          
          document.getElementById('aiCategory').textContent = displayCategory;
          document.getElementById('aiSummary').textContent = data.analysis.summary || 'This document has been analyzed by AI and classified as a legal document.';
          document.getElementById('aiKeyInfo').textContent = data.analysis.key_info || 'Key information extracted from document content.';
          document.getElementById('aiLegalImplications').textContent = data.analysis.legal_implications || 'Legal implications will be determined based on document content.';
          
          // Optional fields (compliance, tags, risk, review)
          const compliance = data.analysis.compliance_status || data.analysis.COMPLIANCE_STATUS || '—';
          const tags = Array.isArray(data.analysis.tags) ? data.analysis.tags.join(', ') : (data.analysis.TAGS || '—');
          const risk = data.analysis.legal_risk_score || data.analysis.LEGAL_RISK_SCORE || '—';
          const review = (data.analysis.requires_legal_review !== undefined)
            ? (data.analysis.requires_legal_review ? 'Yes' : 'No')
            : (data.analysis.LEGAL_REVIEW_REQUIRED ? (data.analysis.LEGAL_REVIEW_REQUIRED === 'YES' ? 'Yes' : 'No') : '—');

          const cEl = document.getElementById('aiCompliance');
          const tEl = document.getElementById('aiTags');
          const rEl = document.getElementById('aiRisk');
          const reviewEl = document.getElementById('aiReview');
          if (cEl) cEl.textContent = compliance;
          if (tEl) tEl.textContent = tags;
          if (rEl) rEl.textContent = risk;
          if (reviewEl) reviewEl.textContent = review;
          
          // Show analysis results
          aiAnalysis.classList.remove('hidden');
          aiPreview.classList.add('hidden');
        } else {
          // Show error state
          aiPreview.innerHTML = `
            <div class="text-center py-12">
              <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: color-mix(in srgb, var(--color-danger-red), white 80%);">
                <i data-lucide="alert-triangle" class="w-8 h-8" style="color: var(--color-danger-red);"></i>
              </div>
              <h3 class="text-lg font-semibold mb-2" style="color: var(--color-charcoal-ink);">Analysis Failed</h3>
              <p style="color: var(--color-charcoal-ink); opacity: 0.8;">${data.message}</p>
            </div>
          `;
        }
      })
      .catch(error => {
        // Show error state
        aiPreview.innerHTML = `
          <div class="text-center py-12">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: color-mix(in srgb, var(--color-danger-red), white 80%);">
              <i data-lucide="alert-triangle" class="w-8 h-8" style="color: var(--color-danger-red);"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2" style="color: var(--color-charcoal-ink);">Analysis Failed</h3>
            <p style="color: var(--color-charcoal-ink); opacity: 0.8;">Unable to analyze document</p>
          </div>
        `;
      });
    }
    
    // Search and filter functionality
    function filterCases() {
      const searchTerm = document.getElementById('caseSearch').value.toLowerCase();
      const statusFilter = document.getElementById('statusFilter').value;
      const priorityFilter = document.getElementById('priorityFilter').value;
      
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
        
        // Status filter
        if (statusFilter && showRow) {
          const status = row.querySelector('td:nth-child(2) .badge')?.textContent?.toLowerCase() || '';
          if (!status.includes(statusFilter)) {
            showRow = false;
          }
        }
        
        // Priority filter
        if (priorityFilter && showRow) {
          const priority = row.querySelector('td:nth-child(3) .badge')?.textContent?.toLowerCase() || '';
          if (!priority.includes(priorityFilter)) {
            showRow = false;
          }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
      });
    }
    
    function clearFilters() {
      document.getElementById('caseSearch').value = '';
      document.getElementById('statusFilter').value = '';
      document.getElementById('priorityFilter').value = '';
      
      // Show all rows
      const rows = document.querySelectorAll('tbody tr');
      rows.forEach(row => {
        row.style.display = '';
      });
    }
    
    // Case actions
    function viewCase(caseId) {
      window.location.href = `/legal/cases/${caseId}`;
    }
    
    function editCase(caseId) {
      window.location.href = `/legal/cases/${caseId}/edit`;
    }
    
    function deleteCase(caseId) {
      if (confirm('Are you sure you want to delete this legal case? This action cannot be undone.')) {
        fetch(`/legal/cases/${caseId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => {
          if (response.ok) {
              window.location.reload();
          } else {
            alert('Error deleting case');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while deleting the case');
        });
      }
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Search and filter event listeners
      document.getElementById('caseSearch').addEventListener('input', filterCases);
      document.getElementById('statusFilter').addEventListener('change', filterCases);
      document.getElementById('priorityFilter').addEventListener('change', filterCases);
      
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
    
    // Toast notification function
    function showToast(message, type = 'info') {
      // Create toast element
      const toast = document.createElement('div');
      toast.className = `alert alert-${type} fixed top-4 right-4 z-50 max-w-sm shadow-lg`;
      toast.innerHTML = `
        <i data-lucide="${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info'}" class="w-5 h-5"></i>
        <span>${message}</span>
      `;
      
      // Add to body
      document.body.appendChild(toast);
      
      // Recreate Lucide icons
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
      
      // Remove after 3 seconds
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 3000);
    }
  </script>
</body>
</html>
