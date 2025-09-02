<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Legal Cases Dashboard - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
  
  <style>
    /* CSS Variables for consistent styling */
    :root {
      --color-regal-navy: #1e3a8a;
      --color-charcoal-ink: #1f2937;
      --color-snow-mist: #f3f4f6;
      --color-white: #ffffff;
      --color-modern-teal: #0d9488;
      --color-golden-ember: #d97706;
      --color-danger-red: #dc2626;
    }
    
    /* Modal styling */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(4px);
    }
    
    .modal.modal-open {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .modal-box {
      background: white;
      border-radius: 12px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      max-height: 90vh;
      overflow-y: auto;
      animation: modalSlideIn 0.3s ease-out;
    }
    
    @keyframes modalSlideIn {
      from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
    
    /* Form styling */
    .form-control {
      margin-bottom: 1rem;
    }
    
    .label {
      margin-bottom: 0.5rem;
    }
    
    .label-text {
      font-weight: 600;
      color: #374151;
    }
    
    .input, .select, .textarea {
      border: 1px solid #d1d5db;
      border-radius: 6px;
      padding: 0.75rem;
      transition: border-color 0.2s ease;
    }
    
    .input:focus, .select:focus, .textarea:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Select element styling */
    .select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
      background-position: right 0.5rem center;
      background-repeat: no-repeat;
      background-size: 1.5em 1.5em;
      padding-right: 2.5rem;
    }
    
    /* File upload zone styling */
    #uploadZone {
      transition: all 0.2s ease;
    }
    
    #uploadZone:hover {
      border-color: #3b82f6;
      background-color: #eff6ff;
    }
    
    /* Loading spinner */
    .loading {
      display: inline-block;
      width: 1rem;
      height: 1rem;
      border: 2px solid #f3f3f3;
      border-top: 2px solid #3b82f6;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
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

        <!-- Page Header with Stats -->
        <div class="mb-8">
          <div class="mb-6">
            <div>
              <h1 class="text-3xl font-bold text-gray-800 mb-2">Legal Cases</h1>
              <p class="text-gray-600">Manage and track all legal cases and proceedings</p>
            </div>
          </div>
          


        <!-- Status Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Cases -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-primary">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-primary text-primary-content rounded-full w-12 h-12">
                    <i data-lucide="briefcase" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-primary badge-outline">Total</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-primary justify-center mb-2">{{ $stats['total_cases'] ?? 0 }}</h2>
                <p class="text-base-content/70">All Cases</p>
              </div>
            </div>
          </div>

          <!-- Approved Cases -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-success">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-success text-success-content rounded-full w-12 h-12">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-success badge-outline">Approved</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-success justify-center mb-2">{{ $stats['approved_cases'] ?? 0 }}</h2>
                <p class="text-base-content/70">Completed</p>
              </div>
            </div>
          </div>

          <!-- Pending Cases -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-warning">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-warning text-warning-content rounded-full w-12 h-12">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-warning badge-outline">Pending</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-warning justify-center mb-2">{{ $stats['pending_cases'] ?? 0 }}</h2>
                <p class="text-base-content/70">Awaiting Review</p>
              </div>
            </div>
          </div>

          <!-- Declined Cases -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-error">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-error text-error-content rounded-full w-12 h-12">
                    <i data-lucide="x-circle" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-error badge-outline">Declined</div>
                </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-error justify-center mb-2">{{ $stats['declined_cases'] ?? 0 }}</h2>
                <p class="text-base-content/70">Not Approved</p>
              </div>
            </div>
          </div>
        </div>





        <!-- Cases Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
          <div class="p-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Legal Cases</h3>
              <div class="text-sm text-gray-500">
                Showing {{ $cases->count() ?? 0 }} of {{ $stats['total_cases'] ?? 0 }} cases
              </div>
            </div>
            <div class="overflow-x-auto">
              <table class="table w-full">
                <thead>
                  <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-left font-semibold text-gray-700">Case Details</th>
                    <th class="px-6 py-4 text-center font-semibold text-gray-700">Type</th>
                    <th class="px-6 py-4 text-center font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-4 text-center font-semibold text-gray-700">Date</th>
                    <th class="px-6 py-4 text-center font-semibold text-gray-700">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($cases ?? [] as $case)
                    <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100">
                      <td class="px-6 py-4">
                        <div>
                          <h4 class="font-semibold text-gray-900 mb-1">{{ $case->case_title ?? 'Untitled Case' }}</h4>
                          <p class="text-sm text-gray-600 mb-2">{{ Str::limit($case->case_description ?? 'No description', 80) }}</p>
                          <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">#{{ $case->case_number ?? 'N/A' }}</span>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 text-center">
                        @if($case->case_type)
                          <span class="badge badge-outline badge-sm">{{ ucfirst($case->case_type) }}</span>
                        @else
                          <span class="text-gray-400 text-sm">N/A</span>
                        @endif
                      </td>
                      <td class="px-6 py-4 text-center">
                        @php
                          $statusConfig = [
                            'pending' => ['class' => 'badge-warning', 'icon' => 'clock'],
                            'ongoing' => ['class' => 'badge-info', 'icon' => 'gavel'],
                            'completed' => ['class' => 'badge-success', 'icon' => 'check-circle'],
                            'rejected' => ['class' => 'badge-error', 'icon' => 'x-circle']
                          ];
                          $status = $case->status ?? 'pending';
                          $config = $statusConfig[$status] ?? $statusConfig['pending'];
                        @endphp
                        <span class="badge {{ $config['class'] }} gap-1">
                          <i data-lucide="{{ $config['icon'] }}" class="w-3 h-3"></i>
                          {{ ucfirst($status) }}
                        </span>
                      </td>

                      <td class="px-6 py-4 text-center">
                        <div class="text-sm text-gray-600">
                          {{ $case->created_at ? $case->created_at->format('M d, Y') : 'N/A' }}
                        </div>
                      </td>
                      <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                          @if($case->status === 'pending')
                            <button onclick="approveCase({{ $case->id ?? 1 }})" 
                                    class="btn btn-success btn-xs tooltip" 
                                    data-tip="Approve Case">
                              <i data-lucide="check" class="w-4 h-4 text-white"></i>
                            </button>
                            <button onclick="declineCase({{ $case->id ?? 1 }})" 
                                    class="btn btn-error btn-xs tooltip" 
                                    data-tip="Decline Case">
                              <i data-lucide="x" class="w-4 h-4 text-white"></i>
                            </button>
                          @else
                            @if(auth()->user()->role === 'Administrator')
                          <button onclick="viewCase({{ $case->id ?? 1 }})" 
                                  class="btn btn-ghost btn-xs tooltip" 
                                  data-tip="View Case">
                            <i data-lucide="eye" class="w-4 h-4 text-blue-600"></i>
                          </button>
                          <button onclick="editCase({{ $case->id ?? 1 }})" 
                                  class="btn btn-ghost btn-xs tooltip" 
                                  data-tip="Edit Case">
                            <i data-lucide="edit" class="w-4 h-4 text-green-600"></i>
                          </button>
                            @endif
                          @endif
                      </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center py-12">
                        <div class="flex flex-col items-center">
                          <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="briefcase" class="w-10 h-10 text-gray-400"></i>
                  </div>
                          <h3 class="text-lg font-semibold text-gray-600 mb-2">No Legal Cases Found</h3>
                          <p class="text-gray-500 text-sm mb-4">Get started by creating your first legal case</p>
                          @if(auth()->user()->role === 'Administrator')
                          <a href="{{ route('legal.create') }}" class="btn btn-primary">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Add New Case
                    </a>
                          @endif
                  </div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
                  </div>
            <!-- Pagination -->
            @if(isset($cases) && $cases->hasPages())
              <div class="flex justify-center p-6 border-t border-gray-200">
                {{ $cases->links() }}
              </div>
            @endif
          </div>
        </div>
      </main>
    </div>
  </div>



  @include('partials.soliera_js')
  
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Role-based access control
    const userRole = '{{ auth()->user()->role }}';
    

    

    
    // Search and filter functionality
    function filterCases() {
      const searchTerm = document.getElementById('caseSearch').value.toLowerCase();
      const statusFilter = document.getElementById('statusFilter').value;
      const priorityFilter = document.getElementById('priorityFilter').value;
      
      const rows = document.querySelectorAll('tbody tr');
      
      rows.forEach(row => {
        let showRow = true;
        
        // Search filter
        if (searchTerm) {
          const title = row.querySelector('td:first-child h4')?.textContent?.toLowerCase() || '';
          const description = row.querySelector('td:first-child p')?.textContent?.toLowerCase() || '';
          if (!title.includes(searchTerm) && !description.includes(searchTerm)) {
            showRow = false;
          }
        }
        
        // Status filter
        if (statusFilter && showRow) {
          const status = row.querySelector('td:nth-child(3) .badge')?.textContent?.toLowerCase() || '';
          if (!status.includes(statusFilter)) {
            showRow = false;
          }
        }
        
        // Priority filter
        if (priorityFilter && showRow) {
          const priority = row.querySelector('td:nth-child(4) .badge')?.textContent?.toLowerCase() || '';
          if (!priority.includes(priorityFilter)) {
            showRow = false;
          }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
      });
    }
    
    function clearFilters() {
      document.getElementById('caseSearch').value = '';
      document.getElementById('statusFilter').value = '';
      document.getElementById('priorityFilter').value = '';
      
      // Show all rows
      const rows = document.querySelectorAll('tbody tr');
      rows.forEach(row => {
        row.style.display = '';
      });
    }
    
    // Case actions
    function viewCase(caseId) {
      window.location.href = `/legal/cases/${caseId}`;
    }
    
    function editCase(caseId) {
      window.location.href = `/legal/cases/${caseId}/edit`;
    }
    
    function deleteCase(caseId) {
      if (confirm('Are you sure you want to delete this legal case? This action cannot be undone.')) {
        fetch(`/legal/cases/${caseId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => {
          if (response.ok) {
              window.location.reload();
          } else {
            alert('Error deleting case');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while deleting the case');
        });
      }
    }

    // Approve a legal case
    function approveCase(caseId) {
      if (confirm('Are you sure you want to approve this legal case?')) {
        fetch(`/legal/cases/${caseId}/approve`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showToast('Legal case approved successfully!', 'success');
            // Reload page to update statistics and table
            setTimeout(() => window.location.reload(), 1000);
          } else {
            throw new Error(data.message || 'Failed to approve case');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showToast('Error approving case: ' + error.message, 'error');
        });
      }
    }

    // Decline a legal case
    function declineCase(caseId) {
      if (confirm('Are you sure you want to decline this legal case?')) {
        fetch(`/legal/cases/${caseId}/decline`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showToast('Legal case declined successfully!', 'success');
            // Reload page to update statistics and table
            setTimeout(() => window.location.reload(), 1000);
          } else {
            throw new Error(data.message || 'Failed to decline case');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showToast('Error declining case: ' + error.message, 'error');
        });
      }
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Search and filter event listeners
      document.getElementById('caseSearch').addEventListener('input', filterCases);
      document.getElementById('statusFilter').addEventListener('change', filterCases);
      document.getElementById('priorityFilter').addEventListener('change', filterCases);
      
      // File input change event listener
      const fileInput = document.getElementById('legal_document');
      if (fileInput) {
        fileInput.addEventListener('change', function(e) {
          if (e.target.files.length > 0) {
            updateFilePreview(e.target.files[0]);
            analyzeDocument(e.target.files[0]);
          }
        });
      }
      
      // Form submission handler
      const addCaseForm = document.getElementById('addCaseForm');
      if (addCaseForm) {
        addCaseForm.addEventListener('submit', function(e) {
          e.preventDefault();
          handleFormSubmission();
        });
      }
    });
    
    // Handle form submission
    function handleFormSubmission() {
      const form = document.getElementById('addCaseForm');
      const formData = new FormData(form);
      
      // Show loading state
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="loading loading-spinner"></i> Creating Case...';
      submitBtn.disabled = true;
      
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message
          showToast('Legal case created successfully!', 'success');
          // Close modal
          closeAddCaseModal();
          // Reload page to show new case
          setTimeout(() => window.location.reload(), 1000);
        } else {
          throw new Error(data.message || 'Failed to create case');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('Error creating case: ' + error.message, 'error');
        // Restore submit button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      });
    }
    
    // Toast notification function
    function showToast(message, type = 'info') {
      // Create toast element
      const toast = document.createElement('div');
      toast.className = `alert alert-${type} fixed bottom-4 right-4 z-50 max-w-sm shadow-lg`;
      toast.innerHTML = `
        <i data-lucide="${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info'}" class="w-5 h-5"></i>
        <span>${message}</span>
      `;
      
      // Add to body
      document.body.appendChild(toast);
      
      // Recreate Lucide icons
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
      
      // Remove after 3 seconds
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 3000);
    }
  </script>
</body>
</html>
