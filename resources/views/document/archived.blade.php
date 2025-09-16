<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Archived Documents - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @vite(['resources/css/soliera.css'])
  @php
    use Illuminate\Support\Facades\Storage;
  @endphp
</head>
<body class="bg-base-100">
  <div class="flex h-screen overflow-hidden">
    @include('partials.sidebarr')
    <div class="flex flex-col flex-1 overflow-hidden">
      @include('partials.navbar')
      <main class="flex-1 overflow-y-auto p-8">
        <div class="mb-4">
          <h1 class="text-3xl font-bold" style="color: var(--color-charcoal-ink);">Archived Documents</h1>
        </div>
        <!-- underline divider (matches other modules) -->
        <div class="border-b border-gray-200 mb-6"></div>

        <!-- Status Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
          <!-- Total Documents -->
          <div class="card bg-base-100 shadow-xl border-l-4 border-l-primary">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-primary text-primary-content rounded-full w-10 h-10">
                    <i data-lucide="folder" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-primary badge-outline text-xs">Total</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-primary justify-center mb-1">{{ $documents->count() }}</h2>
                <p class="text-sm text-base-content/70">Total Documents</p>
              </div>
            </div>
          </div>

          <!-- Received Today -->
          <div class="card bg-base-100 shadow-xl border-l-4 border-l-info">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-info text-info-content rounded-full w-10 h-10">
                    <i data-lucide="inbox" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-info badge-outline text-xs">Today</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-info justify-center mb-1">{{ $documents->where('created_at', '>=', now()->startOfDay())->count() }}</h2>
                <p class="text-sm text-base-content/70">Received Today</p>
              </div>
            </div>
          </div>

          <!-- Expired Documents -->
          <div class="card bg-base-100 shadow-xl border-l-4 border-l-error">
            <div class="card-body p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="avatar placeholder">
                  <div class="bg-error text-error-content rounded-full w-10 h-10">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                  </div>
                </div>
                <div class="badge badge-error badge-outline text-xs">Expired</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-2xl sm:text-3xl font-bold text-error justify-center mb-1">{{ $documents->where('status', 'expired')->count() }}</h2>
                <p class="text-sm text-base-content/70">Expired Documents</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Complete Archived Documents Table -->
        <div class="card bg-white shadow-xl">
          <div class="card-body">
            <div class="flex items-center justify-between mb-6">
              <h3 class="card-title text-xl">
                <i data-lucide="archive" class="w-6 h-6 text-gray-500"></i>
                Document Management System - Archived Documents
              </h3>
              <div class="flex items-center gap-3">
                <div class="text-sm text-gray-600">
                  Total: <span class="font-semibold">{{ $documents->count() }}</span> archived documents
                </div>
              </div>
            </div>
            
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr class="bg-gray-50">
                    <th class="text-left py-4 px-4 font-semibold text-gray-700">Document Name/Title</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Document Type</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Department/Owner</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Date Created</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Confidentiality Level</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Retention Period</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Status</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Expiration Date</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($documents as $index => $document)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                      <!-- Document Name/Title Column -->
                      <td class="py-4 px-4">
                        <div class="flex items-center space-x-3">
                          <div class="avatar placeholder">
                            <div class="bg-blue-100 text-blue-800 rounded-full w-10 h-10 flex items-center justify-center">
                              <span class="text-sm font-semibold">
                                {{ substr($document->title ?? 'UN', 0, 2) }}
                              </span>
                            </div>
                          </div>
                          <div>
                            <h4 class="font-semibold text-gray-900">{{ $document->title ?: 'Untitled Document' }}</h4>
                            <p class="text-sm text-gray-500">#{{ $document->id }}</p>
                          </div>
                        </div>
                      </td>
                      
                      <!-- Document Type Column -->
                      <td class="py-4 px-4 text-center">
                        <span class="text-sm font-medium text-gray-700">{{ ucfirst($document->category ?: 'Unknown') }}</span>
                      </td>
                      
                      <!-- Department/Owner Column -->
                      <td class="py-4 px-4 text-center">
                        <span class="text-sm font-medium text-gray-700">{{ $document->department ?: 'Unassigned' }}</span>
                      </td>
                      
                      <!-- Date Created Column -->
                      <td class="py-4 px-4 text-center">
                        <span class="text-sm text-gray-600">{{ $document->created_at->format('M d, Y') }}</span>
                      </td>
                      
                      <!-- Confidentiality Level Column -->
                      <td class="py-4 px-4 text-center">
                        @php
                          $confidentialityLevel = $document->confidentiality_level ?? 'internal';
                          $confidentialityClass = match($confidentialityLevel) {
                            'restricted' => 'bg-red-100 text-red-800',
                            'confidential' => 'bg-orange-100 text-orange-800',
                            'internal' => 'bg-yellow-100 text-yellow-800',
                            'public' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-800'
                          };
                        @endphp
                        <span class="text-xs font-medium {{ $confidentialityClass }} px-2.5 py-1 rounded-full">
                          {{ ucfirst($confidentialityLevel) }}
                        </span>
                      </td>
                      
                      <!-- Retention Period Column -->
                      <td class="py-4 px-4 text-center">
                        @php
                          $retentionPeriod = $document->retention_period ?? match($document->category) {
                            'contract' => '7 Years',
                            'legal' => '10 Years',
                            'policy' => '5 Years',
                            'report' => '3 Years',
                            default => '2 Years'
                          };
                        @endphp
                        <span class="text-sm text-gray-600">{{ $retentionPeriod }}</span>
                      </td>
                      
                      <!-- Status Column -->
                      <td class="py-4 px-4 text-center">
                        @php
                          $status = $document->status ?? 'active';
                          $statusClass = match($status) {
                            'expired' => 'bg-red-100 text-red-800',
                            'expiring_soon' => 'bg-orange-100 text-orange-800',
                            'active' => 'bg-green-100 text-green-800',
                            'archived' => 'bg-blue-100 text-blue-800',
                            'disposed' => 'bg-gray-100 text-gray-800',
                            default => 'bg-gray-100 text-gray-800'
                          };
                        @endphp
                        <span class="text-xs font-medium {{ $statusClass }} px-2.5 py-1 rounded-full">
                          {{ ucfirst($status) }}
                        </span>
                      </td>
                      
                      <!-- Expiration Date Column -->
                      <td class="py-4 px-4 text-center">
                        @if($document->retention_until)
                          <span class="text-sm text-gray-600">{{ $document->retention_until->format('M d, Y') }}</span>
                        @else
                          <span class="text-sm text-gray-400">Not set</span>
                        @endif
                      </td>
                      
                      <!-- Actions Column -->
                      <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center space-x-2">
                          <button onclick="viewDocument({{ $document->id }})" 
                                  class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 cursor-pointer" 
                                  title="View Document"
                                  type="button">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                          </button>
                          <button onclick="downloadDocument({{ $document->id }})" 
                                  class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200" 
                                  title="Download Document">
                            <i data-lucide="download" class="w-4 h-4"></i>
                          </button>
                          @if($document->status === 'expired')
                            <button onclick="disposeDocument({{ $document->id }})" 
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200" 
                                    title="Dispose Document">
                              <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                          @endif
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="9" class="py-12 text-center">
                        <div class="flex flex-col items-center">
                          <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="archive" class="w-10 h-10 text-gray-400"></i>
                          </div>
                          <h3 class="text-lg font-semibold text-gray-600 mb-2">No Archived Documents Found</h3>
                          <p class="text-gray-500 text-sm mb-4">No documents have been archived yet.</p>
                        </div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Archive Confirmation Modal -->
  <div id="archiveModal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Confirm Archive</h3>
      <p class="py-4">Are you sure you want to archive this document? You can restore it anytime from the Archived Documents section.</p>
      <div class="modal-action">
        <button class="btn btn-ghost" onclick="closeArchiveModal()">Cancel</button>
        <button class="btn btn-warning" onclick="confirmArchive()">Archive Document</button>
      </div>
    </div>
  </div>

  <!-- Unarchive Confirmation Modal -->
  <div id="unarchiveModal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Confirm Unarchive</h3>
      <p class="py-4">Are you sure you want to restore this document? It will be moved back to the active documents list.</p>
      
      <!-- Password Input Field - Only for Administrators -->
      @if(auth()->user()->role === 'Administrator')
        <div class="form-control w-full mb-4">
          <label class="label">
            <span class="label-text font-semibold">Enter Administrator Password to Confirm</span>
          </label>
          <input type="password" id="unarchivePassword" class="input input-bordered w-full" placeholder="Enter administrator password" />
          <div class="label">
            <span class="label-text-alt">Administrator password required to restore archived documents</span>
          </div>
        </div>
        
        <!-- Error Message -->
        <div id="passwordError" class="alert alert-error mb-4 hidden">
          <i data-lucide="alert-circle" class="w-4 h-4"></i>
          <span id="errorMessage">Incorrect password. Please try again.</span>
        </div>
      @else
        <!-- Non-Administrator Message -->
        <div class="alert alert-warning mb-4">
          <i data-lucide="shield-x" class="w-4 h-4"></i>
          <span>Only administrators can restore archived documents. Please contact your system administrator.</span>
        </div>
      @endif
      
      <div class="modal-action">
        <button class="btn btn-ghost" onclick="closeUnarchiveModal()">Cancel</button>
        @if(auth()->user()->role === 'Administrator')
          <button class="btn btn-success" onclick="confirmUnarchive()">Restore Document</button>
        @else
          <button class="btn btn-disabled" disabled>Restore Document</button>
        @endif
      </div>
    </div>
  </div>

  <!-- Permanent Delete Confirmation Modal -->
  <div id="permanentDeleteModal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Confirm Permanent Deletion</h3>
      <p class="py-4 text-red-600 font-semibold">⚠️ WARNING: This action cannot be undone!</p>
      <p class="py-4">Are you absolutely sure you want to permanently delete this archived document? This will remove it from the system completely.</p>
      <div class="modal-action">
        <button class="btn btn-ghost" onclick="closePermanentDeleteModal()">Cancel</button>
        <button class="btn btn-error" onclick="confirmPermanentDelete()">Permanently Delete</button>
      </div>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Modal state variables
    let documentToArchive = null;
    let documentToUnarchive = null;
    let documentToDelete = null;
    
    // Predefined password for unarchiving documents
    const UNARCHIVE_PASSWORD = 'admin123'; // You can change this to your desired password
    
    // User role for conditional access control
    const userRole = '{{ auth()->user()->role }}';
    const isAdministrator = userRole === 'Administrator';

    // Archive functions
    function archiveDocument(documentId) {
      documentToArchive = documentId;
      document.getElementById('archiveModal').classList.add('modal-open');
    }

    function closeArchiveModal() {
      document.getElementById('archiveModal').classList.remove('modal-open');
      documentToArchive = null;
    }

    function confirmArchive() {
      if (!documentToArchive) return;
      
      fetch(`/document/${documentToArchive}/archive`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message and reload page
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while archiving the document');
      })
      .finally(() => {
        closeArchiveModal();
      });
    }

    // Unarchive functions
    function unarchiveDocument(documentId) {
      documentToUnarchive = documentId;
      document.getElementById('unarchiveModal').classList.add('modal-open');
    }

    function closeUnarchiveModal() {
      document.getElementById('unarchiveModal').classList.remove('modal-open');
      documentToUnarchive = null;
      
      // Clear password field and hide error message (only for administrators)
      if (isAdministrator) {
        const passwordField = document.getElementById('unarchivePassword');
        const errorElement = document.getElementById('passwordError');
        
        if (passwordField) passwordField.value = '';
        if (errorElement) errorElement.classList.add('hidden');
      }
    }

    function confirmUnarchive() {
      if (!documentToUnarchive) return;
      
      // Check if user is administrator
      if (!isAdministrator) {
        showPasswordError('Access denied. Only administrators can restore archived documents.');
        return;
      }
      
      // Get password from input field
      const password = document.getElementById('unarchivePassword').value.trim();
      
      // Validate password
      if (!password) {
        showPasswordError('Please enter the administrator password');
        return;
      }
      
      if (password !== UNARCHIVE_PASSWORD) {
        showPasswordError('Incorrect administrator password. Please try again.');
        return;
      }
      
      // Hide any previous error messages
      document.getElementById('passwordError').classList.add('hidden');
      
      // Proceed with unarchive request
      fetch(`/document/${documentToUnarchive}/unarchive`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message and reload page
          location.reload();
        } else {
          showPasswordError('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showPasswordError('An error occurred while unarchiving the document');
      });
    }
    
    // Function to show password error message
    function showPasswordError(message) {
      const errorElement = document.getElementById('passwordError');
      const errorMessageElement = document.getElementById('errorMessage');
      
      if (errorElement && errorMessageElement) {
        errorMessageElement.textContent = message;
        errorElement.classList.remove('hidden');
        
        // Focus on password field for better UX (only for administrators)
        if (isAdministrator) {
          const passwordField = document.getElementById('unarchivePassword');
          if (passwordField) passwordField.focus();
        }
      }
    }

    // Permanent delete functions
    function permanentlyDeleteDocument(documentId) {
      documentToDelete = documentId;
      document.getElementById('permanentDeleteModal').classList.add('modal-open');
    }

    function closePermanentDeleteModal() {
      document.getElementById('permanentDeleteModal').classList.remove('modal-open');
      documentToDelete = null;
    }

    function confirmPermanentDelete() {
      if (!documentToDelete) return;
      
      fetch(`/document/${documentToDelete}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message and reload page
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the document');
      })
      .finally(() => {
        closePermanentDeleteModal();
      });
    }

    // Document actions
    function viewDocument(documentId) {
      console.log('viewDocument called with ID:', documentId);
      viewDocumentDetails(documentId);
    }

    // View document details in modal
    function viewDocumentDetails(documentId) {
      // Show loading state
      Swal.fire({
        title: 'Loading Document Details...',
        text: 'Please wait while we fetch the document information.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      // Fetch document details
      fetch(`/document/${documentId}`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: Failed to fetch document details`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success && data.document) {
          const doc = data.document;
          
          // Format dates
          const formatDate = (dateString) => {
            if (!dateString) return 'Not Set';
            return new Date(dateString).toLocaleDateString('en-US', {
              year: 'numeric',
              month: 'short',
              day: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            });
          };

          // Get status badge class
          const getStatusClass = (status) => {
            const statusConfig = {
              'active': 'bg-green-100 text-green-800',
              'archived': 'bg-gray-100 text-gray-800',
              'disposed': 'bg-gray-100 text-gray-800',
              'expired': 'bg-red-100 text-red-800',
              'pending_release': 'bg-yellow-100 text-yellow-800'
            };
            return statusConfig[status] || 'bg-gray-100 text-gray-800';
          };

          // Get confidentiality badge class
          const getConfidentialityClass = (confidentiality) => {
            const confConfig = {
              'public': 'bg-green-100 text-green-800',
              'internal': 'bg-yellow-100 text-yellow-800',
              'confidential': 'bg-orange-100 text-orange-800',
              'restricted': 'bg-red-100 text-red-800'
            };
            return confConfig[confidentiality] || 'bg-gray-100 text-gray-800';
          };

          // Create modal content
          const modalContent = `
            <div class="text-left">
              <!-- Document Header -->
              <div class="flex items-start justify-between mb-6">
                <div class="flex-1">
                  <h2 class="text-2xl font-bold text-gray-900 mb-2">${doc.title || 'Untitled Document'}</h2>
                  <p class="text-gray-600">Document ID: #${doc.id}</p>
                </div>
                <span class="text-xs font-medium px-3 py-1 rounded-full ${getStatusClass(doc.status)}">
                  ${doc.status ? doc.status.replace('_', ' ').toUpperCase() : 'ACTIVE'}
                </span>
              </div>

              ${doc.description ? `
                <div class="mb-6">
                  <h3 class="font-semibold text-gray-700 mb-2">Description</h3>
                  <p class="text-gray-600">${doc.description}</p>
                </div>
              ` : ''}

              <!-- Document Details Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                  <h4 class="font-semibold text-gray-700 mb-2">Document Information</h4>
                  <div class="space-y-2">
                    <div class="flex justify-between">
                      <span class="text-gray-600">Type:</span>
                      <span class="font-medium">${doc.type || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Category:</span>
                      <span class="font-medium">${doc.category || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Department:</span>
                      <span class="font-medium">${doc.department || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Confidentiality:</span>
                      <span class="px-2 py-1 text-xs rounded-full ${getConfidentialityClass(doc.confidentiality)}">
                        ${doc.confidentiality ? doc.confidentiality.toUpperCase() : 'N/A'}
                      </span>
                    </div>
                  </div>
                </div>
                
                <div>
                  <h4 class="font-semibold text-gray-700 mb-2">Timeline</h4>
                  <div class="space-y-2">
                    <div class="flex justify-between">
                      <span class="text-gray-600">Created:</span>
                      <span class="font-medium">${formatDate(doc.created_at)}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Updated:</span>
                      <span class="font-medium">${formatDate(doc.updated_at)}</span>
                    </div>
                    ${doc.retention_until ? `
                      <div class="flex justify-between">
                        <span class="text-gray-600">Retention Until:</span>
                        <span class="font-medium">${formatDate(doc.retention_until)}</span>
                      </div>
                    ` : ''}
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex flex-wrap gap-2 mt-6 pt-4 border-t border-gray-200">
                <a href="/document/${doc.id}/download" class="btn btn-outline btn-sm">
                  <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                  Download
                </a>
                <button onclick="analyzeDocument(${doc.id})" class="btn btn-primary btn-sm">
                  <i data-lucide="brain" class="w-4 h-4 mr-2"></i>
                  AI Analysis
                </button>
                ${doc.status === 'archived' ? `
                  <button onclick="requestRelease(${doc.id})" class="btn btn-warning btn-sm">
                    <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                    Request Release
                  </button>
                ` : ''}
              </div>
            </div>
          `;

          // Show the modal
          Swal.fire({
            title: 'Document Details',
            html: modalContent,
            width: '800px',
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: 'Close',
            customClass: {
              popup: 'text-left'
            },
            didOpen: () => {
              // Re-initialize Lucide icons in the modal
              lucide.createIcons();
            }
          });
        } else {
          throw new Error('Invalid response format');
        }
      })
      .catch(error => {
        console.error('Error fetching document details:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to load document details. Please try again.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
      });
    }

    function downloadDocument(documentId) {
      window.location.href = `/document/${documentId}/download`;
    }

    function disposeDocument(documentId) {
      if (confirm('Are you sure you want to permanently dispose of this document? This action cannot be undone.')) {
        fetch(`/document/${documentId}/dispose`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Document disposed successfully');
            window.location.reload();
          } else {
            alert('Error disposing document: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error disposing document');
        });
      }
    }

    function refreshTable() {
      location.reload();
    }

    // Filter functions
    function clearFilters() {
      document.getElementById('searchInput').value = '';
      document.getElementById('categoryFilter').value = '';
      document.getElementById('sourceFilter').value = '';
      filterDocuments();
    }

    function filterDocuments() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const categoryFilter = document.getElementById('categoryFilter').value;
      const sourceFilter = document.getElementById('sourceFilter').value;
      
      const rows = document.querySelectorAll('tbody tr');
      
      rows.forEach(row => {
        let showRow = true;
        
        // Search filter
        if (searchTerm) {
          const title = row.querySelector('td:first-child .font-medium')?.textContent?.toLowerCase() || '';
          const description = row.querySelector('td:first-child .text-sm')?.textContent?.toLowerCase() || '';
          if (!title.includes(searchTerm) && !description.includes(searchTerm)) {
            showRow = false;
          }
        }
        
        // Category filter
        if (categoryFilter && showRow) {
          const category = row.querySelector('td:nth-child(2) .badge')?.textContent?.toLowerCase() || '';
          if (category !== categoryFilter.replace('_', ' ')) {
            showRow = false;
          }
        }
        
        // Source filter
        if (sourceFilter && showRow) {
          const source = row.querySelector('td:nth-child(3) .badge')?.textContent?.toLowerCase() || '';
          if (source !== sourceFilter.replace('_', ' ')) {
            showRow = false;
          }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
      });
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('searchInput').addEventListener('input', filterDocuments);
      document.getElementById('categoryFilter').addEventListener('change', filterDocuments);
      document.getElementById('sourceFilter').addEventListener('change', filterDocuments);
      
      // Add Enter key support for password field (only for administrators)
      if (isAdministrator) {
        const passwordField = document.getElementById('unarchivePassword');
        if (passwordField) {
          passwordField.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
              confirmUnarchive();
            }
          });
        }
      }
    });
  </script>
</body>
</html>
