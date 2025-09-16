<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Facilities Reservations</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
  <style>
    /* Enhanced Button Styling */
    .btn {
      border-radius: 0.5rem;
      font-weight: 500;
      letter-spacing: 0.025em;
    }
    
    .btn-sm {
      padding: 0.5rem 0.75rem;
      font-size: 0.875rem;
      min-height: 2rem;
    }
    
    .btn-md {
      padding: 0.625rem 1rem;
      font-size: 0.875rem;
      min-height: 2.5rem;
    }
    
    /* Consistent hover effects */
    .btn:hover {
      transform: translateY(-1px);
    }
    
    .btn:active {
      transform: translateY(0);
    }
    
    /* Enhanced shadow effects */
    .btn-primary {
      background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
      border: none;
      box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }
    
    .btn-primary:hover {
      background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
      box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4);
    }
    
    .btn-warning {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
      border: none;
      box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
    }
    
    .btn-warning:hover {
      background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
      box-shadow: 0 4px 8px rgba(245, 158, 11, 0.4);
    }
    
    .btn-success {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      border: none;
      box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
    }
    
    .btn-success:hover {
      background: linear-gradient(135deg, #059669 0%, #047857 100%);
      box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
    }
    
    .btn-error {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      border: none;
      box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
    }
    
    .btn-error:hover {
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      box-shadow: 0 4px 8px rgba(239, 68, 68, 0.4);
    }
    
    .btn-outline {
      border: 2px solid #e5e7eb;
      background: transparent;
      color: #374151;
    }
    
    .btn-outline:hover {
      background: #f3f4f6;
      border-color: #d1d5db;
      color: #111827;
    }
    
    /* Focus states for accessibility */
    .btn:focus {
      outline: 2px solid #3b82f6;
      outline-offset: 2px;
    }
    
    /* Smooth transitions for all interactive elements */
    .btn, .btn * {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Facility card tweaks */
    .facility-card {
      display: flex;
      flex-direction: column;
      height: 100%;
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 0.75rem;
      overflow: hidden;
    }

    .facility-card-image {
      position: relative;
      width: 100%;
      height: 10rem;
      overflow: hidden;
    }

    .facility-card-image img { width: 100%; height: 100%; object-fit: cover; display: block; }

    .facility-status-badge { position: absolute; top: 0.75rem; right: 0.75rem; }

    .facility-card-body { padding: 1.25rem; display: flex; flex-direction: column; flex: 1; }

    .facility-card-title { font-weight: 700; color: #1f2937; font-size: 1.25rem; line-height: 1.5rem; }

    .line-clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .meta-row { display: flex; flex-wrap: wrap; gap: 1.25rem; align-items: center; }
    .meta-item { display: flex; align-items: center; color: #6b7280; font-size: 0.875rem; }
    .meta-item i { margin-right: 0.5rem; }
    .muted { color: #6b7280; font-size: 0.875rem; }

    /* Facility Action Buttons */
    .facility-action-btn {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 0.0625rem;
      padding: 0.25rem 0.1875rem;
      border-radius: 0.25rem;
      font-size: 0.5rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.025em;
      border: none;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      min-height: 1.75rem;
      position: relative;
      overflow: hidden;
    }
    
    .facility-action-btn i {
      transition: transform 0.3s ease;
    }
    
    .facility-action-btn span {
      font-size: 0.4375rem;
      line-height: 1;
    }
    
    /* View Button - Soft Blue/Teal */
    .facility-btn-view {
      background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
      color: white;
      box-shadow: 0 2px 8px rgba(14, 165, 233, 0.25);
    }
    
    .facility-btn-view:hover {
      background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
      box-shadow: 0 4px 12px rgba(14, 165, 233, 0.35);
      transform: translateY(-2px);
    }
    
    .facility-btn-view:hover i {
      transform: scale(1.1);
    }
    
    /* Edit Button - Warm Orange */
    .facility-btn-edit {
      background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
      color: white;
      box-shadow: 0 2px 8px rgba(249, 115, 22, 0.25);
    }
    
    .facility-btn-edit:hover {
      background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
      box-shadow: 0 4px 12px rgba(249, 115, 22, 0.35);
      transform: translateY(-2px);
    }
    
    .facility-btn-edit:hover i {
      transform: scale(1.1);
    }
    
    /* Reserve Button - Green */
    .facility-btn-reserve {
      background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
      color: white;
      box-shadow: 0 2px 8px rgba(34, 197, 94, 0.25);
    }
    
    .facility-btn-reserve:hover {
      background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
      box-shadow: 0 4px 12px rgba(34, 197, 94, 0.35);
      transform: translateY(-2px);
    }
    
    .facility-btn-reserve:hover i {
      transform: scale(1.1);
    }

    .facility-btn-free {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
      color: white;
      border: 1px solid #d97706;
      transition: all 0.3s ease;
    }

    .facility-btn-free:hover {
      background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .facility-btn-free:hover i {
      transform: scale(1.1);
    }

    /* Custom scrollbar for Recent Requests */
    #mon-recent::-webkit-scrollbar {
      width: 6px;
    }
    
    #mon-recent::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 3px;
    }
    
    #mon-recent::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 3px;
      transition: background 0.2s ease;
    }
    
    #mon-recent::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }
    
    /* Firefox scrollbar */
    #mon-recent {
      scrollbar-width: thin;
      scrollbar-color: #cbd5e1 #f1f5f9;
    }
    
    /* Delete Button - Soft Red */
    .facility-btn-delete {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: white;
      box-shadow: 0 2px 8px rgba(239, 68, 68, 0.25);
    }
    
    .facility-btn-delete:hover {
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
      transform: translateY(-2px);
    }
    
    .facility-btn-delete:hover i {
      transform: scale(1.1);
    }
    
    /* Active state for all buttons */
    .facility-action-btn:active {
      transform: translateY(0);
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
    }
    
    /* Focus state for accessibility */
    .facility-action-btn:focus {
      outline: 2px solid #3b82f6;
      outline-offset: 2px;
    }
    
    /* List view styles */
    .facility-card.list-view {
      flex-direction: row;
      height: auto;
      min-height: 200px;
    }
    
    .facility-card.list-view .facility-card-image {
      width: 300px;
      height: 200px;
      flex-shrink: 0;
    }
    
    .facility-card.list-view .facility-card-body {
      flex: 1;
      padding: 1.5rem;
    }
    
    .facility-card.list-view .facility-card-title {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
    }
    
    .facility-card.list-view .meta-row {
      margin-bottom: 1rem;
    }
    
    .facility-card.list-view .facility-action-btn {
      min-height: 2.5rem;
      padding: 0.5rem 1rem;
      font-size: 0.75rem;
    }
    
    .facility-card.list-view .facility-action-btn span {
      font-size: 0.75rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .facility-action-btn {
        min-height: 1.5rem;
        padding: 0.1875rem 0.125rem;
        font-size: 0.4375rem;
      }
      
      .facility-action-btn span {
        font-size: 0.375rem;
      }
      
      .facility-card.list-view {
        flex-direction: column;
        min-height: auto;
      }
      
      .facility-card.list-view .facility-card-image {
        width: 100%;
        height: 150px;
      }
    }
    /* Image overlay edit controls */
    .img-edit-wrap { position: relative; }
    .img-edit-overlay { position: absolute; inset: 0; display: none; align-items: center; justify-content: center; gap: .75rem; background: rgba(0,0,0,.35); }
    .img-edit-wrap:hover .img-edit-overlay { display: flex; }
    .img-edit-btn { background: #fff; color: #111827; border-radius: .5rem; padding: .35rem .6rem; font-size: .875rem; box-shadow: 0 2px 8px rgba(0,0,0,.15); display: inline-flex; align-items: center; gap: .35rem; }
    .img-edit-btn:hover { background: #f3f4f6; }
    
    /* View toggle button styles */
    #viewToggleBtn {
      transition: all 0.2s ease;
      flex-shrink: 0; /* Prevent button from shrinking */
      min-width: 2.5rem; /* Ensure minimum touch target */
      min-height: 2.5rem;
      display: inline-flex !important; /* Force visibility */
      visibility: visible !important; /* Override any hidden states */
      opacity: 1 !important; /* Ensure full opacity */
    }
    
    #viewToggleBtn:hover {
      transform: scale(1.05);
    }
    
    #viewToggleBtn:focus {
      outline: 2px solid #3b82f6;
      outline-offset: 2px;
    }
    
    /* Ensure button is always visible at all breakpoints */
    @media (max-width: 640px) {
      #viewToggleBtn {
        min-width: 2rem;
        min-height: 2rem;
        padding: 0.25rem;
      }
    }
    
    /* Ensure the toolbar container doesn't hide the button */
    .flex.items-center.space-x-2 {
      flex-wrap: nowrap;
      overflow: visible;
    }
    
    /* Smooth transitions for layout changes */
    .facility-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Respect reduced motion preferences */
    @media (prefers-reduced-motion: reduce) {
      .facility-card,
      #viewToggleBtn {
        transition: none;
      }
      
      #viewToggleBtn:hover {
        transform: none;
      }
    }
  </style>
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

        <!-- Stats Header -->
        <div class="mb-4 sm:mb-6">
          <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="calendar" class="w-5 h-5 text-blue-600"></i>
            Facility Reservations
          </h2>
          <!-- underline divider (matches other modules) -->
          <div class="border-b border-gray-200 mt-3"></div>
        </div>

        <!-- Stats Cards (DaisyUI) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
          <!-- Total Facilities -->
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-primary">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-primary text-primary-content rounded-full w-10 h-10 flex items-center justify-center">
                    <i data-lucide="building" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-primary badge-outline text-xs">All</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-primary justify-center mb-1">{{ $facilities->count() }}</h2>
                <p class="text-sm text-base-content/70">Total Facilities</p>
              </div>
            </div>
          </div>

          <!-- Available Facilities -->
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-success">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-success text-success-content rounded-full w-10 h-10 flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-success badge-outline text-xs">Open</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-success justify-center mb-1">{{ $facilities->where('status', 'available')->count() }}</h2>
                <p class="text-sm text-base-content/70">Available</p>
              </div>
            </div>
          </div>

          <!-- Occupied Facilities -->
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-error">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-error text-error-content rounded-full w-10 h-10 flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-error badge-outline text-xs">Busy</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-error justify-center mb-1">{{ $facilities->where('status', 'occupied')->count() }}</h2>
                <p class="text-sm text-base-content/70">Occupied</p>
              </div>
            </div>
          </div>

          <!-- Total Reservations -->
          <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-info">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                    <div class="bg-info text-info-content rounded-full w-10 h-10 flex items-center justify-center">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-info badge-outline text-xs">Total</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-info justify-center mb-1">{{ $facilities->sum(function($facility) { return $facility->reservations->count(); }) }}</h2>
                <p class="text-sm text-base-content/70">Total Reservations</p>
              </div>
            </div>
          </div>
        </div>


        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3 mb-8">
          <a href="{{ route('facilities.reservation.landing') }}" class="btn btn-outline btn-md hover:btn-primary transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
            <i data-lucide="home" class="w-4 h-4 mr-2"></i>
        
          </a>
          <button type="button" id="openCreateFacilityModal" class="btn btn-primary btn-md hover:btn-primary-focus transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Add New Facility
          </button>
          <a href="{{ route('facility_reservations.calendar') }}" class="btn btn-outline btn-md hover:btn-primary transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
            <i data-lucide="calendar-days" class="w-4 h-4 mr-2"></i>
            <span>Calendar View</span>
          </a>
        </div>

        <!-- Calendar View (Hidden by default) -->
        <div id="calendarView" class="bg-white rounded-xl shadow-lg p-6 mb-8 hidden">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
              <i data-lucide="calendar-days" class="w-6 h-6 text-blue-500 mr-3"></i>
              Facility Availability Calendar
            </h2>
            <div class="flex items-center gap-3">
              <select id="calendarFacilityFilter" class="select select-bordered select-sm">
                <option value="">All Facilities</option>
                @foreach($facilities as $facility)
                  <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                @endforeach
              </select>
              <button onclick="toggleCalendarView()" class="btn btn-ghost btn-sm">
                <i data-lucide="grid-3x3" class="w-4 h-4"></i>
              </button>
            </div>
          </div>
          
          <!-- Calendar Container -->
          <div id="facilityCalendar" class="w-full">
            <!-- Calendar will be rendered here -->
            <div class="text-center py-12">
              <div class="loading loading-spinner loading-lg"></div>
              <p class="mt-4 text-gray-600">Loading calendar...</p>
            </div>
          </div>
        </div>

        <!-- Clickable Breadcrumb Navigation -->
        <div class="mb-6">
          <nav class="flex items-center space-x-2 text-sm">
            <button id="nav-directory" class="text-blue-600 hover:text-blue-800 font-medium flex items-center transition-colors duration-200 {{ $activeTab==='directory' ? 'text-blue-800 font-semibold' : '' }}" onclick="facilityShowTab('directory')">
              <i data-lucide="building" class="w-4 h-4 mr-1"></i>
              Facility Directory
            </button>
            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
            <button id="nav-monitoring" class="text-gray-600 hover:text-blue-600 font-medium flex items-center transition-colors duration-200 {{ $activeTab==='monitoring' ? 'text-blue-600 font-semibold' : '' }}" onclick="facilityShowTab('monitoring')">
              <i data-lucide="activity" class="w-4 h-4 inline mr-1"></i>
              Monitoring
            </button>
            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
            <button id="nav-equipment" class="text-gray-600 hover:text-blue-600 font-medium flex items-center transition-colors duration-200 {{ $activeTab==='equipment' ? 'text-blue-600 font-semibold' : '' }}" onclick="facilityShowTab('equipment')">
              <i data-lucide="box" class="w-4 h-4 inline mr-1"></i>
              Equipment Details
            </button>
          </nav>
        </div>

        <!-- Facilities Grid -->
        <div id="facilitiesGridView" class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
              <i data-lucide="building" class="w-6 h-6 text-blue-500 mr-3"></i>
              Facility Directory
            </h2>
            <div class="flex items-center space-x-2">
              <span class="text-sm text-gray-500">Total: <span id="facilityCount">{{ $facilities->count() }}</span> facilities</span>
              <button 
                id="viewToggleBtn" 
                class="btn btn-ghost btn-sm" 
                title="Switch to list view"
                aria-label="Switch to list view"
                aria-pressed="false"
                tabindex="0"
              >
                <i data-lucide="list" class="w-4 h-4" style="display: inline-block;"></i>
                <span class="fallback-icon" style="display: none;">☰</span>
                </button>
            </div>
          </div>

          @if($facilities->count() > 0)

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              @foreach($facilities as $facility)
                <div id="facility-card-{{ $facility->id }}" class="facility-card hover:shadow-lg transition-all duration-300 hover:scale-105">
                  @if($facility->cover_url)
                    <div class="facility-card-image">
                      <img src="{{ $facility->cover_url }}" alt="{{ $facility->name }}">
                      <div class="facility-status-badge">
                        <div class="badge badge-lg {{ $facility->status === 'available' ? 'badge-success' : ($facility->status === 'occupied' ? 'badge-error' : 'badge-warning') }}">
                          {{ ucfirst($facility->status) }}
                        </div>
                      </div>
                    </div>
                  @endif
                  <div class="facility-card-body">
                  <div class="flex justify-between items-start mb-2">
                    <h3 class="facility-card-title">{{ $facility->name }}</h3>
                      <div class="flex items-center gap-1">
                        @if($facility->status === 'available')
                          <div class="w-2 h-2 bg-green-500 rounded-full" title="Available"></div>
                        @elseif($facility->status === 'occupied')
                          <div class="w-2 h-2 bg-red-500 rounded-full" title="Occupied"></div>
                        @else
                          <div class="w-2 h-2 bg-gray-400 rounded-full" title="Unavailable"></div>
                        @endif
                      </div>
                  </div>
                  
                  @if($facility->description)
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $facility->description }}</p>
                  @endif

                    <!-- Enhanced Facility Details -->
                    <div class="mb-4 space-y-2">
                      <div class="meta-row">
                      @if($facility->location)
                        <div class="meta-item">
                          <i data-lucide="map-pin" class="w-4 h-4"></i>
                          <span>{{ $facility->location }}</span>
                        </div>
                      @endif
                        <div class="meta-item">
                          <i data-lucide="users" class="w-4 h-4"></i>
                          <span>Capacity: {{ $facility->capacity ?? 'N/A' }}</span>
                        </div>
                      </div>
                      
                      <div class="meta-row">
                      <div class="meta-item">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        <span>Reservations: {{ $facility->reservations->count() }}</span>
                    </div>
                    <div class="meta-item">
                          <i data-lucide="star" class="w-4 h-4"></i>
                          <span>Rating: {{ $facility->rating ?? 'N/A' }}</span>
                        </div>
                      </div>

                      <!-- Amenities -->
                      @if($facility->amenities)
                        <div class="flex flex-wrap gap-1 mt-2">
                          @foreach(explode(',', $facility->amenities) as $amenity)
                            <span class="badge badge-outline badge-xs">{{ trim($amenity) }}</span>
                          @endforeach
                        </div>
                      @endif

                      <div class="meta-item text-xs text-gray-500">
                        <i data-lucide="clock" class="w-3 h-3"></i>
                      <span>Updated: {{ $facility->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="mt-auto pt-4 border-t border-gray-100">
                    @if($facility->status === 'available')
                      <!-- 4 buttons layout for available facilities -->
                      <div class="grid grid-cols-2 gap-3 text-sm">
                        <button type="button"
                           class="openViewFacilityBtn facility-action-btn facility-btn-view h-9"
                           data-id="{{ $facility->id }}">
                          <i data-lucide="eye" class="w-3 h-3"></i>
                          <span>View</span>
                        </button>

                        <button type="button"
                           class="openEditFacilityBtn facility-action-btn facility-btn-edit h-9"
                           data-id="{{ $facility->id }}"
                           data-name="{{ $facility->name }}"
                           data-location="{{ $facility->location }}"
                           data-description="{{ $facility->description }}"
                           data-status="{{ $facility->status }}">
                          <i data-lucide="edit" class="w-3 h-3"></i>
                          <span>Edit</span>
                        </button>

                        <button type="button"
                           class="openReserveFacilityBtn facility-action-btn facility-btn-reserve h-9"
                           data-id="{{ $facility->id }}"
                           data-name="{{ $facility->name }}">
                          <i data-lucide="calendar-plus" class="w-3 h-3"></i>
                          <span>Reserve</span>
                        </button>

                        <button type="button" 
                                class="deleteFacilityBtn facility-action-btn facility-btn-delete h-9" 
                                data-id="{{ $facility->id }}"
                                data-name="{{ $facility->name }}"
                                data-location="{{ $facility->location }}"
                                data-status="{{ $facility->status }}"
                                data-reservations="{{ $facility->reservations->count() }}"
                                data-url="{{ route('facilities.destroy', $facility->id) }}">
                          <i data-lucide="trash-2" class="w-3 h-3"></i>
                          <span>Delete</span>
                        </button>
                      </div>
                    @else
                      <!-- 4 buttons layout for occupied facilities -->
                      <div class="grid grid-cols-2 gap-3 text-sm">
                        <button type="button"
                           class="openViewFacilityBtn facility-action-btn facility-btn-view h-9"
                           data-id="{{ $facility->id }}">
                          <i data-lucide="eye" class="w-3 h-3"></i>
                          <span>View</span>
                        </button>

                        <button type="button"
                           class="openEditFacilityBtn facility-action-btn facility-btn-edit h-9"
                           data-id="{{ $facility->id }}"
                           data-name="{{ $facility->name }}"
                           data-location="{{ $facility->location }}"
                           data-description="{{ $facility->description }}"
                           data-status="{{ $facility->status }}">
                          <i data-lucide="edit" class="w-3 h-3"></i>
                          <span>Edit</span>
                        </button>

                        <button type="button" 
                                class="freeFacilityBtn facility-action-btn facility-btn-free h-9" 
                                data-id="{{ $facility->id }}"
                                data-name="{{ $facility->name }}">
                          <i data-lucide="unlock" class="w-3 h-3"></i>
                          <span>Free</span>
                        </button>

                        <button type="button" 
                                class="deleteFacilityBtn facility-action-btn facility-btn-delete h-9" 
                                data-id="{{ $facility->id }}"
                                data-name="{{ $facility->name }}"
                                data-location="{{ $facility->location }}"
                                data-status="{{ $facility->status }}"
                                data-reservations="{{ $facility->reservations->count() }}"
                                data-url="{{ route('facilities.destroy', $facility->id) }}">
                          <i data-lucide="trash-2" class="w-3 h-3"></i>
                          <span>Delete</span>
                        </button>
                      </div>
                    @endif
                  </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-12">
              <i data-lucide="building" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
              <h3 class="text-lg font-semibold text-gray-600 mb-2">No Facilities Found</h3>
              <p class="text-gray-500 mb-6">Add your first facility to get started.</p>
              <a href="{{ route('facilities.create') }}" class="btn btn-primary btn-md hover:btn-primary-focus transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Add Facility
              </a>
            </div>
          @endif
        </div>

        <!-- Monitoring Tab Panel (empty placeholder for now) -->
        <div id="facilitiesMonitoringView" class="bg-white rounded-xl shadow-lg p-6 hidden">
          <!-- Monitoring Header with Export Button -->
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
              <i data-lucide="activity" class="w-6 h-6 text-blue-500 mr-3"></i>
              Monitoring Dashboard
            </h3>
            <button onclick="exportMonitoringPdf()" class="btn btn-outline btn-sm hover:btn-primary transition-all duration-300 shadow-md hover:shadow-lg">
              <i data-lucide="download" class="w-4 h-4 mr-2"></i>
              Export Data
            </button>
          </div>
          
          <!-- Stats Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-primary">
              <div class="card-body p-4">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm text-gray-500">Total Requests</span>
                  <i data-lucide="clipboard-list" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div class="text-3xl font-bold" id="mon-total">0</div>
                <div class="badge badge-success badge-outline mt-2 text-xs" id="mon-total-diff">&nbsp;</div>
              </div>
            </div>
            <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-warning">
              <div class="card-body p-4">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm text-gray-500">In Progress</span>
                  <i data-lucide="clock" class="w-5 h-5 text-amber-500"></i>
                </div>
                <div class="text-3xl font-bold" id="mon-inprogress">0</div>
                <div class="badge badge-error badge-outline mt-2 text-xs" id="mon-inprogress-diff">&nbsp;</div>
              </div>
            </div>
            <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-success">
              <div class="card-body p-4">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm text-gray-500">Completed</span>
                  <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <div class="text-3xl font-bold" id="mon-completed">0</div>
                <div class="badge badge-success badge-outline mt-2 text-xs" id="mon-completed-diff">&nbsp;</div>
              </div>
            </div>
            <div class="card bg-base-100 shadow-xl transition-all duration-300 border-l-4 border-l-error">
              <div class="card-body p-4">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm text-gray-500">Urgent</span>
                  <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                </div>
                <div class="text-3xl font-bold" id="mon-urgent">0</div>
                <div class="badge badge-error badge-outline mt-2 text-xs" id="mon-urgent-diff">&nbsp;</div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Weekly Requests Overview -->
            <div class="card bg-base-100 shadow border lg:col-span-2">
              <div class="card-body">
                <h3 class="card-title text-lg mb-2">Weekly Requests Overview</h3>
                <canvas id="mon-weekly-chart" height="120"></canvas>
              </div>
            </div>
            <!-- Recent Requests -->
            <div class="card bg-base-100 shadow border">
              <div class="card-body">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="card-title text-lg">Recent Requests</h3>
                  <a href="{{ route('facility_reservations.index') }}" class="text-sm text-blue-600 hover:underline">View All</a>
                </div>
                <div id="mon-recent" class="space-y-3 max-h-96 overflow-y-auto pr-2 border border-gray-100 rounded-lg p-3 bg-gray-50/30"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Equipment Details Tab Panel -->
        <div id="facilitiesEquipmentView" class="bg-white rounded-xl shadow-lg p-6 hidden">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
              <i data-lucide="box" class="w-6 h-6 text-blue-500 mr-3"></i>
              Equipment Requests
            </h3>
            <span class="text-sm text-gray-500" id="equip-total"></span>
          </div>
          <div id="equip-cards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
          <div id="equip-empty" class="hidden text-center py-12 text-gray-500">
            <i data-lucide="inbox" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
            <h4 class="text-lg font-semibold mb-2">No equipment requests found</h4>
            <p>Submit an Equipment Request from the landing page to see it here.</p>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <!-- Full Image Preview Modal -->
  <div id="fullImageModal" class="modal">
    <div class="modal-box w-11/12 max-w-5xl bg-white text-gray-800 rounded-xl" data-theme="light" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-xl font-bold">Image Preview</h3>
        <button id="closeFullImageModal" class="btn btn-sm btn-circle btn-ghost"><i data-lucide="x" class="w-5 h-5"></i></button>
      </div>
      <div class="rounded-lg border border-gray-200" style="max-height:80vh; overflow:auto; background:#00000010; display:flex; align-items:center; justify-content:center;">
        <img id="fullImageEl" src="" alt="Preview" style="max-width:100%; max-height:80vh; width:auto; height:auto; object-fit:contain; display:block;">
      </div>
    </div>
  </div>
  
  <!-- Delete Confirmation Modal -->
  <div id="deleteConfirmModal" class="modal">
    <div class="modal-box w-11/12 max-w-md bg-white text-gray-800 rounded-xl" data-theme="light" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
          </div>
          <div>
            <h3 class="text-xl font-bold text-gray-800">Delete Facility</h3>
            <p class="text-sm text-gray-500">This action cannot be undone</p>
          </div>
        </div>
        <button id="closeDeleteModal" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <div class="mb-6">
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
              <i data-lucide="building" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800" id="deleteFacilityName">—</h4>
              <p class="text-sm text-gray-500" id="deleteFacilityLocation">—</p>
            </div>
          </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <span class="text-gray-500">Status:</span>
            <span class="font-medium" id="deleteFacilityStatus">—</span>
          </div>
          <div>
            <span class="text-gray-500">Reservations:</span>
            <span class="font-medium" id="deleteFacilityReservations">—</span>
          </div>
        </div>
        
        <div id="deleteWarningMessage" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg hidden">
          <div class="flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 text-red-600"></i>
            <p class="text-sm text-red-700 font-medium">This facility has active reservations or is currently occupied!</p>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-3">
        <button type="button" class="btn btn-outline btn-sm hover:btn-primary transition-all duration-300 shadow-sm hover:shadow-md" id="cancelDeleteBtn">
          <i data-lucide="x" class="w-4 h-4 mr-2"></i>
          Cancel
        </button>
        <button type="button" class="btn btn-error btn-sm hover:btn-error-focus transition-all duration-300 shadow-sm hover:shadow-md transform hover:scale-105" id="confirmDeleteBtn">
          <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
          <span id="deleteBtnText">Delete Facility</span>
        </button>
      </div>
    </div>
  </div>
  
  <!-- Reserve Facility Modal -->
  <div id="reserveFacilityModal" class="modal">
    <div class="modal-box w-11/12 max-w-3xl bg-white text-gray-800 rounded-xl" data-theme="light" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-2xl font-bold text-gray-800">Reserve a Facility</h3>
        <button id="closeReserveFacilityModal" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <form id="reserveFacilityForm" action="{{ route('facility_reservations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="space-y-6">
          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold flex items-center gap-2">
                <i data-lucide=\"building\" class="w-4 h-4 text-blue-500"></i>
                Facility *
              </span>
            </label>
            <select name="facility_id" id="rf_facility_id" class="select select-bordered w-full" required>
              <option value="">Select facility</option>
              @foreach(\App\Models\Facility::where('status','available')->get() as $fac)
                <option value="{{ $fac->id }}">{{ $fac->name }} ({{ $fac->location }})</option>
              @endforeach
            </select>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold flex items-center gap-2">
                  <i data-lucide=\"calendar\" class="w-4 h-4 text-blue-500"></i>
                  Start Time *
                </span>
              </label>
              <input type="datetime-local" name="start_time" class="input input-bordered w-full" required>
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold flex items-center gap-2">
                  <i data-lucide=\"clock\" class="w-4 h-4 text-blue-500"></i>
                  End Time *
                </span>
              </label>
              <input type="datetime-local" name="end_time" class="input input-bordered w-full" required>
            </div>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold flex items-center gap-2">
                <i data-lucide=\"file-text\" class="w-4 h-4 text-blue-500"></i>
                Purpose
              </span>
            </label>
            <textarea name="purpose" class="textarea textarea-bordered w-full h-24" placeholder="Enter purpose for reservation"></textarea>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold flex items-center gap-2">
                <i data-lucide=\"upload\" class="w-4 h-4 text-blue-500"></i>
                Supporting Document (Optional)
              </span>
            </label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
              <input type="file" name="document" class="hidden" id="rf_document" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
              <label for="rf_document" class="cursor-pointer">
                <i data-lucide="upload-cloud" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                <p class="text-gray-600">Click to upload or drag and drop</p>
                <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX, TXT, JPG, PNG (Max 10MB)</p>
              </label>
            </div>
          </div>

          <div class="alert alert-info">
            <i data-lucide="brain" class="w-5 h-5"></i>
            <div>
              <h3 class="font-bold">AI-Powered Processing</h3>
              <div class="text-sm">
                <p>• Your document will be automatically analyzed by AI for classification</p>
                <p>• The system will check facility availability automatically</p>
                <p>• You'll receive instant approval or notification for review</p>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button type="button" class="btn btn-outline btn-sm hover:btn-primary transition-all duration-300 shadow-sm hover:shadow-md" id="cancelReserveFacility">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm hover:btn-primary-focus transition-all duration-300 shadow-sm hover:shadow-md transform hover:scale-105">
              <i data-lucide="calendar-plus" class="w-4 h-4 mr-2"></i>
              Submit Reservation
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- View Facility Modal -->
  <div id="viewFacilityModal" class="modal">
    <div class="modal-box w-11/12 max-w-5xl bg-white text-gray-800 rounded-xl" data-theme="light" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
          <i data-lucide="building" class="w-6 h-6 text-blue-500"></i>
          <h3 class="text-2xl font-bold text-gray-800" id="vf_name">Facility Details</h3>
        </div>
        <div class="flex items-center gap-3">
          <div class="badge badge-lg" id="vf_status_badge">Available</div>
          <button id="closeViewFacilityModal" class="btn btn-sm btn-circle btn-ghost">
            <i data-lucide="x" class="w-5 h-5"></i>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
          <div class="card bg-white border border-gray-200">
            <div class="card-body">
              <div id="vf_location_wrap" class="mb-4 hidden">
                <label class="text-sm font-medium text-gray-500">Location</label>
                <div class="flex items-center gap-2 mt-1">
                  <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600"></i>
                  <span id="vf_location" class="text-gray-700"></span>
                </div>
              </div>

              <div id="vf_description_wrap" class="mb-4 hidden">
                <label class="text-sm font-medium text-gray-500">Description</label>
                <p id="vf_description" class="mt-1 text-gray-700"></p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t">
                <div>
                  <label class="text-sm font-medium text-gray-500">Total Reservations</label>
                  <p id="vf_reservations_count" class="font-semibold text-lg">0</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-500">Last Updated</label>
                  <p id="vf_updated_at" class="text-sm">—</p>
                </div>
              </div>


            </div>
          </div>
        </div>

        <div class="lg:col-span-1">
          <div class="card bg-white border border-gray-200 h-full">
            <div class="card-body">
              <h3 class="card-title text-lg mb-4 flex items-center">
                <i data-lucide="calendar-clock" class="w-5 h-5 mr-2 text-emerald-600"></i>
                Recent Reservations
              </h3>
              <div id="vf_recent_reservations" class="space-y-3"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Create Facility Modal -->
  <div id="createFacilityModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl bg-white text-gray-800" data-theme="light" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="building" class="w-6 h-6 text-blue-500"></i>
          Add New Facility
        </h3>
        <button id="closeCreateFacilityModal" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      @if($errors->any())
        <div class="alert alert-error mb-6">
          <i data-lucide="alert-circle" class="w-5 h-5"></i>
          <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form id="createFacilityForm" action="{{ route('facilities.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 gap-4">
          <div class="form-control">
            <label class="label"><span class="label-text font-semibold">Facility Name *</span></label>
            <input type="text" name="name" class="input input-bordered" placeholder="Enter facility name" required>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text font-semibold">Location</span></label>
            <input type="text" name="location" class="input input-bordered" placeholder="Enter facility location">
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text font-semibold">Description</span></label>
            <textarea name="description" class="textarea textarea-bordered" placeholder="Enter facility description"></textarea>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text font-semibold">Status *</span></label>
            <select name="status" class="select select-bordered" required>
              <option value="">Select status</option>
              <option value="available">Available</option>
              <option value="unavailable">Unavailable</option>
            </select>
          </div>
        </div>

        <div class="modal-action">
          <button type="button" class="btn btn-outline btn-sm hover:btn-primary transition-all duration-300 shadow-sm hover:shadow-md" id="cancelCreateFacility">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm hover:btn-primary-focus transition-all duration-300 shadow-sm hover:shadow-md transform hover:scale-105">
            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
            Create Facility
          </button>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Edit Facility Modal -->
  <div id="editFacilityModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl bg-white text-gray-800" data-theme="light" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="edit-3" class="w-6 h-6 text-blue-500"></i>
          Edit Facility
        </h3>
        <button id="closeEditFacilityModal" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <form id="editFacilityForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="label"><span class="label-text font-semibold">Facility Image</span></label>
            <div id="editImageWrap" class="img-edit-wrap rounded-xl overflow-hidden border border-gray-200" style="height: 180px; background:#f3f4f6; cursor:pointer;">
              <img id="edit_image_preview" src="" alt="Facility Image" style="width:100%; height:100%; object-fit:cover; display:none;">
              <div class="img-edit-overlay">
                <button type="button" id="btnEditImage" class="img-edit-btn"><i data-lucide="edit-3" class="w-4 h-4"></i><span>Edit</span></button>
                <button type="button" id="btnViewImage" class="img-edit-btn"><i data-lucide="maximize-2" class="w-4 h-4"></i><span>View</span></button>
                <button type="button" id="btnRemoveImage" class="img-edit-btn"><i data-lucide="eraser" class="w-4 h-4"></i><span>Remove BG</span></button>
                <button type="button" id="btnCloseOverlay" class="img-edit-btn"><i data-lucide="x" class="w-4 h-4"></i></button>
              </div>
            </div>
            <input type="file" id="edit_cover_image" name="cover_image" accept=".jpg,.jpeg,.png,.webp" class="hidden">
            <input type="hidden" id="edit_remove_image" name="remove_image" value="0">
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text font-semibold">Facility Name *</span></label>
            <input type="text" name="name" id="edit_name" class="input input-bordered" required>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text font-semibold">Location</span></label>
            <input type="text" name="location" id="edit_location" class="input input-bordered">
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text font-semibold">Description</span></label>
            <textarea name="description" id="edit_description" class="textarea textarea-bordered"></textarea>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text font-semibold">Status *</span></label>
            <select name="status" id="edit_status" class="select select-bordered" required>
              <option value="available">Available</option>
              <option value="unavailable">Unavailable</option>
            </select>
          </div>
        </div>

        <div class="modal-action">
          <button type="button" class="btn btn-outline btn-sm hover:btn-primary transition-all duration-300 shadow-sm hover:shadow-md" id="cancelEditFacility">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm hover:btn-primary-focus transition-all duration-300 shadow-sm hover:shadow-md transform hover:scale-105">
            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
            Update Facility
          </button>
        </div>
      </form>
    </div>
  </div>
  <script>
    // Real-time date and time
    function updateDateTime() {
      const now = new Date();
      const dateElement = document.getElementById('currentDate');
      const timeElement = document.getElementById('currentTime');
      
      const dateOptions = { weekday: 'short', month: 'short', day: 'numeric' };
      const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
      
      if (dateElement) dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
      if (timeElement) timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
    }

    // Global variables
    let currentViewMode = 'grid';
    let isCalendarView = false;
    let filteredFacilities = [];

    // Facilities tabs: directory | monitoring (scoped, minimal; non-breaking)
    function facilityShowTab(tabName) {
      console.log('Switching to tab:', tabName);
      const dirView = document.getElementById('facilitiesGridView');
      const monView = document.getElementById('facilitiesMonitoringView');
      const eqView = document.getElementById('facilitiesEquipmentView');

      if (!dirView || !monView) {
        console.error('Missing required elements for tab switching');
        return;
      }

      // Reset all navigation buttons
      const nav1 = document.getElementById('nav-directory');
      const nav2 = document.getElementById('nav-monitoring');
      const nav3 = document.getElementById('nav-equipment');
      
      [nav1, nav2, nav3].forEach(btn => {
        if (btn) {
          btn.classList.remove('text-blue-600', 'text-blue-800', 'font-semibold');
          btn.classList.add('text-gray-600');
        }
      });

      if (tabName === 'monitoring') {
        dirView.classList.add('hidden');
        monView.classList.remove('hidden');
        if (eqView) eqView.classList.add('hidden');
        
        // Update active navigation button
        if (nav2) {
          nav2.classList.remove('text-gray-600');
          nav2.classList.add('text-blue-600', 'font-semibold');
          // Reflect in URL
          try {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'monitoring');
            window.history.replaceState({}, '', url);
          } catch(e) {}
        }
        
        // Load monitoring data
        console.log('Loading monitoring data for tab switch');
        loadFacilitiesMonitoring();
      } else if (tabName === 'equipment') {
        if (dirView) dirView.classList.add('hidden');
        if (monView) monView.classList.add('hidden');
        if (eqView) eqView.classList.remove('hidden');
        
        // Update active navigation button
        if (nav3) {
          nav3.classList.remove('text-gray-600');
          nav3.classList.add('text-blue-600', 'font-semibold');
          // Reflect in URL
          try {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'equipment');
            window.history.replaceState({}, '', url);
          } catch(e) {}
        }
        
        // Lazy load equipment
        loadEquipmentDetails();
      } else {
        // default to directory
        monView.classList.add('hidden');
        dirView.classList.remove('hidden');
        if (eqView) eqView.classList.add('hidden');
        
        // Update active navigation button
        if (nav1) {
          nav1.classList.remove('text-gray-600');
          nav1.classList.add('text-blue-800', 'font-semibold');
          // Reflect in URL
          try {
            const url = new URL(window.location.href);
            url.searchParams.delete('tab');
            window.history.replaceState({}, '', url);
          } catch(e) {}
        }
      }
      
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }

      // Load monitoring data whenever tab is opened
      if (tabName === 'monitoring') {
        loadFacilitiesMonitoring();
      }
      if (tabName === 'equipment') {
        loadEquipmentDetails();
      }
    }





    // Toggle calendar view
    function toggleCalendarView() {
      const calendarView = document.getElementById('calendarView');
      const gridView = document.getElementById('facilitiesGridView');
      const toggleText = document.getElementById('calendarToggleText');
      
      isCalendarView = !isCalendarView;
      
      if (isCalendarView) {
        calendarView.classList.remove('hidden');
        gridView.classList.add('hidden');
        toggleText.textContent = 'Calendar View';
        loadFacilityCalendar();
      } else {
        calendarView.classList.add('hidden');
        gridView.classList.remove('hidden');
        toggleText.textContent = 'Calendar View';
      }
    }

    // Toggle view mode (grid/list)
    function toggleViewMode() {
      const toggleBtn = document.getElementById('viewToggleBtn');
      const grid = document.querySelector('#facilitiesGridView .grid');
      const facilityCards = document.querySelectorAll('.facility-card');
      
      // Toggle between grid and list
      currentViewMode = currentViewMode === 'grid' ? 'list' : 'grid';
      
      if (currentViewMode === 'grid') {
        // Switch to grid view
        grid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
        
        // Update button state
        toggleBtn.innerHTML = '<i data-lucide="list" class="w-4 h-4" style="display: inline-block;"></i><span class="fallback-icon" style="display: none;">☰</span>';
        toggleBtn.setAttribute('title', 'Switch to list view');
        toggleBtn.setAttribute('aria-label', 'Switch to list view');
        toggleBtn.setAttribute('aria-pressed', 'false');
        
        // Remove list-view class from all cards
        facilityCards.forEach(card => {
          card.classList.remove('list-view');
        });
      } else {
        // Switch to list view
        grid.className = 'grid grid-cols-1 gap-3';
        
        // Update button state
        toggleBtn.innerHTML = '<i data-lucide="grid-3x3" class="w-4 h-4" style="display: inline-block;"></i><span class="fallback-icon" style="display: none;">⊞</span>';
        toggleBtn.setAttribute('title', 'Switch to grid view');
        toggleBtn.setAttribute('aria-label', 'Switch to grid view');
        toggleBtn.setAttribute('aria-pressed', 'true');
        
        // Add list-view class to all cards
        facilityCards.forEach(card => {
          card.classList.add('list-view');
        });
      }
      
      // Recreate icons for the new button content
      lucide.createIcons();
      
      // Add fallback icon handling
      setTimeout(() => {
        const icon = toggleBtn.querySelector('i[data-lucide]');
        const fallback = toggleBtn.querySelector('.fallback-icon');
        if (icon && !icon.querySelector('svg')) {
          // Lucide icon failed to load, show fallback
          if (fallback) {
            icon.style.display = 'none';
            fallback.style.display = 'inline-block';
          }
        }
      }, 100);
      
      // Save preference to localStorage
      localStorage.setItem('facilityView', currentViewMode);
    }

    // Load facility calendar
    function loadFacilityCalendar() {
      const calendarContainer = document.getElementById('facilityCalendar');
      const selectedFacility = document.getElementById('calendarFacilityFilter')?.value || '';
      
      // Show loading state
      calendarContainer.innerHTML = `
        <div class="text-center py-12">
          <div class="loading loading-spinner loading-lg"></div>
          <p class="mt-4 text-gray-600">Loading calendar...</p>
        </div>
      `;
      
      // Simulate calendar loading (replace with actual calendar implementation)
      setTimeout(() => {
        calendarContainer.innerHTML = `
          <div class="bg-gray-50 rounded-lg p-8 text-center">
            <i data-lucide="calendar-days" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">Calendar View</h3>
            <p class="text-gray-500 text-sm mb-4">Calendar functionality will be implemented here</p>
            <p class="text-xs text-gray-400">Selected Facility: ${selectedFacility || 'All Facilities'}</p>
          </div>
        `;
        lucide.createIcons();
      }, 1000);
    }

    // Export facilities data
    function exportFacilities() {
      // Create CSV data
      const facilities = Array.from(document.querySelectorAll('[id^="facility-card-"]:not([style*="display: none"])'))
        .map(card => {
          const data = getFacilityData(card);
          return {
            name: data.name,
            location: data.location,
            status: data.status,
            capacity: data.capacity
          };
        });

      const csvContent = [
        ['Name', 'Location', 'Status', 'Capacity'],
        ...facilities.map(f => [f.name, f.location, f.status, f.capacity])
      ].map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');

      // Download CSV
      const blob = new Blob([csvContent], { type: 'text/csv' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `facilities-export-${new Date().toISOString().split('T')[0]}.csv`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
      
      showToast('Facilities data exported successfully!', 'success');
    }

    // Export monitoring data as PDF
    function exportMonitoringPdf() {
      // Create a form to submit the request
      const form = document.createElement('form');
      form.method = 'GET';
      form.action = '{{ route("facilities.monitoring.export_pdf") }}';
      form.target = '_blank';
      
      // Add any necessary parameters
      const params = new URLSearchParams();
      params.append('_token', '{{ csrf_token() }}');
      
      // Add form data to the form
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = '_token';
      input.value = '{{ csrf_token() }}';
      form.appendChild(input);
      
      // Append form to body and submit
      document.body.appendChild(form);
      form.submit();
      document.body.removeChild(form);
      
      showToast('Monitoring report is being generated...', 'info');
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      updateDateTime();
      
      // Initialize view mode from localStorage or default to grid
      const savedView = localStorage.getItem('facilityView') || 'grid';
      currentViewMode = savedView;

      // Preload equipment details so cards are ready when tab opens
      try { if (typeof loadEquipmentDetails === 'function') loadEquipmentDetails(); } catch (e) {}

      // Initialize facilities tabs default to directory
      if (typeof facilityShowTab === 'function') {
        facilityShowTab('directory');
      }
      
      // Monitoring: fetch and render
      async function loadFacilitiesMonitoring() {
        try {
          const res = await fetch('{{ route('facilities.monitoring.summary') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
          const data = await res.json();
          if (!data.success) throw new Error('Failed to load monitoring data');

          // Stat cards
          document.getElementById('mon-total').textContent = data.summary.total ?? 0;
          document.getElementById('mon-inprogress').textContent = data.summary.in_progress ?? 0;
          document.getElementById('mon-completed').textContent = data.summary.completed ?? 0;
          document.getElementById('mon-urgent').textContent = data.summary.urgent ?? 0;

          // Weekly chart
          const chartCanvas = document.getElementById('mon-weekly-chart');
          if (chartCanvas) {
            const ctx = chartCanvas.getContext('2d');
            const labels = (data.summary.weekly || []).map(p => p.day);
            const values = (data.summary.weekly || []).map(p => p.count);
            console.log('Chart data:', { labels, values });
            try {
              new Chart(ctx, {
                type: 'bar',
                data: {
                  labels,
                  datasets: [{
                    label: 'Requests',
                    data: values,
                    backgroundColor: ['#60a5fa','#34d399','#fbbf24','#60a5fa','#34d399','#fbbf24','#60a5fa'],
                    borderRadius: 6,
                  }]
                },
                options: {
                  responsive: true,
                  plugins: { legend: { display: false } },
                  scales: { y: { beginAtZero: true, ticks: { precision:0 } } }
                }
              });
            } catch (chartError) {
              console.error('Chart creation error:', chartError);
            }
          } else {
            console.error('Chart canvas not found');
          }

          // Recent list
          const recentWrap = document.getElementById('mon-recent');
          if (recentWrap) {
            recentWrap.innerHTML = '';
            (data.recent || []).forEach(r => {
              const priColor = r.priority === 'urgent' ? 'bg-red-100 text-red-700' : (r.priority === 'high' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700');
              const statusColor = r.status === 'completed' ? 'bg-emerald-100 text-emerald-700' : (r.status === 'pending' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700');
              const div = document.createElement('div');
              div.className = 'border rounded-lg p-3 hover:shadow-sm transition flex items-start justify-between';
              
              // Add Out button for approved requests
              const outButton = r.status === 'approved' ? 
                `<button onclick="window.completeRequest(${r.id})" class="btn btn-sm btn-outline btn-success ml-2" title="Mark as Completed" style="cursor: pointer;">
                  <i data-lucide="log-out" class="w-3 h-3 mr-1"></i>Out
                </button>` : '';
              
              div.innerHTML = `
                <div>
                  <div class="text-xs text-gray-500 mb-1">${r.code} <span class="ml-2 inline-block px-2 py-0.5 rounded ${statusColor}">${(r.status||'').charAt(0).toUpperCase() + (r.status||'').slice(1)}</span></div>
                  <div class="font-medium text-gray-800">${r.title || ''}</div>
                  <div class="text-xs text-gray-500 mt-1">${(r.facility ? r.facility + ' • ' : '')}${r.department || ''}</div>
                </div>
                <div class="flex items-center gap-2">
                  <span class="inline-block px-2 py-0.5 rounded ${priColor}">${(r.priority||'').charAt(0).toUpperCase() + (r.priority||'').slice(1)}</span>
                  <span class="text-xs text-gray-400">${r.created_ago || ''}</span>
                  ${outButton}
                </div>`;
              recentWrap.appendChild(div);
            });
          }

          if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();
        } catch (e) {
          console.error('Error loading monitoring data:', e);
          // Show error in the UI
          document.getElementById('mon-total').textContent = 'Error';
          document.getElementById('mon-inprogress').textContent = 'Error';
          document.getElementById('mon-completed').textContent = 'Error';
          document.getElementById('mon-urgent').textContent = 'Error';
        }
      }

      // Notification function
      function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
          type === 'success' ? 'bg-green-500 text-white' : 
          type === 'error' ? 'bg-red-500 text-white' : 
          'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
          <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-3"></i>
            <span>${message}</span>
          </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
          notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
          notification.classList.add('translate-x-full');
          setTimeout(() => {
            document.body.removeChild(notification);
          }, 300);
        }, 5000);
      }

      // Complete request function (mark as completed and free facility)
      window.completeRequest = async function(requestId) {
        if (!confirm('Are you sure you want to mark this request as completed? This will free up the facility.')) {
          return;
        }

        try {
          const response = await fetch(`/facility_reservations/${requestId}/complete`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          const data = await response.json();

          if (data.success) {
            showNotification('Request completed successfully! Email notification sent.', 'success');
            // Reload monitoring data to update the display
            loadFacilitiesMonitoring();
            // Refresh facility dropdown if on landing page
            if (typeof refreshFacilityDropdown === 'function') {
              refreshFacilityDropdown();
            }
          } else {
            showNotification(data.message || 'Failed to complete request', 'error');
          }
        } catch (error) {
          console.error('Error completing request:', error);
          showNotification('Error completing request', 'error');
        }
      }

      // Pre-load monitoring data so it's ready when tab is clicked
      loadFacilitiesMonitoring();


      // Equipment details fetch -> render as cards
      async function loadEquipmentDetails(){
        try{
          const res = await fetch('{{ route('facilities.equipment.details') }}',{ headers: { 'X-Requested-With':'XMLHttpRequest' }});
          const data = await res.json();
          if(!data.success) return;
          const wrap = document.getElementById('equip-cards');
          if (!wrap) return;
          wrap.innerHTML = '';
          const items = data.items || [];
          const empty = document.getElementById('equip-empty');
          let renderItems = items;
          if (renderItems.length === 0) {
            wrap.classList.remove('hidden');
            if (empty) empty.classList.add('hidden');
            renderItems = [
              { equipment_item: 'Vacuum cleaners (upright, canister, robotic)', equipment_quantity: null, id: 0, department: 'Housekeeping', priority: 'low', status: 'catalog', requested_datetime: null, location: '', contact_name: '', contact_email: '' },
              { equipment_item: 'Floor polishing/buffing machines', equipment_quantity: null, id: 0, department: 'Housekeeping', priority: 'low', status: 'catalog', requested_datetime: null, location: '', contact_name: '', contact_email: '' },
              { equipment_item: 'Laundry equipment (washing machines, dryers, steam irons)', equipment_quantity: null, id: 0, department: 'Housekeeping', priority: 'low', status: 'catalog', requested_datetime: null, location: '', contact_name: '', contact_email: '' },
              { equipment_item: 'Cleaning carts / trolleys', equipment_quantity: null, id: 0, department: 'Housekeeping', priority: 'low', status: 'catalog', requested_datetime: null, location: '', contact_name: '', contact_email: '' },
              { equipment_item: 'Linen storage racks', equipment_quantity: null, id: 0, department: 'Housekeeping', priority: 'low', status: 'catalog', requested_datetime: null, location: '', contact_name: '', contact_email: '' },
              { equipment_item: 'Disinfecting sprayers', equipment_quantity: null, id: 0, department: 'Housekeeping', priority: 'low', status: 'catalog', requested_datetime: null, location: '', contact_name: '', contact_email: '' },
              { equipment_item: 'Housekeeping radios / communication devices', equipment_quantity: null, id: 0, department: 'Housekeeping', priority: 'low', status: 'catalog', requested_datetime: null, location: '', contact_name: '', contact_email: '' },
            ];
          } else {
            wrap.classList.remove('hidden');
            if (empty) empty.classList.add('hidden');
          }
          renderItems.forEach(it => {
            const statusBadge = it.status === 'approved' ? 'badge-success' : (it.status==='pending'?'badge-warning':'badge-neutral');
            const priBadge = it.priority === 'urgent' || it.priority==='high' ? 'badge-error' : (it.priority==='medium' ? 'badge-warning' : 'badge-success');
            const card = document.createElement('div');
            card.className = 'facility-card hover:shadow-lg transition-all duration-300 hover:scale-105';
            card.innerHTML = `
              <div class="facility-card-body">
                <div class="flex justify-between items-start mb-2">
                  <h3 class="facility-card-title">${it.equipment_item || 'Equipment'}</h3>
                  <div class="flex items-center gap-2">
                    <span class="badge ${statusBadge}">${it.status==='catalog' ? 'Catalog' : ((it.status||'').charAt(0).toUpperCase() + (it.status||'').slice(1))}</span>
                    <span class="badge ${priBadge}">${(it.priority||'').charAt(0).toUpperCase() + (it.priority||'').slice(1)}</span>
                  </div>
                </div>
                <div class="mb-3 space-y-2">
                  <div class="meta-row">
                    ${it.id ? `<div class=\"meta-item\"><i data-lucide=\"hash\" class=\"w-4 h-4\"></i><span>#${String(it.id).padStart(6,'0')}</span></div>` : ''}
                    <div class="meta-item"><i data-lucide="users" class="w-4 h-4"></i><span>${it.department||''}</span></div>
                    <div class="meta-item"><i data-lucide="box" class="w-4 h-4"></i><span>Qty: ${it.equipment_quantity ?? '—'}</span></div>
                  </div>
                  <div class="meta-row">
                    <div class="meta-item"><i data-lucide="calendar" class="w-4 h-4"></i><span>${it.requested_datetime ? new Date(it.requested_datetime).toLocaleString() : '—'}</span></div>
                    <div class="meta-item"><i data-lucide="map-pin" class="w-4 h-4"></i><span>${it.location||''}</span></div>
                  </div>
                  <div class="meta-row">
                    <div class="meta-item"><i data-lucide="user" class="w-4 h-4"></i><span>${it.contact_name||''}</span></div>
                    <div class="meta-item"><i data-lucide="mail" class="w-4 h-4"></i><span>${it.contact_email||''}</span></div>
                  </div>
                </div>
              </div>`;
            wrap.appendChild(card);
          });
          const totalEl = document.getElementById('equip-total');
          if (totalEl) totalEl.textContent = `${items.length || renderItems.length} items`;
          if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();
        }catch(e){ console.error(e); }
      }
      
      // Set up toggle button event listeners
      const toggleBtn = document.getElementById('viewToggleBtn');
      if (toggleBtn) {
        // Click handler
        toggleBtn.addEventListener('click', toggleViewMode);
        
        // Keyboard handler (Enter/Space)
        toggleBtn.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggleViewMode();
          }
        });
        
        // Initialize button state based on saved preference
        if (currentViewMode === 'list') {
          toggleBtn.innerHTML = '<i data-lucide="grid-3x3" class="w-4 h-4" style="display: inline-block;"></i><span class="fallback-icon" style="display: none;">⊞</span>';
          toggleBtn.setAttribute('title', 'Switch to grid view');
          toggleBtn.setAttribute('aria-label', 'Switch to grid view');
          toggleBtn.setAttribute('aria-pressed', 'true');
        } else {
          toggleBtn.innerHTML = '<i data-lucide="list" class="w-4 h-4" style="display: inline-block;"></i><span class="fallback-icon" style="display: none;">☰</span>';
          toggleBtn.setAttribute('title', 'Switch to list view');
          toggleBtn.setAttribute('aria-label', 'Switch to list view');
          toggleBtn.setAttribute('aria-pressed', 'false');
        }
        
        // Recreate icons after updating innerHTML
        lucide.createIcons();
        
        // Ensure button is visible and has fallback
        toggleBtn.style.display = 'inline-flex';
        toggleBtn.style.visibility = 'visible';
        toggleBtn.style.opacity = '1';
        
        // Add fallback icon handling
        setTimeout(() => {
          const icon = toggleBtn.querySelector('i[data-lucide]');
          const fallback = toggleBtn.querySelector('.fallback-icon');
          if (icon && !icon.querySelector('svg')) {
            // Lucide icon failed to load, show fallback
            if (fallback) {
              icon.style.display = 'none';
              fallback.style.display = 'inline-block';
            }
          }
        }, 100);
        
        // Apply the saved view mode
        const grid = document.querySelector('#facilitiesGridView .grid');
        const facilityCards = document.querySelectorAll('.facility-card');
        
        if (currentViewMode === 'list') {
          grid.className = 'grid grid-cols-1 gap-3';
          facilityCards.forEach(card => {
            card.classList.add('list-view');
          });
        } else {
          grid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
          facilityCards.forEach(card => {
            card.classList.remove('list-view');
          });
        }
      }
      
      
      // Update time every second
      setInterval(updateDateTime, 1000);

      // Modal handlers
      const modal = document.getElementById('createFacilityModal');
      const openBtn = document.getElementById('openCreateFacilityModal');
      const closeBtn = document.getElementById('closeCreateFacilityModal');
      const cancelBtn = document.getElementById('cancelCreateFacility');

      function openModal() {
        modal.classList.add('modal-open');
      }
      function closeModal() {
        modal.classList.remove('modal-open');
      }

      if (openBtn) openBtn.addEventListener('click', openModal);
      if (closeBtn) closeBtn.addEventListener('click', closeModal);
      if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
      if (modal) modal.addEventListener('click', function(e){ if(e.target === modal) closeModal(); });

      // Edit modal handlers
      const editModal = document.getElementById('editFacilityModal');
      const closeEditBtn = document.getElementById('closeEditFacilityModal');
      const cancelEditBtn = document.getElementById('cancelEditFacility');
      const editForm = document.getElementById('editFacilityForm');

      function openEditModal() { editModal.classList.add('modal-open'); }
      function closeEditModal() { editModal.classList.remove('modal-open'); }

      document.querySelectorAll('.openEditFacilityBtn').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-id');
          const name = btn.getAttribute('data-name') || '';
          const location = btn.getAttribute('data-location') || '';
          const description = btn.getAttribute('data-description') || '';
          const status = btn.getAttribute('data-status') || 'available';

          document.getElementById('edit_name').value = name;
          document.getElementById('edit_location').value = location;
          document.getElementById('edit_description').value = description;
          document.getElementById('edit_status').value = status;
          editForm.setAttribute('action', `{{ url('facilities') }}/${id}`);

          // Try to preload current image using facility-specific path only
          const candidates = [
            `{{ url('storage/facilities') }}/${id}/cover.jpg`,
            `{{ url('storage/facilities') }}/${id}/cover.png`,
            `{{ url('storage/facilities') }}/${id}/cover.jpeg`,
            `{{ url('storage/facilities') }}/${id}/cover.webp`,
          ];
          const imgEl = document.getElementById('edit_image_preview');
          const removeInput = document.getElementById('edit_remove_image');
          removeInput.value = '0';
          // Probe images
          (async () => {
            let found = '';
            for (const url of candidates) {
              try {
                const head = await fetch(url, { method: 'HEAD' });
                if (head.ok) { found = url; break; }
              } catch(e) {}
            }
            if (found) {
              imgEl.src = found;
              imgEl.style.display = 'block';
            } else {
              imgEl.removeAttribute('src');
              imgEl.style.display = 'none';
            }
          })();

          openEditModal();
        });
      });

      if (closeEditBtn) closeEditBtn.addEventListener('click', closeEditModal);
      if (cancelEditBtn) cancelEditBtn.addEventListener('click', closeEditModal);
      if (editModal) editModal.addEventListener('click', function(e){ if(e.target === editModal) closeEditModal(); });

      // Image overlay controls
      const fileInput = document.getElementById('edit_cover_image');
      const imgPreview = document.getElementById('edit_image_preview');
      const removeHidden = document.getElementById('edit_remove_image');
      const btnEditImage = document.getElementById('btnEditImage');
      const btnViewImage = document.getElementById('btnViewImage');
      const btnRemoveImage = document.getElementById('btnRemoveImage');
      const btnCloseOverlay = document.getElementById('btnCloseOverlay');
      const fullImageModal = document.getElementById('fullImageModal');
      const fullImageEl = document.getElementById('fullImageEl');
      const closeFullImageModal = document.getElementById('closeFullImageModal');

      if (btnEditImage) btnEditImage.addEventListener('click', () => fileInput && fileInput.click());
      if (btnRemoveImage) btnRemoveImage.addEventListener('click', () => {
        removeHidden.value = '1';
        imgPreview.style.display = 'none';
        fileInput.value = '';
      });
      if (btnCloseOverlay) btnCloseOverlay.addEventListener('click', (e) => {
        // Just closes overlay by forcing mouseout via blur; overlay hides on hover
        e.stopPropagation();
        (document.activeElement && document.activeElement.blur && document.activeElement.blur());
      });
      let wasFromEdit = false;
      function openFullImage(src, fromEdit = false){
        if (!src) return;
        fullImageEl.src = src;
        fullImageModal.classList.add('modal-open');
        if (fromEdit) {
          wasFromEdit = true;
          editModal.classList.remove('modal-open');
        }
        lucide.createIcons();
      }
      if (btnViewImage) btnViewImage.addEventListener('click', () => openFullImage(imgPreview.src, true));
      const imgWrap = document.getElementById('editImageWrap');
      if (imgWrap) imgWrap.addEventListener('click', (e) => {
        // Allow clicking the image area to open viewer (but not when clicking edit/remove buttons)
        const t = e.target;
        const isButton = t.closest && t.closest('.img-edit-btn');
        if (!isButton && imgPreview && imgPreview.src) openFullImage(imgPreview.src, true);
      });
      function closeFullImage(){
        fullImageModal.classList.remove('modal-open');
        if (wasFromEdit) {
          editModal.classList.add('modal-open');
          wasFromEdit = false;
        }
      }
      if (closeFullImageModal) closeFullImageModal.addEventListener('click', closeFullImage);
      if (fullImageModal) fullImageModal.addEventListener('click', function(e){ if (e.target === fullImageModal) closeFullImage(); });
      if (fileInput) fileInput.addEventListener('change', () => {
        if (fileInput.files && fileInput.files[0]) {
          removeHidden.value = '0';
          const url = URL.createObjectURL(fileInput.files[0]);
          imgPreview.src = url;
          imgPreview.style.display = 'block';
        }
      });

      // View modal handlers
      const viewModal = document.getElementById('viewFacilityModal');
      const closeViewBtn = document.getElementById('closeViewFacilityModal');
      function openViewModal(){ viewModal.classList.add('modal-open'); }
      function closeViewModal(){ viewModal.classList.remove('modal-open'); }
      if (closeViewBtn) closeViewBtn.addEventListener('click', closeViewModal);
      if (viewModal) viewModal.addEventListener('click', function(e){ if(e.target === viewModal) closeViewModal(); });

      document.querySelectorAll('.openViewFacilityBtn').forEach(btn => {
        btn.addEventListener('click', async () => {
          const id = btn.getAttribute('data-id');
          try {
            const res = await fetch(`{{ url('/facilities') }}/${id}/ajax`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('Failed to load');
            const data = await res.json();
            if (!data.success) throw new Error('Invalid response');
            const f = data.facility;
            document.getElementById('vf_name').textContent = f.name || 'Facility Details';
            const badge = document.getElementById('vf_status_badge');
            badge.textContent = (f.status || 'available').charAt(0).toUpperCase() + (f.status || 'available').slice(1);
            badge.className = `badge badge-lg ${f.status === 'available' ? 'badge-success' : (f.status === 'occupied' ? 'badge-error' : 'badge-warning')}`;
            // location
            const locWrap = document.getElementById('vf_location_wrap');
            if (f.location) { locWrap.classList.remove('hidden'); document.getElementById('vf_location').textContent = f.location; } else { locWrap.classList.add('hidden'); }
            // desc
            const descWrap = document.getElementById('vf_description_wrap');
            if (f.description) { descWrap.classList.remove('hidden'); document.getElementById('vf_description').textContent = f.description; } else { descWrap.classList.add('hidden'); }
            document.getElementById('vf_reservations_count').textContent = f.reservations_count ?? 0;
            document.getElementById('vf_updated_at').textContent = f.updated_at || '—';

            // recent reservations
            const recentWrap = document.getElementById('vf_recent_reservations');
            recentWrap.innerHTML = '';
            if (Array.isArray(f.recent_reservations) && f.recent_reservations.length) {
              f.recent_reservations.forEach(r => {
                const color = r.status === 'approved' ? 'emerald' : (r.status === 'denied' ? 'red' : 'amber');
                const div = document.createElement('div');
                div.className = 'border-l-4 p-3 rounded-r-md';
                div.style.borderColor = `var(--color-modern-teal)`;
                div.innerHTML = `
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="font-semibold text-sm">${r.reserver}</p>
                      <p class="text-xs text-gray-500">${r.start_time} - ${r.end_time}</p>
                    </div>
                    <div class="badge badge-sm badge-outline">${(r.status||'').charAt(0).toUpperCase() + (r.status||'').slice(1)}</div>
                  </div>`;
                recentWrap.appendChild(div);
              });
            } else {
              const empty = document.createElement('div');
              empty.className = 'text-center py-6 text-gray-500 text-sm';
              empty.textContent = 'No recent reservations.';
              recentWrap.appendChild(empty);
            }

            lucide.createIcons();
            openViewModal();
          } catch(e) {
            console.error(e);
            alert('Failed to load facility details.');
          }
        });
      });

      // Reserve modal handlers
      const reserveModal = document.getElementById('reserveFacilityModal');
      const closeReserveBtn = document.getElementById('closeReserveFacilityModal');
      const cancelReserveBtn = document.getElementById('cancelReserveFacility');
      function openReserveModal(){ reserveModal.classList.add('modal-open'); }
      function closeReserveModal(){ reserveModal.classList.remove('modal-open'); }
      if (closeReserveBtn) closeReserveBtn.addEventListener('click', closeReserveModal);
      if (cancelReserveBtn) cancelReserveBtn.addEventListener('click', closeReserveModal);
      if (reserveModal) reserveModal.addEventListener('click', function(e){ if(e.target === reserveModal) closeReserveModal(); });

      document.querySelectorAll('.openReserveFacilityBtn').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-id');
          const name = btn.getAttribute('data-name') || '';
          const select = document.getElementById('rf_facility_id');
          if (select) {
            // preselect facility
            Array.from(select.options).forEach(o => { o.selected = (o.value === id); });
          }
          openReserveModal();
          lucide.createIcons();
        });
      });

      // Free facility functionality
      document.querySelectorAll('.freeFacilityBtn').forEach(btn => {
        btn.addEventListener('click', async () => {
          const id = btn.getAttribute('data-id');
          const name = btn.getAttribute('data-name') || 'this facility';
          
          if (confirm(`Are you sure you want to free up ${name}? This will make it available for new reservations.`)) {
            try {
              const response = await fetch(`/facilities/${id}/free`, {
                method: 'POST',
                headers: {
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                  'Accept': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
                }
              });
              
              const data = await response.json();
              
              if (data.success) {
                // Show success message
                showNotification(data.message, 'success');
                
                // Reload the page to update the facility status
                setTimeout(() => {
                  window.location.reload();
                }, 1000);
              } else {
                showNotification(data.message || 'Failed to free facility', 'error');
              }
            } catch (error) {
              console.error('Error freeing facility:', error);
              showNotification('Error freeing facility', 'error');
            }
          }
        });
      });

      // Delete facility functionality
      const deleteModal = document.getElementById('deleteConfirmModal');
      const closeDeleteBtn = document.getElementById('closeDeleteModal');
      const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
      const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
      
      function openDeleteModal() { deleteModal.classList.add('modal-open'); }
      function closeDeleteModal() { deleteModal.classList.remove('modal-open'); }
      
      if (closeDeleteBtn) closeDeleteBtn.addEventListener('click', closeDeleteModal);
      if (cancelDeleteBtn) cancelDeleteBtn.addEventListener('click', closeDeleteModal);
      if (deleteModal) deleteModal.addEventListener('click', function(e){ if(e.target === deleteModal) closeDeleteModal(); });

      document.querySelectorAll('.deleteFacilityBtn').forEach(btn => {
        btn.addEventListener('click', function() {
          const facilityId = this.getAttribute('data-id');
          const facilityName = this.getAttribute('data-name');
          const facilityLocation = this.getAttribute('data-location');
          const facilityStatus = this.getAttribute('data-status');
          const reservationsCount = parseInt(this.getAttribute('data-reservations')) || 0;
          const deleteUrl = this.getAttribute('data-url');
          
          // Validate required data
          if (!facilityId || !facilityName || !deleteUrl) {
            console.error('Missing required facility data:', { facilityId, facilityName, deleteUrl });
            showToast('Error: Missing facility information. Please try again.', 'error');
            return;
          }
          
          // Populate modal with facility data
          const nameEl = document.getElementById('deleteFacilityName');
          const locationEl = document.getElementById('deleteFacilityLocation');
          const statusEl = document.getElementById('deleteFacilityStatus');
          const reservationsEl = document.getElementById('deleteFacilityReservations');
          
          if (nameEl) nameEl.textContent = facilityName;
          if (locationEl) locationEl.textContent = facilityLocation || 'No location specified';
          if (statusEl) statusEl.textContent = facilityStatus ? facilityStatus.charAt(0).toUpperCase() + facilityStatus.slice(1) : 'Unknown';
          if (reservationsEl) reservationsEl.textContent = reservationsCount;
          
          // Show warning if facility has reservations or is occupied
          const warningMessage = document.getElementById('deleteWarningMessage');
          if (warningMessage) {
            if (facilityStatus === 'occupied' || reservationsCount > 0) {
              warningMessage.classList.remove('hidden');
            } else {
              warningMessage.classList.add('hidden');
            }
          }
          
          // Reset delete button state
          const deleteBtnText = document.getElementById('deleteBtnText');
          if (deleteBtnText) deleteBtnText.textContent = 'Delete Facility';
          
          if (confirmDeleteBtn) {
            confirmDeleteBtn.disabled = false;
            confirmDeleteBtn.innerHTML = `
              <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
              <span id="deleteBtnText">Delete Facility</span>
            `;
            
            // Store data for deletion
            confirmDeleteBtn.setAttribute('data-url', deleteUrl);
            confirmDeleteBtn.setAttribute('data-facility-id', facilityId);
            confirmDeleteBtn.setAttribute('data-facility-name', facilityName);
          }
          
          lucide.createIcons();
          openDeleteModal();
        });
      });

      // Handle delete confirmation
      if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', async function() {
          const deleteUrl = this.getAttribute('data-url');
          const facilityId = this.getAttribute('data-facility-id');
          const facilityName = this.getAttribute('data-facility-name');
          
          // Validate required attributes
          if (!deleteUrl || !facilityId || !facilityName) {
            console.error('Missing required attributes for deletion:', { deleteUrl, facilityId, facilityName });
            showToast('Error: Missing facility information. Please try again.', 'error');
            return;
          }
          
          const facilityCard = document.getElementById('facility-card-' + facilityId);
          
          if (!facilityCard) {
            console.error('Facility card not found for ID:', facilityId);
            showToast('Error: Facility card not found. Please refresh the page and try again.', 'error');
            return;
          }
          
          // Show loading state
          this.disabled = true;
          this.innerHTML = `
            <i class="loading loading-spinner loading-sm mr-2"></i>
            <span>Deleting...</span>
          `;
          
          try {
            const response = await fetch(deleteUrl, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              }
            });
            
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            if (data.success !== undefined && !data.success) {
              throw new Error(data.message || 'Delete failed');
            }
            
            // Success - close modal and animate card removal
            closeDeleteModal();
            
            // Show success toast
            showToast(`${facilityName} has been deleted successfully.`, 'success');
            
            // Animate card removal
            if (facilityCard) {
              facilityCard.style.transition = 'all 0.5s ease-out';
              facilityCard.style.transform = 'scale(0.8)';
              facilityCard.style.opacity = '0';
              facilityCard.style.margin = '0';
              facilityCard.style.padding = '0';
              facilityCard.style.height = '0';
              facilityCard.style.overflow = 'hidden';
              
              setTimeout(() => {
                facilityCard.remove();
                
                // Update stats cards
                updateFacilityStats();
                
                
                // Check if no facilities left
                const remainingCards = document.querySelectorAll('.facility-card');
                if (remainingCards.length === 0) {
                  showEmptyState();
                }
              }, 500);
            }
            
          } catch (error) {
            console.error('Delete error:', error);
            
            // Reset button state
            this.disabled = false;
            this.innerHTML = `
              <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
              <span id="deleteBtnText">Delete Facility</span>
            `;
            lucide.createIcons();
            
            // Show error toast with more user-friendly message
            const errorMessage = error.message.includes('getAttribute') 
              ? 'An unexpected error occurred. Please refresh the page and try again.'
              : error.message;
            showToast(`Failed to delete facility: ${errorMessage}`, 'error');
          }
        });
      }
      
      // Helper function to update facility stats
      function updateFacilityStats() {
        const totalFacilities = document.querySelectorAll('.facility-card').length;
        const availableFacilities = document.querySelectorAll('.facility-card .badge-success').length;
        const occupiedFacilities = document.querySelectorAll('.facility-card .badge-error').length;
        
        // Update facility count in header
        const facilityCountElement = document.getElementById('facilityCount');
        if (facilityCountElement) {
          facilityCountElement.textContent = totalFacilities;
        }
        
        // Update stats cards if they exist
        const totalCard = document.querySelector('.text-3xl.font-bold');
        if (totalCard) {
          totalCard.textContent = totalFacilities;
        }
      }
      
      // Helper function to show empty state
      function showEmptyState() {
        const grid = document.querySelector('#facilitiesGridView .grid');
        if (grid) {
          // Double-check that there are really no facilities
          const remainingCards = document.querySelectorAll('.facility-card');
          if (remainingCards.length === 0) {
          grid.innerHTML = `
            <div class="col-span-full text-center py-12">
              <i data-lucide="building" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
              <h3 class="text-lg font-semibold text-gray-600 mb-2">No Facilities Found</h3>
              <p class="text-gray-500 mb-6">Add your first facility to get started.</p>
              <button type="button" id="openCreateFacilityModal" class="btn btn-primary btn-md hover:btn-primary-focus transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Add Facility
              </button>
            </div>
          `;
          lucide.createIcons();
          }
        }
      }
      
      // Toast notification function
      function showToast(message, type = 'info', duration = 5000) {
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;
        
        // Set icon based on type
        let icon = 'info';
        if (type === 'success') icon = 'check-circle';
        if (type === 'error') icon = 'alert-circle';
        if (type === 'warning') icon = 'alert-triangle';
        
        toast.innerHTML = `
          <i data-lucide="${icon}" class="w-5 h-5"></i>
          <span>${message}</span>
          <button onclick="this.parentElement.remove()" class="btn btn-ghost btn-xs">
            <i data-lucide="x" class="w-4 h-4"></i>
          </button>
        `;
        
        toastContainer.appendChild(toast);
        lucide.createIcons();
        
        // Animate in
        setTimeout(() => {
          toast.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after duration
        setTimeout(() => {
          if (toast.parentNode) {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
              if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
              }
            }, 300);
          }
        }, duration);
      }
      
      // Create toast container if it doesn't exist
      function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'fixed bottom-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
        return container;
      }
    });
  </script>
</body>
</html> 