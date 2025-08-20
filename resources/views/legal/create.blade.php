<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Legal Case - Soliera</title>
  <link rel="icon" href="swt.jpg" type="image/x-icon">
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

        <!-- Back button -->
        <div class="flex items-center mb-6">
          <a href="{{ route('legal.index') }}" class="btn btn-ghost btn-sm mr-4" style="color: var(--color-regal-navy);">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>Back
          </a>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Left Column: Add New Legal Case Form -->
          <div class="bg-white rounded-xl shadow-lg p-6" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
            <div class="flex items-center mb-6">
              <i data-lucide="plus" class="w-6 h-6 mr-3" style="color: var(--color-regal-navy);"></i>
              <h2 class="text-xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);"> Add New Legal Case</h2>
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

            <form action="{{ route('legal.store') }}" method="POST" enctype="multipart/form-data" id="legalCaseForm">
              @csrf
              
              <!-- Case Title -->
              <div class="form-control mb-6">
                <label class="label">
                  <span class="label-text font-semibold" style="color: var(--color-charcoal-ink);">Case Title *</span>
                </label>
                <input type="text" name="case_title" class="input input-bordered w-full" 
                       value="{{ old('case_title') }}" placeholder="Enter case title" required style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
              </div>

              <!-- Case Description -->
              <div class="form-control mb-6">
                <label class="label">
                  <span class="label-text font-semibold" style="color: var(--color-charcoal-ink);">Case Description</span>
                </label>
                <textarea name="case_description" class="textarea textarea-bordered w-full h-32" 
                          placeholder="Enter case description" style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">{{ old('case_description') }}</textarea>
              </div>

              <!-- File Upload Section -->
              <div class="form-control mb-6">
                <label class="label">
                  <span class="label-text font-semibold" style="color: var(--color-charcoal-ink);">Upload Legal Document *</span>
                </label>
                
                <!-- File Upload Zone -->
                <div class="border-2 border-dashed rounded-xl p-8 text-center transition-colors cursor-pointer"
                     onclick="document.getElementById('legal_document').click()" 
                     ondrop="handleDrop(event)" 
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)"
                     style="border-color: var(--color-regal-navy); background-color: var(--color-white);">
                  
                  <input type="file" name="legal_document" id="legal_document" class="hidden" 
                         accept=".pdf,.doc,.docx,.txt" required>
                  
                  <div class="space-y-4">
                    <div class="flex justify-center">
                      <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%);">
                        <i data-lucide="cloud-arrow-up" class="w-8 h-8" style="color: var(--color-regal-navy);"></i>
                      </div>
                    </div>
                    <div>
                      <p class="text-lg font-medium text-gray-700" style="color: var(--color-charcoal-ink);">Drop your legal document here</p>
                      <p class="text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">or click to browse files</p>
                    </div>
                    <button type="button" class="btn btn-outline btn-primary">
                      <i data-lucide="file" class="w-4 h-4 mr-2"></i>
                      CHOOSE FILE
                    </button>
                  </div>
                </div>
                
                <!-- File Info -->
                <div class="mt-4">
                  <p class="text-sm text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Accepted: PDF, DOC, DOCX, TXT (max 10MB)</p>
                </div>

                <!-- File Preview -->
                <div id="filePreview" class="mt-4 hidden">
                  <div class="rounded-lg p-4 border" style="background-color: color-mix(in srgb, var(--color-modern-teal), white 90%); border-color: var(--color-modern-teal);">
                    <div class="flex items-center gap-3">
                      <i data-lucide="check-circle" class="w-5 h-5" style="color: var(--color-modern-teal);"></i>
                      <div>
                        <p class="font-medium text-green-800" id="fileName" style="color: var(--color-charcoal-ink);"></p>
                        <p class="text-sm text-green-600" id="fileSize" style="color: var(--color-charcoal-ink);"></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="form-control">
                <button type="submit" class="btn btn-warning btn-lg w-full" style="background-color: var(--color-golden-ember); color: var(--color-white); border-color: var(--color-golden-ember);">
                  <i data-lucide="arrow-up" class="w-5 h-5 mr-2"></i>
                  ADD CASE
                </button>
              </div>
            </form>
          </div>

          <!-- Right Column: AI Classification Preview -->
          <div class="bg-white rounded-xl shadow-lg p-6" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
            <div class="flex items-center mb-6">
              <i data-lucide="brain" class="w-6 h-6 mr-3" style="color: var(--color-regal-navy);"></i>
              <h2 class="text-xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">AI Classification Preview</h2>
            </div>

            <!-- AI Preview Content -->
            <div id="aiPreview" class="text-center py-12">
              <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%);">
                <i data-lucide="file-text" class="w-12 h-12" style="color: var(--color-regal-navy);"></i>
              </div>
              <h3 class="text-lg font-semibold text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">Upload a Document</h3>
              <p class="text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">AI will automatically classify your legal document and show the preview here.</p>
            </div>

            <!-- AI Analysis Results -->
            <div id="aiAnalysis" class="hidden space-y-4">
              <!-- AI Classification -->
              <div class="rounded-lg p-4 border" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 90%); border-color: var(--color-regal-navy);">
                <div class="flex items-center gap-2 mb-2">
                  <i data-lucide="wrench" class="w-4 h-4" style="color: var(--color-regal-navy);"></i>
                  <span class="text-sm font-medium" style="color: var(--color-charcoal-ink);">Document Type:</span>
                </div>
                <div class="text-lg font-bold text-blue-900 mb-1" id="aiCategory" style="color: var(--color-charcoal-ink);">Legal General</div>
                <div class="text-sm text-blue-700" id="aiConfidence" style="color: var(--color-charcoal-ink); opacity: 0.8;">AI Confidence: High (95%)</div>
              </div>

              <!-- AI Summary -->
              <div class="rounded-lg p-4 border" style="background-color: color-mix(in srgb, var(--color-snow-mist), black 5%); border-color: color-mix(in srgb, var(--color-snow-mist), black 10%);">
                <h4 class="font-semibold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">AI Summary</h4>
                <p class="text-gray-700 text-sm" id="aiSummary" style="color: var(--color-charcoal-ink);">This document has been analyzed by AI and classified as a general legal document.</p>
              </div>

              <!-- Key Information -->
              <div class="rounded-lg p-4 border" style="background-color: color-mix(in srgb, var(--color-modern-teal), white 90%); border-color: var(--color-modern-teal);">
                <h4 class="font-semibold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Key Information</h4>
                <p class="text-gray-700 text-sm" id="aiKeyInfo" style="color: var(--color-charcoal-ink);">Key information will be extracted during processing.</p>
              </div>

              <!-- Legal Implications -->
              <div class="rounded-lg p-4 border" style="background-color: color-mix(in srgb, var(--color-golden-ember), white 90%); border-color: var(--color-golden-ember);">
                <h4 class="font-semibold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Legal Implications</h4>
                <p class="text-gray-700 text-sm" id="aiLegalImplications" style="color: var(--color-charcoal-ink);">Legal implications will be determined based on document content.</p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
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
      
      if (dateElement) dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
      if (timeElement) timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
    }

    // File upload handling
    function handleDrop(e) {
      e.preventDefault();
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        document.getElementById('legal_document').files = files;
        updateFilePreview(files[0]);
        analyzeDocument(files[0]);
      }
    }

    function handleDragOver(e) {
      e.preventDefault();
    }

    function handleDragLeave(e) {
      e.preventDefault();
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
    document.getElementById('legal_document').addEventListener('change', function(e) {
      if (e.target.files.length > 0) {
        updateFilePreview(e.target.files[0]);
        analyzeDocument(e.target.files[0]);
      }
    });

    // AI Document Analysis
    function analyzeDocument(file) {
      const formData = new FormData();
      formData.append('document_file', file);
      formData.append('_token', '{{ csrf_token() }}');

      // Show loading state
      const aiPreview = document.getElementById('aiPreview');
      const aiAnalysis = document.getElementById('aiAnalysis');
      
      aiPreview.innerHTML = `
        <div class="flex items-center justify-center py-12">
          <div class="text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%);">
              <i data-lucide="loader-2" class="w-8 h-8 animate-spin" style="color: var(--color-regal-navy);"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2" style="color: var(--color-charcoal-ink);">Analyzing Document...</h3>
            <p style="color: var(--color-charcoal-ink); opacity: 0.8;">AI is classifying your legal document</p>
          </div>
        </div>
      `;

      fetch('{{ route("document.analyze-upload") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(async response => {
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
        if (data.success) {
          // Update AI analysis results
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
            'general': 'Legal General'
          };

          const displayCategory = categoryDisplayNames[data.analysis.category] || 'Legal General';
          
          document.getElementById('aiCategory').textContent = displayCategory;
          document.getElementById('aiSummary').textContent = data.analysis.summary || 'This document has been analyzed by AI and classified as a legal document.';
          document.getElementById('aiKeyInfo').textContent = data.analysis.key_info || 'Key information extracted from document content.';
          document.getElementById('aiLegalImplications').textContent = data.analysis.legal_implications || 'Legal implications will be determined based on document content.';
          
          // Show analysis results
          aiAnalysis.classList.remove('hidden');
          aiPreview.classList.add('hidden');
        } else {
          // Show error state
          aiPreview.innerHTML = `
            <div class="text-center py-12">
              <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: color-mix(in srgb, var(--color-danger-red), white 80%);">
                <i data-lucide="alert-triangle" class="w-8 h-8" style="color: var(--color-danger-red);"></i>
              </div>
              <h3 class="text-lg font-semibold mb-2" style="color: var(--color-charcoal-ink);">Analysis Failed</h3>
              <p style="color: var(--color-charcoal-ink); opacity: 0.8;">${data.message}</p>
            </div>
          `;
        }
      })
      .catch(error => {
        // Show error state
        aiPreview.innerHTML = `
          <div class="text-center py-12">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: color-mix(in srgb, var(--color-danger-red), white 80%);">
              <i data-lucide="alert-triangle" class="w-8 h-8" style="color: var(--color-danger-red);"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2" style="color: var(--color-charcoal-ink);">Analysis Failed</h3>
            <p style="color: var(--color-charcoal-ink); opacity: 0.8;">Unable to analyze document</p>
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