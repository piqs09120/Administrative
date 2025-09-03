<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New User - Soliera</title>
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

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
          <div>
            <h2 class="text-2xl font-bold text-gray-800">Add New User</h2>
            <p class="text-gray-600">Create a new user account with role and department assignment</p>
          </div>
          <div class="flex gap-2">
            <a href="{{ route('access.users') }}" class="btn btn-outline">
              <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
              Back to Users
            </a>
          </div>
        </div>

        <!-- User Creation Form -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <form method="POST" action="{{ route('access.users.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Personal Information -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">Full Name *</span>
                  </label>
                  <input type="text" name="employee_name" value="{{ old('employee_name') }}" 
                         class="input input-bordered @error('employee_name') input-error @enderror" 
                         placeholder="Enter full name" required>
                  @error('employee_name')
                    <label class="label">
                      <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                  @enderror
                </div>

                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">Employee ID *</span>
                  </label>
                  <input type="text" name="employee_id" value="{{ old('employee_id') }}" 
                         class="input input-bordered @error('employee_id') input-error @enderror" 
                         placeholder="Enter employee ID" required>
                  @error('employee_id')
                    <label class="label">
                      <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                  @enderror
                </div>

                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">Email Address</span>
                  </label>
                  <input type="email" name="email" value="{{ old('email') }}" 
                         class="input input-bordered @error('email') input-error @enderror" 
                         placeholder="Enter email address">
                  @error('email')
                    <label class="label">
                      <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                  @enderror
                </div>

                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">Phone Number</span>
                  </label>
                  <input type="text" name="phone" value="{{ old('phone') }}" 
                         class="input input-bordered @error('phone') input-error @enderror" 
                         placeholder="Enter phone number">
                  @error('phone')
                    <label class="label">
                      <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                  @enderror
                </div>
              </div>

              <!-- Work Information -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Work Information</h3>
                
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">Department *</span>
                  </label>
                  <select name="dept_name" class="select select-bordered @error('dept_name') select-error @enderror" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                      <option value="{{ $dept }}" @selected(old('dept_name') === $dept)>{{ $dept }}</option>
                    @endforeach
                  </select>
                  @error('dept_name')
                    <label class="label">
                      <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                  @enderror
                </div>

                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">Role *</span>
                  </label>
                  <select name="role" class="select select-bordered @error('role') select-error @enderror" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                      <option value="{{ $role }}" @selected(old('role') === $role)>{{ $role }}</option>
                    @endforeach
                  </select>
                  @error('role')
                    <label class="label">
                      <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                  @enderror
                </div>

                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">Status *</span>
                  </label>
                  <select name="status" class="select select-bordered @error('status') select-error @enderror" required>
                    <option value="">Select Status</option>
                    <option value="active" @selected(old('status') === 'active')>Active</option>
                    <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                  </select>
                  @error('status')
                    <label class="label">
                      <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Security Information -->
            <div class="mt-8">
              <h3 class="text-lg font-semibold text-gray-800 mb-4">Security Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">Password *</span>
                  </label>
                  <input type="password" name="password" 
                         class="input input-bordered @error('password') input-error @enderror" 
                         placeholder="Enter password" required>
                  @error('password')
                    <label class="label">
                      <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                  @enderror
                </div>

                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold">Confirm Password *</span>
                  </label>
                  <input type="password" name="password_confirmation" 
                         class="input input-bordered" 
                         placeholder="Confirm password" required>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-4 mt-8">
              <a href="{{ route('access.users') }}" class="btn btn-outline">Cancel</a>
              <button type="submit" class="btn btn-primary">
                <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                Create User
              </button>
            </div>
          </form>
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
