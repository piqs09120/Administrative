<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Document - Soliera</title>
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

        <!-- Back button and title -->
        <div class="flex items-center mb-6">
          <a href="{{ route('document.index') }}" class="btn btn-ghost btn-sm mr-4" style="color: var(--color-regal-navy);">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>Back
          </a>
        </div>

        @if($errors->any())
          <div class="alert alert-error mb-6">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Upload Document Form - Modern Two Column Layout -->
        <div class="max-w-6xl mx-auto">
          <form action="{{ route('document.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              
              <!-- Left Column: Form Fields -->
              <div class="space-y-6">
                <div>
                  <h1 class="text-3xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Upload New Document</h1>
                  <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Fill details and select a document file.</p>
                </div>

                <!-- Title Field -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold flex items-center gap-2" style="color: var(--color-charcoal-ink);">
                      <i data-lucide="file-text" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>
                      Title *
                    </span>
                  </label>
                  <input type="text" name="title" class="input input-bordered w-full" 
                         value="{{ old('title') }}" placeholder="Enter document title" required style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                </div>

                <!-- Department and Category Fields (Side by Side) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Department Field -->
                  <div class="form-control">
                    <label class="label">
                      <span class="label-text font-semibold flex items-center gap-2" style="color: var(--color-charcoal-ink);">
                        <i data-lucide="building" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>
                        Department
                      </span>
                    </label>
                    <select name="department" class="select select-bordered w-full" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                      <option value="">Select Department</option>
                      <option value="management" {{ old('department') === 'management' ? 'selected' : '' }}>Management</option>
                      <option value="legal" {{ old('department') === 'legal' ? 'selected' : '' }}>Legal</option>
                      <option value="hr" {{ old('department') === 'hr' ? 'selected' : '' }}>Human Resources</option>
                      <option value="finance" {{ old('department') === 'finance' ? 'selected' : '' }}>Finance</option>
                      <option value="operations" {{ old('department') === 'operations' ? 'selected' : '' }}>Operations</option>
                    </select>
                  </div>

                  <!-- AI Category Field -->
                  <div class="form-control">
                    <label class="label">
                      <span class="label-text font-semibold flex items-center gap-2" style="color: var(--color-charcoal-ink);">
                        <i data-lucide="brain" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>
                        Category (AI Determined)
                      </span>
                    </label>
                    <input type="text" name="category" id="aiCategoryField" class="input input-bordered w-full bg-gray-50" 
                           value="{{ old('category') }}" placeholder="Will be determined by AI analysis" readonly style="color: var(--color-charcoal-ink); background-color: var(--color-snow-mist); border-color: var(--color-snow-mist);">
                    <input type="hidden" name="ai_category" id="aiCategoryHidden">
                  </div>
                </div>

                <!-- Author Field -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold flex items-center gap-2" style="color: var(--color-charcoal-ink);">
                      <i data-lucide="user" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>
                      Author
                    </span>
                  </label>
                  <input type="text" name="author" class="input input-bordered w-full" 
                         value="{{ old('author') }}" placeholder="e.g. Jane Doe" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                </div>

                <!-- Summary Field -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold flex items-center gap-2" style="color: var(--color-charcoal-ink);">
                      <i data-lucide="file-text" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>
                      Summary
                    </span>
                  </label>
                  <textarea name="description" class="textarea textarea-bordered w-full h-24" 
                            placeholder="Brief summary of the document..." style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">{{ old('description') }}</textarea>
                </div>
              </div>

              <!-- Right Column: File Upload Area -->
              <div class="space-y-6">
                <div>
                  <h2 class="text-xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Document File</h2>
                  <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">PDF, Word, Excel, PPT, Text</p>
                </div>

                <!-- File Upload Zone -->
                <div class="border-2 border-dashed border-blue-300 rounded-xl p-8 text-center hover:border-blue-400 transition-colors cursor-pointer" 
                     onclick="document.getElementById('document_file').click()" 
                     ondrop="handleDrop(event)" 
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)"
                     style="border-color: var(--color-regal-navy); background-color: var(--color-white);">
                  
                  <input type="file" name="document_file" id="document_file" class="hidden" 
                         accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required>
                  
                  <div class="space-y-4">
                    <div class="flex justify-center">
                      <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%);">
                        <i data-lucide="cloud-upload" class="w-8 h-8" style="color: var(--color-regal-navy);"></i>
                      </div>
                    </div>
                    <div>
                      <p class="text-lg font-medium text-gray-700" style="color: var(--color-charcoal-ink);">Click to select or drag file</p>
                      <p class="text-sm text-gray-500 mt-2" style="color: var(--color-charcoal-ink); opacity: 0.7;">Max file size: 10MB</p>
                    </div>
                  </div>
                </div>

                <!-- File Preview -->
                <div id="filePreview" class="hidden">
                  <div class="bg-green-50 border border-green-200 rounded-lg p-4" style="background-color: color-mix(in srgb, var(--color-modern-teal), white 90%); border-color: var(--color-modern-teal);">
                    <div class="flex items-center gap-3">
                      <i data-lucide="check-circle" class="w-5 h-5" style="color: var(--color-modern-teal);"></i>
                      <div>
                        <p class="font-medium text-green-800" id="fileName" style="color: var(--color-charcoal-ink);"></p>
                        <p class="text-sm text-green-600" id="fileSize" style="color: var(--color-charcoal-ink);"></p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- AI Analysis Results -->
                <div id="aiAnalysis" class="hidden">
                  <div class="bg-blue-50 border border-blue-200 rounded-lg p-4" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 90%); border-color: var(--color-regal-navy);">
                    <div class="flex items-center gap-3 mb-3">
                      <i data-lucide="brain" class="w-5 h-5" style="color: var(--color-regal-navy);"></i>
                      <h3 class="font-medium text-blue-800" style="color: var(--color-charcoal-ink);">AI Analysis Complete</h3>
                    </div>
                    <div class="space-y-2 text-sm" style="color: var(--color-charcoal-ink);">
                      <div><strong>Category:</strong> <span id="aiCategory"></span></div>
                      <div><strong>Summary:</strong> <span id="aiSummary"></span></div>
                      <div><strong>Compliance:</strong> <span id="aiCompliance"></span></div>
                      <div><strong>Tags:</strong> <span id="aiTags"></span></div>
                    </div>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                  <button type="submit" class="btn btn-primary btn-lg w-full">
                    <i data-lucide="upload" class="w-5 h-5 mr-2"></i>
                    Upload Document
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </main>
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
      
      dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
      timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
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
        // Trigger AI analysis
        analyzeDocument(e.target.files[0]);
      }
    });

    // AI Document Analysis
    function analyzeDocument(file) {
      const formData = new FormData();
      formData.append('document_file', file);
      formData.append('_token', '{{ csrf_token() }}');

      // Show loading state
      const aiAnalysis = document.getElementById('aiAnalysis');
      aiAnalysis.innerHTML = `
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="loader-2" class="w-5 h-5 text-blue-500 animate-spin"></i>
            <h3 class="font-medium text-blue-800">AI Analysis in Progress...</h3>
          </div>
          <p class="text-sm text-blue-600">Analyzing document content and determining category...</p>
        </div>
      `;
      aiAnalysis.classList.remove('hidden');
      
      // Also show loading state in category field
      const aiCategoryField = document.getElementById('aiCategoryField');
      if (aiCategoryField) {
        aiCategoryField.value = 'Analyzing...';
        aiCategoryField.classList.remove('bg-gray-50');
        aiCategoryField.classList.add('bg-blue-50', 'border-blue-300');
      }

      fetch('{{ route("document.analyze-upload") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(async response => {
        console.log('Response status:', response.status);
        const contentType = response.headers.get('content-type') || '';
        if (!response.ok) {
          const fallback = contentType.includes('application/json') ? await response.json() : { success: false, message: 'Server error' };
          return fallback;
        }
        if (!contentType.includes('application/json')) {
          return { success: false, message: 'Unexpected response from server' };
        }
        return response.json();
      })
      .then(data => {
        console.log('AI Analysis response:', data);
        if (data.success) {
          // Build UI with analysis results (avoid touching missing nodes)
          const catEl = document.getElementById('aiCategory');
          const sumEl = document.getElementById('aiSummary');
          const compEl = document.getElementById('aiCompliance');
          const tagsEl = document.getElementById('aiTags');
          if (catEl) catEl.textContent = (data.analysis && data.analysis.category) || 'general';
          if (sumEl) sumEl.textContent = (data.analysis && data.analysis.summary) || '';
          if (compEl) compEl.textContent = (data.analysis && data.analysis.compliance_status) || 'review_required';
          if (tagsEl && data.analysis && Array.isArray(data.analysis.tags)) tagsEl.textContent = data.analysis.tags.join(', ');

                     // Auto-fill AI category field
           const aiCategoryField = document.getElementById('aiCategoryField');
           const aiCategoryHidden = document.getElementById('aiCategoryHidden');
           const aiCategory = (data.analysis && data.analysis.category) || 'general';
           
           // Map AI categories to display names
           const categoryMapping = {
             'contract': 'Legal - Contract',
             'legal_notice': 'Legal - Notice',
             'policy': 'Legal - Policy',
             'compliance': 'Legal - Compliance',
             'financial': 'Financial',
             'report': 'Technical - Report',
             'affidavit': 'Legal - Affidavit',
             'memorandum': 'Legal - Memorandum',
             'subpoena': 'Legal - Subpoena',
             'cease_desist': 'Legal - Cease & Desist',
             'legal_brief': 'Legal - Brief',
             'general': 'Operations - General'
           };

           const displayCategory = categoryMapping[aiCategory] || 'Operations - General';
           
           // Set the AI category field
           if (aiCategoryField) {
             aiCategoryField.value = displayCategory;
             aiCategoryField.classList.remove('bg-gray-50');
             
             // Add visual feedback based on analysis type
             if (data.analysis.fallback) {
               aiCategoryField.classList.add('bg-yellow-50', 'border-yellow-300');
               aiCategoryField.title = 'Category determined using fallback analysis (API quota exceeded)';
             } else {
               aiCategoryField.classList.add('bg-green-50', 'border-green-300');
               aiCategoryField.title = 'Category determined by AI analysis';
             }
           }
           
           // Set the hidden field with the original AI category
           if (aiCategoryHidden) {
             aiCategoryHidden.value = aiCategory;
           }

          // Auto-fill summary field
          const summaryField = document.querySelector('textarea[name="description"]');
          if (summaryField && data.analysis && data.analysis.summary) {
            summaryField.value = data.analysis.summary;
          }

          // Show success state
          aiAnalysis.innerHTML = `
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
              <div class="flex items-center gap-3 mb-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                <h3 class="font-medium text-green-800">AI Analysis Complete</h3>
              </div>
              <div class="space-y-2 text-sm">
                <div><strong>Category:</strong> <span id="aiCategory">${data.analysis.category}</span></div>
                <div><strong>Summary:</strong> <span id="aiSummary">${data.analysis.summary}</span></div>
                <div><strong>Compliance:</strong> <span id="aiCompliance">${data.analysis.compliance_status}</span></div>
                <div><strong>Tags:</strong> <span id="aiTags">${data.analysis.tags.join(', ')}</span></div>
              </div>
            </div>
          `;
        } else {
          // Show error state
          aiAnalysis.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
              <div class="flex items-center gap-3 mb-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                <h3 class="font-medium text-red-800">AI Analysis Failed</h3>
              </div>
              <p class="text-sm text-red-600">${data.message || 'Unable to analyze document'}</p>
            </div>
          `;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        aiAnalysis.innerHTML = `
          <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center gap-3 mb-3">
              <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
              <h3 class="font-medium text-red-800">AI Analysis Failed</h3>
            </div>
            <p class="text-sm text-red-600">Network error occurred. Please check your connection or try again.</p>
          </div>
        `;
      });
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      updateDateTime();
      
      // Update time every second
      setInterval(updateDateTime, 1000);
    });
  </script>
</body>
</html> 