<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Management - Soliera</title>
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
            <h1 class="text-3xl font-bold text-gray-800 ml-4">Document Management</h1>
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
          <!-- Total Documents -->
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-blue-100 text-sm font-medium">Total Documents</p>
                <p class="text-3xl font-bold">{{ $documents->count() }}</p>
              </div>
              <div class="bg-blue-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="file-text" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Released Documents -->
          <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-green-100 text-sm font-medium">Released</p>
                <p class="text-3xl font-bold">{{ $documents->where('status', 'released')->count() }}</p>
              </div>
              <div class="bg-green-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="check-circle" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Pending Release -->
          <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-yellow-100 text-sm font-medium">Pending Release</p>
                <p class="text-3xl font-bold">{{ $documents->where('status', 'pending_release')->count() }}</p>
              </div>
              <div class="bg-yellow-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="clock" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Archived -->
          <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-100 text-sm font-medium">Archived</p>
                <p class="text-3xl font-bold">{{ $documents->where('status', 'archived')->count() }}</p>
              </div>
              <div class="bg-gray-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="archive" class="w-8 h-8"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 mb-8">
          <a href="{{ route('document.create') }}" class="btn btn-primary btn-lg">
            <i data-lucide="upload" class="w-5 h-5 mr-2"></i>
            Upload Document
          </a>
          <button class="btn btn-outline btn-lg">
            <i data-lucide="search" class="w-5 h-5 mr-2"></i>
            Search Documents
          </button>
          <button class="btn btn-outline btn-lg">
            <i data-lucide="filter" class="w-5 h-5 mr-2"></i>
            Filter by Status
          </button>
        </div>

        <!-- Documents Grid -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
              <i data-lucide="file-text" class="w-6 h-6 text-blue-500 mr-3"></i>
              Document Library
            </h2>
            <div class="flex items-center space-x-2">
              <span class="text-sm text-gray-500">Total: {{ $documents->count() }} documents</span>
            </div>
          </div>

          @if($documents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              @foreach($documents as $document)
                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:scale-105">
                  <div class="flex justify-between items-start mb-4">
                    <h3 class="font-bold text-gray-800 text-lg">{{ $document->title }}</h3>
                    <div class="badge badge-{{ $document->status === 'archived' ? 'neutral' : ($document->status === 'pending_release' ? 'warning' : 'success') }} badge-lg">
                      {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                    </div>
                  </div>
                  
                  @if($document->description)
                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($document->description, 100) }}</p>
                  @endif
                  
                  <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-500">
                      <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                      <span>Uploaded by: {{ $document->uploader->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                      <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                      <span>{{ $document->created_at->format('M d, Y H:i') }}</span>
                    </div>
                  </div>

                  <div class="flex space-x-2">
                    <a href="{{ route('document.show', $document->id) }}" class="btn btn-sm btn-outline flex-1">
                      <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                    </a>
                    
                    @if($document->status === 'archived')
                      <form action="{{ route('document.request-release', $document->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning w-full" onclick="return confirm('Request release for this document?')">
                          <i data-lucide="send" class="w-4 h-4 mr-1"></i>Request
                        </button>
                      </form>
                    @endif
                    
                    @if($document->status === 'released')
                      <a href="{{ route('document.download', $document->id) }}" class="btn btn-sm btn-success flex-1">
                        <i data-lucide="download" class="w-4 h-4 mr-1"></i>Download
                      </a>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-12">
              <i data-lucide="file-text" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
              <h3 class="text-lg font-semibold text-gray-600 mb-2">No Documents Found</h3>
              <p class="text-gray-500 mb-6">Upload your first document to get started.</p>
              <a href="{{ route('document.create') }}" class="btn btn-primary btn-lg">
                <i data-lucide="upload" class="w-5 h-5 mr-2"></i>
                Upload Document
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

    // Search functionality for document cards
    function setupSearch() {
      const searchInput = document.getElementById('searchInput');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const documentCards = document.querySelectorAll('.grid > div');
          
          documentCards.forEach(card => {
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