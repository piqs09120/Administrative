<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Legal Review - Soliera</title>
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
          <a href="{{ route('facility_reservations.show', $reservation->id) }}" class="btn btn-ghost btn-sm mr-4" title="Back to Reservation Details">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
          </a>
          <h1 class="text-3xl font-bold text-gray-800">Legal Review</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Reservation Summary -->
          <div class="card bg-white shadow-xl">
            <div class="card-body">
              <h2 class="card-title text-xl mb-4 flex items-center">
                <i data-lucide="building" class="w-5 h-5 text-blue-500 mr-2"></i>
                Reservation Summary
              </h2>
              
              <div class="space-y-4">
                <div>
                  <label class="text-sm font-medium text-gray-500">Facility</label>
                  <p class="font-medium">{{ $reservation->facility->name ?? 'N/A' }}</p>
                </div>
                
                <div>
                  <label class="text-sm font-medium text-gray-500">Reserved By</label>
                  <p class="font-medium">{{ $reservation->reserver->name ?? 'N/A' }}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="text-sm font-medium text-gray-500">Start Time</label>
                    <p class="text-sm">{{ \Carbon\Carbon::parse($reservation->start_time)->format('M d, Y H:i') }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-500">End Time</label>
                    <p class="text-sm">{{ \Carbon\Carbon::parse($reservation->end_time)->format('M d, Y H:i') }}</p>
                  </div>
                </div>
                
                <div>
                  <label class="text-sm font-medium text-gray-500">Purpose</label>
                  <p class="text-sm">{{ $reservation->purpose ?? 'No purpose specified' }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- AI Analysis -->
          <div class="card bg-white shadow-xl">
            <div class="card-body">
              <h2 class="card-title text-xl mb-4 flex items-center">
                <i data-lucide="brain" class="w-5 h-5 text-purple-500 mr-2" style="color: var(--color-regal-navy);"></i>
                AI Legal Analysis
              </h2>
              
              @php
                $aiClassification = $legalTask->details['ai_classification'] ?? null;
              @endphp
              @if($aiClassification)
                <div class="space-y-4">
                  <div>
                    <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Document Category</label>
                    <p class="text-sm">
                      <span class="badge badge-primary" style="background-color: var(--color-regal-navy); color: var(--color-white);">{{ ucfirst($aiClassification['category'] ?? 'Unknown') }}</span>
                    </p>
                  </div>

                  @if($aiClassification['legal_implications'] ?? null)
                    <div>
                      <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Legal Implications</label>
                      <p class="text-sm text-gray-700" style="color: var(--color-charcoal-ink);">{{ $aiClassification['legal_implications'] }}</p>
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
                        <span class="badge badge-{{ $statusClass }}" style="background-color: var(--color-regal-navy); color: var(--color-white);">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                      </div>
                    </div>
                  @endif

                  @if($aiClassification['summary'] ?? null)
                    <div>
                      <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Document Summary</label>
                      <p class="text-sm text-gray-700" style="color: var(--color-charcoal-ink);">{{ $aiClassification['summary'] }}</p>
                    </div>
                  @endif

                  @if($aiClassification['key_info'] ?? null)
                    <div>
                      <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Key Information</label>
                      <p class="text-sm text-gray-700" style="color: var(--color-charcoal-ink);">{{ $aiClassification['key_info'] }}</p>
                    </div>
                  @endif
                </div>
              @else
                <div class="text-center py-8">
                  <i data-lucide="file-x" class="w-12 h-12 text-gray-400 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
                  <p class="text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">No AI analysis available for this document.</p>
                </div>
              @endif
            </div>
          </div>
        </div>

        <!-- Document View -->
        @if($reservation->document_path)
          <div class="card bg-white shadow-xl mt-6" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
            <div class="card-body">
              <h2 class="card-title text-xl mb-4 flex items-center">
                <i data-lucide="file-text" class="w-5 h-5 text-blue-500 mr-2" style="color: var(--color-regal-navy);"></i>
                Supporting Document
              </h2>
              
              <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg mb-4" style="background-color: var(--color-snow-mist); border-color: color-mix(in srgb, var(--color-snow-mist), black 10%);">
                <i data-lucide="file" class="w-8 h-8 text-blue-500" style="color: var(--color-regal-navy);"></i>
                <div class="flex-1">
                  <p class="font-medium text-sm" style="color: var(--color-charcoal-ink);">{{ basename($reservation->document_path) }}</p>
                  <p class="text-xs text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">Supporting documentation</p>
                </div>
                <a href="{{ Storage::url($reservation->document_path) }}" 
                   target="_blank" 
                   class="btn btn-sm btn-outline" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">
                  <i data-lucide="external-link" class="w-4 h-4"></i>
                  View Document
                </a>
              </div>
            </div>
          </div>
        @endif

        <!-- Legal Review Actions -->
        <div class="card bg-white shadow-xl mt-6" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
          <div class="card-body">
            <h2 class="card-title text-xl mb-4 flex items-center">
              <i data-lucide="scale" class="w-5 h-5 text-green-500 mr-2" style="color: var(--color-golden-ember);"></i>
              Legal Review Decision
            </h2>
            
            @if($legalTask->status === 'pending')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Legal Approve -->
                  <form action="{{ route('facility_reservations.legal_approve', ['id' => $reservation->id, 'task_id' => $legalTask->id]) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                      <label class="label">
                        <span class="label-text font-medium" style="color: var(--color-charcoal-ink);">Legal Comment (Optional)</span>
                      </label>
                      <textarea name="legal_comment" class="textarea textarea-bordered w-full" rows="3" 
                                placeholder="Add any legal notes or comments..." style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-full" style="background-color: var(--color-modern-teal); color: var(--color-white); border-color: var(--color-modern-teal);">
                      <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                      Approve - Legal Review Complete
                    </button>
                  </form>

                  <!-- Legal Flag -->
                  <form action="{{ route('facility_reservations.legal_flag', ['id' => $reservation->id, 'task_id' => $legalTask->id]) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                      <label class="label">
                        <span class="label-text font-medium" style="color: var(--color-charcoal-ink);">Reason for Flagging (Required)</span>
                      </label>
                      <textarea name="legal_comment" class="textarea textarea-bordered w-full" rows="3" 
                                placeholder="Explain why this reservation is being flagged..." required style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);"></textarea>
                    </div>
                    <button type="submit" class="btn btn-error w-full" style="background-color: var(--color-danger-red); color: var(--color-white); border-color: var(--color-danger-red);">
                      <i data-lucide="flag" class="w-4 h-4 mr-2"></i>
                      Flag - Legal Issues Found
                    </button>
                  </form>
                </div>
            @else
                <div class="alert alert-info" style="background-color: color-mix(in srgb, var(--color-modern-teal), white 90%); border-color: var(--color-modern-teal); color: var(--color-charcoal-ink);">
                  <i data-lucide="info" class="w-5 h-5" style="color: var(--color-modern-teal);"></i>
                  <div>
                    <h4 class="font-bold" style="color: var(--color-charcoal-ink);">Legal Review {{ ucfirst($legalTask->status) }}</h4>
                    <p class="text-sm" style="color: var(--color-charcoal-ink);">This legal review task has already been {{ $legalTask->status }}.
                    @if($legalTask->completed_at) Reviewed on {{ \Carbon\Carbon::parse($legalTask->completed_at)->format('M d, Y H:i') }} by {{ $legalTask->completer->name ?? 'System' }}. @endif</p>
                    @if($legalTask->details['comment'] ?? null)<p class="text-sm mt-1" style="color: var(--color-charcoal-ink);"><strong>Comment:</strong> {{ $legalTask->details['comment'] }}</p>@endif
                  </div>
                </div>
            @endif
            
            <div class="alert alert-info mt-4" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 90%); border-color: var(--color-regal-navy); color: var(--color-charcoal-ink);">
              <i data-lucide="info" class="w-5 h-5" style="color: var(--color-regal-navy);"></i>
              <div>
                <h4 class="font-bold" style="color: var(--color-charcoal-ink);">Legal Review Guidelines</h4>
                <p class="text-sm" style="color: var(--color-charcoal-ink);">
                  • Approve if the document complies with legal requirements and poses no legal risks
                  • Flag if there are legal concerns, compliance issues, or further review needed
                  • Your decision will automatically update the reservation status
                </p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
</body>
</html>

