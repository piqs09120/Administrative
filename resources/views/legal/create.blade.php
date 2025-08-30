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
          <a href="{{ route('legal.case_deck') }}" class="btn btn-ghost btn-sm mr-4" style="color: var(--color-regal-navy);">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>Back
          </a>
        </div>

        <!-- Upload New Document Modal Style Layout -->
        <div class="bg-white rounded-xl shadow-lg p-8 max-w-6xl mx-auto">
          <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Add New Legal Case</h1>
            <button onclick="window.history.back()" class="btn btn-ghost btn-sm">
              <i data-lucide="x" class="w-5 h-5"></i>
            </button>
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
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <!-- Left Column: Form Fields -->
              <div class="space-y-6">
                <!-- Case Title -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Case Title*
                  </label>
                  <input type="text" name="case_title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                         value="{{ old('case_title') }}" placeholder="Enter case title" required 
                         style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                  <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    Enter a descriptive title for the legal case
                  </p>
                </div>

                <!-- Case Description -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Case Description
                  </label>
                  <textarea name="case_description" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                            rows="4" placeholder="Brief description of the legal case..." 
                            style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">{{ old('case_description') }}</textarea>
                  <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    Provide a brief description of the case
                  </p>
                </div>

                <!-- Case Type -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Case Type*
                  </label>
                  <select name="case_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required
                          style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                    <option value="">Select case type</option>
                    <option value="contract_dispute" {{ old('case_type') == 'contract_dispute' ? 'selected' : '' }}>Contract Dispute</option>
                    <option value="employment_law" {{ old('case_type') == 'employment_law' ? 'selected' : '' }}>Employment Law</option>
                    <option value="intellectual_property" {{ old('case_type') == 'intellectual_property' ? 'selected' : '' }}>Intellectual Property</option>
                    <option value="regulatory_compliance" {{ old('case_type') == 'regulatory_compliance' ? 'selected' : '' }}>Regulatory Compliance</option>
                    <option value="litigation" {{ old('case_type') == 'litigation' ? 'selected' : '' }}>Litigation</option>
                    <option value="other" {{ old('case_type') == 'other' ? 'selected' : '' }}>Other</option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    Select the type of legal case
                  </p>
                </div>

                <!-- Priority -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Priority*
                  </label>
                  <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required
                          style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                    <option value="">Select priority</option>
                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    Set the priority level for this case
                  </p>
                </div>

                <!-- Assigned To -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Assigned To
                  </label>
                  <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                    <option value="">Select assignee</option>
                    <option value="legal_team" {{ old('assigned_to') == 'legal_team' ? 'selected' : '' }}>Legal Team</option>
                    <option value="senior_counsel" {{ old('assigned_to') == 'senior_counsel' ? 'selected' : '' }}>Senior Counsel</option>
                    <option value="external_counsel" {{ old('assigned_to') == 'external_counsel' ? 'selected' : '' }}>External Counsel</option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    Assign the case to a team member
                  </p>
                </div>
              </div>

              <!-- Right Column: Document Upload & AI Analysis -->
              <div class="space-y-6">
                <!-- Document File Section -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Document File
                  </label>
                  <p class="text-sm text-gray-500 mb-3" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    PDF, Word, Excel, PPT, Text files (Max: 10MB)
                  </p>
                  
                  <!-- File Upload Zone -->
                  <div class="border-2 border-dashed border-blue-300 rounded-lg p-8 text-center transition-colors cursor-pointer bg-blue-50 hover:bg-blue-100"
                       onclick="document.getElementById('legal_document').click()" 
                       ondrop="handleDrop(event)" 
                       ondragover="handleDragOver(event)" 
                       ondragleave="handleDragLeave(event)"
                       id="uploadZone">
                    
                    <input type="file" name="legal_document" id="legal_document" class="hidden" 
                           accept=".pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx" required>
                    
                    <div class="space-y-4">
                      <div class="flex justify-center">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center bg-blue-100">
                          <i data-lucide="cloud-arrow-up" class="w-8 h-8 text-blue-600"></i>
                        </div>
                      </div>
                      <div>
                        <p class="text-lg font-medium text-gray-700" style="color: var(--color-charcoal-ink);">Click to select or drag file</p>
                        <p class="text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">Max file size: 10MB</p>
                      </div>
                      <p class="text-sm text-blue-600 font-medium">AI will automatically analyze and classify your document</p>
                    </div>
                  </div>
                  
                  <!-- File Preview -->
                  <div id="filePreview" class="mt-4 hidden">
                    <div class="rounded-lg p-4 border border-green-300 bg-green-50">
                      <div class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                        <div class="flex-1">
                          <p class="font-medium text-green-800" id="fileName"></p>
                          <p class="text-sm text-green-600" id="fileSize"></p>
                        </div>
                        <button type="button" onclick="removeFile()" class="text-green-600 hover:text-green-800">
                          <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- AI Analysis Complete Section (matches document modal) -->
                <div id="aiAnalysis" class="hidden"></div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 pt-6 border-t border-gray-200">
              <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                <i data-lucide="upload" class="w-5 h-5"></i>
                ADD CASE
              </button>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
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

    function removeFile() {
      document.getElementById('legal_document').value = '';
      document.getElementById('filePreview').classList.add('hidden');
      document.getElementById('aiAnalysis').classList.add('hidden');
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

      // Show loading state (same as document modal)
      const aiAnalysisPanel = document.getElementById('aiAnalysis');
      aiAnalysisPanel.classList.remove('hidden');
      aiAnalysisPanel.innerHTML = `
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="loader-2" class="w-5 h-5 animate-spin text-blue-500"></i>
            <h3 class="font-medium text-blue-800">Analyzing Document...</h3>
          </div>
          <p class="text-sm text-blue-600">AI is processing your document</p>
        </div>
      `;

      fetch('{{ route("document.analyzeUpload") }}', {
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
          // Update AI analysis results and render like document modal
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
          
          const summary = data.analysis.summary || '—';
          const compliance = data.analysis.compliance_status || 'review_required';
          const tags = data.analysis.tags ? (Array.isArray(data.analysis.tags) ? data.analysis.tags.join(', ') : data.analysis.tags) : '—';
          const risk = data.analysis.legal_risk_score || 'Low';
          const needsReview = (data.analysis.requires_legal_review ? 'Yes' : 'No');

          aiAnalysisPanel.innerHTML = `
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
              <div class="flex items-center gap-3 mb-3">
                <i data-lucide=\"check-circle\" class=\"w-5 h-5 text-green-500\"></i>
                <h3 class="font-medium text-green-800">AI Analysis Complete</h3>
              </div>
              <div class="space-y-2 text-sm">
                <div><strong>Category:</strong> <span class="font-semibold text-green-700">${displayCategory}</span></div>
                <div><strong>Summary:</strong> <span class="text-green-700">${summary}</span></div>
                <div><strong>Compliance:</strong> <span class="text-green-700">${compliance}</span></div>
                <div><strong>Tags:</strong> <span class="text-green-700">${tags}</span></div>
                <div><strong>Legal Risk:</strong> <span class="text-green-700">${risk}</span></div>
                <div><strong>Legal Review Required:</strong> <span class="text-green-700">${needsReview}</span></div>
              </div>
            </div>
          `;
          lucide.createIcons();
        } else {
          // Show error state like document modal
          aiAnalysisPanel.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
              <div class="flex items-center gap-3 mb-3">
                <i data-lucide=\"alert-triangle\" class=\"w-5 h-5 text-red-500\"></i>
                <h3 class="font-medium text-red-800">Analysis Failed</h3>
              </div>
              <p class="text-sm text-red-600">${data.message || 'Unable to analyze document'}</p>
            </div>
          `;
        }
      })
      .catch(error => {
        // Show error state like document modal
        aiAnalysisPanel.innerHTML = `
          <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center gap-3 mb-3">
              <i data-lucide=\"alert-triangle\" class=\"w-5 h-5 text-red-500\"></i>
              <h3 class="font-medium text-red-800">Analysis Failed</h3>
            </div>
            <p class="text-sm text-red-600">Network or server error</p>
          </div>
        `;
      });
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize Lucide icons
      lucide.createIcons();
    });
  </script>
</body>
</html>