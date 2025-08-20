<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Legal Case Details - {{ $request->document->title ?? 'N/A' }}</title>
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
          <a href="{{ route('legal.index') }}" class="btn btn-ghost btn-sm mr-4" style="color: var(--color-regal-navy);">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>Back
          </a>
          <h1 class="text-3xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Legal Case Details</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Document Info -->
          <div class="lg:col-span-2">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <div class="flex items-center justify-between mb-6">
                  <h2 class="card-title text-2xl flex items-center">
                    <i data-lucide="file-text" class="w-6 h-6 mr-3" style="color: var(--color-regal-navy);"></i>
                    <span style="color: var(--color-charcoal-ink);">{{ $request->document->title ?? 'N/A' }}</span>
                  </h2>
                  <div class="badge badge-lg" style="background-color: var(--color-modern-teal); color: var(--color-white);">
                    {{ ucfirst($request->document->status ?? 'N/A') }}
                  </div>
                </div>

                @if($request->document->description)
                  <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Description</label>
                    <p class="mt-1 text-gray-700" style="color: var(--color-charcoal-ink);">{{ $request->document->description }}</p>
                  </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t" style="border-color: var(--color-snow-mist);">
                  <div>
                    <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Uploaded By</label>
                    <p class="font-semibold text-lg" style="color: var(--color-charcoal-ink);">{{ $request->document->uploader->name ?? 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Uploaded At</label>
                    <p class="text-sm" style="color: var(--color-charcoal-ink);">{{ $request->document->created_at->format('M d, Y H:i') }}</p>
                  </div>
                </div>

                <div class="card-actions mt-6">
                  <a href="{{ route('document.download', $request->document->id) }}" class="btn btn-primary" style="background-color: var(--color-regal-navy); color: var(--color-white); border-color: var(--color-regal-navy);">
                    <i data-lucide="download" class="w-4 h-4 mr-2"></i>Download Document
                  </a>
                  <a href="{{ route('legal.edit', $request->id) }}" class="btn btn-outline" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>Edit Case
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- AI Analysis Sidebar -->
          <div class="lg:col-span-1">
            <div class="card bg-white shadow-xl mb-6" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4 flex items-center" style="color: var(--color-charcoal-ink);">
                  <i data-lucide="brain" class="w-5 h-5 mr-2" style="color: var(--color-regal-navy);"></i>
                  AI Analysis
                </h3>

                @if($request->document->ai_analysis)
                  @php
                    $aiAnalysis = json_decode($request->document->ai_analysis, true);
                  @endphp
                  <div class="space-y-4">
                    <div>
                      <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Document Category</label>
                      <div class="mt-1">
                        <span class="badge" style="background-color: var(--color-regal-navy); color: var(--color-white);">{{ ucfirst($aiAnalysis['category'] ?? 'Unknown') }}</span>
                        @if($aiAnalysis['fallback'] ?? false)
                          <span class="badge badge-xs ml-2" style="background-color: var(--color-golden-ember); color: var(--color-white);">Fallback</span>
                        @endif
                      </div>
                    </div>

                    @if($aiAnalysis['summary'] ?? false)
                      <div>
                        <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Summary</label>
                        <p class="mt-1 text-sm text-gray-700" style="color: var(--color-charcoal-ink);">{{ $aiAnalysis['summary'] }}</p>
                      </div>
                    @endif

                    @if($aiAnalysis['key_info'] ?? false)
                      <div>
                        <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Key Information</label>
                        <p class="mt-1 text-sm text-gray-700" style="color: var(--color-charcoal-ink);">{{ $aiAnalysis['key_info'] }}</p>
                      </div>
                    @endif

                    @if($aiAnalysis['legal_implications'] ?? false)
                      <div>
                        <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Legal Implications</label>
                        <p class="mt-1 text-sm text-gray-700" style="color: var(--color-charcoal-ink);">{{ $aiAnalysis['legal_implications'] }}</p>
                      </div>
                    @endif

                    @if($aiAnalysis['compliance_status'] ?? false)
                      <div>
                        <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Compliance Status</label>
                        <div class="mt-1">
                          @php
                            $status = $aiAnalysis['compliance_status'];
                            $statusClass = $status === 'compliant' ? 'success' : ($status === 'non-compliant' ? 'error' : 'warning');
                          @endphp
                          <span class="badge badge-{{ $statusClass }}" style="background-color: var(--color-charcoal-ink); color: var(--color-white);">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                        </div>
                      </div>
                    @endif

                    @if($aiAnalysis['tags'] ?? false)
                      <div>
                        <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Tags</label>
                        <div class="mt-1 flex flex-wrap gap-1">
                          @foreach($aiAnalysis['tags'] as $tag)
                            <span class="badge badge-outline badge-sm" style="border-color: var(--color-regal-navy); color: var(--color-regal-navy);">{{ $tag }}</span>
                          @endforeach
                        </div>
                      </div>
                    @endif
                  </div>
                @elseif($request->document->ai_error)
                  <div class="alert mb-2" style="background-color: color-mix(in srgb, var(--color-danger-red), white 90%); border-color: var(--color-danger-red); color: var(--color-charcoal-ink);">
                    <i data-lucide="alert-triangle" class="w-5 h-5" style="color: var(--color-danger-red);"></i>
                    <div>
                      <h4 class="font-bold" style="color: var(--color-charcoal-ink);">AI Analysis Error</h4>
                      <p class="text-sm" style="color: var(--color-charcoal-ink);">{{ $request->document->ai_error }}</p>
                    </div>
                  </div>
                @else
                  <div class="text-center py-8">
                    <i data-lucide="file-x" class="w-12 h-12 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
                    <p class="text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">No AI analysis available for this document</p>
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