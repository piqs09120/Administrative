<!DOCTYPE html>
<html lang="en" data-theme="light">
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
          <a href="{{ route('facility_reservations.show', $reservation->id) }}" class="btn btn-ghost btn-sm mr-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back
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
                <i data-lucide="brain" class="w-5 h-5 text-purple-500 mr-2"></i>
                AI Legal Analysis
              </h2>
              
              @if($reservation->ai_classification)
                <div class="space-y-4">
                  <div>
                    <label class="text-sm font-medium text-gray-500">Document Category</label>
                    <p class="text-sm">
                      <span class="badge badge-primary">{{ ucfirst($reservation->getAiClassification('category') ?? 'Unknown') }}</span>
                    </p>
                  </div>

                  @if($reservation->getAiClassification('legal_implications'))
                    <div>
                      <label class="text-sm font-medium text-gray-500">Legal Implications</label>
                      <p class="text-sm text-gray-700">{{ $reservation->getAiClassification('legal_implications') }}</p>
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

                  @if($reservation->getAiClassification('summary'))
                    <div>
                      <label class="text-sm font-medium text-gray-500">Document Summary</label>
                      <p class="text-sm text-gray-700">{{ $reservation->getAiClassification('summary') }}</p>
                    </div>
                  @endif

                  @if($reservation->getAiClassification('key_info'))
                    <div>
                      <label class="text-sm font-medium text-gray-500">Key Information</label>
                      <p class="text-sm text-gray-700">{{ $reservation->getAiClassification('key_info') }}</p>
                    </div>
                  @endif
                </div>
              @else
                <div class="text-center py-8">
                  <i data-lucide="file-x" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                  <p class="text-gray-500">No AI analysis available</p>
                </div>
              @endif
            </div>
          </div>
        </div>

        <!-- Document View -->
        @if($reservation->document_path)
          <div class="card bg-white shadow-xl mt-6">
            <div class="card-body">
              <h2 class="card-title text-xl mb-4 flex items-center">
                <i data-lucide="file-text" class="w-5 h-5 text-blue-500 mr-2"></i>
                Supporting Document
              </h2>
              
              <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg mb-4">
                <i data-lucide="file" class="w-8 h-8 text-blue-500"></i>
                <div class="flex-1">
                  <p class="font-medium text-sm">{{ basename($reservation->document_path) }}</p>
                  <p class="text-xs text-gray-500">Supporting documentation</p>
                </div>
                <a href="{{ Storage::url($reservation->document_path) }}" 
                   target="_blank" 
                   class="btn btn-sm btn-outline">
                  <i data-lucide="external-link" class="w-4 h-4"></i>
                  View Document
                </a>
              </div>
            </div>
          </div>
        @endif

        <!-- Legal Review Actions -->
        <div class="card bg-white shadow-xl mt-6">
          <div class="card-body">
            <h2 class="card-title text-xl mb-4 flex items-center">
              <i data-lucide="scale" class="w-5 h-5 text-green-500 mr-2"></i>
              Legal Review Decision
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Legal Approve -->
              <form action="{{ route('facility_reservations.legal_approve', $reservation->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                  <label class="label">
                    <span class="label-text font-medium">Legal Comment (Optional)</span>
                  </label>
                  <textarea name="legal_comment" class="textarea textarea-bordered w-full" rows="3" 
                            placeholder="Add any legal notes or comments..."></textarea>
                </div>
                <button type="submit" class="btn btn-success w-full">
                  <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                  Approve - Legal Review Complete
                </button>
              </form>

              <!-- Legal Flag -->
              <form action="{{ route('facility_reservations.legal_flag', $reservation->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                  <label class="label">
                    <span class="label-text font-medium">Reason for Flagging (Required)</span>
                  </label>
                  <textarea name="legal_comment" class="textarea textarea-bordered w-full" rows="3" 
                            placeholder="Explain why this reservation is being flagged..." required></textarea>
                </div>
                <button type="submit" class="btn btn-error w-full">
                  <i data-lucide="flag" class="w-4 h-4 mr-2"></i>
                  Flag - Legal Issues Found
                </button>
              </form>
            </div>

            <div class="alert alert-info mt-4">
              <i data-lucide="info" class="w-5 h-5"></i>
              <div>
                <h4 class="font-bold">Legal Review Guidelines</h4>
                <p class="text-sm">
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

