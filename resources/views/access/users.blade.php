<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Access Control - Soliera</title>
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
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-800">Access Control</h2>
            <p class="text-gray-600">Manage user accounts, roles, and access permissions</p>
          </div>
          <div class="flex gap-2">
            <a href="{{ route('access.users.export', request()->query()) }}" class="btn btn-outline">
              <i data-lucide="download" class="w-4 h-4 mr-2"></i>
              Export Users
            </a>
          </div>
        </div>
        <!-- underline divider (matches Visitor Management style) -->
        <div class="border-b border-gray-200 mb-6"></div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
          <form method="GET" action="{{ url()->current() }}" id="filtersForm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold">Search Users</span>
                </label>
                <input type="text" name="q" id="searchUsers" value="{{ $filters['q'] ?? '' }}" placeholder="Name, email or ID..." class="input input-bordered" autocomplete="off" />
              </div>
              
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold">Role</span>
                </label>
                <select name="role" class="select select-bordered" onchange="this.form.submit()">
                  <option value="">All Roles</option>
                  @isset($roleOptions)
                    @foreach($roleOptions as $role)
                      <option value="{{ $role }}" @selected(($filters['role'] ?? '') === $role)>{{ $role }}</option>
                    @endforeach
                  @endisset
                </select>
              </div>
              
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold">Department</span>
                </label>
                <select name="department" class="select select-bordered" onchange="this.form.submit()">
                  <option value="">All Departments</option>
                  @isset($departmentOptions)
                    @foreach($departmentOptions as $dept)
                      <option value="{{ $dept }}" @selected(($filters['department'] ?? '') === $dept)>{{ $dept }}</option>
                    @endforeach
                  @endisset
                </select>
              </div>
              
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold">Status</span>
                </label>
                <select name="status" class="select select-bordered" onchange="this.form.submit()">
                  <option value="">All Status</option>
                  <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                  <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                </select>
              </div>
            </div>
          </form>
        </div>
        
        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
              <thead>
                <tr>
                  <th>Employee Code</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Email</th>
                </tr>
              </thead>
              <tbody id="externalUsersBody">
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          @if(isset($users) && method_exists($users, 'links'))
          <div class="flex justify-between items-center mt-6">
            <div class="text-sm text-gray-600">
              Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() ?? 0 }} users
            </div>
            <div>
              {{ $users->onEachSide(1)->links() }}
            </div>
          </div>
          @endif
        </div>
      </main>
    </div>
  </div>

  <!-- Add User Modal -->
  <dialog id="addUserModal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl">
      <form method="dialog">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
      </form>
      <h3 class="font-bold text-lg mb-4">Add New User</h3>
      
      <form method="POST" action="{{ route('access.users.store') }}" id="addUserForm">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Personal Information -->
          <div class="space-y-3">
            <h4 class="font-semibold text-gray-800 mb-3">Personal Information</h4>
            
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Full Name *</span>
              </label>
              <input type="text" name="employee_name" value="{{ old('employee_name') }}" 
                     class="input input-bordered input-sm @error('employee_name') input-error @enderror" 
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
                     class="input input-bordered input-sm @error('employee_id') input-error @enderror" 
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
                     class="input input-bordered input-sm @error('email') input-error @enderror" 
                     placeholder="Enter email address">
              @error('email')
                <label class="label">
                  <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
              @enderror
            </div>


          </div>

          <!-- Work Information -->
          <div class="space-y-3">
            <h4 class="font-semibold text-gray-800 mb-3">Work Information</h4>
            
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Department *</span>
              </label>
              <select name="dept_name" class="select select-bordered select-sm @error('dept_name') select-error @enderror" required>
                <option value="">Select Department</option>
                @isset($departmentOptions)
                  @foreach($departmentOptions as $dept)
                    <option value="{{ $dept }}" @selected(old('dept_name') === $dept)>{{ $dept }}</option>
                  @endforeach
                @endisset
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
              <select name="role" class="select select-bordered select-sm @error('role') select-error @enderror" required>
                <option value="">Select Role</option>
                @isset($roleOptions)
                  @foreach($roleOptions as $role)
                    <option value="{{ $role }}" @selected(old('role') === $role)>{{ $role }}</option>
                  @endforeach
                @endisset
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
              <select name="status" class="select select-bordered select-sm @error('status') select-error @enderror" required>
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
        <div class="mt-6">
          <h4 class="font-semibold text-gray-800 mb-3">Security Information</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Password *</span>
              </label>
              <input type="password" name="password" 
                     class="input input-bordered input-sm @error('password') input-error @enderror" 
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
                     class="input input-bordered input-sm" 
                     placeholder="Confirm password" required>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="modal-action">
          <button type="button" onclick="addUserModal.close()" class="btn btn-outline">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
            Create User
          </button>
        </div>
      </form>
    </div>
  </dialog>

  @include('partials.soliera_js')
  <script>
    // Instant search: debounce and update URL + reload results
    (function(){
      const input = document.getElementById('searchUsers');
      const form = document.getElementById('filtersForm');
      if (!input || !form) return;
      let t = null;
      const debounce = (fn, delay) => { return function(){ clearTimeout(t); t = setTimeout(fn, delay); }; };
      const submitWithQuery = () => {
        const url = new URL(window.location.href);
        const val = input.value.trim();
        if (val) { url.searchParams.set('q', val); } else { url.searchParams.delete('q'); }
        // Preserve other selects
        ['role','department','status'].forEach(n => {
          const el = form.querySelector(`[name="${n}"]`); if (el && el.value) url.searchParams.set(n, el.value); else url.searchParams.delete(n);
        });
        window.location.assign(url.toString());
      };
      input.addEventListener('input', debounce(submitWithQuery, 250));
    })();

    // Load external accounts into Access Control table
    (function(){
      const tbody = document.getElementById('externalUsersBody');
      if (!tbody) return;
      const endpoint = 'https://hr1.soliera-hotel-restaurant.com/api/accounts';
      fetch(endpoint, { headers: { 'Accept': 'application/json' }})
        .then(r => r.json())
        .then(data => {
          const rows = Array.isArray(data) ? data : [];
          if (rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-gray-500">No records from API</td></tr>';
            return;
          }
          const html = rows.map(r => `
            <tr>
              <td>${r.employee_code ?? ''}</td>
              <td>${r.first_name ?? ''}</td>
              <td>${r.last_name ?? ''}</td>
              <td>${r.email ?? ''}</td>
            </tr>
          `).join('');
          tbody.innerHTML = html;
        })
        .catch(() => {
          tbody.innerHTML = '<tr><td colspan="4" class="text-center text-error">Failed to load API data</td></tr>';
        });
    })();

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