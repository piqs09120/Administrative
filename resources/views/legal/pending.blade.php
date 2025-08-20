<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pending Legal Requests - Soliera</title>
  <link rel="icon" href="swt.jpg" type="image/x-icon">
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
          <h1 class="text-3xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">All Pending Legal Requests</h1>
        </div>

        <!-- Pending Document Release Requests Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center" style="color: var(--color-charcoal-ink);">
              <i data-lucide="clock" class="w-6 h-6 text-yellow-500 mr-3" style="color: var(--color-golden-ember);"></i>
              Pending Document Release Requests
              <span class="badge badge-warning badge-lg ml-3" style="background-color: var(--color-golden-ember); color: var(--color-white);">{{ $pendingRequests->count() }}</span>
            </h2>
          </div>

          @if($pendingRequests->count() > 0)
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr class="bg-gray-50" style="background-color: var(--color-snow-mist);">
                    <th class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Document</th>
                    <th class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Requested By</th>
                    <th class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Request Date</th>
                    <th class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pendingRequests as $request)
                    <tr class="hover:bg-gray-50 transition-colors" style="color: var(--color-charcoal-ink);">
                      <td>
                        <div>
                          <div class="font-semibold text-gray-800" style="color: var(--color-charcoal-ink);">{{ $request->document->title }}</div>
                          <div class="text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">{{ Str::limit($request->document->description, 50) }}</div>
                        </div>
                      </td>
                      <td class="font-medium" style="color: var(--color-charcoal-ink);">{{ $request->requester->name ?? 'Unknown' }}</td>
                      <td class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $request->created_at->format('M d, Y H:i') }}</td>
                      <td>
                        <div class="flex space-x-2">
                          <a href="{{ route('legal.show', $request->id) }}" class="btn btn-sm btn-outline" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">
                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                          </a>
                          <button onclick="approveRequest({{ $request->id }})" class="btn btn-sm btn-success" style="background-color: var(--color-modern-teal); color: var(--color-white); border-color: var(--color-modern-teal);">
                            <i data-lucide="check" class="w-4 h-4 mr-1"></i>Approve
                          </button>
                          <button onclick="denyRequest({{ $request->id }})" class="btn btn-sm btn-error" style="background-color: var(--color-danger-red); color: var(--color-white); border-color: var(--color-danger-red);">
                            <i data-lucide="x" class="w-4 h-4 mr-1"></i>Deny
                          </button>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-12" style="color: var(--color-charcoal-ink); opacity: 0.7;">
              <i data-lucide="check-circle" class="w-16 h-16 text-green-300 mx-auto mb-4" style="color: var(--color-modern-teal); opacity: 0.5;"></i>
              <h3 class="text-lg font-semibold text-gray-600 mb-2" style="color: var(--color-charcoal-ink);">No Pending Document Release Requests</h3>
              <p class="text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">All document release requests have been processed.</p>
            </div>
          @endif
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
      
      if (dateElement) dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
      if (timeElement) timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
    }

    // Functions to handle approve/deny from current page (for DocumentReleaseRequests)
    function approveRequest(requestId) {
      if (confirm('Are you sure you want to approve this document release request?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/legal/${requestId}/approve`;
        form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
        document.body.appendChild(form);
        form.submit();
      }
    }

    function denyRequest(requestId) {
      const reason = prompt('Please provide a reason for denying this document release request:');
      if (reason !== null && reason.trim() !== '') {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/legal/${requestId}/deny`;
        form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="reason" value="${reason}">`;
        document.body.appendChild(form);
        form.submit();
      } else if (reason !== null) {
        alert('Denial reason cannot be empty.');
      }
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


