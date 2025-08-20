<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Reservation Visitors - Soliera (VM)</title>
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
          <a href="{{ route('facility_reservations.show', $reservation->id) }}" class="btn btn-ghost btn-sm mr-4" style="color: var(--color-regal-navy);">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>Back to Reservation
          </a>
          <h1 class="text-3xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Manage Visitors for Reservation #{{ $reservation->id }}</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Left Column: Reservation & Task Details -->
          <div class="lg:col-span-2">
            <div class="card bg-white shadow-xl mb-6" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <h2 class="card-title text-2xl flex items-center" style="color: var(--color-charcoal-ink);">
                  <i data-lucide="building" class="w-6 h-6 mr-3" style="color: var(--color-regal-navy);"></i>
                  Reservation Details
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                  <div>
                    <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Facility</label>
                    <p class="text-gray-900" style="color: var(--color-charcoal-ink);">{{ $reservation->facility->name ?? 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Purpose</label>
                    <p class="text-gray-900" style="color: var(--color-charcoal-ink);">{{ $reservation->purpose ?? 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Time</label>
                    <p class="text-gray-900" style="color: var(--color-charcoal-ink);">{{ \Carbon\Carbon::parse($reservation->start_time)->format('M d, Y H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Reserved By</label>
                    <p class="text-gray-900" style="color: var(--color-charcoal-ink);">{{ $reservation->reserver->name ?? 'N/A' }}</p>
                  </div>
                </div>

                <div class="border-t pt-6 mt-6" style="border-color: var(--color-snow-mist);">
                    <h3 class="text-lg font-semibold mb-4 flex items-center" style="color: var(--color-charcoal-ink);">
                        <i data-lucide="users" class="w-5 h-5 mr-2" style="color: var(--color-regal-navy);"></i>
                        Visitor Coordination Task
                    </h3>
                    <ul class="space-y-3">
                        @php
                            $taskStatusClass = [
                                'pending' => 'badge-warning',
                                'in_progress' => 'badge-info',
                                'completed' => 'badge-success',
                                'flagged' => 'badge-error',
                            ][$visitorTask->status] ?? 'badge-info';
                        @endphp
                        <li class="flex items-center gap-3 p-3 rounded-lg border" style="background-color: var(--color-snow-mist); border-color: color-mix(in srgb, var(--color-snow-mist), black 10%);">
                            <i data-lucide="users" class="w-5 h-5" style="color: var(--color-regal-navy);"></i>
                            <div class="flex-1">
                                <p class="font-medium text-sm" style="color: var(--color-charcoal-ink);">Visitor Coordination</p>
                                <p class="text-xs text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                                    Assigned to: {{ $visitorTask->assigned_to_module }}
                                    @if($visitorTask->completed_at)
                                        <span class="ml-2">(Completed {{ \Carbon\Carbon::parse($visitorTask->completed_at)->format('M d, Y H:i') }})</span>
                                    @endif
                                </p>
                            </div>
                            <span class="badge {{ $taskStatusClass }}" style="color: var(--color-white);">{{ ucfirst(str_replace('_', ' ', $visitorTask->status)) }}</span>
                        </li>
                    </ul>

                    <!-- Action Buttons for Visitor Management -->
                    <div class="mt-6">
                        @if($visitorTask->status === 'pending' && $visitors->isEmpty())
                            <form action="{{ route('visitor.perform_extraction_from_reservation', $reservation->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="btn btn-primary" style="background-color: var(--color-modern-teal); color: var(--color-white); border-color: var(--color-modern-teal);">
                                    <i data-lucide="file-search" class="w-4 h-4 mr-2"></i>Extract Visitor Data
                                </button>
                            </form>
                        @elseif($visitorTask->status === 'in_progress' || ($visitorTask->status === 'pending' && $visitors->isNotEmpty()))
                            <form action="{{ route('visitor.perform_approval_from_reservation', $reservation->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="btn btn-success" style="background-color: var(--color-modern-teal); color: var(--color-white); border-color: var(--color-modern-teal);">
                                    <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>Approve Visitors & Generate Passes
                                </button>
                            </form>
                        @else
                             <div class="alert alert-info mt-4" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 90%); border-color: var(--color-regal-navy); color: var(--color-charcoal-ink);">
                                <i data-lucide="info" class="w-5 h-5" style="color: var(--color-regal-navy);"></i>
                                <span>Visitor coordination task is {{ str_replace('_', ' ', $visitorTask->status) }}.</span>
                            </div>
                        @endif
                    </div>
              </div>
            </div>

            <!-- Extracted Visitors List -->
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <h2 class="card-title text-2xl flex items-center" style="color: var(--color-charcoal-ink);">
                  <i data-lucide="users" class="w-6 h-6 mr-3" style="color: var(--color-modern-teal);"></i>
                  Extracted Visitors
                </h2>
                <div class="mt-4 space-y-4">
                    @forelse($visitors as $visitor)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200" style="background-color: var(--color-snow-mist); border-color: var(--color-snow-mist);">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900" style="color: var(--color-charcoal-ink);">{{ $visitor->name }}</p>
                                    <p class="text-sm text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $visitor->company ?? 'N/A' }}</p>
                                </div>
                                <span class="badge badge-lg {{ $visitor->status === 'approved' ? 'badge-success' : 'badge-warning' }}" style="color: var(--color-white);">
                                    {{ ucfirst($visitor->status) }}
                                </span>
                            </div>
                            @if($visitor->facilityReservation && $visitor->facilityReservation->digital_pass_data)
                                @php
                                    $digitalPass = collect($visitor->facilityReservation->digital_pass_data)->where('visitor_id', $visitor->id)->first();
                                @endphp
                                @if($digitalPass)
                                    <div class="mt-3 text-sm" style="color: var(--color-charcoal-ink); opacity: 0.9;">
                                        <p><strong>Pass ID:</strong> {{ $digitalPass['pass_id'] }}</p>
                                        <p><strong>Valid:</strong> {{ \Carbon\Carbon::parse($digitalPass['valid_from'])->format('M d, H:i') }} - {{ \Carbon\Carbon::parse($digitalPass['valid_until'])->format('M d, H:i') }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                            <i data-lucide="users" class="w-12 h-12 text-gray-400 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
                            <p>No visitors extracted yet for this reservation.</p>
                        </div>
                    @endforelse
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: AI Analysis -->
          <div class="lg:col-span-1">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4 flex items-center" style="color: var(--color-charcoal-ink);">
                  <i data-lucide="brain" class="w-5 h-5 mr-2" style="color: var(--color-regal-navy);"></i>
                  AI Document Analysis (Source)
                </h3>
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
                @else
                  <div class="text-center py-8">
                    <i data-lucide="file-x" class="w-12 h-12 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
                    <p class="text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">No AI analysis data available.</p>
                  </div>
                @endif
              </div>
            </div>
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

    document.addEventListener('DOMContentLoaded', function() {
      updateDateTime();
      setInterval(updateDateTime, 1000);
    });
  </script>
</body>
</html>
