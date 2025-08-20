<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Facility Reservations - Soliera</title>
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

        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-800">Facility Reservations</h2>
          <a href="{{ route('facility_reservations.create') }}" class="btn btn-primary">
            <i data-lucide="calendar-plus" class="w-4 h-4 mr-2"></i>Reserve Facility
          </a>
        </div>

        <!-- AI Processing Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-blue-100 text-sm">Total Reservations</p>
                <p class="text-2xl font-bold">{{ $reservations->count() }}</p>
              </div>
              <i data-lucide="calendar" class="w-8 h-8 text-blue-200"></i>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-green-100 text-sm">Auto-Approved</p>
                <p class="text-2xl font-bold">{{ $reservations->where('auto_approved_at', '!=', null)->count() }}</p>
              </div>
              <i data-lucide="check-circle" class="w-8 h-8 text-green-200"></i>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-purple-100 text-sm">Documents Awaiting Processing</p>
                <p class="text-2xl font-bold">{{ $reservations->where('current_workflow_status', 'pending_document_processing')->count() }}</p>
              </div>
              <i data-lucide="brain" class="w-8 h-8 text-purple-200"></i>
            </div>
          </div>
          
          <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-orange-100 text-sm">Pending Tasks</p>
                <p class="text-2xl font-bold">{{ $reservations->where('current_workflow_status', 'pending_tasks')->count() }}</p>
              </div>
              <i data-lucide="clock" class="w-8 h-8 text-orange-200"></i>
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
    });
  </script>
</body>
</html>
