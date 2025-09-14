<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edit Legal Document - Soliera</title>
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
          <div class="flex items-center gap-4">
            <button onclick="window.history.back()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </button>
            <div>
              <h1 class="text-3xl font-bold text-gray-800">Edit Legal Document</h1>
              <p class="text-gray-600">Update document information</p>
            </div>
          </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white rounded-xl shadow-lg p-6 max-w-4xl mx-auto">
          <form action="{{ route('legal.documents.update', $document->id) }}" method="POST" id="editDocumentForm">
            @csrf
            @method('PUT')

            <div class="space-y-6">
              <!-- Document Title -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold text-gray-700">Document Title *</span>
                </label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title', $document->title) }}" 
                       class="input input-bordered w-full @error('title') input-error @enderror" 
                       placeholder="Enter document title"
                       required>
                @error('title')
                  <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                  </label>
                @enderror
              </div>

              <!-- Category -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold text-gray-700">Category</span>
                </label>
                <select name="category" class="select select-bordered w-full @error('category') select-error @enderror">
                  <option value="contract" {{ old('category', $document->category) == 'contract' ? 'selected' : '' }}>Contract</option>
                  <option value="legal_notice" {{ old('category', $document->category) == 'legal_notice' ? 'selected' : '' }}>Legal Notice</option>
                  <option value="policy" {{ old('category', $document->category) == 'policy' ? 'selected' : '' }}>Policy</option>
                  <option value="compliance" {{ old('category', $document->category) == 'compliance' ? 'selected' : '' }}>Compliance</option>
                  <option value="financial" {{ old('category', $document->category) == 'financial' ? 'selected' : '' }}>Financial</option>
                  <option value="report" {{ old('category', $document->category) == 'report' ? 'selected' : '' }}>Report</option>
                  <option value="memorandum" {{ old('category', $document->category) == 'memorandum' ? 'selected' : '' }}>Memorandum</option>
                  <option value="affidavit" {{ old('category', $document->category) == 'affidavit' ? 'selected' : '' }}>Affidavit</option>
                  <option value="subpoena" {{ old('category', $document->category) == 'subpoena' ? 'selected' : '' }}>Subpoena</option>
                  <option value="cease_desist" {{ old('category', $document->category) == 'cease_desist' ? 'selected' : '' }}>Cease & Desist</option>
                  <option value="legal_brief" {{ old('category', $document->category) == 'legal_brief' ? 'selected' : '' }}>Legal Brief</option>
                </select>
                @error('category')
                  <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                  </label>
                @enderror
              </div>

              <!-- Description -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold text-gray-700">Description</span>
                </label>
                <textarea name="description" 
                          class="textarea textarea-bordered w-full h-32 @error('description') textarea-error @enderror" 
                          placeholder="Enter document description">{{ old('description', $document->description) }}</textarea>
                @error('description')
                  <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                  </label>
                @enderror
              </div>

              <!-- Status (Read-only display) -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold text-gray-700">Current Status</span>
                </label>
                <div class="p-3 bg-gray-50 rounded-lg">
                  @php
                    $statusConfig = [
                      'active' => ['class' => 'badge-success', 'text' => 'Active'],
                      'pending_review' => ['class' => 'badge-warning', 'text' => 'Pending Review'],
                      'archived' => ['class' => 'badge-neutral', 'text' => 'Archived'],
                      'draft' => ['class' => 'badge-info', 'text' => 'Draft'],
                      'approved' => ['class' => 'badge-success', 'text' => 'Approved'],
                      'declined' => ['class' => 'badge-error', 'text' => 'Declined']
                    ];
                    $status = $document->status ?? 'active';
                    $config = $statusConfig[$status] ?? $statusConfig['active'];
                  @endphp
                  <span class="badge {{ $config['class'] }}">{{ $config['text'] }}</span>
                </div>
              </div>

              <!-- File Information (Read-only) -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold text-gray-700">File Information</span>
                </label>
                <div class="p-3 bg-gray-50 rounded-lg">
                  <div class="flex items-center gap-3">
                    @php
                      $fileExtension = pathinfo($document->file_path ?? '', PATHINFO_EXTENSION);
                      $iconColor = match(strtolower($fileExtension)) {
                        'pdf' => 'text-red-600',
                        'doc', 'docx' => 'text-blue-600',
                        'xls', 'xlsx' => 'text-green-600',
                        'ppt', 'pptx' => 'text-orange-600',
                        default => 'text-gray-600'
                      };
                    @endphp
                    <i data-lucide="file-text" class="w-5 h-5 {{ $iconColor }}"></i>
                    <div>
                      <p class="font-medium">{{ basename($document->file_path ?? 'N/A') }}</p>
                      <p class="text-sm text-gray-500">Uploaded: {{ $document->created_at->format('M d, Y H:i') }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <button type="button" onclick="window.history.back()" class="btn btn-outline">
                  <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary" id="updateButton">
                  <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                  Update Document
                </button>
              </div>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>

  <!-- Toast Notification Container -->
  <div id="toastContainer" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

  @include('partials.soliera_js')
  
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Handle form submission
    document.getElementById('editDocumentForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const form = this;
      const formData = new FormData(form);
      const updateButton = document.getElementById('updateButton');
      const originalText = updateButton.innerHTML;
      
      // Show loading state
      updateButton.innerHTML = '<i class="loading loading-spinner w-4 h-4 mr-2"></i>Updating...';
      updateButton.disabled = true;
      
      // Submit form via fetch
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
        const data = await response.json();
        
        if (response.ok && data.success) {
          showToast('Document updated successfully!', 'success');
          
          // Redirect back to document view after 2 seconds
          setTimeout(() => {
            window.location.href = `/legal/documents/{{ $document->id }}`;
          }, 2000);
        } else {
          throw new Error(data.message || 'Update failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('Error updating document: ' + error.message, 'error');
      })
      .finally(() => {
        // Restore button
        updateButton.innerHTML = originalText;
        updateButton.disabled = false;
      });
    });
    
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
  </script>
</body>
</html>

