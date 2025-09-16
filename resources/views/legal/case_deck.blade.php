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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    
    /* SweetAlert2 Custom Styling */
    .swal2-popup { 
      font-family: inherit; 
      border-radius: 12px !important; 
    }
    .swal2-confirm { 
      background-color: #ef4444 !important; 
      border: none !important; 
      padding: 12px 24px !important; 
      border-radius: 8px !important; 
      font-weight: 600 !important; 
      color: white !important; 
      margin-right: 8px !important; 
    }
    .swal2-cancel { 
      background-color: #6b7280 !important; 
      border: none !important; 
      padding: 12px 24px !important; 
      border-radius: 8px !important; 
      font-weight: 600 !important; 
      color: white !important; 
      margin-left: 8px !important; 
    }
    .swal2-actions { 
      gap: 10px !important; 
      margin-top: 20px !important; 
    }
    .swal2-title { 
      font-size: 20px !important; 
      font-weight: 600 !important; 
      margin-bottom: 16px !important; 
    }
    .swal2-content { 
      font-size: 16px !important; 
      line-height: 1.5 !important; 
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
          <div class="toast toast-bottom toast-end">
            <div class="alert alert-success">
              <i data-lucide="check-circle" class="w-5 h-5"></i>
              <span>{{ session('success') }}</span>
            </div>
          </div>
        @endif

        @if(session('error'))
          <div class="toast toast-bottom toast-end">
            <div class="alert alert-error">
              <i data-lucide="alert-circle" class="w-5 h-5"></i>
              <span>{{ session('error') }}</span>
            </div>
          </div>
        @endif

        <!-- Legal Cases Content -->
        <div class="pb-5 border-b border-base-300 animate-fadeIn">
          <h1 class="text-2xl font-semibold bg-white bg-clip-text text-[#191970]" style="color: var(--color-charcoal-ink);">Violation & Compliance Cases</h1>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-end mt-6 mb-8">
          @if(auth()->user()->role === 'Administrator')
          <button onclick="openAddCaseModal()" class="btn btn-primary">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Report Violation
          </button>
          @endif
        </div>
          


        <!-- Status Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Cases -->
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-primary">
              <div class="card-body p-4">
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
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-success">
              <div class="card-body p-4">
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
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-warning">
              <div class="card-body p-4">
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
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-error">
              <div class="card-body p-4">
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
            
            <!-- Proper HTML Table -->
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr class="bg-gray-50">
                    <th class="text-left py-4 px-4 font-semibold text-gray-700 w-16">#</th>
                    <th class="text-left py-4 px-4 font-semibold text-gray-700">Case Information</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Type</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-40">Employee Involved</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Incident Date</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Status</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-24">Priority</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($cases ?? collect() as $index => $case)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                      <!-- ID Column -->
                      <td class="py-4 px-4">
                        <div class="text-sm font-medium text-gray-500">
                          #{{ $index + 1 }}
                        </div>
                      </td>
                      
                      <!-- Case Information Column -->
                      <td class="py-4 px-4">
                        <div class="flex items-center space-x-3">
                          <!-- Avatar -->
                          <div class="avatar placeholder">
                            <div class="bg-blue-100 text-blue-800 rounded-full w-10 h-10 flex items-center justify-center">
                              <span class="text-sm font-semibold">
                                {{ substr($case->case_title ?? 'UC', 0, 2) }}
                              </span>
                            </div>
                          </div>
                          
                          <!-- Case Title -->
                          <div>
                            <h4 class="font-semibold text-gray-900">{{ $case->case_title ?? 'Untitled Case' }}</h4>
                            <p class="text-sm text-gray-500">#{{ $case->case_number ?? 'LC-2025-0000' }}</p>
                          </div>
                        </div>
                      </td>
                      
                      <!-- Type Column -->
                      <td class="py-4 px-4 text-center">
                        @if($case->case_type)
                          @php
                            $violationTypes = [
                              'theft' => 'Theft',
                              'hr_policy_violation' => 'HR Policy',
                              'hr_policy' => 'HR Policy',
                              'workplace_harassment' => 'Harassment',
                              'harassment' => 'Harassment',
                              'fraud' => 'Fraud',
                              'safety_violation' => 'Safety',
                              'safety' => 'Safety',
                              'insubordination' => 'Insubordination',
                              'attendance_violation' => 'Attendance',
                              'attendance' => 'Attendance',
                              'confidentiality_breach' => 'Confidentiality',
                              'confidentiality' => 'Confidentiality',
                              'property_damage' => 'Property Damage',
                              'property' => 'Property Damage',
                              'guest_complaint' => 'Guest Complaint',
                              'complaint' => 'Guest Complaint',
                              'regulatory_violation' => 'Regulatory',
                              'regulatory' => 'Regulatory',
                              'violation' => 'Policy Violation',
                              'other' => 'Other'
                            ];
                            $displayType = $violationTypes[$case->case_type] ?? ucfirst(str_replace('_', ' ', $case->case_type));
                          @endphp
                          <span class="text-sm font-medium text-gray-700">{{ $displayType }}</span>
                        @else
                          <span class="text-sm text-gray-400">Not specified</span>
                        @endif
                      </td>
                      
                      <!-- Employee Involved Column -->
                      <td class="py-4 px-4 text-center">
                        @if($case->employee_involved)
                          <span class="text-sm font-medium text-gray-700">{{ $case->employee_involved }}</span>
                        @else
                          <span class="text-sm text-gray-400">Not specified</span>
                        @endif
                      </td>
                      
                      <!-- Incident Date Column -->
                      <td class="py-4 px-4 text-center">
                        @if($case->incident_date)
                          <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($case->incident_date)->format('M d, Y') }}</span>
                        @else
                          <span class="text-sm text-gray-400">Not specified</span>
                        @endif
                      </td>
                      
                      <!-- Status Column -->
                      <td class="py-4 px-4 text-center">
                        @php
                          $statusConfig = [
                            'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'clock', 'text' => 'Pending'],
                            'ongoing' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'play-circle', 'text' => 'Ongoing'],
                            'completed' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'check-circle', 'text' => 'Completed'],
                            'rejected' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'x-circle', 'text' => 'Rejected'],
                            'active' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'play-circle', 'text' => 'Active'],
                            'on_hold' => ['class' => 'bg-orange-100 text-orange-800', 'icon' => 'pause-circle', 'text' => 'On Hold'],
                            'escalated' => ['class' => 'bg-purple-100 text-purple-800', 'icon' => 'arrow-up-circle', 'text' => 'Escalated']
                          ];
                          $status = $statusConfig[$case->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'help-circle', 'text' => ucfirst($case->status)];
                        @endphp
                        <div class="flex items-center justify-center space-x-1">
                          <i data-lucide="{{ $status['icon'] }}" class="w-4 h-4"></i>
                          <span class="text-sm font-medium {{ $status['class'] }} px-2 py-1 rounded-full">{{ $status['text'] }}</span>
                        </div>
                      </td>
                      
                      <!-- Priority Column -->
                      <td class="py-4 px-4 text-center">
                        @php
                          $priority = $case->priority ?? 'medium';
                          $priorityConfig = [
                            'urgent' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Urgent'],
                            'high' => ['class' => 'bg-orange-100 text-orange-800', 'text' => 'High'],
                            'medium' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Medium'],
                            'low' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Low']
                          ];
                          $priorityInfo = $priorityConfig[$priority] ?? ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($priority)];
                        @endphp
                        <span class="text-xs font-medium {{ $priorityInfo['class'] }} px-2 py-1 rounded-full">{{ $priorityInfo['text'] }}</span>
                      </td>
                      
                      <!-- Actions Column -->
                      <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center space-x-2">
                          <!-- Review Button -->
                          <a href="{{ route('legal.cases.review', $case->id ?? 1) }}" 
                             class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200" 
                             title="Review Case">
                            <i data-lucide="search" class="w-4 h-4"></i>
                          </a>
                          
                          @if($case->status === 'pending')
                            <!-- Approve Button -->
                            <button onclick="approveCase({{ $case->id ?? 1 }})" 
                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200" 
                                    title="Approve Case">
                              <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Decline Button -->
                            <button onclick="declineCase({{ $case->id ?? 1 }})" 
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200" 
                                    title="Decline Case">
                              <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                          @endif
                          
                          <!-- Delete Button -->
                          <button onclick="deleteCase({{ $case->id ?? 1 }})" 
                                  class="p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors duration-200" 
                                  title="Delete Case">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="8" class="py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                          <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="shield-alert" class="w-10 h-10 text-gray-400"></i>
                          </div>
                          <h3 class="text-lg font-semibold text-gray-600 mb-2">No Violation Cases Found</h3>
                          <p class="text-gray-500 text-sm mb-4">Track employee violations, compliance issues, and legal actions</p>
                          @if(auth()->user()->role === 'Administrator')
                          <button onclick="openAddCaseModal()" class="btn btn-primary">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Report Violation
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
    <div class="modal-box w-11/12 max-w-5xl bg-white text-gray-800 rounded-xl shadow-2xl" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
            <i data-lucide="shield-alert" class="w-6 h-6 text-red-600"></i>
          </div>
          <div>
        <h2 class="text-2xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Report Violation / Compliance Issue</h2>
            <p class="text-sm text-gray-500">Submit a new legal case for review and investigation</p>
          </div>
        </div>
        <button onclick="closeAddCaseModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <form action="{{ route('legal.store') }}" method="POST" id="addCaseForm">
        @csrf
        
        <!-- Form Sections -->
        <div class="space-y-8">
          <!-- Basic Information Section -->
          <div class="bg-gray-50 rounded-lg p-6">
            <div class="flex items-center gap-2 mb-4">
              <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
              <h3 class="text-lg font-semibold text-gray-800">Basic Information</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Case Title -->
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Case Title*
                </label>
                <input type="text" name="case_title" id="caseTitle" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       value="{{ old('case_title') }}" placeholder="Enter case title" required>
                <p class="mt-1 text-sm text-gray-500">
                  Enter a descriptive title for the legal case
                </p>
              </div>

              <!-- Violation Template -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Violation Template
                </label>
                <select id="violationTemplate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  <option value="">Select a template (optional)</option>
                  <option value="theft_template">Theft / Stealing Template</option>
                  <option value="hr_policy_template">HR Policy Violation Template</option>
                  <option value="harassment_template">Workplace Harassment Template</option>
                  <option value="safety_template">Safety Violation Template</option>
                  <option value="attendance_template">Attendance Violation Template</option>
                  <option value="confidentiality_template">Confidentiality Breach Template</option>
                  <option value="property_damage_template">Property Damage Template</option>
                  <option value="guest_complaint_template">Guest Complaint Template</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">
                  Choose a template to auto-fill violation details
                </p>
              </div>

              <!-- Violation Type -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Violation Type*
                </label>
                <select name="case_type" id="caseType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                  <option value="">Select violation type</option>
                  <option value="theft" {{ old('case_type') == 'theft' ? 'selected' : '' }}>Theft / Stealing</option>
                  <option value="hr_policy_violation" {{ old('case_type') == 'hr_policy_violation' ? 'selected' : '' }}>HR Policy Violation</option>
                  <option value="workplace_harassment" {{ old('case_type') == 'workplace_harassment' ? 'selected' : '' }}>Workplace Harassment</option>
                  <option value="fraud" {{ old('case_type') == 'fraud' ? 'selected' : '' }}>Fraud / Misrepresentation</option>
                  <option value="safety_violation" {{ old('case_type') == 'safety_violation' ? 'selected' : '' }}>Safety Violation</option>
                  <option value="insubordination" {{ old('case_type') == 'insubordination' ? 'selected' : '' }}>Insubordination</option>
                  <option value="attendance_violation" {{ old('case_type') == 'attendance_violation' ? 'selected' : '' }}>Attendance Violation</option>
                  <option value="confidentiality_breach" {{ old('case_type') == 'confidentiality_breach' ? 'selected' : '' }}>Confidentiality Breach</option>
                  <option value="property_damage" {{ old('case_type') == 'property_damage' ? 'selected' : '' }}>Property Damage</option>
                  <option value="guest_complaint" {{ old('case_type') == 'guest_complaint' ? 'selected' : '' }}>Guest Complaint</option>
                  <option value="regulatory_violation" {{ old('case_type') == 'regulatory_violation' ? 'selected' : '' }}>Regulatory Violation</option>
                  <option value="other" {{ old('case_type') == 'other' ? 'selected' : '' }}>Other Violation</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">
                  Select the type of violation or compliance issue
                </p>
              </div>

              <!-- Priority -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Priority*
                </label>
                <select name="priority" id="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                  <option value="">Select priority</option>
                  <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                  <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                  <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                  <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">
                  Set the priority level for this case
                </p>
              </div>
            </div>
          </div>

          <!-- Violation Details Section -->
          <div class="bg-gray-50 rounded-lg p-6">
            <div class="flex items-center gap-2 mb-4">
              <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-600"></i>
              <h3 class="text-lg font-semibold text-gray-800">Violation Details</h3>
            </div>
            
            <div class="space-y-6">
              <!-- Violation Description -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Violation Description*
                </label>
                <textarea name="case_description" id="caseDescription" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                          rows="4" placeholder="Describe the violation in detail..." required>{{ old('case_description') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">
                  Provide detailed description of the violation, including what happened, when, where, and who was involved
                </p>
              </div>
            </div>
          </div>

          <!-- Incident Information Section -->
          <div class="bg-gray-50 rounded-lg p-6">
            <div class="flex items-center gap-2 mb-4">
              <i data-lucide="map-pin" class="w-5 h-5 text-green-600"></i>
              <h3 class="text-lg font-semibold text-gray-800">Incident Information</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Employee Involved -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Employee Involved
                </label>
                <input type="text" name="employee_involved" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       value="{{ old('employee_involved') }}" placeholder="Enter employee name or ID">
                <p class="mt-1 text-sm text-gray-500">
                  Name or employee ID of the person involved in the violation
                </p>
              </div>

              <!-- Incident Date -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Incident Date
                </label>
                <input type="datetime-local" name="incident_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       value="{{ old('incident_date') }}">
                <p class="mt-1 text-sm text-gray-500">
                  When did the violation occur?
                </p>
              </div>

              <!-- Location -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Location
                </label>
                <input type="text" name="incident_location" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       value="{{ old('incident_location') }}" placeholder="e.g., Hotel Lobby, Restaurant, Room 205">
                <p class="mt-1 text-sm text-gray-500">
                  Where did the violation occur?
                </p>
              </div>

              <!-- Assigned To -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Assigned To
                </label>
                <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  <option value="">Select assignee</option>
                  <option value="legal_team" {{ old('assigned_to') == 'legal_team' ? 'selected' : '' }}>Legal Team</option>
                  <option value="hr_team" {{ old('assigned_to') == 'hr_team' ? 'selected' : '' }}>HR Team</option>
                  <option value="security_team" {{ old('assigned_to') == 'security_team' ? 'selected' : '' }}>Security Team</option>
                  <option value="management" {{ old('assigned_to') == 'management' ? 'selected' : '' }}>Management</option>
                  <option value="external_counsel" {{ old('assigned_to') == 'external_counsel' ? 'selected' : '' }}>External Counsel</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">
                  Assign the case to the appropriate team
                </p>
              </div>
            </div>
          </div>
        </div>
              
        <!-- Submit Button -->
        <div class="mt-8 pt-6 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">
              <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
              All required fields must be completed before submission
            </div>
            <div class="flex gap-3">
              <button type="button" onclick="closeAddCaseModal()" class="btn btn-ghost">
                Cancel
              </button>
              <button type="submit" class="btn btn-primary bg-red-600 hover:bg-red-700 text-white">
                <i data-lucide="shield-alert" class="w-5 h-5 mr-2"></i>
                Report Violation
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>


  @include('partials.soliera_js')
  
  <style>
    /* Ensure modal is properly centered */
    .modal {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .modal-box {
      margin: auto;
      max-height: 90vh;
      overflow-y: auto;
    }
  </style>
  
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
        if (!row) return; // Null check
        
        let showRow = true;
        
        // Search filter
        if (searchTerm) {
          const titleElement = row.querySelector('td:first-child h4');
          const descriptionElement = row.querySelector('td:first-child p');
          const title = titleElement?.textContent?.toLowerCase() || '';
          const description = descriptionElement?.textContent?.toLowerCase() || '';
          if (!title.includes(searchTerm) && !description.includes(searchTerm)) {
            showRow = false;
          }
        }
        
        // Priority filter
        if (priorityFilter && showRow) {
          const priorityElement = row.querySelector('td:nth-child(2) .badge');
          const priority = priorityElement?.textContent?.toLowerCase() || '';
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
        if (row) { // Null check
          row.style.display = '';
        }
      });
    }
    
    // Case actions
    function deleteCase(caseId) {
      Swal.fire({
        title: 'Delete Legal Case',
        text: 'Are you sure you want to delete this legal case? This action cannot be undone and will permanently remove the case from the system.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'DELETE CASE',
        cancelButtonText: 'CANCEL',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        focusCancel: true
      }).then((result) => {
        if (result.isConfirmed) {
          // Proceed with deletion
          fetch(`/legal/cases/${caseId}`, {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showEnhancedToast('Legal case deleted successfully!', 'success', 'check-circle', 'The case has been permanently removed from the system.');
              // Remove the row from the table
              const row = document.querySelector(`tr[data-case-id="${caseId}"]`);
              if (row) {
                row.remove();
              }
            } else {
              showEnhancedToast('Error deleting case: ' + (data.message || 'Unknown error'), 'error', 'alert-circle', 'Please try again or contact support if the issue persists.');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showEnhancedToast('Error deleting case: ' + error.message, 'error', 'alert-circle', 'Please try again or contact support if the issue persists.');
          });
        }
      });
    }



    // Approve a legal case
    function approveCase(caseId) {
      Swal.fire({
        title: 'Approve Legal Case',
        text: 'Are you sure you want to approve this legal case? This action will mark the case as completed and cannot be undone.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'APPROVE CASE',
        cancelButtonText: 'CANCEL',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        focusCancel: true
      }).then((result) => {
        if (result.isConfirmed) {
          // Proceed with approval
          fetch(`/legal/cases/${caseId}/approve`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showEnhancedToast('Legal case approved successfully!', 'success', 'check-circle', 'The case has been marked as completed.');
              // Update the row status in the table
              const row = document.querySelector(`tr[data-case-id="${caseId}"]`);
              if (row) {
                const statusBadge = row.querySelector('.status-badge');
                if (statusBadge) {
                  statusBadge.className = 'badge badge-success status-badge';
                  statusBadge.innerHTML = '<i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>Approved';
                  lucide.createIcons();
                }
              }
            } else {
              showEnhancedToast('Error approving case: ' + (data.message || 'Unknown error'), 'error', 'alert-circle', 'Please try again or contact support if the issue persists.');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showEnhancedToast('Error approving case: ' + error.message, 'error', 'alert-circle', 'Please try again or contact support if the issue persists.');
          });
        }
      });
    }



    // Decline a legal case
    function declineCase(caseId) {
      Swal.fire({
        title: 'Decline Legal Case',
        text: 'Are you sure you want to decline this legal case? This action will mark the case as rejected and cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'DECLINE CASE',
        cancelButtonText: 'CANCEL',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        focusCancel: true
      }).then((result) => {
        if (result.isConfirmed) {
          // Proceed with decline
          fetch(`/legal/cases/${caseId}/decline`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showEnhancedToast('Legal case declined successfully!', 'success', 'check-circle', 'The case has been marked as rejected.');
              // Update the row status in the table
              const row = document.querySelector(`tr[data-case-id="${caseId}"]`);
              if (row) {
                const statusBadge = row.querySelector('.status-badge');
                if (statusBadge) {
                  statusBadge.className = 'badge badge-error status-badge';
                  statusBadge.innerHTML = '<i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>Declined';
                  lucide.createIcons();
                }
              }
            } else {
              showEnhancedToast('Error declining case: ' + (data.message || 'Unknown error'), 'error', 'alert-circle', 'Please try again or contact support if the issue persists.');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showEnhancedToast('Error declining case: ' + error.message, 'error', 'alert-circle', 'Please try again or contact support if the issue persists.');
          });
        }
      });
    }



    // Violation Templates
    const violationTemplates = {
      theft_template: {
        title: 'Theft Incident Report',
        description: 'INCIDENT REPORT - THEFT\n\nDate of Incident: [DATE]\nTime of Incident: [TIME]\nLocation: [LOCATION]\n\nDescription:\nA theft incident was reported involving [EMPLOYEE_NAME]. The incident occurred when [DETAILED_DESCRIPTION].\n\nItems Stolen:\n- [ITEM_1]\n- [ITEM_2]\n\nWitnesses:\n- [WITNESS_1]\n- [WITNESS_2]\n\nAction Taken:\n- Security was notified immediately\n- CCTV footage was reviewed\n- Police report filed (if applicable)\n\nRecommendations:\n- [RECOMMENDATION_1]\n- [RECOMMENDATION_2]',
        type: 'theft',
        priority: 'high'
      },
      hr_policy_template: {
        title: 'HR Policy Violation Report',
        description: 'HR POLICY VIOLATION REPORT\n\nEmployee: [EMPLOYEE_NAME]\nDepartment: [DEPARTMENT]\nPosition: [POSITION]\n\nViolation Details:\nThe employee has violated the following HR policy: [POLICY_NAME]\n\nSpecific Violation:\n[VIOLATION_DETAILS]\n\nPolicy Reference:\n[POLICY_REFERENCE]\n\nPrevious Violations:\n- [PREVIOUS_VIOLATION_1]\n- [PREVIOUS_VIOLATION_2]\n\nRecommended Action:\n- [ACTION_1]\n- [ACTION_2]\n\nHR Representative: [HR_REP_NAME]\nDate: [DATE]',
        type: 'hr_policy_violation',
        priority: 'normal'
      },
      harassment_template: {
        title: 'Workplace Harassment Complaint',
        description: 'WORKPLACE HARASSMENT COMPLAINT\n\nComplainant: [COMPLAINANT_NAME]\nAccused: [ACCUSED_NAME]\nDate of Incident: [DATE]\nLocation: [LOCATION]\n\nNature of Harassment:\n[DESCRIPTION_OF_HARASSMENT]\n\nWitnesses:\n- [WITNESS_1]\n- [WITNESS_2]\n\nEvidence:\n- [EVIDENCE_1]\n- [EVIDENCE_2]\n\nImmediate Actions Taken:\n- [ACTION_1]\n- [ACTION_2]\n\nInvestigation Required: YES/NO\n\nHR Representative: [HR_REP_NAME]\nDate: [DATE]',
        type: 'workplace_harassment',
        priority: 'urgent'
      },
      safety_template: {
        title: 'Safety Violation Report',
        description: 'SAFETY VIOLATION REPORT\n\nEmployee: [EMPLOYEE_NAME]\nDepartment: [DEPARTMENT]\nDate: [DATE]\nTime: [TIME]\nLocation: [LOCATION]\n\nSafety Violation:\n[VIOLATION_DESCRIPTION]\n\nPotential Hazards:\n- [HAZARD_1]\n- [HAZARD_2]\n\nImmediate Actions Taken:\n- [ACTION_1]\n- [ACTION_2]\n\nCorrective Measures:\n- [MEASURE_1]\n- [MEASURE_2]\n\nSafety Officer: [SAFETY_OFFICER]\nDate: [DATE]',
        type: 'safety_violation',
        priority: 'high'
      },
      attendance_template: {
        title: 'Attendance Violation Report',
        description: 'ATTENDANCE VIOLATION REPORT\n\nEmployee: [EMPLOYEE_NAME]\nEmployee ID: [EMPLOYEE_ID]\nDepartment: [DEPARTMENT]\n\nViolation Type:\n- [ ] Late arrival\n- [ ] Early departure\n- [ ] Unauthorized absence\n- [ ] Excessive breaks\n\nDetails:\n[VIOLATION_DETAILS]\n\nPrevious Violations:\n- [PREVIOUS_1]\n- [PREVIOUS_2]\n\nCorrective Action:\n- [ACTION_1]\n- [ACTION_2]\n\nSupervisor: [SUPERVISOR_NAME]\nDate: [DATE]',
        type: 'attendance_violation',
        priority: 'normal'
      },
      confidentiality_template: {
        title: 'Confidentiality Breach Report',
        description: 'CONFIDENTIALITY BREACH REPORT\n\nEmployee: [EMPLOYEE_NAME]\nDepartment: [DEPARTMENT]\nDate: [DATE]\n\nBreach Details:\n[BREACH_DESCRIPTION]\n\nConfidential Information Involved:\n- [INFO_1]\n- [INFO_2]\n\nPotential Impact:\n[IMPACT_ASSESSMENT]\n\nImmediate Actions:\n- [ACTION_1]\n- [ACTION_2]\n\nPrevention Measures:\n- [MEASURE_1]\n- [MEASURE_2]\n\nHR Representative: [HR_REP_NAME]\nDate: [DATE]',
        type: 'confidentiality_breach',
        priority: 'high'
      },
      property_damage_template: {
        title: 'Property Damage Report',
        description: 'PROPERTY DAMAGE REPORT\n\nDate: [DATE]\nTime: [TIME]\nLocation: [LOCATION]\n\nDamage Description:\n[DAMAGE_DESCRIPTION]\n\nEstimated Cost: [COST]\n\nCause:\n- [ ] Accidental\n- [ ] Intentional\n- [ ] Negligence\n- [ ] Other: [OTHER_CAUSE]\n\nWitnesses:\n- [WITNESS_1]\n- [WITNESS_2]\n\nActions Taken:\n- [ACTION_1]\n- [ACTION_2]\n\nInsurance Claim: YES/NO\n\nReported by: [REPORTER_NAME]\nDate: [DATE]',
        type: 'property_damage',
        priority: 'normal'
      },
      guest_complaint_template: {
        title: 'Guest Complaint - Legal Action Required',
        description: 'GUEST COMPLAINT - LEGAL ACTION REQUIRED\n\nGuest Name: [GUEST_NAME]\nRoom Number: [ROOM_NUMBER]\nCheck-in Date: [CHECKIN_DATE]\nCheck-out Date: [CHECKOUT_DATE]\n\nComplaint Details:\n[COMPLAINT_DESCRIPTION]\n\nStaff Involved:\n- [STAFF_1]\n- [STAFF_2]\n\nEvidence:\n- [EVIDENCE_1]\n- [EVIDENCE_2]\n\nGuest Demands:\n[GUEST_DEMANDS]\n\nLegal Implications:\n[LEGAL_IMPLICATIONS]\n\nRecommended Action:\n- [ACTION_1]\n- [ACTION_2]\n\nManager: [MANAGER_NAME]\nDate: [DATE]',
        type: 'guest_complaint',
        priority: 'high'
      }
    };

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Violation template handler
      const templateSelect = document.getElementById('violationTemplate');
      if (templateSelect) {
        templateSelect.addEventListener('change', function() {
          const template = violationTemplates[this.value];
          if (template) {
            document.getElementById('caseTitle').value = template.title;
            document.getElementById('caseDescription').value = template.description;
            document.getElementById('caseType').value = template.type;
            document.getElementById('priority').value = template.priority;
          }
        });
      }

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
        if (toast && toast.classList) {
          toast.classList.remove('translate-x-full', 'opacity-0');
          toast.classList.add('translate-x-0', 'opacity-100');
        }
      }, 100);
      
      // Auto remove after duration
      const duration = type === 'error' ? 6000 : 4000;
      setTimeout(() => {
        if (toast && toast.parentNode && toast.classList) {
          toast.classList.add('translate-x-full', 'opacity-0');
          setTimeout(() => {
            if (toast && toast.parentNode) {
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
      if (modal && modal.classList) {
        modal.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
        
        // Initialize Lucide icons in modal
        lucide.createIcons();
      }
    }

    function closeAddCaseModal() {
      const modal = document.getElementById('addCaseModal');
      if (modal && modal.classList) {
        modal.classList.remove('modal-open');
        document.body.style.overflow = 'auto';
      }
      
      // Reset form
      const form = document.getElementById('addCaseForm');
      if (form) {
        form.reset();
      }
      
      // Hide file preview and AI analysis
      const filePreview = document.getElementById('filePreview');
      const aiAnalysis = document.getElementById('aiAnalysis');
      if (filePreview && filePreview.classList) {
        filePreview.classList.add('hidden');
      }
      if (aiAnalysis && aiAnalysis.classList) {
        aiAnalysis.classList.add('hidden');
      }
    }




    // Form validation and submission
    function handleFormSubmission() {
      const form = document.getElementById('addCaseForm');
      const formData = new FormData(form);
      const requiredFields = ['case_title', 'case_type', 'priority', 'case_description'];
      
      // Validate required fields
      let isValid = true;
      requiredFields.forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field && field.classList) {
          if (!field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;
          } else {
            field.classList.remove('border-red-500');
          }
        }
      });
      
      if (!isValid) {
        showToast('Please fill in all required fields', 'error');
        return;
      }
      
      // Show loading state
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="loading loading-spinner"></i> Submitting...';
      submitBtn.disabled = true;
      
      // Submit form
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
          closeAddCaseModal();
          setTimeout(() => window.location.reload(), 1000);
        } else {
          throw new Error(data.message || 'Failed to submit report');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('Error submitting report: ' + error.message, 'error');
      })
      .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      });
    }

    // Event listeners for modal
    document.addEventListener('DOMContentLoaded', function() {
      
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

      // Auto-hide session notifications after 5 seconds
      setTimeout(() => {
        document.querySelectorAll('.toast').forEach(toast => {
          if (toast) {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.5s ease-out';
            setTimeout(() => {
              if (toast.parentNode) {
                toast.remove();
              }
            }, 500);
          }
        });
      }, 5000);
    });
  </script>
</body>
</html>
