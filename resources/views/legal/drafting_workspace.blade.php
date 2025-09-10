<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Legal Document Drafting Workspace - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  @vite(['resources/css/soliera.css'])
  <style>
    .ql-editor {
      min-height: 500px;
      font-family: 'Times New Roman', serif;
      font-size: 12pt;
      line-height: 1.5;
    }
    .ql-toolbar {
      border-top: 1px solid #ccc;
      border-left: 1px solid #ccc;
      border-right: 1px solid #ccc;
    }
    .ql-container {
      border-bottom: 1px solid #ccc;
      border-left: 1px solid #ccc;
      border-right: 1px solid #ccc;
    }
    .drafting-workspace {
      height: calc(100vh - 120px);
    }
    .editor-container {
      height: calc(100vh - 200px);
    }
    .toolbar-section {
      background: #f8f9fa;
      border-bottom: 1px solid #dee2e6;
    }
    .document-info {
      background: #e9ecef;
      border-bottom: 1px solid #dee2e6;
    }
    /* Ensure proper spacing for the new layout */
    .card-body {
      padding: 1rem;
    }
  </style>
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
            <h1 class="text-3xl font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Legal Document Drafting Workspace</h1>
            <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Create and edit legal documents with rich formatting tools</p>
          </div>
        </div>

        <!-- Main Editor Container -->
        <div class="bg-white rounded-xl shadow-lg">
          <!-- Editor Header -->
          <div class="border-b border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                  <i data-lucide="edit-3" class="w-5 h-5 text-blue-600"></i>
                  Document Editor
                </h3>
                @if($document)
                  <span class="badge badge-info">Editing: {{ $document->title }}</span>
                @else
                  <span class="badge badge-primary">New Document</span>
                @endif
              </div>
              <div class="flex items-center gap-2">
                <button onclick="loadTemplate()" class="btn btn-outline btn-sm">
                  <i data-lucide="file-text" class="w-4 h-4 mr-1"></i>
                  Load Template
                </button>
                <button onclick="startBlank()" class="btn btn-outline btn-sm">
                  <i data-lucide="file-plus" class="w-4 h-4 mr-1"></i>
                  Start Blank
                </button>
              </div>
            </div>

            <!-- Document Properties -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-medium">Document Title</span>
                </label>
                <input type="text" id="documentTitle" class="input input-bordered w-full" 
                       value="{{ $document->title ?? '' }}" placeholder="Enter document title...">
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-medium">Type</span>
                </label>
                <select id="documentType" class="select select-bordered w-full">
                  <option value="contract" {{ ($document->category ?? '') == 'contract' ? 'selected' : '' }}>Contract</option>
                  <option value="policy" {{ ($document->category ?? '') == 'policy' ? 'selected' : '' }}>Policy</option>
                  <option value="agreement" {{ ($document->category ?? '') == 'agreement' ? 'selected' : '' }}>Agreement</option>
                  <option value="notice" {{ ($document->category ?? '') == 'notice' ? 'selected' : '' }}>Notice</option>
                  <option value="general" {{ ($document->category ?? '') == 'general' ? 'selected' : '' }}>General</option>
                </select>
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-medium">Department</span>
                </label>
                <select id="documentDepartment" class="select select-bordered w-full">
                  <option value="Legal" {{ ($document->department ?? '') == 'Legal' ? 'selected' : '' }}>Legal</option>
                  <option value="Human Resources" {{ ($document->department ?? '') == 'Human Resources' ? 'selected' : '' }}>Human Resources</option>
                  <option value="Information Technology" {{ ($document->department ?? '') == 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
                  <option value="Finance" {{ ($document->department ?? '') == 'Finance' ? 'selected' : '' }}>Finance</option>
                  <option value="Operations" {{ ($document->department ?? '') == 'Operations' ? 'selected' : '' }}>Operations</option>
                  <option value="Marketing" {{ ($document->department ?? '') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Editor Content -->
          <div class="p-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
              <!-- Quill Editor -->
              <div id="editor" style="height: 500px;"></div>
            </div>
          </div>

          <!-- Editor Footer -->
          <div class="border-t border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-6 text-sm text-gray-600">
                <span id="charCount">Words: 0 | Characters: 0</span>
                <span id="lastSaved">Ready</span>
                <span>Auto-save: <span class="text-green-600">ON</span></span>
              </div>
              <div class="flex items-center gap-2">
                <button onclick="saveDraft()" class="btn btn-outline btn-sm">
                  <i data-lucide="save" class="w-4 h-4 mr-1"></i>
                  Save Draft
                </button>
                <button onclick="submitForReview()" class="btn btn-primary btn-sm">
                  <i data-lucide="send" class="w-4 h-4 mr-1"></i>
                  Submit for Review
                </button>
                <div class="dropdown dropdown-end">
                  <button tabindex="0" class="btn btn-outline btn-sm">
                    <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                    Export
                    <i data-lucide="chevron-down" class="w-3 h-3 ml-1"></i>
                  </button>
                  <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-32">
                    <li><a onclick="exportDocument('pdf')">PDF</a></li>
                    <li><a onclick="exportDocument('word')">Word</a></li>
                  </ul>
                </div>
                <a href="{{ route('legal.legal_documents') }}" class="btn btn-ghost btn-sm">
                  <i data-lucide="x" class="w-4 h-4 mr-1"></i>
                  Close
                </a>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Template Selection Modal -->
  <div id="templateModal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold">Choose a Template</h3>
        <button class="btn btn-sm btn-circle btn-ghost" onclick="closeTemplateModal()">✕</button>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($templates as $key => $template)
          <div class="card bg-base-100 shadow-sm border border-gray-200 cursor-pointer hover:shadow-md transition-shadow" 
               onclick="loadTemplateContent('{{ $key }}', '{{ addslashes($template['title']) }}', `{{ addslashes($template['content']) }}`)">
            <div class="card-body p-4">
              <h4 class="card-title text-sm">{{ $template['title'] }}</h4>
              <p class="text-xs text-gray-600 mt-2">Click to load this template</p>
            </div>
          </div>
        @endforeach
      </div>
      
      <div class="modal-action">
        <button class="btn btn-outline" onclick="closeTemplateModal()">Cancel</button>
      </div>
    </div>
  </div>

  <script>
    let quill;
    let documentId = {{ $document->id ?? 'null' }};
    let autoSaveInterval;
    let isDirty = false;

    // Initialize Quill editor
    document.addEventListener('DOMContentLoaded', function() {
      // Quill configuration
      const toolbarOptions = [
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'font': [] }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'script': 'sub'}, { 'script': 'super' }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        [{ 'direction': 'rtl' }],
        [{ 'align': [] }],
        ['blockquote', 'code-block'],
        ['link', 'image'],
        ['clean']
      ];

      quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
          toolbar: toolbarOptions
        },
        placeholder: 'Start typing your legal document...'
      });

      // Load existing content if editing
      @if($document && $document->metadata && isset($document->metadata['content']))
        quill.setContents(quill.clipboard.convert('{{ addslashes($document->metadata['content']) }}'));
      @elseif($template && is_string($template) && isset($templates[$template]))
        // Load template content
        const templateContent = `{{ addslashes($templates[$template]['content']) }}`;
        quill.setContents(quill.clipboard.convert(templateContent));
        document.getElementById('documentTitle').value = '{{ $templates[$template]['title'] }}';
        isDirty = true;
        updateLastSaved('Template loaded');
      @endif

      // Set up event listeners
      quill.on('text-change', function() {
        isDirty = true;
        updateWordCount();
        updateLastSaved('Unsaved changes');
      });

      // Auto-save every 30 seconds
      autoSaveInterval = setInterval(function() {
        if (isDirty) {
          saveDraft(true); // Silent save
        }
      }, 30000);

      // Update word count on load
      updateWordCount();
    });

    // Word count and character count
    function updateWordCount() {
      const text = quill.getText();
      const words = text.trim().split(/\s+/).filter(word => word.length > 0).length;
      const chars = text.length;
      
      // Update character count in footer
      const charCountElement = document.getElementById('charCount');
      if (charCountElement) {
        charCountElement.textContent = `Words: ${words} | Characters: ${chars}`;
      }
    }

    function updateLastSaved(message) {
      const lastSavedElement = document.getElementById('lastSaved');
      if (lastSavedElement) {
        lastSavedElement.textContent = message;
      }
    }

    // Template functions
    function loadTemplate() {
      document.getElementById('templateModal').classList.add('modal-open');
    }

    function closeTemplateModal() {
      document.getElementById('templateModal').classList.remove('modal-open');
    }

    function loadTemplateContent(templateKey, title, content) {
      if (typeof templateKey === 'string' && title && content) {
        quill.setContents(quill.clipboard.convert(content));
        document.getElementById('documentTitle').value = title;
        isDirty = true;
        updateWordCount();
        updateLastSaved('Template loaded');
        closeTemplateModal();
      } else {
        showNotification('Error loading template', 'error');
        closeTemplateModal();
      }
    }

    function startBlank() {
      quill.setContents([]);
      document.getElementById('documentTitle').value = '';
      isDirty = true;
      updateWordCount();
      updateLastSaved('Started blank document');
    }

    // Save functions
    function saveDraft(silent = false) {
      const title = document.getElementById('documentTitle').value.trim();
      const content = quill.root.innerHTML;
      const documentType = document.getElementById('documentType').value;
      const department = document.getElementById('documentDepartment').value;

      if (!title) {
        if (!silent) {
          showNotification('Please enter a document title', 'error');
        }
        return;
      }

      if (!content || content === '<p><br></p>') {
        if (!silent) {
          showNotification('Please enter some content', 'error');
        }
        return;
      }

      const formData = new FormData();
      formData.append('title', title);
      formData.append('content', content);
      formData.append('document_type', documentType);
      formData.append('department', department);
      formData.append('document_id', documentId || '');
      formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

      fetch('{{ route("legal.documents.save_draft") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          documentId = data.document_id;
          isDirty = false;
          if (!silent) {
            showNotification(data.message, 'success');
          }
          updateLastSaved('Saved at ' + new Date().toLocaleTimeString());
        } else {
          if (!silent) {
            showNotification('Error saving draft: ' + (data.message || 'Unknown error'), 'error');
          }
        }
      })
      .catch(error => {
        console.error('Error:', error);
        if (!silent) {
          showNotification('Error saving draft', 'error');
        }
      });
    }

    function submitForReview() {
      const title = document.getElementById('documentTitle').value.trim();
      const content = quill.root.innerHTML;
      const documentType = document.getElementById('documentType').value;
      const department = document.getElementById('documentDepartment').value;

      if (!title) {
        showNotification('Please enter a document title', 'error');
        return;
      }

      if (!content || content === '<p><br></p>') {
        showNotification('Please enter some content', 'error');
        return;
      }

      if (!confirm('Are you sure you want to submit this document for review? This action cannot be undone.')) {
        return;
      }

      const formData = new FormData();
      formData.append('title', title);
      formData.append('content', content);
      formData.append('document_type', documentType);
      formData.append('department', department);
      formData.append('document_id', documentId || '');
      formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

      fetch('{{ route("legal.documents.submit_review") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification(data.message, 'success');
          setTimeout(() => {
            window.location.href = '{{ route("legal.legal_documents") }}';
          }, 2000);
        } else {
          showNotification('Error submitting document: ' + (data.message || 'Unknown error'), 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('Error submitting document', 'error');
      });
    }

    function exportDocument(format) {
      if (!documentId) {
        showNotification('Please save the document first before exporting', 'error');
        return;
      }

      window.open(`{{ route("legal.documents.export", ":id") }}?format=${format}`.replace(':id', documentId), '_blank');
    }

    // Notification function
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.className = `alert alert-${type === 'error' ? 'error' : type === 'success' ? 'success' : 'info'} fixed top-4 right-4 z-50 max-w-sm`;
      notification.innerHTML = `
        <i data-lucide="${type === 'error' ? 'alert-circle' : type === 'success' ? 'check-circle' : 'info'}" class="w-5 h-5"></i>
        <span>${message}</span>
      `;
      
      document.body.appendChild(notification);
      
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
      
      setTimeout(() => {
        notification.remove();
      }, 5000);
    }

    // Warn before leaving if there are unsaved changes
    window.addEventListener('beforeunload', function(e) {
      if (isDirty) {
        e.preventDefault();
        e.returnValue = '';
      }
    });

    // Clean up on page unload
    window.addEventListener('unload', function() {
      if (autoSaveInterval) {
        clearInterval(autoSaveInterval);
      }
    });
  </script>

  @include('partials.soliera_js')
</body>
</html>
