<!DOCTYPE html>
<html lang="en" data-theme="light">
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
      <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <button onclick="toggleSidebar()" class="btn btn-ghost btn-sm hover:bg-base-300 transition-all hover:scale-105">
              <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h1 class="text-3xl font-bold text-gray-800 ml-4">Add New Legal Case</h1>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Search Bar -->
            <div class="relative">
              <input type="text" id="searchInput" placeholder="Search..." class="input input-bordered input-md w-64 pl-10 bg-white border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
              <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Date and Time -->
            <div class="flex items-center space-x-2 bg-gray-100 px-4 py-3 rounded-lg">
              <i data-lucide="calendar" class="w-5 h-5 text-blue-500"></i>
              <span id="currentDate" class="text-base font-medium text-gray-700"></span>
              <div class="w-px h-5 bg-gray-300"></div>
              <i data-lucide="clock" class="w-5 h-5 text-green-500"></i>
              <span id="currentTime" class="text-base font-medium text-gray-700"></span>
            </div>
            
            <!-- Moon Icon (Dark Mode Toggle) -->
            <button id="darkModeToggle" class="p-2 rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition-colors">
                <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13.66l-.71.71M4.05 19.07l-.71.71M21 12h-1M4 12H3m16.66 5.66l-.71-.71M4.05 4.93l-.71-.71M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="white" viewBox="0 0 24 24" stroke="white">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
                </svg>
            </button>
            <div class="dropdown dropdown-end">
              <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                <i data-lucide="user" class="w-6 h-6 text-gray-600"></i>
              </div>
              <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-white rounded-lg w-52 border border-gray-200">
                <li><a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                  <i data-lucide="user" class="w-4 h-4 text-gray-600"></i>
                  <span>Profile</span>
                </a></li>
                <li><a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                  <i data-lucide="settings" class="w-4 h-4 text-gray-600"></i>
                  <span>Settings</span>
                </a></li>
                <li><a href="#" onclick="logout()" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                  <i data-lucide="log-out" class="w-4 h-4 text-gray-600"></i>
                  <span>Sign out</span>
                </a></li>
              </ul>
            </div>
          </div>
        </div>
      </header>

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
          <a href="{{ route('legal.index') }}" class="btn btn-ghost btn-sm mr-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back to Legal Management
          </a>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Left Column: Add New Legal Case Form -->
          <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-6">
              <i data-lucide="plus" class="w-6 h-6 text-purple-600 mr-3"></i>
              <h2 class="text-xl font-bold text-gray-800">+ Add New Legal Case</h2>
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
                  <span class="label-text font-semibold">Case Title *</span>
                </label>
                <input type="text" name="case_title" class="input input-bordered w-full" 
                       value="{{ old('case_title') }}" placeholder="Enter case title" required>
              </div>

              <!-- Case Description -->
              <div class="form-control mb-6">
                <label class="label">
                  <span class="label-text font-semibold">Case Description</span>
                </label>
                <textarea name="case_description" class="textarea textarea-bordered w-full h-32" 
                          placeholder="Enter case description">{{ old('case_description') }}</textarea>
              </div>

              <!-- File Upload Section -->
              <div class="form-control mb-6">
                <label class="label">
                  <span class="label-text font-semibold">Upload Legal Document *</span>
                </label>
                
                <!-- File Upload Zone -->
                <div class="border-2 border-dashed border-blue-300 rounded-xl p-8 text-center hover:border-blue-400 transition-colors cursor-pointer" 
                     onclick="document.getElementById('legal_document').click()" 
                     ondrop="handleDrop(event)" 
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)">
                  
                  <input type="file" name="legal_document" id="legal_document" class="hidden" 
                         accept=".pdf,.doc,.docx,.txt" required>
                  
                  <div class="space-y-4">
                    <div class="flex justify-center">
                      <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <i data-lucide="cloud-arrow-up" class="w-8 h-8 text-gray-500"></i>
                      </div>
                    </div>
                    <div>
                      <p class="text-lg font-medium text-gray-700">Drop your legal document here</p>
                      <p class="text-sm text-gray-500">or click to browse files</p>
                    </div>
                    <button type="button" class="btn btn-outline btn-primary">
                      <i data-lucide="file" class="w-4 h-4 mr-2"></i>
                      CHOOSE FILE
                    </button>
                  </div>
                </div>
                
                <!-- File Info -->
                <div class="mt-4">
                  <p class="text-sm text-gray-600">Accepted: PDF, DOC, DOCX, TXT (max 10MB)</p>
                </div>

                <!-- File Preview -->
                <div id="filePreview" class="mt-4 hidden">
                  <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                      <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                      <div>
                        <p class="font-medium text-green-800" id="fileName"></p>
                        <p class="text-sm text-green-600" id="fileSize"></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="form-control">
                <button type="submit" class="btn btn-warning btn-lg w-full">
                  <i data-lucide="arrow-up" class="w-5 h-5 mr-2"></i>
                  ADD CASE
                </button>
              </div>
            </form>
          </div>

          <!-- Right Column: AI Classification Preview -->
          <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-6">
              <i data-lucide="brain" class="w-6 h-6 text-blue-600 mr-3"></i>
              <h2 class="text-xl font-bold text-gray-800">AI Classification Preview</h2>
            </div>

            <!-- AI Preview Content -->
            <div id="aiPreview" class="text-center py-12">
              <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="file-text" class="w-12 h-12 text-gray-400"></i>
              </div>
              <h3 class="text-lg font-semibold text-gray-700 mb-2">Upload a Document</h3>
              <p class="text-gray-500">AI will automatically classify your legal document and show the preview here.</p>
            </div>

            <!-- AI Analysis Results -->
            <div id="aiAnalysis" class="hidden space-y-4">
              <!-- AI Classification -->
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                  <i data-lucide="wrench" class="w-4 h-4 text-blue-600"></i>
                  <span class="text-sm font-medium text-blue-800">Document Type:</span>
                </div>
                <div class="text-lg font-bold text-blue-900 mb-1" id="aiCategory">Legal General</div>
                <div class="text-sm text-blue-700" id="aiConfidence">AI Confidence: High (95%)</div>
              </div>

              <!-- AI Summary -->
              <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-2">AI Summary</h4>
                <p class="text-gray-700 text-sm" id="aiSummary">This document has been analyzed by AI and classified as a general legal document.</p>
              </div>

              <!-- Key Information -->
              <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-2">Key Information</h4>
                <p class="text-gray-700 text-sm" id="aiKeyInfo">Key information will be extracted during processing.</p>
              </div>

              <!-- Legal Implications -->
              <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-2">Legal Implications</h4>
                <p class="text-gray-700 text-sm" id="aiLegalImplications">Legal implications will be determined based on document content.</p>
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
    // Dark mode functionality
    function setupDarkMode() {
      const toggle = document.getElementById('darkModeToggle');
      const sunIcon = document.getElementById('sunIcon');
      const moonIcon = document.getElementById('moonIcon');
      
      function updateIcons() {
        if(document.documentElement.classList.contains('dark')) {
          sunIcon.classList.remove('hidden');
          moonIcon.classList.add('hidden');
        } else {
          sunIcon.classList.add('hidden');
          moonIcon.classList.remove('hidden');
        }
      }
      
      // Initial state
      const isDarkMode = localStorage.getItem('darkMode') === 'true';
      if (isDarkMode) {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark');
      }
      updateIcons();
      
      toggle.addEventListener('click', function() {
        console.log('Dark mode toggle clicked!');
        
        // Direct toggle without relying on global function
        if (document.documentElement.classList.contains('dark')) {
          // Switch to light mode
          document.documentElement.classList.remove('dark');
          document.body.classList.remove('dark');
          localStorage.setItem('darkMode', 'false');
          console.log('Switched to LIGHT mode');
        } else {
          // Switch to dark mode
          document.documentElement.classList.add('dark');
          document.body.classList.add('dark');
          localStorage.setItem('darkMode', 'true');
          console.log('Switched to DARK mode');
        }
        
        updateIcons();
      });
    }

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
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <i data-lucide="loader-2" class="w-8 h-8 text-blue-500 animate-spin"></i>
            </div>
            <h3 class="text-lg font-semibold text-blue-700 mb-2">Analyzing Document...</h3>
            <p class="text-blue-600">AI is classifying your legal document</p>
          </div>
        </div>
      `;

      fetch('{{ route("document.analyze") }}', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
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
              <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
              </div>
              <h3 class="text-lg font-semibold text-red-700 mb-2">Analysis Failed</h3>
              <p class="text-red-600">${data.message}</p>
            </div>
          `;
        }
      })
      .catch(error => {
        // Show error state
        aiPreview.innerHTML = `
          <div class="text-center py-12">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
            </div>
            <h3 class="text-lg font-semibold text-red-700 mb-2">Analysis Failed</h3>
            <p class="text-red-600">Unable to analyze document</p>
          </div>
        `;
      });
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      setupDarkMode();
      updateDateTime();
      
      // Update time every second
      setInterval(updateDateTime, 1000);
    });
  </script>
</body>
</html>