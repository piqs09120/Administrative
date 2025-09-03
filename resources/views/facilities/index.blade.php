<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Facilities Reservations</title>
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Facilities -->
          <div class="bg-gradient-to-br from-[#1A2C5B] to-blue-800 rounded-xl p-6 text-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer">
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
          <div class="bg-gradient-to-br from-[#4A8C8C] to-emerald-800 rounded-xl p-6 text-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer">
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
          <div class="bg-gradient-to-br from-[#DC3545] to-red-800 rounded-xl p-6 text-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer">
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
          <div class="bg-gradient-to-br from-[#1A2C5B] to-blue-800 rounded-xl p-6 text-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-blue-100 text-sm font-medium">Total Reservations</p>
                <p class="text-3xl font-bold">{{ $facilities->sum(function($facility) { return $facility->reservations->count(); }) }}</p>
              </div>
              <div class="bg-blue-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="calendar" class="w-8 h-8"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 mb-8">
          <button type="button" id="openCreateFacilityModal" class="btn btn-primary btn-lg">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Add New Facility
          </button>
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

                  <div class="grid grid-cols-2 gap-3 mt-4">
                    <!-- View Button (opens modal) -->
                    <button type="button"
                       class="openViewFacilityBtn group relative overflow-hidden"
                       data-id="{{ $facility->id }}"
                       style="background: linear-gradient(to right, var(--color-regal-navy), color-mix(in srgb, var(--color-regal-navy), black 10%)); color: var(--color-white); padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease; transform: scale(1); box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; width: 8rem; margin-left: auto; margin-right: auto; ">
                      <i data-lucide="eye" class="w-4 h-4 mr-2 transition-transform group-hover:scale-110"></i>
                      <span class="relative z-10">VIEW</span>
                      <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    </button>

                    <!-- Edit Button (opens modal) -->
                    <button type="button"
                       class="openEditFacilityBtn group relative overflow-hidden"
                       data-id="{{ $facility->id }}"
                       data-name="{{ $facility->name }}"
                       data-location="{{ $facility->location }}"
                       data-description="{{ $facility->description }}"
                       data-status="{{ $facility->status }}"
                       style="background: linear-gradient(to right, var(--color-golden-ember), color-mix(in srgb, var(--color-golden-ember), black 10%)); color: var(--color-white); padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease; transform: scale(1); box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; width: 8rem; margin-left: auto; margin-right: auto; ">
                      <i data-lucide="edit" class="w-4 h-4 mr-2 transition-transform group-hover:scale-110"></i>
                      <span class="relative z-10">EDIT</span>
                      <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    </button>

                    @if($facility->status === 'available')
                      <!-- Reserve Button opens modal -->
                      <button type="button"
                         class="openReserveFacilityBtn group relative overflow-hidden"
                         data-id="{{ $facility->id }}"
                         data-name="{{ $facility->name }}"
                         style="background: linear-gradient(to right, var(--color-modern-teal), color-mix(in srgb, var(--color-modern-teal), black 10%)); color: var(--color-white); padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease; transform: scale(1); box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; width: 8rem; margin-left: auto; margin-right: auto; ">
                        <i data-lucide="calendar-plus" class="w-4 h-4 mr-2 transition-transform group-hover:scale-110"></i>
                        <span class="relative z-10">RESERVE</span>
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                      </button>
                    @endif

                    <!-- Delete Button -->
                    <div class="{{ $facility->status === 'occupied' ? 'col-span-2 flex justify-center' : '' }}">
                      <button type="button" 
                              class="deleteFacilityBtn group relative overflow-hidden" 
                              data-id="{{ $facility->id }}"
                              data-name="{{ $facility->name }}"
                              data-location="{{ $facility->location }}"
                              data-status="{{ $facility->status }}"
                              data-reservations="{{ $facility->reservations->count() }}"
                              data-url="{{ route('facilities.destroy', $facility->id) }}"
                              style="background: linear-gradient(to right, var(--color-danger-red), color-mix(in srgb, var(--color-danger-red), black 10%)); color: var(--color-white); padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease; transform: scale(1); box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; width: 8rem; margin-left: auto; margin-right: auto; ">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-2 transition-transform group-hover:scale-110"></i>
                        <span class="relative z-10">DELETE</span>
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                      </button>
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
        <button type="button" class="btn btn-outline" id="cancelDeleteBtn">
          <i data-lucide="x" class="w-4 h-4 mr-2"></i>
          Cancel
        </button>
        <button type="button" class="btn btn-error" id="confirmDeleteBtn">
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

          <div class="flex justify-end gap-2">
            <button type="button" class="btn btn-ghost" id="cancelReserveFacility">Cancel</button>
            <button type="submit" class="btn btn-primary">
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
          <button type="button" class="btn btn-outline" id="cancelCreateFacility">Cancel</button>
          <button type="submit" class="btn btn-primary">
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

      <form id="editFacilityForm" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-4">
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
          <button type="button" class="btn btn-outline" id="cancelEditFacility">Cancel</button>
          <button type="submit" class="btn btn-primary">
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

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      updateDateTime();
      setupSearch();
      
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

          openEditModal();
        });
      });

      if (closeEditBtn) closeEditBtn.addEventListener('click', closeEditModal);
      if (cancelEditBtn) cancelEditBtn.addEventListener('click', closeEditModal);
      if (editModal) editModal.addEventListener('click', function(e){ if(e.target === editModal) closeEditModal(); });

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
          
          const facilityCard = document.querySelector(`[data-id="${facilityId}"]`)?.closest('.bg-white.border.border-gray-200.rounded-xl');
          
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
                const remainingCards = document.querySelectorAll('.bg-white.border.border-gray-200.rounded-xl');
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
        const totalFacilities = document.querySelectorAll('.bg-white.border.border-gray-200.rounded-xl').length;
        const availableFacilities = document.querySelectorAll('.badge-success').length;
        const occupiedFacilities = document.querySelectorAll('.badge-error').length;
        
        // Update stats cards if they exist
        const totalCard = document.querySelector('.text-3xl.font-bold');
        if (totalCard) {
          totalCard.textContent = totalFacilities;
        }
      }
      
      // Helper function to show empty state
      function showEmptyState() {
        const grid = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3.gap-6');
        if (grid) {
          grid.innerHTML = `
            <div class="col-span-full text-center py-12">
              <i data-lucide="building" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
              <h3 class="text-lg font-semibold text-gray-600 mb-2">No Facilities Found</h3>
              <p class="text-gray-500 mb-6">Add your first facility to get started.</p>
              <button type="button" id="openCreateFacilityModal" class="btn btn-primary btn-lg">
                <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                Add Facility
              </button>
            </div>
          `;
          lucide.createIcons();
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