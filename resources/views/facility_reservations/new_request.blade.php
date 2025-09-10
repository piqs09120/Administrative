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
              <a href="{{ route('facility_reservations.index') }}" class="btn btn-ghost btn-sm" title="Back to Reservations">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
              </a>
              <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Facility Requests</h1>
                <p class="text-gray-600">View and manage facility management requests</p>
              </div>
            </div>
          </div>
        </div>


        <!-- Tabs (like Visitor Logs) -->
        <div class="bg-gray-100 px-6 py-2 border-b border-gray-200 mb-0 rounded-t-xl" style="background-color: var(--color-snow-mist); border-color: var(--color-snow-mist);">
          <div class="flex space-x-1">
            <button id="nrTabFacility" class="px-4 py-2 text-sm font-medium text-gray-700 bg-blue-100 rounded-t-lg border-b-2 border-blue-500" onclick="nrShowTab('reservation')" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%); color: var(--color-charcoal-ink); border-color: var(--color-regal-navy);">Facility Request</button>
            <button id="nrTabMaintenance" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-t-lg" onclick="nrShowTab('maintenance')" style="color: var(--color-charcoal-ink);">Maintenance</button>
            <button id="nrTabEquipment" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-200 rounded-t-lg" onclick="nrShowTab('equipment_request')" style="color: var(--color-charcoal-ink);">Equipment Request</button>
          </div>
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
            <div class="overflow-x-auto">
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
                        <button class="btn btn-sm btn-outline btn-info" title="View Details" onclick="viewRequestDetails({{ $request->id }})">
                          <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                        <button class="btn btn-sm btn-outline btn-warning" title="Edit" onclick="editRequest({{ $request->id }})">
                          <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        @if($request->status === 'pending')
                        <button class="btn btn-sm btn-outline btn-success" title="Approve" onclick="approveRequest({{ $request->id }})">
                          <i data-lucide="check" class="w-4 h-4"></i>
                        </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
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
      const dirBtn = document.getElementById('nrTabFacility');
      const mainBtn = document.getElementById('nrTabMaintenance');
      const eqBtn = document.getElementById('nrTabEquipment');
      const setActive = (btn, active) => {
        if (active) {
          btn.classList.add('bg-blue-100','border-b-2','border-blue-500');
          btn.classList.remove('text-gray-500');
          btn.style.backgroundColor = 'color-mix(in srgb, var(--color-regal-navy), white 80%)';
          btn.style.color = 'var(--color-charcoal-ink)';
          btn.style.borderColor = 'var(--color-regal-navy)';
        } else {
          btn.classList.remove('bg-blue-100','border-b-2','border-blue-500');
          btn.classList.add('text-gray-500');
          btn.style.backgroundColor = 'inherit';
          btn.style.borderColor = 'transparent';
        }
      };

      setActive(dirBtn, type === 'reservation');
      setActive(mainBtn, type === 'maintenance');
      setActive(eqBtn, type === 'equipment_request');

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
    
    // Notification function using SweetAlert
    function showNotification(message, type = 'info') {
      const config = {
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: {
          popup: 'rounded-lg'
        }
      };

      switch(type) {
        case 'success':
          Swal.fire({
            ...config,
            icon: 'success',
            title: message
          });
          break;
        case 'error':
          Swal.fire({
            ...config,
            icon: 'error',
            title: message
          });
          break;
        case 'warning':
          Swal.fire({
            ...config,
            icon: 'warning',
            title: message
          });
          break;
        default:
          Swal.fire({
            ...config,
            icon: 'info',
            title: message
          });
      }
    }
  </script>
</body>
</html>
