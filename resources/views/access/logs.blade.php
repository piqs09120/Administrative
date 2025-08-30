<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Access Logs - Soliera</title>
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
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Access Logs</h1>
          <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Total {{ $logs->count() }} log entries</p>
        </div>

        <!-- Action Bar -->
        <div class="flex items-center justify-end mb-6">
          <div class="flex items-center space-x-3">
            <span class="text-gray-600" style="color: var(--color-charcoal-ink);">{{ $logs->count() }} entries</span>
            <button class="btn btn-outline btn-success btn-sm">
              <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
              FILTER
            </button>
          </div>
        </div>

        <!-- Access Logs Table -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="overflow-x-auto">
            <table class="w-full">
              <!-- Table Header -->
              <thead>
                <tr class="border-b border-gray-200">
                  <th class="text-left py-4 px-6 font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">LOG ID</th>
                  <th class="text-center py-4 px-6 font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">DEPARTMENT</th>
                  <th class="text-left py-4 px-6 font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">EMPLOYEE</th>
                  <th class="text-center py-4 px-6 font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">TYPE</th>
                  <th class="text-center py-4 px-6 font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">STATUS</th>
                  <th class="text-left py-4 px-6 font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">DETAILS</th>
                  <th class="text-right py-4 px-6 font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">DATE</th>
                </tr>
              </thead>
              <tbody>
                @foreach($logs as $index => $log)
                  <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200 cursor-pointer">
                    <!-- LOG ID -->
                    <td class="py-4 px-6">
                      <span class="font-mono text-sm font-medium text-gray-900" style="color: var(--color-charcoal-ink);">#{{ $index + 1 }}</span>
                    </td>
                    
                    <!-- DEPARTMENT -->
                    <td class="py-4 px-6 text-center">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%); color: var(--color-regal-navy);">
                        {{ $log->user->Dept_id ?? 'N/A' }}
                      </span>
                    </td>
                    
                    <!-- EMPLOYEE -->
                    <td class="py-4 px-6">
                      <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-3" style="background-color: color-mix(in srgb, var(--color-snow-mist), black 20%);">
                          <i data-lucide="user" class="w-4 h-4 text-gray-600" style="color: var(--color-charcoal-ink);"></i>
                        </div>
                        <div>
                          <div class="font-medium text-gray-900" style="color: var(--color-charcoal-ink);">{{ $log->user->employee_name ?? 'Unknown' }}</div>
                          <div class="text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">ID: {{ $log->user->employee_id ?? 'N/A' }}</div>
                        </div>
                      </div>
                    </td>
                    
                    <!-- TYPE -->
                    <td class="py-4 px-6 text-center">
                      @php
                        $actionType = strtolower($log->action);
                        $typeIcon = 'arrow-right';
                        $typeColor = 'text-blue-500';
                        
                        if (strpos($actionType, 'login') !== false) {
                          $typeIcon = 'arrow-right';
                          $typeColor = 'text-blue-500';
                        } elseif (strpos($actionType, 'logout') !== false) {
                          $typeIcon = 'arrow-left';
                          $typeColor = 'text-orange-500';
                        } elseif (strpos($actionType, 'access') !== false) {
                          $typeIcon = 'key';
                          $typeColor = 'text-green-500';
                        } elseif (strpos($actionType, 'error') !== false || strpos($actionType, 'failed') !== false) {
                          $typeIcon = 'alert-triangle';
                          $typeColor = 'text-red-500';
                        } elseif (strpos($actionType, 'upload') !== false) {
                          $typeIcon = 'upload';
                          $typeColor = 'text-purple-500';
                        } elseif (strpos($actionType, 'download') !== false) {
                          $typeIcon = 'download';
                          $typeColor = 'text-indigo-500';
                        }
                      @endphp
                      <div class="flex items-center justify-center">
                        <i data-lucide="{{ $typeIcon }}" class="w-5 h-5 {{ $typeColor }}"></i>
                        <span class="ml-2 text-sm font-medium text-gray-700" style="color: var(--color-charcoal-ink);">{{ ucfirst($log->action) }}</span>
                      </div>
                    </td>
                    
                    <!-- STATUS -->
                    <td class="py-4 px-6 text-center">
                      @php
                        $status = 'success';
                        $statusColor = 'bg-green-100 text-green-800';
                        $statusIcon = 'check-circle';
                        
                        if (strpos(strtolower($log->action), 'error') !== false || strpos(strtolower($log->action), 'failed') !== false) {
                          $status = 'failed';
                          $statusColor = 'bg-red-100 text-red-800';
                          $statusIcon = 'x-circle';
                        } elseif (strpos(strtolower($log->action), 'pending') !== false) {
                          $status = 'pending';
                          $statusColor = 'bg-yellow-100 text-yellow-800';
                          $statusIcon = 'clock';
                        } elseif (strpos(strtolower($log->action), 'warning') !== false) {
                          $status = 'warning';
                          $statusColor = 'bg-orange-100 text-orange-800';
                          $statusIcon = 'alert-triangle';
                        }
                      @endphp
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                        <i data-lucide="{{ $statusIcon }}" class="w-3 h-3 mr-1"></i>
                        {{ ucfirst($status) }}
                      </span>
                    </td>
                    
                    <!-- DETAILS -->
                    <td class="py-4 px-6">
                      @php
                        $detailIcon = 'info';
                        $detailColor = 'text-blue-500';
                        
                        if (strpos(strtolower($log->description), 'successful') !== false) {
                          $detailIcon = 'check-circle';
                          $detailColor = 'text-green-500';
                        } elseif (strpos(strtolower($log->description), 'error') !== false || strpos(strtolower($log->description), 'failed') !== false) {
                          $detailIcon = 'alert-triangle';
                          $detailColor = 'text-red-500';
                        } elseif (strpos(strtolower($log->description), 'warning') !== false) {
                          $detailIcon = 'alert-triangle';
                          $detailColor = 'text-orange-500';
                        }
                      @endphp
                      <div class="flex items-center">
                        <i data-lucide="{{ $detailIcon }}" class="w-4 h-4 mr-2 {{ $detailColor }}"></i>
                        <span class="text-sm text-gray-700" style="color: var(--color-charcoal-ink);">{{ Str::limit($log->description, 60) }}</span>
                      </div>
                    </td>
                    
                    <!-- DATE -->
                    <td class="py-4 px-6 text-right">
                      <div class="text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                        {{ $log->created_at->format('M d, Y H:i:s') }}
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          
          @if($logs->count() === 0)
            <div class="text-center py-12">
              <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6" style="background-color: var(--color-snow-mist);">
                <i data-lucide="activity" class="w-12 h-12 text-gray-400" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
              </div>
              <h3 class="text-lg font-semibold text-gray-600 mb-2" style="color: var(--color-charcoal-ink);">No Access Logs Found</h3>
              <p class="text-gray-500 mb-6" style="color: var(--color-charcoal-ink); opacity: 0.7;">No access log entries have been recorded yet.</p>
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
      
      // Add row click functionality for future expansion
      const tableRows = document.querySelectorAll('tbody tr');
      tableRows.forEach(row => {
        row.addEventListener('click', function() {
          // Future: Expand row to show more details
          console.log('Row clicked:', this);
        });
      });
    });
  </script>
</body>
</html>
