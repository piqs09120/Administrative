<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Legal Decision - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
  
  <style>
    :root {
      --color-regal-navy: #1e3a8a;
      --color-charcoal-ink: #1f2937;
      --color-snow-mist: #f3f4f6;
      --color-white: #ffffff;
      --color-modern-teal: #0d9488;
      --color-golden-ember: #d97706;
      --color-danger-red: #dc2626;
    }
    
    .decision-step {
      transition: all 0.3s ease;
    }
    
    .decision-step:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .decision-step.active {
      border-color: var(--color-regal-navy);
      background-color: color-mix(in srgb, var(--color-regal-navy), white 95%);
    }
    
    .decision-step.completed {
      border-color: var(--color-modern-teal);
      background-color: color-mix(in srgb, var(--color-modern-teal), white 95%);
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
        <!-- Back button and title -->
        <div class="flex items-center mb-6">
          <a href="{{ route('legal.cases.review', $case->id) }}" class="btn btn-ghost btn-sm mr-4" title="Back to Case Review">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
          </a>
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Legal Decision</h1>
            <p class="text-gray-600">Make informed legal decisions for case: {{ $case->case_title }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Decision Area -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Decision Workflow Steps -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-6 flex items-center">
                  <i data-lucide="workflow" class="w-5 h-5 text-blue-500 mr-2"></i>
                  Decision Workflow
                </h2>
                
                <div class="space-y-4">
                  <!-- Step 1: Evidence Review -->
                  <div class="decision-step card bg-base-100 shadow-sm border-2 completed">
                    <div class="card-body p-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                          </div>
                          <div>
                            <h4 class="font-semibold">Evidence Review</h4>
                            <p class="text-sm text-gray-600">Review all evidence and documentation</p>
                          </div>
                        </div>
                        <span class="badge badge-success">Completed</span>
                      </div>
                    </div>
                  </div>

                  <!-- Step 2: Legal Analysis -->
                  <div class="decision-step card bg-base-100 shadow-sm border-2 active">
                    <div class="card-body p-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <i data-lucide="search" class="w-4 h-4 text-blue-600"></i>
                          </div>
                          <div>
                            <h4 class="font-semibold">Legal Analysis</h4>
                            <p class="text-sm text-gray-600">Analyze legal implications and precedents</p>
                          </div>
                        </div>
                        <span class="badge badge-primary">In Progress</span>
                      </div>
                    </div>
                  </div>

                  <!-- Step 3: Risk Assessment -->
                  <div class="decision-step card bg-base-100 shadow-sm border-2">
                    <div class="card-body p-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-gray-600"></i>
                          </div>
                          <div>
                            <h4 class="font-semibold">Risk Assessment</h4>
                            <p class="text-sm text-gray-600">Evaluate potential risks and consequences</p>
                          </div>
                        </div>
                        <span class="badge badge-outline">Pending</span>
                      </div>
                    </div>
                  </div>

                  <!-- Step 4: Decision Making -->
                  <div class="decision-step card bg-base-100 shadow-sm border-2">
                    <div class="card-body p-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                            <i data-lucide="gavel" class="w-4 h-4 text-gray-600"></i>
                          </div>
                          <div>
                            <h4 class="font-semibold">Decision Making</h4>
                            <p class="text-sm text-gray-600">Make final legal decision</p>
                          </div>
                        </div>
                        <span class="badge badge-outline">Pending</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Legal Analysis Form -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-6 flex items-center">
                  <i data-lucide="file-text" class="w-5 h-5 text-purple-500 mr-2"></i>
                  Legal Analysis
                </h2>
                
                <form id="legalAnalysisForm">
                  @csrf
                  <div class="space-y-6">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Legal Issues Identified</label>
                      <textarea class="textarea textarea-bordered w-full" rows="3" placeholder="List all legal issues identified in this case..."></textarea>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Applicable Laws & Regulations</label>
                      <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 border rounded-lg">
                          <input type="checkbox" class="checkbox" checked>
                          <div>
                            <div class="font-semibold">Labor Law (RA 11058)</div>
                            <div class="text-sm text-gray-600">Occupational Safety and Health Standards</div>
                          </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border rounded-lg">
                          <input type="checkbox" class="checkbox">
                          <div>
                            <div class="font-semibold">Data Privacy Act (RA 10173)</div>
                            <div class="text-sm text-gray-600">Protection of Personal Information</div>
                          </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border rounded-lg">
                          <input type="checkbox" class="checkbox">
                          <div>
                            <div class="font-semibold">Civil Code of the Philippines</div>
                            <div class="text-sm text-gray-600">General civil liability provisions</div>
                          </div>
                        </label>
                      </div>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Legal Precedents</label>
                      <textarea class="textarea textarea-bordered w-full" rows="3" placeholder="Reference similar cases or legal precedents..."></textarea>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Legal Risk Level</label>
                      <select class="select select-bordered w-full">
                        <option value="">Select risk level</option>
                        <option value="low">Low Risk</option>
                        <option value="medium">Medium Risk</option>
                        <option value="high">High Risk</option>
                        <option value="critical">Critical Risk</option>
                      </select>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Legal Recommendations</label>
                      <textarea class="textarea textarea-bordered w-full" rows="4" placeholder="Provide legal recommendations and next steps..."></textarea>
                    </div>
                  </div>
                  
                  <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn btn-outline">Save Draft</button>
                    <button type="submit" class="btn btn-primary">Complete Analysis</button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Decision Options -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-6 flex items-center">
                  <i data-lucide="target" class="w-5 h-5 text-red-500 mr-2"></i>
                  Decision Options
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Approve Option -->
                  <div class="card bg-green-50 border border-green-200">
                    <div class="card-body p-4">
                      <div class="flex items-center gap-3 mb-3">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                        <h4 class="font-semibold text-green-800">Approve Case</h4>
                      </div>
                      <p class="text-sm text-green-700 mb-4">Case meets all legal requirements and can proceed</p>
                      <button onclick="openApproveDecisionModal()" class="btn btn-success w-full">
                        <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                        Approve
                      </button>
                    </div>
                  </div>

                  <!-- Decline Option -->
                  <div class="card bg-red-50 border border-red-200">
                    <div class="card-body p-4">
                      <div class="flex items-center gap-3 mb-3">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                        <h4 class="font-semibold text-red-800">Decline Case</h4>
                      </div>
                      <p class="text-sm text-red-700 mb-4">Case has legal issues that cannot be resolved</p>
                      <button onclick="openDeclineDecisionModal()" class="btn btn-error w-full">
                        <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                        Decline
                      </button>
                    </div>
                  </div>

                  <!-- Escalate Option -->
                  <div class="card bg-yellow-50 border border-yellow-200">
                    <div class="card-body p-4">
                      <div class="flex items-center gap-3 mb-3">
                        <i data-lucide="arrow-up" class="w-6 h-6 text-yellow-600"></i>
                        <h4 class="font-semibold text-yellow-800">Escalate Case</h4>
                      </div>
                      <p class="text-sm text-yellow-700 mb-4">Case requires higher-level legal review</p>
                      <button onclick="openEscalateDecisionModal()" class="btn btn-warning w-full">
                        <i data-lucide="arrow-up" class="w-4 h-4 mr-2"></i>
                        Escalate
                      </button>
                    </div>
                  </div>

                  <!-- Hold Option -->
                  <div class="card bg-blue-50 border border-blue-200">
                    <div class="card-body p-4">
                      <div class="flex items-center gap-3 mb-3">
                        <i data-lucide="pause" class="w-6 h-6 text-blue-600"></i>
                        <h4 class="font-semibold text-blue-800">Put on Hold</h4>
                      </div>
                      <p class="text-sm text-blue-700 mb-4">Case needs additional information or time</p>
                      <button onclick="openHoldDecisionModal()" class="btn btn-info w-full">
                        <i data-lucide="pause" class="w-4 h-4 mr-2"></i>
                        Hold
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar - Decision Support -->
          <div class="space-y-6">
            <!-- Case Summary -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-6 flex items-center">
                  <i data-lucide="file-text" class="w-5 h-5 text-indigo-500 mr-2"></i>
                  Case Summary
                </h2>
                
                <div class="space-y-3">
                  <div>
                    <span class="text-sm text-gray-600">Case ID:</span>
                    <span class="font-semibold">{{ $case->case_number ?? 'N/A' }}</span>
                  </div>
                  <div>
                    <span class="text-sm text-gray-600">Type:</span>
                    <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $case->case_type ?? 'Unknown')) }}</span>
                  </div>
                  <div>
                    <span class="text-sm text-gray-600">Priority:</span>
                    <span class="badge {{ $case->priority === 'urgent' ? 'badge-error' : ($case->priority === 'high' ? 'badge-warning' : 'badge-info') }}">
                      {{ ucfirst($case->priority ?? 'Normal') }}
                    </span>
                  </div>
                  <div>
                    <span class="text-sm text-gray-600">Status:</span>
                    <span class="badge {{ $case->status === 'pending' ? 'badge-warning' : ($case->status === 'completed' ? 'badge-success' : 'badge-error') }}">
                      {{ ucfirst($case->status) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Legal Resources -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-6 flex items-center">
                  <i data-lucide="book-open" class="w-5 h-5 text-purple-500 mr-2"></i>
                  Legal Resources
                </h2>
                
                <div class="space-y-3">
                  <a href="#" class="btn btn-outline w-full justify-start">
                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                    Labor Code Reference
                  </a>
                  <a href="#" class="btn btn-outline w-full justify-start">
                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                    Data Privacy Guidelines
                  </a>
                  <a href="#" class="btn btn-outline w-full justify-start">
                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                    Legal Precedents Database
                  </a>
                  <a href="#" class="btn btn-outline w-full justify-start">
                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                    Compliance Checklist
                  </a>
                </div>
              </div>
            </div>

            <!-- Decision History -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-6 flex items-center">
                  <i data-lucide="history" class="w-5 h-5 text-orange-500 mr-2"></i>
                  Decision History
                </h2>
                
                <div class="space-y-3">
                  <div class="flex items-center gap-3 p-2 bg-gray-50 rounded">
                    <i data-lucide="user" class="w-4 h-4 text-gray-500"></i>
                    <div class="flex-1">
                      <div class="text-sm font-semibold">Case Created</div>
                      <div class="text-xs text-gray-500">{{ $case->created_at ? $case->created_at->format('M d, Y H:i') : 'N/A' }}</div>
                    </div>
                  </div>
                  
                  <div class="flex items-center gap-3 p-2 bg-gray-50 rounded">
                    <i data-lucide="search" class="w-4 h-4 text-gray-500"></i>
                    <div class="flex-1">
                      <div class="text-sm font-semibold">Investigation Started</div>
                      <div class="text-xs text-gray-500">Pending</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Approve Decision Modal -->
  <div id="approveDecisionModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-green-800">Approve Legal Case</h2>
        <button onclick="closeApproveDecisionModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form id="approveDecisionForm">
        @csrf
        <div class="space-y-6">
          <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center gap-3 mb-2">
              <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
              <h4 class="font-semibold text-green-800">Approval Confirmation</h4>
            </div>
            <p class="text-sm text-green-700">This case has been reviewed and meets all legal requirements for approval.</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Approval Comments</label>
            <textarea class="textarea textarea-bordered w-full" rows="3" placeholder="Add any comments about the approval decision..."></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Next Steps</label>
            <textarea class="textarea textarea-bordered w-full" rows="2" placeholder="Describe any follow-up actions required..."></textarea>
          </div>
        </div>
        
        <div class="modal-action">
          <button type="button" onclick="closeApproveDecisionModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-success">Approve Case</button>
        </div>
      </form>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Modal functions
    function openApproveDecisionModal() {
      document.getElementById('approveDecisionModal').classList.add('modal-open');
    }
    
    function closeApproveDecisionModal() {
      document.getElementById('approveDecisionModal').classList.remove('modal-open');
    }
    
    function openDeclineDecisionModal() {
      alert('Decline Decision modal - To be implemented');
    }
    
    function openEscalateDecisionModal() {
      alert('Escalate Decision modal - To be implemented');
    }
    
    function openHoldDecisionModal() {
      alert('Hold Decision modal - To be implemented');
    }
  </script>
</body>
</html>
