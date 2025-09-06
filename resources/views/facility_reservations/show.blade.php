<!DOCTYPE html>
<html lang="en">
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
          <a href="{{ route('facility_reservations.index') }}" class="btn btn-ghost btn-sm mr-4" style="color: var(--color-regal-navy);" title="Back to Reservations">
            <i data-lucide="arrow-left" class="w-4 h-4" style="color: var(--color-regal-navy);"></i>
          </a>
          <h1 class="text-3xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Reservation Details</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Reservation Info -->
          <div class="lg:col-span-2">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <div class="flex items-center justify-between mb-6">
                  <h2 class="card-title text-2xl flex items-center">
                    <i data-lucide="building" class="w-6 h-6" style="color: var(--color-regal-navy);"></i>
                    <span class="ml-3" style="color: var(--color-charcoal-ink);">{{ $reservation->facility->name ?? 'N/A' }}</span>
                  </h2>
                  <div class="flex items-center gap-2">
                    <span class="badge badge-lg gap-2" style="background-color: var(--color-regal-navy); color: var(--color-white);">
                      Current Status: {{ ucfirst(str_replace('_', ' ', $reservation->current_workflow_status)) }}
                    </span>
                    @if($reservation->status === 'approved')
                      <span class="badge badge-lg gap-2" style="background-color: var(--color-modern-teal); color: var(--color-white);">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Reservation Approved
                      </span>
                      @if($reservation->auto_approved_at)
                        <span class="badge badge-sm" style="background-color: var(--color-regal-navy); color: var(--color-white);">Auto-Approved</span>
                      @endif
                    @elseif($reservation->status === 'denied')
                      <span class="badge badge-lg gap-2" style="background-color: var(--color-danger-red); color: var(--color-white);">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Reservation Denied
                      </span>
                    @endif
                  </div>
                </div>

                <!-- Reservation Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <div class="space-y-4">
                    <div>
                      <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Reserved By</label>
                      <div class="flex items-center gap-3 mt-1">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%);">
                          <span class="text-sm font-medium text-blue-600" style="color: var(--color-regal-navy);">
                            {{ substr($reservation->reserver->name ?? 'N/A', 0, 1) }}
                          </span>
                        </div>
                        <span class="font-medium" style="color: var(--color-charcoal-ink);">{{ $reservation->reserver->name ?? 'N/A' }}</span>
                      </div>
                    </div>

                    <div>
                      <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Start Time</label>
                      <div class="flex items-center gap-2 mt-1">
                        <i data-lucide="calendar" class="w-4 h-4" style="color: var(--color-modern-teal);"></i>
                        <span style="color: var(--color-charcoal-ink);">{{ \Carbon\Carbon::parse($reservation->start_time)->format('M d, Y H:i') }}</span>
                      </div>
                    </div>

                    <div>
                      <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">End Time</label>
                      <div class="flex items-center gap-2 mt-1">
                        <i data-lucide="clock" class="w-4 h-4" style="color: var(--color-modern-teal);"></i>
                        <span style="color: var(--color-charcoal-ink);">{{ \Carbon\Carbon::parse($reservation->end_time)->format('M d, Y H:i') }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <div>
                      <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Purpose</label>
                      <p class="mt-1 text-gray-700" style="color: var(--color-charcoal-ink);">{{ $reservation->purpose ?? 'No purpose specified' }}</p>
                    </div>

                    @if($reservation->status !== 'pending')
                      <div>
                        <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">
                          {{ ucfirst($reservation->status) }} By
                        </label>
                        <div class="flex items-center gap-2 mt-1">
                          <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center" style="background-color: color-mix(in srgb, var(--color-modern-teal), white 80%);">
                            <span class="text-sm font-medium text-green-600" style="color: var(--color-modern-teal);">
                              {{ substr($reservation->approver->name ?? 'N/A', 0, 1) }}
                            </span>
                          </div>
                          <span style="color: var(--color-charcoal-ink);">{{ $reservation->approver->name ?? 'N/A' }}</span>
                        </div>
                      </div>

                      <div>
                        <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Remarks</label>
                        <p class="mt-1 text-gray-700" style="color: var(--color-charcoal-ink);">{{ $reservation->remarks ?? 'No remarks' }}</p>
                      </div>
                    @endif

                    @if($reservation->auto_approved_at)
                      <div>
                        <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Auto-Approved At</label>
                        <div class="flex items-center gap-2 mt-1">
                          <i data-lucide="zap" class="w-4 h-4" style="color: var(--color-golden-ember);"></i>
                          <span style="color: var(--color-charcoal-ink);">{{ \Carbon\Carbon::parse($reservation->auto_approved_at)->format('M d, Y H:i') }}</span>
                        </div>
                      </div>
                    @endif
                  </div>
                </div>

                <!-- New: Workflow Tasks Section -->
                <div class="border-t pt-6" style="border-color: var(--color-snow-mist);">
                    <h3 class="text-lg font-semibold mb-4 flex items-center" style="color: var(--color-charcoal-ink);">
                        <i data-lucide="git-branch" class="w-5 h-5 mr-2" style="color: var(--color-regal-navy);"></i>
                        Workflow Tasks
                    </h3>

                    @if($reservation->tasks->count() > 0)
                        <ul class="space-y-3">
                            @foreach($reservation->tasks->sortBy('created_at') as $task)
                                @php
                                    $taskStatusClass = [
                                        'pending' => 'badge-warning',
                                        'completed' => 'badge-success',
                                        'flagged' => 'badge-error',
                                    ][$task->status] ?? 'badge-info';
                                    $taskIcon = [
                                        'document_classification' => 'file-text',
                                        'legal_review' => 'scale',
                                        'visitor_coordination' => 'users',
                                    ][$task->task_type] ?? 'circle-check';
                                @endphp
                                <li class="flex items-center gap-3 p-3 rounded-lg border" style="background-color: var(--color-snow-mist); border-color: color-mix(in srgb, var(--color-snow-mist), black 10%);">
                                    <i data-lucide="{{ $taskIcon }}" class="w-5 h-5" style="color: var(--color-regal-navy);"></i>
                                    <div class="flex-1">
                                        <p class="font-medium text-sm" style="color: var(--color-charcoal-ink);">{{ ucfirst(str_replace('_', ' ', $task->task_type)) }}</p>
                                        <p class="text-xs text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                                            Assigned to: {{ $task->assigned_to_module }}
                                            @if($task->completed_at)
                                                <span class="ml-2">(Completed {{ \Carbon\Carbon::parse($task->completed_at)->format('M d, Y H:i') }})</span>
                                            @endif
                                        </p>
                                    </div>
                                    <span class="badge {{ $taskStatusClass }}" style="color: var(--color-white);">{{ ucfirst($task->status) }}</span>
                                    @if($task->task_type === 'legal_review' && $task->status === 'pending' && in_array(strtolower(auth()->user()->role ?? ''), ['legal', 'administrator']))
                                        <a href="{{ route('facility_reservations.legal_review', $reservation->id) }}" class="btn btn-sm btn-outline" style="color: var(--color-golden-ember); border-color: var(--color-golden-ember);">
                                            <i data-lucide="scale" class="w-4 h-4"></i>Review
                                        </a>
                                    @elseif($task->task_type === 'visitor_coordination')
                                        <a href="{{ route('visitor.manage_reservation_visitors', $reservation->id) }}" class="btn btn-sm btn-outline" style="color: var(--color-modern-teal); border-color: var(--color-modern-teal);">
                                            <i data-lucide="users" class="w-4 h-4"></i>Manage Visitors (VM)
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        
                        @if($reservation->current_workflow_status === 'ready_for_approval')
                          <div class="alert alert-info mt-4" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 90%); border-color: var(--color-regal-navy); color: var(--color-charcoal-ink);">
                            <i data-lucide="info" class="w-5 h-5" style="color: var(--color-regal-navy);"></i>
                            <span>This reservation is ready for final approval.</span>
                          </div>
                        @elseif($reservation->current_workflow_status === 'pending_document_processing')
                          <div class="alert alert-warning mt-4" style="background-color: color-mix(in srgb, var(--color-golden-ember), white 90%); border-color: var(--color-golden-ember); color: var(--color-charcoal-ink);">
                            <i data-lucide="alert-triangle" class="w-5 h-5" style="color: var(--color-golden-ember);"></i>
                            <span>Document is being processed or awaiting classification.</span>
                          </div>
                        @endif
                    @else
                        <div class="text-center py-8" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                            <i data-lucide="check-circle" class="w-12 h-12 mx-auto mb-4" style="color: var(--color-regal-navy); opacity: 0.5;"></i>
                            <p>No workflow tasks initiated yet for this reservation.</p>
                        </div>
                    @endif
                </div>

                <!-- Approval Actions for Pending Reservations -->
                @if($reservation->status === 'pending' && $reservation->current_workflow_status === 'ready_for_approval')
                  <div class="border-t pt-6" style="border-color: var(--color-snow-mist);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--color-charcoal-ink);">Manual Approval Actions</h3>
                    <div class="flex gap-3">
                      <form action="{{ route('facility_reservations.approve', $reservation->id) }}" method="POST" class="flex-1">
                        @csrf
                        <div class="flex gap-2">
                          <input type="text" name="remarks" class="input input-bordered flex-1" placeholder="Approval remarks (optional)" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                          <button type="submit" class="btn" style="background-color: var(--color-modern-teal); color: var(--color-white); border-color: var(--color-modern-teal);">
                            <i data-lucide="check" class="w-4 h-4 mr-2"></i>Approve
                          </button>
                        </div>
                      </form>
                      <form action="{{ route('facility_reservations.deny', $reservation->id) }}" method="POST" class="flex-1">
                        @csrf
                        <div class="flex gap-2">
                          <input type="text" name="remarks" class="input input-bordered flex-1" placeholder="Denial remarks (optional)" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                          <button type="submit" class="btn" style="background-color: var(--color-danger-red); color: var(--color-white); border-color: var(--color-danger-red);">
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
            <div class="card bg-white shadow-xl mb-6" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4 flex items-center" style="color: var(--color-charcoal-ink);">
                  <i data-lucide="brain" class="w-5 h-5 mr-2" style="color: var(--color-regal-navy);"></i>
                  AI Analysis
                </h3>

                @php
                  $documentTask = $reservation->tasks->where('task_type', 'document_classification')->first();
                  $aiClassification = $documentTask ? ($documentTask->details['ai_classification'] ?? null) : null;
                @endphp

                @if($aiClassification)
                  <div class="space-y-4">
                    <div>
                      <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Document Category</label>
                      <div class="mt-1">
                        <span class="badge" style="background-color: var(--color-regal-navy); color: var(--color-white);">{{ ucfirst($aiClassification['category'] ?? 'Unknown') }}</span>
                        @if($aiClassification['fallback'] ?? false)
                          <span class="badge badge-xs ml-2" style="background-color: var(--color-golden-ember); color: var(--color-white);">Fallback</span>
                        @endif
                      </div>
                    </div>

                    @if($aiClassification['summary'] ?? null)
                      <div>
                        <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Summary</label>
                        <p class="mt-1 text-sm text-gray-700" style="color: var(--color-charcoal-ink);">{{ $aiClassification['summary'] }}</p>
                      </div>
                    @endif

                    @if($aiClassification['key_info'] ?? null)
                      <div>
                        <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Key Information</label>
                        <p class="mt-1 text-sm text-gray-700" style="color: var(--color-charcoal-ink);">{{ $aiClassification['key_info'] }}</p>
                      </div>
                    @endif

                    @if($aiClassification['legal_implications'] ?? null)
                      <div>
                        <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Legal Implications</label>
                        <p class="mt-1 text-sm text-gray-700" style="color: var(--color-charcoal-ink);">{{ $aiClassification['legal_implications'] }}</p>
                      </div>
                    @endif

                    @if($aiClassification['compliance_status'] ?? null)
                      <div>
                        <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Compliance Status</label>
                        <div class="mt-1">
                          @php
                            $status = $aiClassification['compliance_status'];
                            $statusClass = $status === 'compliant' ? 'success' : ($status === 'non-compliant' ? 'error' : 'warning');
                          @endphp
                          <span class="badge badge-{{ $statusClass }}" style="background-color: var(--color-charcoal-ink); color: var(--color-white);">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                        </div>
                      </div>
                    @endif

                    @if($aiClassification['tags'] ?? null)
                      <div>
                        <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Tags</label>
                        <div class="mt-1 flex flex-wrap gap-1">
                          @foreach($aiClassification['tags'] as $tag)
                            <span class="badge badge-outline badge-sm" style="border-color: var(--color-regal-navy); color: var(--color-regal-navy);">{{ $tag }}</span>
                          @endforeach
                        </div>
                      </div>
                    @endif
                  </div>
                @elseif($reservation->ai_error)
                  <div class="alert mb-2" style="background-color: color-mix(in srgb, var(--color-danger-red), white 90%); border-color: var(--color-danger-red); color: var(--color-charcoal-ink);">
                    <i data-lucide="alert-triangle" class="w-5 h-5" style="color: var(--color-danger-red);"></i>
                    <div>
                      <h4 class="font-bold" style="color: var(--color-charcoal-ink);">AI Analysis Error</h4>
                      <p class="text-sm" style="color: var(--color-charcoal-ink);">{{ $reservation->ai_error }}</p>
                    </div>
                  </div>
                @else
                  <div class="text-center py-8">
                    <i data-lucide="file-x" class="w-12 h-12 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
                    <p class="text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">No document uploaded for AI analysis</p>
                  </div>
                @endif
              </div>
            </div>

            <!-- Document Card -->
            @if($reservation->document_path)
              <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
                <div class="card-body">
                  <h3 class="card-title text-lg mb-4 flex items-center" style="color: var(--color-charcoal-ink);">
                    <i data-lucide="file-text" class="w-5 h-5 mr-2" style="color: var(--color-regal-navy);"></i>
                    Supporting Document
                  </h3>
                  
                  <div class="flex items-center gap-3 p-3 rounded-lg border" style="background-color: var(--color-snow-mist); border-color: color-mix(in srgb, var(--color-snow-mist), black 10%);">
                    <i data-lucide="file" class="w-8 h-8" style="color: var(--color-regal-navy);"></i>
                    <div class="flex-1">
                      <p class="font-medium text-sm" style="color: var(--color-charcoal-ink);">{{ basename($reservation->document_path) }}</p>
                      <p class="text-xs text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">Uploaded document</p>
                    </div>
                    <a href="{{ Storage::url($reservation->document_path) }}" 
                       target="_blank" 
                       class="btn btn-sm btn-outline" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">
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
      updateDateTime();
      
      // Update time every second
      setInterval(updateDateTime, 1000);
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
