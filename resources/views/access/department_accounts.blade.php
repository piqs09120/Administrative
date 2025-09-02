<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Department Accounts - Soliera</title>
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
        <div class="mb-8">
          <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Department Accounts</h1>
            <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Manage and monitor department user accounts across the organization</p>
          </div>

          <!-- Statistics Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Accounts -->
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-primary">
              <div class="card-body p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="avatar placeholder">
                    <div class="bg-primary text-primary-content rounded-full w-12 h-12">
                      <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                  </div>
                  <div class="badge badge-primary badge-outline">Total</div>
                </div>
                <div class="text-center">
                  <h2 class="card-title text-4xl font-bold text-primary justify-center mb-2">{{ $stats['total'] ?? 0 }}</h2>
                  <p class="text-base-content/70">Department Accounts</p>
                </div>
              </div>
            </div>

            <!-- Active Accounts -->
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-success">
              <div class="card-body p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="avatar placeholder">
                    <div class="bg-success text-success-content rounded-full w-12 h-12">
                      <i data-lucide="check-circle" class="w-6 h-6"></i>
                    </div>
                  </div>
                  <div class="badge badge-success badge-outline">Active</div>
                </div>
                <div class="text-center">
                  <h2 class="card-title text-4xl font-bold text-success justify-center mb-2">{{ $stats['active'] ?? 0 }}</h2>
                  <p class="text-base-content/70">Active Accounts</p>
                </div>
              </div>
            </div>

            <!-- Inactive Accounts -->
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-warning">
              <div class="card-body p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="avatar placeholder">
                    <div class="bg-warning text-warning-content rounded-full w-12 h-12">
                      <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                  </div>
                  <div class="badge badge-warning badge-outline">Inactive</div>
                </div>
                <div class="text-center">
                  <h2 class="card-title text-4xl font-bold text-warning justify-center mb-2">{{ $stats['inactive'] ?? 0 }}</h2>
                  <p class="text-base-content/70">Inactive Accounts</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Department Accounts Management Section -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <!-- Header with Search and Actions -->
          <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                  <i data-lucide="building" class="w-5 h-5 text-blue-600"></i>
                  Department Accounts List
                </h3>
                <!-- Search Bar -->
                <div class="relative">
                  <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                  <input type="text" 
                         id="searchInput"
                         placeholder="Search accounts..." 
                         class="input input-bordered input-sm w-64 pl-10 pr-4 bg-gray-50 border-gray-200 focus:bg-white focus:border-blue-300">
                </div>
              </div>
              
              <!-- Add New Account Button - Only for Administrator -->
              @if(auth()->user()->role === 'Administrator')
                <button onclick="openAddAccountModal()" class="btn btn-primary btn-sm">
                  <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                  Add Account
                </button>
              @endif
            </div>

            <!-- Filters Row -->
            <div class="flex items-center gap-4">
              <!-- Department Filter -->
              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Department:</label>
                <select id="departmentFilter" class="select select-bordered select-sm w-40">
                  <option value="">All Departments</option>
                  @foreach($departments->keys() as $deptName)
                    <option value="{{ $deptName }}">{{ $deptName }}</option>
                  @endforeach
                </select>
              </div>

              <!-- Status Filter -->
              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Status:</label>
                <select id="statusFilter" class="select select-bordered select-sm w-32">
                  <option value="">All Status</option>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>

              <!-- Clear Filters Button -->
              <button onclick="clearFilters()" class="btn btn-ghost btn-xs text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-3 h-3 mr-1"></i>
                Clear
              </button>
            </div>
          </div>

          <!-- Department Accounts Table -->
          <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
              <thead>
                <tr class="bg-gray-50">
                  <th class="text-left py-3 px-4 font-medium text-gray-700">Employee</th>
                  <th class="text-left py-3 px-4 font-medium text-gray-700">Department</th>
                  <th class="text-left py-3 px-4 font-medium text-gray-700">Role</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Status</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Last Login</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($departmentAccounts as $account)
                  <tr class="hover:bg-gray-50 transition-colors" data-account-id="{{ $account->Dept_no }}">
                    <td class="py-3 px-4">
                      <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                          <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                          <div class="font-medium text-gray-900">{{ $account->employee_name ?? 'Unknown' }}</div>
                          <div class="text-sm text-gray-500">{{ $account->email ?? 'No email' }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="py-3 px-4">
                      <span class="badge badge-outline badge-sm">{{ $account->dept_name ?? 'Unknown' }}</span>
                    </td>
                    <td class="py-3 px-4">
                      <span class="text-sm text-gray-600">{{ $account->role ?? 'No role assigned' }}</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                      @php
                        $statusConfig = [
                          'active' => ['icon' => 'check-circle', 'color' => 'text-success', 'badge' => 'badge-success'],
                          'inactive' => ['icon' => 'clock', 'color' => 'text-warning', 'badge' => 'badge-warning']
                        ];
                        $status = $account->status ?? 'active';
                        $config = $statusConfig[$status] ?? $statusConfig['active'];
                      @endphp
                      <div class="flex items-center justify-center">
                        <span class="badge {{ $config['badge'] }} badge-sm">{{ ucfirst($status) }}</span>
                      </div>
                    </td>
                    <td class="py-3 px-4 text-center text-sm text-gray-600">
                      {{ $account->last_login ? \Carbon\Carbon::parse($account->last_login)->format('M d, Y H:i') : 'Never' }}
                    </td>
                    <td class="py-3 px-4 text-center">
                      <div class="flex items-center justify-center gap-1">
                        <button onclick="viewAccount({{ $account->Dept_no }})" class="btn btn-ghost btn-xs tooltip" data-tip="View Details">
                          <i data-lucide="eye" class="w-4 h-4 text-blue-600"></i>
                        </button>
                        @if(auth()->user()->role === 'Administrator')
                          <button onclick="editAccount({{ $account->Dept_no }})" class="btn btn-ghost btn-xs tooltip" data-tip="Edit Account">
                            <i data-lucide="edit" class="w-4 h-4 text-green-600"></i>
                          </button>
                          <button onclick="toggleAccountStatus({{ $account->Dept_no }})" class="btn btn-ghost btn-xs tooltip" data-tip="Toggle Status">
                            <i data-lucide="toggle-left" class="w-4 h-4 text-orange-600"></i>
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center py-12">
                      <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                          <i data-lucide="users" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No Department Accounts Found</h3>
                        <p class="text-gray-500 text-sm">No department accounts available at the moment.</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Add Account Modal -->
  <div id="addAccountModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="user-plus" class="w-8 h-8 text-blue-500"></i>
          Add New Department Account
        </h3>
        <button onclick="closeAddAccountModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <form id="addAccountForm" method="POST" action="{{ route('access.department_accounts.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Employee Name *</span>
            </label>
            <input type="text" name="employee_name" class="input input-bordered w-full" required>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Email</span>
            </label>
            <input type="email" name="email" class="input input-bordered w-full">
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Department *</span>
            </label>
            <select name="dept_name" class="select select-bordered w-full" required>
              <option value="">Select Department</option>
              <option value="Management">Management</option>
              <option value="Reception">Reception</option>
              <option value="Housekeeping">Housekeeping</option>
              <option value="Restaurant">Restaurant</option>
              <option value="Legal">Legal</option>
              <option value="IT">IT</option>
              <option value="Finance">Finance</option>
            </select>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Role</span>
            </label>
            <select name="role" class="select select-bordered w-full">
              <option value="">Select Role</option>
              <option value="Manager">Manager</option>
              <option value="Supervisor">Supervisor</option>
              <option value="Staff">Staff</option>
              <option value="Officer">Officer</option>
            </select>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Status</span>
            </label>
            <select name="status" class="select select-bordered w-full">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Phone</span>
            </label>
            <input type="tel" name="phone" class="input input-bordered w-full">
          </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <button type="button" onclick="closeAddAccountModal()" class="btn btn-outline">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
            Add Account
          </button>
        </div>
      </form>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Modal functions
    function openAddAccountModal() {
      document.getElementById('addAccountModal').classList.add('modal-open');
    }

    function closeAddAccountModal() {
      document.getElementById('addAccountModal').classList.remove('modal-open');
      document.getElementById('addAccountForm').reset();
    }

    // Account action functions
    function viewAccount(accountId) {
      // Implement view account functionality
      console.log('Viewing account:', accountId);
    }

    function editAccount(accountId) {
      // Implement edit account functionality
      console.log('Editing account:', accountId);
    }

    function toggleAccountStatus(accountId) {
      // Implement toggle status functionality
      console.log('Toggling status for account:', accountId);
    }

    // Filtering functionality
    function filterAccounts() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const departmentFilter = document.getElementById('departmentFilter').value;
      const statusFilter = document.getElementById('statusFilter').value;
      
      const rows = document.querySelectorAll('tbody tr[data-account-id]');
      
      rows.forEach(row => {
        let showRow = true;
        
        // Search filter
        if (searchTerm) {
          const employeeName = row.querySelector('td:first-child .font-medium')?.textContent?.toLowerCase() || '';
          const email = row.querySelector('td:first-child .text-sm')?.textContent?.toLowerCase() || '';
          if (!employeeName.includes(searchTerm) && !email.includes(searchTerm)) {
            showRow = false;
          }
        }
        
        // Department filter
        if (departmentFilter && showRow) {
          const department = row.querySelector('td:nth-child(2) .badge')?.textContent?.toLowerCase() || '';
          if (department !== departmentFilter.toLowerCase()) {
            showRow = false;
          }
        }
        
        // Status filter
        if (statusFilter && showRow) {
          const status = row.querySelector('td:nth-child(4) .badge')?.textContent?.toLowerCase() || '';
          if (status !== statusFilter.toLowerCase()) {
            showRow = false;
          }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
      });
    }
    
    function clearFilters() {
      document.getElementById('searchInput').value = '';
      document.getElementById('departmentFilter').value = '';
      document.getElementById('statusFilter').value = '';
      
      // Show all rows
      const rows = document.querySelectorAll('tbody tr[data-account-id]');
      rows.forEach(row => {
        row.style.display = '';
      });
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchInput');
      const departmentFilter = document.getElementById('departmentFilter');
      const statusFilter = document.getElementById('statusFilter');
      
      if (searchInput) {
        searchInput.addEventListener('input', filterAccounts);
      }
      if (departmentFilter) {
        departmentFilter.addEventListener('change', filterAccounts);
      }
      if (statusFilter) {
        statusFilter.addEventListener('change', filterAccounts);
      }
    });

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
      const addAccountModal = document.getElementById('addAccountModal');
      if (event.target === addAccountModal) {
        closeAddAccountModal();
      }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeAddAccountModal();
      }
    });
  </script>
</body>
</html>

