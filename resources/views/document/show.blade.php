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
          <a href="{{ route('document.index') }}" class="btn btn-ghost btn-sm mr-4" style="color: var(--color-regal-navy);">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>Back
          </a>
        </div>

        <!-- Success Banner -->
        @if(session('success') && str_contains(session('success'), 'uploaded'))
          <div class="bg-green-500 text-white p-4 rounded-lg mb-6" style="background-color: var(--color-modern-teal); color: var(--color-white);">
            <div class="flex items-center">
              <i data-lucide="check-circle" class="w-6 h-6 mr-3"></i>
              <span class="text-lg font-medium">Document uploaded and analyzed successfully!</span>
            </div>
          </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Document Information -->
          <div class="lg:col-span-2">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <div class="flex justify-between items-start mb-4">
                  <h2 class="card-title text-2xl" style="color: var(--color-charcoal-ink);">{{ $document->title }}</h2>
                  <div class="badge badge-lg badge-{{ $document->status === 'archived' ? 'neutral' : ($document->status === 'pending_release' ? 'warning' : 'success') }}" style="background-color: var(--color-regal-navy); color: var(--color-white);">
                    {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                  </div>
                </div>

                @if($document->description)
                  <div class="mb-4">
                    <h3 class="font-semibold text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">Description</h3>
                    <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $document->description }}</p>
                  </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1" style="color: var(--color-charcoal-ink);">Uploaded By</h3>
                    <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $document->uploader->name ?? 'Unknown' }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1" style="color: var(--color-charcoal-ink);">File Path</h3>
                    <p class="text-gray-600 text-sm" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $document->file_path }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1" style="color: var(--color-charcoal-ink);">Category</h3>
                    <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $document->category ?? 'Uncategorized' }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1" style="color: var(--color-charcoal-ink);">Upload Date</h3>
                    <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $document->created_at->format('M d, Y H:i') }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1" style="color: var(--color-charcoal-ink);">Last Updated</h3>
                    <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $document->updated_at->format('M d, Y H:i') }}</p>
                  </div>
                </div>

                <!-- AI Tags -->
                @if($document->ai_analysis && isset($document->ai_analysis['tags']))
                  <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-3" style="color: var(--color-charcoal-ink);">AI Tags</h3>
                    <div class="flex flex-wrap gap-2">
                      @foreach($document->ai_analysis['tags'] as $tag)
                        <span class="badge badge-outline badge-lg" style="border-color: var(--color-regal-navy); color: var(--color-regal-navy);">{{ $tag }}</span>
                      @endforeach
                    </div>
                  </div>
                @endif

                <!-- Compliance Status -->
                @if($document->ai_analysis && isset($document->ai_analysis['compliance_status']))
                  <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">Compliance Status</h3>
                    <span class="badge badge-{{ $document->ai_analysis['compliance_status'] === 'compliant' ? 'success' : ($document->ai_analysis['compliance_status'] === 'non-compliant' ? 'error' : 'warning') }} badge-lg" style="background-color: var(--color-regal-navy); color: var(--color-white);">
                      {{ ucfirst(str_replace('_', ' ', $document->ai_analysis['compliance_status'])) }}
                    </span>
                  </div>
                @endif

                <div class="card-actions">
                  @if($document->status === 'archived')
                    <form action="{{ route('document.request-release', $document->id) }}" method="POST" class="inline">
                      @csrf
                      <button type="submit" class="btn btn-warning" onclick="return confirm('Request release for this document?')" style="background-color: var(--color-golden-ember); color: var(--color-white); border-color: var(--color-golden-ember);">
                        <i data-lucide="send" class="w-4 h-4 mr-2"></i>Request Release
                      </button>
                    </form>
                  @endif
                  
                  <form action="{{ route('document.analyze', $document->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background-color: var(--color-modern-teal); color: var(--color-white); border-color: var(--color-modern-teal);">
                      <i data-lucide="brain" class="w-4 h-4 mr-2"></i>AI Analysis
                    </button>
                  </form>
                  
                  <a href="{{ route('document.edit', $document->id) }}" class="btn btn-outline" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>Edit
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Request History -->
          <div class="lg:col-span-1">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4" style="color: var(--color-charcoal-ink);">
                  <i data-lucide="rotate-ccw" class="w-5 h-5 mr-2" style="color: var(--color-regal-navy);"></i>Request History
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