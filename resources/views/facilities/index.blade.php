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
        </div>

        <!-- Stats Cards (DaisyUI) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
          <!-- Total Facilities -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-primary">
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
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-success">
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
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-error">
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
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-info">
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
          <button type="button" id="openCreateFacilityModal" class="btn btn-primary btn-md hover:btn-primary-focus transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Add New Facility
          </button>
          <a href="{{ route('facility_reservations.index') }}" class="btn btn-outline btn-md hover:btn-primary transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
            <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
            View Reservations
          </a>
          <a href="{{ route('facility_reservations.calendar') }}" class="btn btn-outline btn-md hover:btn-primary transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
            <i data-lucide="calendar-days" class="w-4 h-4 mr-2"></i>
            <span>Calendar View</span>
          </a>
          <button onclick="exportFacilities()" class="btn btn-outline btn-md hover:btn-primary transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
            <i data-lucide="download" class="w-4 h-4 mr-2"></i>
            Export Data
          </button>
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

        <!-- Tab Navigation -->
        <div class="bg-gray-100 px-6 py-2 border-b border-gray-200 mb-6" style="background-color: var(--color-snow-mist); border-color: var(--color-snow-mist);">
          <div class="flex space-x-1">
            <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-blue-100 rounded-t-lg border-b-2 border-blue-500" onclick="showFacilityTab('directory')" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%); color: var(--color-charcoal-ink); border-color: var(--color-regal-navy);">
              <i data-lucide="building" class="w-4 h-4 mr-1"></i>Facility Directory
            </button>
            <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-t-lg" onclick="showFacilityTab('monitoring')" style="color: var(--color-charcoal-ink); hover:background-color: var(--color-snow-mist);">
              <i data-lucide="activity" class="w-4 h-4 mr-1"></i>Monitoring
            </button>
          </div>
        </div>

        <!-- Facility Directory Tab -->
        <div id="facility-directory-tab" class="bg-white rounded-xl shadow-lg p-6">
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

        <!-- Monitoring Tab -->
        <div id="facility-monitoring-tab" class="bg-white rounded-xl shadow-lg p-6 hidden">
          <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1" style="color: var(--color-charcoal-ink);">Facility Monitoring</h1>
            <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Real-time monitoring of facility status, usage analytics, and performance metrics</p>
          </div>

          <!-- Real-Time Dashboard Stats -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Live Status Overview -->
            <div class="stats-card bg-gradient-to-br from-green-400 via-green-500 to-emerald-600 text-white transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-2xl rounded-2xl overflow-hidden relative">
              <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
              <div class="relative p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                      <i data-lucide="activity" class="w-7 h-7 text-white animate-pulse"></i>
                    </div>
                    <div>
                      <h3 class="text-sm font-semibold text-white/90 uppercase tracking-wide">Live Status</h3>
                      <p class="text-xs text-white/70">Active Facilities</p>
                    </div>
                  </div>
                  <div class="w-3 h-3 bg-green-300 rounded-full animate-ping"></div>
                </div>
                <div class="flex items-end justify-between">
                  <p class="text-4xl font-bold text-white" id="liveStatusCount">-</p>
                  <div class="text-right">
                    <p class="text-xs text-white/70">Real-time</p>
                    <div class="w-16 h-1 bg-white/30 rounded-full mt-1">
                      <div class="w-full h-full bg-white rounded-full animate-pulse"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Current Reservations -->
            <div class="stats-card bg-gradient-to-br from-blue-400 via-blue-500 to-cyan-600 text-white transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-2xl rounded-2xl overflow-hidden relative">
              <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
              <div class="relative p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                      <i data-lucide="calendar" class="w-7 h-7 text-white animate-bounce"></i>
                    </div>
                    <div>
                      <h3 class="text-sm font-semibold text-white/90 uppercase tracking-wide">Active Reservations</h3>
                      <p class="text-xs text-white/70">In Progress</p>
                    </div>
                  </div>
                  <div class="w-3 h-3 bg-blue-300 rounded-full animate-ping"></div>
                </div>
                <div class="flex items-end justify-between">
                  <p class="text-4xl font-bold text-white" id="activeReservationsCount">-</p>
                  <div class="text-right">
                    <p class="text-xs text-white/70">Today</p>
                    <div class="w-16 h-1 bg-white/30 rounded-full mt-1">
                      <div class="w-3/4 h-full bg-white rounded-full animate-pulse"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Utilization Rate -->
            <div class="stats-card bg-gradient-to-br from-purple-400 via-purple-500 to-violet-600 text-white transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-2xl rounded-2xl overflow-hidden relative">
              <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
              <div class="relative p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                      <i data-lucide="trending-up" class="w-7 h-7 text-white animate-pulse"></i>
                    </div>
                    <div>
                      <h3 class="text-sm font-semibold text-white/90 uppercase tracking-wide">Utilization</h3>
                      <p class="text-xs text-white/70">Average Usage</p>
                    </div>
                  </div>
                  <div class="w-3 h-3 bg-purple-300 rounded-full animate-ping"></div>
                </div>
                <div class="flex items-end justify-between">
                  <p class="text-4xl font-bold text-white" id="utilizationRate">-</p>
                  <div class="text-right">
                    <p class="text-xs text-white/70">Efficiency</p>
                    <div class="w-16 h-1 bg-white/30 rounded-full mt-1">
                      <div class="w-4/5 h-full bg-white rounded-full animate-pulse"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Revenue Today -->
            <div class="stats-card bg-gradient-to-br from-orange-400 via-orange-500 to-amber-600 text-white transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-2xl rounded-2xl overflow-hidden relative">
              <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
              <div class="relative p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                      <i data-lucide="dollar-sign" class="w-7 h-7 text-white animate-bounce"></i>
                    </div>
                    <div>
                      <h3 class="text-sm font-semibold text-white/90 uppercase tracking-wide">Revenue Today</h3>
                      <p class="text-xs text-white/70">Total Earnings</p>
                    </div>
                  </div>
                  <div class="w-3 h-3 bg-orange-300 rounded-full animate-ping"></div>
                </div>
                <div class="flex items-end justify-between">
                  <p class="text-4xl font-bold text-white" id="revenueToday">-</p>
                  <div class="text-right">
                    <p class="text-xs text-white/70">Profit</p>
                    <div class="w-16 h-1 bg-white/30 rounded-full mt-1">
                      <div class="w-full h-full bg-white rounded-full animate-pulse"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Monitoring Content -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Facility Status & Alerts -->
            <div class="lg:col-span-2 space-y-6">
              <!-- Facility Status Grid -->
              <div class="card bg-white border border-gray-200">
                <div class="card-header p-4 border-b border-gray-200">
                  <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i data-lucide="building" class="w-5 h-5 text-blue-500 mr-2"></i>
                    Facility Status Overview
                  </h3>
                </div>
                <div class="card-body p-4">
                  <div id="facility-status-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Facility status cards will be loaded here -->
                  </div>
                </div>
              </div>

              <!-- Usage Analytics Chart -->
              <div class="card bg-white border border-gray-200">
                <div class="card-header p-4 border-b border-gray-200">
                  <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i data-lucide="bar-chart" class="w-5 h-5 text-green-500 mr-2"></i>
                    Usage Analytics
                  </h3>
                </div>
                <div class="card-body p-4">
                  <div id="usage-chart" class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                    <div class="text-center">
                      <i data-lucide="bar-chart" class="w-12 h-12 text-gray-400 mx-auto mb-2"></i>
                      <p class="text-gray-500">Usage analytics chart will be displayed here</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column: Activity Timeline & Quick Actions -->
            <div class="space-y-6">
              <!-- Activity Timeline -->
              <div class="card bg-white border border-gray-200">
                <div class="card-header p-4 border-b border-gray-200">
                  <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i data-lucide="clock" class="w-5 h-5 text-purple-500 mr-2"></i>
                    Activity Timeline
                  </h3>
                </div>
                <div class="card-body p-4">
                  <div id="activity-timeline" class="space-y-3 max-h-64 overflow-y-auto">
                    <!-- Activity items will be loaded here -->
                  </div>
                </div>
              </div>

              <!-- Quick Actions -->
              <div class="card bg-white border border-gray-200">
                <div class="card-header p-4 border-b border-gray-200">
                  <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i data-lucide="zap" class="w-5 h-5 text-yellow-500 mr-2"></i>
                    Quick Actions
                  </h3>
                </div>
                <div class="card-body p-4">
                  <div class="space-y-2">
                    <button onclick="refreshMonitoringData()" class="btn btn-outline btn-sm w-full justify-start">
                      <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                      Refresh Data
                    </button>
                    <button onclick="exportMonitoringReport()" class="btn btn-outline btn-sm w-full justify-start">
                      <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                      Export Report
                    </button>
                    <button onclick="openFacilityMap()" class="btn btn-outline btn-sm w-full justify-start">
                      <i data-lucide="map" class="w-4 h-4 mr-2"></i>
                      View Map
                    </button>
                    <button onclick="openMaintenanceSchedule()" class="btn btn-outline btn-sm w-full justify-start">
                      <i data-lucide="wrench" class="w-4 h-4 mr-2"></i>
                      Maintenance
                    </button>
                  </div>
                  
                  <!-- Demo Functions -->
                  <div class="border-t border-gray-200 pt-3 mt-4">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Demo Functions</h4>
                    <div class="grid grid-cols-2 gap-2">
                      <button onclick="addTestActivity()" class="btn btn-sm bg-indigo-500 hover:bg-indigo-600 text-white border-0">
                        <i data-lucide="plus" class="w-3 h-3 mr-1"></i>Add Activity
                      </button>
                      <button onclick="addTestAlert()" class="btn btn-sm bg-red-500 hover:bg-red-600 text-white border-0">
                        <i data-lucide="bell" class="w-3 h-3 mr-1"></i>Add Alert
                      </button>
                      <button onclick="markAllAlertsRead()" class="btn btn-sm bg-green-500 hover:bg-green-600 text-white border-0">
                        <i data-lucide="check" class="w-3 h-3 mr-1"></i>Mark Read
                      </button>
                      <button onclick="clearAllAlerts()" class="btn btn-sm bg-gray-500 hover:bg-gray-600 text-white border-0">
                        <i data-lucide="trash" class="w-3 h-3 mr-1"></i>Clear All
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Alerts & Notifications -->
              <div class="card bg-white border border-gray-200">
                <div class="card-header p-4 border-b border-gray-200">
                  <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i data-lucide="bell" class="w-5 h-5 text-red-500 mr-2"></i>
                    Alerts & Notifications
                  </h3>
                </div>
                <div class="card-body p-4">
                  <div id="alerts-container" class="space-y-2">
                    <!-- Alerts will be loaded here -->
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
    // Global variables
    let currentFacilityTab = 'directory';

    // Tab functionality
    function showFacilityTab(tabName) {
      currentFacilityTab = tabName;
      
      // Hide all tabs
      document.getElementById('facility-directory-tab').classList.add('hidden');
      document.getElementById('facility-monitoring-tab').classList.add('hidden');
      
      // Show selected tab
      document.getElementById('facility-' + tabName + '-tab').classList.remove('hidden');
      
      // Update tab buttons
      const tabs = document.querySelectorAll('[onclick^="showFacilityTab"]');
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
      if (tabName === 'monitoring') {
        loadFacilityMonitoringData();
      }
    }

    // Load facility monitoring data
    function loadFacilityMonitoringData() {
      // Get facilities data from the page
      const facilities = @json($facilities);
      
      // Update dashboard stats
      updateDashboardStats(facilities);
      
      // Load facility status grid
      loadFacilityStatusGrid(facilities);
      
      // Load activity timeline
      loadActivityTimeline(facilities);
      
      // Load alerts
      loadAlerts(facilities);
      
      // Load usage analytics
      loadUsageAnalytics(facilities);
    }

    // Update dashboard statistics
    function updateDashboardStats(facilities) {
      const availableCount = facilities.filter(f => f.status === 'available').length;
      const occupiedCount = facilities.filter(f => f.status === 'occupied').length;
      const totalReservations = facilities.reduce((sum, f) => sum + (f.reservations_count || 0), 0);
      const utilizationRate = facilities.length > 0 ? Math.round((occupiedCount / facilities.length) * 100) : 0;
      const revenueToday = totalReservations * 150; // Mock revenue calculation

      document.getElementById('liveStatusCount').textContent = availableCount;
      document.getElementById('activeReservationsCount').textContent = totalReservations;
      document.getElementById('utilizationRate').textContent = utilizationRate + '%';
      document.getElementById('revenueToday').textContent = '₱' + revenueToday.toLocaleString();
    }

    // Load facility status grid
    function loadFacilityStatusGrid(facilities) {
      const container = document.getElementById('facility-status-grid');
      if (!container) return;

      container.innerHTML = facilities.map((facility, index) => {
        const statusConfig = getFacilityStatusConfig(facility.status);
        const lastActivity = getLastActivity(facility);
        const utilization = Math.random() * 100; // Mock utilization data
        const isOccupied = facility.status === 'occupied';
        const isAvailable = facility.status === 'available';
        
        return `
          <div class="facility-status-card group p-6 border-2 border-gray-200 rounded-2xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105 hover:-translate-y-2 bg-white relative overflow-hidden" 
               style="animation-delay: ${index * 100}ms; animation: slideInUp 0.6s ease-out forwards;">
            <!-- Animated Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-gray-50 to-white opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            
            <!-- Status Indicator with Animation -->
            <div class="flex items-center justify-between mb-4 relative z-10">
              <div class="flex items-center space-x-4">
                <div class="relative">
                  <div class="w-4 h-4 rounded-full ${isOccupied ? 'bg-red-500' : isAvailable ? 'bg-green-500' : 'bg-yellow-500'} ${isOccupied ? 'animate-pulse' : ''}"></div>
                  ${isOccupied ? '<div class="absolute inset-0 w-4 h-4 rounded-full bg-red-500 animate-ping opacity-75"></div>' : ''}
                </div>
                <div>
                  <h4 class="font-bold text-xl text-gray-800 group-hover:text-blue-600 transition-colors duration-300">${facility.name}</h4>
                  <p class="text-sm text-gray-500 flex items-center">
                    <i data-lucide="map-pin" class="w-3 h-3 mr-1"></i>
                    ${facility.location || 'No Location'}
                  </p>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <span class="badge ${statusConfig.badgeClass} text-xs px-3 py-1 rounded-full font-semibold shadow-lg" style="background-color: ${statusConfig.color}; color: white;">
                  ${statusConfig.label}
                </span>
                ${isOccupied ? '<i data-lucide="lock" class="w-4 h-4 text-red-500 animate-bounce"></i>' : ''}
              </div>
            </div>
            
            <!-- Metrics Grid -->
            <div class="grid grid-cols-2 gap-4 mb-4 relative z-10">
              <div class="bg-gray-50 rounded-xl p-3 group-hover:bg-blue-50 transition-colors duration-300">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-medium text-gray-600 uppercase tracking-wide">Capacity</span>
                  <i data-lucide="users" class="w-4 h-4 text-gray-400"></i>
                </div>
                <p class="text-lg font-bold text-gray-800 mt-1">${facility.capacity || 'N/A'}</p>
              </div>
              
              <div class="bg-gray-50 rounded-xl p-3 group-hover:bg-green-50 transition-colors duration-300">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-medium text-gray-600 uppercase tracking-wide">Reservations</span>
                  <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                </div>
                <p class="text-lg font-bold text-gray-800 mt-1">${facility.reservations_count || 0}</p>
              </div>
            </div>
            
            <!-- Utilization Bar with Animation -->
            <div class="relative z-10">
              <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-gray-600">Utilization</span>
                <span class="text-sm font-bold text-gray-800">${utilization.toFixed(1)}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-blue-400 to-blue-600 transition-all duration-1000 ease-out relative" 
                     style="width: ${utilization}%">
                  <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div>
                </div>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex space-x-2 mt-4 relative z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <button onclick="viewFacilityDetails(${facility.id})" 
                      class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold py-2 px-3 rounded-lg transition-all duration-200 transform hover:scale-105">
                <i data-lucide="eye" class="w-3 h-3 mr-1"></i>View
              </button>
              <button onclick="editFacility(${facility.id})" 
                      class="flex-1 bg-gray-500 hover:bg-gray-600 text-white text-xs font-semibold py-2 px-3 rounded-lg transition-all duration-200 transform hover:scale-105">
                <i data-lucide="edit" class="w-3 h-3 mr-1"></i>Edit
              </button>
              ${isOccupied ? `
              <button onclick="freeFacility(${facility.id})" 
                      class="flex-1 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold py-2 px-3 rounded-lg transition-all duration-200 transform hover:scale-105">
                <i data-lucide="unlock" class="w-3 h-3 mr-1"></i>Free
              </button>
              ` : ''}
            </div>
          </div>
        `;
      }).join('');

      // Re-initialize Lucide icons
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    }

    // Global activity timeline data
    let activityTimeline = [];
    let alertsData = [];

    // Load activity timeline
    function loadActivityTimeline(facilities) {
      const container = document.getElementById('activity-timeline');
      if (!container) return;

      // Initialize with mock data if empty
      if (activityTimeline.length === 0) {
        activityTimeline = [
          {
            id: 1,
            time: new Date(Date.now() - 2 * 60 * 1000), // 2 minutes ago
            action: 'Facility "gigi" status changed to Occupied',
            icon: 'building',
            color: 'text-red-500',
            bgColor: 'bg-red-50',
            borderColor: 'border-red-200',
            isNew: true,
            type: 'status_change',
            facilityId: 1
          },
          {
            id: 2,
            time: new Date(Date.now() - 15 * 60 * 1000), // 15 minutes ago
            action: 'New reservation created for "fef"',
            icon: 'calendar',
            color: 'text-blue-500',
            bgColor: 'bg-blue-50',
            borderColor: 'border-blue-200',
            isNew: false,
            type: 'reservation_created',
            facilityId: 2
          },
          {
            id: 3,
            time: new Date(Date.now() - 60 * 60 * 1000), // 1 hour ago
            action: 'Facility "ocada" maintenance completed',
            icon: 'wrench',
            color: 'text-green-500',
            bgColor: 'bg-green-50',
            borderColor: 'border-green-200',
            isNew: false,
            type: 'maintenance_completed',
            facilityId: 3
          },
          {
            id: 4,
            time: new Date(Date.now() - 2 * 60 * 60 * 1000), // 2 hours ago
            action: 'Reservation cancelled for "laire"',
            icon: 'x-circle',
            color: 'text-orange-500',
            bgColor: 'bg-orange-50',
            borderColor: 'border-orange-200',
            isNew: false,
            type: 'reservation_cancelled',
            facilityId: 4
          },
          {
            id: 5,
            time: new Date(Date.now() - 3 * 60 * 60 * 1000), // 3 hours ago
            action: 'Facility "sogo" status changed to Available',
            icon: 'check-circle',
            color: 'text-green-500',
            bgColor: 'bg-green-50',
            borderColor: 'border-green-200',
            isNew: false,
            type: 'status_change',
            facilityId: 5
          }
        ];
      }

      renderActivityTimeline();
    }

    // Render activity timeline
    function renderActivityTimeline() {
      const container = document.getElementById('activity-timeline');
      if (!container) return;

      // Sort by time (newest first)
      const sortedActivities = [...activityTimeline].sort((a, b) => b.time - a.time);

      container.innerHTML = sortedActivities.map((activity, index) => {
        const timeAgo = getTimeAgo(activity.time);
        
        return `
          <div class="activity-item flex items-start space-x-4 p-4 hover:shadow-lg rounded-xl transition-all duration-300 transform hover:scale-105 ${activity.bgColor} ${activity.borderColor} border-l-4 cursor-pointer" 
               style="animation-delay: ${index * 150}ms; animation: slideInRight 0.6s ease-out forwards;"
               onclick="handleActivityClick(${activity.id}, '${activity.type}')">
            <div class="relative flex-shrink-0">
              <div class="w-10 h-10 ${activity.bgColor} rounded-full flex items-center justify-center shadow-lg">
                <i data-lucide="${activity.icon}" class="w-5 h-5 ${activity.color}"></i>
              </div>
              ${activity.isNew ? '<div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-ping"></div>' : ''}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-800 leading-relaxed">${activity.action}</p>
              <p class="text-xs text-gray-500 mt-1 flex items-center">
                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                ${timeAgo}
              </p>
            </div>
            ${activity.isNew ? '<div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>' : ''}
          </div>
        `;
      }).join('');

      // Re-initialize Lucide icons
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    }

    // Handle activity click
    function handleActivityClick(activityId, type) {
      const activity = activityTimeline.find(a => a.id === activityId);
      if (!activity) return;

      // Mark as read
      activity.isNew = false;
      
      // Show detailed modal or perform action based on type
      switch(type) {
        case 'status_change':
          showActivityDetails(activity, 'Status Change Details');
          break;
        case 'reservation_created':
          showActivityDetails(activity, 'Reservation Details');
          break;
        case 'maintenance_completed':
          showActivityDetails(activity, 'Maintenance Report');
          break;
        case 'reservation_cancelled':
          showActivityDetails(activity, 'Cancellation Details');
          break;
        default:
          showActivityDetails(activity, 'Activity Details');
      }
      
      // Re-render to update isNew status
      renderActivityTimeline();
    }

    // Show activity details modal
    function showActivityDetails(activity, title) {
      const modal = document.createElement('div');
      modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
      modal.innerHTML = `
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="activity-modal">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">${title}</h3>
            <button onclick="closeActivityModal()" class="text-gray-400 hover:text-gray-600">
              <i data-lucide="x" class="w-5 h-5"></i>
            </button>
          </div>
          <div class="space-y-4">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 ${activity.bgColor} rounded-full flex items-center justify-center">
                <i data-lucide="${activity.icon}" class="w-5 h-5 ${activity.color}"></i>
              </div>
              <div>
                <p class="font-medium text-gray-800">${activity.action}</p>
                <p class="text-sm text-gray-500">${getTimeAgo(activity.time)}</p>
              </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <h4 class="font-medium text-gray-700 mb-2">Additional Information</h4>
              <p class="text-sm text-gray-600">This activity was automatically logged by the system. Click "View Facility" to see more details about the related facility.</p>
            </div>
            <div class="flex space-x-3">
              <button onclick="viewFacilityFromActivity(${activity.facilityId})" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition-colors">
                <i data-lucide="building" class="w-4 h-4 mr-2 inline"></i>View Facility
              </button>
              <button onclick="closeActivityModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition-colors">
                Close
              </button>
            </div>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      
      // Animate in
      setTimeout(() => {
        const modalContent = modal.querySelector('#activity-modal');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
      }, 10);
      
      // Re-initialize Lucide icons
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    }

    // Close activity modal
    function closeActivityModal() {
      const modal = document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');
      if (modal) {
        modal.remove();
      }
    }

    // View facility from activity
    function viewFacilityFromActivity(facilityId) {
      closeActivityModal();
      // This would open the facility details or scroll to the facility in the grid
      showNotification(`Opening facility details for ID: ${facilityId}`, 'info');
    }

    // Get time ago string
    function getTimeAgo(date) {
      const now = new Date();
      const diffMs = now - date;
      const diffMins = Math.floor(diffMs / (1000 * 60));
      const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
      const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

      if (diffMins < 1) return 'Just now';
      if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
      if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
      return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
    }

    // Add new activity
    function addActivity(action, type, facilityId) {
      const newActivity = {
        id: Date.now(),
        time: new Date(),
        action: action,
        icon: getActivityIcon(type),
        color: getActivityColor(type),
        bgColor: getActivityBgColor(type),
        borderColor: getActivityBorderColor(type),
        isNew: true,
        type: type,
        facilityId: facilityId
      };
      
      activityTimeline.unshift(newActivity);
      
      // Keep only last 50 activities
      if (activityTimeline.length > 50) {
        activityTimeline = activityTimeline.slice(0, 50);
      }
      
      renderActivityTimeline();
      showNotification('New activity added to timeline', 'info');
    }

    // Get activity icon based on type
    function getActivityIcon(type) {
      const icons = {
        'status_change': 'building',
        'reservation_created': 'calendar',
        'reservation_cancelled': 'x-circle',
        'maintenance_completed': 'wrench',
        'maintenance_scheduled': 'clock',
        'facility_freed': 'unlock',
        'alert_triggered': 'alert-triangle'
      };
      return icons[type] || 'activity';
    }

    // Get activity color based on type
    function getActivityColor(type) {
      const colors = {
        'status_change': 'text-red-500',
        'reservation_created': 'text-blue-500',
        'reservation_cancelled': 'text-orange-500',
        'maintenance_completed': 'text-green-500',
        'maintenance_scheduled': 'text-yellow-500',
        'facility_freed': 'text-green-500',
        'alert_triggered': 'text-red-500'
      };
      return colors[type] || 'text-gray-500';
    }

    // Get activity background color
    function getActivityBgColor(type) {
      const colors = {
        'status_change': 'bg-red-50',
        'reservation_created': 'bg-blue-50',
        'reservation_cancelled': 'bg-orange-50',
        'maintenance_completed': 'bg-green-50',
        'maintenance_scheduled': 'bg-yellow-50',
        'facility_freed': 'bg-green-50',
        'alert_triggered': 'bg-red-50'
      };
      return colors[type] || 'bg-gray-50';
    }

    // Get activity border color
    function getActivityBorderColor(type) {
      const colors = {
        'status_change': 'border-red-200',
        'reservation_created': 'border-blue-200',
        'reservation_cancelled': 'border-orange-200',
        'maintenance_completed': 'border-green-200',
        'maintenance_scheduled': 'border-yellow-200',
        'facility_freed': 'border-green-200',
        'alert_triggered': 'border-red-200'
      };
      return colors[type] || 'border-gray-200';
    }

    // Load alerts and notifications
    function loadAlerts(facilities) {
      const container = document.getElementById('alerts-container');
      if (!container) return;

      // Initialize with mock data if empty
      if (alertsData.length === 0) {
        alertsData = [
          {
            id: 1,
            type: 'warning',
            message: 'Facility "gigi" has been occupied for 3+ hours',
            time: new Date(Date.now() - 5 * 60 * 1000), // 5 minutes ago
            icon: 'clock',
            bgColor: 'bg-yellow-50',
            borderColor: 'border-yellow-200',
            iconColor: 'text-yellow-600',
            isUrgent: true,
            isRead: false,
            facilityId: 1,
            priority: 'high'
          },
          {
            id: 2,
            type: 'info',
            message: 'Maintenance scheduled for "ocada" tomorrow',
            time: new Date(Date.now() - 60 * 60 * 1000), // 1 hour ago
            icon: 'wrench',
            bgColor: 'bg-blue-50',
            borderColor: 'border-blue-200',
            iconColor: 'text-blue-600',
            isUrgent: false,
            isRead: false,
            facilityId: 3,
            priority: 'medium'
          },
          {
            id: 3,
            type: 'success',
            message: 'All facilities are operational',
            time: new Date(Date.now() - 2 * 60 * 60 * 1000), // 2 hours ago
            icon: 'check-circle',
            bgColor: 'bg-green-50',
            borderColor: 'border-green-200',
            iconColor: 'text-green-600',
            isUrgent: false,
            isRead: true,
            facilityId: null,
            priority: 'low'
          }
        ];
      }

      renderAlerts();
    }

    // Render alerts
    function renderAlerts() {
      const container = document.getElementById('alerts-container');
      if (!container) return;

      // Sort by priority and time (urgent first, then by time)
      const sortedAlerts = [...alertsData].sort((a, b) => {
        if (a.isUrgent && !b.isUrgent) return -1;
        if (!a.isUrgent && b.isUrgent) return 1;
        return b.time - a.time;
      });

      container.innerHTML = sortedAlerts.map((alert, index) => {
        const timeAgo = getTimeAgo(alert.time);
        
        return `
          <div class="alert-item ${alert.bgColor} ${alert.borderColor} border-l-4 p-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:scale-105 cursor-pointer ${alert.isRead ? 'opacity-75' : ''}" 
               style="animation-delay: ${index * 200}ms; animation: slideInLeft 0.6s ease-out forwards;"
               onclick="handleAlertClick(${alert.id})">
            <div class="flex items-start space-x-3">
              <div class="flex-shrink-0 relative">
                <div class="w-8 h-8 ${alert.bgColor} rounded-full flex items-center justify-center shadow-sm">
                  <i data-lucide="${alert.icon}" class="w-4 h-4 ${alert.iconColor} ${alert.isUrgent ? 'animate-pulse' : ''}"></i>
                </div>
                ${alert.isUrgent ? '<div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-ping"></div>' : ''}
                ${!alert.isRead ? '<div class="absolute -bottom-1 -right-1 w-3 h-3 bg-blue-500 rounded-full"></div>' : ''}
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800 leading-relaxed">${alert.message}</p>
                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                      <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                      ${timeAgo}
                    </p>
                  </div>
                  <div class="flex items-center space-x-2 ml-2">
                    ${alert.isUrgent ? '<div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>' : ''}
                    <button onclick="dismissAlert(${alert.id})" class="text-gray-400 hover:text-gray-600 transition-colors">
                      <i data-lucide="x" class="w-3 h-3"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        `;
      }).join('');

      // Re-initialize Lucide icons
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    }

    // Handle alert click
    function handleAlertClick(alertId) {
      const alert = alertsData.find(a => a.id === alertId);
      if (!alert) return;

      // Mark as read
      alert.isRead = true;
      
      // Show alert details modal
      showAlertDetails(alert);
      
      // Re-render to update read status
      renderAlerts();
    }

    // Show alert details modal
    function showAlertDetails(alert) {
      const modal = document.createElement('div');
      modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
      modal.innerHTML = `
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="alert-modal">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Alert Details</h3>
            <button onclick="closeAlertModal()" class="text-gray-400 hover:text-gray-600">
              <i data-lucide="x" class="w-5 h-5"></i>
            </button>
          </div>
          <div class="space-y-4">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 ${alert.bgColor} rounded-full flex items-center justify-center">
                <i data-lucide="${alert.icon}" class="w-5 h-5 ${alert.iconColor}"></i>
              </div>
              <div>
                <p class="font-medium text-gray-800">${alert.message}</p>
                <p class="text-sm text-gray-500">${getTimeAgo(alert.time)}</p>
              </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <h4 class="font-medium text-gray-700 mb-2">Alert Information</h4>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-600">Priority:</span>
                  <span class="font-medium ${alert.priority === 'high' ? 'text-red-600' : alert.priority === 'medium' ? 'text-yellow-600' : 'text-green-600'}">${alert.priority.toUpperCase()}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Status:</span>
                  <span class="font-medium ${alert.isUrgent ? 'text-red-600' : 'text-green-600'}">${alert.isUrgent ? 'URGENT' : 'NORMAL'}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Read:</span>
                  <span class="font-medium ${alert.isRead ? 'text-green-600' : 'text-red-600'}">${alert.isRead ? 'YES' : 'NO'}</span>
                </div>
              </div>
            </div>
            <div class="flex space-x-3">
              ${alert.facilityId ? `
              <button onclick="viewFacilityFromAlert(${alert.facilityId})" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition-colors">
                <i data-lucide="building" class="w-4 h-4 mr-2 inline"></i>View Facility
              </button>
              ` : ''}
              <button onclick="dismissAlert(${alert.id})" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg transition-colors">
                <i data-lucide="x" class="w-4 h-4 mr-2 inline"></i>Dismiss
              </button>
              <button onclick="closeAlertModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition-colors">
                Close
              </button>
            </div>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      
      // Animate in
      setTimeout(() => {
        const modalContent = modal.querySelector('#alert-modal');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
      }, 10);
      
      // Re-initialize Lucide icons
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    }

    // Close alert modal
    function closeAlertModal() {
      const modal = document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');
      if (modal) {
        modal.remove();
      }
    }

    // Dismiss alert
    function dismissAlert(alertId) {
      const alertIndex = alertsData.findIndex(a => a.id === alertId);
      if (alertIndex !== -1) {
        alertsData.splice(alertIndex, 1);
        renderAlerts();
        showNotification('Alert dismissed', 'success');
      }
      closeAlertModal();
    }

    // View facility from alert
    function viewFacilityFromAlert(facilityId) {
      closeAlertModal();
      showNotification(`Opening facility details for ID: ${facilityId}`, 'info');
    }

    // Add new alert
    function addAlert(message, type, priority = 'medium', facilityId = null) {
      const newAlert = {
        id: Date.now(),
        type: type,
        message: message,
        time: new Date(),
        icon: getAlertIcon(type),
        bgColor: getAlertBgColor(type),
        borderColor: getAlertBorderColor(type),
        iconColor: getAlertIconColor(type),
        isUrgent: priority === 'high',
        isRead: false,
        facilityId: facilityId,
        priority: priority
      };
      
      alertsData.unshift(newAlert);
      
      // Keep only last 20 alerts
      if (alertsData.length > 20) {
        alertsData = alertsData.slice(0, 20);
      }
      
      renderAlerts();
      showNotification('New alert received', 'warning');
    }

    // Get alert icon based on type
    function getAlertIcon(type) {
      const icons = {
        'warning': 'alert-triangle',
        'info': 'info',
        'success': 'check-circle',
        'error': 'x-circle',
        'maintenance': 'wrench',
        'security': 'shield',
        'system': 'settings'
      };
      return icons[type] || 'bell';
    }

    // Get alert background color
    function getAlertBgColor(type) {
      const colors = {
        'warning': 'bg-yellow-50',
        'info': 'bg-blue-50',
        'success': 'bg-green-50',
        'error': 'bg-red-50',
        'maintenance': 'bg-purple-50',
        'security': 'bg-red-50',
        'system': 'bg-gray-50'
      };
      return colors[type] || 'bg-gray-50';
    }

    // Get alert border color
    function getAlertBorderColor(type) {
      const colors = {
        'warning': 'border-yellow-200',
        'info': 'border-blue-200',
        'success': 'border-green-200',
        'error': 'border-red-200',
        'maintenance': 'border-purple-200',
        'security': 'border-red-200',
        'system': 'border-gray-200'
      };
      return colors[type] || 'border-gray-200';
    }

    // Get alert icon color
    function getAlertIconColor(type) {
      const colors = {
        'warning': 'text-yellow-600',
        'info': 'text-blue-600',
        'success': 'text-green-600',
        'error': 'text-red-600',
        'maintenance': 'text-purple-600',
        'security': 'text-red-600',
        'system': 'text-gray-600'
      };
      return colors[type] || 'text-gray-600';
    }

    // Load usage analytics (enhanced chart)
    function loadUsageAnalytics(facilities) {
      const container = document.getElementById('usage-chart');
      if (!container) return;

      // Mock chart data
      const chartData = {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        data: [65, 78, 82, 75, 90, 85, 70],
        colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#84CC16']
      };

      container.innerHTML = `
        <div class="w-full h-full p-6">
          <div class="flex items-center justify-between mb-6">
            <h4 class="text-lg font-semibold text-gray-800">Weekly Usage Trend</h4>
            <div class="flex items-center space-x-2">
              <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
              <span class="text-sm text-gray-600">Live Data</span>
            </div>
          </div>
          
          <div class="relative">
            <!-- Chart Container -->
            <div class="flex items-end justify-between space-x-3 h-48 mb-4">
              ${chartData.data.map((value, index) => `
                <div class="flex flex-col items-center group cursor-pointer" style="animation-delay: ${index * 100}ms; animation: slideInUp 0.8s ease-out forwards;">
                  <div class="relative">
                    <div class="bg-gradient-to-t from-blue-400 to-blue-600 w-8 rounded-t-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 group-hover:from-blue-500 group-hover:to-blue-700" 
                         style="height: ${value}%; animation: growUp 1.2s ease-out forwards;">
                      <div class="absolute inset-0 bg-gradient-to-t from-transparent via-white/20 to-transparent rounded-t-lg"></div>
                    </div>
                    <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                      ${value}%
                    </div>
                  </div>
                  <span class="text-xs text-gray-600 mt-2 font-medium">${chartData.labels[index]}</span>
                </div>
              `).join('')}
            </div>
            
            <!-- Chart Legend -->
            <div class="flex items-center justify-center space-x-6 mt-4">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                <span class="text-xs text-gray-600">Utilization %</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-xs text-gray-600">Peak Hours</span>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    // Get facility status configuration
    function getFacilityStatusConfig(status) {
      switch (status) {
        case 'available':
          return {
            label: 'Available',
            color: '#22c55e',
            badgeClass: 'badge-success'
          };
        case 'occupied':
          return {
            label: 'Occupied',
            color: '#ef4444',
            badgeClass: 'badge-error'
          };
        case 'maintenance':
          return {
            label: 'Maintenance',
            color: '#f59e0b',
            badgeClass: 'badge-warning'
          };
        default:
          return {
            label: 'Unknown',
            color: '#6b7280',
            badgeClass: 'badge-neutral'
          };
      }
    }

    // Get last activity for facility
    function getLastActivity(facility) {
      if (facility.updated_at) {
        const date = new Date(facility.updated_at);
        return date.toLocaleDateString('en-US', { 
          month: 'short', 
          day: 'numeric', 
          hour: '2-digit', 
          minute: '2-digit' 
        });
      }
      return 'N/A';
    }

    // View facility details
    function viewFacilityDetails(facilityId) {
      // This would open a modal or redirect to facility details
      console.log('View facility details:', facilityId);
      // You can implement this based on your existing facility view functionality
    }

    // Edit facility
    function editFacility(facilityId) {
      // This would open edit modal or redirect to edit page
      console.log('Edit facility:', facilityId);
      // You can implement this based on your existing facility edit functionality
    }

    // Quick action functions
    function refreshMonitoringData() {
      console.log('Refreshing monitoring data...');
      loadFacilityMonitoringData();
      showNotification('Monitoring data refreshed successfully!', 'success');
    }

    function exportMonitoringReport() {
      console.log('Exporting monitoring report...');
      showNotification('Exporting monitoring report...', 'info');
      // Implement export functionality
    }

    function openFacilityMap() {
      console.log('Opening facility map...');
      showNotification('Facility map feature coming soon!', 'info');
      // Implement map functionality
    }

    function openMaintenanceSchedule() {
      console.log('Opening maintenance schedule...');
      showNotification('Maintenance schedule feature coming soon!', 'info');
      // Implement maintenance functionality
    }

    // Real-time updates (simulate)
    function startRealTimeUpdates() {
      setInterval(() => {
        if (currentFacilityTab === 'monitoring') {
          // Simulate real-time data updates
          const facilities = @json($facilities);
          updateDashboardStats(facilities);
        }
      }, 30000); // Update every 30 seconds

      // Simulate real-time activities every 2 minutes
      setInterval(() => {
        if (currentFacilityTab === 'monitoring') {
          simulateRandomActivity();
        }
      }, 120000);

      // Simulate random alerts every 3 minutes
      setInterval(() => {
        if (currentFacilityTab === 'monitoring') {
          simulateRandomAlert();
        }
      }, 180000);
    }

    // Simulate random activity
    function simulateRandomActivity() {
      const activities = [
        'Facility "gigi" status changed to Available',
        'New reservation created for "fef"',
        'Facility "ocada" maintenance completed',
        'Reservation cancelled for "laire"',
        'Facility "sogo" status changed to Occupied',
        'Maintenance scheduled for "rara"',
        'Facility "laire" freed up',
        'Emergency maintenance required for "gigi"'
      ];

      const types = [
        'status_change',
        'reservation_created',
        'maintenance_completed',
        'reservation_cancelled',
        'status_change',
        'maintenance_scheduled',
        'facility_freed',
        'alert_triggered'
      ];

      const facilityIds = [1, 2, 3, 4, 5, 6];

      const randomActivity = activities[Math.floor(Math.random() * activities.length)];
      const randomType = types[Math.floor(Math.random() * types.length)];
      const randomFacilityId = facilityIds[Math.floor(Math.random() * facilityIds.length)];

      addActivity(randomActivity, randomType, randomFacilityId);
    }

    // Simulate random alert
    function simulateRandomAlert() {
      const alerts = [
        'Facility "gigi" has been occupied for 4+ hours',
        'Maintenance required for "ocada"',
        'High utilization detected on "fef"',
        'Security alert on "rara"',
        'System maintenance scheduled',
        'Facility "sogo" temperature alert',
        'Power outage detected',
        'Network connectivity issues'
      ];

      const types = ['warning', 'info', 'error', 'maintenance', 'system', 'security'];
      const priorities = ['high', 'medium', 'low'];
      const facilityIds = [1, 2, 3, 4, 5, 6, null];

      const randomAlert = alerts[Math.floor(Math.random() * alerts.length)];
      const randomType = types[Math.floor(Math.random() * types.length)];
      const randomPriority = priorities[Math.floor(Math.random() * priorities.length)];
      const randomFacilityId = facilityIds[Math.floor(Math.random() * facilityIds.length)];

      addAlert(randomAlert, randomType, randomPriority, randomFacilityId);
    }

    // Demo functions for testing
    function addTestActivity() {
      addActivity('Test activity added manually', 'info', 1);
    }

    function addTestAlert() {
      addAlert('Test alert - This is a demo notification', 'warning', 'high', 1);
    }

    function clearAllAlerts() {
      alertsData = [];
      renderAlerts();
      showNotification('All alerts cleared', 'success');
    }

    function markAllAlertsRead() {
      alertsData.forEach(alert => {
        alert.isRead = true;
      });
      renderAlerts();
      showNotification('All alerts marked as read', 'success');
    }

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

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      updateDateTime();
      
      // Initialize view mode from localStorage or default to grid
      const savedView = localStorage.getItem('facilityView') || 'grid';
      currentViewMode = savedView;
      
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

      // Setup monitoring search functionality
      const monitoringSearchInput = document.getElementById('facilityMonitoringSearch');
      if (monitoringSearchInput) {
        monitoringSearchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const monitoringCards = document.querySelectorAll('.facility-status-card');
          
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

      // Start real-time updates
      startRealTimeUpdates();
    });

    // Notification function
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
  </script>

  <style>
    /* Custom Animations */
    @keyframes slideInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes slideInRight {
      from {
        opacity: 0;
        transform: translateX(30px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes slideInLeft {
      from {
        opacity: 0;
        transform: translateX(-30px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes growUp {
      from {
        height: 0;
      }
      to {
        height: var(--target-height);
      }
    }

    @keyframes shimmer {
      0% {
        background-position: -200px 0;
      }
      100% {
        background-position: calc(200px + 100%) 0;
      }
    }

    @keyframes float {
      0%, 100% {
        transform: translateY(0px);
      }
      50% {
        transform: translateY(-10px);
      }
    }

    @keyframes pulse-glow {
      0%, 100% {
        box-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
      }
      50% {
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.8);
      }
    }

    /* Enhanced Stats Cards */
    .stats-card {
      position: relative;
      overflow: hidden;
    }

    .stats-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .stats-card:hover::before {
      left: 100%;
    }

    /* Facility Status Cards */
    .facility-status-card {
      position: relative;
      overflow: hidden;
    }

    .facility-status-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #3B82F6, #10B981, #F59E0B, #EF4444);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .facility-status-card:hover::before {
      transform: scaleX(1);
    }

    /* Activity Items */
    .activity-item {
      position: relative;
    }

    .activity-item::after {
      content: '';
      position: absolute;
      left: 19px;
      top: 40px;
      bottom: -20px;
      width: 2px;
      background: linear-gradient(to bottom, #E5E7EB, transparent);
    }

    .activity-item:last-child::after {
      display: none;
    }

    /* Alert Items */
    .alert-item {
      position: relative;
    }

    .alert-item::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 4px;
      background: currentColor;
      border-radius: 0 2px 2px 0;
    }

    /* Chart Bars */
    .chart-bar {
      position: relative;
      overflow: hidden;
    }

    .chart-bar::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.3) 50%, transparent 70%);
      transform: translateX(-100%);
      animation: shimmer 2s infinite;
    }

    /* Loading States */
    .loading-shimmer {
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200px 100%;
      animation: shimmer 1.5s infinite;
    }

    /* Hover Effects */
    .hover-lift:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Pulse Animation for Urgent Items */
    .urgent-pulse {
      animation: pulse-glow 2s infinite;
    }

    /* Floating Animation */
    .float-animation {
      animation: float 3s ease-in-out infinite;
    }

    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
      width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }

    /* Responsive Animations */
    @media (max-width: 768px) {
      .stats-card {
        transform: none !important;
      }
      
      .facility-status-card {
        transform: none !important;
      }
    }
  </style>
</body>
</html> 