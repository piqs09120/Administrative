<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Facility Damage Case #{{ $case->case_number }} - Legal Management</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
</head>
<body class="bg-base-100">
  <div class="flex h-screen overflow-hidden">
    @include('partials.sidebarr')
    <div class="flex flex-col flex-1 overflow-hidden">
      @include('partials.navbar')
      <main class="flex-1 overflow-y-auto p-8">
        <!-- Back Button -->
        <div class="mb-4">
          <a href="{{ route('legal.facility_damage_cases') }}" class="btn btn-sm btn-ghost">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back to Cases
          </a>
        </div>

        <!-- Page Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold" style="color: var(--color-charcoal-ink);">Case #{{ $case->case_number }}</h1>
              <p class="text-gray-600 mt-2">{{ $case->case_title }}</p>
            </div>
            <div class="flex gap-2">
              @php
                $priorityColors = [
                  'urgent' => 'badge-error',
                  'high' => 'badge-warning',
                  'medium' => 'badge-info',
                  'low' => 'badge-success',
                ];
                $statusColors = [
                  'pending' => 'badge-warning',
                  'ongoing' => 'badge-info',
                  'completed' => 'badge-success',
                  'rejected' => 'badge-error',
                ];
                $priorityBadge = $priorityColors[$case->priority] ?? 'badge-neutral';
                $statusBadge = $statusColors[$case->status] ?? 'badge-neutral';
              @endphp
              <span class="badge badge-lg {{ $priorityBadge }}">{{ ucfirst($case->priority) }} Priority</span>
              <span class="badge badge-lg {{ $statusBadge }}">{{ ucfirst($case->status) }}</span>
            </div>
          </div>
        </div>
        <div class="border-b border-gray-200 mb-6"></div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Case Description -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-4">
                  <i data-lucide="file-text" class="w-5 h-5"></i>
                  Case Description
                </h2>
                <div class="prose max-w-none">
                  <pre class="whitespace-pre-wrap text-sm text-gray-700">{{ $case->case_description }}</pre>
                </div>
              </div>
            </div>

            <!-- Facility Reservation Details -->
            @if($reservation)
              <div class="card bg-white shadow-xl">
                <div class="card-body">
                  <h2 class="card-title text-xl mb-4">
                    <i data-lucide="building" class="w-5 h-5"></i>
                    Facility Reservation Details
                  </h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="text-sm font-semibold text-gray-600">Facility</label>
                      <p class="text-gray-900 font-medium">{{ $reservation->facility->name }}</p>
                      @if($reservation->facility->location)
                        <p class="text-sm text-gray-500">{{ $reservation->facility->location }}</p>
                      @endif
                    </div>
                    <div>
                      <label class="text-sm font-semibold text-gray-600">Reservation ID</label>
                      <p class="text-gray-900 font-medium">#{{ $reservation->id }}</p>
                    </div>
                    <div>
                      <label class="text-sm font-semibold text-gray-600">Reserved By</label>
                      <p class="text-gray-900 font-medium">{{ $reservation->reserver->name ?? 'Unknown' }}</p>
                      @if($reservation->reserver)
                        <p class="text-sm text-gray-500">{{ $reservation->reserver->email ?? '' }}</p>
                      @endif
                    </div>
                    <div>
                      <label class="text-sm font-semibold text-gray-600">Reservation Period</label>
                      <p class="text-gray-900 font-medium">
                        {{ $reservation->start_time->format('M d, Y h:i A') }}<br>
                        to {{ $reservation->end_time->format('M d, Y h:i A') }}
                      </p>
                    </div>
                    <div>
                      <label class="text-sm font-semibold text-gray-600">Purpose</label>
                      <p class="text-gray-900">{{ $reservation->purpose ?? 'N/A' }}</p>
                    </div>
                    <div>
                      <label class="text-sm font-semibold text-gray-600">Status</label>
                      <p class="text-gray-900 font-medium">{{ ucfirst($reservation->status) }}</p>
                    </div>
                  </div>
                </div>
              </div>
            @endif

            <!-- Damage Information -->
            @php
              $metadata = $case->metadata ?? [];
              $damageCost = $metadata['damage_cost'] ?? $reservation->damage_cost ?? 0;
              $inspectionNotes = $metadata['inspection_notes'] ?? $reservation->inspection_notes ?? null;
            @endphp
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-4">
                  <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                  Damage Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="text-sm font-semibold text-gray-600">Estimated Damage Cost</label>
                    <p class="text-2xl font-bold text-red-600">₱{{ number_format($damageCost, 2) }}</p>
                  </div>
                  @if($reservation && $reservation->inspected_at)
                    <div>
                      <label class="text-sm font-semibold text-gray-600">Inspection Date</label>
                      <p class="text-gray-900 font-medium">{{ $reservation->inspected_at->format('M d, Y h:i A') }}</p>
                    </div>
                  @endif
                  @if($reservation && $reservation->inspected_by)
                    @php
                      $inspector = \App\Models\User::find($reservation->inspected_by);
                    @endphp
                    <div>
                      <label class="text-sm font-semibold text-gray-600">Inspected By</label>
                      <p class="text-gray-900 font-medium">{{ $inspector->name ?? 'Unknown' }}</p>
                    </div>
                  @endif
                </div>
                @if($inspectionNotes)
                  <div class="mt-4">
                    <label class="text-sm font-semibold text-gray-600">Inspection Notes</label>
                    <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                      <p class="text-gray-700 whitespace-pre-wrap">{{ $inspectionNotes }}</p>
                    </div>
                  </div>
                @endif
              </div>
            </div>

            <!-- Evidence Documents -->
            @if($case->documents && $case->documents->count() > 0)
              <div class="card bg-white shadow-xl">
                <div class="card-body">
                  <h2 class="card-title text-xl mb-4">
                    <i data-lucide="paperclip" class="w-5 h-5"></i>
                    Evidence Documents
                  </h2>
                  <div class="space-y-2">
                    @foreach($case->documents as $document)
                      <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                          <i data-lucide="file" class="w-5 h-5 text-gray-400"></i>
                          <div>
                            <p class="font-medium text-gray-900">{{ $document->title }}</p>
                            <p class="text-sm text-gray-500">{{ $document->category ?? 'Evidence' }}</p>
                          </div>
                        </div>
                        <a href="{{ route('legal.documents.show', $document->id) }}" class="btn btn-sm btn-ghost">
                          View
                        </a>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            @endif
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Case Information -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4">Case Information</h3>
                <div class="space-y-3">
                  <div>
                    <label class="text-sm font-semibold text-gray-600">Case Number</label>
                    <p class="text-gray-900 font-medium">{{ $case->case_number }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-semibold text-gray-600">Case Type</label>
                    <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $case->case_type)) }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-semibold text-gray-600">Created Date</label>
                    <p class="text-gray-900">{{ $case->created_at->format('M d, Y h:i A') }}</p>
                  </div>
                  @if($case->incident_date)
                    <div>
                      <label class="text-sm font-semibold text-gray-600">Incident Date</label>
                      <p class="text-gray-900">{{ $case->incident_date->format('M d, Y h:i A') }}</p>
                    </div>
                  @endif
                  @if($case->incident_location)
                    <div>
                      <label class="text-sm font-semibold text-gray-600">Incident Location</label>
                      <p class="text-gray-900">{{ $case->incident_location }}</p>
                    </div>
                  @endif
                  @if($case->assignedTo)
                    <div>
                      <label class="text-sm font-semibold text-gray-600">Assigned To</label>
                      <p class="text-gray-900">{{ $case->assignedTo->employee_name ?? 'Unassigned' }}</p>
                    </div>
                  @endif
                  @if($case->createdBy)
                    <div>
                      <label class="text-sm font-semibold text-gray-600">Created By</label>
                      <p class="text-gray-900">{{ $case->createdBy->employee_name ?? 'System' }}</p>
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4">Quick Actions</h3>
                <div class="space-y-2">
                  <a href="{{ route('legal.cases.show', $case->id) }}" class="btn btn-primary btn-sm w-full">
                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>View Full Case Details
                  </a>
                  @if($reservation)
                    <a href="{{ route('facility_reservations.show', $reservation->id) }}" class="btn btn-outline btn-sm w-full">
                      <i data-lucide="building" class="w-4 h-4 mr-2"></i>View Reservation
                    </a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    lucide.createIcons();
  </script>
</body>
</html>



