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
          <div class="mb-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Department Accounts</h1>
            <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Manage and monitor department user accounts across the organization</p>
          </div>
          <!-- underline divider (matches Visitor Management style) -->
          <div class="border-b border-gray-200 mb-6"></div>

          <!-- Statistics Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Accounts -->
            <x-stat-card 
              title="Total Accounts" 
              :value="$stats['total'] ?? 0" 
              icon="fa-users" 
              iconColor="text-yellow-400" 
              bgColor="bg-blue-900" />
            
            <!-- Active Accounts -->
            <x-stat-card 
              title="Active Accounts" 
              :value="$stats['active'] ?? 0" 
              icon="fa-check-circle" 
              iconColor="text-yellow-400" 
              bgColor="bg-blue-900" />
            
            <!-- Inactive Accounts -->
            <x-stat-card 
              title="Inactive Accounts" 
              :value="$stats['inactive'] ?? 0" 
              icon="fa-clock" 
              iconColor="text-yellow-400" 
              bgColor="bg-blue-900" />
          </div>
        </div>

        <!-- Department Accounts Management Section -->
        <x-table-card :title="'Department Accounts List'">
          <!-- Department Accounts Table -->
          <table class="table table-zebra w-full">
              <thead>
                <tr class="bg-gray-50">
                  <th class="text-left py-3 px-4 font-medium text-gray-700">Employee</th>
                  <th class="text-left py-3 px-4 font-medium text-gray-700">Department</th>
                  <th class="text-left py-3 px-4 font-medium text-gray-700">Role</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Status</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($departmentAccounts as $account)
                  <tr class="hover:bg-gray-50 transition-colors" data-account-id="{{ $account->Dept_no }}">
                    <td class="py-3 px-4">
                      <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-blue-900 flex items-center justify-center">
                          <i data-lucide="user" class="w-5 h-5 text-white"></i>
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
                          'active' => ['icon' => 'check-circle', 'color' => 'text-success', 'badge' => 'badge-success', 'badgeStyle' => 'background-color: #22c55e; color: white;'],
                          'inactive' => ['icon' => 'clock', 'color' => 'text-warning', 'badge' => 'badge-warning', 'badgeStyle' => '']
                        ];
                        $status = $account->status ?? 'active';
                        $config = $statusConfig[$status] ?? $statusConfig['active'];
                      @endphp
                      <div class="flex items-center justify-center">
                        <span class="badge {{ $config['badge'] }} badge-sm" style="{{ $config['badgeStyle'] }}">{{ ucfirst($status) }}</span>
                      </div>
                    </td>
                    <td class="py-3 px-4 text-center">
                      <div class="flex items-center justify-center gap-2">
                        <button onclick="viewAccount({{ $account->Dept_no }})" 
                                class="p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110 tooltip" 
                                data-tip="View Details"
                                style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);"
                                onmouseover="this.style.background='#E6940F'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'"
                                onmouseout="this.style.background='#F7A923'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                          <i data-lucide="eye" class="w-4 h-4" style="fill: none;"></i>
                        </button>
                        @if(auth()->user()->role === 'Administrator')
                          <button onclick="openEditModal({{ $account->Dept_no }})" 
                                  class="p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110 tooltip" 
                                  data-tip="Edit Account"
                                  style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);"
                                  onmouseover="this.style.background='#E6940F'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'"
                                  onmouseout="this.style.background='#F7A923'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                            <i data-lucide="edit" class="w-4 h-4" style="fill: none;"></i>
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-12">
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
        </x-table-card>
      </main>
    </div>
  </div>

  <!-- Toast Container -->
  <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

  <!-- Edit Account Modal -->
  <div id="editAccountModal" class="modal">
    <div class="modal-box w-11/12 max-w-md" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-gray-800">Edit Account</h3>
        <button onclick="closeEditModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      <form id="editAccountForm" class="space-y-4">
        <div>
          <label class="label text-sm font-semibold">Employee Name</label>
          <input id="ea_name" type="text" class="input input-bordered w-full" />
        </div>
        <div>
          <label class="label text-sm font-semibold">Role</label>
          <input id="ea_role" type="text" class="input input-bordered w-full" />
        </div>
        <div>
          <label class="label text-sm font-semibold">Status</label>
          <select id="ea_status" class="select select-bordered w-full">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" class="btn btn-outline" onclick="closeEditModal()">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- View Account Modal -->
  <div id="viewAccountModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl animate-scaleIn" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-blue-900 flex items-center justify-center">
            <i data-lucide="user" class="w-5 h-5 text-white"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800" id="va_title">Employee Details</h3>
        </div>
        <button onclick="closeViewModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Loading State -->
      <div id="va_loading" class="py-8 text-center">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-blue-50">
          <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-blue-600"></i>
        </div>
        <p class="text-gray-600">Loading employee details...</p>
      </div>

      <!-- Error State -->
      <div id="va_error" class="hidden">
        <div class="alert alert-error">
          <i data-lucide="alert-triangle" class="w-5 h-5"></i>
          <span id="va_error_text">Unable to load employee details.</span>
        </div>
      </div>

      <!-- Content -->
      <div id="va_content" class="hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[55vh] overflow-y-auto">
          <div class="space-y-2">
            <div>
              <div class="text-xs uppercase tracking-wide text-gray-500">Employee Name</div>
              <div class="text-gray-900 font-semibold" id="va_name">—</div>
            </div>
            <div>
              <div class="text-xs uppercase tracking-wide text-gray-500">Email</div>
              <div class="text-gray-900" id="va_email">—</div>
            </div>
          </div>
          <div class="space-y-2">
            <div>
              <div class="text-xs uppercase tracking-wide text-gray-500">Department</div>
              <div class="text-gray-900" id="va_department">—</div>
            </div>
            <div>
              <div class="text-xs uppercase tracking-wide text-gray-500">Role</div>
              <div class="text-gray-900" id="va_role">—</div>
            </div>
            <div>
              <div class="text-xs uppercase tracking-wide text-gray-500">Status</div>
              <div id="va_status" class="inline-flex items-center gap-2">
                <span class="badge badge-sm">—</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Meta -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
          <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
            <div class="text-xs uppercase tracking-wide text-gray-500">Employee ID</div>
            <div class="text-gray-900" id="va_employee_id">—</div>
          </div>
          <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
            <div class="text-xs uppercase tracking-wide text-gray-500">Department No.</div>
            <div class="text-gray-900" id="va_dept_no">—</div>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-200">
        <button id="va_edit_btn" class="btn btn-primary">
          <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
          Edit
        </button>
        <button onclick="closeViewModal()" class="btn btn-outline">Close</button>
      </div>
    </div>
  </div>



  @include('partials.soliera_js')
  
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    

    // Account action functions
    let currentViewAccountId = null;
    function openViewModalShell() {
      const modal = document.getElementById('viewAccountModal');
      if (modal) modal.classList.add('modal-open');
      // Reset states
      document.getElementById('va_loading').classList.remove('hidden');
      document.getElementById('va_error').classList.add('hidden');
      document.getElementById('va_content').classList.add('hidden');
    }

    function closeViewModal() {
      const modal = document.getElementById('viewAccountModal');
      if (modal) modal.classList.remove('modal-open');
      currentViewAccountId = null;
    }

    async function viewAccount(accountId) {
      currentViewAccountId = accountId;
      openViewModalShell();
      try {
        const res = await fetch(`{{ url('/access/department-accounts') }}/${accountId}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Failed to load account');

        const acc = data.account || {};
        document.getElementById('va_name').textContent = acc.employee_name || 'Unknown';
        document.getElementById('va_email').textContent = acc.email || '—';
        document.getElementById('va_department').textContent = acc.dept_name || '—';
        document.getElementById('va_role').textContent = acc.role || '—';
        document.getElementById('va_employee_id').textContent = acc.employee_id || '—';
        document.getElementById('va_dept_no').textContent = acc.Dept_no || '—';

        const status = (acc.status || 'inactive').toLowerCase();
        const badge = document.querySelector('#va_status .badge');
        if (badge) {
          badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
          badge.className = `badge badge-sm ${status === 'active' ? 'badge-success' : 'badge-warning'}`;
        }

        // Wire edit button
        const editBtn = document.getElementById('va_edit_btn');
        if (editBtn) {
          editBtn.onclick = function () {
            closeViewModal();
            openEditModal(accountId);
          };
        }

        document.getElementById('va_loading').classList.add('hidden');
        document.getElementById('va_content').classList.remove('hidden');
        lucide.createIcons();
      } catch (e) {
        console.error(e);
        document.getElementById('va_loading').classList.add('hidden');
        document.getElementById('va_error_text').textContent = e.message || 'Unable to load employee details.';
        document.getElementById('va_error').classList.remove('hidden');
        lucide.createIcons();
      }
    }

    // Edit modal helpers
    let editingAccountId = null;
    async function openEditModal(accountId) {
      try {
        const res = await fetch(`{{ url('/access/department-accounts') }}/${accountId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
        const data = await res.json();
        if (!data.success) throw new Error('Failed to load account');
        editingAccountId = accountId;
        document.getElementById('ea_name').value = data.account.employee_name || '';
        document.getElementById('ea_role').value = data.account.role || '';
        document.getElementById('ea_status').value = data.account.status || 'inactive';
        document.getElementById('editAccountModal').classList.add('modal-open');
      } catch (e) {
        showToast('Unable to load account for editing', 'error');
      }
    }
    function closeEditModal(){
      document.getElementById('editAccountModal').classList.remove('modal-open');
      editingAccountId = null;
    }


    // Wire confirm button
    document.addEventListener('DOMContentLoaded', function() {
      // Edit submit
      const editForm = document.getElementById('editAccountForm');
      if (editForm) {
        editForm.addEventListener('submit', async function(e){
          e.preventDefault();
          if (!editingAccountId) return;
          const payload = {
            employee_name: document.getElementById('ea_name').value,
            role: document.getElementById('ea_role').value,
            status: document.getElementById('ea_status').value,
          };
          try {
            const res = await fetch(`{{ url('/access/department-accounts') }}/${editingAccountId}`, {
              method: 'PUT',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!data.success) throw new Error(data.message || 'Update failed');
            showToast('Account updated successfully', 'success');
            closeEditModal();
            // Update UI row
            const row = document.querySelector(`tr[data-account-id="${editingAccountId}"]`);
            if (row) {
              row.querySelector('td:first-child .font-medium').textContent = data.account.employee_name || 'Unknown';
              row.querySelector('td:nth-child(3) .text-sm').textContent = data.account.role || 'No role assigned';
              const badge = row.querySelector('td:nth-child(4) .badge');
              const st = data.account.status || 'inactive';
              if (badge) {
                badge.textContent = st.charAt(0).toUpperCase() + st.slice(1);
                badge.className = `badge ${st === 'active' ? 'badge-success' : 'badge-warning'} badge-sm`;
              }
              if (window.__updateDeptCards) window.__updateDeptCards();
            }
          } catch (err) {
            showToast('Unable to update account. Please try again.', 'error');
          }
        });
      }
    });

    // Toast
    function showToast(message, type = 'info', duration = 3000) {
      // Use global showNotification if available (has progress bar), otherwise use local implementation
      if (typeof window.showNotification !== 'undefined' && window.showNotification.toString().indexOf('progressBar') !== -1) {
        window.showNotification(message, type, duration);
        return;
      }
      
      // Fallback to local implementation with progress bar
      if (!document.getElementById('notification-progress-style')) {
        const style = document.createElement('style');
        style.id = 'notification-progress-style';
        style.textContent = `
          @keyframes progressBar {
            from { width: 100%; }
            to { width: 0%; }
          }
          @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
          }
        `;
        document.head.appendChild(style);
      }

      const notification = document.createElement('div');
      const alertType = type === 'error' ? 'error' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info';
      notification.className = `alert alert-${alertType} fixed bottom-4 right-4 z-[9999] max-w-sm shadow-lg relative overflow-hidden`;
      notification.style.cssText = 'position: fixed; bottom: 1rem; right: 1rem; z-index: 9999; max-width: 24rem; animation: slideInRight 0.3s ease-out;';
      
      const iconMap = { 'success': 'check-circle', 'error': 'alert-circle', 'warning': 'alert-triangle', 'info': 'info' };
      const icon = iconMap[type] || 'info';
      
      notification.innerHTML = `
        <div class="flex items-center gap-2 px-4 py-3">
          <i data-lucide="${icon}" class="w-5 h-5"></i>
          <span>${message}</span>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-black/20">
          <div class="notification-progress h-full bg-white/50" style="width: 100%; animation: progressBar ${duration}ms linear forwards;"></div>
        </div>
      `;
      
      document.body.appendChild(notification);
      notification.offsetHeight;
      
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
      
      setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease-out';
        setTimeout(() => {
          if (notification.parentNode) notification.remove();
        }, 300);
      }, duration);
    }


    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Auto-update card counts based on table rows
      function updateCountsFromTable() {
        const rows = Array.from(document.querySelectorAll('tbody tr[data-account-id]'));
        const total = rows.length;
        const active = rows.filter(r => (r.querySelector('td:nth-child(4) .badge')?.textContent || '').trim().toLowerCase() === 'active').length;
        const inactive = total - active;

        const totalEl = document.getElementById('da_total_count');
        const activeEl = document.getElementById('da_active_count');
        const inactiveEl = document.getElementById('da_inactive_count');
        if (totalEl) totalEl.textContent = total;
        if (activeEl) activeEl.textContent = active;
        if (inactiveEl) inactiveEl.textContent = inactive;
      }

      // Initial update after DOM is ready
      updateCountsFromTable();

      // Also recalc after async operations we control
      window.__updateDeptCards = updateCountsFromTable;
    });

  </script>
</body>
</html>


