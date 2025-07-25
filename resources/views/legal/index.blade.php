<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Legal Management - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
  @fluxAppearance
</head>
<body class="bg-base-100">
  <div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    @include('partials.sidebarr')
    <!-- Main content -->
    <div class="flex flex-col flex-1 overflow-hidden">
      <!-- Header - Exactly like the image -->
      <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <button onclick="toggleSidebar()" class="btn btn-ghost btn-sm hover:bg-base-300 transition-all hover:scale-105">
              <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h1 class="text-3xl font-bold text-gray-800 ml-4">Legal Management</h1>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Search Bar - Like in the image -->
            <div class="relative">
              <input type="text" id="searchInput" placeholder="Search..." class="input input-bordered input-md w-64 pl-10 bg-white border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
              <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Date and Time - Like in the image -->
            <div class="flex items-center space-x-2 bg-gray-100 px-4 py-3 rounded-lg">
              <i data-lucide="calendar" class="w-5 h-5 text-blue-500"></i>
              <span id="currentDate" class="text-base font-medium text-gray-700"></span>
              <div class="w-px h-5 bg-gray-300"></div>
              <i data-lucide="clock" class="w-5 h-5 text-green-500"></i>
              <span id="currentTime" class="text-base font-medium text-gray-700"></span>
            </div>
            
            <!-- Utility Icons - Like in the image -->
            <div class="dropdown dropdown-end mr-2">
              <div tabindex="0" role="button" class="btn btn-ghost btn-circle" onclick="toggleDarkMode()">
                <i id="darkModeIcon" data-lucide="moon" class="w-6 h-6 text-gray-600"></i>
              </div>
            </div>
            <div class="dropdown dropdown-end">
              <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                <i data-lucide="user" class="w-6 h-6 text-gray-600"></i>
              </div>
              <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-white rounded-lg w-52 border border-gray-200">
                <li><a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                  <i data-lucide="user" class="w-4 h-4 text-gray-600"></i>
                  <span>Profile</span>
                </a></li>
                <li><a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                  <i data-lucide="settings" class="w-4 h-4 text-gray-600"></i>
                  <span>Settings</span>
                </a></li>
                <li><a href="#" onclick="logout()" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                  <i data-lucide="log-out" class="w-4 h-4 text-gray-600"></i>
                  <span>Sign out</span>
                </a></li>
              </ul>
            </div>
          </div>
        </div>
      </header>

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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Pending Requests -->
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-blue-100 text-sm font-medium">Pending Requests</p>
                <p class="text-3xl font-bold">{{ $pendingRequests->count() }}</p>
              </div>
              <div class="bg-blue-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="clock" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Pending Facility Reservations -->
          <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-green-100 text-sm font-medium">Pending Reservations</p>
                <p class="text-3xl font-bold">{{ $pendingFacilityReservations->count() }}</p>
              </div>
              <div class="bg-green-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="calendar" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Approved Today -->
          <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-purple-100 text-sm font-medium">Approved Today</p>
                <p class="text-3xl font-bold">{{ $approvedFacilityReservations->where('updated_at', '>=', now()->startOfDay())->count() }}</p>
              </div>
              <div class="bg-purple-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="check-circle" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Denied Today -->
          <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-orange-100 text-sm font-medium">Denied Today</p>
                <p class="text-3xl font-bold">{{ $deniedFacilityReservations->where('updated_at', '>=', now()->startOfDay())->count() }}</p>
              </div>
              <div class="bg-orange-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="x-circle" class="w-8 h-8"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 mb-8">
          <a href="{{ route('legal.pending') }}" class="btn btn-primary btn-lg">
            <i data-lucide="clock" class="w-5 h-5 mr-2"></i>
            View Pending Requests
          </a>
          <a href="{{ route('legal.approved') }}" class="btn btn-success btn-lg">
            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
            View Approved
          </a>
          <a href="{{ route('legal.denied') }}" class="btn btn-error btn-lg">
            <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
            View Denied
          </a>
        </div>

        <!-- Pending Requests Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
              <i data-lucide="clock" class="w-6 h-6 text-yellow-500 mr-3"></i>
              Pending Approval Requests
              <span class="badge badge-warning badge-lg ml-3">{{ $pendingRequests->count() }}</span>
            </h2>
          </div>

          @if($pendingRequests->count() > 0)
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr class="bg-gray-50">
                    <th class="font-semibold text-gray-700">Document</th>
                    <th class="font-semibold text-gray-700">Requested By</th>
                    <th class="font-semibold text-gray-700">Request Date</th>
                    <th class="font-semibold text-gray-700">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pendingRequests as $request)
                    <tr class="hover:bg-gray-50 transition-colors">
                      <td>
                        <div>
                          <div class="font-semibold text-gray-800">{{ $request->document->title }}</div>
                          <div class="text-sm text-gray-500">{{ Str::limit($request->document->description, 50) }}</div>
                        </div>
                      </td>
                      <td class="font-medium">{{ $request->requester->name ?? 'Unknown' }}</td>
                      <td class="text-gray-600">{{ $request->created_at->format('M d, Y H:i') }}</td>
                      <td>
                        <div class="flex space-x-2">
                          <a href="{{ route('legal.show', $request->id) }}" class="btn btn-sm btn-outline">
                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                          </a>
                          <button onclick="approveRequest({{ $request->id }})" class="btn btn-sm btn-success">
                            <i data-lucide="check" class="w-4 h-4 mr-1"></i>Approve
                          </button>
                          <button onclick="denyRequest({{ $request->id }})" class="btn btn-sm btn-error">
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
            <div class="text-center py-12">
              <i data-lucide="check-circle" class="w-16 h-16 text-green-300 mx-auto mb-4"></i>
              <h3 class="text-lg font-semibold text-gray-600 mb-2">No Pending Requests</h3>
              <p class="text-gray-500">All document release requests have been processed.</p>
            </div>
          @endif
        </div>

        <!-- Pending Facility Reservation Requests Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
              <i data-lucide="calendar" class="w-6 h-6 text-yellow-500 mr-3"></i>
              Pending Facility Reservation Requests
              <span class="badge badge-warning badge-lg ml-3">{{ $pendingFacilityReservations->count() }}</span>
            </h2>
          </div>

          @if($pendingFacilityReservations->count() > 0)
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr class="bg-gray-50">
                    <th class="font-semibold text-gray-700">Facility</th>
                    <th class="font-semibold text-gray-700">Reserved By</th>
                    <th class="font-semibold text-gray-700">Start</th>
                    <th class="font-semibold text-gray-700">End</th>
                    <th class="font-semibold text-gray-700">Purpose</th>
                    <th class="font-semibold text-gray-700">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pendingFacilityReservations as $reservation)
                    <tr class="hover:bg-gray-50 transition-colors">
                      <td class="font-medium">{{ $reservation->facility->name ?? 'N/A' }}</td>
                      <td class="font-medium">{{ $reservation->reserver->name ?? 'N/A' }}</td>
                      <td class="text-gray-600">{{ \Carbon\Carbon::parse($reservation->start_time)->format('M d, Y H:i') }}</td>
                      <td class="text-gray-600">{{ \Carbon\Carbon::parse($reservation->end_time)->format('M d, Y H:i') }}</td>
                      <td>
                        <span class="badge badge-outline">{{ $reservation->purpose ?? '-' }}</span>
                      </td>
                      <td>
                        <form action="/facility_reservations/{{ $reservation->id }}/approve" method="POST" class="inline">
                          @csrf
                          <input type="text" name="remarks" class="input input-bordered input-sm mr-2" placeholder="Remarks (optional)">
                          <button type="submit" class="btn btn-success btn-sm">
                            <i data-lucide="check" class="w-4 h-4 mr-1"></i>Approve
                          </button>
                        </form>
                        <form action="/facility_reservations/{{ $reservation->id }}/deny" method="POST" class="inline">
                          @csrf
                          <input type="text" name="remarks" class="input input-bordered input-sm mr-2" placeholder="Remarks (optional)">
                          <button type="submit" class="btn btn-error btn-sm">
                            <i data-lucide="x" class="w-4 h-4 mr-1"></i>Deny
                          </button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-12">
              <i data-lucide="check-circle" class="w-16 h-16 text-green-300 mx-auto mb-4"></i>
              <h3 class="text-lg font-semibold text-gray-600 mb-2">No Pending Facility Reservations</h3>
              <p class="text-gray-500">All facility reservation requests have been processed.</p>
            </div>
          @endif
        </div>

        <!-- Recently Approved Facility Reservations -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
          <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
            <i data-lucide="check-circle" class="w-6 h-6 text-green-500 mr-3"></i>
            Recently Approved Facility Reservations
          </h3>
          @if($approvedFacilityReservations->count() > 0)
            <div class="space-y-4">
              @foreach($approvedFacilityReservations as $reservation)
                <div class="border-l-4 border-green-500 pl-4 py-3 bg-green-50 rounded-r-lg">
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="font-semibold text-gray-800">{{ $reservation->facility->name ?? 'N/A' }}</p>
                      <p class="text-sm text-gray-600">
                        Reserved by: {{ $reservation->reserver->name ?? 'N/A' }}
                      </p>
                      <p class="text-sm text-gray-600">
                        Approved by: {{ $reservation->approver->name ?? 'N/A' }}
                      </p>
                      <p class="text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($reservation->updated_at)->format('M d, Y H:i') }}
                      </p>
                    </div>
                    <div class="badge badge-success badge-lg">Approved</div>
                  </div>
                  @if($reservation->remarks)
                    <div class="text-sm text-gray-600 mt-2 italic">
                      "{{ $reservation->remarks }}"
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-8">
              <p class="text-gray-500">No recently approved facility reservations</p>
            </div>
          @endif
        </div>

        <!-- Recently Denied Facility Reservations -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
            <i data-lucide="x-circle" class="w-6 h-6 text-red-500 mr-3"></i>
            Recently Denied Facility Reservations
          </h3>
          @if($deniedFacilityReservations->count() > 0)
            <div class="space-y-4">
              @foreach($deniedFacilityReservations as $reservation)
                <div class="border-l-4 border-red-500 pl-4 py-3 bg-red-50 rounded-r-lg">
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="font-semibold text-gray-800">{{ $reservation->facility->name ?? 'N/A' }}</p>
                      <p class="text-sm text-gray-600">
                        Reserved by: {{ $reservation->reserver->name ?? 'N/A' }}
                      </p>
                      <p class="text-sm text-gray-600">
                        Denied by: {{ $reservation->approver->name ?? 'N/A' }}
                      </p>
                      <p class="text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($reservation->updated_at)->format('M d, Y H:i') }}
                      </p>
                    </div>
                    <div class="badge badge-error badge-lg">Denied</div>
                  </div>
                  @if($reservation->remarks)
                    <div class="text-sm text-gray-600 mt-2 italic">
                      "{{ $reservation->remarks }}"
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-8">
              <p class="text-gray-500">No recently denied facility reservations</p>
            </div>
          @endif
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    // Dark mode functionality - works on entire screen
    function toggleDarkMode() {
      const html = document.documentElement;
      const body = document.body;
      const icon = document.getElementById('darkModeIcon');
      const header = document.querySelector('header');
      const sidebar = document.getElementById('sidebar');
      const main = document.querySelector('main');
      
      if (html.classList.contains('dark')) {
        // Switch to light mode
        html.classList.remove('dark');
        body.classList.remove('dark');
        localStorage.setItem('darkMode', 'false');
        icon.setAttribute('data-lucide', 'moon');
        icon.classList.remove('text-yellow-500');
        icon.classList.add('text-gray-600');
        
        // Update header styles
        header.classList.remove('dark:bg-gray-800', 'dark:border-gray-700');
        header.classList.add('bg-white', 'border-gray-200');
        
        // Update sidebar styles
        if (sidebar) {
          sidebar.classList.remove('dark:bg-gray-900');
        }
        
        // Update main content styles
        if (main) {
          main.classList.remove('dark:bg-gray-900');
        }
        
        console.log('Switched to LIGHT mode');
      } else {
        // Switch to dark mode
        html.classList.add('dark');
        body.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
        icon.setAttribute('data-lucide', 'sun');
        icon.classList.remove('text-gray-600');
        icon.classList.add('text-yellow-500');
        
        // Update header styles
        header.classList.remove('bg-white', 'border-gray-200');
        header.classList.add('dark:bg-gray-800', 'dark:border-gray-700');
        
        // Update sidebar styles
        if (sidebar) {
          sidebar.classList.add('dark:bg-gray-900');
        }
        
        // Update main content styles
        if (main) {
          main.classList.add('dark:bg-gray-900');
        }
        
        console.log('Switched to DARK mode');
      }
      
      // Recreate the icon
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

    // Real-time date and time
    function updateDateTime() {
      const now = new Date();
      const dateElement = document.getElementById('currentDate');
      const timeElement = document.getElementById('currentTime');
      
      const dateOptions = { weekday: 'short', month: 'short', day: 'numeric' };
      const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
      
      dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
      timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
    }

    // Search functionality
    function setupSearch() {
      const searchInput = document.getElementById('searchInput');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const tableRows = document.querySelectorAll('tbody tr');
          
          tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }
    }

    // Initialize dark mode from localStorage
    function initializeDarkMode() {
      const darkMode = localStorage.getItem('darkMode');
      const html = document.documentElement;
      const body = document.body;
      const icon = document.getElementById('darkModeIcon');
      const header = document.querySelector('header');
      const sidebar = document.getElementById('sidebar');
      const main = document.querySelector('main');
      
      if (darkMode === 'true') {
        html.classList.add('dark');
        body.classList.add('dark');
        icon.setAttribute('data-lucide', 'sun');
        icon.classList.remove('text-gray-600');
        icon.classList.add('text-yellow-500');
        
        header.classList.remove('bg-white', 'border-gray-200');
        header.classList.add('dark:bg-gray-800', 'dark:border-gray-700');
        
        // Update sidebar styles
        if (sidebar) {
          sidebar.classList.add('dark:bg-gray-900');
        }
        
        // Update main content styles
        if (main) {
          main.classList.add('dark:bg-gray-900');
        }
        
        console.log('Initialized DARK mode');
      } else {
        html.classList.remove('dark');
        body.classList.remove('dark');
        icon.setAttribute('data-lucide', 'moon');
        icon.classList.remove('text-yellow-500');
        icon.classList.add('text-gray-600');
        
        header.classList.remove('dark:bg-gray-800', 'dark:border-gray-700');
        header.classList.add('bg-white', 'border-gray-200');
        
        // Update sidebar styles
        if (sidebar) {
          sidebar.classList.remove('dark:bg-gray-900');
        }
        
        // Update main content styles
        if (main) {
          main.classList.remove('dark:bg-gray-900');
        }
        
        console.log('Initialized LIGHT mode');
      }
      
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      initializeDarkMode();
      updateDateTime();
      setupSearch();
      
      // Update time every second
      setInterval(updateDateTime, 1000);
    });

    function approveRequest(requestId) {
      if (confirm('Are you sure you want to approve this request?')) {
        // Add your approval logic here
        console.log('Approving request:', requestId);
      }
    }

    function denyRequest(requestId) {
      if (confirm('Are you sure you want to deny this request?')) {
        // Add your denial logic here
        console.log('Denying request:', requestId);
      }
    }
  </script>
</body>
</html> 