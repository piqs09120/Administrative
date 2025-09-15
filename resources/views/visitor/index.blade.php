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
  <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
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
          <div class="alert alert-success mb-6 animate-fadeIn hidden" style="background-color: var(--color-modern-teal); color: var(--color-white);">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
          </div>
        @endif
        
        <!-- Alert Section -->
        @if($pendingExitVisitors->count() > 0 || $approachingTimeoutVisitors->count() > 0)
        <div class="mt-8 space-y-4">
          <!-- Pending Exit Alert -->
          @if($pendingExitVisitors->count() > 0)
          <div class="alert alert-error shadow-lg animate-pulse">
            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            <div>
              <h3 class="font-bold">⚠️ URGENT: {{ $pendingExitVisitors->count() }} Visitor(s) Overdue</h3>
              <div class="text-sm">These visitors have exceeded their expected checkout time and are marked as PENDING EXIT.</div>
            </div>
          </div>
          @endif
          
          <!-- Approaching Timeout Alert -->
          @if($approachingTimeoutVisitors->count() > 0)
          <div class="alert alert-warning shadow-lg">
            <i data-lucide="clock" class="w-6 h-6"></i>
            <div>
              <h3 class="font-bold">⏰ {{ $approachingTimeoutVisitors->count() }} Visitor(s) Approaching Timeout</h3>
              <div class="text-sm">These visitors are within 10 minutes of their expected checkout time.</div>
            </div>
          </div>
          @endif
        </div>
        @endif

        <!-- Stats Cards (DaisyUI, same as Visitor Logs) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
          <!-- Total Visitors -->
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-primary">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-primary text-primary-content rounded-full w-10 h-10">
                    <i data-lucide="users" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-primary badge-outline text-xs">All</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-primary justify-center mb-1">{{ $visitors->count() }}</h2>
                <p class="text-sm text-base-content/70">Total Visitors</p>
              </div>
            </div>
          </div>
          
          <!-- Active Visitors -->
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-success">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-success text-success-content rounded-full w-10 h-10">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-success badge-outline text-xs">Active</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-success justify-center mb-1">{{ $visitors->whereNull('time_out')->count() }}</h2>
                <p class="text-sm text-base-content/70">Currently In</p>
              </div>
            </div>
          </div>
          
          <!-- Today's Visitors -->
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-warning">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-warning text-warning-content rounded-full w-10 h-10">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-warning badge-outline text-xs">Today</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-warning justify-center mb-1">{{ $visitors->where('time_in', '>=', now()->startOfDay())->count() }}</h2>
                <p class="text-sm text-base-content/70">Today's Visitors</p>
              </div>
            </div>
          </div>
          
          <!-- Completed Visits -->
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-info">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-info text-info-content rounded-full w-10 h-10">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-info badge-outline text-xs">Done</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-info justify-center mb-1">{{ $visitors->whereNotNull('time_out')->count() }}</h2>
                <p class="text-sm text-base-content/70">Completed</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-end items-center mt-8 mb-6">
          <div class="flex gap-2">
            <a href="{{ route('visitor.management.landing') }}" class="btn btn-info btn-sm hover:scale-105 transition-all" style="background-color: var(--color-golden-ember); color: var(--color-white); border-color: var(--color-golden-ember);">
              <i data-lucide="home" class="w-4 h-4 mr-1"></i>Landing Page
            </a>
            <a href="{{ route('visitor.export.excel') }}" class="btn btn-success btn-sm hover:scale-105 transition-all" style="background-color: var(--color-modern-teal); color: var(--color-white); border-color: var(--color-modern-teal);">
              <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-1"></i>Export Excel
            </a>
          </div>
        </div>
        
        <!-- MODERN VISITOR INTERFACE -->
        <div class="mt-8">
          <!-- Clickable Breadcrumb Navigation -->
          <div class="mb-6">
            <nav class="flex items-center space-x-2 text-sm">
              <button id="nav-current" class="text-blue-600 hover:text-blue-800 font-medium flex items-center transition-colors duration-200 {{ $activeTab==='current' ? 'text-blue-800 font-semibold' : '' }}" onclick="showTab('current')">
                <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                Current Visitors
              </button>
              <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
              <button id="nav-scheduled" class="text-gray-600 hover:text-blue-600 font-medium flex items-center transition-colors duration-200 {{ $activeTab==='scheduled' ? 'text-blue-600 font-semibold' : '' }}" onclick="showTab('scheduled')">
                <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                Scheduled Visits
              </button>
              <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
              <button id="nav-monitoring" class="text-gray-600 hover:text-blue-600 font-medium flex items-center transition-colors duration-200 {{ $activeTab==='monitoring' ? 'text-blue-600 font-semibold' : '' }}" onclick="showTab('monitoring')">
                <i data-lucide="activity" class="w-4 h-4 inline mr-1"></i>
                Monitoring
              </button>
            </nav>
          </div>

          <!-- Main Content -->
          <div class="bg-white rounded-lg border border-gray-200 mt-0" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
            <!-- Current Visitors Tab -->
            <div id="current-tab" class="h-96 flex">
              <!-- Left Panel - Current Visitors List -->
              <div class="w-full p-6 overflow-y-auto">
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
                  @forelse($visitors->whereNotNull('time_in')->whereNull('time_out') as $visitor) {{-- Only show visitors actually checked in --}}
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
                            
                            @if($visitor->pass_id)
                              <span class="badge badge-info" style="background-color: var(--color-modern-teal); color: var(--color-white);">
                                <i data-lucide="id-card" class="w-3 h-3 mr-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $visitor->pass_type ?? 'Pass')) }}
                              </span>
                            @endif
                            
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
                          
                          @if($visitor->pass_id)
                            <div class="mt-2 text-xs text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.6;">
                              <i data-lucide="key" class="w-3 h-3 inline mr-1"></i>
                              Pass ID: {{ $visitor->pass_id }}
                              @if($visitor->pass_valid_until)
                                | Valid until: {{ \Carbon\Carbon::parse($visitor->pass_valid_until)->format('M d, h:i A') }}
                              @endif
                            </div>
                          @endif
                        </div>
                        <div class="flex items-center gap-2 ml-4">
                          @if($visitor->pass_id)
                            <button 
                              class="vm-view-pass-btn btn btn-sm btn-primary"
                              data-visitor-id="{{ $visitor->id }}"
                              data-visitor-name="{{ $visitor->name }}"
                              data-pass-url="{{ url('/visitor') }}/"
                              onclick="event.stopPropagation();"
                              style="background-color: var(--color-regal-navy); color: var(--color-white);">
                              <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View Pass
                            </button>
                          @endif
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
              
            </div>
            
            <!-- Scheduled Visits Tab -->
            <div id="scheduled-tab" class="h-96 p-6 overflow-y-auto hidden">
              <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-1" style="color: var(--color-charcoal-ink);">Scheduled Visits</h1>
                <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Upcoming visitor appointments</p>
              </div>
              
              <!-- Table Header for Scheduled Visit Cards -->
              <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4" style="background-color: var(--color-snow-mist); border-color: var(--color-snow-mist);">
                <div class="grid grid-cols-12 gap-4 text-sm font-medium text-gray-700" style="color: var(--color-charcoal-ink);">
                  <div class="col-span-4">Visitor Information</div>
                  <div class="col-span-2 text-center">Scheduled Time</div>
                  <div class="col-span-2 text-center">Purpose</div>
                  <div class="col-span-2 text-center">Status</div>
                  <div class="col-span-2 text-center">Actions</div>
                </div>
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
            
                    
            <!-- Monitoring Tab -->
            <div id="monitoring-tab" class="h-96 p-6 overflow-y-auto hidden">
              <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-1" style="color: var(--color-charcoal-ink);">Monitoring</h1>
                <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Visitors with passes and their current status</p>
              </div>

              <!-- Search Bar -->
              <div class="mb-6">
                <div class="relative">
                  <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
                  <input type="text" placeholder="Search visitors..." class="input input-bordered w-full pl-10" id="monitoringSearch" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);"/>
                </div>
              </div>

              <!-- Pending Exit Visitors Section -->
              @if($pendingExitVisitors->count() > 0)
              <div class="mb-6">
                <div class="alert alert-error shadow-lg">
                  <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                  <div>
                    <h3 class="font-bold">🚨 OVERDUE VISITORS ({{ $pendingExitVisitors->count() }})</h3>
                    <div class="text-sm">These visitors have exceeded their expected checkout time and require immediate attention.</div>
                  </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                  @foreach($pendingExitVisitors as $visitor)
                  <div class="card bg-error/10 border border-error/30 shadow-lg">
                    <div class="card-body p-4">
                      <div class="flex items-start justify-between mb-3">
                        <div class="avatar placeholder">
                          <div class="bg-error text-error-content rounded-full w-10 h-10">
                            <i data-lucide="user" class="w-5 h-5"></i>
                          </div>
                        </div>
                        <div class="badge badge-error badge-outline text-xs animate-pulse">OVERDUE</div>
                      </div>
                      
                      <h3 class="font-bold text-lg text-error mb-2">{{ $visitor->name }}</h3>
                      <p class="text-sm text-base-content/70 mb-2">{{ $visitor->company ?? 'No Company' }}</p>
                      
                      <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                          <span class="text-base-content/60">Expected Out:</span>
                          <span class="font-medium">{{ $visitor->expected_time_out }}</span>
                        </div>
                        <div class="flex justify-between">
                          <span class="text-base-content/60">Overdue Since:</span>
                          <span class="font-medium text-error">{{ $visitor->pending_exit_at ? $visitor->pending_exit_at->diffForHumans() : 'Unknown' }}</span>
                        </div>
                        <div class="flex justify-between">
                          <span class="text-base-content/60">Host:</span>
                          <span class="font-medium">{{ $visitor->host_employee ?? 'N/A' }}</span>
                        </div>
                      </div>
                      
                      <div class="card-actions justify-end mt-4">
                        <button class="btn btn-error btn-sm" onclick="checkOutVisitor({{ $visitor->id }})">
                          <i data-lucide="log-out" class="w-4 h-4"></i>
                          Check Out
                        </button>
                      </div>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
              @endif

              <!-- Approaching Timeout Visitors Section -->
              @if($approachingTimeoutVisitors->count() > 0)
              <div class="mb-6">
                <div class="alert alert-warning shadow-lg">
                  <i data-lucide="clock" class="w-6 h-6"></i>
                  <div>
                    <h3 class="font-bold">⏰ APPROACHING TIMEOUT ({{ $approachingTimeoutVisitors->count() }})</h3>
                    <div class="text-sm">These visitors are within 10 minutes of their expected checkout time.</div>
                  </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                  @foreach($approachingTimeoutVisitors as $visitor)
                  <div class="card bg-warning/10 border border-warning/30 shadow-lg">
                    <div class="card-body p-4">
                      <div class="flex items-start justify-between mb-3">
                        <div class="avatar placeholder">
                          <div class="bg-warning text-warning-content rounded-full w-10 h-10">
                            <i data-lucide="user" class="w-5 h-5"></i>
                          </div>
                        </div>
                        <div class="badge badge-warning badge-outline text-xs">APPROACHING</div>
                      </div>
                      
                      <h3 class="font-bold text-lg text-warning mb-2">{{ $visitor->name }}</h3>
                      <p class="text-sm text-base-content/70 mb-2">{{ $visitor->company ?? 'No Company' }}</p>
                      
                      <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                          <span class="text-base-content/60">Expected Out:</span>
                          <span class="font-medium">{{ $visitor->expected_time_out }}</span>
                        </div>
                        <div class="flex justify-between">
                          <span class="text-base-content/60">Time Remaining:</span>
                          <span class="font-medium text-warning">
                            @php
                              $now = \Carbon\Carbon::now();
                              $expectedCheckout = \Carbon\Carbon::parse($visitor->expected_time_out);
                              $minutesRemaining = $now->diffInMinutes($expectedCheckout, false);
                            @endphp
                            {{ $minutesRemaining > 0 ? $minutesRemaining . ' minutes' : 'Overdue' }}
                          </span>
                        </div>
                        <div class="flex justify-between">
                          <span class="text-base-content/60">Host:</span>
                          <span class="font-medium">{{ $visitor->host_employee ?? 'N/A' }}</span>
                        </div>
                      </div>
                      
                      <div class="card-actions justify-end mt-4">
                        <button class="btn btn-warning btn-sm" onclick="checkOutVisitor({{ $visitor->id }})">
                          <i data-lucide="log-out" class="w-4 h-4"></i>
                          Check Out
                        </button>
                      </div>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
              @endif

              <!-- Visitor Cards Grid -->
              <div id="monitoring-visitors-cards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Loading state -->
                <div class="col-span-full text-center py-8" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                            <i data-lucide="users" class="w-12 h-12 text-gray-400 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
                            <p class="text-gray-500">Loading visitors...</p>
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
      z-index: 10000;
      overflow: hidden;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modal.modal-open {
      display: flex !important;
      visibility: visible !important;
      opacity: 1 !important;
    }

    /* ===== MODAL BOX ===== */
    .modal-box {
      background: white;
      border-radius: 16px;
      box-shadow: 
        0 20px 25px -5px rgba(0, 0, 0, 0.1),
        0 10px 10px -5px rgba(0, 0, 0, 0.04),
        0 0 0 1px rgba(0, 0, 0, 0.05);
      width: 95vw;
      max-width: 1000px;
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

    .form-grid-three {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 24px;
      margin-bottom: 32px;
    }

    .form-column {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .form-section {
      display: flex;
      flex-direction: column;
      gap: 16px;
      padding: 20px;
      background: #f8fafc;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
    }

    .section-header {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 8px;
      padding-bottom: 12px;
      border-bottom: 2px solid #e2e8f0;
    }

    .section-icon {
      width: 20px;
      height: 20px;
      color: #3b82f6;
    }

    .section-title {
      font-size: 16px;
      font-weight: 600;
      color: #374151;
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

    /* ===== PASS ISSUANCE STYLES ===== */
    .form-section-divider {
      margin: 32px 0 24px;
      position: relative;
      text-align: center;
    }

    .divider-content {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      background: white;
      padding: 0 24px;
      color: #64748b;
      font-weight: 600;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .divider-content::before,
    .divider-content::after {
      content: '';
      position: absolute;
      top: 50%;
      width: 100px;
      height: 1px;
      background: linear-gradient(to right, transparent, #e2e8f0, transparent);
    }

    .divider-content::before {
      right: 100%;
    }

    .divider-content::after {
      left: 100%;
    }

    .divider-icon {
      width: 20px;
      height: 20px;
      color: #3b82f6;
    }

    .form-grid-small {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .checkbox-label {
      display: flex;
      align-items: center;
      gap: 12px;
      cursor: pointer;
      font-weight: 500;
    }

    .form-checkbox {
      width: 18px;
      height: 18px;
      accent-color: #3b82f6;
    }

    .form-help-text {
      font-size: 12px;
      color: #64748b;
      margin-top: 4px;
      margin-left: 30px;
    }

    .hidden {
      display: none !important;
    }

    /* View Pass Button Styles */
    .vm-view-pass-btn {
      transition: all 0.2s ease-in-out;
    }

    .vm-view-pass-btn:hover:not(:disabled) {
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
    }

    .vm-view-pass-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    /* Loading spinner animation */
    @keyframes spin {
      from {
        transform: rotate(0deg);
      }
      to {
        transform: rotate(360deg);
      }
    }

    .animate-spin {
      animation: spin 1s linear infinite;
    }

    /* ===== MONITORING CARDS STYLES ===== */
    .monitoring-visitor-card {
      transition: all 0.3s ease;
      border: 2px solid #e5e7eb;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    }

    .monitoring-visitor-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      border-color: #3b82f6;
    }

    .monitoring-visitor-card .badge {
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
    }

    .monitoring-visitor-card .btn {
      font-size: 0.75rem;
      padding: 0.5rem 0.75rem;
      border-radius: 0.375rem;
      transition: all 0.2s ease;
      font-weight: 600;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .monitoring-visitor-card .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Status badge colors */
    .badge-success { background-color: #22c55e !important; }
    .badge-warning { background-color: #f59e0b !important; }
    .badge-info { background-color: #3b82f6 !important; }
    .badge-neutral { background-color: #6b7280 !important; }
    .badge-error { background-color: #ef4444 !important; }
    .badge-ghost { background-color: #9ca3af !important; }

    /* ===== SUCCESS MODAL STYLES ===== */
    .success-modal {
      max-width: 1000px;
    }

    .success-modal .modal-box {
      max-width: 1000px;
      width: 95vw;
    }

    #visitorSuccessModal {
      z-index: 10001;
      display: none;
      visibility: hidden;
      opacity: 0;
    }

    #visitorSuccessModal.modal-open {
      display: flex !important;
      visibility: visible !important;
      opacity: 1 !important;
    }

    .success-message {
      text-align: center;
      padding: 24px 32px;
      background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
      border: 2px solid #22c55e;
      border-radius: 12px;
      margin-bottom: 24px;
    }

    .success-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 48px;
      height: 48px;
      background: #22c55e;
      color: white;
      border-radius: 50%;
      margin-bottom: 16px;
    }

    .success-title {
      font-size: 20px;
      font-weight: 700;
      color: #16a34a;
      margin: 0;
    }

    .success-body {
      padding: 0 32px 32px;
    }

    .success-content {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .pass-info-section {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 24px;
    }

    .pass-info-title {
      font-size: 18px;
      font-weight: 600;
      color: #374151;
      margin: 0 0 20px 0;
      padding-bottom: 12px;
      border-bottom: 2px solid #e2e8f0;
    }

    .pass-details {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .pass-detail-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
    }

    .pass-detail-label {
      font-weight: 600;
      color: #6b7280;
      font-size: 14px;
    }

    .pass-detail-value {
      font-weight: 500;
      color: #374151;
      font-size: 14px;
    }

    .status-active {
      color: #22c55e !important;
      font-weight: 600;
    }

    /* ===== PASS INFO SECTION ===== */
    .pass-info-section {
      text-align: center;
    }

    .pass-info-title {
      font-size: 1.125rem;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 1rem;
    }

    .pass-info-container {
      background: white;
      border: 2px solid #e5e7eb;
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1rem;
      display: flex;
      gap: 2rem;
      justify-content: center;
    }

    .pass-info-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .pass-info-label {
      font-size: 0.75rem;
      color: #6b7280;
      margin: 0;
    }

    .pass-info-value {
      font-size: 0.875rem;
      font-weight: 600;
      color: #1f2937;
      margin: 0;
    }

    .success-actions {
      justify-content: center;
      gap: 16px;
    }

    @media (max-width: 768px) {
      .form-grid-three {
        grid-template-columns: 1fr;
        gap: 16px;
      }
      
      .form-grid-small {
        grid-template-columns: 1fr;
      }
      
      .divider-content::before,
      .divider-content::after {
        width: 50px;
      }

      .success-actions {
        flex-direction: column;
      }

      .success-actions .btn {
        width: 100%;
      }
    }
    
    /* Modal improvements */
    #visitorDetailsModal .modal-box {
      max-height: 90vh;
      overflow-y: auto;
    }
    
    #visitorDetailsModal .modal-box::-webkit-scrollbar {
      width: 6px;
    }
    
    #visitorDetailsModal .modal-box::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }
    
    #visitorDetailsModal .modal-box::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }
    
    #visitorDetailsModal .modal-box::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }
    
    /* Ensure cards fit properly */
    .visitor-detail-card {
      min-height: fit-content;
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
      document.getElementById('monitoring-tab').classList.add('hidden');
      
      // Show selected tab
      document.getElementById(tabName + '-tab').classList.remove('hidden');
      
      // Reset all navigation buttons
      const nav1 = document.getElementById('nav-current');
      const nav2 = document.getElementById('nav-scheduled');
      const nav3 = document.getElementById('nav-monitoring');
      
      [nav1, nav2, nav3].forEach(btn => {
        if (btn) {
          btn.classList.remove('text-blue-600', 'text-blue-800', 'font-semibold');
          btn.classList.add('text-gray-600');
        }
      });
      
      // Update active navigation button
      if (tabName === 'current' && nav1) {
        nav1.classList.remove('text-gray-600');
        nav1.classList.add('text-blue-800', 'font-semibold');
        // Reflect in URL
        try {
          const url = new URL(window.location.href);
          url.searchParams.delete('tab');
          window.history.replaceState({}, '', url);
        } catch(e) {}
      } else if (tabName === 'scheduled' && nav2) {
        nav2.classList.remove('text-gray-600');
        nav2.classList.add('text-blue-600', 'font-semibold');
        // Reflect in URL
        try {
          const url = new URL(window.location.href);
          url.searchParams.set('tab', 'scheduled');
          window.history.replaceState({}, '', url);
        } catch(e) {}
      } else if (tabName === 'monitoring' && nav3) {
        nav3.classList.remove('text-gray-600');
        nav3.classList.add('text-blue-600', 'font-semibold');
        // Reflect in URL
        try {
          const url = new URL(window.location.href);
          url.searchParams.set('tab', 'monitoring');
          window.history.replaceState({}, '', url);
        } catch(e) {}
      }

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
        case 'monitoring':
          loadMonitoringData();
          break;
      }
    }

    // Load current visitors - DISABLED to prevent 404 errors
    function loadCurrentVisitors() {
      // No AJAX call - data is already loaded server-side
      console.log('Current visitors data is already loaded server-side');
    }

    // Load scheduled visits - DISABLED to prevent 404 errors
    function loadScheduledVisits() {
      // No AJAX call - data is already loaded server-side
      console.log('Scheduled visits data is already loaded server-side');
    }




    // Load monitoring data
    function loadMonitoringData() {
      // Load monitoring stats
      fetch('{{ route("visitor.monitoring.stats") }}')
        .then(response => response.json())
        .then(stats => {
          updateMonitoringStats(stats);
        })
        .catch(error => {
          console.error('Error loading monitoring stats:', error);
        });

      // Load monitoring visitors table
      fetch('{{ route("visitor.monitoring.visitors") }}')
        .then(response => response.json())
        .then(data => {
          console.log('Monitoring visitors response:', data);
          if (data.success) {
            console.log('Visitors loaded:', data.data);
            updateMonitoringVisitorsCards(data.data);
          } else {
            console.error('Error loading monitoring visitors:', data.message);
          }
        })
        .catch(error => {
          console.error('Error loading monitoring visitors:', error);
        });
    }

    // Update monitoring statistics
    function updateMonitoringStats(stats) {
      document.getElementById('monitoring-today-registrations').textContent = stats.today_registrations || 0;
      document.getElementById('monitoring-today-checkins').textContent = stats.today_checkins || 0;
      document.getElementById('monitoring-today-checkouts').textContent = stats.today_checkouts || 0;
      
      const mostActiveUser = stats.most_active_user;
      if (mostActiveUser && mostActiveUser.user) {
        document.getElementById('monitoring-most-active-user').textContent = 
          `${mostActiveUser.user.name} (${mostActiveUser.activity_count} activities)`;
      } else {
        document.getElementById('monitoring-most-active-user').textContent = 'No data';
      }
    }

    // Update activity log
    function updateActivityLog(activities) {
      const container = document.getElementById('monitoring-activity-log');
      if (!container) return;

      if (activities.length === 0) {
        container.innerHTML = `
          <div class="text-center py-8" style="color: var(--color-charcoal-ink); opacity: 0.7;">
            <i data-lucide="activity" class="w-12 h-12 text-gray-400 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
            <p class="text-gray-500">No activity logs found</p>
          </div>
        `;
        return;
      }

      container.innerHTML = activities.map(activity => `
        <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200" style="background-color: var(--color-snow-mist); border-color: var(--color-snow-mist);">
          <div class="flex-shrink-0 mr-4">
            <i data-lucide="${activity.action_icon || 'activity'}" class="w-5 h-5 ${activity.action_color || 'text-gray-600'}" style="color: var(--color-charcoal-ink);"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-gray-900" style="color: var(--color-charcoal-ink);">
                ${activity.checked_in_by ? activity.checked_in_by.name : 'System'} ${activity.action === 'register' ? 'registered' : activity.action === 'checkin' ? 'checked in' : 'checked out'} visitor
              </p>
              <p class="text-xs text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                ${new Date(activity.action_time).toLocaleString()}
              </p>
            </div>
            <p class="text-sm text-gray-600 truncate" style="color: var(--color-charcoal-ink); opacity: 0.8;">
              ${activity.visitor ? activity.visitor.name : 'Unknown Visitor'} ${activity.visitor && activity.visitor.company ? `(${activity.visitor.company})` : ''}
            </p>
            ${activity.notes ? `<p class="text-xs text-gray-500 mt-1" style="color: var(--color-charcoal-ink); opacity: 0.7;">${activity.notes}</p>` : ''}
          </div>
        </div>
      `).join('');

      // Re-initialize Lucide icons
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    }

    // Update monitoring visitors cards
    function updateMonitoringVisitorsCards(visitors) {
      console.log('Updating monitoring visitors cards with:', visitors);
      const container = document.getElementById('monitoring-visitors-cards');
      if (!container) return;

      if (visitors.length === 0) {
        container.innerHTML = `
          <div class="col-span-full text-center py-8" style="color: var(--color-charcoal-ink); opacity: 0.7;">
              <i data-lucide="users" class="w-12 h-12 text-gray-400 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
              <p class="text-gray-500">No visitors with passes found</p>
          </div>
        `;
        return;
      }

      container.innerHTML = visitors.map(visitor => {
        // Use the status from backend (it's already properly determined)
        let status = (visitor.status || 'Pending').toLowerCase();
        
        // Debug logging
        console.log('Visitor:', visitor.name, 'Status:', status, 'Original status:', visitor.status);
        console.log('Check in time:', visitor.check_in_time, 'Expected time out:', visitor.expected_time_out);
        console.log('Actual check in time:', visitor.actual_check_in_time);
        console.log('Actual check out time:', visitor.actual_check_out_time);
        console.log('Raw time_in:', visitor.time_in, 'Raw time_out:', visitor.time_out);
        console.log('Current time:', new Date().toLocaleString());
        console.log('Pending exit:', visitor.pending_exit);
        
        const statusConfig = getStatusConfig(status);
        // Calculate duration from actual check-in to checkout time or now
        // For completed visitors, use time_out; for active visitors, use null (current time)
        const duration = calculateDuration(visitor.time_in, visitor.time_out);
        console.log('Calculated duration:', duration);
        
        // Add real-time duration update for active visitors
        let durationElement = null;
        if (!visitor.time_out && visitor.time_in) {
          durationElement = `data-duration-start="${visitor.time_in}"`;
        }
        
        // Additional debug for duration calculation
        if (visitor.time_in) {
          const start = new Date(visitor.time_in);
          const end = visitor.time_out ? new Date(visitor.time_out) : new Date();
          const diffMs = end - start;
          console.log('Start time:', start.toLocaleString());
          console.log('End time:', end.toLocaleString());
          console.log('Difference in ms:', diffMs);
          console.log('Difference in minutes:', Math.floor(diffMs / (1000 * 60)));
        }
        
        return `
          <div class="monitoring-visitor-card bg-white rounded-lg border-2 border-gray-100 p-4 hover:shadow-2xl hover:border-blue-200 transition-all duration-300 shadow-lg" 
               style="background-color: var(--color-white); border-color: #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
            
            <!-- Card Header with Status Badge -->
            <div class="flex justify-between items-start mb-3">
              <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 mb-1" style="color: var(--color-charcoal-ink);">${visitor.name}</h3>
                <p class="text-sm text-gray-600 font-medium" style="color: var(--color-charcoal-ink); opacity: 0.8;">${visitor.company || 'No Company'}</p>
              </div>
              <span class="badge ${statusConfig.badgeClass} text-xs font-semibold px-2 py-1" style="background-color: ${statusConfig.color}; color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" data-field="status">
                ${statusConfig.label}
              </span>
            </div>

            <!-- Visitor ID -->
            <div class="mb-3 p-2 bg-gray-50 rounded-md" style="background-color: #f8fafc;">
              <div class="flex items-center gap-2 text-xs text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">
                <i data-lucide="id-card" class="w-3 h-3 text-blue-500"></i>
                <span class="font-semibold">ID:</span>
                <span class="font-mono text-blue-600 font-bold">${visitor.pass_id || 'N/A'}</span>
              </div>
            </div>

            <!-- Contact Information -->
            <div class="mb-3 space-y-2">
              <div class="flex items-center gap-2 text-xs text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">
                <i data-lucide="mail" class="w-3 h-3 text-green-500"></i>
                <span class="font-medium">${visitor.email || 'N/A'}</span>
              </div>
              <div class="flex items-center gap-2 text-xs text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">
                <i data-lucide="phone" class="w-3 h-3 text-purple-500"></i>
                <span class="font-medium">${visitor.contact || 'N/A'}</span>
              </div>
              <div class="flex items-center gap-2 text-xs text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">
                <i data-lucide="building" class="w-3 h-3 text-orange-500"></i>
                <span class="font-medium">${visitor.department || 'N/A'}</span>
              </div>
            </div>

            <!-- Visit Details -->
            <div class="mb-3 p-2 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-md border border-blue-100" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-color: #bfdbfe;">
              <div class="text-xs space-y-1">
                <div class="flex items-center gap-2">
                  <i data-lucide="target" class="w-3 h-3 text-blue-600"></i>
                  <span class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Purpose:</span>
                  <span class="text-gray-600 font-medium" style="color: var(--color-charcoal-ink); opacity: 0.8;">${visitor.purpose || 'N/A'}</span>
                </div>
                <div class="flex items-center gap-2">
                  <i data-lucide="user" class="w-3 h-3 text-green-600"></i>
                  <span class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Host:</span>
                  <span class="text-gray-600 font-medium" style="color: var(--color-charcoal-ink); opacity: 0.8;">${visitor.host_employee || 'N/A'}</span>
                </div>
                ${visitor.vehicle_plate ? `
                <div class="flex items-center gap-2">
                  <i data-lucide="car" class="w-3 h-3 text-purple-600"></i>
                  <span class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Vehicle:</span>
                  <span class="text-gray-600 font-medium" style="color: var(--color-charcoal-ink); opacity: 0.8;">${visitor.vehicle_plate}</span>
                </div>
                ` : ''}
              </div>
            </div>

            <!-- Time Information -->
            <div class="mb-3 p-2 bg-gray-50 rounded-md border border-gray-200" style="background-color: #f9fafb; border-color: #e5e7eb;">
              <div class="space-y-1">
                <div class="flex justify-between items-center text-xs">
                  <span class="text-gray-600 font-medium" style="color: var(--color-charcoal-ink); opacity: 0.8;">Expected In:</span>
                  <span class="font-semibold text-blue-600" style="color: #2563eb;">${visitor.check_in_time || 'N/A'}</span>
                </div>
                ${visitor.expected_date_out ? `
                <div class="flex justify-between items-center text-xs">
                  <span class="text-gray-600 font-medium" style="color: var(--color-charcoal-ink); opacity: 0.8;">Expected Date Out:</span>
                  <span class="font-semibold text-orange-600" style="color: #ea580c;">${visitor.expected_date_out}</span>
                </div>
                ` : ''}
                ${visitor.expected_time_out ? `
                <div class="flex justify-between items-center text-xs">
                  <span class="text-gray-600 font-medium" style="color: var(--color-charcoal-ink); opacity: 0.8;">Expected Time Out:</span>
                  <span class="font-semibold text-orange-600" style="color: #ea580c;" data-field="expected-time-out">${visitor.expected_time_out}</span>
                </div>
                ` : ''}
                ${visitor.actual_check_in_time && visitor.actual_check_in_time !== 'N/A' ? `
                <div class="flex justify-between items-center text-xs">
                  <span class="text-gray-600 font-medium" style="color: var(--color-charcoal-ink); opacity: 0.8;">Actual In:</span>
                  <span class="font-semibold text-green-600" style="color: #16a34a;" data-field="actual-check-in">${visitor.actual_check_in_time}</span>
                </div>
                ` : ''}
                ${visitor.actual_check_out_time && visitor.actual_check_out_time !== 'N/A' ? `
                <div class="flex justify-between items-center text-xs">
                  <span class="text-gray-600 font-medium" style="color: var(--color-charcoal-ink); opacity: 0.8;">Actual Out:</span>
                  <span class="font-semibold text-red-600" style="color: #dc2626;" data-field="actual-check-out">${visitor.actual_check_out_time}</span>
                </div>
                ` : ''}
                ${duration ? `
                <div class="flex justify-between items-center text-xs">
                  <span class="text-gray-600 font-medium" style="color: var(--color-charcoal-ink); opacity: 0.8;">Duration:</span>
                  <span class="font-bold text-purple-600" style="color: #9333ea;" data-field="duration">${duration}</span>
                </div>
                ` : ''}
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2 pt-3 border-t border-gray-200" style="border-color: #e5e7eb;">
              ${status === 'active' ? `
              <!-- ACTIVE visitors: Check Out and Details -->
              <button 
                onclick="checkOutVisitor(${visitor.id})" 
                class="btn btn-sm btn-outline btn-error flex-1"
                style="color: var(--color-danger-red); border-color: var(--color-danger-red);">
                <i data-lucide="log-out" class="w-4 h-4 mr-1"></i>Check Out
              </button>
              <button 
                onclick="viewVisitorDetails(${visitor.id})" 
                class="btn btn-sm btn-outline"
                style="color: var(--color-charcoal-ink); border-color: var(--color-snow-mist);">
                <i data-lucide="info" class="w-4 h-4 mr-1"></i>Details
              </button>
              ` : status === 'pending' ? `
              <!-- PENDING visitors: Details only -->
              <button 
                onclick="viewVisitorDetails(${visitor.id})" 
                class="btn btn-sm btn-outline w-full"
                style="color: var(--color-charcoal-ink); border-color: var(--color-snow-mist);">
                <i data-lucide="info" class="w-4 h-4 mr-1"></i>Details
              </button>
              ` : status === 'completed' ? `
              <!-- COMPLETED visitors: View Pass and Details -->
              ${visitor.pass_id ? `
              <button 
                class="vm-view-pass-btn btn btn-sm btn-primary flex-1"
                data-visitor-id="${visitor.id}"
                data-visitor-name="${visitor.name}"
                data-pass-url="{{ url('/visitor') }}/"
                onclick="event.stopPropagation();"
                style="background-color: var(--color-regal-navy); color: var(--color-white);">
                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
              </button>
              ` : ''}
              <button 
                onclick="viewVisitorDetails(${visitor.id})" 
                class="btn btn-sm btn-outline"
                style="color: var(--color-charcoal-ink); border-color: var(--color-snow-mist);">
                <i data-lucide="info" class="w-4 h-4 mr-1"></i>Details
              </button>
              ` : `
              <!-- OTHER statuses: Details only -->
              <button 
                onclick="viewVisitorDetails(${visitor.id})" 
                class="btn btn-sm btn-outline w-full"
                style="color: var(--color-charcoal-ink); border-color: var(--color-snow-mist);">
                <i data-lucide="info" class="w-4 h-4 mr-1"></i>Details
              </button>
              `}
            </div>
          </div>
        `;
      }).join('');

      // Re-initialize Lucide icons
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }

      // Add event listeners for View Pass buttons
      addViewPassEventListeners();
      
      // Start real-time duration updates
      startDurationUpdates();
      
      // Also update durations immediately
      updateAllDurations();
    }

    // Start real-time duration updates
    function startDurationUpdates() {
      // Update durations every 30 seconds for real-time display
      setInterval(() => {
        updateAllDurations();
      }, 30000); // 30 seconds
    }

    // Update all duration displays
    function updateAllDurations() {
      const cards = document.querySelectorAll('.monitoring-visitor-card');
      console.log('Updating durations for', cards.length, 'cards');
      
      cards.forEach(card => {
        const actualCheckInEl = card.querySelector('[data-field="actual-check-in"]');
        const actualCheckOutEl = card.querySelector('[data-field="actual-check-out"]');
        const durationEl = card.querySelector('[data-field="duration"]');
        const statusEl = card.querySelector('[data-field="status"]');
        
        if (actualCheckInEl && durationEl) {
          const actualCheckIn = actualCheckInEl.textContent;
          const actualCheckOut = actualCheckOutEl ? actualCheckOutEl.textContent : null;
          const status = statusEl ? statusEl.textContent : '';
          
          console.log('Found check-in time:', actualCheckIn);
          console.log('Found check-out time:', actualCheckOut);
          console.log('Status:', status);
          
          if (actualCheckIn && actualCheckIn !== 'N/A') {
            // Only update duration for active visitors (not completed)
            if (status !== 'Completed' && !actualCheckOut) {
              // Calculate duration from actual check-in to now (for active visitors)
              const duration = calculateDuration(actualCheckIn, null);
              console.log('Calculated duration for active visitor:', duration);
              if (duration) {
                durationEl.textContent = duration;
              }
            }
            // For completed visitors, duration should already be calculated and static
            // No need to update it
          }
        }
      });
    }

    // Add event listeners for View Pass buttons
    function addViewPassEventListeners() {
      console.log('Adding event listeners for View Pass buttons...');
      const viewPassButtons = document.querySelectorAll('.vm-view-pass-btn');
      console.log('Found', viewPassButtons.length, 'View Pass buttons');
      
      viewPassButtons.forEach((button, index) => {
        console.log(`Button ${index}:`, button);
        button.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          
          console.log('View Pass button clicked!');
          const visitorId = this.getAttribute('data-visitor-id');
          const visitorName = this.getAttribute('data-visitor-name');
          const passUrl = this.getAttribute('data-pass-url');
          
          console.log('Button data:', { visitorId, visitorName, passUrl });
          
          viewVisitorPass(visitorId, visitorName, this, passUrl);
        });
      });
    }

    // View visitor pass
    function viewVisitorPass(visitorId, visitorName, button, passUrl = null) {
      console.log('=== VIEW VISITOR PASS FUNCTION CALLED ===');
      console.log('Visitor ID:', visitorId);
      console.log('Visitor Name:', visitorName);
      console.log('Button:', button);
      console.log('Pass URL:', passUrl);
      
      // Disable button and show loading state
      const originalText = button.innerHTML;
      button.disabled = true;
      button.innerHTML = '<i data-lucide="loader-2" class="w-3 h-3 mr-1 animate-spin"></i>Loading...';
      
      // Re-initialize Lucide icons for the spinner
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }

      // Use provided URL or generate one
      const url = passUrl ? `${passUrl}${visitorId}/pass` : `{{ url('/visitor') }}/${visitorId}/pass`;
      
      // Fetch visitor pass data
      console.log('Fetching pass for visitor ID:', visitorId);
      console.log('Final URL:', url);
      
      fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
        .then(response => {
          console.log('Response status:', response.status);
          console.log('Response headers:', response.headers);
          
          if (!response.ok) {
            if (response.status === 404) {
              throw new Error('No pass found for this visitor');
            } else if (response.status === 403) {
              throw new Error('You don\'t have permission to view this pass');
            } else {
              throw new Error('Unable to load pass. Please try again.');
            }
          }
          return response.json();
        })
        .then(data => {
          console.log('Response data:', data);
          if (data.success) {
            // Open the existing pass modal with the fetched data
            console.log('Opening modal with data:', data.data);
            openVisitorPassModal(data.data);
          } else {
            console.error('API returned error:', data.message);
            throw new Error(data.message || 'Unable to load pass details');
          }
        })
        .catch(error => {
          console.error('Error loading visitor pass:', error);
          console.error('Error details:', error);
          
          // Handle different error types
          if (error.message.includes('404') || error.message.includes('No pass found')) {
            showNotification('No pass found for this visitor.', 'error');
          } else if (error.message.includes('403') || error.message.includes('permission')) {
            showNotification('You don\'t have permission to view this pass.', 'error');
          } else {
            // For other errors, try to show modal with basic info
            console.log('Attempting to show modal with basic info...');
            const basicPassData = {
              visitor_name: visitorName,
              company: 'N/A',
              purpose: 'N/A',
              pass_number: 'N/A',
              status: 'Unknown',
              check_in_time: null
            };
            openVisitorPassModal(basicPassData);
            showNotification('Unable to load full pass details, showing basic info.', 'warning');
          }
        })
        .finally(() => {
          // Re-enable button
          button.disabled = false;
          button.innerHTML = originalText;
          
          // Re-initialize Lucide icons
          if (typeof lucide !== 'undefined') {
            lucide.createIcons();
          }
        });
    }

    // Open visitor pass modal with data (same as TEST MODAL)
    function openVisitorPassModal(passData) {
      console.log('=== OPENING VISITOR PASS MODAL ===');
      console.log('Pass data:', passData);
      
      const modal = document.getElementById('visitorSuccessModal');
      if (!modal) {
        console.error('Visitor pass modal not found');
        return;
      }

      // Convert passData to visitor format that showSuccessModal expects
      const visitor = {
        name: passData.visitor_name,
        company: passData.company,
        purpose: passData.purpose,
        pass_id: passData.pass_number,
        time_in: passData.check_in_time,
        status: passData.status,
        arrival_date: passData.arrival_date,
        arrival_time: passData.arrival_time,
        expected_date_out: passData.expected_date_out,
        expected_time_out: passData.expected_time_out,
        pass_data: { qr_code: passData.qr_code }
      };

      // Use the same showSuccessModal function as TEST MODAL
      showSuccessModal(visitor);

      // After modal is shown, update additional fields and buttons
      setTimeout(() => {
        // Update status with proper color coding
        const statusEl = document.getElementById('success-status');
        if (statusEl) {
          statusEl.textContent = passData.status;
          statusEl.className = 'badge';
          
          // Set appropriate badge color based on status
          switch(passData.status) {
            case 'Active':
              statusEl.classList.add('badge-success');
              break;
            case 'Expired':
              statusEl.classList.add('badge-warning');
              break;
            case 'Revoked':
              statusEl.classList.add('badge-error');
              break;
            case 'Used':
              statusEl.classList.add('badge-info');
              break;
            default:
              statusEl.classList.add('badge-neutral');
          }
        }

        // Update download button
        const downloadBtn = modal.querySelector('button[onclick="downloadPass()"]');
        if (downloadBtn && passData.download_url) {
          downloadBtn.onclick = () => {
            window.open(passData.download_url, '_blank');
          };
          
          // Disable download for expired, revoked, or used passes
          downloadBtn.disabled = !['Active'].includes(passData.status);
          
          // Update button text based on status
          if (passData.status === 'Expired') {
            downloadBtn.textContent = 'Pass Expired';
            downloadBtn.classList.add('btn-disabled');
          } else if (passData.status === 'Revoked') {
            downloadBtn.textContent = 'Pass Revoked';
            downloadBtn.classList.add('btn-disabled');
          } else if (passData.status === 'Used') {
            downloadBtn.textContent = 'Pass Used';
            downloadBtn.classList.add('btn-disabled');
          } else {
            downloadBtn.textContent = 'Download Pass';
            downloadBtn.classList.remove('btn-disabled');
          }
        }

        // Update modal title to reflect viewing existing pass
        const modalTitle = modal.querySelector('.modal-title');
        if (modalTitle) {
          modalTitle.innerHTML = '<i data-lucide="eye" class="modal-icon"></i>View Visitor Pass';
        }

        // Re-initialize Lucide icons
        if (window.lucide && window.lucide.createIcons) {
          window.lucide.createIcons();
        }
      }, 100);
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
            <div class="flex gap-2">
              ${visitor.pass_id ? `
              <button class="vm-view-pass-btn btn btn-sm btn-primary" data-visitor-id="${visitor.id}" data-visitor-name="${visitor.name}" data-pass-url="{{ url('/visitor') }}/" onclick="event.stopPropagation();" style="background-color: var(--color-regal-navy); color: var(--color-white);">
                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View Pass
              </button>` : ''}
              ${!visitor.time_out ? 
                `<button onclick="checkOutVisitor(${visitor.id})" class="btn btn-sm btn-outline btn-error" style="color: var(--color-danger-red); border-color: var(--color-danger-red);">
                  <i data-lucide="log-out" class="w-4 h-4 mr-1"></i>Check Out
                </button>` : 
                `<button onclick="checkInVisitor(${visitor.id})" class="btn btn-sm btn-outline btn-success" style="color: var(--color-modern-teal); border-color: var(--color-modern-teal);">
                  <i data-lucide="log-in" class="w-4 h-4 mr-1"></i>Check In
                </button>`
              }
            </div>
          </div>
        </div>
      `).join('');

      // Recreate icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }

      // Ensure View Pass works in Current Visitors too
      addViewPassEventListeners();
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
      // If details panel is removed, skip
      if (!document.getElementById('visitor-details')) {
        return;
      }
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

      // Update pass information
      const passInfoSection = document.getElementById('pass-info-section');
      if (visitor.pass_id) {
        passInfoSection.classList.remove('hidden');
        document.getElementById('detail-pass-id').textContent = visitor.pass_id;
        document.getElementById('detail-pass-type').textContent = visitor.pass_type ? visitor.pass_type.replace('_', ' ').toUpperCase() : 'N/A';
        document.getElementById('detail-access-level').textContent = visitor.access_level ? visitor.access_level.replace('_', ' ').toUpperCase() : 'N/A';
        document.getElementById('detail-valid-until').textContent = visitor.pass_valid_until ? formatTime(visitor.pass_valid_until) : 'N/A';
        
        if (visitor.escort_required && visitor.escort_required !== 'no') {
          document.getElementById('escort-info').classList.remove('hidden');
          document.getElementById('detail-escort-required').textContent = visitor.escort_required.toUpperCase();
        } else {
          document.getElementById('escort-info').classList.add('hidden');
        }
      } else {
        passInfoSection.classList.add('hidden');
      }

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

    function formatDateTime(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString('en-US', { 
        month: 'numeric',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit',
        hour12: true 
      });
    }

    // Get status configuration for monitoring cards
    function getStatusConfig(status) {
      const s = (status || '').toLowerCase();
      switch (s) {
        case 'active':
          return {
            label: 'Active',
            color: '#22c55e',
            badgeClass: 'badge-success'
          };
        case 'pending':
          return {
            label: 'Pending',
            color: '#f59e0b',
            badgeClass: 'badge-warning'
          };
        case 'completed':
          return {
            label: 'Completed',
            color: '#3b82f6',
            badgeClass: 'badge-info'
          };
        case 'expired':
          return {
            label: 'Expired',
            color: '#6b7280',
            badgeClass: 'badge-neutral'
          };
        case 'revoked':
          return {
            label: 'Revoked',
            color: '#ef4444',
            badgeClass: 'badge-error'
          };
        case 'overdue':
          return {
            label: 'Overdue',
            color: '#dc2626',
            badgeClass: 'badge-error'
          };
        case 'pending_exit':
          return {
            label: 'Pending Exit',
            color: '#ef4444',
            badgeClass: 'badge-error'
          };
        default:
          return {
            label: 'Unknown',
            color: '#9ca3af',
            badgeClass: 'badge-ghost'
          };
      }
    }

    // Calculate duration between check-in and check-out times
    function calculateDuration(checkInTime, checkOutTime) {
      if (!checkInTime || checkInTime === 'N/A') return null;
      
      try {
        const start = new Date(checkInTime);
        const end = checkOutTime ? new Date(checkOutTime) : new Date();
        
        // Check if dates are valid
        if (isNaN(start.getTime())) {
          console.error('Invalid check-in time:', checkInTime);
          return 'Invalid time';
        }
        
        const diffMs = end - start;
        const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
        const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
        
        if (diffMs < 0) return '0m'; // Handle negative durations
        
        if (diffHours > 0) {
          return `${diffHours}h ${diffMinutes}m`;
        } else {
          return `${diffMinutes}m`;
        }
      } catch (error) {
        console.error('Duration calculation error:', error);
        return 'Error';
      }
    }

    // Render status as a colored badge for Monitoring table (kept for compatibility)
    function getStatusBadge(status) {
      const s = (status || '').toLowerCase();
      let badgeClass = 'badge';
      switch (s) {
        case 'active':
          badgeClass += ' badge-success';
          break;
        case 'pending':
          badgeClass += ' badge-warning';
          break;
        case 'completed':
          badgeClass += ' badge-info';
          break;
        case 'expired':
          badgeClass += ' badge-neutral';
          break;
        case 'revoked':
          badgeClass += ' badge-error';
          break;
        default:
          badgeClass += ' badge-ghost';
      }
      return `<span class="${badgeClass}">${status}</span>`;
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

      // Setup monitoring search
      const monitoringSearchInput = document.getElementById('monitoringSearch');
      if (monitoringSearchInput) {
        monitoringSearchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const monitoringCards = document.querySelectorAll('.monitoring-visitor-card');
          
          monitoringCards.forEach(card => {
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
      
      if (dateElement) {
        dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
      }
      if (timeElement) {
        timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
      }
    }

    // Dark mode functionality
    function setupDarkMode() {
      const toggle = document.getElementById('darkModeToggle');
      const sunIcon = document.getElementById('sunIcon');
      const moonIcon = document.getElementById('moonIcon');
      
      function updateIcons() {
        if (sunIcon && moonIcon) {
          if(document.documentElement.classList.contains('dark')) {
            sunIcon.classList.remove('hidden');
            moonIcon.classList.add('hidden');
          } else {
            sunIcon.classList.add('hidden');
            moonIcon.classList.remove('hidden');
          }
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
      
      if (toggle) {
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
      
      // Load initial data - DISABLED to prevent 404 errors
      // loadCurrentVisitors(); // Disabled - data already loaded server-side
      // updateStats(); // Disabled - stats already loaded server-side
      
      // Load monitoring data on page load
      loadMonitoringData();
      
      
      // Initialize all Lucide icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }

      // Ensure View Pass buttons are clickable on initial render
      // This binds events for server-rendered buttons before any AJAX refresh
      if (typeof addViewPassEventListeners === 'function') {
        addViewPassEventListeners();
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
      
      // Initialize pass issuance functionality
      setupPassIssuanceModal();
      
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
    const registrationForm = document.getElementById('visitorRegistrationForm');
    if (registrationForm) {
      registrationForm.addEventListener('submit', function(e) {
      e.preventDefault();
      console.log('Form submitted!');
      
      // Add loading state
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i data-lucide="loader-2" class="btn-icon animate-spin"></i>Registering...';
      submitBtn.disabled = true;
      
      // Remove test code - we'll handle this properly with the actual response
      
      // Submit form data
      const formData = new FormData(this);
      console.log('Form data:', Object.fromEntries(formData));
      
      fetch('{{ route("visitor.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        }
      })
      .then(response => {
        console.log('Response received:', response);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('Data received:', data);
        
        // Get form data for modal display - prioritize server response data
        const formData = new FormData(document.getElementById('visitorRegistrationForm'));
        const visitorData = {
          name: data.visitor?.name || formData.get('name') || 'New Visitor',
          company: data.visitor?.company || formData.get('company') || 'N/A',
          purpose: data.visitor?.purpose || formData.get('purpose') || 'N/A',
          pass_id: data.visitor?.pass_id || 'VMS-' + Math.random().toString(36).substr(2, 6).toUpperCase(),
          time_in: data.visitor?.time_in || null, // Keep null for registered visitors
          pass_data: data.pass_data || null,
          expected_date_out: data.visitor?.expected_date_out || formData.get('expected_date_out') || null,
          expected_time_out: data.visitor?.expected_time_out || formData.get('expected_time_out') || null,
          arrival_date: data.visitor?.arrival_date || formData.get('arrival_date') || null,
          arrival_time: data.visitor?.arrival_time || formData.get('arrival_time') || null,
          validity_period: data.validity_info?.validity_period || null
        };
        
        console.log('Form data extracted:', {
          name: formData.get('name'),
          company: formData.get('company'),
          purpose: formData.get('purpose'),
          email: formData.get('email')
        });
        
        console.log('Prepared visitor data for modal:', visitorData);
        
        // Close registration modal first
        closeVisitorModal();
        
        // Show success modal immediately after registration
          console.log('About to show success modal...');
          console.log('Calling showSuccessModal with:', visitorData);
          showSuccessModal(visitorData);
        
        // Show success message
        if (data.success) {
          console.log('Registration successful!');
          showNotification('Visitor registered and pass issued! Please check them in when they arrive.', 'success');
        } else {
          console.log('Registration had issues but showing modal anyway');
          showNotification('Visitor registered (with warnings)', 'warning');
        }
        
        // Refresh visitor list
        if (typeof loadCurrentVisitors === 'function') {
          loadCurrentVisitors();
        }
        if (typeof updateStats === 'function') {
          updateStats();
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Even if there's an error, try to show success modal with form data
        const formData = new FormData(document.getElementById('visitorRegistrationForm'));
        const visitorData = {
          name: formData.get('name') || 'New Visitor',
          company: formData.get('company') || 'N/A',
          purpose: formData.get('purpose') || 'N/A',
          pass_id: 'VMS-' + Math.random().toString(36).substr(2, 6).toUpperCase(),
          time_in: null, // Keep null for registered visitors
          pass_data: null
        };
        
        // Close registration modal
        closeVisitorModal();
        
        // Show success modal immediately
          console.log('Showing modal after error...');
          showSuccessModal(visitorData);
        
        showNotification('Visitor registered (offline mode)', 'warning');
      })
      .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      });
    });
    }

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

    // ===== SUCCESS MODAL FUNCTIONS =====
    
    // Show success modal with visitor data
    // Function to calculate validity period based on expected departure time
    function calculateValidityPeriod(visitor) {
      console.log('Calculating validity period for visitor:', visitor);
      
      if (visitor.expected_date_out && visitor.expected_time_out) {
        console.log('Using expected_date_out and expected_time_out');
        // Combine expected date and time
        const expectedDateTime = new Date(visitor.expected_date_out + ' ' + visitor.expected_time_out);
        const now = new Date();
        
        console.log('Expected datetime:', expectedDateTime);
        console.log('Current datetime:', now);
        
        // Calculate difference in hours
        const diffMs = expectedDateTime - now;
        const diffHours = Math.ceil(diffMs / (1000 * 60 * 60));
        
        console.log('Difference in hours:', diffHours);
        
        if (diffHours <= 0) {
          return 'Expired';
        } else if (diffHours < 24) {
          return `${diffHours} Hour${diffHours > 1 ? 's' : ''}`;
        } else {
          const diffDays = Math.floor(diffHours / 24);
          const remainingHours = diffHours % 24;
          if (remainingHours === 0) {
            return `${diffDays} Day${diffDays > 1 ? 's' : ''}`;
          } else {
            return `${diffDays} Day${diffDays > 1 ? 's' : ''} ${remainingHours} Hour${remainingHours > 1 ? 's' : ''}`;
          }
        }
      } else if (visitor.expected_time_out) {
        console.log('Using only expected_time_out');
        // If only time is provided, assume today
        const today = new Date().toISOString().split('T')[0];
        const expectedDateTime = new Date(today + ' ' + visitor.expected_time_out);
        const now = new Date();
        
        const diffMs = expectedDateTime - now;
        const diffHours = Math.ceil(diffMs / (1000 * 60 * 60));
        
        if (diffHours <= 0) {
          return 'Expired';
        } else {
          return `${diffHours} Hour${diffHours > 1 ? 's' : ''}`;
        }
      }
      
      console.log('No expected departure time found, using default');
      // Default fallback
      return '24 Hours';
    }

    function showSuccessModal(visitor) {
      console.log('=== SHOW SUCCESS MODAL FUNCTION CALLED ===');
      console.log('Visitor data:', visitor);
      
      const modal = document.getElementById('visitorSuccessModal');
      console.log('Modal element found:', modal);
      
      if (!modal) {
        console.error('Success modal not found!');
        alert('Success modal not found! Check console for details.');
        return;
      }
      
      // Update success modal with visitor data
      if (visitor) {
        console.log('Updating modal with visitor data...');
        const passId = visitor.pass_id || 'VMS-' + Math.random().toString(36).substr(2, 6).toUpperCase();
        
        // Update all the fields
        const passNumberEl = document.getElementById('success-pass-number');
        const visitorNameEl = document.getElementById('success-visitor-name');
        const companyEl = document.getElementById('success-company');
        const checkinTimeEl = document.getElementById('success-checkin-time');
        const purposeEl = document.getElementById('success-purpose');
        const statusEl = document.getElementById('success-status');
        const arrivalDateEl = document.getElementById('success-arrival-date');
        const arrivalDateValueEl = document.getElementById('success-arrival-date-value');
        const arrivalTimeEl = document.getElementById('success-arrival-time');
        const arrivalTimeValueEl = document.getElementById('success-arrival-time-value');
        const expectedDateOutEl = document.getElementById('success-expected-date-out');
        const expectedDateOutValueEl = document.getElementById('success-expected-date-out-value');
        const expectedTimeOutEl = document.getElementById('success-expected-time-out');
        const expectedTimeOutValueEl = document.getElementById('success-expected-time-out-value');
        
        if (passNumberEl) passNumberEl.textContent = passId;
        if (visitorNameEl) visitorNameEl.textContent = visitor.name || 'N/A';
        if (companyEl) companyEl.textContent = visitor.company || 'N/A';
        if (checkinTimeEl) checkinTimeEl.textContent = visitor.time_in ? formatDateTime(visitor.time_in) : 'Not checked in yet';
        if (purposeEl) purposeEl.textContent = visitor.purpose || 'N/A';
        if (statusEl) statusEl.textContent = visitor.time_in ? 'Active' : 'Registered';
        
        // Show/hide arrival date and time fields
        if (visitor.arrival_date) {
          if (arrivalDateEl) arrivalDateEl.style.display = 'flex';
          if (arrivalDateValueEl) arrivalDateValueEl.textContent = visitor.arrival_date;
        } else {
          if (arrivalDateEl) arrivalDateEl.style.display = 'none';
        }
        
        if (visitor.arrival_time) {
          if (arrivalTimeEl) arrivalTimeEl.style.display = 'flex';
          if (arrivalTimeValueEl) arrivalTimeValueEl.textContent = visitor.arrival_time;
        } else {
          if (arrivalTimeEl) arrivalTimeEl.style.display = 'none';
        }
        
        // Show/hide expected date and time out fields
        if (visitor.expected_date_out) {
          if (expectedDateOutEl) expectedDateOutEl.style.display = 'flex';
          if (expectedDateOutValueEl) expectedDateOutValueEl.textContent = visitor.expected_date_out;
        } else {
          if (expectedDateOutEl) expectedDateOutEl.style.display = 'none';
        }
        
        if (visitor.expected_time_out) {
          if (expectedTimeOutEl) expectedTimeOutEl.style.display = 'flex';
          if (expectedTimeOutValueEl) expectedTimeOutValueEl.textContent = visitor.expected_time_out;
        } else {
          if (expectedTimeOutEl) expectedTimeOutEl.style.display = 'none';
        }

        // Update validity period - use backend calculation if available, otherwise calculate frontend
        console.log('Updating validity period...');
        console.log('Visitor validity_period:', visitor.validity_period);
        console.log('Visitor expected_date_out:', visitor.expected_date_out);
        console.log('Visitor expected_time_out:', visitor.expected_time_out);
        
        const validityPeriod = visitor.validity_period || calculateValidityPeriod(visitor);
        console.log('Calculated validity period:', validityPeriod);
        
        const validityEl = document.getElementById('validity-period');
        if (validityEl) {
          validityEl.textContent = validityPeriod;
          console.log('Updated validity element with:', validityPeriod);
        } else {
          console.error('Validity element not found!');
        }

        // Render QR image using same URL stored in pass_data
        const qrContainer = document.getElementById('qr-code-placeholder');
        if (qrContainer) {
          qrContainer.innerHTML = '';
          const img = document.createElement('img');
          img.alt = 'Visitor Pass QR Code';
          // If visitor has pass_data with qr_code, use it; else generate based on pass number
          const qrUrl = (visitor.pass_data && visitor.pass_data.qr_code) ? visitor.pass_data.qr_code : `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent('` + "${passId}" + `')}`;
          img.src = qrUrl;
          img.width = 200;
          img.height = 200;
          qrContainer.appendChild(img);
        }
      }
      
      // Show modal immediately
      console.log('Showing modal...');
      modal.classList.add('modal-open');
      document.body.classList.add('modal-open');
      document.body.style.overflow = 'hidden';
      
      // Initialize Lucide icons
      setTimeout(() => {
        if (window.lucide && window.lucide.createIcons) {
          window.lucide.createIcons();
        }
      }, 100);
      
      console.log('Modal should be visible now!');
    }

    // Generate QR Code
    function generateQRCode(passId, visitor) {
      const qrContainer = document.getElementById('qr-code-placeholder');
      if (!qrContainer) {
        console.error('QR code container not found');
        return;
      }

      // Clear existing content
      qrContainer.innerHTML = '';

      // Create QR code data
      const qrData = {
        passId: passId,
        visitorName: visitor.name || 'N/A',
        company: visitor.company || 'N/A',
        purpose: visitor.purpose || 'N/A',
        timeIn: visitor.time_in || new Date().toISOString(),
        facility: visitor.facility?.name || 'N/A',
        generatedAt: new Date().toISOString()
      };

      const qrString = JSON.stringify(qrData);

      // Generate QR code
      QRCode.toCanvas(qrString, {
        width: 200,
        height: 200,
        margin: 2,
        color: {
          dark: '#000000',
          light: '#FFFFFF'
        }
      }, function (error, canvas) {
        if (error) {
          console.error('QR Code generation error:', error);
          qrContainer.innerHTML = `
            <i data-lucide="qr-code" class="w-16 h-16 text-gray-400"></i>
            <p class="text-sm text-gray-500 mt-2">QR Code generation failed</p>
          `;
        } else {
          qrContainer.appendChild(canvas);
        }
        
        // Re-initialize Lucide icons
        if (window.lucide && window.lucide.createIcons) {
          window.lucide.createIcons();
        }
      });
    }

    // Close success modal
    function closeSuccessModal() {
      const modal = document.getElementById('visitorSuccessModal');
      modal.classList.remove('modal-open');
      document.body.classList.remove('modal-open');
      document.body.style.overflow = 'auto';
    }

    // Download pass function
    function downloadPass() {
      // Get the current visitor data from the success modal
      const passNumber = document.getElementById('success-pass-number').textContent;
      const visitorName = document.getElementById('success-visitor-name').textContent;
      const company = document.getElementById('success-company').textContent;
      const checkinTime = document.getElementById('success-checkin-time').textContent;
      const purpose = document.getElementById('success-purpose').textContent;
      
      // Create a simple text-based pass that can be downloaded
      const passContent = `
VISITOR PASS - SOLIERA
========================

Pass Number: ${passNumber}
Visitor: ${visitorName}
Company: ${company}
Check-in Time: ${checkinTime}
Purpose: ${purpose}
Status: Active

Valid until: ${new Date(Date.now() + 24 * 60 * 60 * 1000).toLocaleString()}

This pass is valid for 24 hours from check-in time.
Please keep this pass with you at all times during your visit.

Generated on: ${new Date().toLocaleString()}
      `.trim();
      
      // Create and download the file
      const blob = new Blob([passContent], { type: 'text/plain' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `visitor-pass-${passNumber}.txt`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
      
      showNotification('Visitor pass downloaded successfully!', 'success');
    }



    // Test View Pass modal function
    function testViewPassModal() {
      console.log('Testing View Pass Modal...');
      const testPassData = {
        visitor_name: 'Test Visitor',
        company: 'Test Company',
        purpose: 'Testing View Pass Modal',
        pass_number: 'VMS-TEST456',
        status: 'Active',
        check_in_time: new Date().toISOString(),
        download_url: '#'
      };
      openVisitorPassModal(testPassData);
    }


    // ===== PASS ISSUANCE MODAL FUNCTIONS =====
    
    // Setup pass issuance functionality in modal
    function setupPassIssuanceModal() {
      const passValiditySelect = document.querySelector('select[name="pass_validity"]');
      const customValiditySection = document.getElementById('customValiditySection');
      
      if (passValiditySelect && customValiditySection) {
        passValiditySelect.addEventListener('change', function() {
          if (this.value === 'custom') {
            customValiditySection.classList.remove('hidden');
          } else {
            customValiditySection.classList.add('hidden');
          }
        });
      }
      
      // Auto-calculate validity period based on selection
      setupValidityCalculationModal();
    }

    // Auto-calculate validity period based on selection
    function setupValidityCalculationModal() {
      const passValiditySelect = document.querySelector('select[name="pass_validity"]');
      const validFromInput = document.querySelector('input[name="pass_valid_from"]');
      const validUntilInput = document.querySelector('input[name="pass_valid_until"]');
      
      if (passValiditySelect && validFromInput && validUntilInput) {
        passValiditySelect.addEventListener('change', function() {
          if (this.value !== 'custom' && this.value !== '') {
            const now = new Date();
            let validUntil = new Date(now);
            
            switch(this.value) {
              case '1_hour':
                validUntil.setHours(validUntil.getHours() + 1);
                break;
              case '4_hours':
                validUntil.setHours(validUntil.getHours() + 4);
                break;
              case '24_hours':
                validUntil.setHours(validUntil.getHours() + 24);
                break;
              case '1_day':
                validUntil.setDate(validUntil.getDate() + 1);
                break;
              case '3_days':
                validUntil.setDate(validUntil.getDate() + 3);
                break;
              case '1_week':
                validUntil.setDate(validUntil.getDate() + 7);
                break;
            }
            
            validFromInput.value = now.toISOString().slice(0, 16);
            validUntilInput.value = validUntil.toISOString().slice(0, 16);
          }
        });
      }
    }

    // ===== CHECK-IN/CHECK-OUT FUNCTIONS =====
    
    // Check out a visitor
    function checkOutVisitor(visitorId) {
      if (!confirm('Are you sure you want to check out this visitor?')) {
        return;
      }

      fetch(`{{ route('visitor.checkout', '') }}/${visitorId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification('Visitor checked out successfully!', 'success');
          // Refresh the current visitors list
          loadCurrentVisitors();
          // Refresh monitoring data if on monitoring tab
          if (currentTab === 'monitoring') {
            loadMonitoringData();
          }
        } else {
          showNotification('Error checking out visitor', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('Error checking out visitor', 'error');
      });
    }

    // Check in a visitor (for visitors who were pre-registered)
    function checkInVisitor(visitorId) {
      if (!confirm('Are you sure you want to check in this visitor?')) {
        return;
      }

      // For now, we'll just update the time_in to now
      // In a real system, you might want to create a separate check-in endpoint
      fetch(`{{ route('visitor.update', '') }}/${visitorId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          time_in: new Date().toISOString(),
          time_out: null
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification('Visitor checked in successfully!', 'success');
          // Refresh the current visitors list
          loadCurrentVisitors();
          // Refresh monitoring data if on monitoring tab
          if (currentTab === 'monitoring') {
            loadMonitoringData();
          }
        } else {
          showNotification('Error checking in visitor', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('Error checking in visitor', 'error');
      });
    }

    // Check in existing registered visitor
    function checkInExistingVisitor(visitorId) {
      if (!confirm('Are you sure you want to check in this visitor?')) {
        return;
      }

      fetch(`{{ route("visitor.checkin_existing", '') }}/${visitorId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification('Visitor checked in successfully!', 'success');
          // Refresh the current visitors list
          loadCurrentVisitors();
          // Refresh monitoring data if on monitoring tab
          if (currentTab === 'monitoring') {
            loadMonitoringData();
          }
        } else {
          showNotification(data.message || 'Error checking in visitor', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('Error checking in visitor', 'error');
      });
    }

    // View visitor details
    function viewVisitorDetails(visitorId) {
      console.log('Opening visitor details for ID:', visitorId);
      
      // Show modal and loading state
      const modal = document.getElementById('visitorDetailsModal');
      const loading = document.getElementById('visitorDetailsLoading');
      const content = document.getElementById('visitorDetailsContent');
      
      modal.classList.add('modal-open');
      loading.classList.remove('hidden');
      content.classList.add('hidden');
      
      // Fetch visitor details
      const url = `{{ route('visitor.details', '') }}/${visitorId}`;
      console.log('Fetching visitor details from:', url);
      
      fetch(url)
        .then(response => {
          console.log('Response status:', response.status);
          console.log('Response ok:', response.ok);
          
          if (!response.ok) {
            throw new Error(`HTTP ${response.status}: Failed to fetch visitor details`);
          }
          return response.json();
        })
        .then(data => {
          console.log('Response data:', data);
          
          if (data.success && data.visitor) {
            console.log('Visitor data received:', data.visitor);
            populateVisitorDetails(data.visitor);
            loading.classList.add('hidden');
            content.classList.remove('hidden');
          } else {
            console.error('Invalid response format:', data);
            throw new Error('Invalid response format');
          }
        })
        .catch(error => {
          console.error('Error fetching visitor details:', error);
          
          // Try to get visitor data from current page data as fallback
          const fallbackData = extractVisitorDataFromPage(visitorId);
          if (fallbackData) {
            console.log('Using fallback data from page');
            populateVisitorDetails(fallbackData);
            loading.classList.add('hidden');
            content.classList.remove('hidden');
            showNotification('Using cached data - some information may be outdated', 'warning');
          } else {
            showNotification('Failed to load visitor details', 'error');
            closeVisitorDetailsModal();
          }
        });
    }

    // Extract visitor data from the current page data
    function extractVisitorDataFromPage(visitorId) {
      // Try to find visitor in current monitoring data
      if (window.currentVisitors) {
        const visitor = window.currentVisitors.find(v => v.id == visitorId);
        if (visitor) {
          return visitor;
        }
      }
      
      // Try to find visitor in scheduled visits
      if (window.scheduledVisitors) {
        const visitor = window.scheduledVisitors.find(v => v.id == visitorId);
        if (visitor) {
          return visitor;
        }
      }
      
      // Try to find visitor in completed visits
      if (window.completedVisitors) {
        const visitor = window.completedVisitors.find(v => v.id == visitorId);
        if (visitor) {
          return visitor;
        }
      }
      
      return null;
    }

    // Populate visitor details in the modal
    function populateVisitorDetails(visitor) {
      console.log('Populating visitor details:', visitor);
      
      // Header information
      document.getElementById('detailVisitorName').textContent = visitor.name || 'Unknown';
      document.getElementById('detailVisitorCompany').textContent = visitor.company || 'No Company';
      document.getElementById('detailVisitorPassId').textContent = visitor.pass_id || 'N/A';
      
      // Status badge - use the status from backend
      const statusEl = document.getElementById('detailVisitorStatus');
      const status = visitor.status || 'Pending';
      statusEl.textContent = status;
      
      // Apply appropriate badge class based on status
      let badgeClass = 'badge-warning'; // default
      if (status === 'Active') {
        badgeClass = 'badge-success';
      } else if (status === 'Completed') {
        badgeClass = 'badge-info';
      } else if (status === 'Pending Exit') {
        badgeClass = 'badge-error';
      }
      
      statusEl.className = 'badge ' + badgeClass;
      
      // Duration
      const duration = calculateDuration(visitor.time_in, visitor.time_out);
      document.getElementById('detailVisitorDuration').textContent = duration;
      
      // Personal Information
      document.getElementById('detailName').textContent = visitor.name || '-';
      document.getElementById('detailCompany').textContent = visitor.company || '-';
      document.getElementById('detailEmail').textContent = visitor.email || '-';
      document.getElementById('detailContact').textContent = visitor.contact || '-';
      document.getElementById('detailIdType').textContent = visitor.id_type || '-';
      document.getElementById('detailIdNumber').textContent = visitor.id_number || '-';
      
      // Visit Information
      document.getElementById('detailPurpose').textContent = visitor.purpose || '-';
      document.getElementById('detailHost').textContent = visitor.host_employee || '-';
      document.getElementById('detailDepartment').textContent = visitor.department || '-';
      document.getElementById('detailFacility').textContent = visitor.facility?.name || '-';
      document.getElementById('detailVehicle').textContent = visitor.vehicle_plate || '-';
      
      // Time Information
      document.getElementById('detailExpectedIn').textContent = formatDateTime(visitor.arrival_date, visitor.arrival_time) || '-';
      document.getElementById('detailActualIn').textContent = formatDateTime(visitor.time_in) || '-';
      document.getElementById('detailExpectedOut').textContent = formatDateTime(visitor.expected_date_out, visitor.expected_time_out) || '-';
      document.getElementById('detailActualOut').textContent = formatDateTime(visitor.time_out) || '-';
      document.getElementById('detailDuration').textContent = duration;
      
      // Pass Information
      document.getElementById('detailPassId').textContent = visitor.pass_id || '-';
      document.getElementById('detailPassType').textContent = visitor.pass_type ? visitor.pass_type.replace('_', ' ').toUpperCase() : '-';
      document.getElementById('detailAccessLevel').textContent = visitor.access_level ? visitor.access_level.replace('_', ' ').toUpperCase() : '-';
      document.getElementById('detailValidFrom').textContent = formatDateTime(visitor.pass_valid_from) || '-';
      document.getElementById('detailValidUntil').textContent = formatDateTime(visitor.pass_valid_until) || '-';
      document.getElementById('detailEscortRequired').textContent = visitor.escort_required ? visitor.escort_required.toUpperCase() : 'NO';
      
      // Store current visitor ID for actions
      window.currentDetailVisitorId = visitor.id;
    }

    // Calculate duration between two times
    function calculateDuration(timeIn, timeOut) {
      if (!timeIn) return 'Not checked in';
      
      const start = new Date(timeIn);
      const end = timeOut ? new Date(timeOut) : new Date();
      const diffMs = end - start;
      
      if (diffMs < 0) return '0m';
      
      const hours = Math.floor(diffMs / (1000 * 60 * 60));
      const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
      
      if (hours > 0) {
        return `${hours}h ${minutes}m`;
      } else {
        return `${minutes}m`;
      }
    }

    // Format date and time
    function formatDateTime(date, time) {
      if (!date) return null;
      
      let dateTime;
      if (time) {
        dateTime = new Date(date + ' ' + time);
      } else {
        dateTime = new Date(date);
      }
      
      if (isNaN(dateTime.getTime())) return null;
      
      return dateTime.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      });
    }

    // Close visitor details modal
    function closeVisitorDetailsModal() {
      const modal = document.getElementById('visitorDetailsModal');
      modal.classList.remove('modal-open');
      window.currentDetailVisitorId = null;
    }
  </script>

  <!-- Visitor Registration Modal -->
  <div id="visitorModal" class="modal" onclick="closeVisitorModal()">
    <div class="modal-box" onclick="event.stopPropagation()">
      <!-- Modal Header -->
      <div class="modal-header">
        <h3 class="modal-title">
          <i data-lucide="user-plus" class="modal-icon"></i>
              Visitor Registered & Pass Issuance
        </h3>
        <button onclick="closeVisitorModal()" class="modal-close-btn" aria-label="Close modal">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form action="{{ route('visitor.store') }}" method="POST" enctype="multipart/form-data" id="visitorRegistrationForm" class="visitor-registration-form">
          @csrf
          
          <!-- Three Column Grid Layout -->
          <div class="form-grid-three">
            
            <!-- Personal Information Section -->
            <div class="form-section">
              <div class="section-header">
                <i data-lucide="user" class="section-icon"></i>
                <span class="section-title">Personal Information</span>
              </div>
              
              <div class="form-control">
                <label class="form-label">
                  Full Name *
                </label>
                <input type="text" name="name" class="form-input" 
                       placeholder="Enter visitor's full name" required>
              </div>

              <div class="form-control">
                <label class="form-label">
                  Email Address *
                </label>
                <input type="email" name="email" class="form-input" 
                       placeholder="Enter email address" required>
              </div>

              <div class="form-control">
                <label class="form-label">
                  Phone Number *
                </label>
                <input type="tel" name="contact" class="form-input" 
                       placeholder="Enter phone number" required>
              </div>

              <div class="form-control">
                <label class="form-label">
                  Company/Organization
                </label>
                <input type="text" name="company" class="form-input" 
                       placeholder="Enter company name">
              </div>
            </div>

            <!-- Visit Information Section -->
            <div class="form-section">
              <div class="section-header">
                <i data-lucide="calendar" class="section-icon"></i>
                <span class="section-title">Visit Information</span>
              </div>
              
              <div class="form-control">
                <label class="form-label">
                  Purpose of Visit *
                </label>
                <textarea name="purpose" class="form-textarea" 
                          placeholder="Describe the purpose of visit" required></textarea>
              </div>

              <div class="form-control">
                <label class="form-label">
                  Person to Visit
                </label>
                <input type="text" name="host_employee" class="form-input" 
                       placeholder="Enter host name">
              </div>

              <div class="form-control">
                <label class="form-label">
                  Department
                </label>
                <select name="facility_id" class="form-select">
                  <option value="">Select Department</option>
                  @foreach($facilities as $facility)
                    <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-control">
                <label class="form-label">
                  <i data-lucide="calendar" class="form-icon"></i>
                  Expected Date Out
                </label>
                <input type="date" name="expected_date_out" class="form-input" 
                       placeholder="What date will the visitor leave?">
              </div>

              <div class="form-control">
                <label class="form-label">
                  <i data-lucide="clock" class="form-icon"></i>
                  Expected Time Out
                </label>
                <input type="time" name="expected_time_out" class="form-input" 
                       placeholder="What time will the visitor leave?">
              </div>
            </div>

            <!-- Identification Section -->
            <div class="form-section">
              <div class="section-header">
                <i data-lucide="credit-card" class="section-icon"></i>
                <span class="section-title">Identification</span>
              </div>
              
              <div class="form-control">
                <label class="form-label">
                  ID Type
                </label>
                <select name="id_type" class="form-select">
                  <option value="">Select ID Type</option>
                  <option value="national_id">National ID</option>
                  <option value="driver_license">Driver's License</option>
                  <option value="passport">Passport</option>
                  <option value="company_id">Company ID</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div class="form-control">
                <label class="form-label">
                  ID Number
                </label>
                <input type="text" name="id_number" class="form-input" 
                       placeholder="Enter ID number">
              </div>

              <div class="form-control">
                <label class="form-label">
                  Vehicle Plate Number (if applicable)
                </label>
                <input type="text" name="vehicle_plate" class="form-input" 
                       placeholder="Enter vehicle plate number">
              </div>
            </div>
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
            Register Visitor & Generate Pass
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Visitor Details Modal -->
  <div id="visitorDetailsModal" class="modal" onclick="closeVisitorDetailsModal()">
    <div class="modal-box w-11/12 max-w-6xl max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
      <!-- Modal Header -->
      <div class="flex items-center justify-center mb-6 sticky top-0 bg-white z-10 pb-4 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
          <i data-lucide="user" class="w-6 h-6 text-blue-500 mr-3"></i>
          Visitor Details
        </h2>
      </div>

      <!-- Loading State -->
      <div id="visitorDetailsLoading" class="text-center py-8">
        <div class="loading loading-spinner loading-lg text-blue-500"></div>
        <p class="text-gray-600 mt-4">Loading visitor details...</p>
      </div>

      <!-- Visitor Details Content -->
      <div id="visitorDetailsContent" class="hidden">
        <!-- Visitor Info Header -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center">
                <i data-lucide="user" class="w-8 h-8 text-white"></i>
              </div>
              <div>
                <h3 id="detailVisitorName" class="text-2xl font-bold text-gray-800">Visitor Name</h3>
                <p id="detailVisitorCompany" class="text-gray-600">Company Name</p>
                <div class="flex items-center space-x-4 mt-2">
                  <span id="detailVisitorStatus" class="badge badge-primary">Status</span>
                  <span id="detailVisitorPassId" class="text-sm text-gray-500">Pass ID</span>
                </div>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm text-gray-500">Visit Duration</p>
              <p id="detailVisitorDuration" class="text-lg font-semibold text-blue-600">0h 0m</p>
            </div>
          </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Personal Information -->
          <div class="card bg-white shadow-sm h-fit">
            <div class="card-body p-6">
              <h4 class="card-title text-lg mb-4 flex items-center">
                <i data-lucide="user-circle" class="w-5 h-5 text-green-500 mr-2"></i>
                Personal Information
              </h4>
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-gray-600">Full Name:</span>
                  <span id="detailName" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Company:</span>
                  <span id="detailCompany" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Email:</span>
                  <span id="detailEmail" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Contact:</span>
                  <span id="detailContact" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">ID Type:</span>
                  <span id="detailIdType" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">ID Number:</span>
                  <span id="detailIdNumber" class="font-medium">-</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Visit Information -->
          <div class="card bg-white shadow-sm h-fit">
            <div class="card-body p-6">
              <h4 class="card-title text-lg mb-4 flex items-center">
                <i data-lucide="calendar" class="w-5 h-5 text-purple-500 mr-2"></i>
                Visit Information
              </h4>
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-gray-600">Purpose:</span>
                  <span id="detailPurpose" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Host Employee:</span>
                  <span id="detailHost" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Department:</span>
                  <span id="detailDepartment" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Facility:</span>
                  <span id="detailFacility" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Vehicle Plate:</span>
                  <span id="detailVehicle" class="font-medium">-</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Time Information -->
          <div class="card bg-white shadow-sm h-fit">
            <div class="card-body p-6">
              <h4 class="card-title text-lg mb-4 flex items-center">
                <i data-lucide="clock" class="w-5 h-5 text-orange-500 mr-2"></i>
                Time Information
              </h4>
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-gray-600">Expected In:</span>
                  <span id="detailExpectedIn" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Actual In:</span>
                  <span id="detailActualIn" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Expected Out:</span>
                  <span id="detailExpectedOut" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Actual Out:</span>
                  <span id="detailActualOut" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Duration:</span>
                  <span id="detailDuration" class="font-medium text-blue-600">-</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Pass Information -->
          <div class="card bg-white shadow-sm h-fit">
            <div class="card-body p-6">
              <h4 class="card-title text-lg mb-4 flex items-center">
                <i data-lucide="id-card" class="w-5 h-5 text-blue-500 mr-2"></i>
                Pass Information
              </h4>
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-gray-600">Pass ID:</span>
                  <span id="detailPassId" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Pass Type:</span>
                  <span id="detailPassType" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Access Level:</span>
                  <span id="detailAccessLevel" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Valid From:</span>
                  <span id="detailValidFrom" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Valid Until:</span>
                  <span id="detailValidUntil" class="font-medium">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Escort Required:</span>
                  <span id="detailEscortRequired" class="font-medium">-</span>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Visitor Registration Success Modal -->
  <div id="visitorSuccessModal" class="modal" onclick="closeSuccessModal()">
    <div class="modal-box success-modal" onclick="event.stopPropagation()">
      <!-- Modal Header -->
      <div class="modal-header">
        <h3 class="modal-title">
          <i data-lucide="user-plus" class="modal-icon"></i>
              Visitor Registered & Pass Issued
        </h3>
        <button onclick="closeSuccessModal()" class="modal-close-btn" aria-label="Close modal">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Success Message -->
      <div class="success-message">
        <div class="success-icon">
          <i data-lucide="check-circle" class="w-8 h-8"></i>
        </div>
        <h2 class="success-title">Visitor Pass Generated Successfully!</h2>
      </div>

      <!-- Modal Body -->
      <div class="modal-body success-body">
        <div class="success-content">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Pass Information Section -->
            <div class="pass-info-section">
              <h3 class="pass-info-title">Pass Information</h3>
              <div class="pass-details">
                <div class="pass-detail-item">
                  <span class="pass-detail-label">Pass Number:</span>
                    <span class="pass-detail-value" id="success-pass-number">Loading...</span>
                </div>
                <div class="pass-detail-item">
                  <span class="pass-detail-label">Visitor:</span>
                    <span class="pass-detail-value" id="success-visitor-name">Loading...</span>
                </div>
                <div class="pass-detail-item">
                  <span class="pass-detail-label">Company:</span>
                    <span class="pass-detail-value" id="success-company">Loading...</span>
                </div>
                <div class="pass-detail-item">
                  <span class="pass-detail-label">Check-in Time:</span>
                    <span class="pass-detail-value" id="success-checkin-time">Loading...</span>
                </div>
                <div class="pass-detail-item">
                  <span class="pass-detail-label">Purpose:</span>
                    <span class="pass-detail-value" id="success-purpose">Loading...</span>
                </div>
                <div class="pass-detail-item" id="success-arrival-date" style="display: none;">
                  <span class="pass-detail-label">Arrival Date:</span>
                    <span class="pass-detail-value" id="success-arrival-date-value">Loading...</span>
                </div>
                <div class="pass-detail-item" id="success-arrival-time" style="display: none;">
                  <span class="pass-detail-label">Arrival Time:</span>
                    <span class="pass-detail-value" id="success-arrival-time-value">Loading...</span>
                </div>
                <div class="pass-detail-item" id="success-expected-date-out" style="display: none;">
                  <span class="pass-detail-label">Expected Date Out:</span>
                    <span class="pass-detail-value" id="success-expected-date-out-value">Loading...</span>
                </div>
                <div class="pass-detail-item" id="success-expected-time-out" style="display: none;">
                  <span class="pass-detail-label">Expected Time Out:</span>
                    <span class="pass-detail-value" id="success-expected-time-out-value">Loading...</span>
                </div>
                <div class="pass-detail-item">
                  <span class="pass-detail-label">Status:</span>
                  <span class="pass-detail-value status-active" id="success-status">Active</span>
                </div>
              </div>
            </div>

            <!-- Pass Information -->
            <div class="pass-info-section">
              <h3 class="pass-info-title">Pass Information</h3>
              <div class="pass-info-container">
                <div class="pass-info-item">
                  <i data-lucide="id-card" class="w-6 h-6 text-blue-500"></i>
                  <div>
                    <p class="pass-info-label">Pass Type</p>
                    <p class="pass-info-value">Visitor Pass</p>
                  </div>
                </div>
                <div class="pass-info-item">
                  <i data-lucide="clock" class="w-6 h-6 text-green-500"></i>
                  <div>
                    <p class="pass-info-label">Valid For</p>
                    <p class="pass-info-value" id="validity-period">24 Hours</p>
                  </div>
                </div>
                <div class="mt-4 flex items-center justify-center">
                  <div id="qr-code-placeholder" class="border rounded p-3 bg-white"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <div class="modal-actions success-actions">
          <button type="button" onclick="downloadPass()" class="btn btn-primary">
            <i data-lucide="download" class="btn-icon"></i>
            Download Pass
          </button>
        </div>
      </div>
    </div>
  </div>
</body>
</html> 
</html> 