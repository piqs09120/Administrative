<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Details - Soliera</title>
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
      <main class="flex-1 overflow-y-auto bg-gray-50 p-3 sm:p-4 lg:p-6 xl:p-8">
        @if(session('success'))
          <div class="alert alert-success mb-4 sm:mb-6">
            <i data-lucide="check-circle" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            <span class="text-sm sm:text-base">{{ session('success') }}</span>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-error mb-4 sm:mb-6">
            <i data-lucide="alert-circle" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            <span class="text-sm sm:text-base">{{ session('error') }}</span>
          </div>
        @endif

        <!-- Back button and title -->
        <div class="flex items-center mb-4 sm:mb-6">
          <a href="{{ route('document.index') }}" class="btn btn-ghost btn-sm mr-2 sm:mr-4" style="color: var(--color-regal-navy);">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1 sm:mr-2" style="color: var(--color-regal-navy);"></i>
            <span class="hidden sm:inline">Back</span>
          </a>
        </div>

        <!-- Success Banner -->
        @if(session('success') && str_contains(session('success'), 'uploaded'))
          <div class="bg-green-500 text-white p-3 sm:p-4 rounded-lg mb-4 sm:mb-6" style="background-color: var(--color-modern-teal); color: var(--color-white);">
            <div class="flex items-center">
              <i data-lucide="check-circle" class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3"></i>
              <span class="text-sm sm:text-lg font-medium">Document uploaded and analyzed successfully!</span>
            </div>
          </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
          <!-- Document Information -->
          <div class="lg:col-span-2">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4 space-y-2 sm:space-y-0">
                  <h2 class="card-title text-lg sm:text-xl lg:text-2xl" style="color: var(--color-charcoal-ink);">{{ $document->title }}</h2>
                  <div class="badge badge-sm sm:badge-lg badge-{{ $document->status === 'archived' ? 'neutral' : ($document->status === 'pending_release' ? 'warning' : 'success') }}" style="background-color: var(--color-regal-navy); color: var(--color-white);">
                    {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                  </div>
                </div>

                @if($document->description)
                  <div class="mb-4">
                    <h3 class="font-semibold text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">Description</h3>
                    <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $document->description }}</p>
                  </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-6">
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1 text-sm sm:text-base" style="color: var(--color-charcoal-ink);">Uploaded By</h3>
                    <p class="text-gray-600 text-sm sm:text-base" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $document->uploader->name ?? 'Unknown' }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1 text-sm sm:text-base" style="color: var(--color-charcoal-ink);">File Path</h3>
                    <p class="text-gray-600 text-xs sm:text-sm break-all" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $document->file_path }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1 text-sm sm:text-base" style="color: var(--color-charcoal-ink);">Category</h3>
                    <p class="text-gray-600 text-sm sm:text-base" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $document->category ?? 'Uncategorized' }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1 text-sm sm:text-base" style="color: var(--color-charcoal-ink);">Upload Date</h3>
                    <p class="text-gray-600 text-sm sm:text-base" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $document->created_at->format('M d, Y H:i') }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1 text-sm sm:text-base" style="color: var(--color-charcoal-ink);">Last Updated</h3>
                    <p class="text-gray-600 text-sm sm:text-base" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $document->updated_at->format('M d, Y H:i') }}</p>
                  </div>
                </div>

                <!-- AI Tags -->
                @if($document->ai_analysis && isset($document->ai_analysis['tags']))
                  <div class="mb-4 sm:mb-6">
                    <h3 class="font-semibold text-gray-700 mb-2 sm:mb-3 text-sm sm:text-base" style="color: var(--color-charcoal-ink);">AI Tags</h3>
                    <div class="flex flex-wrap gap-1 sm:gap-2">
                      @foreach($document->ai_analysis['tags'] as $tag)
                        <span class="badge badge-outline badge-sm sm:badge-lg text-xs sm:text-sm" style="border-color: var(--color-regal-navy); color: var(--color-regal-navy);">{{ $tag }}</span>
                      @endforeach
                    </div>
                  </div>
                @endif

                <!-- Compliance Status -->
                @if($document->ai_analysis && isset($document->ai_analysis['compliance_status']))
                  <div class="mb-4 sm:mb-6">
                    <h3 class="font-semibold text-gray-700 mb-2 text-sm sm:text-base" style="color: var(--color-charcoal-ink);">Compliance Status</h3>
                    <span class="badge badge-{{ $document->ai_analysis['compliance_status'] === 'compliant' ? 'success' : ($document->ai_analysis['compliance_status'] === 'non-compliant' ? 'error' : 'warning') }} badge-sm sm:badge-lg text-xs sm:text-sm" style="background-color: var(--color-regal-navy); color: var(--color-white);">
                      {{ ucfirst(str_replace('_', ' ', $document->ai_analysis['compliance_status'])) }}
                    </span>
                  </div>
                @endif

                <div class="card-actions flex flex-wrap gap-2">
                  @if($document->status === 'archived')
                    <form action="{{ route('document.requestRelease', $document->id) }}" method="POST" class="inline">
                      @csrf
                      <button type="submit" class="btn btn-warning btn-sm sm:btn-md" onclick="return confirm('Request release for this document?')" style="background-color: var(--color-golden-ember); color: var(--color-white); border-color: var(--color-golden-ember);">
                        <i data-lucide="send" class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2"></i>
                        <span class="text-xs sm:text-sm">Request Release</span>
                      </button>
                    </form>
                  @endif
                  
                  <form action="{{ route('document.analyze', $document->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm sm:btn-md" style="background-color: var(--color-modern-teal); color: var(--color-white); border-color: var(--color-modern-teal);">
                      <i data-lucide="brain" class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2"></i>
                      <span class="text-xs sm:text-sm">AI Analysis</span>
                    </button>
                  </form>
                  
                  <!-- Administrator Actions -->
                  @if($isAdmin)
                    <!-- Edit Button -->
                    <a href="{{ route('document.edit', $document->id) }}" class="btn btn-outline btn-sm sm:btn-md" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">
                      <i data-lucide="edit" class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2"></i>
                      <span class="text-xs sm:text-sm">Edit</span>
                    </a>
                    
                    <!-- Download Button -->
                    <a href="{{ route('document.download', $document->id) }}" class="btn btn-outline btn-sm sm:btn-md" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">
                      <i data-lucide="download" class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2"></i>
                      <span class="text-xs sm:text-sm">Download</span>
                    </a>
                    
                    <!-- Archive Button (if not already archived) -->
                    @if($document->status !== 'archived' && $document->status !== 'disposed')
                      <button onclick="archiveDocument({{ $document->id }})" class="btn btn-warning btn-sm sm:btn-md" style="background-color: var(--color-golden-ember); color: var(--color-white); border-color: var(--color-golden-ember);">
                        <i data-lucide="archive" class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2"></i>
                        <span class="text-xs sm:text-sm">Archive</span>
                      </button>
                    @endif
                    
                    <!-- Unarchive Button (if archived) -->
                    @if($document->status === 'archived')
                      <button onclick="unarchiveDocument({{ $document->id }})" class="btn btn-info btn-sm sm:btn-md" style="background-color: var(--color-modern-teal); color: var(--color-white); border-color: var(--color-modern-teal);">
                        <i data-lucide="archive-restore" class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2"></i>
                        <span class="text-xs sm:text-sm">Unarchive</span>
                      </button>
                    @endif
                    
                    <!-- Dispose Button (if expired) -->
                    @if($document->status === 'expired')
                      <button onclick="disposeDocument({{ $document->id }})" class="btn btn-error btn-sm sm:btn-md" style="background-color: var(--color-danger-red); color: var(--color-white); border-color: var(--color-danger-red);">
                        <i data-lucide="trash-2" class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2"></i>
                        <span class="text-xs sm:text-sm">Dispose</span>
                      </button>
                    @endif
                    
                    <!-- Delete Button (permanent deletion) -->
                    <button onclick="deleteDocument({{ $document->id }})" class="btn btn-error btn-sm sm:btn-md" style="background-color: var(--color-danger-red); color: var(--color-white); border-color: var(--color-danger-red);">
                      <i data-lucide="trash" class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2"></i>
                      <span class="text-xs sm:text-sm">Delete</span>
                    </button>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <!-- Request History -->
          <div class="lg:col-span-1">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body p-4 sm:p-6">
                <h3 class="card-title text-base sm:text-lg mb-3 sm:mb-4" style="color: var(--color-charcoal-ink);">
                  <i data-lucide="rotate-ccw" class="w-4 h-4 sm:w-5 sm:h-5 mr-2" style="color: var(--color-regal-navy);"></i>Request History
                </h3>

                @if($document->documentRequests->count() > 0)
                  <div class="space-y-3">
                    @foreach($document->documentRequests->sortByDesc('created_at') as $request)
                      <div class="border-l-4 border-{{ $request->status === 'pending' ? 'yellow' : ($request->status === 'approved' ? 'green' : 'red') }}-500 pl-3" style="border-color: var(--color-{{ $request->status === 'pending' ? 'golden-ember' : ($request->status === 'approved' ? 'modern-teal' : 'danger-red') }});">
                        <div class="flex justify-between items-start">
                          <div>
                            <p class="font-semibold text-sm" style="color: var(--color-charcoal-ink);">
                              {{ ucfirst($request->status) }}
                            </p>
                            <p class="text-xs text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                              Requested by: {{ $request->requester->name ?? 'Unknown' }}
                            </p>
                            <p class="text-xs text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                              {{ $request->created_at->format('M d, Y H:i') }}
                            </p>
                          </div>
                          <div class="badge badge-sm badge-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : 'error') }}" style="background-color: var(--color-{{ $request->status === 'pending' ? 'golden-ember' : ($request->status === 'approved' ? 'modern-teal' : 'danger-red') }}); color: var(--color-white);">
                            {{ ucfirst($request->status) }}
                          </div>
                        </div>
                        
                        @if($request->approved_by)
                          <p class="text-xs text-gray-500 mt-1" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                            {{ $request->status === 'approved' ? 'Approved' : 'Denied' }} by: {{ $request->approver->name ?? 'Unknown' }}
                          </p>
                        @endif
                        
                        @if($request->remarks)
                          <p class="text-xs text-gray-600 mt-1 italic" style="color: var(--color-charcoal-ink); opacity: 0.8;">
                            "{{ $request->remarks }}"
                          </p>
                        @endif
                      </div>
                    @endforeach
                  </div>
                @else
                  <div class="text-center py-4" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    <i data-lucide="folder" class="w-12 h-12 text-gray-300 mx-auto mb-2" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
                    <p class="text-gray-500 text-sm" style="color: var(--color-charcoal-ink); opacity: 0.7;">No request history</p>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>


        <!-- Legal Case Information (if applicable) -->
        @if($document->legal_case_data)
          <div class="mt-4 sm:mt-6">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body p-4 sm:p-6">
                <h3 class="card-title text-base sm:text-lg mb-3 sm:mb-4 flex items-center" style="color: var(--color-charcoal-ink);">
                  <i data-lucide="scale" class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3" style="color: var(--color-golden-ember);"></i>
                  Legal Case Information
                  <div class="badge badge-xs sm:badge-sm ml-2 sm:ml-3" style="background-color: var(--color-golden-ember); color: var(--color-white);">Auto-Generated</div>
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                  <div>
                    <h4 class="font-semibold text-xs sm:text-sm mb-1" style="color: var(--color-charcoal-ink);">Case Title</h4>
                    <p class="text-xs sm:text-sm" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $document->legal_case_data['case_title'] ?? 'N/A' }}</p>
                  </div>
                  <div>
                    <h4 class="font-semibold text-xs sm:text-sm mb-1" style="color: var(--color-charcoal-ink);">Priority</h4>
                    <span class="badge badge-xs sm:badge-sm" style="background-color: var(--color-{{ $document->legal_case_data['priority'] === 'urgent' ? 'danger-red' : ($document->legal_case_data['priority'] === 'normal' ? 'golden-ember' : 'modern-teal') }}); color: var(--color-white);">
                      {{ ucfirst($document->legal_case_data['priority'] ?? 'normal') }}
                    </span>
                  </div>
                  <div>
                    <h4 class="font-semibold text-xs sm:text-sm mb-1" style="color: var(--color-charcoal-ink);">Status</h4>
                    <p class="text-xs sm:text-sm" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ ucfirst(str_replace('_', ' ', $document->legal_case_data['status'] ?? 'pending')) }}</p>
                  </div>
                  <div>
                    <h4 class="font-semibold text-xs sm:text-sm mb-1" style="color: var(--color-charcoal-ink);">Compliance Status</h4>
                    <p class="text-xs sm:text-sm" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ ucfirst(str_replace('_', ' ', $document->legal_case_data['compliance_status'] ?? 'review_required')) }}</p>
                  </div>
                </div>
                
                @if(isset($document->legal_case_data['case_description']))
                  <div class="mt-3 sm:mt-4">
                    <h4 class="font-semibold text-xs sm:text-sm mb-2" style="color: var(--color-charcoal-ink);">Case Description</h4>
                    <div class="p-2 sm:p-3 rounded-lg text-xs sm:text-sm" style="background-color: color-mix(in srgb, var(--color-snow-mist), white 50%); color: var(--color-charcoal-ink);">
                      {{ $document->legal_case_data['case_description'] }}
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>
        @endif


        <!-- Linked Reservation Information (if applicable) -->
        @if($document->linked_reservation_id)
          <div class="mt-4 sm:mt-6">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body p-4 sm:p-6">
                <h3 class="card-title text-base sm:text-lg mb-3 sm:mb-4 flex items-center" style="color: var(--color-charcoal-ink);">
                  <i data-lucide="calendar-check" class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3" style="color: var(--color-modern-teal);"></i>
                  Auto-Scheduled Facility Reservation
                  <div class="badge badge-xs sm:badge-sm ml-2 sm:ml-3" style="background-color: var(--color-modern-teal); color: var(--color-white);">Auto-Created</div>
                </h3>
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                  <p class="text-xs sm:text-sm" style="color: var(--color-charcoal-ink); opacity: 0.8;">
                    A facility reservation was automatically created based on the content of this document.
                  </p>
                  <a href="{{ route('facility_reservations.show', $document->linked_reservation_id) }}" 
                     class="btn btn-xs sm:btn-sm btn-outline flex-shrink-0" 
                     style="color: var(--color-modern-teal); border-color: var(--color-modern-teal);">
                    <i data-lucide="external-link" class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2"></i>
                    <span class="text-xs sm:text-sm">View Reservation</span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        @endif
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

    // Document action functions
    function archiveDocument(documentId) {
      if (confirm('Are you sure you want to archive this document?')) {
        fetch(`/document/${documentId}/archive`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while archiving the document.');
        });
      }
    }

    function unarchiveDocument(documentId) {
      if (confirm('Are you sure you want to unarchive this document?')) {
        fetch(`/document/${documentId}/unarchive`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while unarchiving the document.');
        });
      }
    }

    function disposeDocument(documentId) {
      if (confirm('Are you sure you want to dispose of this document? This action cannot be undone!')) {
        fetch(`/document/${documentId}/dispose`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while disposing of the document.');
        });
      }
    }

    function deleteDocument(documentId) {
      if (confirm('Are you sure you want to permanently delete this document? This action cannot be undone!')) {
        fetch(`/document/${documentId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            window.location.href = '/document';
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while deleting the document.');
        });
      }
    }


  </script>
</body>
</html> 