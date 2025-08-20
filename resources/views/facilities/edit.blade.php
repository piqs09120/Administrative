<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Facility - {{ $facility->name }}</title>
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
          <a href="{{ route('facilities.index') }}" class="btn btn-ghost btn-sm mr-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back
          </a>
        </div>

        <!-- Edit Facility Form -->
        <div class="max-w-2xl mx-auto">
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

          <div class="card bg-white shadow-xl">
            <div class="card-body">
              <form action="{{ route('facilities.update', $facility->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-control mb-4">
                  <label class="label">
                    <span class="label-text font-semibold">Facility Name *</span>
                  </label>
                  <input type="text" name="name" class="input input-bordered" 
                         value="{{ old('name', $facility->name) }}" placeholder="Enter facility name" required>
                </div>

                <div class="form-control mb-4">
                  <label class="label">
                    <span class="label-text font-semibold">Location</span>
                  </label>
                  <input type="text" name="location" class="input input-bordered" 
                         value="{{ old('location', $facility->location) }}" placeholder="Enter facility location">
                </div>

                <div class="form-control mb-4">
                  <label class="label">
                    <span class="label-text font-semibold">Description</span>
                  </label>
                  <textarea name="description" class="textarea textarea-bordered" 
                            placeholder="Enter facility description">{{ old('description', $facility->description) }}</textarea>
                </div>

                <div class="form-control mb-6">
                  <label class="label">
                    <span class="label-text font-semibold">Status *</span>
                  </label>
                  <select name="status" class="select select-bordered" required>
                    <option value="">Select status</option>
                    <option value="available" {{ old('status', $facility->status) === 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ old('status', $facility->status) === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    <option value="occupied" {{ old('status', $facility->status) === 'occupied' ? 'selected' : '' }}>Occupied</option>
                  </select>
                </div>

                <div class="card-actions justify-end">
                  <a href="{{ route('facilities.index') }}" class="btn btn-ghost">Cancel</a>
                  <button type="submit" class="btn btn-primary">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>Update Facility
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

