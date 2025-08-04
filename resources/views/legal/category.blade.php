<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $categoryName }} - Soliera</title>
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
      <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <button onclick="toggleSidebar()" class="btn btn-ghost btn-sm hover:bg-base-300 transition-all hover:scale-105">
              <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h1 class="text-3xl font-bold text-gray-800 ml-4">{{ $categoryName }}</h1>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Search Bar -->
            <div class="relative">
              <input type="text" id="searchInput" placeholder="Search documents..." class="input input-bordered input-md w-64 pl-10 bg-white border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
              <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Date and Time -->
            <div class="flex items-center space-x-2 bg-gray-100 px-4 py-3 rounded-lg">
              <i data-lucide="calendar" class="w-5 h-5 text-blue-500"></i>
              <span id="currentDate" class="text-base font-medium text-gray-700"></span>
              <div class="w-px h-5 bg-gray-300"></div>
              <i data-lucide="clock" class="w-5 h-5 text-green-500"></i>
              <span id="currentTime" class="text-base font-medium text-gray-700"></span>
            </div>
            
            <!-- Moon Icon (Dark Mode Toggle) -->
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

                 <!-- Breadcrumbs -->
         <div class="text-sm text-gray-600 mb-4">
           <a href="{{ route('legal.index') }}" class="hover:text-blue-600">Legal Management</a>
           <span class="mx-2">></span>
           <a href="{{ route('legal.index') }}" class="hover:text-blue-600">Legal Document Folders</a>
           <span class="mx-2">></span>
           <span class="text-gray-800">{{ $categoryName }}</span>
         </div>

         <!-- Page Title -->
         <div class="mb-6">
           <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $categoryName }}</h1>
           <p class="text-gray-600">Browse {{ strtolower($categoryName) }} from {{ date('Y') }}. Documents are automatically classified by AI.</p>
         </div>

                   <!-- Action Bar -->
          <div class="flex items-center justify-end mb-6">
            <div class="flex items-center space-x-3">
              <span class="text-gray-600">{{ $documents->total() }} documents</span>
              <button class="btn btn-outline btn-success btn-sm">
                <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                FILTER
              </button>
            </div>
          </div>

                   <!-- Documents Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if($documents->count() > 0)
              @foreach($documents as $document)
                @php
                  // Generate different accent colors for visual variety
                  $accentColors = ['bg-red-500', 'bg-yellow-500', 'bg-green-500', 'bg-blue-500', 'bg-purple-500', 'bg-orange-500'];
                  $accentColor = $accentColors[$loop->index % count($accentColors)];
                @endphp
                <div class="bg-white border border-gray-200 rounded-lg hover:shadow-lg transition-shadow overflow-hidden">
                  <!-- Colored Left Border Accent -->
                  <div class="flex">
                    <div class="{{ $accentColor }} w-2"></div>
                    <div class="flex-1 p-6">
                      <!-- Card Header -->
                      <div class="flex items-start justify-between mb-4">
                        <h3 class="font-bold text-gray-800 text-lg leading-tight">{{ $document->title }}</h3>
                        <div class="dropdown dropdown-end">
                          <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                          </div>
                          <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-white rounded-lg w-32 border border-gray-200">
                            <li><a href="{{ route('document.show', $document->id) }}" class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
                              <i data-lucide="eye" class="w-4 h-4"></i>
                              <span>View</span>
                            </a></li>
                            <li><a href="{{ route('document.download', $document->id) }}" class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
                              <i data-lucide="download" class="w-4 h-4"></i>
                              <span>Download</span>
                            </a></li>
                            <li><a href="{{ route('document.edit', $document->id) }}" class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
                              <i data-lucide="edit" class="w-4 h-4"></i>
                              <span>Edit</span>
                            </a></li>
                          </ul>
                        </div>
                      </div>

                      <!-- Subtitle/Description -->
                      <p class="text-gray-600 text-sm mb-4">{{ Str::limit($document->description, 80) }}</p>

                      <!-- Status/Type Badges -->
                      <div class="flex flex-wrap gap-2 mb-4">
                        @if($document->status === 'archived')
                          <span class="badge badge-neutral gap-1">
                            <i data-lucide="archive" class="w-3 h-3"></i>
                            Archived
                          </span>
                        @elseif($document->status === 'pending_release')
                          <span class="badge badge-warning gap-1">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            Pending Release
                          </span>
                        @else
                          <span class="badge badge-info gap-1">
                            <i data-lucide="file-text" class="w-3 h-3"></i>
                            {{ ucfirst($document->status) }}
                          </span>
                        @endif
                        <span class="badge badge-accent">{{ $categoryName }}</span>
                      </div>

                      <!-- Document Details -->
                      <div class="space-y-2 text-sm text-gray-500">
                        <div class="flex items-center gap-2">
                          <i data-lucide="user" class="w-4 h-4"></i>
                          <span>{{ $document->uploader->name ?? 'Unknown' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                          <i data-lucide="calendar" class="w-4 h-4"></i>
                          <span>{{ $document->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                          <i data-lucide="file-text" class="w-4 h-4"></i>
                          <span>Author: {{ $document->uploader->name ?? 'Unknown' }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
           @else
             <div class="col-span-full text-center py-12">
               <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                 <i data-lucide="folder" class="w-12 h-12 text-gray-400"></i>
               </div>
               <h3 class="text-lg font-semibold text-gray-600 mb-2">No Documents Found</h3>
               <p class="text-gray-500 mb-6">No documents have been classified as {{ $categoryName }} yet.</p>
               <a href="{{ route('legal.create') }}" class="btn btn-warning">
                 <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                 Add First Document
               </a>
             </div>
           @endif
         </div>

         <!-- Pagination -->
         @if($documents->hasPages())
           <div class="mt-8">
             {{ $documents->links() }}
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
        if (document.documentElement.classList.contains('dark')) {
          document.documentElement.classList.remove('dark');
          document.body.classList.remove('dark');
          localStorage.setItem('darkMode', 'false');
        } else {
          document.documentElement.classList.add('dark');
          document.body.classList.add('dark');
          localStorage.setItem('darkMode', 'true');
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

    // Search functionality
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