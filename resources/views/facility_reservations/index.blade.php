<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Facility Reservations - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
</head>
<body class="bg-gray-50">
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

        <!-- Page Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
              <a href="{{ route('facilities.index') }}" class="btn btn-ghost btn-sm" title="Back to Facilities">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
              </a>
              <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Facility Reservations</h1>
                <p class="text-gray-600">Manage and track facility reservation requests</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <a href="{{ route('facility_reservations.user_history') }}" class="btn btn-outline">
                <i data-lucide="history" class="w-4 h-4 mr-2"></i>My History
              </a>
              <a href="{{ route('facility_reservations.admin_analytics') }}" class="btn btn-outline">
                <i data-lucide="bar-chart" class="w-4 h-4 mr-2"></i>Analytics
              </a>
              <button type="button" id="openReserveFacilityModal" class="btn btn-primary">
                <i data-lucide="calendar-plus" class="w-4 h-4 mr-2"></i>Reserve Facility
              </button>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Reservations -->
          <div class="card bg-white shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-primary cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                  <div class="bg-primary text-white rounded-full w-12 h-12">
                    <i data-lucide="calendar" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-primary badge-outline group-hover:badge-primary transition-colors duration-300">Reservations</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-primary justify-center mb-2 group-hover:text-primary-focus transition-colors duration-300">{{ $reservations->count() }}</h2>
                <p class="text-gray-600 group-hover:text-gray-800 transition-colors duration-300">Total Reservations</p>
              </div>
            </div>
          </div>

          <!-- Auto-Approved -->
          <div class="card bg-white shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-success cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                  <div class="bg-success text-white rounded-full w-12 h-12">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-success badge-outline group-hover:badge-success transition-colors duration-300">Auto-Approved</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-success justify-center mb-2 group-hover:text-success-focus transition-colors duration-300">{{ $reservations->where('auto_approved_at', '!=', null)->count() }}</h2>
                <p class="text-gray-600 group-hover:text-gray-800 transition-colors duration-300">Auto-Approved</p>
              </div>
            </div>
          </div>

          <!-- Documents Awaiting Processing -->
          <div class="card bg-white shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-info cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                  <div class="bg-info text-white rounded-full w-12 h-12">
                    <i data-lucide="brain" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-info badge-outline group-hover:badge-info transition-colors duration-300">Processing</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-info justify-center mb-2 group-hover:text-info-focus transition-colors duration-300">{{ $reservations->where('current_workflow_status', 'pending_document_processing')->count() }}</h2>
                <p class="text-gray-600 group-hover:text-gray-800 transition-colors duration-300">Documents Awaiting Processing</p>
              </div>
            </div>
          </div>

          <!-- Pending Tasks -->
          <div class="card bg-white shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-neutral cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                  <div class="bg-neutral text-white rounded-full w-12 h-12">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-neutral badge-outline group-hover:badge-neutral transition-colors duration-300">Pending</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-neutral justify-center mb-2 group-hover:text-neutral-focus transition-colors duration-300">{{ $reservations->where('current_workflow_status', 'pending_tasks')->count() }}</h2>
                <p class="text-gray-600 group-hover:text-gray-800 transition-colors duration-300">Pending Tasks</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Facility Reservations Table -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
              <i data-lucide="calendar" class="w-6 h-6 text-blue-500 mr-3"></i>
              AI-Enhanced Reservation Directory
            </h3>
            <div class="flex items-center space-x-2">
              <span class="text-sm text-gray-500">Total: {{ $reservations->count() }} reservations</span>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
              <thead>
                <tr>
                  <th>Facility</th>
                  <th>Reserved By</th>
                  <th>Start</th>
                  <th>End</th>
                  <th>Status</th>
                  <th>Workflow Status</th>
                  <th>AI Analysis</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($reservations as $reservation)
                  <tr>
                    <td>
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                          <i data-lucide="building" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                          <div class="font-semibold">{{ $reservation->facility->name ?? 'N/A' }}</div>
                          <div class="text-sm text-gray-500">{{ $reservation->facility->location ?? 'N/A' }}</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                          <span class="text-sm font-medium text-gray-600">
                            {{ substr($reservation->reserver->name ?? 'N/A', 0, 1) }}
                          </span>
                        </div>
                        <span class="font-medium">{{ $reservation->reserver->name ?? 'N/A' }}</span>
                      </div>
                    </td>
                    <td>
                      <div class="text-sm">
                        <div class="font-medium">{{ \Carbon\Carbon::parse($reservation->start_time)->format('M d, Y') }}</div>
                        <div class="text-gray-600">{{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }}</div>
                      </div>
                    </td>
                    <td>
                      <div class="text-sm">
                        <div class="font-medium">{{ \Carbon\Carbon::parse($reservation->end_time)->format('M d, Y') }}</div>
                        <div class="text-gray-600">{{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}</div>
                      </div>
                    </td>
                    <td>
                      <div class="flex items-center gap-2">
                        @if($reservation->status === 'approved')
                          <span class="badge badge-success gap-1">
                            <i data-lucide="check" class="w-3 h-3"></i>
                            Approved
                          </span>
                          @if($reservation->auto_approved_at)
                            <span class="badge badge-info badge-xs">Auto</span>
                          @endif
                        @elseif($reservation->status === 'denied')
                          <span class="badge badge-error gap-1">
                            <i data-lucide="x" class="w-3 h-3"></i>
                            Denied
                          </span>
                        @else
                          <span class="badge badge-warning gap-1">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            Pending
                          </span>
                        @endif
                      </div>
                    </td>
                    <td>
                        @php
                            $workflowStatusClass = [
                                'submitted' => 'badge-neutral',
                                'pending_document_processing' => 'badge-warning',
                                'pending_tasks' => 'badge-info',
                                'ready_for_approval' => 'badge-success',
                                'approved' => 'badge-success',
                                'denied' => 'badge-error',
                            ][$reservation->current_workflow_status] ?? 'badge-ghost';
                        @endphp
                        <span class="badge {{ $workflowStatusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $reservation->current_workflow_status)) }}
                        </span>
                    </td>
                    <td>
                      @php
                        $documentTask = $reservation->tasks->where('task_type', 'document_classification')->first();
                        $aiClassification = $documentTask ? ($documentTask->details['ai_classification'] ?? null) : null;
                      @endphp
                      @if($aiClassification)
                        <div class="text-sm">
                          <div class="font-medium text-blue-600">
                            {{ ucfirst($aiClassification['category'] ?? 'Unknown') }}
                          </div>
                          @if($aiClassification['fallback'] ?? false)
                            <div class="text-xs text-gray-500">Fallback Analysis</div>
                          @endif
                        </div>
                      @elseif($reservation->ai_error)
                        <div class="text-sm text-red-600">
                          <i data-lucide="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                          AI Error
                        </div>
                      @else
                        <div class="text-sm text-gray-500">No Document</div>
                      @endif
                    </td>
                    <td>
                      <div class="flex items-center gap-2">
                        <a href="{{ route('facility_reservations.show', $reservation->id) }}" 
                           class="btn btn-sm btn-outline">
                          <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        
                        @if($reservation->current_workflow_status === 'ready_for_approval' && auth()->user()->role === 'administrator')
                          <form action="{{ route('facility_reservations.approve', $reservation->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                              <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                          </form>
                          
                          <form action="{{ route('facility_reservations.deny', $reservation->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-error">
                              <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                          </form>
                        @endif
                        
                        @if($reservation->document_path)
                          <a href="{{ Storage::url($reservation->document_path) }}" 
                             target="_blank" 
                             class="btn btn-sm btn-outline">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                          </a>
                        @endif
                        <form action="{{ route('facility_reservations.destroy', $reservation->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this reservation? This action cannot be undone.');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline btn-error">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center py-8">
                      <div class="flex flex-col items-center">
                        <i data-lucide="calendar-x" class="w-12 h-12 text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No Reservations Found</h3>
                        <p class="text-gray-500 mb-4">Start by creating your first facility reservation.</p>
                        <a href="{{ route('facility_reservations.create') }}" class="btn btn-primary">
                          <i data-lucide="calendar-plus" class="w-4 h-4 mr-2"></i>Create Reservation
                        </a>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <!-- Reserve Facility Modal -->
  <div id="reserveFacilityModal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl bg-white text-gray-800" data-theme="light" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="calendar-plus" class="w-6 h-6 text-blue-500"></i>
          Reserve a Facility
        </h3>
        <button id="closeReserveFacilityModal" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      @if($errors->any())
        <div class="alert alert-error mb-6">
          <i data-lucide="alert-circle" class="w-5 h-5"></i>
          <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form id="reserveFacilityForm" action="{{ route('facility_reservations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Facility Selection -->
        <div class="form-control mb-6">
          <label class="label">
            <span class="label-text font-semibold flex items-center gap-2">
              <i data-lucide="building" class="w-4 h-4 text-blue-500"></i>
              Facility *
            </span>
          </label>
          <select name="facility_id" class="select select-bordered w-full" required>
            <option value="">Select facility</option>
            @foreach($facilities as $facility)
              <option value="{{ $facility->id }}">{{ $facility->name }} ({{ $facility->location }})</option>
            @endforeach
          </select>
        </div>

        <!-- Date and Time Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>
                Start Time *
              </span>
            </label>
            <input type="datetime-local" name="start_time" class="input input-bordered w-full" required>
          </div>
          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                End Time *
              </span>
            </label>
            <input type="datetime-local" name="end_time" class="input input-bordered w-full" required>
          </div>
        </div>

        <!-- Purpose -->
        <div class="form-control mb-6">
          <label class="label">
            <span class="label-text font-semibold flex items-center gap-2">
              <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i>
              Purpose
            </span>
          </label>
          <textarea name="purpose" class="textarea textarea-bordered w-full h-24" placeholder="Enter purpose for reservation"></textarea>
        </div>

        <!-- Document Upload -->
        <div class="form-control mb-6">
          <label class="label">
            <span class="label-text font-semibold flex items-center gap-2">
              <i data-lucide="upload" class="w-4 h-4 text-blue-500"></i>
              Supporting Document (Optional)
            </span>
          </label>
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
            <input type="file" name="document" class="hidden" id="modal-document-upload" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
            <label for="modal-document-upload" class="cursor-pointer">
              <i data-lucide="upload-cloud" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
              <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
              <p class="text-sm text-gray-500">PDF, DOC, DOCX, TXT, JPG, PNG (Max 10MB)</p>
            </label>
          </div>
          <div id="modal-file-info" class="mt-2 text-sm text-gray-600 hidden">
            <i data-lucide="file" class="w-4 h-4 inline mr-1"></i>
            <span id="modal-file-name"></span>
          </div>
        </div>

        <div class="modal-action">
          <button type="button" class="btn btn-outline" id="cancelReserveFacility">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="calendar-plus" class="w-4 h-4 mr-2"></i>
            Submit Reservation
          </button>
        </div>
      </form>
    </div>
  </div>
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

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      updateDateTime();
      
      // Update time every second
      setInterval(updateDateTime, 1000);

      // Modal logic
      const modal = document.getElementById('reserveFacilityModal');
      const openBtn = document.getElementById('openReserveFacilityModal');
      const closeBtn = document.getElementById('closeReserveFacilityModal');
      const cancelBtn = document.getElementById('cancelReserveFacility');
      function openModal(){ modal.classList.add('modal-open'); }
      function closeModal(){ modal.classList.remove('modal-open'); }
      if (openBtn) openBtn.addEventListener('click', openModal);
      if (closeBtn) closeBtn.addEventListener('click', closeModal);
      if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
      if (modal) modal.addEventListener('click', function(e){ if(e.target === modal) closeModal(); });

      // File preview inside modal
      const fileInput = document.getElementById('modal-document-upload');
      if (fileInput){
        fileInput.addEventListener('change', function(e){
          const file = e.target.files[0];
          const info = document.getElementById('modal-file-info');
          const name = document.getElementById('modal-file-name');
          if (file){ name.textContent = file.name; info.classList.remove('hidden'); }
          else { info.classList.add('hidden'); }
        });
      }
    });
  </script>
</body>
</html>
