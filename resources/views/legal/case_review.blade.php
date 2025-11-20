<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Legal Case Review - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
  
  <style>
    :root {
      --color-regal-navy: #F7A923;
      --color-charcoal-ink: #2C3E50;
      --color-snow-mist: #f3f4f6;
      --color-white: #ffffff;
      --color-modern-teal: #0d9488;
      --color-golden-ember: #E6940F;
      --color-danger-red: #dc2626;
      --color-button-secondary: #E6940F;
    }
    
    /* Force button primary to use orange-yellow */
    .btn.btn-primary {
      background-color: #F7A923 !important;
      border-color: #F7A923 !important;
      color: #2C3E50 !important;
    }
    
    .btn.btn-primary:hover {
      background-color: #E6940F !important;
      border-color: #E6940F !important;
    }
    
    
    .evidence-card {
      transition: all 0.2s ease;
    }
    
    .evidence-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
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

        <!-- Back button and title -->
        <div class="flex items-center mb-6">
          <a href="{{ route('legal.case_deck') }}" class="btn btn-ghost btn-sm mr-4" title="Back to Legal Cases">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
          </a>
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Legal Case Review</h1>
            <p class="text-gray-600">Comprehensive review and investigation of legal cases</p>
          </div>
        </div>

        <div class="w-full">
          <!-- Main Content - Case Details -->
          <div class="space-y-6">
            <!-- Case Overview -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <div class="flex items-center justify-between mb-6">
                  <h2 class="card-title text-xl flex items-center">
                    <i data-lucide="scale" class="w-5 h-5 text-blue-500 mr-2" style="color: var(--color-regal-navy);"></i>
                    Case Overview
                  </h2>
                  <div class="flex items-center gap-2">
                    <span class="badge badge-outline">{{ $case->case_number ?? 'N/A' }}</span>
                    <span class="badge {{ $case->status === 'pending' ? 'badge-warning' : ($case->status === 'completed' ? 'badge-success' : 'badge-error') }}">
                      {{ ucfirst($case->status) }}
                    </span>
                  </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Case Title</h3>
                    <p class="text-gray-900">{{ $case->case_title ?? 'Untitled Case' }}</p>
                  </div>
                  
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Violation Type</h3>
                    <span class="badge badge-outline">
                      {{ ucfirst(str_replace('_', ' ', $case->case_type ?? 'Unknown')) }}
                    </span>
                  </div>
                  
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Employee Involved</h3>
                    <p class="text-gray-900">{{ $case->employee_involved ?? 'N/A' }}</p>
                  </div>
                  
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Incident Date</h3>
                    <p class="text-gray-900">
                      @if($case->incident_date)
                        {{ \Carbon\Carbon::parse($case->incident_date)->format('M d, Y H:i') }}
                      @else
                        {{ $case->created_at ? $case->created_at->format('M d, Y') : 'N/A' }}
                      @endif
                    </p>
                  </div>
                  
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Location</h3>
                    <p class="text-gray-900">{{ $case->incident_location ?? 'N/A' }}</p>
                  </div>
                  
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Priority</h3>
                    <span class="badge {{ $case->priority === 'urgent' ? 'badge-error' : ($case->priority === 'high' ? 'badge-warning' : 'badge-info') }}">
                      {{ ucfirst($case->priority ?? 'Normal') }}
                    </span>
                  </div>
                </div>
                
                <div class="mt-6">
                  <h3 class="font-semibold text-gray-700 mb-2">Case Description</h3>
                  <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $case->case_description ?? 'No description provided' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Case Actions -->
            <div class="card bg-white shadow-md">
              <div class="card-body">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                  <div class="flex flex-wrap items-center gap-3">
                    <div class="badge {{ $case->status === 'pending' ? 'badge-warning' : ($case->status === 'completed' ? 'badge-success' : 'badge-error') }} px-4 py-2 text-sm font-semibold">
                      Status: {{ ucfirst($case->status) }}
                    </div>
                    @if($case->case_type === 'facility_damage')
                      <div class="badge badge-outline px-4 py-2 flex items-center gap-2 text-sm">
                        <i data-lucide="building" class="w-4 h-4"></i>
                        Facility Damage Case
                      </div>
                    @endif
                  </div>

                  @if($case->status === 'pending')
                    <div class="flex flex-wrap gap-2">
                      <button onclick="openApproveModal()" class="btn btn-success btn-sm lg:btn-md flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Approve
                      </button>
                      <button onclick="openDeclineModal()" class="btn btn-error btn-sm lg:btn-md flex items-center gap-2">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        Decline
                      </button>
                      <button onclick="openEscalateModal()" class="btn btn-warning btn-sm lg:btn-md flex items-center gap-2">
                        <i data-lucide="arrow-up-circle" class="w-4 h-4"></i>
                        Escalate
                      </button>
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <!-- Evidence & Documents -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <div class="flex items-center justify-between mb-6">
                  <h2 class="card-title text-xl flex items-center">
                    <i data-lucide="folder-open" class="w-5 h-5 text-green-500 mr-2" style="color: var(--color-modern-teal);"></i>
                    Evidence & Documents
                  </h2>
                  <button onclick="openAddEvidenceModal()" class="btn btn-sm btn-primary">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Add Evidence
                  </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4" id="evidenceGrid">
                  <!-- Sample Evidence Cards -->
                  <div class="evidence-card card bg-base-100 shadow-md border">
                    <div class="card-body p-4">
                      <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="file-text" class="w-8 h-8 text-blue-500"></i>
                        <div class="flex-1">
                          <h4 class="font-semibold text-sm">Incident Report</h4>
                          <p class="text-xs text-gray-500">PDF • 2.3 MB</p>
                        </div>
                      </div>
                      <div class="flex gap-2">
                        <button class="btn btn-xs btn-outline">View</button>
                        <button class="btn btn-xs btn-primary">Download</button>
                      </div>
                    </div>
                  </div>
                  
                  <div class="evidence-card card bg-base-100 shadow-md border">
                    <div class="card-body p-4">
                      <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="camera" class="w-8 h-8 text-green-500"></i>
                        <div class="flex-1">
                          <h4 class="font-semibold text-sm">CCTV Footage</h4>
                          <p class="text-xs text-gray-500">MP4 • 15.2 MB</p>
                        </div>
                      </div>
                      <div class="flex gap-2">
                        <button class="btn btn-xs btn-outline">View</button>
                        <button class="btn btn-xs btn-primary">Download</button>
                      </div>
                    </div>
                  </div>
                  
                  <div class="evidence-card card bg-base-100 shadow-md border">
                    <div class="card-body p-4">
                      <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="users" class="w-8 h-8 text-purple-500"></i>
                        <div class="flex-1">
                          <h4 class="font-semibold text-sm">Witness Statements</h4>
                          <p class="text-xs text-gray-500">DOCX • 1.8 MB</p>
                        </div>
                      </div>
                      <div class="flex gap-2">
                        <button class="btn btn-xs btn-outline">View</button>
                        <button class="btn btn-xs btn-primary">Download</button>
                      </div>
                    </div>
                  </div>
                  
                  <div class="evidence-card card bg-base-100 shadow-md border">
                    <div class="card-body p-4">
                      <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="file-check" class="w-8 h-8 text-orange-500"></i>
                        <div class="flex-1">
                          <h4 class="font-semibold text-sm">Legal Documents</h4>
                          <p class="text-xs text-gray-500">PDF • 3.2 MB</p>
                        </div>
                      </div>
                      <div class="flex gap-2">
                        <button class="btn btn-xs btn-outline">View</button>
                        <button class="btn btn-xs btn-primary">Download</button>
                      </div>
                    </div>
                  </div>
                  
                  <div class="evidence-card card bg-base-100 shadow-md border">
                    <div class="card-body p-4">
                      <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="phone" class="w-8 h-8 text-red-500"></i>
                        <div class="flex-1">
                          <h4 class="font-semibold text-sm">Audio Recording</h4>
                          <p class="text-xs text-gray-500">MP3 • 4.1 MB</p>
                        </div>
                      </div>
                      <div class="flex gap-2">
                        <button class="btn btn-xs btn-outline">View</button>
                        <button class="btn btn-xs btn-primary">Download</button>
                      </div>
                    </div>
                  </div>
                  
                  <div class="evidence-card card bg-base-100 shadow-md border">
                    <div class="card-body p-4">
                      <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="mail" class="w-8 h-8 text-indigo-500"></i>
                        <div class="flex-1">
                          <h4 class="font-semibold text-sm">Email Evidence</h4>
                          <p class="text-xs text-gray-500">EML • 0.8 MB</p>
                        </div>
                      </div>
                      <div class="flex gap-2">
                        <button class="btn btn-xs btn-outline">View</button>
                        <button class="btn btn-xs btn-primary">Download</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>


  <!-- Investigation Modal -->
  <div id="investigationModal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Start Investigation</h2>
        <button onclick="closeInvestigationModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form id="investigationForm">
        @csrf
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Investigation Type</label>
            <select class="select select-bordered w-full" required>
              <option value="">Select investigation type</option>
              <option value="internal">Internal Investigation</option>
              <option value="external">External Investigation</option>
              <option value="joint">Joint Investigation</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Investigation Scope</label>
            <textarea class="textarea textarea-bordered w-full" rows="3" placeholder="Describe the scope of investigation..."></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Assigned Investigators</label>
            <select class="select select-bordered w-full" multiple>
              <option value="hr_team">HR Team</option>
              <option value="security_team">Security Team</option>
              <option value="legal_team">Legal Team</option>
              <option value="management">Management</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Expected Completion Date</label>
            <input type="date" class="input input-bordered w-full">
          </div>
        </div>
        
        <div class="modal-action">
          <button type="button" onclick="closeInvestigationModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-primary">Start Investigation</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Evidence Modal -->
  <div id="addEvidenceModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Add Evidence</h2>
        <button onclick="closeAddEvidenceModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form id="addEvidenceForm" enctype="multipart/form-data">
        @csrf
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Evidence Type</label>
            <select class="select select-bordered w-full" required>
              <option value="">Select evidence type</option>
              <option value="document">Document</option>
              <option value="photo">Photo</option>
              <option value="video">Video</option>
              <option value="audio">Audio Recording</option>
              <option value="witness_statement">Witness Statement</option>
              <option value="cctv">CCTV Footage</option>
              <option value="other">Other</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Evidence Description</label>
            <textarea class="textarea textarea-bordered w-full" rows="3" placeholder="Describe the evidence..."></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
            <input type="file" class="file-input file-input-bordered w-full" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp4,.mp3,.wav">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Evidence Date</label>
            <input type="datetime-local" class="input input-bordered w-full">
          </div>
        </div>
        
        <div class="modal-action">
          <button type="button" onclick="closeAddEvidenceModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Evidence</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Legal Notes Modal -->
  <div id="legalNotesModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Add Legal Notes</h2>
        <button onclick="closeLegalNotesModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form action="{{ route('legal.cases.notes', $case->id) }}" method="POST">
        @csrf
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Note Type</label>
            <select name="note_type" class="select select-bordered w-full" required>
              <option value="">Select note type</option>
              <option value="investigation">Investigation</option>
              <option value="legal">Legal Analysis</option>
              <option value="compliance">Compliance</option>
              <option value="general">General</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
            <select name="note_priority" class="select select-bordered w-full" required>
              <option value="">Select priority</option>
              <option value="low">Low</option>
              <option value="normal">Normal</option>
              <option value="high">High</option>
              <option value="urgent">Urgent</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Note Content</label>
            <textarea name="note_content" class="textarea textarea-bordered w-full" rows="6" placeholder="Enter your legal notes..." required></textarea>
          </div>
        </div>
        
        <div class="modal-action">
          <button type="button" onclick="closeLegalNotesModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Note</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Witness Interview Modal -->
  <div id="witnessModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Interview Witnesses</h2>
        <button onclick="closeWitnessModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form>
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Witness Name</label>
            <input type="text" class="input input-bordered w-full" placeholder="Enter witness name">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Information</label>
            <input type="text" class="input input-bordered w-full" placeholder="Phone number or email">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Interview Date</label>
            <input type="datetime-local" class="input input-bordered w-full">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Interview Notes</label>
            <textarea class="textarea textarea-bordered w-full" rows="4" placeholder="Record witness testimony..."></textarea>
          </div>
        </div>
        
        <div class="modal-action">
          <button type="button" onclick="closeWitnessModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-primary">Schedule Interview</button>
        </div>
      </form>
    </div>
  </div>

  <!-- External Counsel Modal -->
  <div id="externalCounselModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Consult External Counsel</h2>
        <button onclick="closeExternalCounselModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form>
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Law Firm</label>
            <input type="text" class="input input-bordered w-full" placeholder="Enter law firm name">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Attorney Name</label>
            <input type="text" class="input input-bordered w-full" placeholder="Enter attorney name">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Information</label>
            <input type="text" class="input input-bordered w-full" placeholder="Phone number or email">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Consultation Type</label>
            <select class="select select-bordered w-full">
              <option value="">Select consultation type</option>
              <option value="legal_advice">Legal Advice</option>
              <option value="case_review">Case Review</option>
              <option value="compliance_check">Compliance Check</option>
              <option value="litigation_support">Litigation Support</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Consultation Notes</label>
            <textarea class="textarea textarea-bordered w-full" rows="4" placeholder="Record consultation details..."></textarea>
          </div>
        </div>
        
        <div class="modal-action">
          <button type="button" onclick="closeExternalCounselModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-primary">Schedule Consultation</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Approve Case Modal -->
  <div id="approveModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-green-800">Approve Legal Case</h2>
        <button onclick="closeApproveModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form action="{{ route('legal.cases.approve', $case->id) }}" method="POST">
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
            <textarea name="approval_comments" class="textarea textarea-bordered w-full" rows="3" placeholder="Add any comments about the approval decision..."></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Next Steps</label>
            <textarea name="next_steps" class="textarea textarea-bordered w-full" rows="2" placeholder="Describe any follow-up actions required..."></textarea>
          </div>
        </div>
        
        <div class="modal-action">
          <button type="button" onclick="closeApproveModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-success">Approve Case</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Decline Case Modal -->
  <div id="declineModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-red-800">Decline Legal Case</h2>
        <button onclick="closeDeclineModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form action="{{ route('legal.cases.decline', $case->id) }}" method="POST">
        @csrf
        <div class="space-y-6">
          <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center gap-3 mb-2">
              <i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>
              <h4 class="font-semibold text-red-800">Decline Confirmation</h4>
            </div>
            <p class="text-sm text-red-700">This case does not meet the legal requirements and will be declined.</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Decline</label>
            <select name="decline_reason" class="select select-bordered w-full" required>
              <option value="">Select reason</option>
              <option value="insufficient_evidence">Insufficient Evidence</option>
              <option value="no_legal_basis">No Legal Basis</option>
              <option value="outside_jurisdiction">Outside Jurisdiction</option>
              <option value="duplicate_case">Duplicate Case</option>
              <option value="other">Other</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Decline Comments</label>
            <textarea name="decline_comments" class="textarea textarea-bordered w-full" rows="3" placeholder="Provide detailed explanation for declining this case..." required></textarea>
          </div>
        </div>
        
        <div class="modal-action">
          <button type="button" onclick="closeDeclineModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-error">Decline Case</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Escalate Case Modal -->
  <div id="escalateModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-orange-800">Escalate Legal Case</h2>
        <button onclick="closeEscalateModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form action="{{ route('legal.cases.escalate', $case->id) }}" method="POST">
        @csrf
        <div class="space-y-6">
          <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <div class="flex items-center gap-3 mb-2">
              <i data-lucide="arrow-up" class="w-5 h-5 text-orange-600"></i>
              <h4 class="font-semibold text-orange-800">Escalation Confirmation</h4>
            </div>
            <p class="text-sm text-orange-700">This case requires higher-level review and will be escalated.</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Escalate To</label>
            <select name="escalate_to" class="select select-bordered w-full" required>
              <option value="">Select escalation level</option>
              <option value="senior_legal">Senior Legal Counsel</option>
              <option value="management">Management</option>
              <option value="external_counsel">External Counsel</option>
              <option value="regulatory_body">Regulatory Body</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Escalation Reason</label>
            <select name="escalation_reason" class="select select-bordered w-full" required>
              <option value="">Select reason</option>
              <option value="complex_legal_issues">Complex Legal Issues</option>
              <option value="high_risk">High Risk Case</option>
              <option value="regulatory_concern">Regulatory Concern</option>
              <option value="precedent_setting">Precedent Setting</option>
              <option value="other">Other</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Escalation Notes</label>
            <textarea name="escalation_notes" class="textarea textarea-bordered w-full" rows="3" placeholder="Explain why this case needs to be escalated..." required></textarea>
          </div>
        </div>
        
        <div class="modal-action">
          <button type="button" onclick="closeEscalateModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-warning">Escalate Case</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Hold Case Modal -->
  <div id="holdModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-blue-800">Put Case on Hold</h2>
        <button onclick="closeHoldModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form action="{{ route('legal.cases.hold', $case->id) }}" method="POST">
        @csrf
        <div class="space-y-6">
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center gap-3 mb-2">
              <i data-lucide="pause" class="w-5 h-5 text-blue-600"></i>
              <h4 class="font-semibold text-blue-800">Hold Confirmation</h4>
            </div>
            <p class="text-sm text-blue-700">This case will be temporarily put on hold pending further information or action.</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Hold Reason</label>
            <select name="hold_reason" class="select select-bordered w-full" required>
              <option value="">Select reason</option>
              <option value="pending_investigation">Pending Investigation</option>
              <option value="awaiting_documents">Awaiting Documents</option>
              <option value="legal_review">Legal Review Required</option>
              <option value="external_consultation">External Consultation</option>
              <option value="other">Other</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Expected Resolution Date</label>
            <input type="date" name="expected_resolution_date" class="input input-bordered w-full" required>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Hold Notes</label>
            <textarea name="hold_notes" class="textarea textarea-bordered w-full" rows="3" placeholder="Explain why this case is being put on hold..." required></textarea>
          </div>
        </div>
        
        <div class="modal-action">
          <button type="button" onclick="closeHoldModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-info">Put on Hold</button>
        </div>
      </form>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Modal functions
    function openInvestigationModal() {
      document.getElementById('investigationModal').classList.add('modal-open');
    }
    
    function closeInvestigationModal() {
      document.getElementById('investigationModal').classList.remove('modal-open');
    }
    
    function openAddEvidenceModal() {
      document.getElementById('addEvidenceModal').classList.add('modal-open');
    }
    
    function closeAddEvidenceModal() {
      document.getElementById('addEvidenceModal').classList.remove('modal-open');
    }
    
    function openLegalNotesModal() {
      document.getElementById('legalNotesModal').classList.add('modal-open');
    }
    
    function closeLegalNotesModal() {
      document.getElementById('legalNotesModal').classList.remove('modal-open');
    }
    
    function openComplianceModal() {
      window.location.href = '{{ route("legal.cases.compliance", $case->id) }}';
    }
    
    function openWitnessModal() {
      document.getElementById('witnessModal').classList.add('modal-open');
    }
    
    function closeWitnessModal() {
      document.getElementById('witnessModal').classList.remove('modal-open');
    }
    
    function openExternalCounselModal() {
      document.getElementById('externalCounselModal').classList.add('modal-open');
    }
    
    function closeExternalCounselModal() {
      document.getElementById('externalCounselModal').classList.remove('modal-open');
    }
    
    function openApproveModal() {
      document.getElementById('approveModal').classList.add('modal-open');
    }
    
    function closeApproveModal() {
      document.getElementById('approveModal').classList.remove('modal-open');
    }
    
    function openDeclineModal() {
      document.getElementById('declineModal').classList.add('modal-open');
    }
    
    function closeDeclineModal() {
      document.getElementById('declineModal').classList.remove('modal-open');
    }
    
    function openEscalateModal() {
      document.getElementById('escalateModal').classList.add('modal-open');
    }
    
    function closeEscalateModal() {
      document.getElementById('escalateModal').classList.remove('modal-open');
    }
    
    function openHoldModal() {
      document.getElementById('holdModal').classList.add('modal-open');
    }
    
    function closeHoldModal() {
      document.getElementById('holdModal').classList.remove('modal-open');
    }

    // AJAX form submissions
    document.addEventListener('DOMContentLoaded', function() {
      // Handle all form submissions with AJAX
      const forms = document.querySelectorAll('form[action*="legal/cases"]');
      
      forms.forEach(form => {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          
          const formData = new FormData(this);
          const action = this.getAttribute('action');
          const method = this.getAttribute('method') || 'POST';
          
          // Show loading state
          const submitBtn = this.querySelector('button[type="submit"]');
          const originalText = submitBtn.textContent;
          submitBtn.textContent = 'Processing...';
          submitBtn.disabled = true;
          
          fetch(action, {
            method: method,
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Show success message
              showNotification(data.message, 'success');
              
              // Close modal
              const modal = this.closest('.modal');
              if (modal) {
                modal.classList.remove('modal-open');
              }
              
              // Reload page to show updated data
              setTimeout(() => {
                window.location.reload();
              }, 1500);
            } else {
              showNotification(data.message || 'An error occurred', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while processing the request', 'error');
          })
          .finally(() => {
            // Reset button state
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
          });
        });
      });
    });

    // Notification function
    function showNotification(message, type = 'info') {
      // Create notification element
      const notification = document.createElement('div');
      notification.className = `alert alert-${type} fixed top-4 right-4 z-50 max-w-sm`;
      notification.innerHTML = `
        <div class="flex items-center gap-2">
          <i data-lucide="${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info'}" class="w-5 h-5"></i>
          <span>${message}</span>
        </div>
      `;
      
      document.body.appendChild(notification);
      
      // Initialize Lucide icons for the notification
      lucide.createIcons();
      
      // Remove notification after 5 seconds
      setTimeout(() => {
        notification.remove();
      }, 5000);
    }
  </script>
</body>
</html>
