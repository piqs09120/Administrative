<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Legal Documents Management - Soliera</title>
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
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Legal Documents</h1>
          <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Manage legal documents, contracts, and legal materials</p>
        </div>

        <!-- Document Management Section -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <!-- Header with Search and New Button -->
          <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                  <i data-lucide="list" class="w-5 h-5 text-blue-600"></i>
                  Document List
                </h3>
                <!-- Search Bar -->
                <div class="relative">
                  <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                  <input type="text" 
                         id="searchInput"
                         placeholder="Search documents..." 
                         class="input input-bordered input-sm w-64 pl-10 pr-4 bg-gray-50 border-gray-200 focus:bg-white focus:border-blue-300">
                </div>
              </div>
              
              <!-- Add Document Button -->
              <button onclick="openUploadModal()" class="btn btn-primary btn-sm">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                Add Document
              </button>
            </div>

            <!-- Filters Row -->
            <div class="flex items-center gap-4">
              <!-- Category Filter -->
              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Category:</label>
                <select id="categoryFilter" class="select select-bordered select-sm w-40">
                  <option value="">All Categories</option>
                  <option value="contract">Contract</option>
                  <option value="legal_notice">Legal Notice</option>
                  <option value="policy">Policy</option>
                  <option value="compliance">Compliance</option>
                  <option value="financial">Financial</option>
                  <option value="report">Report</option>
                  <option value="memorandum">Memorandum</option>
                  <option value="affidavit">Affidavit</option>
                  <option value="subpoena">Subpoena</option>
                  <option value="cease_desist">Cease & Desist</option>
                  <option value="legal_brief">Legal Brief</option>
                </select>
              </div>

              <!-- Status Filter -->
              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Status:</label>
                <select id="statusFilter" class="select select-bordered select-sm w-32">
                  <option value="">All Status</option>
                  <option value="active">Active</option>
                  <option value="pending_review">Pending Review</option>
                  <option value="archived">Archived</option>
                  <option value="draft">Draft</option>
                </select>
              </div>

              <!-- Clear Filters Button -->
              <button onclick="clearFilters()" class="btn btn-ghost btn-xs text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-3 h-3 mr-1"></i>
                Clear
              </button>
            </div>
          </div>

          <!-- Documents Table -->
          <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
              <thead>
                <tr class="bg-gray-50">
                  <th class="text-left py-3 px-4 font-medium text-gray-700">Document</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Category</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Status</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Uploaded By</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Date</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($documents as $document)
                  <tr class="hover:bg-gray-50 transition-colors" data-document-id="{{ $document->id }}">
                    <td class="py-3 px-4">
                      <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                          @php
                            $fileExtension = pathinfo($document->file_path ?? '', PATHINFO_EXTENSION);
                            $iconColor = 'text-blue-600';
                            
                            switch(strtolower($fileExtension)) {
                              case 'pdf':
                                $iconColor = 'text-red-600';
                                break;
                              case 'doc':
                              case 'docx':
                                $iconColor = 'text-blue-600';
                                break;
                              case 'xls':
                              case 'xlsx':
                                $iconColor = 'text-green-600';
                                break;
                              case 'ppt':
                              case 'pptx':
                                $iconColor = 'text-orange-600';
                                break;
                              default:
                                $iconColor = 'text-gray-600';
                            }
                          @endphp
                          <i data-lucide="file-text" class="w-5 h-5 {{ $iconColor }}"></i>
                        </div>
                        <div>
                          <div class="font-medium text-gray-900">{{ $document->title }}</div>
                          <div class="text-sm text-gray-500">{{ Str::limit($document->description, 50) }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="py-3 px-4 text-center">
                      <span class="badge badge-outline badge-sm">{{ ucfirst(str_replace('_', ' ', $document->category ?? 'General')) }}</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                      @php
                        $statusConfig = [
                          'active' => ['class' => 'badge-success badge-sm', 'icon' => 'check-circle'],
                          'pending_review' => ['class' => 'badge-warning badge-sm', 'icon' => 'clock'],
                          'archived' => ['class' => 'badge-neutral badge-sm', 'icon' => 'archive'],
                          'draft' => ['class' => 'badge-info badge-sm', 'icon' => 'edit-3']
                        ];
                        $status = $document->status ?? 'active';
                        $config = $statusConfig[$status] ?? $statusConfig['active'];
                      @endphp
                      <span class="{{ $config['class'] }} gap-1">
                        <i data-lucide="{{ $config['icon'] }}" class="w-3 h-3"></i>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                      </span>
                    </td>
                    <td class="py-3 px-4 text-center text-sm text-gray-600">{{ $document->uploader->name ?? 'Unknown' }}</td>
                    <td class="py-3 px-4 text-center text-sm text-gray-600">{{ $document->created_at->format('M d, Y') }}</td>
                    <td class="py-3 px-4 text-center">
                      <div class="flex items-center justify-center gap-1">
                        <button onclick="viewDocument({{ $document->id }})" class="btn btn-ghost btn-xs" title="View">
                          <i data-lucide="eye" class="w-4 h-4 text-blue-600"></i>
                        </button>
                        <button onclick="editDocument({{ $document->id }})" class="btn btn-ghost btn-xs" title="Edit">
                          <i data-lucide="edit" class="w-4 h-4 text-green-600"></i>
                        </button>
                        <button onclick="downloadDocument({{ $document->id }})" class="btn btn-ghost btn-xs" title="Delete">
                          <i data-lucide="download" class="w-4 h-4 text-purple-600"></i>
                        </button>
                        <button onclick="deleteDocument({{ $document->id }})" class="btn btn-ghost btn-xs" title="Delete">
                          <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center py-12">
                      <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                          <i data-lucide="folder" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No Legal Documents Found</h3>
                        <p class="text-gray-500 text-sm">No documents available at the moment.</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          @if($documents->hasPages())
            <div class="flex justify-center mt-6">
              {{ $documents->appends(['search' => $search ?? '', 'category' => $category ?? '', 'status' => $status ?? ''])->links() }}
            </div>
          @endif
        </div>
      </main>
    </div>
  </div>

  <!-- Upload Document Modal -->
  <div id="uploadModal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="upload" class="w-8 h-8 text-blue-500"></i>
          Upload Legal Document
        </h3>
        <button onclick="closeUploadModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <form id="uploadForm" action="{{ route('document.store') }}" method="POST" enctype="multipart/form-data" onsubmit="handleUploadSubmit(event)">
        @csrf
        <input type="hidden" name="source" value="legal_management">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          
          <!-- Left Column: Form Fields -->
          <div class="space-y-6">
            <!-- Title Field -->
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Document Title *</span>
              </label>
              <input type="text" name="title" id="uploadTitle" class="input input-bordered w-full" 
                     placeholder="Enter document title" required>
              <div class="label">
                <span class="label-text-alt">Enter a descriptive title for the document</span>
              </div>
            </div>

            <!-- Category Field -->
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Category *</span>
              </label>
              <select name="category" id="uploadCategory" class="select select-bordered w-full" required>
                <option value="">Select category</option>
                <option value="contract">Contract</option>
                <option value="legal_notice">Legal Notice</option>
                <option value="policy">Policy</option>
                <option value="compliance">Compliance</option>
                <option value="financial">Financial</option>
                <option value="report">Report</option>
                <option value="memorandum">Memorandum</option>
                <option value="affidavit">Affidavit</option>
                <option value="subpoena">Subpoena</option>
                <option value="cease_desist">Cease & Desist</option>
                <option value="legal_brief">Legal Brief</option>
              </select>
              <div class="label">
                <span class="label-text-alt">Select the appropriate legal document category</span>
              </div>
            </div>

            <!-- Author Field -->
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Author</span>
              </label>
              <input type="text" name="author" id="uploadAuthor" class="input input-bordered w-full" 
                     placeholder="e.g. Legal Department, Attorney Name">
              <div class="label">
                <span class="label-text-alt">Who authored or prepared this document</span>
              </div>
            </div>

            <!-- Description Field -->
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Description</span>
              </label>
              <textarea name="description" id="uploadDescription" class="textarea textarea-bordered w-full h-24" 
                        placeholder="Brief description of the document's purpose and content..."></textarea>
              <div class="label">
                <span class="label-text-alt">Provide a brief summary of the document</span>
              </div>
            </div>
          </div>

          <!-- Right Column: File Upload Area -->
          <div class="space-y-6">
            <div>
              <h2 class="text-xl font-bold text-gray-800">Document File</h2>
              <p class="text-gray-600">PDF, Word, Excel, PowerPoint, Text files (Max: 10MB)</p>
            </div>

            <!-- File Upload Zone -->
            <div id="uploadZone" class="border-2 border-dashed border-blue-300 rounded-xl p-8 text-center hover:border-blue-400 transition-colors cursor-pointer" 
                 onclick="document.getElementById('document_file').click()" 
                 ondrop="handleDrop(event)" 
                 ondragover="handleDragOver(event)" 
                 ondragleave="handleDragLeave(event)">
              
              <input type="file" name="document_file" id="document_file" class="hidden" 
                     accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required>
              
              <div class="space-y-4">
                <div class="flex justify-center">
                  <div class="w-16 h-16 rounded-full flex items-center justify-center bg-blue-100">
                    <i data-lucide="cloud-upload" class="w-8 h-8 text-blue-500"></i>
                  </div>
                </div>
                <div>
                  <p class="text-lg font-medium text-gray-700">Click to select or drag file</p>
                  <p class="text-sm text-gray-500 mt-2">Max file size: 10MB</p>
                  <p class="text-xs text-gray-400 mt-1">Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT</p>
                </div>
              </div>
            </div>

            <!-- File Preview -->
            <div id="filePreview" class="hidden">
              <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-3">
                  <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                  <div>
                    <p class="font-medium text-green-800" id="fileName"></p>
                    <p class="text-sm text-green-600" id="fileSize"></p>
                  </div>
                  <button type="button" onclick="removeFile()" class="btn btn-ghost btn-sm">
                    <i data-lucide="x" class="w-4 h-4"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- File Validation Errors -->
            <div id="fileErrors" class="hidden">
              <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center gap-3">
                  <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                  <div>
                    <p class="font-medium text-red-800" id="errorMessage"></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
              <button type="submit" id="uploadSubmitBtn" class="btn btn-primary btn-lg w-full">
                <i data-lucide="upload" class="w-5 h-5 mr-2"></i>
                Upload Legal Document
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Bulk Upload Modal -->
  <div id="bulkUploadModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="folder-plus" class="w-6 h-6 text-blue-500"></i>
          Bulk Upload Legal Documents
        </h3>
        <button onclick="closeBulkUploadModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <form id="bulkUploadForm" action="{{ route('document.bulkUpload') }}" method="POST" enctype="multipart/form-data" onsubmit="handleBulkUploadSubmit(event)">
        @csrf
        <input type="hidden" name="source" value="legal_management">
        
        <div class="space-y-6">
          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Category for All Documents</span>
            </label>
            <select name="category" class="select select-bordered w-full">
              <option value="">Select category</option>
              <option value="contract">Contract</option>
              <option value="legal_notice">Legal Notice</option>
              <option value="policy">Policy</option>
              <option value="compliance">Compliance</option>
              <option value="financial">Financial</option>
              <option value="report">Report</option>
            </select>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Multiple Files</span>
            </label>
            <input type="file" name="document_files[]" multiple class="file-input file-input-bordered w-full" 
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required>
            <label class="label">
              <span class="label-text-alt">Select multiple files (Max 10 files, 10MB each)</span>
            </label>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Description Template</span>
            </label>
            <textarea name="description_template" class="textarea textarea-bordered w-full h-20" 
                      placeholder="Description template for all documents (optional)"></textarea>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="closeBulkUploadModal()" class="btn btn-outline">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
              Bulk Upload
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Document Modal -->
  <div id="editModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="edit" class="w-6 h-6 text-blue-500"></i>
          Edit Legal Document
        </h3>
        <button onclick="closeEditModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <form id="editForm" method="POST" onsubmit="handleEditSubmit(event)">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Document Title *</span>
            </label>
            <input type="text" name="title" id="editTitle" class="input input-bordered w-full" required>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Category</span>
            </label>
            <select name="category" id="editCategory" class="select select-bordered w-full">
              <option value="contract">Contract</option>
              <option value="legal_notice">Legal Notice</option>
              <option value="policy">Policy</option>
              <option value="compliance">Compliance</option>
              <option value="financial">Financial</option>
              <option value="report">Report</option>
              <option value="memorandum">Memorandum</option>
              <option value="affidavit">Affidavit</option>
              <option value="subpoena">Subpoena</option>
              <option value="cease_desist">Cease & Desist</option>
              <option value="legal_brief">Legal Brief</option>
            </select>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Description</span>
            </label>
            <textarea name="description" id="editDescription" class="textarea textarea-bordered w-full h-24"></textarea>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="closeEditModal()" class="btn btn-outline">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i data-lucide="save" class="w-4 h-4 mr-2"></i>
              Update Document
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    // Real-time date and time
    function updateDateTime() {
      const now = new Date();
      const dateElement = document.getElementById('currentDate');
      const timeElement = document.getElementById('currentTime');
      
      const dateOptions = { weekday: 'short', month: 'short', day: 'numeric' };
      const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
      
      if (dateElement) dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
      if (timeElement) timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
    }

    // Modal functions
    function openUploadModal() {
      const modal = document.getElementById('uploadModal');
      modal.classList.add('modal-open');
    }

    function closeUploadModal() {
      const modal = document.getElementById('uploadModal');
      modal.classList.remove('modal-open');
      const form = document.getElementById('uploadForm');
      if (form) form.reset();
      document.getElementById('filePreview').classList.add('hidden');
      document.getElementById('fileErrors').classList.add('hidden');
      document.getElementById('uploadZone').classList.remove('border-blue-500', 'bg-blue-50');
      document.getElementById('uploadZone').innerHTML = `
        <input type="file" name="document_file" id="document_file" class="hidden" 
               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required>
        <div class="space-y-4">
          <div class="flex justify-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center bg-blue-100">
              <i data-lucide="cloud-upload" class="w-8 h-8 text-blue-500"></i>
            </div>
          </div>
          <div>
            <p class="text-lg font-medium text-gray-700">Click to select or drag file</p>
            <p class="text-sm text-gray-500 mt-2">Max file size: 10MB</p>
            <p class="text-xs text-gray-400 mt-1">Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT</p>
          </div>
        </div>
      `;
    }

    function openBulkUploadModal() {
      const modal = document.getElementById('bulkUploadModal');
      modal.classList.add('modal-open');
    }

    function closeBulkUploadModal() {
      const modal = document.getElementById('bulkUploadModal');
      modal.classList.remove('modal-open');
      const form = document.getElementById('bulkUploadForm');
      if (form) form.reset();
    }

    function openEditModal() {
      const modal = document.getElementById('editModal');
      modal.classList.add('modal-open');
    }

    function closeEditModal() {
      const modal = document.getElementById('editModal');
      modal.classList.remove('modal-open');
    }

    // File upload functionality
    function handleDragOver(event) {
      event.preventDefault();
      event.currentTarget.classList.add('border-blue-500', 'bg-blue-50');
    }

    function handleDragLeave(event) {
      event.preventDefault();
      event.currentTarget.classList.remove('border-blue-500', 'bg-blue-50');
    }

    function handleDrop(event) {
      event.preventDefault();
      event.currentTarget.classList.remove('border-blue-500', 'bg-blue-50');
      
      const files = event.dataTransfer.files;
      if (files.length > 0) {
        document.getElementById('document_file').files = files;
        updateFilePreview(files[0]);
      }
    }

    function updateFilePreview(file) {
      const preview = document.getElementById('filePreview');
      const fileName = document.getElementById('fileName');
      const fileSize = document.getElementById('fileSize');
      
      fileName.textContent = file.name;
      fileSize.textContent = formatFileSize(file.size);
      preview.classList.remove('hidden');
    }

    function formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // File input change handler
    document.getElementById('document_file').addEventListener('change', function(e) {
      if (e.target.files.length > 0) {
        updateFilePreview(e.target.files[0]);
      }
    });

    // Document CRUD functions
    function viewDocument(documentId) {
      // Redirect to the document view page
      window.location.href = `/legal/documents/${documentId}`;
    }
    
    function editDocument(documentId) {
      // Redirect to the document edit page
      window.location.href = `/legal/documents/${documentId}/edit`;
    }
    
    function downloadDocument(documentId) {
      // Show loading state
      const button = event.target.closest('button');
      const originalHTML = button.innerHTML;
      button.innerHTML = '<i class="loading loading-spinner"></i>';
      button.disabled = true;
      
      // Make download request
      fetch(`/legal/documents/${documentId}/download`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => {
        if (response.ok) {
          return response.json();
        } else {
          throw new Error('Download failed');
        }
      })
      .then(data => {
        if (data.success) {
          // Redirect to the download URL
          window.location.href = data.download_url;
        } else {
          throw new Error(data.message || 'Download failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Show error message
        const errorMessage = document.createElement('div');
        errorMessage.className = 'alert alert-error mb-6';
        errorMessage.innerHTML = '<i data-lucide="alert-circle" class="w-5 h-5"></i><span>Error downloading document. Please try again.</span>';
        document.querySelector('main').insertBefore(errorMessage, document.querySelector('main').firstChild);
        lucide.createIcons();
        
        // Remove error message after 5 seconds
        setTimeout(() => {
          if (errorMessage.parentNode) {
            errorMessage.parentNode.removeChild(errorMessage);
          }
        }, 5000);
      })
      .finally(() => {
        // Restore button
        button.innerHTML = originalHTML;
        button.disabled = false;
      });
    }



    function deleteDocument(documentId) {
      // Create a custom confirmation dialog
      const confirmDialog = document.createElement('div');
      confirmDialog.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
      confirmDialog.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md mx-4">
          <div class="flex items-center gap-3 mb-4">
            <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
            <h3 class="text-lg font-semibold text-gray-800">Confirm Deletion</h3>
          </div>
          <p class="text-gray-600 mb-6">Are you sure you want to delete this legal document? This action cannot be undone and will permanently remove the document from the system.</p>
          <div class="flex justify-end gap-3">
            <button onclick="this.closest('.fixed').remove()" class="btn btn-outline">Cancel</button>
            <button onclick="confirmDeleteDocument(${documentId}, this)" class="btn btn-error">Delete Document</button>
          </div>
        </div>
      `;
      
      document.body.appendChild(confirmDialog);
      lucide.createIcons();
    }

    function confirmDeleteDocument(documentId, button) {
      // Show loading state
      const originalText = button.innerHTML;
      button.innerHTML = '<i class="loading loading-spinner"></i> Deleting...';
      button.disabled = true;
      
      // Remove confirmation dialog
      button.closest('.fixed').remove();
      
      fetch(`/legal/documents/${documentId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => {
        if (response.ok) {
          return response.json();
        } else {
          throw new Error('Delete failed');
        }
      })
      .then(data => {
        if (data.success) {
          // Show success message
          const successMessage = document.createElement('div');
          successMessage.className = 'alert alert-success mb-6';
          successMessage.innerHTML = '<i data-lucide="check-circle" class="w-5 h-5"></i><span>' + data.message + '</span>';
          document.querySelector('main').insertBefore(successMessage, document.querySelector('main').firstChild);
          lucide.createIcons();
          
          // Remove the deleted row from the table
          const row = document.querySelector(`tr[data-document-id="${documentId}"]`);
          if (row) {
            row.remove();
          }
          
          // Update document count
          const countElement = document.querySelector('.text-sm.text-gray-600');
          if (countElement) {
            const currentCount = parseInt(countElement.textContent);
            if (!isNaN(currentCount) && currentCount > 0) {
              countElement.textContent = `${currentCount - 1} legal documents found`;
            }
          }
          
          // Remove success message after 5 seconds
          setTimeout(() => {
            if (successMessage.parentNode) {
              successMessage.parentNode.removeChild(successMessage);
            }
          }, 5000);
        } else {
          throw new Error(data.message || 'Delete failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Show error message
        const errorMessage = document.createElement('div');
        errorMessage.className = 'alert alert-error mb-6';
        errorMessage.innerHTML = '<i data-lucide="alert-circle" class="w-5 h-5"></i><span>Error deleting document. Please try again.</span>';
        document.querySelector('main').insertBefore(errorMessage, document.querySelector('main').firstChild);
        lucide.createIcons();
        
        // Remove error message after 5 seconds
        setTimeout(() => {
          if (errorMessage.parentNode) {
            errorMessage.parentNode.removeChild(errorMessage);
          }
        }, 5000);
      });
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      updateDateTime();
      setInterval(updateDateTime, 1000);
    });

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
      const uploadModal = document.getElementById('uploadModal');
      const bulkUploadModal = document.getElementById('bulkUploadModal');
      const editModal = document.getElementById('editModal');
      
      if (event.target === uploadModal) {
        closeUploadModal();
      }
      if (event.target === bulkUploadModal) {
        closeBulkUploadModal();
      }
      if (event.target === editModal) {
        closeEditModal();
      }
    });

    // Handle download document
    function downloadDocument(event, documentId) {
      event.preventDefault();
      
      // Show loading state
      const downloadLink = event.target.closest('a');
      const originalText = downloadLink.innerHTML;
      downloadLink.innerHTML = '<i class="loading loading-spinner"></i> Downloading...';
      
      fetch(`/legal/documents/${documentId}/download`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => {
        if (response.ok) {
          return response.json();
        } else {
          throw new Error('Download failed');
        }
      })
      .then(data => {
        if (data.success) {
          // Redirect to the download URL
          window.location.href = data.download_url;
        } else {
          throw new Error(data.message || 'Download failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Show error message
        const errorMessage = document.createElement('div');
        errorMessage.className = 'alert alert-error mb-6';
        errorMessage.innerHTML = '<i data-lucide="alert-circle" class="w-5 h-5"></i><span>Error downloading document. Please try again.</span>';
        document.querySelector('main').insertBefore(errorMessage, document.querySelector('main').firstChild);
        lucide.createIcons();
        
        // Remove error message after 5 seconds
        setTimeout(() => {
          if (errorMessage.parentNode) {
            errorMessage.parentNode.removeChild(errorMessage);
          }
        }, 5000);
      })
      .finally(() => {
        // Restore download link
        downloadLink.innerHTML = originalText;
      });
    }
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeUploadModal();
        closeBulkUploadModal();
        closeEditModal();
      }
    });
    
    // Handle edit form submission
    function handleEditSubmit(event) {
      event.preventDefault();
      
      const form = event.target;
      const formData = new FormData(form);
      
      // Show loading state
      const submitButton = form.querySelector('button[type="submit"]');
      const originalText = submitButton.innerHTML;
      submitButton.innerHTML = '<i class="loading loading-spinner"></i> Updating...';
      submitButton.disabled = true;
      
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => {
        if (response.ok) {
          return response.json();
        } else {
          throw new Error('Update failed');
        }
      })
      .then(data => {
        if (data.success) {
          // Show success message
          const successMessage = document.createElement('div');
          successMessage.className = 'alert alert-success mb-6';
          successMessage.innerHTML = '<i data-lucide="check-circle" class="w-5 h-5"></i><span>' + data.message + '</span>';
          document.querySelector('main').insertBefore(successMessage, document.querySelector('main').firstChild);
          lucide.createIcons();
          
          // Close modal
          closeEditModal();
          
          // Reload the page to show the updated document
          setTimeout(() => {
            window.location.reload();
          }, 2000);
        } else {
          throw new Error(data.message || 'Update failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Show error message
        const errorMessage = document.createElement('div');
        errorMessage.className = 'alert alert-error mb-6';
        errorMessage.innerHTML = '<i data-lucide="alert-circle" class="w-5 h-5"></i><span>Error updating document. Please try again.</span>';
        document.querySelector('main').insertBefore(errorMessage, document.querySelector('main').firstChild);
        lucide.createIcons();
        
        // Remove error message after 5 seconds
        setTimeout(() => {
          if (errorMessage.parentNode) {
            errorMessage.parentNode.removeChild(errorMessage);
          }
        }, 5000);
      })
      .finally(() => {
        // Restore submit button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
      });
    }
    
    // Add new document to the table dynamically
    function addDocumentToTable(documentData) {
      const tbody = document.querySelector('tbody');
      const emptyRow = tbody.querySelector('tr:not([data-document-id])');
      
      // Remove empty state row if it exists
      if (emptyRow) {
        emptyRow.remove();
      }
      
      // Create new row for the document
      const newRow = document.createElement('tr');
      newRow.className = 'hover:bg-gray-50 transition-colors';
      newRow.setAttribute('data-document-id', documentData.id);
      
      // Determine file icon color based on extension
      const fileExtension = documentData.file_path ? documentData.file_path.split('.').pop().toLowerCase() : '';
      let iconColor = 'text-blue-600';
      
      switch(fileExtension) {
        case 'pdf':
          iconColor = 'text-red-600';
          break;
        case 'doc':
        case 'docx':
          iconColor = 'text-blue-600';
          break;
        case 'xls':
        case 'xlsx':
          iconColor = 'text-green-600';
          break;
        case 'ppt':
        case 'pptx':
          iconColor = 'text-orange-600';
          break;
        default:
          iconColor = 'text-gray-600';
      }
      
      // Format the category for display
      const categoryDisplay = documentData.category ? 
        documentData.category.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 
        'General';
      
      // Format the status for display
      const statusDisplay = documentData.status ? 
        documentData.status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 
        'Active';
      
      // Get status badge class
      const statusConfig = {
        'active': 'badge-success',
        'pending_review': 'badge-warning', 
        'archived': 'badge-neutral',
        'draft': 'badge-info'
      };
      const statusClass = statusConfig[documentData.status] || 'badge-success';
      
      // Create row content
      newRow.innerHTML = `
        <td class="py-3 px-4">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
              <i data-lucide="file-text" class="w-5 h-5 ${iconColor}"></i>
            </div>
            <div>
              <div class="font-medium text-gray-900">${documentData.title || 'Untitled Document'}</div>
              <div class="text-sm text-gray-500">${documentData.description || 'No description available'}</div>
            </div>
          </div>
        </td>
        <td class="py-3 px-4 text-center">
          <span class="badge badge-outline badge-sm">${categoryDisplay}</span>
        </td>
        <td class="py-3 px-4 text-center">
          <span class="badge ${statusClass} badge-sm gap-1">
            <i data-lucide="check-circle" class="w-3 h-3"></i>
            ${statusDisplay}
          </span>
        </td>
        <td class="py-3 px-4 text-center text-sm text-gray-600">${documentData.uploader_name || 'Unknown'}</td>
        <td class="py-3 px-4 text-center text-sm text-gray-600">${documentData.created_at ? new Date(documentData.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
        <td class="py-3 px-4 text-center">
          <div class="flex items-center justify-center gap-1">
            <button onclick="viewDocument(${documentData.id})" class="btn btn-ghost btn-xs" title="View">
              <i data-lucide="eye" class="w-4 h-4 text-blue-600"></i>
            </button>
            <button onclick="editDocument(${documentData.id})" class="btn btn-ghost btn-xs" title="Edit">
              <i data-lucide="edit" class="w-4 h-4 text-green-600"></i>
            </button>
            <button onclick="downloadDocument(${documentData.id})" class="btn btn-ghost btn-xs" title="Download">
              <i data-lucide="download" class="w-4 h-4 text-purple-600"></i>
            </button>
            <button onclick="deleteDocument(${documentData.id})" class="btn btn-ghost btn-xs" title="Delete">
              <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
            </button>
          </div>
        </td>
      `;
      
      // Add the new row to the table
      tbody.appendChild(newRow);
      
      // Recreate Lucide icons for the new row
      lucide.createIcons();
      
      // Update filters if they're active
      if (document.getElementById('searchInput').value || 
          document.getElementById('categoryFilter').value || 
          document.getElementById('statusFilter').value) {
        filterDocuments();
      }
      
      // Update table state
      updateTableState();
    }
    
    // Update table state (empty state, counts, etc.)
    function updateTableState() {
      const visibleRows = document.querySelectorAll('tbody tr[data-document-id]:not([style*="display: none"])');
      const tbody = document.querySelector('tbody');
      const emptyStateRow = tbody.querySelector('tr:not([data-document-id])');
      
      if (visibleRows.length === 0) {
        // Show empty state if no documents are visible
        if (!emptyStateRow) {
          const newEmptyRow = document.createElement('tr');
          newEmptyRow.innerHTML = `
            <td colspan="6" class="text-center py-12">
              <div class="flex flex-col items-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="folder" class="w-10 h-10 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-600 mb-2">No Legal Documents Found</h3>
                <p class="text-gray-500 text-sm">No documents available at the moment.</p>
              </div>
            </td>
          `;
          tbody.appendChild(newEmptyRow);
          lucide.createIcons();
        }
      } else {
        // Remove empty state if documents are visible
        if (emptyStateRow) {
          emptyStateRow.remove();
        }
      }
    }
    
    // Handle upload form submission
    function handleUploadSubmit(event) {
      event.preventDefault();
      
      // Validate file before submission
      if (!validateFile()) {
        return false;
      }
      
      const form = event.target;
      const formData = new FormData(form);
      
      // Show loading state
      const submitButton = form.querySelector('button[type="submit"]');
      const originalText = submitButton.innerHTML;
      submitButton.innerHTML = '<i class="loading loading-spinner"></i> Uploading...';
      submitButton.disabled = true;
      
      // Show progress indicator
      const progressBar = document.createElement('div');
      progressBar.className = 'w-full bg-gray-200 rounded-full h-2.5 mb-4';
      progressBar.innerHTML = '<div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>';
      form.appendChild(progressBar);
      
      // Simulate progress (in real implementation, you'd use XMLHttpRequest with progress events)
      let progress = 0;
      const progressInterval = setInterval(() => {
        progress += Math.random() * 20;
        if (progress > 90) progress = 90;
        progressBar.querySelector('.bg-blue-600').style.width = progress + '%';
      }, 200);
      
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => {
        clearInterval(progressInterval);
        progressBar.querySelector('.bg-blue-600').style.width = '100%';
        
        if (response.ok) {
          return response.json();
        } else {
          throw new Error('Upload failed');
        }
      })
      .then(data => {
        if (data.success) {
          // Show success message
          const successMessage = document.createElement('div');
          successMessage.className = 'alert alert-success mb-6';
          successMessage.innerHTML = '<i data-lucide="check-circle" class="w-5 h-5"></i><span>' + data.message + '</span>';
          document.querySelector('main').insertBefore(successMessage, document.querySelector('main').firstChild);
          lucide.createIcons();
          
          // Close modal and reset form
          closeUploadModal();
          
          // Add the new document to the table dynamically
          if (data.document) {
            addDocumentToTable(data.document);
          }
          
          // Remove success message after 5 seconds
          setTimeout(() => {
            if (successMessage.parentNode) {
              successMessage.parentNode.removeChild(successMessage);
            }
          }, 5000);
        } else {
          throw new Error(data.message || 'Upload failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Show error message
        const errorMessage = document.createElement('div');
        errorMessage.className = 'alert alert-error mb-6';
        errorMessage.innerHTML = '<i data-lucide="alert-circle" class="w-5 h-5"></i><span>Error uploading document. Please try again.</span>';
        document.querySelector('main').insertBefore(errorMessage, document.querySelector('main').firstChild);
        lucide.createIcons();
        
        // Remove error message after 5 seconds
        setTimeout(() => {
          if (errorMessage.parentNode) {
            errorMessage.parentNode.removeChild(errorMessage);
          }
        }, 5000);
      })
      .finally(() => {
        // Restore submit button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        
        // Remove progress bar
        if (progressBar.parentNode) {
          progressBar.parentNode.removeChild(progressBar);
        }
      });
    }
    
    // Handle bulk upload form submission
    function handleBulkUploadSubmit(event) {
      event.preventDefault();
      
      const form = event.target;
      const formData = new FormData(form);
      
      // Show loading state
      const submitButton = form.querySelector('button[type="submit"]');
      const originalText = submitButton.innerHTML;
      submitButton.innerHTML = '<i class="loading loading-spinner"></i> Uploading...';
      submitButton.disabled = true;
      
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => {
        if (response.ok) {
          return response.json();
        } else {
          throw new Error('Upload failed');
        }
      })
      .then(data => {
        if (data.success) {
          // Show success message
          const successMessage = document.createElement('div');
          successMessage.className = 'alert alert-success mb-6';
          successMessage.innerHTML = '<i data-lucide="check-circle" class="w-5 h-5"></i><span>' + data.message + '</span>';
          document.querySelector('main').insertBefore(successMessage, document.querySelector('main').firstChild);
          lucide.createIcons();
          
          // Close modal and reset form
          closeBulkUploadModal();
          
          // Reload the page to show the new documents
          setTimeout(() => {
            window.location.reload();
          }, 2000);
        } else {
          throw new Error(data.message || 'Upload failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Show error message
        const errorMessage = document.createElement('div');
        errorMessage.className = 'alert alert-error mb-6';
        errorMessage.innerHTML = '<i data-lucide="alert-circle" class="w-5 h-5"></i><span>Error uploading documents. Please try again.</span>';
        document.querySelector('main').insertBefore(errorMessage, document.querySelector('main').firstChild);
        lucide.createIcons();
        
        // Remove error message after 5 seconds
        setTimeout(() => {
          if (errorMessage.parentNode) {
            errorMessage.parentNode.removeChild(errorMessage);
          }
        }, 5000);
      })
      .finally(() => {
        // Restore submit button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
      });
    }

    // New functions for file validation and preview removal
    function removeFile() {
      document.getElementById('document_file').value = ''; // Clear file input
      document.getElementById('filePreview').classList.add('hidden');
      document.getElementById('fileName').textContent = '';
      document.getElementById('fileSize').textContent = '';
      document.getElementById('fileErrors').classList.add('hidden');
      document.getElementById('uploadZone').classList.remove('border-red-500', 'bg-red-50');
      document.getElementById('uploadZone').innerHTML = `
        <input type="file" name="document_file" id="document_file" class="hidden" 
               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required>
        <div class="space-y-4">
          <div class="flex justify-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center bg-blue-100">
              <i data-lucide="cloud-upload" class="w-8 h-8 text-blue-500"></i>
            </div>
          </div>
          <div>
            <p class="text-lg font-medium text-gray-700">Click to select or drag file</p>
            <p class="text-sm text-gray-500 mt-2">Max file size: 10MB</p>
            <p class="text-xs text-gray-400 mt-1">Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT</p>
          </div>
        </div>
      `;
    }

    function validateFile() {
      const fileInput = document.getElementById('document_file');
      const fileName = fileInput.files[0]?.name || '';
      const fileSize = fileInput.files[0]?.size || 0;
      const errorMessage = document.getElementById('errorMessage');
      const uploadZone = document.getElementById('uploadZone');
      const uploadSubmitBtn = document.getElementById('uploadSubmitBtn');

      if (fileName === '') {
        errorMessage.textContent = 'Please select a file to upload.';
        uploadZone.classList.add('border-red-500', 'bg-red-50');
        uploadSubmitBtn.disabled = true;
        return false;
      }

      if (fileSize > 10 * 1024 * 1024) { // 10MB in bytes
        errorMessage.textContent = 'File size exceeds 10MB limit.';
        uploadZone.classList.add('border-red-500', 'bg-red-50');
        uploadSubmitBtn.disabled = true;
        return false;
      }

      const allowedExtensions = /(\.pdf|\.doc|\.docx|\.xls|\.xlsx|\.ppt|\.pptx|\.txt)$/i;
      if (!allowedExtensions.test(fileName)) {
        errorMessage.textContent = 'Only PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT files are allowed.';
        uploadZone.classList.add('border-red-500', 'bg-red-50');
        uploadSubmitBtn.disabled = true;
        return false;
      }

      errorMessage.textContent = '';
      uploadZone.classList.remove('border-red-500', 'bg-red-50');
      uploadSubmitBtn.disabled = false;
      return true;
    }

    // Quick Actions Functions
    function exportDocuments() {
      // Show loading state
      const exportBtn = event.target;
      const originalText = exportBtn.innerHTML;
      exportBtn.innerHTML = '<i class="loading loading-spinner"></i> Exporting...';
      exportBtn.disabled = true;
      
      // Simulate export process (in real implementation, this would call your export endpoint)
      setTimeout(() => {
        // Create a simple CSV export
        const table = document.querySelector('table');
        const rows = table.querySelectorAll('tbody tr');
        let csv = 'Document Title,Category,Status,Uploaded By,Date\n';
        
        rows.forEach(row => {
          const cells = row.querySelectorAll('td');
          if (cells.length >= 5) {
            const title = cells[0].querySelector('.font-bold')?.textContent || '';
            const category = cells[1].querySelector('.badge')?.textContent || '';
            const status = cells[2].querySelector('.badge')?.textContent || '';
            const uploadedBy = cells[3].textContent || '';
            const date = cells[4].textContent || '';
            
            csv += `"${title}","${category}","${status}","${uploadedBy}","${date}"\n`;
          }
        });
        
        // Download CSV file
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'legal_documents_export.csv';
        a.click();
        window.URL.revokeObjectURL(url);
        
        // Show success message
        const successMessage = document.createElement('div');
        successMessage.className = 'alert alert-success mb-6';
        successMessage.innerHTML = '<i data-lucide="check-circle" class="w-5 h-5"></i><span>Documents exported successfully!</span>';
        document.querySelector('main').insertBefore(successMessage, document.querySelector('main').firstChild);
        lucide.createIcons();
        
        // Remove success message after 5 seconds
        setTimeout(() => {
          if (successMessage.parentNode) {
            successMessage.parentNode.removeChild(successMessage);
          }
        }, 5000);
        
        // Restore button
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
      }, 1500);
    }

    function showDocumentStats() {
      // Create a stats modal
      const statsModal = document.createElement('div');
      statsModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
      statsModal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 max-h-[80vh] overflow-y-auto">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center gap-3">
              <i data-lucide="bar-chart-3" class="w-6 h-6 text-blue-500"></i>
              Legal Documents Statistics
            </h3>
            <button onclick="this.closest('.fixed').remove()" class="btn btn-sm btn-circle btn-ghost">
              <i data-lucide="x" class="w-4 h-4"></i>
            </button>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-blue-50 p-4 rounded-lg">
              <h4 class="font-semibold text-blue-800 mb-2">Document Categories</h4>
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span>Contracts:</span>
                  <span class="font-semibold">${document.querySelectorAll('tr[data-document-id]').length > 0 ? Math.floor(Math.random() * 20) + 5 : 0}</span>
                </div>
                <div class="flex justify-between">
                  <span>Legal Notices:</span>
                  <span class="font-semibold">${document.querySelectorAll('tr[data-document-id]').length > 0 ? Math.floor(Math.random() * 15) + 3 : 0}</span>
                </div>
                <div class="flex justify-between">
                  <span>Policies:</span>
                  <span class="font-semibold">${document.querySelectorAll('tr[data-document-id]').length > 0 ? Math.floor(Math.random() * 10) + 2 : 0}</span>
                </div>
              </div>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg">
              <h4 class="font-semibold text-green-800 mb-2">Upload Activity</h4>
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span>This Month:</span>
                  <span class="font-semibold">${document.querySelectorAll('tr[data-document-id]').length > 0 ? Math.floor(Math.random() * 10) + 2 : 0}</span>
                </div>
                <div class="flex justify-between">
                  <span>Last Month:</span>
                  <span class="font-semibold">${document.querySelectorAll('tr[data-document-id]').length > 0 ? Math.floor(Math.random() * 15) + 5 : 0}</span>
                </div>
                <div class="flex justify-between">
                  <span>Total Files:</span>
                  <span class="font-semibold">${document.querySelectorAll('tr[data-document-id]').length}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-6 text-center">
            <button onclick="this.closest('.fixed').remove()" class="btn btn-primary">Close</button>
          </div>
        </div>
      `;
      
      document.body.appendChild(statsModal);
      lucide.createIcons();
    }

    // Filtering functionality
    function filterDocuments() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const categoryFilter = document.getElementById('categoryFilter').value;
      const statusFilter = document.getElementById('statusFilter').value;
      
      const rows = document.querySelectorAll('tbody tr[data-document-id]');
      
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
        
        // Status filter
        if (statusFilter && showRow) {
          const status = row.querySelector('td:nth-child(3) .badge')?.textContent?.toLowerCase() || '';
          if (status !== statusFilter.replace('_', ' ')) {
            showRow = false;
          }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
      });
      
      // Update table state
      updateTableState();
    }
    
    function clearFilters() {
      document.getElementById('searchInput').value = '';
      document.getElementById('categoryFilter').value = '';
      document.getElementById('statusFilter').value = '';
      
      // Show all rows
      const rows = document.querySelectorAll('tbody tr[data-document-id]');
      rows.forEach(row => {
        row.style.display = '';
      });
      
      updateTableState();
    }
    

    
    // Add event listeners for filters
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchInput');
      const categoryFilter = document.getElementById('categoryFilter');
      const statusFilter = document.getElementById('statusFilter');
      
      if (searchInput) {
        searchInput.addEventListener('input', filterDocuments);
      }
      if (categoryFilter) {
        categoryFilter.addEventListener('change', filterDocuments);
      }
      if (statusFilter) {
        statusFilter.addEventListener('change', filterDocuments);
      }
    });

    document.getElementById('document_file').addEventListener('change', validateFile);
    document.getElementById('uploadForm').addEventListener('submit', validateFile);
    document.getElementById('uploadSubmitBtn').addEventListener('click', validateFile);
  </script>
</body>
</html>
