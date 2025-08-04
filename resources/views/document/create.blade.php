<!DOCTYPE html>
<html lang="en" data-theme="light">
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
      <!-- Header - Exactly like the image -->
      <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <button onclick="toggleSidebar()" class="btn btn-ghost btn-sm hover:bg-base-300 transition-all hover:scale-105">
              <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h1 class="text-3xl font-bold text-gray-800 ml-4">Upload Document</h1>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Search Bar - Like in the image -->
            <div class="relative">
              <input type="text" id="searchInput" placeholder="Search..." class="input input-bordered input-md w-64 pl-10 bg-white border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
              <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Date and Time - Like in the image -->
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

        <!-- Back button and title -->
        <div class="flex items-center mb-6">
          <a href="{{ route('document.index') }}" class="btn btn-ghost btn-sm mr-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back
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
                  <h1 class="text-3xl font-bold text-gray-800 mb-2">Upload New Document</h1>
                  <p class="text-gray-600">Fill details and select a document file.</p>
                </div>

                <!-- Title Field -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold flex items-center gap-2">
                      <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i>
                      Title *
                    </span>
                  </label>
                  <input type="text" name="title" class="input input-bordered w-full" 
                         value="{{ old('title') }}" placeholder="Enter document title" required>
                </div>

                <!-- Department and Category Fields (Side by Side) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Department Field -->
                  <div class="form-control">
                    <label class="label">
                      <span class="label-text font-semibold flex items-center gap-2">
                        <i data-lucide="building" class="w-4 h-4 text-blue-500"></i>
                        Department
                      </span>
                    </label>
                    <select name="department" class="select select-bordered w-full">
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
                      <span class="label-text font-semibold flex items-center gap-2">
                        <i data-lucide="brain" class="w-4 h-4 text-blue-500"></i>
                        Category (AI Determined)
                      </span>
                    </label>
                    <input type="text" name="category" id="aiCategoryField" class="input input-bordered w-full bg-gray-50" 
                           value="{{ old('category') }}" placeholder="Will be determined by AI analysis" readonly>
                    <input type="hidden" name="ai_category" id="aiCategoryHidden">
                  </div>
                </div>

                <!-- Author Field -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold flex items-center gap-2">
                      <i data-lucide="user" class="w-4 h-4 text-blue-500"></i>
                      Author
                    </span>
                  </label>
                  <input type="text" name="author" class="input input-bordered w-full" 
                         value="{{ old('author') }}" placeholder="e.g. Jane Doe">
                </div>

                <!-- Summary Field -->
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-semibold flex items-center gap-2">
                      <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i>
                      Summary
                    </span>
                  </label>
                  <textarea name="description" class="textarea textarea-bordered w-full h-24" 
                            placeholder="Brief summary of the document...">{{ old('description') }}</textarea>
                </div>
              </div>

              <!-- Right Column: File Upload Area -->
              <div class="space-y-6">
                <div>
                  <h2 class="text-xl font-bold text-gray-800 mb-2">Document File</h2>
                  <p class="text-gray-600">PDF, Word, Excel, PPT, Text</p>
                </div>

                <!-- File Upload Zone -->
                <div class="border-2 border-dashed border-blue-300 rounded-xl p-8 text-center hover:border-blue-400 transition-colors cursor-pointer" 
                     onclick="document.getElementById('document_file').click()" 
                     ondrop="handleDrop(event)" 
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)">
                  
                  <input type="file" name="document_file" id="document_file" class="hidden" 
                         accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required>
                  
                  <div class="space-y-4">
                    <div class="flex justify-center">
                      <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
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
                    </div>
                  </div>
                </div>

                <!-- AI Analysis Results -->
                <div id="aiAnalysis" class="hidden">
                  <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-3">
                      <i data-lucide="brain" class="w-5 h-5 text-blue-500"></i>
                      <h3 class="font-medium text-blue-800">AI Analysis Complete</h3>
                    </div>
                    <div class="space-y-2 text-sm">
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

      fetch('{{ route("document.analyze") }}', {
        method: 'POST',
        body: formData
      })
      .then(response => {
        console.log('Response status:', response.status);
        return response.json();
      })
      .then(data => {
        console.log('AI Analysis response:', data);
        if (data.success) {
          // Update AI analysis display
          document.getElementById('aiCategory').textContent = data.analysis.category;
          document.getElementById('aiSummary').textContent = data.analysis.summary;
          document.getElementById('aiCompliance').textContent = data.analysis.compliance_status;
          document.getElementById('aiTags').textContent = data.analysis.tags.join(', ');

                     // Auto-fill AI category field
           const aiCategoryField = document.getElementById('aiCategoryField');
           const aiCategoryHidden = document.getElementById('aiCategoryHidden');
           const aiCategory = data.analysis.category;
           
           // Map AI categories to display names
           const categoryMapping = {
             'contract': 'Legal - Contract',
             'legal_notice': 'Legal - Notice',
             'policy': 'Legal - Policy',
             'compliance': 'Legal - Compliance',
             'financial': 'Financial',
             'report': 'Technical - Report',
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
          if (summaryField && data.analysis.summary) {
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
            <p class="text-sm text-red-600">Network error occurred</p>
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