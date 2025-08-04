<!DOCTYPE html>
<html lang="en" data-theme="light">
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
      <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <button onclick="toggleSidebar()" class="btn btn-ghost btn-sm hover:bg-base-300 transition-all hover:scale-105">
              <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h1 class="text-3xl font-bold text-gray-800 ml-4">Document Details</h1>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Search Bar -->
            <div class="relative">
              <input type="text" id="searchInput" placeholder="Search..." class="input input-bordered input-md w-64 pl-10 bg-white border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
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

        <!-- Back button and title -->
        <div class="flex items-center mb-6">
          <a href="{{ route('document.index') }}" class="btn btn-ghost btn-sm mr-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back
          </a>
        </div>

        <!-- Success Banner -->
        @if(session('success') && str_contains(session('success'), 'uploaded'))
          <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
            <div class="flex items-center">
              <i data-lucide="check-circle" class="w-6 h-6 mr-3"></i>
              <span class="text-lg font-medium">Document uploaded and analyzed successfully!</span>
            </div>
          </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Document Information -->
          <div class="lg:col-span-2">
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <div class="flex justify-between items-start mb-4">
                  <h2 class="card-title text-2xl">{{ $document->title }}</h2>
                  <div class="badge badge-lg badge-{{ $document->status === 'archived' ? 'neutral' : ($document->status === 'pending_release' ? 'warning' : 'success') }}">
                    {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                  </div>
                </div>

                @if($document->description)
                  <div class="mb-4">
                    <h3 class="font-semibold text-gray-700 mb-2">Description</h3>
                    <p class="text-gray-600">{{ $document->description }}</p>
                  </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1">Uploaded By</h3>
                    <p class="text-gray-600">{{ $document->uploader->name ?? 'Unknown' }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1">File Path</h3>
                    <p class="text-gray-600 text-sm">{{ $document->file_path }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1">Category</h3>
                    <p class="text-gray-600">{{ $document->category ?? 'Uncategorized' }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1">Upload Date</h3>
                    <p class="text-gray-600">{{ $document->created_at->format('M d, Y H:i') }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1">Last Updated</h3>
                    <p class="text-gray-600">{{ $document->updated_at->format('M d, Y H:i') }}</p>
                  </div>
                </div>

                <!-- AI Tags -->
                @if($document->ai_analysis && isset($document->ai_analysis['tags']))
                  <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-3">AI Tags</h3>
                    <div class="flex flex-wrap gap-2">
                      @foreach($document->ai_analysis['tags'] as $tag)
                        <span class="badge badge-outline badge-lg">{{ $tag }}</span>
                      @endforeach
                    </div>
                  </div>
                @endif

                <!-- Compliance Status -->
                @if($document->ai_analysis && isset($document->ai_analysis['compliance_status']))
                  <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-2">Compliance Status</h3>
                    <span class="badge badge-{{ $document->ai_analysis['compliance_status'] === 'compliant' ? 'success' : ($document->ai_analysis['compliance_status'] === 'non-compliant' ? 'error' : 'warning') }} badge-lg">
                      {{ ucfirst(str_replace('_', ' ', $document->ai_analysis['compliance_status'])) }}
                    </span>
                  </div>
                @endif

                <div class="card-actions">
                  @if($document->status === 'archived')
                    <form action="{{ route('document.request-release', $document->id) }}" method="POST" class="inline">
                      @csrf
                      <button type="submit" class="btn btn-warning" onclick="return confirm('Request release for this document?')">
                        <i data-lucide="send" class="w-4 h-4 mr-2"></i>Request Release
                      </button>
                    </form>
                  @endif
                  
                  <form action="{{ route('document.analyze', $document->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                      <i data-lucide="brain" class="w-4 h-4 mr-2"></i>AI Analysis
                    </button>
                  </form>
                  
                  <a href="{{ route('document.edit', $document->id) }}" class="btn btn-outline">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>Edit
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Request History -->
          <div class="lg:col-span-1">
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4">
                  <i data-lucide="rotate-ccw" class="w-5 h-5 mr-2"></i>Request History
                </h3>

                @if($document->documentRequests->count() > 0)
                  <div class="space-y-3">
                    @foreach($document->documentRequests->sortByDesc('created_at') as $request)
                      <div class="border-l-4 border-{{ $request->status === 'pending' ? 'yellow' : ($request->status === 'approved' ? 'green' : 'red') }}-500 pl-3">
                        <div class="flex justify-between items-start">
                          <div>
                            <p class="font-semibold text-sm">
                              {{ ucfirst($request->status) }}
                            </p>
                            <p class="text-xs text-gray-500">
                              Requested by: {{ $request->requester->name ?? 'Unknown' }}
                            </p>
                            <p class="text-xs text-gray-500">
                              {{ $request->created_at->format('M d, Y H:i') }}
                            </p>
                          </div>
                          <div class="badge badge-sm badge-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : 'error') }}">
                            {{ ucfirst($request->status) }}
                          </div>
                        </div>
                        
                        @if($request->approved_by)
                          <p class="text-xs text-gray-500 mt-1">
                            {{ $request->status === 'approved' ? 'Approved' : 'Denied' }} by: {{ $request->approver->name ?? 'Unknown' }}
                          </p>
                        @endif
                        
                        @if($request->remarks)
                          <p class="text-xs text-gray-600 mt-1 italic">
                            "{{ $request->remarks }}"
                          </p>
                        @endif
                      </div>
                    @endforeach
                  </div>
                @else
                  <div class="text-center py-4">
                    <i data-lucide="folder" class="w-12 h-12 text-gray-300 mx-auto mb-2"></i>
                    <p class="text-gray-500 text-sm">No request history</p>
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