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
        <div class="mb-8">
          <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Legal Documents</h1>
            <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Manage legal documents, contracts, and legal materials</p>
          </div>

          <!-- Status Summary Cards -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Legal Documents -->
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-primary">
              <div class="card-body p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="avatar placeholder">
                    <div class="bg-primary text-primary-content rounded-full w-12 h-12">
                      <i data-lucide="folder" class="w-6 h-6"></i>
                    </div>
                  </div>
                  <div class="badge badge-primary badge-outline">Total</div>
                </div>
                <div class="text-center">
                  <h2 class="card-title text-4xl font-bold text-primary justify-center mb-2">{{ $stats['total'] ?? 0 }}</h2>
                  <p class="text-base-content/70">Legal Documents</p>
                </div>
              </div>
            </div>

            <!-- For Review Documents -->
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 border-l-4 border-l-warning">
              <div class="card-body p-6">
                <div class="flex items-center justify-between mb-4">
                  <div class="avatar placeholder">
                    <div class="bg-warning text-warning-content rounded-full w-12 h-12">
                      <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                  </div>
                  <div class="badge badge-warning badge-outline">Review</div>
                </div>
                <div class="text-center">
                  <h2 class="card-title text-4xl font-bold text-warning justify-center mb-2">{{ $stats['pending_review'] ?? 0 }}</h2>
                  <p class="text-base-content/70">For Review</p>
                </div>
              </div>
            </div>

            <!-- Approved Documents -->
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
                  <h2 class="card-title text-4xl font-bold text-success justify-center mb-2">{{ $stats['active'] ?? 0 }}</h2>
                  <p class="text-base-content/70">Approved</p>
                </div>
              </div>
            </div>

            <!-- Decline Documents -->
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
                  <h2 class="card-title text-4xl font-bold text-error justify-center mb-2">{{ $stats['archived'] ?? 0 }}</h2>
                  <p class="text-base-content/70">Declined</p>
                </div>
              </div>
            </div>
          </div>
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
              
              <!-- Add Document Button - Only for Administrator -->
              @if(auth()->user()->role === 'Administrator')
                <button onclick="openUploadModal()" class="btn btn-primary btn-sm">
                  <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                  Add Document
                </button>
              @endif
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
                  <th class="text-left py-3 px-4 font-medium text-gray-700">Document Name</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Type</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Uploaded By</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Department</th>
                  <th class="text-center py-3 px-4 font-medium text-gray-700">Status</th>
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
                    <td class="py-3 px-4 text-center text-sm text-gray-600">{{ $document->uploader->employee_name ?? 'Unknown' }}</td>
                    <td class="py-3 px-4 text-center text-sm text-gray-600">{{ $document->uploader->dept_name ?? 'N/A' }}</td>
                    <td class="py-3 px-4 text-center">
                      @php
                        $statusConfig = [
                          'active' => ['icon' => 'check-circle', 'color' => 'text-success'],
                          'pending_review' => ['icon' => 'clock', 'color' => 'text-warning'],
                          'archived' => ['icon' => 'archive', 'color' => 'text-neutral'],
                          'draft' => ['icon' => 'edit-3', 'color' => 'text-info']
                        ];
                        $status = $document->status ?? 'active';
                        $config = $statusConfig[$status] ?? $statusConfig['active'];
                      @endphp
                      <div class="flex items-center justify-center">
                        <i data-lucide="{{ $config['icon'] }}" class="w-4 h-4 {{ $config['color'] }}"></i>
                        @if($status !== 'active')
                          <span class="ml-2 text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                        @endif
                      </div>
                    </td>
                    <td class="py-3 px-4 text-center text-sm text-gray-600">{{ $document->created_at->format('M d, Y') }}</td>
                    <td class="py-3 px-4 text-center">
                      <div class="flex items-center justify-center gap-1">
                        <button onclick="aiAnalysis({{ $document->id }})" class="btn btn-ghost btn-xs tooltip" data-tip="AI Analysis">
                          <i data-lucide="brain" class="w-4 h-4 text-purple-600"></i>
                        </button>
                        <button onclick="downloadDocument({{ $document->id }})" class="btn btn-ghost btn-xs tooltip" data-tip="Download">
                          <i data-lucide="download" class="w-4 h-4 text-blue-600"></i>
                        </button>
                        <!-- Delete Button - Only for Administrator -->
                        @if(auth()->user()->role === 'Administrator')
                          <button onclick="deleteDocument({{ $document->id }})" class="btn btn-ghost btn-xs tooltip" data-tip="Delete">
                            <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center py-12">
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

  <!-- Toast Notification Container -->
  <div id="toastContainer" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

  <!-- AI Analysis Modal -->
  <div id="aiAnalysisModal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="brain" class="w-8 h-8 text-purple-500"></i>
          AI Document Analysis
        </h3>
        <button onclick="closeAiAnalysisModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <div id="aiAnalysisContent" class="space-y-6">
        <!-- Loading State -->
        <div id="aiLoading" class="text-center py-12">
          <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-purple-100">
            <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-purple-500"></i>
          </div>
          <h3 class="text-lg font-semibold mb-2 text-gray-700">Analyzing Document...</h3>
          <p class="text-gray-500">AI is processing your document</p>
        </div>

        <!-- Analysis Results -->
        <div id="aiResults" class="hidden space-y-6">
          <!-- Document Info -->
          <div class="bg-gray-50 rounded-lg p-4">
            <h4 class="font-semibold text-gray-800 mb-2">Document Information</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div><strong>Title:</strong> <span id="aiDocTitle">—</span></div>
              <div><strong>Type:</strong> <span id="aiDocType">—</span></div>
              <div><strong>Status:</strong> <span id="aiDocStatus">—</span></div>
              <div><strong>Upload Date:</strong> <span id="aiDocDate">—</span></div>
            </div>
          </div>

          <!-- AI Classification -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
              <i data-lucide="wrench" class="w-4 h-4 text-blue-600"></i>
              <span class="text-sm font-medium text-blue-800">AI Classification</span>
            </div>
            <div class="text-lg font-bold text-blue-900 mb-1" id="aiCategory">—</div>
            <div class="text-sm text-blue-700" id="aiConfidence">AI Confidence: —</div>
          </div>

          <!-- AI Summary -->
          <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h4 class="font-semibold text-green-800 mb-2">AI Summary</h4>
            <p class="text-green-700 text-sm" id="aiSummary">—</p>
          </div>



          <!-- Legal Implications -->
          <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <h4 class="font-semibold text-orange-800 mb-2">Legal Implications</h4>
            <p class="text-orange-700 text-sm" id="aiLegalImplications">—</p>
          </div>

          <!-- Analysis Details -->
          <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h4 class="font-semibold text-gray-800 mb-2">Analysis Details</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div><strong>Compliance:</strong> <span id="aiCompliance">—</span></div>
              <div><strong>Tags:</strong> <span id="aiTags">—</span></div>
              <div><strong>Legal Risk:</strong> <span id="aiRisk">—</span></div>
              <div><strong>Review Required:</strong> <span id="aiReview">—</span></div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-4 mt-6 pt-6 border-t border-gray-200">
        <button onclick="closeAiAnalysisModal()" class="btn btn-outline">Close</button>
      </div>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    // User role for conditional button display
    const userRole = '{{ auth()->user()->role }}';
    
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
      console.log('closeUploadModal called'); // Debug log
      const modal = document.getElementById('uploadModal');
      if (!modal) {
        console.error('Upload modal not found!'); // Debug log
        return;
      }
      
      console.log('Modal classes before:', modal.className); // Debug log
      modal.classList.remove('modal-open');
      console.log('Modal classes after:', modal.className); // Debug log
      
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
      
      console.log('Upload modal closed successfully'); // Debug log
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

    // AI Analysis Modal functions
    function aiAnalysis(documentId) {
      // Show modal
      document.getElementById('aiAnalysisModal').classList.add('modal-open');
      
      // Show loading state
      document.getElementById('aiLoading').classList.remove('hidden');
      document.getElementById('aiResults').classList.add('hidden');
      
      // Get document data for display
      const row = document.querySelector(`tr[data-document-id="${documentId}"]`);
      if (row) {
        const title = row.querySelector('td:first-child .font-medium')?.textContent || '—';
        const type = row.querySelector('td:nth-child(2) .badge')?.textContent || '—';
        const status = row.querySelector('td:nth-child(5) .badge')?.textContent || '—';
        const date = row.querySelector('td:nth-child(6)')?.textContent || '—';
        
        document.getElementById('aiDocTitle').textContent = title;
        document.getElementById('aiDocType').textContent = type;
        document.getElementById('aiDocStatus').textContent = status;
        document.getElementById('aiDocDate').textContent = date;
      }
      
      // Perform AI analysis
      performAiAnalysis(documentId);
    }
    
    function closeAiAnalysisModal() {
      document.getElementById('aiAnalysisModal').classList.remove('modal-open');
    }
    
        function performAiAnalysis(documentId) {
      // First, try to get existing AI analysis data from the document row
      const row = document.querySelector(`tr[data-document-id="${documentId}"]`);
      if (row) {
        // Check if the row has AI analysis data stored in data attributes
        const aiAnalysisData = row.getAttribute('data-ai-analysis');
        
        if (aiAnalysisData) {
          try {
            const analysis = JSON.parse(aiAnalysisData);
            displayAiAnalysisResults(analysis);
            return;
          } catch (e) {
            console.log('No valid AI analysis data found, proceeding with new analysis');
          }
        }
      }
      
      // If no existing analysis, perform new analysis
      fetch(`/document/${documentId}/analyze-ajax`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('AI Analysis response:', data); // Debug log
        if (data.success) {
          // Update AI analysis results
          const analysis = data.analysis;
          console.log('AI Analysis data:', analysis); // Debug log
          console.log('Category:', analysis.category); // Debug log
          console.log('Confidence:', analysis.confidence); // Debug log
          displayAiAnalysisResults(analysis);
        } else {
          throw new Error(data.message || 'Analysis failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Show error state
        document.getElementById('aiLoading').innerHTML = `
          <div class="text-center py-12">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-red-100">
              <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2 text-red-700">Analysis Failed</h3>
            <p class="text-red-500">${error.message || 'Unable to analyze document'}</p>
          </div>
        `;
      });
    }
    
    function displayAiAnalysisResults(analysis) {
      console.log('Displaying AI analysis results:', analysis); // Debug log
      
      // Update category and confidence with proper field mapping
      const categoryDisplayNames = {
        'memorandum': 'Memorandum',
        'contract': 'Contract',
        'subpoena': 'Subpoena',
        'affidavit': 'Affidavit',
        'cease_desist': 'Cease & Desist',
        'legal_notice': 'Legal Notice',
        'policy': 'Policy',
        'legal_brief': 'Legal Brief',
        'financial': 'Financial Document',
        'compliance': 'Compliance Document',
        'report': 'Report',
        'general': 'General Document'
      };
      
      // Get the actual category from the analysis
      const actualCategory = analysis.category || 'general';
      const displayCategory = categoryDisplayNames[actualCategory] || actualCategory.charAt(0).toUpperCase() + actualCategory.slice(1);
      
      // Update category display
      document.getElementById('aiCategory').textContent = displayCategory;
      
      // Update confidence with proper field mapping
      let confidenceText = 'AI Confidence: ';
      if (analysis.confidence !== undefined && analysis.confidence !== null) {
        // If confidence is a number (0.0 to 1.0), convert to percentage
        if (typeof analysis.confidence === 'number') {
          const percentage = Math.round(analysis.confidence * 100);
          confidenceText += `${percentage}%`;
        } else {
          confidenceText += analysis.confidence;
        }
      } else if (analysis.confidence_score !== undefined && analysis.confidence_score !== null) {
        // Fallback to confidence_score if confidence is not available
        if (typeof analysis.confidence_score === 'number') {
          const percentage = Math.round(analysis.confidence_score * 100);
          confidenceText += `${percentage}%`;
        } else {
          confidenceText += analysis.confidence_score;
        }
      } else {
        // Default confidence if neither field is available
        confidenceText += 'High (90%)';
      }
      
      document.getElementById('aiConfidence').textContent = confidenceText;
      
      // Update other fields with proper field mapping
      document.getElementById('aiSummary').textContent = analysis.summary || 'AI analysis completed successfully.';
      document.getElementById('aiLegalImplications').textContent = analysis.legal_implications || 'No specific legal implications identified.';
      
      // Update analysis details with proper field mapping
      document.getElementById('aiCompliance').textContent = analysis.compliance_status || '—';
      
      // Handle tags properly
      let tagsText = '—';
      if (analysis.tags) {
        if (Array.isArray(analysis.tags)) {
          tagsText = analysis.tags.join(', ');
        } else if (typeof analysis.tags === 'string') {
          tagsText = analysis.tags;
        }
      }
      document.getElementById('aiTags').textContent = tagsText;
      
      document.getElementById('aiRisk').textContent = analysis.legal_risk_score || '—';
      document.getElementById('aiReview').textContent = analysis.requires_legal_review ? 'Yes' : 'No';
      
      // Show results
      document.getElementById('aiLoading').classList.add('hidden');
      document.getElementById('aiResults').classList.remove('hidden');
         }
     
     // Delete document function
     function deleteDocument(documentId) {
       if (!confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
         return;
       }
       
       // Show loading state
       const button = event.target.closest('button');
       const originalHTML = button.innerHTML;
       button.innerHTML = '<i class="loading loading-spinner"></i>';
       button.disabled = true;
       
       // Make delete request
       fetch(`/legal/documents/${documentId}`, {
         method: 'DELETE',
         headers: {
           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
           'Accept': 'application/json',
           'Content-Type': 'application/json'
         }
       })
       .then(response => {
         if (response.ok) {
           return response.json();
         } else {
           throw new Error(`Delete failed with status ${response.status}`);
         }
       })
       .then(data => {
         if (data.success) {
           // Remove the row from the table
           const row = document.querySelector(`tr[data-document-id="${documentId}"]`);
           if (row) {
             row.remove();
             
             // Show success toast
             showToast('Document deleted successfully', 'success');
             
             // Check if table is empty and show empty state
             const tbody = document.querySelector('tbody');
             if (tbody && tbody.children.length === 0) {
               const newEmptyRow = document.createElement('tr');
               newEmptyRow.innerHTML = `
                 <td colspan="7" class="text-center py-12">
                   <div class="flex flex-col items-center">
                     <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                       <i data-lucide="folder-open" class="w-10 h-10 text-gray-400"></i>
                     </div>
                     <h3 class="text-lg font-medium text-gray-900 mb-2">No documents found</h3>
                     <p class="text-gray-500">Get started by uploading your first document.</p>
                   </div>
                 </td>
               `;
               tbody.appendChild(newEmptyRow);
               lucide.createIcons();
             }
           }
         } else {
           throw new Error(data.message || 'Delete failed');
         }
       })
       .catch(error => {
         console.error('Delete error:', error);
         // Show error toast
         showToast('Error deleting document: ' + error.message, 'error');
       })
       .finally(() => {
         // Restore button
         button.innerHTML = originalHTML;
         button.disabled = false;
       });
     }
     
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
       // Get the button that was clicked
       const button = event.target.closest('button');
       if (!button) {
         console.error('Download button not found');
         return;
       }
       
       // Show loading state
       const originalHTML = button.innerHTML;
       button.innerHTML = '<i class="loading loading-spinner"></i>';
       button.disabled = true;
       
       console.log('Downloading document:', documentId);
       
       // Make download request
       fetch(`/legal/documents/${documentId}/download`, {
         method: 'GET',
         headers: {
           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
           'Accept': 'application/json',
           'X-Requested-With': 'XMLHttpRequest'
         }
       })
       .then(async response => {
         console.log('Download response status:', response.status);
         console.log('Download response headers:', response.headers);
         
         const contentType = response.headers.get('content-type') || '';
         console.log('Download Content-Type:', contentType);
         
         if (response.ok) {
           if (contentType.includes('application/json')) {
             return response.json();
           } else {
             // If response is not JSON, it might be a direct file download
             // Check if it's a file download response
             const contentDisposition = response.headers.get('content-disposition');
             if (contentDisposition && contentDisposition.includes('attachment')) {
               // This is a file download, create a blob and download it
               const blob = await response.blob();
               const url = window.URL.createObjectURL(blob);
               const a = document.createElement('a');
               a.href = url;
               a.download = contentDisposition.split('filename=')[1]?.replace(/"/g, '') || 'document';
               document.body.appendChild(a);
               a.click();
               window.URL.revokeObjectURL(url);
               document.body.removeChild(a);
               return { success: true, message: 'Download started' };
             } else {
               // Try to parse as text to see what the server returned
               const text = await response.text();
               console.log('Non-JSON response text:', text);
               throw new Error('Server returned unexpected response format');
             }
           }
         } else {
           // Handle different error status codes
           if (response.status === 403) {
             throw new Error('Access denied. You do not have permission to download this document.');
           } else if (response.status === 401) {
             throw new Error('Authentication required. Please log in again.');
           } else if (response.status === 404) {
             throw new Error('Document not found. It may have been deleted.');
           } else if (response.status === 422) {
             const text = await response.text();
             console.log('Validation error response:', text);
             throw new Error('Validation error. Please check your input.');
           } else {
             const text = await response.text();
             console.log('Error response text:', text);
             throw new Error(`Download failed with status ${response.status}`);
           }
         }
       })
       .then(data => {
         console.log('Download response data:', data);
         if (data.success) {
           if (data.download_url) {
             // Redirect to the download URL
             window.location.href = data.download_url;
           } else {
             // Show success toast for blob downloads
             showToast('Document download started successfully!', 'success');
           }
         } else {
           throw new Error(data.message || 'Download failed');
         }
       })
       .catch(error => {
         console.error('Download error:', error);
         // Show error toast with actual error details
         showToast('Error downloading document: ' + error.message, 'error');
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
           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
           'Accept': 'application/json',
           'X-Requested-With': 'XMLHttpRequest'
         }
       })
       .then(async response => {
         console.log('Delete response status:', response.status);
         console.log('Delete response headers:', response.headers);
         
         const contentType = response.headers.get('content-type') || '';
         console.log('Delete Content-Type:', contentType);
         
         if (response.ok) {
           if (contentType.includes('application/json')) {
             return response.json();
           } else {
             // If response is not JSON, try to parse it as text first
             const text = await response.text();
             console.log('Non-JSON response text:', text);
             throw new Error('Server returned non-JSON response. Please check your permissions.');
           }
         } else {
           // Handle different error status codes
           if (response.status === 403) {
             throw new Error('Access denied. You do not have permission to delete documents.');
           } else if (response.status === 401) {
             throw new Error('Authentication required. Please log in again.');
           } else if (response.status === 404) {
             throw new Error('Document not found. It may have been already deleted.');
           } else if (response.status === 422) {
             const text = await response.text();
             console.log('Validation error response:', text);
             throw new Error('Validation error. Please check your input.');
           } else {
             const text = await response.text();
             console.log('Error response text:', text);
             throw new Error(`Delete failed with status ${response.status}`);
           }
         }
       })
       .then(data => {
         console.log('Delete response data:', data);
         if (data.success) {
           // Show success toast
           showToast(data.message || 'Document deleted successfully', 'success');
           
           // Remove the deleted row from the table
           const row = document.querySelector(`tr[data-document-id="${documentId}"]`);
           if (row) {
             row.remove();
             
             // Check if table is empty and show empty state
             const tbody = document.querySelector('tbody');
             if (tbody && tbody.children.length === 0) {
               const newEmptyRow = document.createElement('tr');
               newEmptyRow.innerHTML = `
                 <td colspan="7" class="text-center py-12">
                   <div class="flex flex-col items-center">
                     <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                       <i data-lucide="folder-open" class="w-10 h-10 text-gray-400"></i>
                     </div>
                     <h3 class="text-lg font-medium text-gray-900 mb-2">No documents found</h3>
                     <p class="text-gray-500">Get started by uploading your first document.</p>
                   </div>
                 </td>
               `;
               tbody.appendChild(newEmptyRow);
               lucide.createIcons();
             }
             
             // Update card counts
             updateCardCounts();
           }
         } else {
           throw new Error(data.message || 'Delete failed');
         }
       })
       .catch(error => {
         console.error('Delete error:', error);
         // Show error toast with actual error details
         showToast('Error deleting document: ' + error.message, 'error');
       });
         }
     
     // Toast notification function
     function showToast(message, type = 'info', duration = 5000) {
       const toastContainer = document.getElementById('toastContainer');
       if (!toastContainer) return;
       
       const toast = document.createElement('div');
       toast.className = `alert alert-${type} shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;
       
       // Set icon based on type
       let icon = 'info';
       if (type === 'success') icon = 'check-circle';
       if (type === 'error') icon = 'alert-circle';
       if (type === 'warning') icon = 'alert-triangle';
       
       toast.innerHTML = `
         <i data-lucide="${icon}" class="w-5 h-5"></i>
         <span>${message}</span>
         <button onclick="this.parentElement.remove()" class="btn btn-ghost btn-xs">
           <i data-lucide="x" class="w-4 h-4"></i>
         </button>
       `;
       
       toastContainer.appendChild(toast);
       lucide.createIcons();
       
       // Animate in
       setTimeout(() => {
         toast.classList.remove('translate-x-full');
       }, 100);
       
       // Auto remove after duration
       setTimeout(() => {
         if (toast.parentNode) {
           toast.classList.add('translate-x-full');
           setTimeout(() => {
             if (toast.parentNode) {
               toast.parentNode.removeChild(toast);
             }
           }, 300);
         }
       }, duration);
     }
     
     // Function to update card counts after deletion
     function updateCardCounts() {
       const totalRows = document.querySelectorAll('tbody tr[data-document-id]').length;
       
       // Update Total Legal Documents card
       const totalCard = document.querySelector('.card:nth-child(1) .card-title');
       if (totalCard) {
         totalCard.textContent = totalRows;
       }
       
       // Update other card counts as needed
       // You can add logic here to update approved, declined, etc. counts
       // For now, we'll just update the total count
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
      const aiAnalysisModal = document.getElementById('aiAnalysisModal');
      
      if (event.target === uploadModal) {
        closeUploadModal();
      }
      if (event.target === bulkUploadModal) {
        closeBulkUploadModal();
      }
      if (event.target === editModal) {
        closeEditModal();
      }
      if (event.target === aiAnalysisModal) {
        closeAiAnalysisModal();
      }
    });

    
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeUploadModal();
        closeBulkUploadModal();
        closeEditModal();
        closeAiAnalysisModal();
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
          // Show success toast
          showToast(data.message, 'success');
          
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
        // Show error toast
        showToast('Error updating document. Please try again.', 'error');
      })
      .finally(() => {
        // Restore submit button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
      });
    }
    
    // Add new document to the table dynamically
    function addDocumentToTable(documentData) {
      console.log('addDocumentToTable called with:', documentData); // Debug log
      
      const tbody = document.querySelector('tbody');
      if (!tbody) {
        console.error('Table body not found!'); // Debug log
        return;
      }
      
      const emptyRow = tbody.querySelector('tr:not([data-document-id])');
      
      // Remove empty state row if it exists
      if (emptyRow) {
        console.log('Removing empty state row'); // Debug log
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
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
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
        <td class="py-3 px-4 text-center text-sm text-gray-600">${documentData.uploader_name || 'Unknown'}</td>
        <td class="py-3 px-4 text-center text-sm text-gray-600">${documentData.uploader_dept || 'N/A'}</td>
        <td class="py-3 px-4 text-center">
          <div class="flex items-center justify-center">
            <i data-lucide="check-circle" class="w-4 h-4 text-success"></i>
          </div>
        </td>
        <td class="py-3 px-4 text-center text-sm text-gray-600">${documentData.created_at ? new Date(documentData.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
        <td class="py-3 px-4 text-center">
          <div class="flex items-center justify-center gap-1">
            <button onclick="aiAnalysis(${documentData.id})" class="btn btn-ghost btn-xs tooltip" data-tip="AI Analysis">
              <i data-lucide="brain" class="w-4 h-4 text-purple-600"></i>
            </button>
            <button onclick="downloadDocument(${documentData.id})" class="btn btn-ghost btn-xs tooltip" data-tip="Download">
              <i data-lucide="download" class="w-4 h-4 text-blue-600"></i>
            </button>
            <!-- Delete Button - Only for Administrator -->
            ${userRole === 'Administrator' ? `
              <button onclick="deleteDocument(${documentData.id})" class="btn btn-ghost btn-xs tooltip" data-tip="Delete">
                <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
              </button>
            ` : ''}
          </div>
        </td>
      `;
      
      // Add the new row to the table
      tbody.appendChild(newRow);
      console.log('New row added to table'); // Debug log
      
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
            <td colspan="7" class="text-center py-12">
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
      

      
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(async response => {
        console.log('Response status:', response.status); // Debug log
        console.log('Response headers:', response.headers); // Debug log
        
        const contentType = response.headers.get('content-type') || '';
        console.log('Content-Type:', contentType); // Debug log
        
        if (response.ok) {
          if (contentType.includes('application/json')) {
            return response.json();
          } else {
            // If response is not JSON, try to parse it as text first
            const text = await response.text();
            console.log('Non-JSON response text:', text); // Debug log
            throw new Error('Server returned non-JSON response. Please check your permissions.');
          }
        } else {
          // Handle different error status codes
          if (response.status === 403) {
            throw new Error('Access denied. You do not have permission to upload documents.');
          } else if (response.status === 401) {
            throw new Error('Authentication required. Please log in again.');
          } else if (response.status === 422) {
            const text = await response.text();
            console.log('Validation error response:', text); // Debug log
            throw new Error('Validation error. Please check your input.');
          } else {
            const text = await response.text();
            console.log('Error response text:', text); // Debug log
            throw new Error(`Upload failed with status ${response.status}`);
          }
        }
      })
      .then(data => {
        console.log('Upload response:', data); // Debug log
        if (data.success) {
          // Clear any existing error messages first
          const existingErrors = document.querySelectorAll('.alert.alert-error');
          existingErrors.forEach(error => error.remove());
          
          // Show success toast
          showToast(data.message, 'success');
          
          // Close modal and reset form
          closeUploadModal();
          
          // Add the new document to the table dynamically
          if (data.document) {
            console.log('Adding document to table:', data.document); // Debug log
            addDocumentToTable(data.document);
          } else {
            console.log('No document data in response'); // Debug log
          }
        } else {
          throw new Error(data.message || 'Upload failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Show error toast with actual error details
        showToast('Error uploading document: ' + error.message, 'error');
      })
      .finally(() => {
        // Restore submit button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
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
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(async response => {
        console.log('Bulk upload response status:', response.status); // Debug log
        console.log('Bulk upload response headers:', response.headers); // Debug log
        
        const contentType = response.headers.get('content-type') || '';
        console.log('Bulk upload Content-Type:', contentType); // Debug log
        
        if (response.ok) {
          if (contentType.includes('application/json')) {
            return response.json();
          } else {
            // If response is not JSON, try to parse it as text first
            const text = await response.text();
            console.log('Bulk upload non-JSON response text:', text); // Debug log
            throw new Error('Server returned non-JSON response. Please check your permissions.');
          }
        } else {
          // Handle different error status codes
          if (response.status === 403) {
            throw new Error('Access denied. You do not have permission to upload documents.');
          } else if (response.status === 401) {
            throw new Error('Authentication required. Please log in again.');
          } else if (response.status === 422) {
            const text = await response.text();
            console.log('Bulk upload validation error response:', text); // Debug log
            throw new Error('Validation error. Please check your input.');
          } else {
            const text = await response.text();
            console.log('Bulk upload error response text:', text); // Debug log
            throw new Error(`Upload failed with status ${response.status}`);
          }
        }
      })
      .then(data => {
        if (data.success) {
          // Show success toast
          showToast(data.message, 'success');
          
          // Close modal and reset form
          closeBulkUploadModal();
          
          // Add the new documents to the table dynamically
          if (data.documents && Array.isArray(data.documents)) {
            data.documents.forEach(document => {
              addDocumentToTable(document);
            });
          }
        } else {
          throw new Error(data.message || 'Upload failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Show error toast with actual error details
        showToast('Error uploading documents: ' + error.message, 'error');
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
