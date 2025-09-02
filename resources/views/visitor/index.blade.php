<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Visitor Management - Soliera</title>
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

      <!-- Visitor Management Content -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 transition-slow">
        <div class="pb-5 border-b border-base-300 animate-fadeIn">
          <h1 class="text-2xl font-semibold bg-white bg-clip-text text-[#191970]" style="color: var(--color-charcoal-ink);">Visitor Management</h1>
        </div>
        
        @if(session('success'))
          <div class="alert alert-success mb-6 animate-fadeIn" style="background-color: var(--color-modern-teal); color: var(--color-white);">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
          </div>
        @endif
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
          <!-- Total Visitors -->
          <div class="card bg-gradient-to-br from-blue-50 to-blue-100 shadow-lg hover:shadow-xl transition-all duration-300 animate-fadeIn" style="background-color: var(--color-white); border-color: var(--color-snow-mist); animation-delay: 0.1s">
            <div class="card-body">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-blue-800" style="color: var(--color-charcoal-ink);">Total Visitors</h3>
                  <p class="text-3xl font-bold text-blue-900" style="color: var(--color-regal-navy);">{{ $visitors->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-blue-200 text-blue-600" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%); color: var(--color-regal-navy);">
                  <i data-lucide="users" class="w-8 h-8"></i>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Active Visitors -->
          <div class="card bg-gradient-to-br from-green-50 to-green-100 shadow-lg hover:shadow-xl transition-all duration-300 animate-fadeIn" style="background-color: var(--color-white); border-color: var(--color-snow-mist); animation-delay: 0.2s">
            <div class="card-body">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-green-800" style="color: var(--color-charcoal-ink);">Currently In</h3>
                  <p class="text-3xl font-bold text-green-900" style="color: var(--color-modern-teal);">{{ $visitors->whereNull('time_out')->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-green-200 text-green-600" style="background-color: color-mix(in srgb, var(--color-modern-teal), white 80%); color: var(--color-modern-teal);">
                  <i data-lucide="user-check" class="w-8 h-8"></i>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Today's Visitors -->
          <div class="card bg-gradient-to-br from-purple-50 to-purple-100 shadow-lg hover:shadow-xl transition-all duration-300 animate-fadeIn" style="background-color: var(--color-white); border-color: var(--color-snow-mist); animation-delay: 0.3s">
            <div class="card-body">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-purple-800" style="color: var(--color-charcoal-ink);">Today's Visitors</h3>
                  <p class="text-3xl font-bold text-purple-900" style="color: var(--color-purple);">{{ $visitors->where('time_in', '>=', now()->startOfDay())->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-purple-200 text-purple-600" style="background-color: color-mix(in srgb, var(--color-purple), white 80%); color: var(--color-purple);">
                  <i data-lucide="calendar" class="w-8 h-8"></i>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Completed Visits -->
          <div class="card bg-gradient-to-br from-orange-50 to-orange-100 shadow-lg hover:shadow-xl transition-all duration-300 animate-fadeIn" style="background-color: var(--color-white); border-color: var(--color-snow-mist); animation-delay: 0.4s">
            <div class="card-body">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-orange-800" style="color: var(--color-charcoal-ink);">Completed</h3>
                  <p class="text-3xl font-bold text-orange-900" style="color: var(--color-golden-ember);">{{ $visitors->whereNotNull('time_out')->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-orange-200 text-orange-600" style="background-color: color-mix(in srgb, var(--color-golden-ember), white 80%); color: var(--color-golden-ember);">
                  <i data-lucide="check-circle" class="w-8 h-8"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-between items-center mt-8 mb-6">
          <div class="flex gap-3">
            <button onclick="openVisitorModal()" class="btn btn-primary hover:scale-105 transition-all" style="background-color: var(--color-regal-navy); color: var(--color-white); border-color: var(--color-regal-navy);">
              <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>Register New Visitor
            </button>
          </div>
          <div class="flex gap-2">
            <a href="{{ route('visitor.export.excel') }}" class="btn btn-success btn-sm hover:scale-105 transition-all" style="background-color: var(--color-modern-teal); color: var(--color-white); border-color: var(--color-modern-teal);">
              <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-1"></i>Export Excel
            </a>
            <a href="{{ route('visitor.export.pdf') }}" class="btn btn-error btn-sm hover:scale-105 transition-all" style="background-color: var(--color-danger-red); color: var(--color-white); border-color: var(--color-danger-red);">
              <i data-lucide="file-text" class="w-4 h-4 mr-1"></i>Export PDF
            </a>
          </div>
        </div>
        
        <!-- MODERN VISITOR INTERFACE -->
        <div class="mt-8">
          <!-- Navigation Tabs -->
          <div class="bg-gray-100 px-6 py-2 border-b border-gray-200" style="background-color: var(--color-snow-mist); border-color: var(--color-snow-mist);">
            <div class="flex space-x-1">
              <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-blue-100 rounded-t-lg border-b-2 border-blue-500" onclick="showTab('current')" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%); color: var(--color-charcoal-ink); border-color: var(--color-regal-navy);">
                Current Visitors
              </button>
              <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-t-lg" onclick="showTab('scheduled')" style="color: var(--color-charcoal-ink); hover:background-color: var(--color-snow-mist);">
                Scheduled Visits
              </button>
              <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-t-lg" onclick="showTab('checkin')" style="color: var(--color-charcoal-ink); hover:background-color: var(--color-snow-mist);">
                Check In/Out
              </button>
            </div>
          </div>

          <!-- Main Content -->
          <div class="bg-white rounded-lg border border-gray-200 mt-0" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
            <!-- Current Visitors Tab -->
            <div id="current-tab" class="h-96 flex">
              <!-- Left Panel - Current Visitors List -->
              <div class="w-2/3 p-6 overflow-y-auto">
                <div class="mb-6">
                  <h1 class="text-2xl font-bold text-gray-900 mb-1" style="color: var(--color-charcoal-ink);">Current Visitors</h1>
                  <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Visitors currently in the building</p>
                </div>
                
                <!-- Search Bar -->
                <div class="mb-6">
                  <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
                    <input type="text" placeholder="Search visitors..." class="input input-bordered w-full pl-10" id="visitorSearch" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);"/>
                  </div>
                </div>
                
                <!-- Visitor Cards -->
                <div class="space-y-4">
                  @forelse($visitors->whereNull('time_out') as $visitor) {{-- Only show currently checked-in visitors --}}
                    <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer visitor-card" 
                         data-visitor-id="{{ $visitor->id }}"
                         onclick="selectVisitor({{ $visitor->id }})" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
                      <div class="flex items-center justify-between">
                        <div class="flex-1">
                          <h3 class="font-semibold text-gray-900" style="color: var(--color-charcoal-ink);">{{ $visitor->name }}</h3>
                          <p class="text-sm text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $visitor->company ?? 'No Company' }}</p>
                          
                          <div class="flex items-center gap-4 mt-2">
                            @if($visitor->time_out)
                              <span class="badge badge-outline text-gray-500" style="border-color: var(--color-charcoal-ink); color: var(--color-charcoal-ink); opacity: 0.7;">Checked Out</span>
                            @else
                              <span class="badge badge-primary" style="background-color: var(--color-regal-navy); color: var(--color-white);">Checked In</span>
                            @endif
                            
                            <span class="badge badge-outline text-gray-600" style="border-color: var(--color-regal-navy); color: var(--color-regal-navy);">{{ $visitor->purpose }}</span>
                            
                            <div class="flex items-center gap-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                              <i data-lucide="clock" class="w-3 h-3"></i>
                              <span>In: {{ \Carbon\Carbon::parse($visitor->time_in)->format('h:i A') }}</span>
                              @if($visitor->time_out)
                                <span>Out: {{ \Carbon\Carbon::parse($visitor->time_out)->format('h:i A') }}</span>
                              @endif
                            </div>
                            
                            <div class="flex items-center gap-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                              <i data-lucide="building" class="w-3 h-3"></i>
                              <span>{{ $visitor->facility->name ?? 'No Location' }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @empty
                    <div class="text-center py-8" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                      <i data-lucide="users" class="w-12 h-12 text-gray-400 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
                      <p class="text-gray-500">No visitors currently in the building</p>
                    </div>
                  @endforelse
                </div>
              </div>
              
              <!-- Right Panel - Visitor Details -->
              <div class="w-1/3 bg-gray-50 border-l border-gray-200 p-6 overflow-y-auto" style="background-color: var(--color-snow-mist); border-color: var(--color-snow-mist);">
                <div id="visitor-details" class="hidden">
                  <h2 class="text-xl font-bold text-gray-900 mb-1" style="color: var(--color-charcoal-ink);">Visitor Details</h2>
                  <p class="text-gray-600 mb-6" style="color: var(--color-charcoal-ink); opacity: 0.8;">Information for <span id="selected-visitor-name">John Smith</span></p>
                  
                  <div class="bg-white rounded-lg border border-gray-200 p-4 space-y-4" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
                    <div>
                      <label class="text-sm font-medium text-gray-700" style="color: var(--color-charcoal-ink);">Visitor Name</label>
                      <p class="text-gray-900" id="detail-name" style="color: var(--color-charcoal-ink);"></p>
                    </div>
                    
                    <div>
                      <label class="text-sm font-medium text-gray-700" style="color: var(--color-charcoal-ink);">Company</label>
                      <p class="text-gray-900" id="detail-company" style="color: var(--color-charcoal-ink);"></p>
                    </div>
                    
                    <div>
                      <label class="text-sm font-medium text-gray-700" style="color: var(--color-charcoal-ink);">Purpose</label>
                      <p class="text-gray-900" id="detail-purpose" style="color: var(--color-charcoal-ink);"></p>
                    </div>
                    
                    <div>
                      <label class="text-sm font-medium text-gray-700" style="color: var(--color-charcoal-ink);">Host Employee</label>
                      <p class="text-gray-900" id="detail-host" style="color: var(--color-charcoal-ink);"></p>
                    </div>
                    
                    <div>
                      <label class="text-sm font-medium text-gray-700" style="color: var(--color-charcoal-ink);">Floor/Location</label>
                      <p class="text-gray-900" id="detail-location" style="color: var(--color-charcoal-ink);"></p>
                    </div>
                    
                    <div>
                      <label class="text-sm font-medium text-gray-700" style="color: var(--color-charcoal-ink);">Check-in Time</label>
                      <p class="text-gray-900" id="detail-checkin" style="color: var(--color-charcoal-ink);"></p>
                    </div>
                    
                    <div>
                      <label class="text-sm font-medium text-gray-700" style="color: var(--color-charcoal-ink);">Status</label>
                      <p class="text-gray-900" id="detail-status" style="color: var(--color-charcoal-ink);"></p>
                    </div>
                  </div>
                  
                  <div class="mt-6 space-y-3">
                    <button class="btn btn-primary w-full" style="background-color: var(--color-regal-navy); color: var(--color-white); border-color: var(--color-regal-navy);">
                      <i data-lucide="edit" class="w-4 h-4 mr-2"></i>Edit Visitor
                    </button>
                    <button class="btn btn-outline w-full" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">
                      <i data-lucide="log-out" class="w-4 h-4 mr-2"></i>Check Out
                    </button>
                  </div>
                  
                  <!-- Digital Pass Section -->
                  <div id="digital-pass-section" class="mt-6 space-y-2 hidden">
                    <h3 class="text-lg font-semibold text-gray-800" style="color: var(--color-charcoal-ink);">Digital Pass</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 90%); border-color: var(--color-regal-navy);">
                      <p class="text-sm text-gray-700" style="color: var(--color-charcoal-ink);">Pass ID: <span id="pass-id" class="font-medium"></span></p>
                      <p class="text-sm text-gray-700" style="color: var(--color-charcoal-ink);">Valid: <span id="pass-valid-from"></span> to <span id="pass-valid-until"></span></p>
                      <p class="text-sm text-gray-700" style="color: var(--color-charcoal-ink);">Facility: <span id="pass-facility"></span></p>
                      <p class="text-sm text-gray-700" style="color: var(--color-charcoal-ink);">Purpose: <span id="pass-purpose"></span></p>
                      <p class="text-sm text-gray-700" style="color: var(--color-charcoal-ink);">Access Level: <span id="pass-access-level"></span></p>
                    </div>
                  </div>
                </div>
                
                <div id="no-selection" class="text-center py-12" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                  <i data-lucide="user" class="w-16 h-16 text-gray-300 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
                  <p class="text-gray-500">Select a visitor to view details</p>
                </div>
              </div>
            </div>
            
            <!-- Scheduled Visits Tab -->
            <div id="scheduled-tab" class="h-96 p-6 overflow-y-auto hidden">
              <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-1" style="color: var(--color-charcoal-ink);">Scheduled Visits</h1>
                <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Upcoming visitor appointments</p>
              </div>
              
              <div class="space-y-4">
                <!-- Sample scheduled visits -->
                @forelse($visitors->whereNull('time_out')->where('time_in', '>=', now()->startOfDay())->where('time_in', '<=', now()->endOfDay()) as $visitor) {{-- Only show scheduled visitors for today --}}
                <div class="bg-white rounded-lg border border-gray-200 p-4" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <h3 class="font-semibold text-gray-900" style="color: var(--color-charcoal-ink);">{{ $visitor->name }}</h3>
                      <p class="text-sm text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $visitor->company ?? 'No Company' }}</p>
                      
                      <div class="flex items-center gap-4 mt-2">
                        <span class="badge badge-warning" style="background-color: var(--color-golden-ember); color: var(--color-white);">Scheduled</span>
                        <span class="badge badge-outline text-gray-600" style="border-color: var(--color-regal-navy); color: var(--color-regal-navy);">{{ $visitor->purpose ?? 'No Purpose' }}</span>
                        
                        <div class="flex items-center gap-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                          <i data-lucide="calendar" class="w-3 h-3"></i>
                          <span>{{ \Carbon\Carbon::parse($visitor->time_in)->format('Y-m-d') }}</span>
                        </div>
                        
                        <div class="flex items-center gap-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                          <i data-lucide="clock" class="w-3 h-3"></i>
                          <span>{{ \Carbon\Carbon::parse($visitor->time_in)->format('h:i A') }}</span>
                        </div>
                        
                        <span class="text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">Host: {{ $visitor->host_employee ?? 'N/A' }}</span>
                      </div>
                    </div>
                    <button class="btn btn-outline btn-sm" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">Pre-Register</button>
                  </div>
                </div>
                @empty
                <div class="text-center py-8" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                  <i data-lucide="calendar" class="w-12 h-12 text-gray-400 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
                  <p class="text-gray-500">No scheduled visits for today</p>
                </div>
                @endforelse
              </div>
            </div>
            
            <!-- Check In/Out Tab -->
            <div id="checkin-tab" class="h-96 p-6 overflow-y-auto hidden">
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left: Check-In Form -->
                <div>
                  <h2 class="text-xl font-bold text-gray-900 mb-1" style="color: var(--color-charcoal-ink);">Visitor Check-In</h2>
                  <p class="text-gray-600 mb-6" style="color: var(--color-charcoal-ink); opacity: 0.8;">Register a new visitor or check in scheduled visitor</p>
                  
                  <form id="checkin-form" class="bg-white rounded-lg border border-gray-200 p-6 space-y-4" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">Visitor Name</label>
                      <input type="text" name="name" placeholder="Enter visitor name" class="input input-bordered w-full" required style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);"/>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">Company</label>
                      <input type="text" name="company" placeholder="Enter company name" class="input input-bordered w-full" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);"/>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">Purpose of Visit</label>
                      <input type="text" name="purpose" placeholder="Meeting, consultation, etc." class="input input-bordered w-full" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);"/>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">Host Employee</label>
                      <input type="text" name="host_employee" placeholder="Enter host employee name" class="input input-bordered w-full" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);"/>
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">Facility/Location</label>
                      <select name="facility_id" class="select select-bordered w-full" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                        <option value="">Select facility</option>
                        @foreach($facilities as $facility)
                          <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    
                    <button type="button" onclick="checkInVisitor()" class="btn btn-primary w-full" style="background-color: var(--color-regal-navy); color: var(--color-white); border-color: var(--color-regal-navy);">
                      <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                      Check In Visitor
                    </button>
                  </form>
                </div>
                
                <!-- Right: Quick Actions -->
                <div>
                  <h2 class="text-xl font-bold text-gray-900 mb-1" style="color: var(--color-charcoal-ink);">Quick Actions</h2>
                  <p class="text-gray-600 mb-6" style="color: var(--color-charcoal-ink); opacity: 0.8;">Common visitor management tasks</p>
                  
                  <div class="space-y-3">
                    <div class="bg-white rounded-lg border border-gray-200 p-4 text-center hover:shadow-md transition-shadow cursor-pointer" onclick="viewAllVisitors()" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
                      <i data-lucide="users" class="w-6 h-6 text-gray-900 mx-auto mb-2" style="color: var(--color-regal-navy);"></i>
                      <p class="text-sm font-medium text-gray-900" style="color: var(--color-charcoal-ink);">View All Visitors</p>
                    </div>
                    
                    <div class="bg-white rounded-lg border border-gray-200 p-4 text-center hover:shadow-md transition-shadow cursor-pointer" onclick="scheduleVisit()" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
                      <i data-lucide="calendar" class="w-6 h-6 text-gray-900 mx-auto mb-2" style="color: var(--color-modern-teal);"></i>
                      <p class="text-sm font-medium text-gray-900" style="color: var(--color-charcoal-ink);">Schedule Visit</p>
                    </div>
                    
                    <div class="bg-white rounded-lg border border-gray-200 p-4 text-center hover:shadow-md transition-shadow cursor-pointer" onclick="emergencyEvacuation()" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
                      <i data-lucide="x-circle" class="w-6 h-6 text-gray-900 mx-auto mb-2" style="color: var(--color-danger-red);"></i>
                      <p class="text-sm font-medium text-gray-900" style="color: var(--color-charcoal-ink);">Emergency Evacuation</p>
                    </div>
                    
                    <div class="bg-white rounded-lg border border-gray-200 p-4 text-center hover:shadow-md transition-shadow cursor-pointer" onclick="buildingDirectory()" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
                      <i data-lucide="building" class="w-6 h-6 text-gray-900 mx-auto mb-2" style="color: var(--color-regal-navy);"></i>
                      <p class="text-sm font-medium text-gray-900" style="color: var(--color-charcoal-ink);">Building Directory</p>
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
  
  <style>
    /* ===== MODAL BASE STYLES ===== */
    .modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(8px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      overflow: hidden;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modal.modal-open {
      display: flex;
    }

    /* ===== MODAL BOX ===== */
    .modal-box {
      background: white;
      border-radius: 16px;
      box-shadow: 
        0 20px 25px -5px rgba(0, 0, 0, 0.1),
        0 10px 10px -5px rgba(0, 0, 0, 0.04),
        0 0 0 1px rgba(0, 0, 0, 0.05);
      width: 90vw;
      max-width: 800px;
      max-height: 90vh;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transform: scale(0.9) translateY(20px);
      opacity: 0;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }

    .modal.modal-open .modal-box {
      transform: scale(1) translateY(0);
      opacity: 1;
    }

    /* ===== MODAL HEADER ===== */
    .modal-header {
      padding: 24px 32px 20px;
      border-bottom: 1px solid #e5e7eb;
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      border-radius: 16px 16px 0 0;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .modal-title {
      font-size: 24px;
      font-weight: 700;
      color: #1e293b;
      display: flex;
      align-items: center;
      gap: 0;
      margin: 0;
      line-height: 1.2;
    }

    .modal-icon {
      width: 32px;
      height: 32px;
      color: #0f172a;
      display: inline-block;
      margin-right: 12px;
      flex-shrink: 0;
      opacity: 1;
      filter: none;
    }

    /* Ensure icon is visible and properly styled */
    .modal-title .modal-icon {
      color: #0f172a !important;
      background: rgba(15, 23, 42, 0.05);
      border: none;
      padding: 8px;
      margin: 0 12px 0 0;
      vertical-align: middle;
      transition: all 0.2s ease;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Icon hover effects */
    .modal-title:hover .modal-icon {
      color: #1e40af !important;
      transform: scale(1.05);
      background: rgba(30, 64, 175, 0.1);
      box-shadow: 0 2px 8px rgba(30, 64, 175, 0.2);
    }

    .modal-close-btn {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: none;
      background: white;
      color: #64748b;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .modal-close-btn:hover {
      background: #f1f5f9;
      color: #475569;
      transform: scale(1.05);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* ===== MODAL BODY ===== */
    .modal-body {
      padding: 32px;
      flex: 1;
      overflow-y: auto;
      background: white;
    }

    .visitor-registration-form {
      display: flex;
      flex-direction: column;
      gap: 0;
    }

    /* ===== FORM GRID LAYOUT ===== */
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 32px;
      margin-bottom: 32px;
    }

    .form-column {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .form-control.full-width {
      grid-column: 1 / -1;
      margin-top: 8px;
    }

    /* ===== FORM CONTROLS ===== */
    .form-control {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .form-label {
      font-size: 14px;
      font-weight: 600;
      color: #374151;
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 0;
    }

    .form-icon {
      width: 16px;
      height: 16px;
      color: #0f172a;
    }

    .form-input,
    .form-select,
    .form-textarea {
      padding: 12px 16px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      font-size: 14px;
      color: #1f2937;
      background: white;
      transition: all 0.2s ease;
      width: 100%;
      box-sizing: border-box;
    }

    .form-input:hover,
    .form-select:hover,
    .form-textarea:hover {
      border-color: #d1d5db;
      background: #fafafa;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
      outline: none;
      border-color: #0f172a;
      box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
      background: white;
    }

    .form-textarea {
      min-height: 120px;
      resize: vertical;
      font-family: inherit;
      line-height: 1.5;
    }

    /* ===== MODAL FOOTER ===== */
    .modal-footer {
      padding: 24px 32px;
      border-top: 1px solid #e5e7eb;
      background: #f8fafc;
      border-radius: 0 0 16px 16px;
    }

    .modal-actions {
      display: flex;
      justify-content: flex-end;
      gap: 16px;
    }

    /* ===== BUTTONS ===== */
    .btn {
      padding: 12px 24px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      min-height: 44px;
    }

    .btn-secondary {
      background: white;
      color: #6b7280;
      border: 2px solid #d1d5db;
    }

    .btn-secondary:hover {
      background: #f9fafb;
      color: #374151;
      border-color: #9ca3af;
      transform: translateY(-1px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
      background: #0f172a;
      color: white;
      border: 2px solid #0f172a;
    }

    .btn-primary:hover {
      background: #1e293b;
      border-color: #1e293b;
      transform: translateY(-1px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-icon {
      width: 16px;
      height: 16px;
    }

    /* ===== RESPONSIVE DESIGN ===== */
    @media (max-width: 768px) {
      .modal-box {
        width: 95vw;
        max-height: 95vh;
        margin: 16px;
      }

      .modal-header {
        padding: 20px 24px 16px;
      }

      .modal-body {
        padding: 24px;
      }

      .modal-footer {
        padding: 20px 24px;
      }

      .form-grid {
        grid-template-columns: 1fr;
        gap: 24px;
      }

      .modal-actions {
        flex-direction: column;
        width: 100%;
      }

      .btn {
        width: 100%;
        justify-content: center;
      }
    }

    @media (max-width: 480px) {
      .modal-box {
        width: 98vw;
        margin: 8px;
      }

      .modal-header {
        padding: 16px 20px 12px;
      }

      .modal-body {
        padding: 20px;
      }

      .modal-footer {
        padding: 16px 20px;
      }
    }

    /* ===== SIDEBAR AWARE POSITIONING ===== */
    @media (min-width: 1024px) {
      .modal {
        left: 256px; /* Account for sidebar width */
        width: calc(100vw - 256px);
      }
    }

    /* ===== BODY SCROLL PREVENTION ===== */
    body.modal-open {
      overflow: hidden;
      padding-right: 0;
    }

    /* ===== MODAL BACKDROP CLICK ===== */
    .modal {
      cursor: pointer;
    }

    .modal-box {
      cursor: default;
    }

    /* ===== LOADING ANIMATION ===== */
    .animate-spin {
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      from {
        transform: rotate(0deg);
      }
      to {
        transform: rotate(360deg);
      }
    }

    /* ===== ICON ENTRANCE ANIMATION ===== */
    .modal.modal-open .modal-icon {
      animation: iconSlideIn 0.4s ease-out 0.2s both;
    }

    @keyframes iconSlideIn {
      from {
        opacity: 0;
        transform: translateX(-20px) scale(0.8);
      }
      to {
        opacity: 1;
        transform: translateX(0) scale(1);
      }
    }
  </style>
  
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
        tab.style.backgroundColor = 'inherit';
        tab.style.color = 'var(--color-charcoal-ink)';
        tab.style.borderColor = 'transparent';
      });
      
      event.target.classList.remove('text-gray-500');
      event.target.classList.add('bg-blue-100', 'text-gray-700', 'border-blue-500');
      event.target.style.backgroundColor = 'color-mix(in srgb, var(--color-regal-navy), white 80%)';
      event.target.style.color = 'var(--color-charcoal-ink)';
      event.target.style.borderColor = 'var(--color-regal-navy)';

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
          updateVisitorCards(visitors.filter(v => !v.time_out)); // Filter for only currently checked-in
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
          <div class="text-center py-8" style="color: var(--color-charcoal-ink); opacity: 0.7;">
            <i data-lucide="users" class="w-12 h-12 text-gray-400 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
            <p class="text-gray-500">No visitors currently in the building</p>
          </div>
        `;
        return;
      }

      container.innerHTML = visitors.map(visitor => `
        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer visitor-card" 
             data-visitor-id="${visitor.id}"
             onclick="selectVisitor(${visitor.id})" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900" style="color: var(--color-charcoal-ink);">${visitor.name}</h3>
              <p class="text-sm text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">${visitor.company || 'No Company'}</p>
              
              <div class="flex items-center gap-4 mt-2">
                ${visitor.time_out ? 
                  '<span class="badge badge-outline text-gray-500" style="border-color: var(--color-charcoal-ink); color: var(--color-charcoal-ink); opacity: 0.7;">Checked Out</span>' : 
                  '<span class="badge badge-primary" style="background-color: var(--color-regal-navy); color: var(--color-white);">Checked In</span>'
                }
                
                <span class="badge badge-outline text-gray-600" style="border-color: var(--color-regal-navy); color: var(--color-regal-navy);">${visitor.purpose || 'No Purpose'}</span>
                
                <div class="flex items-center gap-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                  <i data-lucide="clock" class="w-3 h-3"></i>
                  <span>In: ${formatTime(visitor.time_in)}</span>
                  ${visitor.time_out ? `<span>Out: ${formatTime(visitor.time_out)}</span>` : ''}
                </div>
                
                <div class="flex items-center gap-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                  <i data-lucide="building" class="w-3 h-3"></i>
                  <span>${visitor.facility ? visitor.facility.name : 'No Location'}</span>
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
          <div class="text-center py-8" style="color: var(--color-charcoal-ink); opacity: 0.7;">
            <i data-lucide="calendar" class="w-12 h-12 text-gray-400 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
            <p class="text-gray-500">No scheduled visits for today</p>
          </div>
        `;
        return;
      }

      container.innerHTML = visitors.map(visitor => `
        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer visitor-card" 
             data-visitor-id="${visitor.id}"
             onclick="selectVisitor(${visitor.id})" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900" style="color: var(--color-charcoal-ink);">${visitor.name}</h3>
              <p class="text-sm text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">${visitor.company || 'No Company'}</p>
              
              <div class="flex items-center gap-4 mt-2">
                <span class="badge badge-warning" style="background-color: var(--color-golden-ember); color: var(--color-white);">Scheduled</span>
                <span class="badge badge-outline text-gray-600" style="border-color: var(--color-regal-navy); color: var(--color-regal-navy);">${visitor.purpose || 'No Purpose'}</span>
                
                <div class="flex items-center gap-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                  <i data-lucide="calendar" class="w-3 h-3"></i>
                  <span>${formatDate(visitor.time_in)}</span>
                </div>
                
                <div class="flex items-center gap-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                  <i data-lucide="clock" class="w-3 h-3"></i>
                  <span>${formatTime(visitor.time_in)}</span>
                </div>
                
                <span class="text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">Host: ${visitor.host_employee || 'N/A'}</span>
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
        card.style.borderColor = 'var(--color-snow-mist)';
      });
      
      // Add selection to clicked card
      event.currentTarget.classList.add('ring-2', 'ring-blue-500');
      event.currentTarget.style.borderColor = 'var(--color-regal-navy)';
      
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
      const editBtn = document.querySelector('#visitor-details .btn-primary');

      if (editBtn) {
          editBtn.onclick = () => window.location.href = `{{ route('visitor.edit', '') }}/${visitor.id}`;
      }

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

      // Display Digital Pass info if available
      const digitalPassSection = document.getElementById('digital-pass-section');
      if (visitor.digital_pass) {
          document.getElementById('pass-id').textContent = visitor.digital_pass.pass_id;
          document.getElementById('pass-valid-from').textContent = formatDate(visitor.digital_pass.valid_from);
          document.getElementById('pass-valid-until').textContent = formatDate(visitor.digital_pass.valid_until);
          document.getElementById('pass-facility').textContent = visitor.digital_pass.facility;
          document.getElementById('pass-purpose').textContent = visitor.digital_pass.purpose;
          document.getElementById('pass-access-level').textContent = visitor.digital_pass.access_level;
          digitalPassSection.classList.remove('hidden');
      } else {
          digitalPassSection.classList.add('hidden');
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
            // Clear details panel after checkout
            document.getElementById('visitor-details').classList.add('hidden');
            document.getElementById('no-selection').classList.remove('hidden');
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
      
      fetch('{{ route("visitor.store") }}', {
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

    function formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function showNotification(message, type = 'info') {
      // Create notification element
      const notification = document.createElement('div');
              notification.className = `alert alert-${type === 'error' ? 'error' : type === 'success' ? 'success' : 'info'} fixed bottom-4 right-4 z-50 max-w-sm`;
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

    // Logout function
    function logout() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = '{{ route("logout") }}';
      }
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      setupDarkMode();
      updateDateTime();
      setupSearch();
      
      // Update time every second
      setInterval(updateDateTime, 1000);
      
      // Load initial data
      loadCurrentVisitors();
      updateStats();
      
      // Initialize all Lucide icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    });

    // ===== VISITOR MODAL FUNCTIONS =====
    
    // Open visitor registration modal
    function openVisitorModal() {
      const modal = document.getElementById('visitorModal');
      modal.classList.add('modal-open');
      document.body.classList.add('modal-open');
      document.body.style.overflow = 'hidden';
      
      // Initialize Lucide icons in the modal
      setTimeout(() => {
        if (window.lucide && window.lucide.createIcons) {
          console.log('Initializing Lucide icons in modal...');
          window.lucide.createIcons();
        } else {
          console.log('Lucide not available:', window.lucide);
        }
      }, 50);
      
      // Focus first input for accessibility
      setTimeout(() => {
        const firstInput = modal.querySelector('input, select, textarea');
        if (firstInput) firstInput.focus();
      }, 100);
      
      // Add keyboard event listeners
      document.addEventListener('keydown', handleVisitorModalKeyboard);
    }

    // Close visitor registration modal
    function closeVisitorModal() {
      const modal = document.getElementById('visitorModal');
      modal.classList.remove('modal-open');
      document.body.classList.remove('modal-open');
      document.body.style.overflow = 'auto';
      
      // Reset form
      const form = document.getElementById('visitorRegistrationForm');
      if (form) form.reset();
      
      // Remove keyboard event listeners
      document.removeEventListener('keydown', handleVisitorModalKeyboard);
    }

    // Handle form submission
    document.getElementById('visitorRegistrationForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Add loading state
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i data-lucide="loader-2" class="btn-icon animate-spin"></i>Registering...';
      submitBtn.disabled = true;
      
      // Submit form data
      const formData = new FormData(this);
      
      fetch('{{ route("visitor.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message
          showNotification('Visitor registered successfully!', 'success');
          
          // Close modal
          closeVisitorModal();
          
          // Refresh visitor list
          loadCurrentVisitors();
          updateStats();
        } else {
          // Show error message
          showNotification(data.message || 'Error registering visitor', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('Error registering visitor', 'error');
      })
      .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      });
    });

    // Handle keyboard events for visitor modal
    function handleVisitorModalKeyboard(event) {
      if (event.key === 'Escape') {
        closeVisitorModal();
      }
      
      // Prevent tab from going outside modal
      if (event.key === 'Tab') {
        const modal = document.getElementById('visitorModal');
        const focusableElements = modal.querySelectorAll(
          'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        if (event.shiftKey) {
          if (document.activeElement === firstElement) {
            event.preventDefault();
            lastElement.focus();
          }
        } else {
          if (document.activeElement === lastElement) {
            event.preventDefault();
            firstElement.focus();
          }
        }
      }
    }
  </script>

  <!-- Visitor Registration Modal -->
  <div id="visitorModal" class="modal" onclick="closeVisitorModal()">
    <div class="modal-box" onclick="event.stopPropagation()">
      <!-- Modal Header -->
      <div class="modal-header">
        <h3 class="modal-title">
          <i data-lucide="user-plus" class="modal-icon"></i>
          Register New Visitor
        </h3>
        <button onclick="closeVisitorModal()" class="modal-close-btn" aria-label="Close modal">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form action="{{ route('visitor.store') }}" method="POST" enctype="multipart/form-data" id="visitorRegistrationForm" class="visitor-registration-form">
          @csrf
          
          <!-- Two Column Grid Layout -->
          <div class="form-grid">
            
            <!-- Left Column: Basic Information -->
            <div class="form-column">
              <!-- Visitor Name Field -->
              <div class="form-control">
                <label class="form-label">
                  <i data-lucide="user" class="form-icon"></i>
                  Full Name *
                </label>
                <input type="text" name="name" class="form-input" 
                       placeholder="Enter visitor's full name" required>
              </div>

              <!-- Company Field -->
              <div class="form-control">
                <label class="form-label">
                  <i data-lucide="building" class="form-icon"></i>
                  Company
                </label>
                <input type="text" name="company" class="form-input" 
                       placeholder="Enter company name">
              </div>

              <!-- Email Field -->
              <div class="form-control">
                <label class="form-label">
                  <i data-lucide="mail" class="form-icon"></i>
                  Email *
                </label>
                <input type="email" name="email" class="form-input" 
                       placeholder="Enter email address" required>
              </div>

              <!-- Phone Field -->
              <div class="form-control">
                <label class="form-label">
                  <i data-lucide="phone" class="form-icon"></i>
                  Phone Number *
                </label>
                <input type="tel" name="phone" class="form-input" 
                       placeholder="Enter phone number" required>
              </div>
            </div>

            <!-- Right Column: Visit Details -->
            <div class="form-column">
              <!-- Purpose Field -->
              <div class="form-control">
                <label class="form-label">
                  <i data-lucide="target" class="form-icon"></i>
                  Visit Purpose *
                </label>
                <select name="purpose" class="form-select" required>
                  <option value="">Select Purpose</option>
                  <option value="Meeting">Meeting</option>
                  <option value="Interview">Interview</option>
                  <option value="Delivery">Delivery</option>
                  <option value="Maintenance">Maintenance</option>
                  <option value="Consultation">Consultation</option>
                  <option value="Other">Other</option>
                </select>
              </div>

              <!-- Host Field -->
              <div class="form-control">
                <label class="form-label">
                  <i data-lucide="user-check" class="form-icon"></i>
                  Host/Contact Person *
                </label>
                <input type="text" name="host" class="form-input" 
                       placeholder="Enter host name" required>
              </div>

              <!-- Expected Duration Field -->
              <div class="form-control">
                <label class="form-label">
                  <i data-lucide="clock" class="form-icon"></i>
                  Expected Duration
                </label>
                <select name="expected_duration" class="form-select">
                  <option value="">Select Duration</option>
                  <option value="1 hour">1 hour</option>
                  <option value="2 hours">2 hours</option>
                  <option value="4 hours">4 hours</option>
                  <option value="Full day">Full day</option>
                  <option value="Multiple days">Multiple days</option>
                </select>
              </div>

              <!-- ID Number Field -->
              <div class="form-control">
                <label class="form-label">
                  <i data-lucide="credit-card" class="form-icon"></i>
                  ID Number
                </label>
                <input type="text" name="id_number" class="form-input" 
                       placeholder="Enter ID number">
              </div>
            </div>
          </div>

          <!-- Full Width: Additional Information -->
          <div class="form-control full-width">
            <label class="form-label">
              <i data-lucide="file-text" class="form-icon"></i>
              Additional Notes
            </label>
            <textarea name="notes" class="form-textarea" 
                      placeholder="Any additional information about the visit..."></textarea>
          </div>
        </form>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <div class="modal-actions">
          <button type="button" onclick="closeVisitorModal()" class="btn btn-secondary">
            Cancel
          </button>
          <button type="submit" form="visitorRegistrationForm" class="btn btn-primary">
            <i data-lucide="user-plus" class="btn-icon"></i>
            Register Visitor
          </button>
        </div>
      </div>
    </div>
  </div>
</body>
</html> 