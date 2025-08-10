<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reserve Facility - Soliera</title>
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

        <!-- Back button and title -->
        <div class="flex items-center mb-6">
            <a href="{{ route('facility_reservations.index') }}" class="btn btn-ghost btn-sm mr-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back
            </a>
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

        <!-- Reserve Facility Form -->
        <div class="max-w-4xl mx-auto">
          <div class="card bg-white shadow-xl">
            <div class="card-body">
              <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Reserve a Facility</h1>
                <p class="text-gray-600">Select a facility, specify your reservation details, and upload supporting documents for AI analysis.</p>
              </div>

              <form action="{{ route('facility_reservations.store') }}" method="POST" enctype="multipart/form-data">
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
                                <option value="{{ $facility->id }}"
                                    {{ (request('facility') == $facility->id || old('facility_id') == $facility->id) ? 'selected' : '' }}>
                                    {{ $facility->name }} ({{ $facility->location }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                <!-- Date and Time Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <!-- Start Time -->
                  <div class="form-control">
                        <label class="label">
                      <span class="label-text font-semibold flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>
                        Start Time *
                      </span>
                        </label>
                    <input type="datetime-local" name="start_time" class="input input-bordered w-full"
                               value="{{ old('start_time') }}" required>
                    </div>

                  <!-- End Time -->
                  <div class="form-control">
                        <label class="label">
                      <span class="label-text font-semibold flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                        End Time *
                      </span>
                        </label>
                    <input type="datetime-local" name="end_time" class="input input-bordered w-full"
                               value="{{ old('end_time') }}" required>
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
                  <textarea name="purpose" class="textarea textarea-bordered w-full h-24"
                                  placeholder="Enter purpose for reservation">{{ old('purpose') }}</textarea>
                    </div>

                <!-- Document Upload -->
                <div class="form-control mb-8">
                  <label class="label">
                    <span class="label-text font-semibold flex items-center gap-2">
                      <i data-lucide="upload" class="w-4 h-4 text-blue-500"></i>
                      Supporting Document (Optional)
                    </span>
                  </label>
                  <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                    <input type="file" name="document" class="hidden" id="document-upload" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                    <label for="document-upload" class="cursor-pointer">
                      <i data-lucide="upload-cloud" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                      <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                      <p class="text-sm text-gray-500">PDF, DOC, DOCX, TXT, JPG, PNG (Max 10MB)</p>
                    </label>
                  </div>
                  <div id="file-info" class="mt-2 text-sm text-gray-600 hidden">
                    <i data-lucide="file" class="w-4 h-4 inline mr-1"></i>
                    <span id="file-name"></span>
                  </div>
                </div>

                <!-- AI Processing Notice -->
                <div class="alert alert-info mb-6">
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

                <!-- Submit Buttons -->
                    <div class="card-actions justify-end">
                  <a href="{{ route('facility_reservations.index') }}" class="btn btn-ghost">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>Cancel
                  </a>
                        <button type="submit" class="btn btn-primary">
                    <i data-lucide="calendar-plus" class="w-4 h-4 mr-2"></i>Submit Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>
      </main>
    </div>
</div>

  @include('partials.soliera_js')
  <script>
    // File upload handling
    document.getElementById('document-upload').addEventListener('change', function(e) {
      const file = e.target.files[0];
      const fileInfo = document.getElementById('file-info');
      const fileName = document.getElementById('file-name');
      
      if (file) {
        fileName.textContent = file.name;
        fileInfo.classList.remove('hidden');
      } else {
        fileInfo.classList.add('hidden');
      }
    });

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
      
      if (dateElement) dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
      if (timeElement) timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
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
