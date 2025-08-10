<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Management - Soliera</title>
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Documents -->
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer">
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
          <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer">
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
          <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer">
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
          <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer">
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
                    @if($document->department)
                      <div class="flex items-center text-sm text-gray-500">
                        <i data-lucide="building" class="w-4 h-4 mr-2"></i>
                        <span>Department: {{ $document->department }}</span>
                      </div>
                    @endif
                    @if($document->author)
                      <div class="flex items-center text-sm text-gray-500">
                        <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                        <span>Author: {{ $document->author }}</span>
                      </div>
                    @endif
                    <div class="flex items-center text-sm text-gray-500">
                      <i data-lucide="user-check" class="w-4 h-4 mr-2"></i>
                      <span>Uploaded by: {{ $document->uploader->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                      <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                      <span>{{ $document->created_at->format('M d, Y H:i') }}</span>
                    </div>
                  </div>

                  <div class="flex flex-wrap gap-2">
                    <a href="{{ route('document.show', $document->id) }}" class="btn btn-sm btn-outline flex-1 min-w-0">
                      <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                    </a>
                    
                    @if($document->status === 'archived')
                      <form action="{{ route('document.request-release', $document->id) }}" method="POST" class="flex-1 min-w-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning w-full" onclick="return confirm('Request release for this document?')">
                          <i data-lucide="send" class="w-4 h-4 mr-1"></i>Request
                        </button>
                      </form>
                    @endif
                    
                    @if($document->status === 'released')
                      <a href="{{ route('document.download', $document->id) }}" class="btn btn-sm btn-success flex-1 min-w-0">
                        <i data-lucide="download" class="w-4 h-4 mr-1"></i>Download
                      </a>
                    @endif
                    
                    <form action="{{ route('document.destroy', $document->id) }}" method="POST" class="flex-1 min-w-0">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-error w-full" onclick="return confirm('Are you sure you want to delete this document? This action cannot be undone.')">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>Delete
                      </button>
                    </form>
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

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      setupDarkMode();
      updateDateTime();
      setupSearch();
      
      // Update time every second
      setInterval(updateDateTime, 1000);
    });
  </script>
</body>
</html> 