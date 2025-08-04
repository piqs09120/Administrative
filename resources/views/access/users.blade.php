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
      <!-- Header - Exactly like the image -->
      <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <button onclick="toggleSidebar()" class="btn btn-ghost btn-sm hover:bg-base-300 transition-all hover:scale-105">
              <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h1 class="text-3xl font-bold text-gray-800 ml-4">Access Control</h1>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Search Bar - Like in the image -->
            <div class="relative">
              <input type="text" id="searchInput" placeholder="Search..." class="input input-bordered input-md w-64 pl-10 bg-white border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
              <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Date and Time - Like in the image -->
            <div class="flex items-center space-x-2 bg-gray-100 px-4 py-3 rounded-lg">
              <i data-lucide="calendar" class="w-5 h-5 text-blue-500"></i>
              <span id="currentDate" class="text-base font-medium text-gray-700"></span>
              <div class="w-px h-5 bg-gray-300"></div>
              <i data-lucide="clock" class="w-5 h-5 text-green-500"></i>
              <span id="currentTime" class="text-base font-medium text-gray-700"></span>
            </div>
            
            <!-- Moon Icon (Dark Mode Toggle) -->
            <button id="darkModeToggle" class="p-2 rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition-colors">
                <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13.66l-.71.71M4.05 19.07l-.71.71M21 12h-1M4 12H3m16.66 5.66l-.71-.71M4.05 4.93l-.71-.71M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="white" viewBox="0 0 24 24" stroke="white">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
                </svg>
            </button>
            <div class="dropdown dropdown-end">
              <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                <i data-lucide="user" class="w-6 h-6 text-gray-600"></i>
              </div>
              <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-white rounded-lg w-52 border border-gray-200">
                <li><a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                  <i data-lucide="user" class="w-4 h-4 text-gray-600"></i>
                  <span>Profile</span>
                </a></li>
                <li><a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                  <i data-lucide="settings" class="w-4 h-4 text-gray-600"></i>
                  <span>Settings</span>
                </a></li>
                <li><a href="#" onclick="logout()" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                  <i data-lucide="log-out" class="w-4 h-4 text-gray-600"></i>
                  <span>Sign out</span>
                </a></li>
              </ul>
            </div>
          </div>
        </div>
      </header>

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
            <h2 class="text-2xl font-bold text-gray-800">User Management</h2>
            <p class="text-gray-600">Manage user accounts, roles, and access permissions</p>
          </div>
          <div class="flex gap-2">
            <button class="btn btn-primary">
              <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
              Add New User
            </button>
            <button class="btn btn-outline">
              <i data-lucide="download" class="w-4 h-4 mr-2"></i>
              Export Users
            </button>
          </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Search Users</span>
              </label>
              <input type="text" placeholder="Name or email..." class="input input-bordered" />
            </div>
            
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Role</span>
              </label>
              <select class="select select-bordered">
                <option value="">All Roles</option>
                <option value="administrator">Administrator</option>
                <option value="manager">Manager</option>
                <option value="staff">Staff</option>
              </select>
            </div>
            
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Department</span>
              </label>
              <select class="select select-bordered">
                <option value="">All Departments</option>
                <option value="management">Management</option>
                <option value="reception">Reception</option>
                <option value="restaurant">Restaurant</option>
                <option value="housekeeping">Housekeeping</option>
              </select>
            </div>
            
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Status</span>
              </label>
              <select class="select select-bordered">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
              </select>
            </div>
          </div>
        </div>
        
        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Role</th>
                  <th>Department</th>
                  <th>Status</th>
                  <th>Last Login</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($users as $user)
                <tr class="hover">
                  <td>
                    <div class="flex items-center gap-3">
                      <div class="avatar">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                          <span class="text-blue-600 font-medium">
                            {{ substr($user['name'], 0, 1) }}{{ substr(explode(' ', $user['name'])[1] ?? '', 0, 1) }}
                          </span>
                        </div>
                      </div>
                      <div>
                        <div class="font-bold">{{ $user['name'] }}</div>
                        <div class="text-sm text-gray-600">{{ $user['email'] }}</div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="flex items-center gap-2">
                      @if($user['role'] == 'Administrator')
                        <i data-lucide="crown" class="w-4 h-4 text-yellow-500"></i>
                      @elseif(str_contains($user['role'], 'Manager'))
                        <i data-lucide="user-check" class="w-4 h-4 text-blue-500"></i>
                      @else
                        <i data-lucide="user" class="w-4 h-4 text-gray-500"></i>
                      @endif
                      {{ $user['role'] }}
                    </div>
                  </td>
                  <td>{{ $user['department'] }}</td>
                  <td>
                    @if($user['status'] == 'Active')
                      <div class="badge badge-success">Active</div>
                    @else
                      <div class="badge badge-error">Inactive</div>
                    @endif
                  </td>
                  <td>
                    <div class="text-sm">
                      {{ date('M j, Y', strtotime($user['last_login'])) }}
                      <br>
                      <span class="text-gray-600">{{ date('H:i', strtotime($user['last_login'])) }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="dropdown dropdown-end">
                      <div tabindex="0" role="button" class="btn btn-ghost btn-xs">
                        <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                      </div>
                      <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-white rounded-lg w-52 border border-gray-200">
                        <li><a class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                          <i data-lucide="eye" class="w-4 h-4"></i> View Profile
                        </a></li>
                        <li><a class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                          <i data-lucide="edit" class="w-4 h-4"></i> Edit User
                        </a></li>
                        <li><a href="{{ route('access.users.editRole', $user['id']) }}" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                          <i data-lucide="user-check" class="w-4 h-4"></i> Edit Role
                        </a></li>
                        <li><a class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                          <i data-lucide="key" class="w-4 h-4"></i> Reset Password
                        </a></li>
                        <li><a class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                          <i data-lucide="activity" class="w-4 h-4"></i> View Activity
                        </a></li>
                        <li><hr class="my-1"></li>
                        @if($user['status'] == 'Active')
                          <li><a class="flex items-center gap-3 px-3 py-2 text-yellow-600 hover:bg-yellow-50 rounded-md transition-colors">
                            <i data-lucide="pause" class="w-4 h-4"></i> Suspend User
                          </a></li>
                        @else
                          <li><a class="flex items-center gap-3 px-3 py-2 text-green-600 hover:bg-green-50 rounded-md transition-colors">
                            <i data-lucide="play" class="w-4 h-4"></i> Activate User
                          </a></li>
                        @endif
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <div class="flex justify-between items-center mt-6">
            <div class="text-sm text-gray-600">
              Showing 1 to 4 of 24 users
            </div>
            <div class="join">
              <button class="join-item btn btn-sm">«</button>
              <button class="join-item btn btn-sm btn-active">1</button>
              <button class="join-item btn btn-sm">2</button>
              <button class="join-item btn btn-sm">3</button>
              <button class="join-item btn btn-sm">»</button>
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