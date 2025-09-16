<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Facility Reservations - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
</head>
<body class="bg-gray-50">
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

       

      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <!-- Reserve Facility Modal -->
  <div id="reserveFacilityModal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl bg-white text-gray-800" data-theme="light" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="calendar-plus" class="w-6 h-6 text-blue-500"></i>
          Reserve a Facility
        </h3>
        <button id="closeReserveFacilityModal" class="btn btn-sm btn-circle btn-ghost">
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

      <form id="reserveFacilityForm" action="{{ route('facility_reservations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Facility Selection -->
        <div class="form-control mb-6">
          <label class="label">
            <span class="label-text font-semibold flex items-center gap-2">
              <i data-lucide="building" class="w-4 h-4 text-blue-500"></i>
              Facility *
            </span>
          </label>
          <select name="facility_id" class="select select-bordered w-full" required>
            <option value="">Select facility</option>
            @foreach($facilities as $facility)
              <option value="{{ $facility->id }}">{{ $facility->name }} ({{ $facility->location }})</option>
            @endforeach
          </select>
        </div>

        <!-- Date and Time Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>
                Start Time *
              </span>
            </label>
            <input type="datetime-local" name="start_time" class="input input-bordered w-full" required>
          </div>
          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                End Time *
              </span>
            </label>
            <input type="datetime-local" name="end_time" class="input input-bordered w-full" required>
          </div>
        </div>

        <!-- Purpose -->
        <div class="form-control mb-6">
          <label class="label">
            <span class="label-text font-semibold flex items-center gap-2">
              <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i>
              Purpose
            </span>
          </label>
          <textarea name="purpose" class="textarea textarea-bordered w-full h-24" placeholder="Enter purpose for reservation"></textarea>
        </div>

        <!-- Document Upload -->
        <div class="form-control mb-6">
          <label class="label">
            <span class="label-text font-semibold flex items-center gap-2">
              <i data-lucide="upload" class="w-4 h-4 text-blue-500"></i>
              Supporting Document (Optional)
            </span>
          </label>
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
            <input type="file" name="document" class="hidden" id="modal-document-upload" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
            <label for="modal-document-upload" class="cursor-pointer">
              <i data-lucide="upload-cloud" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
              <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
              <p class="text-sm text-gray-500">PDF, DOC, DOCX, TXT, JPG, PNG (Max 10MB)</p>
            </label>
          </div>
          <div id="modal-file-info" class="mt-2 text-sm text-gray-600 hidden">
            <i data-lucide="file" class="w-4 h-4 inline mr-1"></i>
            <span id="modal-file-name"></span>
          </div>
        </div>

        <div class="modal-action">
          <button type="button" class="btn btn-outline" id="cancelReserveFacility">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="calendar-plus" class="w-4 h-4 mr-2"></i>
            Submit Reservation
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

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      updateDateTime();
      
      // Update time every second
      setInterval(updateDateTime, 1000);

      // Modal logic
      const modal = document.getElementById('reserveFacilityModal');
      const openBtn = document.getElementById('openReserveFacilityModal');
      const closeBtn = document.getElementById('closeReserveFacilityModal');
      const cancelBtn = document.getElementById('cancelReserveFacility');
      function openModal(){ modal.classList.add('modal-open'); }
      function closeModal(){ modal.classList.remove('modal-open'); }
      if (openBtn) openBtn.addEventListener('click', openModal);
      if (closeBtn) closeBtn.addEventListener('click', closeModal);
      if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
      if (modal) modal.addEventListener('click', function(e){ if(e.target === modal) closeModal(); });

      // File preview inside modal
      const fileInput = document.getElementById('modal-document-upload');
      if (fileInput){
        fileInput.addEventListener('change', function(e){
          const file = e.target.files[0];
          const info = document.getElementById('modal-file-info');
          const name = document.getElementById('modal-file-name');
          if (file){ name.textContent = file.name; info.classList.remove('hidden'); }
          else { info.classList.add('hidden'); }
        });
      }
    });
  </script>
</body>
</html>
