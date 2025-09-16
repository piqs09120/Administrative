<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receive Documents - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @vite(['resources/css/soliera.css'])
</head>
<body class="bg-gray-50">
  <div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    @include('partials.sidebarr')
    <!-- Main content -->
    <div class="flex flex-col flex-1 overflow-hidden">
      <!-- Header -->
      @include('partials.navbar')

      <!-- Main content area -->
      <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
        <!-- Page Header -->
        <div class="pb-5 mb-6 animate-fadeIn">
          <div class="border-b-2 border-gray-500 w-full"></div>
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
              <a href="{{ route('document.index') }}" class="btn btn-ghost btn-sm" title="Back to Documents">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
              </a>
              <div>
                <h1 class="text-2xl font-semibold bg-white bg-clip-text text-[#191970]" style="color: var(--color-charcoal-ink);">Receive Documents</h1>
                <p class="text-gray-600">Process and manage incoming documents</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <a href="{{ route('document.reports') }}" class="btn btn-outline">
                <i data-lucide="bar-chart" class="w-4 h-4 mr-2"></i>Reports
              </a>
              <button onclick="openBulkProcessModal()" class="btn btn-primary">
                <i data-lucide="upload" class="w-4 h-4 mr-2"></i>Bulk Process
              </button>
            </div>
          </div>
        </div>

        <!-- Document Processing Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <!-- Pending Review -->
          <div class="card bg-white shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="card-body">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-yellow-100 text-yellow-800 rounded-full w-12 h-12">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-warning">Pending</div>
              </div>
              <h3 class="card-title text-lg">Pending Review</h3>
              <p class="text-gray-600 mb-4">Documents awaiting your review and classification</p>
              <div class="text-3xl font-bold text-yellow-600 mb-2">{{ $documents->where('status', 'pending_review')->count() }}</div>
              <button onclick="viewPendingDocuments()" class="btn btn-warning btn-sm w-full">
                <i data-lucide="eye" class="w-4 h-4 mr-2"></i>Review Now
              </button>
            </div>
          </div>

          <!-- Recently Received -->
          <div class="card bg-white shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="card-body">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-blue-100 text-blue-800 rounded-full w-12 h-12">
                    <i data-lucide="inbox" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-info">New</div>
              </div>
              <h3 class="card-title text-lg">Recently Received</h3>
              <p class="text-gray-600 mb-4">Documents received in the last 24 hours</p>
              <div class="text-3xl font-bold text-blue-600 mb-2">{{ $documents->where('created_at', '>=', now()->subDay())->count() }}</div>
              <button onclick="viewRecentDocuments()" class="btn btn-info btn-sm w-full">
                <i data-lucide="clock" class="w-4 h-4 mr-2"></i>View Recent
              </button>
            </div>
          </div>

          <!-- Expiring Soon -->
          <div class="card bg-white shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="card-body">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder">
                  <div class="bg-red-100 text-red-800 rounded-full w-12 h-12">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-error">Urgent</div>
              </div>
              <h3 class="card-title text-lg">Expiring Soon</h3>
              <p class="text-gray-600 mb-4">Documents with retention periods ending soon</p>
              <div class="text-3xl font-bold text-red-600 mb-2">{{ $documents->whereNotNull('retention_until')->where('retention_until', '<=', now()->addDays(30))->count() }}</div>
              <button onclick="viewExpiringDocuments()" class="btn btn-error btn-sm w-full">
                <i data-lucide="alert-circle" class="w-4 h-4 mr-2"></i>Review Urgent
              </button>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="card bg-white shadow-xl mb-8">
          <div class="card-body">
            <h3 class="card-title mb-4">
              <i data-lucide="zap" class="w-5 h-5 text-yellow-500"></i>
              Quick Actions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <button onclick="classifyDocuments()" class="btn btn-outline">
                <i data-lucide="tag" class="w-4 h-4 mr-2"></i>
                Auto-Classify
              </button>
              <button onclick="archiveOldDocuments()" class="btn btn-outline">
                <i data-lucide="archive" class="w-4 h-4 mr-2"></i>
                Archive Old
              </button>
              <button onclick="generateReport()" class="btn btn-outline">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                Generate Report
              </button>
              <button onclick="exportData()" class="btn btn-outline">
                <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                Export Data
              </button>
            </div>
          </div>
        </div>

        <!-- Complete Documents Table -->
        <div class="card bg-white shadow-xl">
          <div class="card-body">
            <div class="flex items-center justify-between mb-6">
              <h3 class="card-title text-xl">
                <i data-lucide="file-text" class="w-6 h-6 text-blue-500"></i>
                Document Management System - All Documents
              </h3>
              <div class="flex items-center gap-3">
                <div class="text-sm text-gray-600">
                  Total: <span class="font-semibold">{{ $documents->count() }}</span> documents
                </div>
                <button onclick="refreshTable()" class="btn btn-sm btn-outline">
                  <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
              </div>
            </div>
            
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr class="bg-gray-50">
                    <th class="text-left py-4 px-4 font-semibold text-gray-700 w-16">#</th>
                    <th class="text-left py-4 px-4 font-semibold text-gray-700">Document Information</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Department</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Confidentiality</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Status</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Retention</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Received</th>
                    <th class="text-center py-4 px-4 font-semibold text-gray-700 w-32">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($documents as $index => $document)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                      <!-- ID Column -->
                      <td class="py-4 px-4">
                        <div class="text-sm font-medium text-gray-500">
                          #{{ $index + 1 }}
                        </div>
                      </td>
                      
                      <!-- Document Information Column -->
                      <td class="py-4 px-4">
                        <div class="flex items-center space-x-3">
                          <!-- Document Icon -->
                          <div class="avatar placeholder">
                            <div class="bg-blue-100 text-blue-800 rounded-full w-10 h-10 flex items-center justify-center">
                              <i data-lucide="file-text" class="w-5 h-5"></i>
                            </div>
                          </div>
                          <!-- Document Details -->
                          <div>
                            <h4 class="font-semibold text-gray-900">{{ $document->title ?: 'Untitled Document' }}</h4>
                            <p class="text-sm text-gray-500">{{ Str::limit($document->description, 50) ?: 'No description' }}</p>
                            @if($document->document_uid)
                              <p class="text-xs text-gray-400">ID: {{ $document->document_uid }}</p>
                            @endif
                          </div>
                        </div>
                      </td>
                      
                      <!-- Department Column -->
                      <td class="py-4 px-4 text-center">
                        <span class="badge badge-outline">
                          {{ $document->department ?: 'Unassigned' }}
                        </span>
                      </td>
                      
                      <!-- Confidentiality Column -->
                      <td class="py-4 px-4 text-center">
                        @php
                          $confidentialityConfig = [
                            'public' => ['class' => 'badge-success', 'icon' => 'globe'],
                            'internal' => ['class' => 'badge-warning', 'icon' => 'shield'], 
                            'restricted' => ['class' => 'badge-error', 'icon' => 'lock']
                          ];
                          $config = $confidentialityConfig[$document->confidentiality] ?? ['class' => 'badge-neutral', 'icon' => 'file'];
                        @endphp
                        <span class="badge {{ $config['class'] }} gap-1">
                          <i data-lucide="{{ $config['icon'] }}" class="w-3 h-3"></i>
                          {{ ucfirst($document->confidentiality ?: 'Not Set') }}
                        </span>
                      </td>
                      
                      <!-- Status Column -->
                      <td class="py-4 px-4 text-center">
                        @php
                          $statusConfig = [
                            'active' => ['class' => 'badge-success', 'icon' => 'check-circle'],
                            'pending_review' => ['class' => 'badge-warning', 'icon' => 'clock'],
                            'archived' => ['class' => 'badge-neutral', 'icon' => 'archive'],
                            'draft' => ['class' => 'badge-info', 'icon' => 'edit']
                          ];
                          $config = $statusConfig[$document->status] ?? ['class' => 'badge-neutral', 'icon' => 'file'];
                        @endphp
                        <span class="badge {{ $config['class'] }} gap-1">
                          <i data-lucide="{{ $config['icon'] }}" class="w-3 h-3"></i>
                          {{ ucfirst(str_replace('_', ' ', $document->status ?: 'Unknown')) }}
                        </span>
                      </td>
                      
                      <!-- Retention Column -->
                      <td class="py-4 px-4 text-center">
                        @if($document->retention_until)
                          @php
                            $daysUntilExpiry = now()->diffInDays($document->retention_until, false);
                            $isExpiringSoon = $daysUntilExpiry <= 30 && $daysUntilExpiry >= 0;
                            $isExpired = $daysUntilExpiry < 0;
                          @endphp
                          <div class="text-sm">
                            <div class="font-medium {{ $isExpired ? 'text-red-600' : ($isExpiringSoon ? 'text-yellow-600' : 'text-gray-600') }}">
                              {{ $document->retention_until->format('M d, Y') }}
                            </div>
                            <div class="text-xs {{ $isExpired ? 'text-red-500' : ($isExpiringSoon ? 'text-yellow-500' : 'text-gray-500') }}">
                              @if($isExpired)
                                Expired {{ abs($daysUntilExpiry) }} days ago
                              @elseif($isExpiringSoon)
                                Expires in {{ $daysUntilExpiry }} days
                              @else
                                {{ $daysUntilExpiry }} days remaining
                              @endif
                            </div>
                          </div>
                        @else
                          <span class="text-sm text-gray-400">No retention policy</span>
                        @endif
                      </td>
                      
                      <!-- Received Date Column -->
                      <td class="py-4 px-4 text-center">
                        <div class="text-sm">
                          <div class="font-medium text-gray-600">{{ $document->created_at->format('M d, Y') }}</div>
                          <div class="text-gray-500">{{ $document->created_at->format('g:i A') }}</div>
                        </div>
                      </td>
                      
                      <!-- Actions Column -->
                      <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center space-x-1">
                          <!-- View Button -->
                          <button onclick="viewDocument({{ $document->id }})" 
                                  class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                  title="View Document">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                          </button>
                          
                          <!-- Download Button -->
                          @if($document->file_path)
                            <a href="{{ Storage::url($document->file_path) }}" 
                               target="_blank"
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                               title="Download Document">
                              <i data-lucide="download" class="w-4 h-4"></i>
                            </a>
                          @endif
                          
                          <!-- Process Button -->
                          <button onclick="processDocument({{ $document->id }})" 
                                  class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200"
                                  title="Process Document">
                            <i data-lucide="settings" class="w-4 h-4"></i>
                          </button>
                          
                          <!-- Archive Button -->
                          @if($document->status !== 'archived')
                            <button onclick="archiveDocument({{ $document->id }})" 
                                    class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors duration-200"
                                    title="Archive Document">
                              <i data-lucide="archive" class="w-4 h-4"></i>
                            </button>
                          @endif
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="8" class="py-12 text-center">
                        <div class="flex flex-col items-center">
                          <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="inbox" class="w-10 h-10 text-gray-400"></i>
                          </div>
                          <h3 class="text-lg font-semibold text-gray-600 mb-2">No Documents Found</h3>
                          <p class="text-gray-500 text-sm mb-4">No documents have been received yet.</p>
                          <div class="flex gap-3">
                            <button onclick="refreshTable()" class="btn btn-outline">
                              <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                              Refresh
                            </button>
                            <a href="{{ route('document.reports') }}" class="btn btn-primary">
                              <i data-lucide="bar-chart" class="w-4 h-4 mr-2"></i>
                              View Reports
                            </a>
                          </div>
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

  @include('partials.soliera_js')
  
  <script>
    function viewPendingDocuments() {
      window.location.href = "{{ route('document.index') }}?status=pending_review";
    }

    function viewRecentDocuments() {
      window.location.href = "{{ route('document.index') }}?recent=1";
    }

    function viewExpiringDocuments() {
      window.location.href = "{{ route('document.index') }}?expiring=1";
    }

    function classifyDocuments() {
      // Auto-classify documents using AI
      fetch('/document/auto-classify', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Documents classified successfully!');
          location.reload();
        } else {
          alert('Error classifying documents: ' + data.message);
        }
      });
    }

    function archiveOldDocuments() {
      if (confirm('Archive documents older than 1 year?')) {
        fetch('/document/archive-old', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Old documents archived successfully!');
            location.reload();
          } else {
            alert('Error archiving documents: ' + data.message);
          }
        });
      }
    }

    function generateReport() {
      window.open("{{ route('document.reports') }}", '_blank');
    }

    function exportData() {
      window.open("{{ route('document.reports.basic') }}", '_blank');
    }

    function viewDocument(id) {
      console.log('viewDocument called with ID:', id);
      viewDocumentDetails(id);
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

    function processDocument(id) {
      // Open document processing modal
      alert('Document processing feature coming soon!');
    }

    function openBulkProcessModal() {
      alert('Bulk processing feature coming soon!');
    }

    function refreshTable() {
      location.reload();
    }

    function archiveDocument(id) {
      if (confirm('Are you sure you want to archive this document?')) {
        fetch(`/document/${id}/archive`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Document archived successfully!');
            location.reload();
          } else {
            alert('Error archiving document: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error archiving document');
        });
      }
    }
  </script>
</body>
</html>
