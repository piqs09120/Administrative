<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Facility Reservations - Soliera</title>
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
      <!-- Header - Exactly like Document Management -->
      <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <button onclick="toggleSidebar()" class="btn btn-ghost btn-sm hover:bg-base-300 transition-all hover:scale-105">
              <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h1 class="text-3xl font-bold text-gray-800 ml-4">Facility Reservations</h1>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Search Bar - Like in Document Management -->
            <div class="relative">
              <input type="text" id="searchInput" placeholder="Search..." class="input input-bordered input-md w-64 pl-10 bg-white border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
              <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Date and Time - Like in Document Management -->
            <div class="flex items-center space-x-2 bg-gray-100 px-4 py-3 rounded-lg">
              <i data-lucide="calendar" class="w-5 h-5 text-blue-500"></i>
              <span id="currentDate" class="text-base font-medium text-gray-700"></span>
              <div class="w-px h-5 bg-gray-300"></div>
              <i data-lucide="clock" class="w-5 h-5 text-green-500"></i>
              <span id="currentTime" class="text-base font-medium text-gray-700"></span>
            </div>
            
            <!-- Moon Icon (Dark Mode Toggle) - Like in Document Management -->
            <button id="darkModeToggle" class="p-2 rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition-colors">
                <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13.66l-.71.71M4.05 19.07l-.71.71M21 12h-1M4 12H3m16.66 5.66l-.71-.71M4.05 4.93l-.71-.71M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="white" viewBox="0 0 24 24" stroke="white">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
                </svg>
            </button>
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

        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-800">Facility Reservations</h2>
          <a href="{{ route('facility_reservations.create') }}" class="btn btn-primary">
            <i data-lucide="calendar-plus" class="w-4 h-4 mr-2"></i>Reserve Facility
          </a>
        </div>

        <!-- Facility Reservations Table -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
              <i data-lucide="calendar" class="w-6 h-6 text-blue-500 mr-3"></i>
              Reservation Directory
            </h3>
            <div class="flex items-center space-x-2">
              <span class="text-sm text-gray-500">Total: {{ $reservations->count() }} reservations</span>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
              <thead>
                <tr>
                  <th>Facility</th>
                  <th>Reserved By</th>
                  <th>Start</th>
                  <th>End</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($reservations as $reservation)
                  <tr>
                    <td>
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                          <i data-lucide="building" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                          <div class="font-semibold">{{ $reservation->facility->name ?? 'N/A' }}</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                          <span class="text-sm font-medium text-gray-600">
                            {{ substr($reservation->reserver->name ?? 'N/A', 0, 1) }}
                          </span>
                        </div>
                        <span class="font-medium">{{ $reservation->reserver->name ?? 'N/A' }}</span>
                      </div>
                    </td>
                    <td>
                      <div class="text-sm">
                        <div class="font-medium">{{ \Carbon\Carbon::parse($reservation->start_time)->format('M d, Y') }}</div>
                        <div class="text-gray-600">{{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }}</div>
                      </div>
                    </td>
                    <td>
                      <div class="text-sm">
                        <div class="font-medium">{{ \Carbon\Carbon::parse($reservation->end_time)->format('M d, Y') }}</div>
                        <div class="text-gray-600">{{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}</div>
                      </div>
                    </td>
                    <td>
                      <span class="badge badge-{{ $reservation->status === 'pending' ? 'warning' : ($reservation->status === 'approved' ? 'success' : ($reservation->status === 'denied' ? 'error' : 'neutral')) }} badge-lg">
                        {{ ucfirst($reservation->status) }}
                      </span>
                    </td>
                    <td>
                      <div class="flex items-center gap-2">
                        <a href="{{ route('facility_reservations.show', $reservation->id) }}" class="btn btn-sm btn-outline">
                          <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                        </a>
                        @if($reservation->status === 'pending')
                          <form action="{{ route('facility_reservations.approve', $reservation->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                              <i data-lucide="check" class="w-4 h-4 mr-1"></i>Approve
                            </button>
                          </form>
                          <form action="{{ route('facility_reservations.deny', $reservation->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-error btn-sm">
                              <i data-lucide="x" class="w-4 h-4 mr-1"></i>Deny
                            </button>
                          </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center py-8">
                      <div class="flex flex-col items-center">
                        <i data-lucide="calendar" class="w-16 h-16 text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No Reservations Found</h3>
                        <p class="text-gray-500 mb-4">No facility reservations have been made yet.</p>
                        <a href="{{ route('facility_reservations.create') }}" class="btn btn-primary">
                          <i data-lucide="calendar-plus" class="w-4 h-4 mr-2"></i>
                          Make First Reservation
                        </a>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    // Dark mode functionality
    function setupDarkMode() {
      const toggle = document.getElementById('darkModeToggle');
      const sunIcon = document.getElementById('sunIcon');
      const moonIcon = document.getElementById('moonIcon');
      
      function updateIcons() {
        if(document.documentElement.classList.contains('dark')) {
          sunIcon.classList.remove('hidden');
          moonIcon.classList.add('hidden');
        } else {
          sunIcon.classList.add('hidden');
          moonIcon.classList.remove('hidden');
        }
      }
      
      // Initial state
      const isDarkMode = localStorage.getItem('darkMode') === 'true';
      if (isDarkMode) {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark');
      }
      updateIcons();
      
      toggle.addEventListener('click', function() {
        console.log('Dark mode toggle clicked!');
        
        // Direct toggle without relying on global function
        if (document.documentElement.classList.contains('dark')) {
          // Switch to light mode
          document.documentElement.classList.remove('dark');
          document.body.classList.remove('dark');
          localStorage.setItem('darkMode', 'false');
          console.log('Switched to LIGHT mode');
        } else {
          // Switch to dark mode
          document.documentElement.classList.add('dark');
          document.body.classList.add('dark');
          localStorage.setItem('darkMode', 'true');
          console.log('Switched to DARK mode');
        }
        
        updateIcons();
      });
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

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      setupDarkMode();
      updateDateTime();
      
      // Update time every second
      setInterval(updateDateTime, 1000);
    });
  </script>
</body>
</html>
