<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservation Details - Soliera</title>
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

        <!-- Back button and title -->
        <div class="flex items-center mb-6">
          <a href="{{ route('facility_reservations.index') }}" class="btn btn-ghost btn-sm mr-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back
          </a>
          <h1 class="text-3xl font-bold text-gray-800">Reservation Details</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Reservation Info -->
          <div class="lg:col-span-2">
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <div class="flex items-center justify-between mb-6">
                  <h2 class="card-title text-2xl flex items-center">
                    <i data-lucide="building" class="w-6 h-6 text-blue-500 mr-3"></i>
                    {{ $reservation->facility->name ?? 'N/A' }}
                  </h2>
                  <div class="flex items-center gap-2">
                    @if($reservation->status === 'approved')
                      <span class="badge badge-success badge-lg gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Approved
                      </span>
                      @if($reservation->auto_approved_at)
                        <span class="badge badge-info badge-sm">Auto-Approved</span>
                      @endif
                    @elseif($reservation->status === 'denied')
                      <span class="badge badge-error badge-lg gap-2">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Denied
                      </span>
                    @else
                      <span class="badge badge-warning badge-lg gap-2">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        Pending
                      </span>
                    @endif
                  </div>
                </div>

                <!-- Reservation Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <div class="space-y-4">
                    <div>
                      <label class="text-sm font-medium text-gray-500">Reserved By</label>
                      <div class="flex items-center gap-3 mt-1">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                          <span class="text-sm font-medium text-blue-600">
                            {{ substr($reservation->reserver->name ?? 'N/A', 0, 1) }}
                          </span>
                        </div>
                        <span class="font-medium">{{ $reservation->reserver->name ?? 'N/A' }}</span>
                      </div>
                    </div>

                    <div>
                      <label class="text-sm font-medium text-gray-500">Start Time</label>
                      <div class="flex items-center gap-2 mt-1">
                        <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                        <span>{{ \Carbon\Carbon::parse($reservation->start_time)->format('M d, Y H:i') }}</span>
                      </div>
                    </div>

                    <div>
                      <label class="text-sm font-medium text-gray-500">End Time</label>
                      <div class="flex items-center gap-2 mt-1">
                        <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                        <span>{{ \Carbon\Carbon::parse($reservation->end_time)->format('M d, Y H:i') }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <div>
                      <label class="text-sm font-medium text-gray-500">Purpose</label>
                      <p class="mt-1 text-gray-700">{{ $reservation->purpose ?? 'No purpose specified' }}</p>
                    </div>

                    @if($reservation->status !== 'pending')
                      <div>
                        <label class="text-sm font-medium text-gray-500">
                          {{ ucfirst($reservation->status) }} By
                        </label>
                        <div class="flex items-center gap-2 mt-1">
                          <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <span class="text-sm font-medium text-green-600">
                              {{ substr($reservation->approver->name ?? 'N/A', 0, 1) }}
                            </span>
                          </div>
                          <span>{{ $reservation->approver->name ?? 'N/A' }}</span>
                        </div>
                      </div>

                      <div>
                        <label class="text-sm font-medium text-gray-500">Remarks</label>
                        <p class="mt-1 text-gray-700">{{ $reservation->remarks ?? 'No remarks' }}</p>
                      </div>
                    @endif

                    @if($reservation->auto_approved_at)
                      <div>
                        <label class="text-sm font-medium text-gray-500">Auto-Approved At</label>
                        <div class="flex items-center gap-2 mt-1">
                          <i data-lucide="zap" class="w-4 h-4 text-yellow-500"></i>
                          <span>{{ \Carbon\Carbon::parse($reservation->auto_approved_at)->format('M d, Y H:i') }}</span>
                        </div>
                      </div>
                    @endif
                  </div>
                </div>

                <!-- Special Requirements -->
                @if($reservation->requires_legal_review || $reservation->requires_visitor_coordination)
                  <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                      <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-500 mr-2"></i>
                      Special Requirements
                    </h3>
                    <div class="flex gap-3">
                      @if($reservation->requires_legal_review)
                        <span class="badge badge-warning gap-2">
                          <i data-lucide="scale" class="w-4 h-4"></i>
                          Legal Review Required
                        </span>
                      @endif
                      @if($reservation->requires_visitor_coordination)
                        <span class="badge badge-info gap-2">
                          <i data-lucide="users" class="w-4 h-4"></i>
                          Visitor Coordination Required
                        </span>
                      @endif
                    </div>
                  </div>
                @endif

                <!-- Digital Passes Section -->
                @if(($reservation->digital_passes_generated ?? false) && !empty($reservation->digital_pass_data))
                  <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                      <i data-lucide="ticket" class="w-5 h-5 text-green-600 mr-2"></i>
                      Digital Passes
                    </h3>

                    <div class="mb-4 text-sm text-gray-600">
                      <span class="badge badge-success mr-2">Generated</span>
                      @if($reservation->security_notified)
                        <span class="badge badge-info mr-2">Security Notified</span>
                        <span>on {{ optional($reservation->security_notified_at)->format('M d, Y H:i') }}</span>
                      @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                      @foreach($reservation->digital_pass_data as $index => $pass)
                        <div class="p-4 bg-gray-50 rounded-lg border">
                          <div class="flex items-center justify-between mb-2">
                            <div class="font-semibold">{{ $pass['visitor_name'] }}</div>
                            <div class="text-xs text-gray-500">{{ $pass['pass_id'] }}</div>
                          </div>
                          <div class="text-sm text-gray-700 mb-2">
                            <div class="flex items-center gap-2"><i data-lucide="building" class="w-4 h-4"></i> {{ $pass['facility'] }}</div>
                            <div class="flex items-center gap-2">
                              <i data-lucide="calendar" class="w-4 h-4"></i>
                              {{ \Carbon\Carbon::parse($pass['valid_from'])->format('M d, Y h:i A') }} → {{ \Carbon\Carbon::parse($pass['valid_until'])->format('M d, Y h:i A') }}
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>

                    <div class="mt-4 flex gap-2">
                      <button id="downloadPassesCsv" type="button" class="btn btn-outline btn-sm">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i>Download CSV
                      </button>
                      <button id="printPasses" type="button" class="btn btn-outline btn-sm">
                        <i data-lucide="printer" class="w-4 h-4 mr-2"></i>Print Passes
                      </button>
                    </div>
                  </div>
                @endif

                <!-- Legal Review Section -->
                @if($reservation->requires_legal_review && in_array(strtolower(auth()->user()->role ?? ''), ['legal', 'administrator']))
                  <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                      <i data-lucide="scale" class="w-5 h-5 text-red-500 mr-2"></i>
                      Legal Review Required
                    </h3>
                    @if($reservation->legal_reviewed_at)
                      <div class="alert alert-success">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <div>
                          <h4 class="font-bold">Legal Review Completed</h4>
                          <p class="text-sm">Reviewed by {{ $reservation->legalReviewer->name ?? 'System' }} on {{ \Carbon\Carbon::parse($reservation->legal_reviewed_at)->format('M d, Y H:i') }}</p>
                          @if($reservation->legal_comment)
                            <p class="text-sm mt-1"><strong>Comment:</strong> {{ $reservation->legal_comment }}</p>
                          @endif
                        </div>
                      </div>
                    @else
                      <div class="flex gap-3">
                        <a href="{{ route('facility_reservations.legal_review', $reservation->id) }}" class="btn btn-warning">
                          <i data-lucide="scale" class="w-4 h-4 mr-2"></i>Perform Legal Review
                        </a>
                      </div>
                    @endif
                  </div>
                @endif

                <!-- Visitor Coordination Section -->
                    @if($reservation->requires_visitor_coordination || $reservation->ai_classification)
                  <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                      <i data-lucide="users" class="w-5 h-5 text-blue-500 mr-2"></i>
                      Visitor Coordination Required
                    </h3>
                    
                        @if(!$reservation->visitor_data)
                      <!-- Extract Visitor Data -->
                      <div class="alert alert-info mb-4">
                        <i data-lucide="info" class="w-5 h-5"></i>
                        <div>
                          <h4 class="font-bold">Extract Visitor Information</h4>
                              <p class="text-sm">Extract visitor details from the uploaded document using AI analysis. This is available even if the system did not automatically flag visitor coordination.</p>
                        </div>
                      </div>
                      <form action="{{ route('facility_reservations.extract_visitors', $reservation->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-info">
                          <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>Extract Visitor Data
                        </button>
                      </form>
                    @else
                      <!-- Show Extracted Visitors -->
                      <div class="mb-4">
                        <h4 class="font-medium mb-2">Extracted Visitors:</h4>
                        <div class="space-y-2">
                          @foreach($reservation->visitor_data as $visitor)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                              <div>
                                <p class="font-medium">{{ $visitor['name'] }}</p>
                                <p class="text-sm text-gray-500">
                                  Status: 
                                  @if($visitor['status'] === 'pending_approval')
                                    <span class="badge badge-warning">Pending Approval</span>
                                  @elseif($visitor['status'] === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                  @endif
                                </p>
                              </div>
                              @if($visitor['status'] === 'approved')
                                <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                              @endif
                            </div>
                          @endforeach
                        </div>
                      </div>

                      @php
                        $hasPendingVisitors = collect($reservation->visitor_data)->contains('status', 'pending_approval');
                        $allApproved = collect($reservation->visitor_data)->every(function($visitor) {
                          return $visitor['status'] === 'approved';
                        });
                      @endphp

                      @if($hasPendingVisitors)
                        <form action="{{ route('facility_reservations.approve_visitors', $reservation->id) }}" method="POST">
                          @csrf
                          <button type="submit" class="btn btn-success">
                            <i data-lucide="check" class="w-4 h-4 mr-2"></i>Approve All Visitors & Generate Passes
                          </button>
                        </form>
                      @elseif($allApproved)
                        <div class="alert alert-success">
                          <i data-lucide="check-circle" class="w-5 h-5"></i>
                          <div>
                            <h4 class="font-bold">Visitor Setup Complete</h4>
                            <p class="text-sm">All visitors have been approved and digital passes have been generated.</p>
                          </div>
                        </div>
                      @endif
                    @endif
                  </div>
                @endif

                <!-- Approval Actions for Pending Reservations -->
                @if($reservation->status === 'pending' && auth()->user()->role === 'administrator' && !$reservation->requires_legal_review && !$reservation->requires_visitor_coordination)
                  <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Manual Approval Actions</h3>
                    <div class="flex gap-3">
                      <form action="{{ route('facility_reservations.approve', $reservation->id) }}" method="POST" class="flex-1">
                        @csrf
                        <div class="flex gap-2">
                          <input type="text" name="remarks" class="input input-bordered flex-1" placeholder="Approval remarks (optional)">
                          <button type="submit" class="btn btn-success">
                            <i data-lucide="check" class="w-4 h-4 mr-2"></i>Approve
                          </button>
                        </div>
                      </form>
                      <form action="{{ route('facility_reservations.deny', $reservation->id) }}" method="POST" class="flex-1">
                        @csrf
                        <div class="flex gap-2">
                          <input type="text" name="remarks" class="input input-bordered flex-1" placeholder="Denial remarks (optional)">
                          <button type="submit" class="btn btn-error">
                            <i data-lucide="x" class="w-4 h-4 mr-2"></i>Deny
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>

          <!-- AI Analysis Sidebar -->
          <div class="lg:col-span-1">
            <!-- AI Analysis Card -->
            <div class="card bg-white shadow-xl mb-6">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4 flex items-center">
                  <i data-lucide="brain" class="w-5 h-5 text-purple-500 mr-2"></i>
                  AI Analysis
                </h3>

                @if($reservation->ai_classification)
                  <div class="space-y-4">
                    <div>
                      <label class="text-sm font-medium text-gray-500">Document Category</label>
                      <div class="mt-1">
                        <span class="badge badge-primary">{{ ucfirst($reservation->getAiClassification('category') ?? 'Unknown') }}</span>
                        @if($reservation->getAiClassification('fallback'))
                          <span class="badge badge-warning badge-xs ml-2">Fallback</span>
                        @endif
                      </div>
                    </div>

                    @if($reservation->getAiClassification('summary'))
                      <div>
                        <label class="text-sm font-medium text-gray-500">Summary</label>
                        <p class="mt-1 text-sm text-gray-700">{{ $reservation->getAiClassification('summary') }}</p>
                      </div>
                    @endif

                    @if($reservation->getAiClassification('key_info'))
                      <div>
                        <label class="text-sm font-medium text-gray-500">Key Information</label>
                        <p class="mt-1 text-sm text-gray-700">{{ $reservation->getAiClassification('key_info') }}</p>
                      </div>
                    @endif

                    @if($reservation->getAiClassification('legal_implications'))
                      <div>
                        <label class="text-sm font-medium text-gray-500">Legal Implications</label>
                        <p class="mt-1 text-sm text-gray-700">{{ $reservation->getAiClassification('legal_implications') }}</p>
                      </div>
                    @endif

                    @if($reservation->getAiClassification('compliance_status'))
                      <div>
                        <label class="text-sm font-medium text-gray-500">Compliance Status</label>
                        <div class="mt-1">
                          @php
                            $status = $reservation->getAiClassification('compliance_status');
                            $statusClass = $status === 'compliant' ? 'success' : ($status === 'non-compliant' ? 'error' : 'warning');
                          @endphp
                          <span class="badge badge-{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                        </div>
                      </div>
                    @endif

                    @if($reservation->getAiClassification('tags'))
                      <div>
                        <label class="text-sm font-medium text-gray-500">Tags</label>
                        <div class="mt-1 flex flex-wrap gap-1">
                          @foreach($reservation->getAiClassification('tags') as $tag)
                            <span class="badge badge-outline badge-sm">{{ $tag }}</span>
                          @endforeach
                        </div>
                      </div>
                    @endif
                  </div>
                @elseif($reservation->ai_error)
                  <div class="alert alert-error">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    <div>
                      <h4 class="font-bold">AI Analysis Error</h4>
                      <p class="text-sm">{{ $reservation->ai_error }}</p>
                    </div>
                  </div>
                @else
                  <div class="text-center py-8">
                    <i data-lucide="file-x" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-gray-500">No document uploaded for AI analysis</p>
                  </div>
                @endif
              </div>
            </div>

            <!-- Document Card -->
            @if($reservation->document_path)
              <div class="card bg-white shadow-xl">
                <div class="card-body">
                  <h3 class="card-title text-lg mb-4 flex items-center">
                    <i data-lucide="file-text" class="w-5 h-5 text-blue-500 mr-2"></i>
                    Supporting Document
                  </h3>
                  
                  <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <i data-lucide="file" class="w-8 h-8 text-blue-500"></i>
                    <div class="flex-1">
                      <p class="font-medium text-sm">{{ basename($reservation->document_path) }}</p>
                      <p class="text-xs text-gray-500">Uploaded document</p>
                    </div>
                    <a href="{{ Storage::url($reservation->document_path) }}" 
                       target="_blank" 
                       class="btn btn-sm btn-outline">
                      <i data-lucide="external-link" class="w-4 h-4"></i>
                    </a>
                  </div>
                </div>
              </div>
            @endif
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    // Dark mode functionality
    function setupDarkMode() {
      const toggle = document.getElementById('darkModeToggle');
      const sunIcon = document.getElementById('sunIcon');
      const moonIcon = document.getElementById('moonIcon');
      
      function updateIcons() {
        if (!sunIcon || !moonIcon) return;
        if(document.documentElement.classList.contains('dark')) {
          sunIcon.classList.remove('hidden');
          moonIcon.classList.add('hidden');
        } else {
          sunIcon.classList.add('hidden');
          moonIcon.classList.remove('hidden');
        }
      }
      
      // Initial state
      const isDarkMode = localStorage.getItem('darkMode') === 'true';
      if (isDarkMode) {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark');
      }
      updateIcons();
      
      if (toggle) {
        toggle.addEventListener('click', function() {
          console.log('Dark mode toggle clicked!');
          
          // Direct toggle without relying on global function
          if (document.documentElement.classList.contains('dark')) {
            // Switch to light mode
            document.documentElement.classList.remove('dark');
            document.body.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
            console.log('Switched to LIGHT mode');
          } else {
            // Switch to dark mode
            document.documentElement.classList.add('dark');
            document.body.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
            console.log('Switched to DARK mode');
          }
          
          updateIcons();
        });
      }
    }

    // Real-time date and time
    function updateDateTime() {
      const now = new Date();
      const dateElement = document.getElementById('currentDate');
      const timeElement = document.getElementById('currentTime');
      
      const dateOptions = { weekday: 'short', month: 'short', day: 'numeric' };
      const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
      
      if (dateElement) dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
      if (timeElement) timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
    }

    function initReservationShowPage() {
      setupDarkMode();
      updateDateTime();
      
      // Update time every second
      setInterval(updateDateTime, 1000);

      // CSV download for passes
      const csvBtn = document.getElementById('downloadPassesCsv');
      if (csvBtn) {
        csvBtn.addEventListener('click', function(){
          try {
            const passes = @json($reservation->digital_pass_data ?? []);
            if (!passes || passes.length === 0) {
              alert('No digital passes available to export. Approve visitors first.');
              return;
            }
            const headers = ['pass_id','visitor_name','visitor_company','valid_from','valid_until','facility','purpose','access_level'];
            const rows = [headers.join(',')].concat(passes.map(p => {
              const vf = new Date(p.valid_from);
              const vu = new Date(p.valid_until);
              const fmt = (d)=> d.toLocaleString('en-US', { month:'short', day:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit', hour12:true });
              return [p.pass_id,p.visitor_name,(p.visitor_company||''),fmt(vf),fmt(vu),p.facility,(p.purpose||''),(p.access_level||'visitor')]
                .map(v => '"'+String(v).replace(/"/g,'""')+'"').join(',');
            }));
            const blob = new Blob([rows.join('\n')], {type: 'text/csv;charset=utf-8;'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = 'digital_passes_{{ $reservation->id }}.csv';
            document.body.appendChild(a); a.click(); document.body.removeChild(a);
            URL.revokeObjectURL(url);
          } catch(e) {}
        });
      }

      // Print helper
      const printBtn = document.getElementById('printPasses');
      if (printBtn) {
        printBtn.addEventListener('click', function(e){
          e.preventDefault();
          try {
            window.print();
          } catch(err) {
            alert('Unable to open print dialog. Please use Ctrl+P/Cmd+P.');
          }
        });
      }
    }

    // Run immediately if DOM is ready; otherwise wait for DOMContentLoaded
    if (document.readyState !== 'loading') {
      initReservationShowPage();
    } else {
      document.addEventListener('DOMContentLoaded', initReservationShowPage);
    }
  </script>
</body>
</html>
