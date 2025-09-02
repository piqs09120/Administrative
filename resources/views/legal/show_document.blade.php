<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $document->title }} - Legal Document - Soliera</title>
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
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">{{ $document->title }}</h1>
              <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Legal Document Details</p>
            </div>
            <div class="flex gap-2">
              <a href="{{ route('legal.legal_documents') }}" class="btn btn-outline">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Documents
              </a>
              <a href="{{ route('legal.documents.download', $document->id) }}" class="btn btn-primary">
                <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                Download
              </a>
            </div>
          </div>
        </div>

        <!-- Document Details -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Document Info -->
            <div class="lg:col-span-2">
              <h2 class="text-xl font-bold text-gray-800 mb-4">Document Information</h2>
              
              <div class="space-y-4">
                <div>
                  <label class="text-sm font-medium text-gray-600">Title</label>
                  <p class="text-lg font-medium">{{ $document->title }}</p>
                </div>
                
                <div>
                  <label class="text-sm font-medium text-gray-600">Description</label>
                  <p class="text-gray-700">{{ $document->description ?? 'No description provided' }}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="text-sm font-medium text-gray-600">Category</label>
                    <p class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $document->category ?? 'General')) }}</p>
                  </div>
                  
                  <div>
                    <label class="text-sm font-medium text-gray-600">Status</label>
                    <p class="text-gray-700">
                      @php
                        $statusConfig = [
                          'active' => ['class' => 'badge-success', 'icon' => 'check-circle'],
                          'pending_review' => ['class' => 'badge-warning', 'icon' => 'clock'],
                          'archived' => ['class' => 'badge-neutral', 'icon' => 'archive'],
                          'draft' => ['class' => 'badge-info', 'icon' => 'edit-3']
                        ];
                        $status = $document->status ?? 'active';
                        $config = $statusConfig[$status] ?? $statusConfig['active'];
                      @endphp
                      <span class="badge {{ $config['class'] }} gap-1">
                        <i data-lucide="{{ $config['icon'] }}" class="w-3 h-3"></i>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                      </span>
                    </p>
                  </div>
                  
                  <div>
                    <label class="text-sm font-medium text-gray-600">Uploaded By</label>
                    <p class="text-gray-700">{{ $document->uploader->name ?? 'Unknown' }}</p>
                  </div>
                  
                  <div>
                    <label class="text-sm font-medium text-gray-600">Upload Date</label>
                    <p class="text-gray-700">{{ $document->created_at->format('M d, Y g:i A') }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Document Preview -->
            <div>
              <h2 class="text-xl font-bold text-gray-800 mb-4">Document Preview</h2>
              
              <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-center h-64">
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
                
                <div class="text-center">
                  <i data-lucide="file-text" class="w-16 h-16 {{ $iconColor }} mb-4"></i>
                  <p class="text-lg font-medium text-gray-700">{{ $document->title }}</p>
                  <p class="text-gray-500">{{ strtoupper($fileExtension) }} File</p>
                </div>
              </div>
              
              <div class="mt-4">
                <a href="{{ route('legal.documents.download', $document->id) }}" class="btn btn-primary w-full">
                  <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                  Download Document
                </a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- AI Analysis (if available) -->
        @if($document->ai_analysis)
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4">AI Analysis</h2>
          
          <div class="space-y-4">
            <div>
              <label class="text-sm font-medium text-gray-600">Summary</label>
              <p class="text-gray-700">{{ $document->ai_analysis['summary'] ?? 'No summary available' }}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="text-sm font-medium text-gray-600">Legal Risk Score</label>
                <p class="text-gray-700">{{ $document->ai_analysis['legal_risk_score'] ?? 'N/A' }}</p>
              </div>
              
              <div>
                <label class="text-sm font-medium text-gray-600">Requires Legal Review</label>
                <p class="text-gray-700">{{ ($document->ai_analysis['requires_legal_review'] ?? false) ? 'Yes' : 'No' }}</p>
              </div>
              
              <div>
                <label class="text-sm font-medium text-gray-600">Requires Visitor Coordination</label>
                <p class="text-gray-700">{{ ($document->ai_analysis['requires_visitor_coordination'] ?? false) ? 'Yes' : 'No' }}</p>
              </div>
            </div>
            
            <div>
              <label class="text-sm font-medium text-gray-600">Key Information</label>
              <p class="text-gray-700">{{ $document->ai_analysis['key_info'] ?? 'No key information extracted' }}</p>
            </div>
          </div>
        </div>
        @endif
        
        <!-- Document Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Document Actions</h2>
          
          <div class="flex flex-wrap gap-2">
            <!-- Edit and Delete Buttons - Only for Administrator -->
            @if(auth()->user()->role === 'Administrator')
              <button onclick="editDocument({{ $document->id }})" class="btn btn-outline">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit Document
              </button>
              
              <button onclick="deleteDocument({{ $document->id }})" class="btn btn-error">
                <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                Delete Document
              </button>
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
    
    // Edit document function
    function editDocument(documentId) {
      // Redirect to edit page
      window.location.href = `/legal/documents/${documentId}/edit`;
    }
    
    // Delete document function
    function deleteDocument(documentId) {
      if (confirm('Are you sure you want to delete this legal document? This action cannot be undone.')) {
        fetch(`/legal/documents/${documentId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => {
          if (response.ok) {
            // Redirect to documents list
            window.location.href = '{{ route("legal.legal_documents") }}';
          } else {
            alert('Error deleting document');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while deleting the document');
        });
      }
    }
  </script>
</body>
</html>