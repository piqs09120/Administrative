<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Case Review - {{ $case->case_title }} - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @vite(['resources/css/soliera.css'])
  
  <style>
    :root {
      --color-regal-navy: #F7A923;
      --color-charcoal-ink: #2C3E50;
      --color-filing: #3b82f6;
      --color-investigation: #f97316;
      --color-review: #a855f7;
      --color-resolution: #22c55e;
      --color-closed: #6b7280;
    }
    
    .btn.btn-primary {
      background-color: #F7A923 !important;
      border-color: #F7A923 !important;
      color: #2C3E50 !important;
    }
    
    .btn.btn-primary:hover {
      background-color: #E6940F !important;
      border-color: #E6940F !important;
    }
    
    /* Workflow Progress Bar */
    .workflow-progress {
      position: relative;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 2rem 0;
    }
    
    .workflow-step {
      position: relative;
      z-index: 10;
      display: flex;
      flex-direction: column;
      align-items: center;
      flex: 1;
    }
    
    .workflow-step-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 3px solid #e5e7eb;
      background: white;
      transition: all 0.3s ease;
    }
    
    .workflow-step.active .workflow-step-icon {
      background: var(--color-regal-navy);
      border-color: var(--color-regal-navy);
      box-shadow: 0 0 0 4px rgba(247, 169, 35, 0.2);
    }
    
    .workflow-step.completed .workflow-step-icon {
      background: #22c55e;
      border-color: #22c55e;
    }
    
    .workflow-step-label {
      margin-top: 0.5rem;
      font-size: 0.875rem;
      font-weight: 600;
      color: #6b7280;
    }
    
    .workflow-step.active .workflow-step-label {
      color: #1f2937;
    }
    
    .workflow-step.completed .workflow-step-label {
      color: #22c55e;
    }
    
    .workflow-line {
      position: absolute;
      top: 25px;
      left: 0;
      right: 0;
      height: 3px;
      background: #e5e7eb;
      z-index: 1;
    }
    
    .workflow-line-progress {
      height: 100%;
      background: #22c55e;
      transition: width 0.5s ease;
    }
    
    /* Tabs */
    .tab-content {
      display: none;
    }
    
    .tab-content.active {
      display: block;
    }
    
    .tab-btn {
      padding: 1rem 1.5rem;
      border-bottom: 3px solid transparent;
      color: #6b7280;
      font-weight: 600;
      transition: all 0.2s;
      cursor: pointer;
    }
    
    .tab-btn:hover {
      color: #1f2937;
      background: #f3f4f6;
    }
    
    .tab-btn.active {
      color: #F7A923;
      border-bottom-color: #F7A923;
      background: #fff;
    }
    
    /* Evidence Cards */
    .evidence-card {
      transition: all 0.2s ease;
    }
    
    .evidence-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    /* Timeline */
    .timeline-item {
      position: relative;
      padding-left: 2rem;
      padding-bottom: 2rem;
      border-left: 2px solid #e5e7eb;
    }
    
    .timeline-item:last-child {
      border-left-color: transparent;
      padding-bottom: 0;
    }
    
    .timeline-dot {
      position: absolute;
      left: -0.5rem;
      top: 0;
      width: 1rem;
      height: 1rem;
      border-radius: 50%;
      background: #F7A923;
      border: 3px solid white;
      box-shadow: 0 0 0 2px #e5e7eb;
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
        <!-- Back button and header -->
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center">
            <a href="{{ route('legal.case_deck') }}" class="btn btn-ghost btn-sm mr-4">
              <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
              <h1 class="text-3xl font-bold text-gray-800">{{ $case->case_title }}</h1>
              <p class="text-gray-600">Case #{{ $case->case_number }}</p>
            </div>
          </div>
          <div class="flex gap-2">
            @if($case->workflow_stage !== 'closed')
            <button onclick="openStageTransitionModal()" class="btn btn-primary">
              <i data-lucide="arrow-right" class="w-4 h-4 mr-2"></i>
              Advance Stage
            </button>
            @endif
          </div>
        </div>

        <!-- Workflow Progress -->
        <div class="card bg-white shadow-lg mb-6">
          <div class="card-body">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
              <i data-lucide="git-branch" class="w-5 h-5 mr-2 text-blue-600"></i>
              Workflow Progress
            </h3>
            <div class="workflow-progress">
              <div class="workflow-line">
                <div class="workflow-line-progress" style="width: {{ ['filing' => 0, 'investigation' => 25, 'review' => 50, 'resolution' => 75, 'closed' => 100][$case->workflow_stage] }}%"></div>
              </div>
              
              @foreach(['filing', 'investigation', 'review', 'resolution', 'closed'] as $index => $stage)
              <div class="workflow-step {{ $case->workflow_stage === $stage ? 'active' : '' }} {{ array_search($case->workflow_stage, ['filing', 'investigation', 'review', 'resolution', 'closed']) > $index ? 'completed' : '' }}">
                <div class="workflow-step-icon">
                  <i data-lucide="{{ ['filing' => 'file-text', 'investigation' => 'search', 'review' => 'clipboard-check', 'resolution' => 'check-circle', 'closed' => 'archive'][$stage] }}" class="w-6 h-6"></i>
                </div>
                <span class="workflow-step-label">{{ ucfirst($stage) }}</span>
                @if($case->workflow_stage === $stage)
                <span class="text-xs text-gray-500 mt-1">{{ $case->days_in_current_stage }} days</span>
                @endif
              </div>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="card bg-white shadow">
            <div class="card-body p-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs text-gray-600">Priority</p>
                  <p class="text-lg font-bold">{{ ucfirst($case->priority) }}</p>
                </div>
                <i data-lucide="alert-triangle" class="w-8 h-8 text-orange-500"></i>
              </div>
            </div>
          </div>
          <div class="card bg-white shadow">
            <div class="card-body p-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs text-gray-600">Evidence</p>
                  <p class="text-lg font-bold">{{ $case->evidence->count() }}</p>
                </div>
                <i data-lucide="paperclip" class="w-8 h-8 text-blue-500"></i>
              </div>
            </div>
          </div>
          <div class="card bg-white shadow">
            <div class="card-body p-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs text-gray-600">Witnesses</p>
                  <p class="text-lg font-bold">{{ $case->witnesses->count() }}</p>
                </div>
                <i data-lucide="users" class="w-8 h-8 text-green-500"></i>
              </div>
            </div>
          </div>
          <div class="card bg-white shadow">
            <div class="card-body p-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs text-gray-600">Days Open</p>
                  <p class="text-lg font-bold">{{ $case->created_at->diffInDays(now()) }}</p>
                </div>
                <i data-lucide="calendar" class="w-8 h-8 text-purple-500"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="card bg-white shadow-lg">
          <div class="border-b border-gray-200">
            <div class="flex overflow-x-auto">
              <button class="tab-btn active" onclick="switchTab('overview')" id="tab-overview">
                <i data-lucide="info" class="w-4 h-4 inline mr-2"></i>
                Overview
              </button>
              <button class="tab-btn" onclick="switchTab('investigation')" id="tab-investigation">
                <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>
                Investigation
              </button>
              <button class="tab-btn" onclick="switchTab('evidence')" id="tab-evidence">
                <i data-lucide="paperclip" class="w-4 h-4 inline mr-2"></i>
                Evidence ({{ $case->evidence->count() }})
              </button>
              <button class="tab-btn" onclick="switchTab('witnesses')" id="tab-witnesses">
                <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
                Witnesses ({{ $case->witnesses->count() }})
              </button>
              <button class="tab-btn" onclick="switchTab('resolution')" id="tab-resolution">
                <i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>
                Resolution
              </button>
              <button class="tab-btn" onclick="switchTab('activity')" id="tab-activity">
                <i data-lucide="activity" class="w-4 h-4 inline mr-2"></i>
                Activity Log
              </button>
            </div>
          </div>

          <div class="card-body p-6">
            <!-- TAB 1: OVERVIEW -->
            <div id="content-overview" class="tab-content active">
              <h3 class="text-xl font-bold mb-4">Case Overview</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                  <h4 class="font-semibold text-gray-700 flex items-center">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                    Basic Information
                  </h4>
                  <div class="pl-6 space-y-3">
                    <div>
                      <label class="text-sm text-gray-600">Case Type</label>
                      <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $case->case_type)) }}</p>
                    </div>
                    <div>
                      <label class="text-sm text-gray-600">Status</label>
                      <p class="font-medium">
                        <span class="badge {{ $case->status === 'pending' ? 'badge-warning' : ($case->status === 'completed' ? 'badge-success' : 'badge-error') }}">
                          {{ ucfirst($case->status) }}
                        </span>
                      </p>
                    </div>
                    <div>
                      <label class="text-sm text-gray-600">Created Date</label>
                      <p class="font-medium">{{ $case->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                      <label class="text-sm text-gray-600">Created By</label>
                      <p class="font-medium">{{ $case->createdBy->Fname ?? 'Unknown' }} {{ $case->createdBy->Lname ?? '' }}</p>
                    </div>
                  </div>
                </div>

                <!-- Incident Details -->
                <div class="space-y-4">
                  <h4 class="font-semibold text-gray-700 flex items-center">
                    <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i>
                    Incident Details
                  </h4>
                  <div class="pl-6 space-y-3">
                    <div>
                      <label class="text-sm text-gray-600">Employee Involved</label>
                      <p class="font-medium">{{ $case->employee_involved ?? 'Not specified' }}</p>
                    </div>
                    <div>
                      <label class="text-sm text-gray-600">Incident Date</label>
                      <p class="font-medium">{{ $case->incident_date ? $case->incident_date->format('M d, Y h:i A') : 'Not specified' }}</p>
                    </div>
                    <div>
                      <label class="text-sm text-gray-600">Location</label>
                      <p class="font-medium">{{ $case->incident_location ?? 'Not specified' }}</p>
                    </div>
                    <div>
                      <label class="text-sm text-gray-600">Assigned To</label>
                      <p class="font-medium">{{ $case->assignedTo->Fname ?? 'Unassigned' }} {{ $case->assignedTo->Lname ?? '' }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div class="mt-6">
                <h4 class="font-semibold text-gray-700 mb-2">Case Description</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                  <p class="text-gray-700 whitespace-pre-wrap">{{ $case->case_description }}</p>
                </div>
              </div>
            </div>

            <!-- TAB 2: INVESTIGATION -->
            <div id="content-investigation" class="tab-content">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold">Investigation</h3>
                @if($case->workflow_stage === 'investigation' && auth()->user()->role === 'Administrator')
                <button onclick="openInvestigationNoteModal()" class="btn btn-sm btn-primary">
                  <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                  Add Note
                </button>
                @endif
              </div>

              <!-- Investigator Assignment -->
              <div class="card bg-gray-50 mb-6">
                <div class="card-body">
                  <h4 class="font-semibold mb-4">Investigator</h4>
                  @if($case->investigator_id)
                  <div class="flex items-center">
                    <div class="avatar placeholder mr-4">
                      <div class="bg-blue-900 text-white rounded-full w-12">
                        <span class="text-lg">{{ substr($case->investigator->Fname ?? 'U', 0, 1) }}</span>
                      </div>
                    </div>
                    <div>
                      <p class="font-semibold">{{ $case->investigator->Fname }} {{ $case->investigator->Lname }}</p>
                      <p class="text-sm text-gray-600">{{ $case->investigator->email }}</p>
                    </div>
                  </div>
                  @else
                  <p class="text-gray-500">No investigator assigned yet</p>
                  @if(auth()->user()->role === 'Administrator')
                  <button onclick="openAssignInvestigatorModal()" class="btn btn-sm btn-outline mt-2">
                    Assign Investigator
                  </button>
                  @endif
                  @endif
                </div>
              </div>

              <!-- Investigation Timeline -->
              <div class="space-y-4 mb-6">
                <h4 class="font-semibold">Investigation Timeline</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Started</p>
                    <p class="font-semibold">{{ $case->investigation_started_at ? $case->investigation_started_at->format('M d, Y') : 'Not started' }}</p>
                  </div>
                  <div class="bg-orange-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Duration</p>
                    <p class="font-semibold">
                      @if($case->investigation_started_at && $case->investigation_completed_at)
                        {{ $case->investigation_started_at->diffInDays($case->investigation_completed_at) }} days
                      @elseif($case->investigation_started_at)
                        {{ $case->investigation_started_at->diffInDays(now()) }} days (ongoing)
                      @else
                        N/A
                      @endif
                    </p>
                  </div>
                  <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Completed</p>
                    <p class="font-semibold">{{ $case->investigation_completed_at ? $case->investigation_completed_at->format('M d, Y') : 'Not completed' }}</p>
                  </div>
                </div>
              </div>

              <!-- Investigation Notes -->
              <div class="mb-6">
                <h4 class="font-semibold mb-2">Investigation Notes</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                  <p class="text-gray-700 whitespace-pre-wrap">{{ $case->investigation_notes ?? 'No investigation notes yet.' }}</p>
                </div>
              </div>

              <!-- Investigation Findings -->
              @if($case->investigation_findings)
              <div>
                <h4 class="font-semibold mb-2">Investigation Findings</h4>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                  <p class="text-gray-700 whitespace-pre-wrap">{{ $case->investigation_findings }}</p>
                </div>
              </div>
              @endif
            </div>

            <!-- TAB 3: EVIDENCE -->
            <div id="content-evidence" class="tab-content">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold">Evidence & Documentation</h3>
                @if(auth()->user()->role === 'Administrator')
                <button onclick="openUploadEvidenceModal()" class="btn btn-sm btn-primary">
                  <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
                  Upload Evidence
                </button>
                @endif
              </div>

              @if($case->evidence->count() > 0)
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($case->evidence as $evidence)
                <div class="evidence-card card bg-white border border-gray-200">
                  <div class="card-body p-4">
                    <div class="flex items-start justify-between mb-3">
                      <div class="flex items-center">
                        @php
                          $iconMap = [
                            'document' => 'file-text',
                            'photo' => 'image',
                            'video' => 'video',
                            'audio' => 'mic',
                            'other' => 'file'
                          ];
                          $icon = $iconMap[$evidence->evidence_type] ?? 'file';
                        @endphp
                        <i data-lucide="{{ $icon }}" class="w-8 h-8 text-blue-500 mr-3"></i>
                        <div>
                          <h5 class="font-semibold text-sm">{{ $evidence->title }}</h5>
                          <p class="text-xs text-gray-500">{{ $evidence->file_size_human }}</p>
                        </div>
                      </div>
                      <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-xs">
                          <i data-lucide="more-vertical" class="w-4 h-4"></i>
                        </label>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-white rounded-box w-52">
                          <li><a href="{{ asset('storage/' . $evidence->file_path) }}" target="_blank">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            View
                          </a></li>
                          <li><a href="{{ asset('storage/' . $evidence->file_path) }}" download>
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Download
                          </a></li>
                          @if(auth()->user()->role === 'Administrator')
                          <li><a onclick="deleteEvidence({{ $evidence->id }})" class="text-red-600">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            Delete
                          </a></li>
                          @endif
                        </ul>
                      </div>
                    </div>
                    @if($evidence->description)
                    <p class="text-xs text-gray-600 mb-2">{{ $evidence->description }}</p>
                    @endif
                    <div class="text-xs text-gray-500">
                      <p>Uploaded: {{ $evidence->created_at->format('M d, Y') }}</p>
                      @if($evidence->collected_at)
                      <p>Collected: {{ $evidence->collected_at->format('M d, Y') }}</p>
                      @endif
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              @else
              <div class="text-center py-12 bg-gray-50 rounded-lg">
                <i data-lucide="inbox" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <p class="text-gray-600 mb-4">No evidence uploaded yet</p>
                @if(auth()->user()->role === 'Administrator')
                <button onclick="openUploadEvidenceModal()" class="btn btn-primary btn-sm">
                  <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
                  Upload First Evidence
                </button>
                @endif
              </div>
              @endif
            </div>

            <!-- TAB 4: WITNESSES -->
            <div id="content-witnesses" class="tab-content">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold">Witnesses</h3>
                @if(auth()->user()->role === 'Administrator')
                <button onclick="openAddWitnessModal()" class="btn btn-sm btn-primary">
                  <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                  Add Witness
                </button>
                @endif
              </div>

              @if($case->witnesses->count() > 0)
              <div class="space-y-4">
                @foreach($case->witnesses as $witness)
                <div class="card bg-white border border-gray-200">
                  <div class="card-body">
                    <div class="flex items-start justify-between">
                      <div class="flex items-start">
                        <div class="avatar placeholder mr-4">
                          <div class="bg-purple-100 text-purple-700 rounded-full w-12">
                            <span class="text-lg">{{ substr($witness->witness_name, 0, 1) }}</span>
                          </div>
                        </div>
                        <div class="flex-1">
                          <h4 class="font-semibold text-lg">{{ $witness->witness_name }}</h4>
                          <div class="text-sm text-gray-600 space-y-1 mt-2">
                            @if($witness->witness_department)
                            <p><i data-lucide="briefcase" class="w-3 h-3 inline mr-1"></i> {{ $witness->witness_department }}</p>
                            @endif
                            @if($witness->witness_position)
                            <p><i data-lucide="user" class="w-3 h-3 inline mr-1"></i> {{ $witness->witness_position }}</p>
                            @endif
                            @if($witness->witness_contact)
                            <p><i data-lucide="phone" class="w-3 h-3 inline mr-1"></i> {{ $witness->witness_contact }}</p>
                            @endif
                            @if($witness->witness_email)
                            <p><i data-lucide="mail" class="w-3 h-3 inline mr-1"></i> {{ $witness->witness_email }}</p>
                            @endif
                          </div>
                          @if($witness->statement)
                          <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm font-semibold mb-1">Statement ({{ ucfirst($witness->statement_type) }})</p>
                            <p class="text-sm text-gray-700">{{ $witness->statement }}</p>
                            @if($witness->statement_date)
                            <p class="text-xs text-gray-500 mt-2">Recorded: {{ $witness->statement_date->format('M d, Y h:i A') }}</p>
                            @endif
                          </div>
                          @endif
                        </div>
                      </div>
                      @if(auth()->user()->role === 'Administrator')
                      <button onclick="deleteWitness({{ $witness->id }})" class="btn btn-ghost btn-sm text-red-600">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                      @endif
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              @else
              <div class="text-center py-12 bg-gray-50 rounded-lg">
                <i data-lucide="users" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <p class="text-gray-600 mb-4">No witnesses recorded yet</p>
                @if(auth()->user()->role === 'Administrator')
                <button onclick="openAddWitnessModal()" class="btn btn-primary btn-sm">
                  <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                  Add First Witness
                </button>
                @endif
              </div>
              @endif
            </div>

            <!-- TAB 5: RESOLUTION -->
            <div id="content-resolution" class="tab-content">
              <h3 class="text-xl font-bold mb-4">Resolution</h3>
              
              @if($case->workflow_stage === 'resolution' || $case->workflow_stage === 'closed')
              <div class="space-y-6">
                <!-- Resolution Decision -->
                <div>
                  <h4 class="font-semibold mb-2">Resolution Decision</h4>
                  @if($case->resolution_decision)
                  <div class="bg-{{ $case->resolution_decision === 'approved' ? 'green' : ($case->resolution_decision === 'rejected' ? 'red' : 'gray') }}-50 border-l-4 border-{{ $case->resolution_decision === 'approved' ? 'green' : ($case->resolution_decision === 'rejected' ? 'red' : 'gray') }}-500 p-4 rounded">
                    <p class="font-semibold text-lg">{{ ucfirst($case->resolution_decision) }}</p>
                    @if($case->resolved_at)
                    <p class="text-sm text-gray-600 mt-1">Resolved on {{ $case->resolved_at->format('M d, Y h:i A') }}</p>
                    @endif
                  </div>
                  @else
                  <p class="text-gray-500">No resolution decision yet</p>
                  @if(auth()->user()->role === 'Administrator' && $case->workflow_stage === 'resolution')
                  <button onclick="openResolutionModal()" class="btn btn-primary btn-sm mt-2">
                    Make Resolution Decision
                  </button>
                  @endif
                  @endif
                </div>

                <!-- Resolution Notes -->
                @if($case->resolution_notes)
                <div>
                  <h4 class="font-semibold mb-2">Resolution Notes</h4>
                  <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $case->resolution_notes }}</p>
                  </div>
                </div>
                @endif

                <!-- Disciplinary Actions -->
                @if($case->disciplinary_actions)
                <div>
                  <h4 class="font-semibold mb-2">Disciplinary Actions Taken</h4>
                  <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $case->disciplinary_actions }}</p>
                  </div>
                </div>
                @endif

                <!-- Preventive Measures -->
                @if($case->preventive_measures)
                <div>
                  <h4 class="font-semibold mb-2">Preventive Measures</h4>
                  <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $case->preventive_measures }}</p>
                  </div>
                </div>
                @endif
              </div>
              @else
              <div class="text-center py-12 bg-gray-50 rounded-lg">
                <i data-lucide="clock" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <p class="text-gray-600">Case must reach resolution stage before entering resolution details</p>
                <p class="text-sm text-gray-500 mt-2">Current stage: <span class="font-semibold">{{ ucfirst($case->workflow_stage) }}</span></p>
              </div>
              @endif
            </div>

            <!-- TAB 6: ACTIVITY LOG -->
            <div id="content-activity" class="tab-content">
              <h3 class="text-xl font-bold mb-4">Activity Log</h3>
              
              @if($case->activities->count() > 0)
              <div class="space-y-4">
                @foreach($case->activities as $activity)
                <div class="timeline-item">
                  <div class="timeline-dot"></div>
                  <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <p class="font-semibold">{{ $activity->action_description }}</p>
                        <p class="text-sm text-gray-600 mt-1">
                          by {{ $activity->user_name ?? 'System' }}
                        </p>
                        @if($activity->changes)
                        <div class="mt-2 text-xs bg-gray-50 p-2 rounded">
                          <pre class="text-gray-600">{{ json_encode($activity->changes, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        @endif
                      </div>
                      <div class="text-right text-sm text-gray-500">
                        <p>{{ $activity->created_at->format('M d, Y') }}</p>
                        <p>{{ $activity->created_at->format('h:i A') }}</p>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              @else
              <div class="text-center py-12 bg-gray-50 rounded-lg">
                <i data-lucide="activity" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <p class="text-gray-600">No activity recorded yet</p>
              </div>
              @endif
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- MODALS -->
  
  <!-- Stage Transition Modal -->
  <div id="stageTransitionModal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Advance Workflow Stage</h3>
      <p class="text-sm text-gray-600 mb-4">
        Current stage: <span class="font-semibold">{{ ucfirst($case->workflow_stage) }}</span>
      </p>
      <form action="{{ route('legal.cases.transition', $case->id) }}" method="POST">
        @csrf
        <input type="hidden" name="current_stage" value="{{ $case->workflow_stage }}">
        <div class="form-control mb-4">
          <label class="label"><span class="label-text">Transition Notes (Optional)</span></label>
          <textarea name="notes" class="textarea textarea-bordered" rows="3" placeholder="Add any notes about this transition..."></textarea>
        </div>
        <div class="modal-action">
          <button type="button" onclick="closeStageTransitionModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-primary">Advance to {{ ucfirst(['filing' => 'investigation', 'investigation' => 'review', 'review' => 'resolution', 'resolution' => 'closed'][$case->workflow_stage] ?? 'next stage') }}</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Upload Evidence Modal -->
  <div id="uploadEvidenceModal" class="modal">
    <div class="modal-box max-w-2xl">
      <h3 class="font-bold text-lg mb-4">Upload Evidence</h3>
      <form action="{{ route('legal.cases.evidence', $case->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="space-y-4">
          <div class="form-control">
            <label class="label"><span class="label-text">Evidence Type*</span></label>
            <select name="evidence_type" class="select select-bordered" required>
              <option value="">Select type</option>
              <option value="document">Document</option>
              <option value="photo">Photo</option>
              <option value="video">Video</option>
              <option value="audio">Audio</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text">Title*</span></label>
            <input type="text" name="evidence_description" class="input input-bordered" required placeholder="Brief title for this evidence">
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text">Description</span></label>
            <textarea name="description" class="textarea textarea-bordered" rows="2" placeholder="Optional description"></textarea>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text">File*</span></label>
            <input type="file" name="evidence_file" class="file-input file-input-bordered w-full" required>
            <label class="label"><span class="label-text-alt">Max file size: 10MB</span></label>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text">Collection Date*</span></label>
            <input type="date" name="evidence_date" class="input input-bordered" required>
          </div>
        </div>
        <div class="modal-action">
          <button type="button" onclick="closeUploadEvidenceModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
            Upload
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Witness Modal -->
  <div id="addWitnessModal" class="modal">
    <div class="modal-box max-w-2xl">
      <h3 class="font-bold text-lg mb-4">Add Witness</h3>
      <form action="{{ route('legal.cases.witness.add', $case->id) }}" method="POST">
        @csrf
        <div class="space-y-4">
          <div class="form-control">
            <label class="label"><span class="label-text">Witness Name*</span></label>
            <input type="text" name="witness_name" class="input input-bordered" required>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="form-control">
              <label class="label"><span class="label-text">Department</span></label>
              <input type="text" name="witness_department" class="input input-bordered">
            </div>
            <div class="form-control">
              <label class="label"><span class="label-text">Position</span></label>
              <input type="text" name="witness_position" class="input input-bordered">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="form-control">
              <label class="label"><span class="label-text">Contact Number</span></label>
              <input type="text" name="witness_contact" class="input input-bordered">
            </div>
            <div class="form-control">
              <label class="label"><span class="label-text">Email</span></label>
              <input type="email" name="witness_email" class="input input-bordered">
            </div>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text">Statement Type</span></label>
            <select name="statement_type" class="select select-bordered">
              <option value="written">Written Statement</option>
              <option value="verbal">Verbal Statement</option>
              <option value="video">Video Statement</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text">Statement</span></label>
            <textarea name="statement" class="textarea textarea-bordered" rows="4" placeholder="Record witness statement here..."></textarea>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text">Statement Date</span></label>
            <input type="datetime-local" name="statement_date" class="input input-bordered">
          </div>
        </div>
        <div class="modal-action">
          <button type="button" onclick="closeAddWitnessModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
            Add Witness
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Investigation Note Modal -->
  <div id="investigationNoteModal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Add Investigation Note</h3>
      <form action="{{ route('legal.cases.investigation.note', $case->id) }}" method="POST">
        @csrf
        <div class="form-control mb-4">
          <label class="label"><span class="label-text">Investigation Note*</span></label>
          <textarea name="investigation_notes" class="textarea textarea-bordered" rows="6" required placeholder="Add investigation notes...">{{ $case->investigation_notes }}</textarea>
        </div>
        <div class="form-control mb-4">
          <label class="label"><span class="label-text">Investigation Findings</span></label>
          <textarea name="investigation_findings" class="textarea textarea-bordered" rows="4" placeholder="Final investigation findings...">{{ $case->investigation_findings }}</textarea>
        </div>
        <div class="modal-action">
          <button type="button" onclick="closeInvestigationNoteModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Notes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Resolution Modal -->
  <div id="resolutionModal" class="modal">
    <div class="modal-box max-w-2xl">
      <h3 class="font-bold text-lg mb-4">Case Resolution</h3>
      <form action="{{ route('legal.cases.resolution', $case->id) }}" method="POST">
        @csrf
        <div class="space-y-4">
          <div class="form-control">
            <label class="label"><span class="label-text">Resolution Decision*</span></label>
            <select name="resolution_decision" class="select select-bordered" required>
              <option value="">Select decision</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
              <option value="dismissed">Dismissed</option>
              <option value="settled">Settled</option>
              <option value="pending">Pending Further Review</option>
            </select>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text">Resolution Notes*</span></label>
            <textarea name="resolution_notes" class="textarea textarea-bordered" rows="4" required placeholder="Explain the resolution decision..."></textarea>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text">Disciplinary Actions</span></label>
            <textarea name="disciplinary_actions" class="textarea textarea-bordered" rows="3" placeholder="List any disciplinary actions taken..."></textarea>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text">Preventive Measures</span></label>
            <textarea name="preventive_measures" class="textarea textarea-bordered" rows="3" placeholder="List preventive measures to avoid similar incidents..."></textarea>
          </div>
        </div>
        <div class="modal-action">
          <button type="button" onclick="closeResolutionModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit Resolution</button>
        </div>
      </form>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    lucide.createIcons();

    // Tab switching
    function switchTab(tabName) {
      // Hide all tab contents
      document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
      });
      
      // Remove active class from all tab buttons
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      
      // Show selected tab content
      document.getElementById('content-' + tabName).classList.add('active');
      document.getElementById('tab-' + tabName).classList.add('active');
      
      // Reinitialize Lucide icons
      lucide.createIcons();
    }

    // Modal functions
    function openStageTransitionModal() {
      document.getElementById('stageTransitionModal').classList.add('modal-open');
    }

    function closeStageTransitionModal() {
      document.getElementById('stageTransitionModal').classList.remove('modal-open');
    }

    function openUploadEvidenceModal() {
      document.getElementById('uploadEvidenceModal').classList.add('modal-open');
    }

    function closeUploadEvidenceModal() {
      document.getElementById('uploadEvidenceModal').classList.remove('modal-open');
    }

    function openAddWitnessModal() {
      document.getElementById('addWitnessModal').classList.add('modal-open');
    }

    function closeAddWitnessModal() {
      document.getElementById('addWitnessModal').classList.remove('modal-open');
    }

    function openInvestigationNoteModal() {
      document.getElementById('investigationNoteModal').classList.add('modal-open');
    }

    function closeInvestigationNoteModal() {
      document.getElementById('investigationNoteModal').classList.remove('modal-open');
    }

    function openResolutionModal() {
      document.getElementById('resolutionModal').classList.add('modal-open');
    }

    function closeResolutionModal() {
      document.getElementById('resolutionModal').classList.remove('modal-open');
    }

    function openAssignInvestigatorModal() {
      Swal.fire({
        title: 'Assign Investigator',
        text: 'Feature coming soon - investigator assignment',
        icon: 'info'
      });
    }

    // Delete functions
    function deleteEvidence(id) {
      Swal.fire({
        title: 'Delete Evidence?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/legal/cases/evidence/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              location.reload();
            }
          });
        }
      });
    }

    function deleteWitness(id) {
      Swal.fire({
        title: 'Remove Witness?',
        text: 'This will remove the witness record from this case.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Remove',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/legal/cases/witness/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              location.reload();
            }
          });
        }
      });
    }

    // Close modals when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          modal.classList.remove('modal-open');
        }
      });
    });
  </script>
</body>
</html>
