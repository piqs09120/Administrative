<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>New Request - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .swal2-popup {
      font-family: inherit;
      border-radius: 12px !important;
    }
    .swal2-confirm {
      background-color: #22c55e !important;
      border: none !important;
      padding: 12px 24px !important;
      border-radius: 8px !important;
      font-weight: 600 !important;
      font-size: 14px !important;
      color: white !important;
      margin-right: 8px !important;
    }
    .swal2-cancel {
      background-color: #6b7280 !important;
      border: none !important;
      padding: 12px 24px !important;
      border-radius: 8px !important;
      font-weight: 600 !important;
      font-size: 14px !important;
      color: white !important;
      margin-left: 8px !important;
    }
    .swal2-actions {
      gap: 10px !important;
      margin-top: 20px !important;
    }
    .swal2-title {
      font-size: 20px !important;
      font-weight: 600 !important;
      margin-bottom: 16px !important;
    }
    .swal2-content {
      font-size: 16px !important;
      line-height: 1.5 !important;
    }
  </style>
  @vite(['resources/css/soliera.css'])
</head>
<body class="bg-gray-50">
  <div class="flex h-screen overflow-hidden">
    <!-- Hidden CSRF Token -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
              <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Facility Requests</h1>
                <p class="text-gray-600">View and manage facility management requests</p>
              </div>
            </div>
          </div>
          <!-- underline divider (matches other modules) -->
          <div class="border-b border-gray-200"></div>
        </div>


        <!-- Clickable Breadcrumb Navigation -->
        <div class="mb-6">
          <nav class="flex items-center space-x-2 text-sm">
            <button id="nav-facility" class="text-blue-600 hover:text-blue-800 font-medium flex items-center transition-colors duration-200 {{ $activeTab==='reservation' ? 'text-blue-800 font-semibold' : '' }}" onclick="nrShowTab('reservation')">
              <i data-lucide="building" class="w-4 h-4 mr-1"></i>
              Facility Request
            </button>
            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
            <button id="nav-maintenance" class="text-gray-600 hover:text-blue-600 font-medium flex items-center transition-colors duration-200 {{ $activeTab==='maintenance' ? 'text-blue-600 font-semibold' : '' }}" onclick="nrShowTab('maintenance')">
              <i data-lucide="wrench" class="w-4 h-4 inline mr-1"></i>
              Maintenance
            </button>
            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
            <button id="nav-equipment" class="text-gray-600 hover:text-blue-600 font-medium flex items-center transition-colors duration-200 {{ $activeTab==='equipment_request' ? 'text-blue-600 font-semibold' : '' }}" onclick="nrShowTab('equipment_request')">
              <i data-lucide="box" class="w-4 h-4 inline mr-1"></i>
              Equipment Request
            </button>
          </nav>
        </div>

        <!-- Requests Table Section -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
              <i data-lucide="list" class="w-6 h-6 text-blue-500 mr-3"></i>
              Submitted Requests
            </h3>
            <div class="flex items-center space-x-2">
              <span class="text-sm text-gray-500">Total: <span id="nrTotalCount">{{ $requests->count() }}</span> requests</span>
            </div>
          </div>

          @if($requests->count() > 0)
            <x-table-card :title="'Recent Reservation Requests'" :pagination="method_exists($requests, 'hasPages') && $requests->hasPages() ? $requests->links() : null">
              <table class="table table-zebra w-full" id="nrRequestsTable">
                <thead>
                  <tr>
                    <th class="text-left">Request ID</th>
                    <th class="text-left">Request Type</th>
                    <th class="text-left">Department</th>
                    <th class="text-left">Priority</th>
                    <th class="text-left">Location</th>
                    <th class="text-left">Facility / Equipment</th>
                    <th class="text-left">Requested Date & Time</th>
                    <th class="text-left">Until</th>
                    <th class="text-left">Contact Name</th>
                    <th class="text-left">Contact Email</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($requests as $request)
                  <tr data-rt="{{ $request->request_type }}">
                    <td class="font-mono text-sm">#{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>
                      <span class="badge badge-outline">{{ ucfirst(str_replace('_', ' ', $request->request_type)) }}</span>
                    </td>
                    <td>{{ $request->department }}</td>
                    <td>
                      @php
                        $priorityClass = match($request->priority) {
                          'low' => 'badge-success',
                          'medium' => 'badge-warning', 
                          'high' => 'badge-error',
                          'urgent' => 'badge-error',
                          default => 'badge-neutral'
                        };
                      @endphp
                      <span class="badge {{ $priorityClass }}">{{ ucfirst($request->priority) }}</span>
                    </td>
                    <td>{{ $request->location }}</td>
                    <td>
                      @if($request->request_type === 'equipment_request')
                        @php
                          $equip = null;
                          if (!empty($request->notes)) {
                            $decoded = is_array($request->notes) ? $request->notes : json_decode($request->notes, true);
                            $equip = $decoded;
                          }
                        @endphp
                        <span class="text-sm">
                          {{ $equip['equipment_item'] ?? '—' }}
                          @if(!empty($equip['equipment_quantity']))
                            <span class="text-gray-500">× {{ $equip['equipment_quantity'] }}</span>
                          @endif
                        </span>
                      @else
                        @if($request->facility)
                          <span class="text-sm">{{ $request->facility->name }}</span>
                        @else
                          <span class="text-gray-400 text-sm">N/A</span>
                        @endif
                      @endif
                    </td>
                    <td>{{ $request->requested_datetime ? $request->requested_datetime->format('M d, Y h:i A') : 'N/A' }}</td>
                    <td>
                      @if($request->request_type === 'reservation')
                        {{ $request->requested_end_datetime ? $request->requested_end_datetime->format('M d, Y h:i A') : '—' }}
                      @else
                        —
                      @endif
                    </td>
                    <td>{{ $request->contact_name }}</td>
                    <td>
                      <span class="text-sm text-blue-600">{{ $request->contact_email }}</span>
                    </td>
                    <td>
                      @php
                        $statusClass = match($request->status) {
                          'pending' => 'badge-warning',
                          'approved' => 'badge-success',
                          'rejected' => 'badge-error',
                          'in_progress' => 'badge-info',
                          'completed' => 'badge-success',
                          default => 'badge-neutral'
                        };
                      @endphp
                      <span class="badge {{ $statusClass }}">{{ ucfirst($request->status) }}</span>
                    </td>
                    <td>
                      <div class="flex space-x-2">
                        <button class="p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110" 
                                title="View Details" 
                                onclick="viewRequestDetails({{ $request->id }})"
                                style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);"
                                onmouseover="this.style.background='#E6940F'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'"
                                onmouseout="this.style.background='#F7A923'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                          <i data-lucide="eye" class="w-4 h-4" style="fill: none;"></i>
                        </button>
                        <button class="p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110" 
                                title="Edit" 
                                onclick="editRequest({{ $request->id }})"
                                style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);"
                                onmouseover="this.style.background='#E6940F'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'"
                                onmouseout="this.style.background='#F7A923'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                          <i data-lucide="edit" class="w-4 h-4" style="fill: none;"></i>
                        </button>
                        @if($request->status === 'pending')
                        <button class="p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110" 
                                title="Approve" 
                                onclick="approveRequest({{ $request->id }})"
                                style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);"
                                onmouseover="this.style.background='#E6940F'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'"
                                onmouseout="this.style.background='#F7A923'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                          <i data-lucide="check" class="w-4 h-4" style="fill: none;"></i>
                        </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </x-table-card>
          @else
            <div class="text-center py-12">
              <i data-lucide="inbox" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
              <h3 class="text-lg font-semibold text-gray-500 mb-2">No requests found</h3>
              <p class="text-gray-400">Submit your first request using the form above.</p>
            </div>
          @endif
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    // Tab filtering logic
    function nrShowTab(type) {
      // Reset all navigation buttons
      const nav1 = document.getElementById('nav-facility');
      const nav2 = document.getElementById('nav-maintenance');
      const nav3 = document.getElementById('nav-equipment');
      
      [nav1, nav2, nav3].forEach(btn => {
        if (btn) {
          btn.classList.remove('text-blue-600', 'text-blue-800', 'font-semibold');
          btn.classList.add('text-gray-600');
        }
      });

      // Update active navigation button
      if (type === 'reservation' && nav1) {
        nav1.classList.remove('text-gray-600');
        nav1.classList.add('text-blue-800', 'font-semibold');
        // Reflect in URL
        try {
          const url = new URL(window.location.href);
          url.searchParams.delete('tab');
          window.history.replaceState({}, '', url);
        } catch(e) {}
      } else if (type === 'maintenance' && nav2) {
        nav2.classList.remove('text-gray-600');
        nav2.classList.add('text-blue-600', 'font-semibold');
        // Reflect in URL
        try {
          const url = new URL(window.location.href);
          url.searchParams.set('tab', 'maintenance');
          window.history.replaceState({}, '', url);
        } catch(e) {}
      } else if (type === 'equipment_request' && nav3) {
        nav3.classList.remove('text-gray-600');
        nav3.classList.add('text-blue-600', 'font-semibold');
        // Reflect in URL
        try {
          const url = new URL(window.location.href);
          url.searchParams.set('tab', 'equipment_request');
          window.history.replaceState({}, '', url);
        } catch(e) {}
      }

      // Filter rows
      const rows = document.querySelectorAll('#nrRequestsTable tbody tr');
      let count = 0;
      rows.forEach(row => {
        const rt = row.getAttribute('data-rt');
        const show = rt === type;
        row.style.display = show ? '' : 'none';
        if (show) count++;
      });
      const totalEl = document.getElementById('nrTotalCount');
      if (totalEl) totalEl.textContent = count;
    }

    document.addEventListener('DOMContentLoaded', function(){
      // Default to Facility Request tab (reservation)
      const urlParams = new URLSearchParams(window.location.search);
      const initial = urlParams.get('tab') || 'reservation';
      if (typeof nrShowTab === 'function') nrShowTab(initial);
    });
    
    // View request details
    function viewRequestDetails(requestId) {
      // Show loading state
      const button = event.target.closest('button');
      const originalContent = button.innerHTML;
      button.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>';
      button.disabled = true;
      
      // Fetch request details
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                       document.querySelector('input[name="_token"]')?.value ||
                       '{{ csrf_token() }}';
      
      fetch(`/facility_reservations/${requestId}/show-request`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show details in SweetAlert modal
          const request = data.data;
          const priorityColor = request.priority === 'urgent' ? '#ef4444' : 
                               request.priority === 'high' ? '#f59e0b' : 
                               request.priority === 'medium' ? '#3b82f6' : '#22c55e';
          
          Swal.fire({
            title: 'Request Details',
            html: `
              <div class="text-left space-y-3">
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-600">Request ID:</span>
                  <span class="font-mono text-blue-600">#${String(request.id).padStart(6, '0')}</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-600">Type:</span>
                  <span class="badge badge-outline">${request.request_type}</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-600">Department:</span>
                  <span>${request.department}</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-600">Priority:</span>
                  <span class="badge" style="background-color: ${priorityColor}; color: white;">${request.priority}</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-600">Location:</span>
                  <span>${request.location}</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-600">Facility:</span>
                  <span>${request.facility?.name || 'N/A'}</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-600">Requested Date:</span>
                  <span>${new Date(request.requested_datetime).toLocaleString()}</span>
                </div>
                ${request.request_type === 'reservation' ? `
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-600">Until:</span>
                  <span>${request.requested_end_datetime ? new Date(request.requested_end_datetime).toLocaleString() : '—'}</span>
                </div>` : ''}
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-600">Contact:</span>
                  <span>${request.contact_name}</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-600">Email:</span>
                  <span class="text-blue-600">${request.contact_email}</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-semibold text-gray-600">Status:</span>
                  <span class="badge badge-warning">${request.status}</span>
                </div>
                <div class="mt-4">
                  <span class="font-semibold text-gray-600 block mb-2">Description:</span>
                  <p class="text-gray-700 bg-gray-50 p-3 rounded">${request.description}</p>
                </div>
              </div>
            `,
            width: '600px',
            showConfirmButton: true,
            confirmButtonText: 'Close',
            confirmButtonColor: '#3b82f6',
            customClass: {
              popup: 'rounded-lg'
            }
          });
        } else {
          showNotification('Error loading request details', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('Error loading request details', 'error');
      })
      .finally(() => {
        // Reset button
        button.innerHTML = originalContent;
        button.disabled = false;
      });
    }
    
    // Edit request
    function editRequest(requestId) {
      Swal.fire({
        title: 'Edit Request',
        text: 'Are you sure you want to edit this request?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Edit',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        focusConfirm: false
      }).then((result) => {
        if (result.isConfirmed) {
          // Show loading state
          const button = event.target.closest('button');
          const originalContent = button.innerHTML;
          button.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>';
          button.disabled = true;
          
          // For now, just show a notification that edit is coming soon
          setTimeout(() => {
            Swal.fire({
              title: 'Coming Soon!',
              text: 'Edit functionality is currently under development.',
              icon: 'info',
              confirmButtonColor: '#3b82f6',
              customClass: {
                popup: 'rounded-lg'
              }
            });
            // Reset button
            button.innerHTML = originalContent;
            button.disabled = false;
          }, 1000);
        }
      });
    }
    
    // Approve request
    function approveRequest(requestId) {
      Swal.fire({
        title: 'Approve Request',
        text: 'Are you sure you want to approve this request? This action will change the status from "Pending" to "Approved" and cannot be undone.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        focusConfirm: false
      }).then((result) => {
        if (result.isConfirmed) {
          // Show loading state
          const button = event.target.closest('button');
          const originalContent = button.innerHTML;
          button.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>';
          button.disabled = true;
          
          // Send approval request
          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                           document.querySelector('input[name="_token"]')?.value ||
                           '{{ csrf_token() }}';
          
          fetch(`/facility_reservations/${requestId}/approve-request`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              _token: csrfToken
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                title: 'Success!',
                text: 'Request has been approved successfully! An email notification has been sent to the requester.',
                icon: 'success',
                confirmButtonColor: '#22c55e',
                customClass: {
                  popup: 'rounded-lg'
                }
              }).then(() => {
                // Reload page to update status
                window.location.reload();
              });
            } else {
              Swal.fire({
                title: 'Error!',
                text: data.message || 'Error approving request',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                customClass: {
                  popup: 'rounded-lg'
                }
              });
              // Reset button
              button.innerHTML = originalContent;
              button.disabled = false;
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              title: 'Error!',
              text: 'Error approving request',
              icon: 'error',
              confirmButtonColor: '#ef4444',
              customClass: {
                popup: 'rounded-lg'
              }
            });
            // Reset button
            button.innerHTML = originalContent;
            button.disabled = false;
          });
        }
      });
    }
    
    // Notification function - uses global showNotification with progress bar if available
    if (typeof window.showNotification === 'undefined' || window.showNotification.toString().indexOf('progressBar') === -1) {
      window.showNotification = function(message, type = 'info', duration = 3000) {
        // Remove any existing notification progress style if it exists
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
      };
    }
  </script>
</body>
</html>
