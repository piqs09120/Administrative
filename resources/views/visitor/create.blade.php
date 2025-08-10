<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Visitor - Soliera</title>
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
          <a href="{{ route('visitor.index') }}" class="btn btn-ghost btn-sm mr-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back
          </a>
        </div>

        <!-- Register Visitor Form -->
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
              <form action="{{ route('visitor.store') }}" method="POST">
                @csrf

                <div class="form-control mb-4">
                  <label class="label">
                    <span class="label-text font-semibold">Name *</span>
                  </label>
                  <input type="text" name="name" class="input input-bordered"
                         value="{{ old('name') }}" placeholder="Enter visitor name" required>
                </div>

                <div class="form-control mb-4">
                  <label class="label">
                    <span class="label-text font-semibold">Contact</span>
                  </label>
                  <input type="text" name="contact" class="input input-bordered"
                         value="{{ old('contact') }}" placeholder="Enter contact info">
                </div>

                <div class="form-control mb-4">
                  <label class="label">
                    <span class="label-text font-semibold">Purpose</span>
                  </label>
                  <input type="text" name="purpose" class="input input-bordered"
                         value="{{ old('purpose') }}" placeholder="Purpose of visit">
                </div>

                <div class="form-control mb-4">
                  <label class="label">
                    <span class="label-text font-semibold">Facility/Department</span>
                  </label>
                  <select name="facility_id" class="select select-bordered">
                    <option value="">Select facility/department</option>
                    @foreach($facilities as $facility)
                      <option value="{{ $facility->id }}" {{ old('facility_id') == $facility->id ? 'selected' : '' }}>
                        {{ $facility->name }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="form-control mb-6">
                  <label class="label">
                    <span class="label-text font-semibold">Time In *</span>
                  </label>
                  <input type="datetime-local" name="time_in" class="input input-bordered"
                         value="{{ old('time_in') ?? now()->format('Y-m-d\TH:i') }}" required>
                </div>

                <div class="card-actions justify-end">
                  <a href="{{ route('visitor.index') }}" class="btn btn-ghost">Cancel</a>
                  <button type="submit" class="btn btn-primary">
                    <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>Register Visitor
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
