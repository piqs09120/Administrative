<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Facilities Management - Soliera</title>
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
            <h1 class="text-3xl font-bold text-gray-800 ml-4">Facilities Management</h1>
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Facilities -->
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-blue-100 text-sm font-medium">Total Facilities</p>
                <p class="text-3xl font-bold">{{ $facilities->count() }}</p>
              </div>
              <div class="bg-blue-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="building" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Available Facilities -->
          <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-green-100 text-sm font-medium">Available</p>
                <p class="text-3xl font-bold">{{ $facilities->where('status', 'available')->count() }}</p>
              </div>
              <div class="bg-green-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="check-circle" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Occupied Facilities -->
          <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-red-100 text-sm font-medium">Occupied</p>
                <p class="text-3xl font-bold">{{ $facilities->where('status', 'occupied')->count() }}</p>
              </div>
              <div class="bg-red-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="x-circle" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Total Reservations -->
          <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-purple-100 text-sm font-medium">Total Reservations</p>
                <p class="text-3xl font-bold">{{ $facilities->sum(function($facility) { return $facility->reservations->count(); }) }}</p>
              </div>
              <div class="bg-purple-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="calendar" class="w-8 h-8"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 mb-8">
          <a href="{{ route('facilities.create') }}" class="btn btn-primary btn-lg">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Add New Facility
          </a>
          <a href="{{ route('facility_reservations.index') }}" class="btn btn-outline btn-lg">
            <i data-lucide="calendar" class="w-5 h-5 mr-2"></i>
            View Reservations
          </a>
          <button class="btn btn-outline btn-lg">
            <i data-lucide="filter" class="w-5 h-5 mr-2"></i>
            Filter by Status
          </button>
        </div>

        <!-- Facilities Grid -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
              <i data-lucide="building" class="w-6 h-6 text-blue-500 mr-3"></i>
              Facility Directory
            </h2>
            <div class="flex items-center space-x-2">
              <span class="text-sm text-gray-500">Total: {{ $facilities->count() }} facilities</span>
            </div>
          </div>

          @if($facilities->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              @foreach($facilities as $facility)
                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:scale-105">
                  <div class="flex justify-between items-start mb-4">
                    <h3 class="font-bold text-gray-800 text-lg">{{ $facility->name }}</h3>
                    <div class="badge badge-{{ $facility->status === 'available' ? 'success' : 'error' }} badge-lg">
                      {{ ucfirst($facility->status) }}
                    </div>
                  </div>
                  
                  @if($facility->location)
                    <div class="flex items-center text-sm text-gray-600 mb-3">
                      <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i>
                      <span>{{ $facility->location }}</span>
                    </div>
                  @endif
                  
                  @if($facility->description)
                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($facility->description, 100) }}</p>
                  @endif

                  <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-500">
                      <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                      <span>Reservations: {{ $facility->reservations->count() }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                      <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                      <span>Updated: {{ $facility->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                  </div>

                  <div class="flex space-x-2">
                    <a href="{{ route('facilities.show', $facility->id) }}" class="btn btn-sm btn-outline flex-1">
                      <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                    </a>
                    <a href="{{ route('facilities.edit', $facility->id) }}" class="btn btn-sm btn-outline flex-1">
                      <i data-lucide="edit" class="w-4 h-4 mr-1"></i>Edit
                    </a>
                    @if($facility->status === 'available')
                      <a href="{{ route('facility_reservations.create') }}?facility={{ $facility->id }}" class="btn btn-sm btn-primary flex-1">
                        <i data-lucide="calendar-plus" class="w-4 h-4 mr-1"></i>Reserve
                      </a>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-12">
              <i data-lucide="building" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
              <h3 class="text-lg font-semibold text-gray-600 mb-2">No Facilities Found</h3>
              <p class="text-gray-500 mb-6">Add your first facility to get started.</p>
              <a href="{{ route('facilities.create') }}" class="btn btn-primary btn-lg">
                <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                Add Facility
              </a>
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

    // Search functionality for facility cards
    function setupSearch() {
      const searchInput = document.getElementById('searchInput');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const facilityCards = document.querySelectorAll('.grid > div');
          
          facilityCards.forEach(card => {
            const text = card.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
              card.style.display = '';
            } else {
              card.style.display = 'none';
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
  </script>
</body>
</html> 