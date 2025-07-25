<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Visitor Management - Soliera</title>
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
            <h1 class="text-3xl font-bold text-gray-800 ml-4">Visitor Management</h1>
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

      <!-- Visitor Management Content -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow">
        <div class="pb-5 border-b border-base-300 animate-fadeIn">
          <h1 class="text-2xl font-semibold bg-white bg-clip-text text-[#191970]">Visitor Management</h1>
        </div>
        
        @if(session('success'))
          <div class="alert alert-success mb-6 animate-fadeIn">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
          </div>
        @endif
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
          <!-- Total Visitors -->
          <div class="card bg-gradient-to-br from-blue-50 to-blue-100 shadow-lg hover:shadow-xl transition-all duration-300 animate-fadeIn" style="animation-delay: 0.1s">
            <div class="card-body">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-blue-800">Total Visitors</h3>
                  <p class="text-3xl font-bold text-blue-900">{{ $visitors->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-blue-200 text-blue-600">
                  <i data-lucide="users" class="w-8 h-8"></i>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Active Visitors -->
          <div class="card bg-gradient-to-br from-green-50 to-green-100 shadow-lg hover:shadow-xl transition-all duration-300 animate-fadeIn" style="animation-delay: 0.2s">
            <div class="card-body">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-green-800">Currently In</h3>
                  <p class="text-3xl font-bold text-green-900">{{ $visitors->whereNull('time_out')->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-green-200 text-green-600">
                  <i data-lucide="user-check" class="w-8 h-8"></i>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Today's Visitors -->
          <div class="card bg-gradient-to-br from-purple-50 to-purple-100 shadow-lg hover:shadow-xl transition-all duration-300 animate-fadeIn" style="animation-delay: 0.3s">
            <div class="card-body">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-purple-800">Today's Visitors</h3>
                  <p class="text-3xl font-bold text-purple-900">{{ $visitors->where('time_in', '>=', now()->startOfDay())->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-purple-200 text-purple-600">
                  <i data-lucide="calendar" class="w-8 h-8"></i>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Completed Visits -->
          <div class="card bg-gradient-to-br from-orange-50 to-orange-100 shadow-lg hover:shadow-xl transition-all duration-300 animate-fadeIn" style="animation-delay: 0.4s">
            <div class="card-body">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-orange-800">Completed</h3>
                  <p class="text-3xl font-bold text-orange-900">{{ $visitors->whereNotNull('time_out')->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-orange-200 text-orange-600">
                  <i data-lucide="check-circle" class="w-8 h-8"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-between items-center mt-8 mb-6">
          <div class="flex gap-3">
            <a href="{{ route('visitor.create') }}" class="btn btn-primary hover:scale-105 transition-all">
              <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>Register New Visitor
            </a>
          </div>
          <div class="flex gap-2">
            <a href="{{ route('visitor.export.excel') }}" class="btn btn-success btn-sm hover:scale-105 transition-all">
              <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-1"></i>Export Excel
            </a>
            <a href="{{ route('visitor.export.pdf') }}" class="btn btn-error btn-sm hover:scale-105 transition-all">
              <i data-lucide="file-text" class="w-4 h-4 mr-1"></i>Export PDF
            </a>
          </div>
        </div>
        
        <!-- MODERN VISITOR INTERFACE -->
        <div class="mt-8">
          <!-- Navigation Tabs -->
          <div class="bg-gray-100 px-6 py-2 border-b border-gray-200">
            <div class="flex space-x-1">
              <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-blue-100 rounded-t-lg border-b-2 border-blue-500" onclick="showTab('current')">
                Current Visitors
              </button>
              <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-t-lg" onclick="showTab('scheduled')">
                Scheduled Visits
              </button>
              <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-t-lg" onclick="showTab('checkin')">
                Check In/Out
              </button>
            </div>
          </div>

          <!-- Main Content -->
          <div class="bg-white rounded-lg border border-gray-200 mt-0">
            <!-- Current Visitors Tab -->
            <div id="current-tab" class="h-96 flex">
              <!-- Left Panel - Current Visitors List -->
              <div class="w-2/3 p-6 overflow-y-auto">
                <div class="mb-6">
                  <h1 class="text-2xl font-bold text-gray-900 mb-1">Current Visitors</h1>
                  <p class="text-gray-600">Visitors currently in the building</p>
                </div>
                
                <!-- Search Bar -->
                <div class="mb-6">
                  <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text" placeholder="Search visitors..." class="input input-bordered w-full pl-10" id="visitorSearch" />
                  </div>
                </div>
                
                <!-- Visitor Cards -->
                <div class="space-y-4">
                  @forelse($visitors as $visitor)
                    <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer visitor-card" 
                         data-visitor-id="{{ $visitor->id }}"
                         onclick="selectVisitor({{ $visitor->id }})">
                      <div class="flex items-center justify-between">
                        <div class="flex-1">
                          <h3 class="font-semibold text-gray-900">{{ $visitor->name }}</h3>
                          <p class="text-sm text-gray-600">{{ $visitor->company ?? 'No Company' }}</p>
                          
                          <div class="flex items-center gap-4 mt-2">
                            @if($visitor->time_out)
                              <span class="badge badge-outline text-gray-500">Checked Out</span>
                            @else
                              <span class="badge badge-primary">Checked In</span>
                            @endif
                            
                            <span class="badge badge-outline text-gray-600">{{ $visitor->purpose }}</span>
                            
                            <div class="flex items-center gap-1 text-sm text-gray-500">
                              <i data-lucide="clock" class="w-3 h-3"></i>
                              <span>In: {{ \Carbon\Carbon::parse($visitor->time_in)->format('h:i A') }}</span>
                              @if($visitor->time_out)
                                <span>Out: {{ \Carbon\Carbon::parse($visitor->time_out)->format('h:i A') }}</span>
                              @endif
                            </div>
                            
                            <div class="flex items-center gap-1 text-sm text-gray-500">
                              <i data-lucide="building" class="w-3 h-3"></i>
                              <span>{{ $visitor->facility->name ?? 'No Location' }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @empty
                    <div class="text-center py-8">
                      <i data-lucide="users" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                      <p class="text-gray-500">No visitors found</p>
                    </div>
                  @endforelse
                </div>
              </div>
              
              <!-- Right Panel - Visitor Details -->
              <div class="w-1/3 bg-gray-50 border-l border-gray-200 p-6 overflow-y-auto">
                <div id="visitor-details" class="hidden">
                  <h2 class="text-xl font-bold text-gray-900 mb-1">Visitor Details</h2>
                  <p class="text-gray-600 mb-6">Information for <span id="selected-visitor-name">John Smith</span></p>
                  
                  <div class="bg-white rounded-lg border border-gray-200 p-4 space-y-4">
                    <div>
                      <label class="text-sm font-medium text-gray-700">Visitor Name</label>
                      <p class="text-gray-900" id="detail-name">John Smith</p>
                    </div>
                    
                    <div>
                      <label class="text-sm font-medium text-gray-700">Company</label>
                      <p class="text-gray-900" id="detail-company">Legal Partners LLC</p>
                    </div>
                    
                    <div>
                      <label class="text-sm font-medium text-gray-700">Purpose</label>
                      <p class="text-gray-900" id="detail-purpose">Contract Discussion</p>
                    </div>
                    
                    <div>
                      <label class="text-sm font-medium text-gray-700">Host Employee</label>
                      <p class="text-gray-900" id="detail-host">Sarah Johnson</p>
                    </div>
                    
                    <div>
                      <label class="text-sm font-medium text-gray-700">Floor/Location</label>
                      <p class="text-gray-900" id="detail-location">3rd Floor - Conference Room A</p>
                    </div>
                    
                    <div>
                      <label class="text-sm font-medium text-gray-700">Check-in Time</label>
                      <p class="text-gray-900" id="detail-checkin">9:30 AM</p>
                    </div>
                    
                    <div>
                      <label class="text-sm font-medium text-gray-700">Status</label>
                      <p class="text-gray-900" id="detail-status">Currently In Building</p>
                    </div>
                  </div>
                  
                  <div class="mt-6 space-y-3">
                    <button class="btn btn-primary w-full">
                      <i data-lucide="edit" class="w-4 h-4 mr-2"></i>Edit Visitor
                    </button>
                    <button class="btn btn-outline w-full">
                      <i data-lucide="log-out" class="w-4 h-4 mr-2"></i>Check Out
                    </button>
                  </div>
                </div>
                
                <div id="no-selection" class="text-center py-12">
                  <i data-lucide="user" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                  <p class="text-gray-500">Select a visitor to view details</p>
                </div>
              </div>
            </div>
            
            <!-- Scheduled Visits Tab -->
            <div id="scheduled-tab" class="h-96 p-6 overflow-y-auto hidden">
              <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Scheduled Visits</h1>
                <p class="text-gray-600">Upcoming visitor appointments</p>
              </div>
              
              <div class="space-y-4">
                <!-- Sample scheduled visits -->
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <h3 class="font-semibold text-gray-900">Lisa Brown</h3>
                      <p class="text-sm text-gray-600">Financial Advisors</p>
                      
                      <div class="flex items-center gap-4 mt-2">
                        <span class="badge badge-primary">Scheduled</span>
                        <span class="badge badge-outline text-gray-600">Budget Review</span>
                        
                        <div class="flex items-center gap-1 text-sm text-gray-500">
                          <i data-lucide="calendar" class="w-3 h-3"></i>
                          <span>2024-01-18</span>
                        </div>
                        
                        <div class="flex items-center gap-1 text-sm text-gray-500">
                          <i data-lucide="clock" class="w-3 h-3"></i>
                          <span>03:00 PM</span>
                        </div>
                        
                        <span class="text-sm text-gray-500">Host: Robert Taylor</span>
                      </div>
                    </div>
                    <button class="btn btn-outline btn-sm">Pre-Register</button>
                  </div>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <h3 class="font-semibold text-gray-900">James Anderson</h3>
                      <p class="text-sm text-gray-600">Security Systems</p>
                      
                      <div class="flex items-center gap-4 mt-2">
                        <span class="badge badge-primary">Scheduled</span>
                        <span class="badge badge-outline text-gray-600">Security Assessment</span>
                        
                        <div class="flex items-center gap-1 text-sm text-gray-500">
                          <i data-lucide="calendar" class="w-3 h-3"></i>
                          <span>2024-01-19</span>
                        </div>
                        
                        <div class="flex items-center gap-1 text-sm text-gray-500">
                          <i data-lucide="clock" class="w-3 h-3"></i>
                          <span>10:00 AM</span>
                        </div>
                        
                        <span class="text-sm text-gray-500">Host: Sarah Johnson</span>
                      </div>
                    </div>
                    <button class="btn btn-outline btn-sm">Pre-Register</button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Check In/Out Tab -->
            <div id="checkin-tab" class="h-96 p-6 overflow-y-auto hidden">
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left: Check-In Form -->
                <div>
                  <h2 class="text-xl font-bold text-gray-900 mb-1">Visitor Check-In</h2>
                  <p class="text-gray-600 mb-6">Register a new visitor or check in scheduled visitor</p>
                  
                  <form id="checkin-form" class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Visitor Name</label>
                      <input type="text" name="name" placeholder="Enter visitor name" class="input input-bordered w-full" required />
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                      <input type="text" name="company" placeholder="Enter company name" class="input input-bordered w-full" />
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Purpose of Visit</label>
                      <input type="text" name="purpose" placeholder="Meeting, consultation, etc." class="input input-bordered w-full" />
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Host Employee</label>
                      <input type="text" name="host_employee" placeholder="Enter host employee name" class="input input-bordered w-full" />
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Facility/Location</label>
                      <select name="facility_id" class="select select-bordered w-full">
                        <option value="">Select facility</option>
                        @foreach($facilities as $facility)
                          <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    
                    <button type="button" onclick="checkInVisitor()" class="btn btn-primary w-full">
                      <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                      Check In Visitor
                    </button>
                  </form>
                </div>
                
                <!-- Right: Quick Actions -->
                <div>
                  <h2 class="text-xl font-bold text-gray-900 mb-1">Quick Actions</h2>
                  <p class="text-gray-600 mb-6">Common visitor management tasks</p>
                  
                  <div class="space-y-3">
                    <div class="bg-white rounded-lg border border-gray-200 p-4 text-center hover:shadow-md transition-shadow cursor-pointer" onclick="viewAllVisitors()">
                      <i data-lucide="users" class="w-6 h-6 text-gray-900 mx-auto mb-2"></i>
                      <p class="text-sm font-medium text-gray-900">View All Visitors</p>
                    </div>
                    
                    <div class="bg-white rounded-lg border border-gray-200 p-4 text-center hover:shadow-md transition-shadow cursor-pointer" onclick="scheduleVisit()">
                      <i data-lucide="calendar" class="w-6 h-6 text-gray-900 mx-auto mb-2"></i>
                      <p class="text-sm font-medium text-gray-900">Schedule Visit</p>
                    </div>
                    
                    <div class="bg-white rounded-lg border border-gray-200 p-4 text-center hover:shadow-md transition-shadow cursor-pointer" onclick="emergencyEvacuation()">
                      <i data-lucide="x-circle" class="w-6 h-6 text-gray-900 mx-auto mb-2"></i>
                      <p class="text-sm font-medium text-gray-900">Emergency Evacuation</p>
                    </div>
                    
                    <div class="bg-white rounded-lg border border-gray-200 p-4 text-center hover:shadow-md transition-shadow cursor-pointer" onclick="buildingDirectory()">
                      <i data-lucide="building" class="w-6 h-6 text-gray-900 mx-auto mb-2"></i>
                      <p class="text-sm font-medium text-gray-900">Building Directory</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    // Global variables
    let currentVisitorId = null;
    let currentTab = 'current';

    // Tab functionality
    function showTab(tabName) {
      currentTab = tabName;
      
      // Hide all tabs
      document.getElementById('current-tab').classList.add('hidden');
      document.getElementById('scheduled-tab').classList.add('hidden');
      document.getElementById('checkin-tab').classList.add('hidden');
      
      // Show selected tab
      document.getElementById(tabName + '-tab').classList.remove('hidden');
      
      // Update tab buttons
      const tabs = document.querySelectorAll('[onclick^="showTab"]');
      tabs.forEach(tab => {
        tab.classList.remove('bg-blue-100', 'text-gray-700', 'border-blue-500');
        tab.classList.add('text-gray-500');
      });
      
      event.target.classList.remove('text-gray-500');
      event.target.classList.add('bg-blue-100', 'text-gray-700', 'border-blue-500');

      // Load data for the selected tab
      loadTabData(tabName);
    }

    // Load data for specific tabs
    function loadTabData(tabName) {
      switch(tabName) {
        case 'current':
          loadCurrentVisitors();
          break;
        case 'scheduled':
          loadScheduledVisits();
          break;
        case 'checkin':
          // Check-in form is static, no data loading needed
          break;
      }
    }

    // Load current visitors
    function loadCurrentVisitors() {
      fetch('{{ route("visitor.current") }}')
        .then(response => response.json())
        .then(visitors => {
          updateVisitorCards(visitors);
        })
        .catch(error => {
          console.error('Error loading current visitors:', error);
          showNotification('Error loading visitors', 'error');
        });
    }

    // Load scheduled visits
    function loadScheduledVisits() {
      fetch('{{ route("visitor.scheduled") }}')
        .then(response => response.json())
        .then(visitors => {
          updateScheduledVisits(visitors);
        })
        .catch(error => {
          console.error('Error loading scheduled visits:', error);
          showNotification('Error loading scheduled visits', 'error');
        });
    }

    // Update visitor cards in the current visitors tab
    function updateVisitorCards(visitors) {
      const container = document.querySelector('#current-tab .space-y-4');
      if (!container) return;

      if (visitors.length === 0) {
        container.innerHTML = `
          <div class="text-center py-8">
            <i data-lucide="users" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
            <p class="text-gray-500">No visitors currently in the building</p>
          </div>
        `;
        return;
      }

      container.innerHTML = visitors.map(visitor => `
        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer visitor-card" 
             data-visitor-id="${visitor.id}"
             onclick="selectVisitor(${visitor.id})">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900">${visitor.name}</h3>
              <p class="text-sm text-gray-600">${visitor.company || 'No Company'}</p>
              
              <div class="flex items-center gap-4 mt-2">
                ${visitor.time_out ? 
                  '<span class="badge badge-outline text-gray-500">Checked Out</span>' : 
                  '<span class="badge badge-primary">Checked In</span>'
                }
                
                <span class="badge badge-outline text-gray-600">${visitor.purpose || 'No Purpose'}</span>
                
                <div class="flex items-center gap-1 text-sm text-gray-500">
                  <i data-lucide="clock" class="w-3 h-3"></i>
                  <span>In: ${formatTime(visitor.time_in)}</span>
                  ${visitor.time_out ? `<span>Out: ${formatTime(visitor.time_out)}</span>` : ''}
                </div>
                
                <div class="flex items-center gap-1 text-sm text-gray-500">
                  <i data-lucide="building" class="w-3 h-3"></i>
                  <span>${visitor.facility ? visitor.facility.name : 'No Location'}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      `).join('');

      // Recreate icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

    // Update scheduled visits
    function updateScheduledVisits(visitors) {
      const container = document.querySelector('#scheduled-tab .space-y-4');
      if (!container) return;

      if (visitors.length === 0) {
        container.innerHTML = `
          <div class="text-center py-8">
            <i data-lucide="calendar" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
            <p class="text-gray-500">No scheduled visits for today</p>
          </div>
        `;
        return;
      }

      container.innerHTML = visitors.map(visitor => `
        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer visitor-card" 
             data-visitor-id="${visitor.id}"
             onclick="selectVisitor(${visitor.id})">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900">${visitor.name}</h3>
              <p class="text-sm text-gray-600">${visitor.company || 'No Company'}</p>
              
              <div class="flex items-center gap-4 mt-2">
                <span class="badge badge-warning">Scheduled</span>
                <span class="badge badge-outline text-gray-600">${visitor.purpose || 'No Purpose'}</span>
                
                <div class="flex items-center gap-1 text-sm text-gray-500">
                  <i data-lucide="clock" class="w-3 h-3"></i>
                  <span>Scheduled: ${formatTime(visitor.time_in)}</span>
                </div>
                
                <div class="flex items-center gap-1 text-sm text-gray-500">
                  <i data-lucide="building" class="w-3 h-3"></i>
                  <span>${visitor.facility ? visitor.facility.name : 'No Location'}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      `).join('');

      // Recreate icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

    // Visitor selection
    function selectVisitor(visitorId) {
      currentVisitorId = visitorId;
      
      // Remove selection from all cards
      document.querySelectorAll('.visitor-card').forEach(card => {
        card.classList.remove('ring-2', 'ring-blue-500');
      });
      
      // Add selection to clicked card
      event.currentTarget.classList.add('ring-2', 'ring-blue-500');
      
      // Fetch visitor details
      fetch(`{{ route('visitor.details', '') }}/${visitorId}`)
        .then(response => response.json())
        .then(visitor => {
          updateVisitorDetails(visitor);
        })
        .catch(error => {
          console.error('Error loading visitor details:', error);
          showNotification('Error loading visitor details', 'error');
        });
    }

    // Update visitor details panel
    function updateVisitorDetails(visitor) {
      // Show visitor details
      document.getElementById('no-selection').classList.add('hidden');
      document.getElementById('visitor-details').classList.remove('hidden');
      
      // Update details
      document.getElementById('selected-visitor-name').textContent = visitor.name;
      document.getElementById('detail-name').textContent = visitor.name;
      document.getElementById('detail-company').textContent = visitor.company || 'No Company';
      document.getElementById('detail-purpose').textContent = visitor.purpose || 'No Purpose';
      document.getElementById('detail-host').textContent = visitor.host_employee || 'No Host';
      document.getElementById('detail-location').textContent = visitor.facility ? visitor.facility.name : 'No Location';
      document.getElementById('detail-checkin').textContent = formatTime(visitor.time_in);
      document.getElementById('detail-status').textContent = visitor.time_out ? 'Checked Out' : 'Currently In Building';

      // Update action buttons
      const checkoutBtn = document.querySelector('#visitor-details .btn-outline');
      if (checkoutBtn) {
        if (visitor.time_out) {
          checkoutBtn.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>Already Checked Out';
          checkoutBtn.disabled = true;
          checkoutBtn.classList.add('btn-disabled');
        } else {
          checkoutBtn.innerHTML = '<i data-lucide="log-out" class="w-4 h-4 mr-2"></i>Check Out';
          checkoutBtn.disabled = false;
          checkoutBtn.classList.remove('btn-disabled');
          checkoutBtn.onclick = () => checkOutVisitor(visitor.id);
        }
      }

      // Recreate icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

    // Check out visitor
    function checkOutVisitor(visitorId) {
      if (confirm('Are you sure you want to check out this visitor?')) {
        fetch(`{{ route('visitor.checkout', '') }}/${visitorId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification(data.message, 'success');
            // Refresh current visitors
            loadCurrentVisitors();
            // Update stats
            updateStats();
          } else {
            showNotification('Error checking out visitor', 'error');
          }
        })
        .catch(error => {
          console.error('Error checking out visitor:', error);
          showNotification('Error checking out visitor', 'error');
        });
      }
    }

    // Check in visitor
    function checkInVisitor() {
      const formData = new FormData(document.getElementById('checkin-form'));
      
      fetch('{{ route("visitor.checkin") }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification(data.message, 'success');
            // Clear form
            document.getElementById('checkin-form').reset();
            // Refresh current visitors
            loadCurrentVisitors();
            // Update stats
            updateStats();
          } else {
            showNotification('Error checking in visitor', 'error');
          }
        })
        .catch(error => {
          console.error('Error checking in visitor:', error);
          showNotification('Error checking in visitor', 'error');
        });
    }

    // Update stats
    function updateStats() {
      fetch('{{ route("visitor.stats") }}')
        .then(response => response.json())
        .then(stats => {
          // Update stat cards
          document.querySelector('.text-blue-900').textContent = stats.total;
          document.querySelector('.text-green-900').textContent = stats.currentlyIn;
          document.querySelector('.text-purple-900').textContent = stats.todayVisitors;
          document.querySelector('.text-orange-900').textContent = stats.completed;
        })
        .catch(error => {
          console.error('Error updating stats:', error);
        });
    }

    // Quick Actions
    function viewAllVisitors() {
      fetch('{{ route("visitor.quick.viewAll") }}')
        .then(response => response.json())
        .then(visitors => {
          showNotification(`Found ${visitors.length} total visitors`, 'info');
          // You could open a modal with all visitors here
        })
        .catch(error => {
          console.error('Error loading all visitors:', error);
          showNotification('Error loading visitors', 'error');
        });
    }

    function scheduleVisit() {
      // Open scheduling modal or redirect to scheduling page
      showNotification('Scheduling feature coming soon!', 'info');
    }

    function emergencyEvacuation() {
      if (confirm('Are you sure you want to activate emergency evacuation protocol?')) {
        fetch('{{ route("visitor.quick.emergency") }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          }
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showNotification(data.message, 'success');
            } else {
              showNotification('Error activating emergency protocol', 'error');
            }
          })
          .catch(error => {
            console.error('Error activating emergency protocol:', error);
            showNotification('Error activating emergency protocol', 'error');
          });
      }
    }

    function buildingDirectory() {
      fetch('{{ route("visitor.quick.directory") }}')
        .then(response => response.json())
        .then(facilities => {
          showNotification(`Found ${facilities.length} facilities in directory`, 'info');
          // You could open a modal with building directory here
        })
        .catch(error => {
          console.error('Error loading building directory:', error);
          showNotification('Error loading building directory', 'error');
        });
    }

    // Utility functions
    function formatTime(timeString) {
      const date = new Date(timeString);
      return date.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit', 
        hour12: true 
      });
    }

    function showNotification(message, type = 'info') {
      // Create notification element
      const notification = document.createElement('div');
      notification.className = `alert alert-${type === 'error' ? 'error' : type === 'success' ? 'success' : 'info'} fixed top-4 right-4 z-50 max-w-sm`;
      notification.innerHTML = `
        <i data-lucide="${type === 'error' ? 'alert-circle' : type === 'success' ? 'check-circle' : 'info'}" class="w-5 h-5"></i>
        <span>${message}</span>
      `;
      
      document.body.appendChild(notification);
      
      // Recreate icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
      
      // Remove after 3 seconds
      setTimeout(() => {
        notification.remove();
      }, 3000);
    }

    // Search functionality - filters visitor cards in real-time
    function setupSearch() {
      const searchInput = document.getElementById('visitorSearch');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const visitorCards = document.querySelectorAll('.visitor-card');
          
          visitorCards.forEach(card => {
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
        icon.classList.remove('text-yellow-500', 'text-white');
        icon.classList.add('text-gray-600');
        
        const button = icon.closest('.btn');
        if (button) {
          button.classList.remove('bg-blue-600', 'bg-blue-700', 'text-white');
          button.classList.add('btn-ghost');
        }
        
        header.classList.remove('dark:bg-gray-800', 'dark:border-gray-700');
        header.classList.add('bg-white', 'border-gray-200');
        
        if (sidebar) sidebar.classList.remove('dark:bg-gray-900');
        if (main) main.classList.remove('dark:bg-gray-900');
        
      } else {
        // Switch to dark mode
        html.classList.add('dark');
        body.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
        icon.setAttribute('data-lucide', 'sun');
        icon.classList.remove('text-gray-600');
        icon.classList.add('text-yellow-500');
        
        const button = icon.closest('.btn');
        if (button) {
          button.classList.remove('bg-blue-600', 'bg-blue-700', 'text-white');
          button.classList.add('btn-ghost');
        }
        
        header.classList.remove('bg-white', 'border-gray-200');
        header.classList.add('dark:bg-gray-800', 'dark:border-gray-700');
        
        if (sidebar) sidebar.classList.add('dark:bg-gray-900');
        if (main) main.classList.add('dark:bg-gray-900');
      }
      
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
        icon.classList.remove('text-gray-600', 'text-white');
        icon.classList.add('text-yellow-500');
        
        const button = icon.closest('.btn');
        if (button) {
          button.classList.remove('bg-blue-600', 'bg-blue-700', 'text-white');
          button.classList.add('btn-ghost');
        }
        
        header.classList.remove('bg-white', 'border-gray-200');
        header.classList.add('dark:bg-gray-800', 'dark:border-gray-700');
        
        if (sidebar) sidebar.classList.add('dark:bg-gray-900');
        if (main) main.classList.add('dark:bg-gray-900');
      } else {
        html.classList.remove('dark');
        body.classList.remove('dark');
        icon.setAttribute('data-lucide', 'moon');
        icon.classList.remove('text-yellow-500', 'text-white');
        icon.classList.add('text-gray-600');
        
        const button = icon.closest('.btn');
        if (button) {
          button.classList.remove('bg-blue-600', 'bg-blue-700', 'text-white');
          button.classList.add('btn-ghost');
        }
        
        header.classList.remove('dark:bg-gray-800', 'dark:border-gray-700');
        header.classList.add('bg-white', 'border-gray-200');
        
        if (sidebar) sidebar.classList.remove('dark:bg-gray-900');
        if (main) main.classList.remove('dark:bg-gray-900');
      }
      
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

    // Logout function
    function logout() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = '{{ route("logout") }}';
      }
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      initializeDarkMode();
      updateDateTime();
      setupSearch();
      
      // Update time every second
      setInterval(updateDateTime, 1000);
      
      // Load initial data
      loadCurrentVisitors();
      updateStats();
    });
  </script>
</body>
</html> 