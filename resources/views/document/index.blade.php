<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Document Management - Soliera</title>
  <link rel="icon" href="swt.jpg" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
          <h1 class="text-3xl font-bold text-gray-800 mb-2">Documents</h1>
          <p class="text-gray-600">Manage and organize your document library</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Documents -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-primary cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                  <div class="bg-primary text-primary-content rounded-full w-12 h-12">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-primary badge-outline group-hover:badge-primary transition-colors duration-300">Documents</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-primary justify-center mb-2 group-hover:text-primary-focus transition-colors duration-300">{{ $documents->count() }}</h2>
                <p class="text-base-content/70 group-hover:text-base-content transition-colors duration-300">Total Documents</p>
              </div>
            </div>
          </div>

          <!-- Received Today -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-success cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                  <div class="bg-success text-success-content rounded-full w-12 h-12">
                    <i data-lucide="calendar-plus" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-success badge-outline group-hover:badge-success transition-colors duration-300">Today</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-success justify-center mb-2 group-hover:text-success-focus transition-colors duration-300">{{ $documents->where('created_at', '>=', now()->startOfDay())->count() }}</h2>
                <p class="text-base-content/70 group-hover:text-base-content transition-colors duration-300">Received Today</p>
              </div>
            </div>
          </div>

          <!-- Released Documents -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-info cursor-pointer group">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                  <div class="bg-info text-info-content rounded-full w-12 h-12">
                    <i data-lucide="send" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-info badge-outline group-hover:badge-info transition-colors duration-300">Released</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-info justify-center mb-2 group-hover:text-info-focus transition-colors duration-300">{{ $documents->where('status', 'released')->count() }}</h2>
                <p class="text-base-content/70 group-hover:text-base-content transition-colors duration-300">Released Documents</p>
              </div>
            </div>
          </div>

          <!-- Archived Documents -->
          <div class="card bg-base-100 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 border-l-4 border-l-neutral cursor-pointer group" onclick="window.location.href='{{ route('document.archived') }}'" title="View Archived Documents" role="button">
            <div class="card-body p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="avatar placeholder group-hover:scale-110 transition-transform duration-300">
                  <div class="bg-neutral text-neutral-content rounded-full w-12 h-12">
                    <i data-lucide="archive" class="w-6 h-6"></i>
                  </div>
                </div>
                <div class="badge badge-neutral badge-outline group-hover:badge-neutral transition-colors duration-300">Archived</div>
              </div>
              <div class="text-center">
                <h2 class="card-title text-4xl font-bold text-neutral justify-center mb-2 group-hover:text-neutral-focus transition-colors duration-300">{{ $documents->where('status', 'archived')->count() }}</h2>
                <p class="text-base-content/70 group-hover:text-base-content transition-colors duration-300">Archived Documents</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Document Library -->
        <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200">
          <div class="p-6">
            <!-- Top Controls Section -->
            <div class="flex items-center justify-between mb-6">
              <!-- Left Side: Search and Filters -->
              <div class="flex items-center gap-4">
                <!-- Search Bar -->
                <div class="relative">
                  <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                  <input type="text" 
                         id="documentSearchInput"
                         placeholder="Search documents..." 
                         class="input input-bordered input-sm w-64 pl-10 pr-4 bg-gray-50 border-gray-200 focus:bg-white focus:border-blue-300">
                </div>
                
                <!-- Status Filter -->
                <div class="flex items-center gap-2">
                  <label class="text-sm font-medium text-gray-700">Filter by Status:</label>
                  <select id="statusFilter" class="select select-bordered select-sm w-32">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="pending_review">Pending Review</option>
                    <option value="archived">Archived</option>
                    <option value="draft">Draft</option>
                  </select>
                </div>
              </div>
              
              <!-- Right Side: Upload Button - Only for Administrator -->
              @if(auth()->user()->role === 'Administrator')
                <button onclick="openUploadModal()" class="btn btn-primary btn-lg shadow-lg hover:shadow-xl transition-all duration-300">
                  <i data-lucide="upload" class="w-5 h-5 mr-2"></i>
                  UPLOAD
                </button>
              @endif
            </div>

            <!-- Document Library Header -->
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                  <i data-lucide="folder-open" class="w-5 h-5 text-blue-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Document Library ({{ $documents->where('status', '!=', 'archived')->count() }})</h2>
              </div>

            </div>

            @if($documents->where('status', '!=', 'archived')->count() > 0)
              <!-- Documents Table -->
              <div class="overflow-x-auto">
                <table class="table w-full">
                  <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                      <th class="text-left py-4 px-6 font-semibold text-gray-700">Document</th>
                      <th class="text-left py-4 px-6 font-semibold text-gray-700">Details</th>
                      <th class="text-center py-4 px-6 font-semibold text-gray-700">Status</th>
                      <th class="text-center py-4 px-6 font-semibold text-gray-700">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($documents->where('status', '!=', 'archived') as $document)
                      <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100" data-document-id="{{ $document->id }}">
                        <!-- Document Column -->
                        <td class="py-4 px-6">
                          <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
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
                              <i data-lucide="file-text" class="w-6 h-6 {{ $iconColor }}"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                              <div class="font-medium text-gray-900 truncate">{{ $document->title }}</div>
                              <div class="text-sm text-gray-500">
                                @if($document->file_path)
                                  @php
                                    $fileSize = 0;
                                    if (Storage::exists($document->file_path)) {
                                      $fileSize = Storage::size($document->file_path);
                                    }
                                    $sizeFormatted = $fileSize > 0 ? number_format($fileSize / 1024, 1) . ' KB' : 'Unknown size';
                                  @endphp
                                  {{ strtoupper($fileExtension) }} • {{ $sizeFormatted }}
                                @else
                                  No file attached
                                @endif
                              </div>
                            </div>
                          </div>
                        </td>
                        
                        <!-- Details Column -->
                        <td class="py-4 px-6">
                          <div class="text-sm">
                            <div class="font-medium text-gray-900">{{ $document->uploader_name }}</div>
                            <div class="text-gray-500">{{ $document->created_at->format('M d, Y g:i A') }}</div>
                          </div>
                        </td>
                        
                        <!-- Status Column -->
                        <td class="py-4 px-6 text-center">
                          @php
                            $statusConfig = [
                              'active' => ['icon' => 'check-circle', 'color' => 'text-success'],
                              'pending_review' => ['icon' => 'clock', 'color' => 'text-warning'],
                              'draft' => ['icon' => 'edit-3', 'color' => 'text-info'],
                              'legal' => ['icon' => 'gavel', 'color' => 'text-primary'],
                              'visitor' => ['icon' => 'users', 'color' => 'text-secondary'],
                              'high_risk' => ['icon' => 'alert-triangle', 'color' => 'text-error'],
                              'general' => ['icon' => 'file-text', 'color' => 'text-neutral']
                            ];
                            $status = $document->status ?? 'active';
                            $config = $statusConfig[$status] ?? $statusConfig['active'];
                          @endphp
                          <div class="flex justify-center">
                            <i data-lucide="{{ $config['icon'] }}" class="w-5 h-5 {{ $config['color'] }}"></i>
                          </div>
                        </td>
                        
                        <!-- Actions Column -->
                        <td class="py-4 px-6 text-center">
                          <div class="relative" x-data="{ open: false }">
                            <!-- 3-Dot Menu Button -->
                            <button @click="open = !open" 
                                    @click.away="open = false"
                                    class="btn btn-ghost btn-sm btn-circle hover:bg-gray-200 transition-colors"
                                    aria-label="Document actions menu">
                              <i data-lucide="more-horizontal" class="w-5 h-5 text-gray-600"></i>
                            </button>
                            
                            <!-- Dropdown Menu (opens upward to avoid being cut off) -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 bottom-full mb-2 w-48 max-h-64 overflow-auto bg-white rounded-lg shadow-lg border border-gray-200 z-[100]"
                                 style="display: none;">
                              
                             
                              
                              <!-- Archive Option - Only for Administrator -->
                              @if(auth()->user()->role === 'Administrator')
                                <button onclick="archiveDocument({{ $document->id }})" 
                                        class="w-full flex items-center gap-3 px-4 py-3 text-left text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition-colors">
                                  <i data-lucide="archive" class="w-4 h-4 text-amber-600"></i>
                                  <span>Archive</span>
                                </button>
                              @endif
                              
                              <!-- Divider -->
                              <div class="border-t border-gray-200"></div>
                              
                              <!-- Delete Option - Only for Administrator -->
                              @if(auth()->user()->role === 'Administrator')
                                <button onclick="deleteDocument({{ $document->id }})" 
                                        class="w-full flex items-center gap-3 px-4 py-3 text-left text-sm text-red-600 hover:bg-red-50 transition-colors rounded-b-lg">
                                  <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                                  <span>Delete</span>
                                </button>
                              @endif
                            </div>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <!-- Empty State - Centered and Minimal -->
              <div class="text-center py-16">
                <!-- Large Icon -->
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                  <i data-lucide="folder" class="w-10 h-10 text-gray-400"></i>
                </div>
                
                <!-- Empty State Text -->
                <h3 class="text-xl font-semibold text-gray-700 mb-3">No Active Documents Found</h3>
                <p class="text-gray-500 mb-8">Upload your first document to get started.</p>
                
                <!-- Primary Call-to-Action - Only for Administrator -->
                @if(auth()->user()->role === 'Administrator')
                  <button onclick="openUploadModal()" class="btn btn-primary btn-lg shadow-lg hover:shadow-xl transition-all duration-300">
                    <i data-lucide="upload" class="w-5 h-5 mr-2"></i>
                    UPLOAD DOCUMENT
                  </button>
                @endif
              </div>
            @endif
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Document View Modal -->
  <div id="documentViewModal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center">
          <i data-lucide="file-text" class="w-6 h-6 text-blue-500 mr-3"></i>
          <span id="modalDocumentTitle">Document Details</span>
        </h3>
        <button onclick="closeDocumentModal()" class="btn btn-ghost btn-sm">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <div id="modalDocumentContent" class="space-y-6">
        <!-- Loading state -->
        <div class="text-center py-8">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
          <p class="text-gray-600">Loading document details...</p>
        </div>
      </div>

      <div class="modal-action">
        <button onclick="closeDocumentModal()" class="btn btn-outline">Close</button>
        <button onclick="downloadModalDocument()" class="btn btn-primary">
          <i data-lucide="download" class="w-4 h-4 mr-2"></i>
          Download
        </button>
      </div>
    </div>
  </div>

  <!-- Upload Document Modal -->
  <div id="uploadModal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="upload" class="w-8 h-8 text-blue-500"></i>
          Upload New Document
        </h3>
        <button onclick="closeUploadModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

              <form id="uploadForm" action="{{ route('document.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="source" value="document_management">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          
          <!-- Left Column: Form Fields -->
          <div class="space-y-6">
            <!-- Title Field -->
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Title *</span>
              </label>
              <input type="text" name="title" id="uploadTitle" class="input input-bordered w-full" 
                     placeholder="Enter document title" required>
              <div class="label">
                <span class="label-text-alt">Enter a descriptive title for the document</span>
              </div>
            </div>

            <!-- Department Field -->
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Department</span>
              </label>
              <select name="department" id="uploadDepartment" class="select select-bordered w-full">
                <option value="Management">Management</option>
                <option value="Legal">Legal</option>
                <option value="HR">HR</option>
                <option value="Finance">Finance</option>
                <option value="IT">IT</option>
                <option value="Operations">Operations</option>
              </select>
              <div class="label">
                <span class="label-text-alt">Select the department this document belongs to</span>
              </div>
            </div>

            
          </div>

          <!-- Right Column: File Upload Area -->
          <div class="space-y-6">
            <div>
              <h2 class="text-xl font-bold text-gray-800">Document File</h2>
              <p class="text-gray-600">PDF, Word, Excel, PPT, Text files (Max: 10MB)</p>
            </div>

            <!-- File Upload Zone -->
            <div id="uploadZone" class="border-2 border-dashed border-blue-300 rounded-xl p-8 text-center hover:border-blue-400 transition-colors cursor-pointer" 
                 onclick="triggerFileSelection()" 
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

            

            <!-- Submit Button -->
            <div class="pt-4">
              <button type="submit" id="uploadSubmitBtn" class="btn btn-primary btn-lg w-full">
                <i data-lucide="upload" class="w-5 h-5 mr-2"></i>
                UPLOAD DOCUMENT
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Archive Confirmation Modal -->
  <div id="archiveModal" class="modal">
    <div class="modal-box w-11/12 max-w-md">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
          <i data-lucide="archive" class="w-6 h-6 text-orange-600"></i>
        </div>
        <div>
          <h3 class="text-xl font-bold text-gray-800">Archive Document</h3>
          <p class="text-sm text-gray-600">Move to archived documents</p>
        </div>
      </div>
      
      <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
          <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-600 mt-0.5"></i>
          <div>
            <p class="font-medium text-orange-800 mb-1">Document will be archived</p>
            <p class="text-sm text-orange-700">This document will be moved to the archived documents section. You can restore it anytime from there.</p>
          </div>
        </div>
      </div>
      
      <div class="flex justify-end gap-3">
        <button onclick="closeArchiveModal()" class="btn btn-ghost">
          Cancel
        </button>
        <button onclick="confirmArchive()" class="btn btn-warning">
          <i data-lucide="archive" class="w-4 h-4 mr-2"></i>
          Archive Document
        </button>
      </div>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <style>
    /* Performance-optimized CSS - No lag, smooth scrolling */
    
    /* Hardware acceleration for smooth performance */
    * {
      -webkit-transform: translateZ(0);
      transform: translateZ(0);
      -webkit-backface-visibility: hidden;
      backface-visibility: hidden;
    }
    
    /* Optimized scrolling */
    .overflow-y-auto {
      -webkit-overflow-scrolling: touch;
      scroll-behavior: smooth;
    }
    
    /* Remove heavy animations that cause lag */
    .hover\:scale-105,
    .hover\:scale-110 {
      transition: none !important;
      transform: none !important;
    }
    
    .hover\:shadow-xl,
    .hover\:shadow-2xl {
      transition: none !important;
      box-shadow: none !important;
    }
    
    /* Lightweight hover effects */
    .hover\:bg-gray-50:hover {
      background-color: #f9fafb;
    }
    
    .hover\:bg-blue-200:hover {
      background-color: #bfdbfe;
    }
    
    .hover\:bg-orange-200:hover {
      background-color: #fed7aa;
    }
    
    .hover\:bg-red-200:hover {
      background-color: #fecaca;
    }
    
    /* Optimized button interactions */
    .btn {
      transition: background-color 0.1s ease, border-color 0.1s ease;
      will-change: background-color, border-color;
    }
    
    .btn:hover {
      transform: none;
      box-shadow: none;
    }
    
    /* Optimized table performance */
    tbody tr {
      transition: background-color 0.1s ease;
      will-change: background-color;
    }
    
    tbody tr:hover {
      background-color: #f9fafb;
      transform: none;
      box-shadow: none;
    }
    
    /* Remove alternating row backgrounds for better performance */
    tbody tr:nth-child(even) {
      background-color: transparent;
    }
    
    /* Optimized modal performance */
    .modal {
      will-change: opacity;
      transition: opacity 0.15s ease;
    }
    
    .modal-box {
      will-change: transform;
      transition: transform 0.15s ease;
    }
    
    /* Optimized search and filter inputs */
    input, select, textarea {
      transition: border-color 0.1s ease, box-shadow 0.1s ease;
      will-change: border-color, box-shadow;
    }
    
    /* Remove heavy transitions */
    .transition-all,
    .transition-colors,
    .transition-transform,
    .transition-shadow {
      transition: none !important;
    }
    
    /* Optimized dropdown animations */
    .dropdown-content {
      transition: opacity 0.1s ease;
      will-change: opacity;
    }
    
    /* Responsive optimizations */
    @media (max-width: 1024px) {
      .w-\[30\%\], .w-\[35\%\], .w-\[15\%\], .w-\[20\%\] {
        width: 100%;
      }
      
      tbody tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1rem;
      }
      
      tbody td {
        display: block;
        padding: 0.5rem 0;
        border: none;
      }
      
      thead {
        display: none;
      }
      
      .inline-flex.items-center.px-3.py-2.h-8 {
        width: 100%;
        justify-content: center;
        margin-bottom: 0.5rem;
      }
    }

    @media (max-width: 640px) {
      .px-4.py-4 {
        padding: 0.75rem 0.5rem;
      }
      
      .text-sm {
        font-size: 0.875rem;
      }
      
      .text-xs {
        font-size: 0.75rem;
      }
    }

    /* Search highlighting - lightweight */
    .search-highlight {
      background-color: #fef3c7;
      padding: 0.125rem 0.25rem;
      border-radius: 0.25rem;
      font-weight: 600;
    }

    /* Focus indicators for accessibility */
    tbody tr:focus-within {
      outline: 2px solid #3b82f6;
      outline-offset: 2px;
    }

    .inline-flex.items-center.px-3.py-2.h-8:focus {
      outline: 2px solid #3b82f6;
      outline-offset: 2px;
    }

    /* Upload Button Styling */
    .btn-primary.btn-lg {
      background: #007bff !important;
      color: white !important;
      padding: 10px 20px !important;
      border: none !important;
      border-radius: 6px !important;
      font-weight: 500 !important;
      font-size: 14px !important;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
      transition: background-color 0.1s ease !important;
      height: 40px !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
    }

    .btn-primary.btn-lg:hover {
      background: #0056b3 !important;
      transform: none !important;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    }

    .btn-primary.btn-lg:active {
      background: #004085 !important;
      transform: none !important;
      box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;
    }

    .btn-primary.btn-lg:focus {
      outline: 2px solid #80bdff !important;
      outline-offset: 2px !important;
    }

    /* Document Header Layout */
    .document-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
    }

    /* Responsive adjustments for upload button */
    @media (max-width: 1024px) {
      .btn-primary.btn-lg {
        padding: 8px 16px !important;
        font-size: 13px !important;
      }
    }

    @media (max-width: 768px) {
      .btn-primary.btn-lg {
        padding: 6px 12px !important;
        font-size: 12px !important;
        height: 36px !important;
      }
      
      .btn-primary.btn-lg .w-6.h-6 {
        width: 1rem !important;
        height: 1rem !important;
      }
    }

    /* Document View Modal Styling */
    #documentViewModal .modal-box {
      max-height: 90vh;
      overflow-y: auto;
    }

    #documentViewModal .prose {
      color: inherit;
    }

    #documentViewModal .prose h1,
    #documentViewModal .prose h2,
    #documentViewModal .prose h3,
    #documentViewModal .prose h4,
    #documentViewModal .prose h5,
    #documentViewModal .prose h6 {
      color: #1f2937;
      margin-top: 1.5em;
      margin-bottom: 0.5em;
    }

    #documentViewModal .prose p {
      margin-bottom: 1em;
      line-height: 1.6;
    }

    #documentViewModal .prose ul,
    #documentViewModal .prose ol {
      margin-bottom: 1em;
      padding-left: 1.5em;
    }

    #documentViewModal .prose li {
      margin-bottom: 0.5em;
    }

    #documentViewModal .prose table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 1em;
    }

    #documentViewModal .prose th,
    #documentViewModal .prose td {
      border: 1px solid #e5e7eb;
      padding: 0.5rem;
      text-align: left;
    }

    #documentViewModal .prose th {
      background-color: #f9fafb;
      font-weight: 600;
    }

    /* Responsive modal adjustments */
    @media (max-width: 768px) {
      #documentViewModal .modal-box {
        width: 95%;
        margin: 1rem;
        max-height: 95vh;
      }
      
      #documentViewModal .prose {
        font-size: 0.875rem;
      }
    }

    /* Print styles */
    @media print {
      .btn, .dropdown, #tableLoading {
        display: none !important;
      }
      
      tbody tr {
        break-inside: avoid;
        page-break-inside: avoid;
      }
      
      thead {
        position: static;
      }
      
      .bg-gray-50, .bg-white {
        background-color: white !important;
      }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
      .border-gray-200 {
        border-color: #000 !important;
      }
      
      .text-gray-500 {
        color: #000 !important;
      }
      
      .bg-gray-50 {
        background-color: #f0f0f0 !important;
      }
      
      .hover\:bg-gray-50:hover {
        background-color: #e0e0e0 !important;
      }
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
      * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
      }
      
      .animate-spin {
        animation: none !important;
      }
    }
  </style>
  
  <script>
    // Optimized JavaScript - No lag, instant response
    
    // Simple date/time update
    function updateDateTime() {
      const now = new Date();
      const dateElement = document.getElementById('currentDate');
      const timeElement = document.getElementById('currentTime');
      
      if (dateElement) dateElement.textContent = now.toLocaleDateString('en-US', { 
        weekday: 'short', 
        month: 'short', 
        day: 'numeric' 
      });
      if (timeElement) timeElement.textContent = now.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit', 
        hour12: true 
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
      document.getElementById('uploadZone').classList.remove('border-blue-500', 'bg-blue-50');
    }

    function closeDocumentModal() {
      const modal = document.getElementById('documentViewModal');
      modal.classList.remove('modal-open');
    }

    // File handling functions
    function triggerFileSelection() {
      document.getElementById('document_file').click();
    }

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

    function removeFile() {
      document.getElementById('document_file').value = '';
      document.getElementById('filePreview').classList.add('hidden');

    }

    function formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    

    // Document action functions
    function viewDocument(documentId) {
      // Load document details and show modal
      fetch(`/document/${documentId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('modalDocumentTitle').textContent = data.document.title;
            document.getElementById('modalDocumentContent').innerHTML = `
              <div class="space-y-4">
                <div><strong>Title:</strong> ${data.document.title}</div>
                <div><strong>Category:</strong> ${data.document.category || 'N/A'}</div>
                <div><strong>Status:</strong> ${data.document.status || 'N/A'}</div>
                <div><strong>Uploaded:</strong> ${data.document.created_at}</div>
              </div>
            `;
            document.getElementById('documentViewModal').classList.add('modal-open');
          }
        })
        .catch(error => {
          console.error('Error loading document:', error);
          showToast('Error loading document details', 'error');
        });
    }

    function downloadDocument(documentId) {
      window.open(`/document/${documentId}/download`, '_blank');
    }

    // Archive modal state
    let documentToArchive = null;

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
          showToast('Document archived successfully', 'success');
          // Redirect to Archived Documents page after success
          setTimeout(() => { window.location.href = '{{ route('document.archived') }}'; }, 700);
        } else {
          showToast('Failed to archive document', 'error');
        }
      })
      .catch(error => {
        console.error('Error archiving document:', error);
        showToast('Error archiving document', 'error');
      })
      .finally(() => {
        closeArchiveModal();
      });
    }

    function deleteDocument(documentId) {
      if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        fetch(`/document/${documentId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showToast('Document deleted successfully', 'success');
            setTimeout(() => location.reload(), 1000);
          } else {
            showToast('Failed to delete document', 'error');
          }
        })
        .catch(error => {
          console.error('Error deleting document:', error);
          showToast('Error deleting document', 'error');
        });
      }
    }

    // Filter documents function
    function filterDocuments() {
      const searchTerm = document.getElementById('documentSearchInput').value.toLowerCase();
      const statusFilter = document.getElementById('statusFilter').value;
      
      const rows = document.querySelectorAll('tbody tr[data-document-id]');
      
      rows.forEach(row => {
        let showRow = true;
        
        // Search filter
        if (searchTerm) {
          const title = row.querySelector('td:first-child .font-medium')?.textContent?.toLowerCase() || '';
          if (!title.includes(searchTerm)) {
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
    }

    // Form submission handler
    function handleUploadSubmit(event) {
      event.preventDefault();
      
      console.log('Form submission started');
      

      
      // Check if file is selected
      const fileInput = document.getElementById('document_file');
      if (!fileInput.files || fileInput.files.length === 0) {
        showToast('Please select a file to upload', 'error');
        return;
      }
      
      const form = event.target;
      const formData = new FormData(form);
      
      // Show loading state
      const submitButton = form.querySelector('button[type="submit"]');
      const originalText = submitButton.innerHTML;
      submitButton.innerHTML = '<i class="loading loading-spinner"></i> Uploading...';
      submitButton.disabled = true;
      
      console.log('Submitting form to:', form.action);
              console.log('Form data:', {
          title: formData.get('title'),
          source: formData.get('source'),
          hasFile: formData.has('document_file')
        });
      
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        console.log('Upload response received:', response.status, response.statusText);
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('Upload response data:', data);
        
        if (data.success) {
          showToast('Document uploaded successfully!', 'success');
          
          // Close modal and reset form
          closeUploadModal();
          
          // Reload the page to show the new document
          setTimeout(() => {
            location.reload();
          }, 1000);
        } else {
          throw new Error(data.message || 'Upload failed');
        }
      })
      .catch(error => {
        console.error('Upload error:', error);
        showToast('Error uploading document: ' + error.message, 'error');
        
        // Show detailed error in the form
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-error mt-4';
        errorDiv.innerHTML = `
          <i data-lucide="alert-circle" class="w-5 h-5"></i>
          <span>Upload failed: ${error.message}</span>
        `;
        
        const form = document.getElementById('uploadForm');
        form.insertBefore(errorDiv, form.firstChild);
        
        // Remove error after 5 seconds
        setTimeout(() => {
          if (errorDiv.parentNode) {
            errorDiv.parentNode.removeChild(errorDiv);
          }
        }, 5000);
      })
      .finally(() => {
        // Restore submit button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
      });
    }

    // File input change handler
    document.addEventListener('DOMContentLoaded', function() {
      const fileInput = document.getElementById('document_file');
      if (fileInput) {
        fileInput.addEventListener('change', function(e) {
          console.log('File input change event triggered:', e.target.files);
          if (e.target.files.length > 0) {
            const file = e.target.files[0];
            console.log('File selected:', file);
            
                          // Update preview
              updateFilePreview(file);
          }
        });
      }

      // Form submission handler
      const uploadForm = document.getElementById('uploadForm');
      if (uploadForm) {
        uploadForm.addEventListener('submit', handleUploadSubmit);
      }

      // Search and filter event listeners
      const searchInput = document.getElementById('documentSearchInput');
      const statusFilter = document.getElementById('statusFilter');
      
      if (searchInput) {
        searchInput.addEventListener('input', filterDocuments);
      }
      if (statusFilter) {
        statusFilter.addEventListener('change', filterDocuments);
      }
      
      // Modal event listeners
      // Close archive modal when clicking outside
      document.addEventListener('click', function(event) {
        const archiveModal = document.getElementById('archiveModal');
        if (event.target === archiveModal) {
          closeArchiveModal();
        }
      });
      
      // Close archive modal with Escape key
      document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
          closeArchiveModal();
        }
      });
      
      console.log('Document management page initialized');
    });
  </script>
</body>
</html> 
