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
          <div class="bg-gradient-to-r from-blue-50 to-indigo-100 rounded-lg p-6 border border-blue-200">
            <div class="flex items-center justify-between">
              <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">
                  <i data-lucide="file-text" class="w-8 h-8 inline-block mr-3 text-blue-600"></i>
                  Legal Document Drafting Workspace
                </h1>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Editor Container -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200">
          <!-- Editor Header -->
          <div class="bg-gradient-to-r from-gray-50 to-blue-50 border-b border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                  <div class="p-2 bg-blue-100 rounded-lg">
                    <i data-lucide="edit-3" class="w-6 h-6 text-blue-600"></i>
                  </div>
                  <div>
                    <h3 class="text-xl font-semibold text-gray-800">Document Editor</h3>
                    <p class="text-sm text-gray-500">Professional legal document creation</p>
                  </div>
                </div>
                @if($document)
                  <span class="badge badge-info badge-lg">
                    <i data-lucide="file-edit" class="w-4 h-4 mr-1"></i>
                    Editing: {{ $document->title }}
                  </span>
                @else
                  <span class="badge badge-primary badge-lg">
                    <i data-lucide="file-plus" class="w-4 h-4 mr-1"></i>
                    New Document
                  </span>
                @endif
              </div>
              <div class="flex items-center gap-3">
                <button onclick="loadTemplate()" class="btn btn-primary btn-sm">
                  <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                  Load Template
                </button>
                <button onclick="startBlank()" class="btn btn-outline btn-sm">
                  <i data-lucide="file-plus" class="w-4 h-4 mr-2"></i>
                  Start Blank
                </button>
              </div>
            </div>

            <!-- Document Properties -->
            <div class="bg-white rounded-lg p-4 border border-gray-200">
              <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <i data-lucide="settings" class="w-4 h-4"></i>
                Document Properties
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-medium text-gray-700">Document Title</span>
                  </label>
                  <input type="text" id="documentTitle" class="input input-bordered w-full focus:ring-2 focus:ring-blue-500" 
                         value="{{ $document->title ?? '' }}" placeholder="Enter document title...">
                </div>
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-medium text-gray-700">Document Type</span>
                  </label>
                  <select id="documentType" class="select select-bordered w-full focus:ring-2 focus:ring-blue-500">
                    <option value="contract" {{ ($document->category ?? '') == 'contract' ? 'selected' : '' }}>Contract</option>
                    <option value="policy" {{ ($document->category ?? '') == 'policy' ? 'selected' : '' }}>Policy</option>
                    <option value="agreement" {{ ($document->category ?? '') == 'agreement' ? 'selected' : '' }}>Agreement</option>
                    <option value="notice" {{ ($document->category ?? '') == 'notice' ? 'selected' : '' }}>Notice</option>
                    <option value="memorandum" {{ ($document->category ?? '') == 'memorandum' ? 'selected' : '' }}>Memorandum</option>
                    <option value="license" {{ ($document->category ?? '') == 'license' ? 'selected' : '' }}>License</option>
                    <option value="subpoena" {{ ($document->category ?? '') == 'subpoena' ? 'selected' : '' }}>Subpoena</option>
                    <option value="affidavit" {{ ($document->category ?? '') == 'affidavit' ? 'selected' : '' }}>Affidavit</option>
                    <option value="cease and desist" {{ ($document->category ?? '') == 'cease and desist' ? 'selected' : '' }}>Cease & Desist</option>
                    <option value="legal brief" {{ ($document->category ?? '') == 'legal brief' ? 'selected' : '' }}>Legal Brief</option>
                    <option value="financial" {{ ($document->category ?? '') == 'financial' ? 'selected' : '' }}>Financial</option>
                    <option value="compliance" {{ ($document->category ?? '') == 'compliance' ? 'selected' : '' }}>Compliance</option>
                    <option value="report" {{ ($document->category ?? '') == 'report' ? 'selected' : '' }}>Report</option>
                    <option value="general" {{ ($document->category ?? '') == 'general' ? 'selected' : '' }}>General</option>
                  </select>
                </div>
                <div class="form-control">
                  <label class="label">
                    <span class="label-text font-medium text-gray-700">Department</span>
                  </label>
                  <select id="documentDepartment" class="select select-bordered w-full focus:ring-2 focus:ring-blue-500">
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
          </div>

          <!-- Editor Content -->
          <div class="p-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
              <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                  <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i data-lucide="type" class="w-4 h-4"></i>
                    Rich Text Editor
                  </h4>
                  <div class="flex items-center gap-4 text-xs text-gray-500">
                    <span id="wordCount">Words: 0</span>
                    <span id="charCount">Characters: 0</span>
                    <span class="text-green-600" id="lastSaved">Ready</span>
                  </div>
                </div>
              </div>
              <!-- Quill Editor -->
              <div id="editor" style="height: 500px;"></div>
            </div>
          </div>

          <!-- Editor Footer -->
          <div class="bg-gradient-to-r from-gray-50 to-blue-50 border-t border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-6 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                  <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i>
                  <span id="wordCount">Words: 0</span>
                </div>
                <div class="flex items-center gap-2">
                  <i data-lucide="type" class="w-4 h-4 text-green-500"></i>
                  <span id="charCount">Characters: 0</span>
                </div>
                <div class="flex items-center gap-2">
                  <i data-lucide="clock" class="w-4 h-4 text-orange-500"></i>
                  <span id="lastSaved">Ready</span>
                </div>
                <div class="flex items-center gap-2">
                  <i data-lucide="shield-check" class="w-4 h-4 text-green-500"></i>
                  <span class="text-green-600">Auto-save: ON</span>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <button onclick="saveDocument()" class="btn btn-primary btn-sm">
                  <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                  Save Document
                </button>
                <div class="dropdown dropdown-end">
                  <button tabindex="0" class="btn btn-outline btn-sm">
                    <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                    Export
                    <i data-lucide="chevron-down" class="w-4 h-4 ml-1"></i>
                  </button>
                  <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-40">
                    <li><a onclick="exportDocument('pdf')" class="flex items-center gap-2">
                      <i data-lucide="file-text" class="w-4 h-4"></i>
                      Export as PDF
                    </a></li>
                    <li><a onclick="exportDocument('word')" class="flex items-center gap-2">
                      <i data-lucide="file-text" class="w-4 h-4"></i>
                      Export as Word
                    </a></li>
                  </ul>
                </div>
                <a href="{{ route('legal.legal_documents') }}" class="btn btn-ghost btn-sm">
                  <i data-lucide="x" class="w-4 h-4 mr-2"></i>
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

  <!-- E‑Signature Modal -->
  <div id="esignModal" class="modal">
    <div class="modal-box w-11/12 max-w-lg">
      <h3 class="font-bold text-lg mb-4">Send for E‑Signature</h3>
      <div class="grid grid-cols-1 gap-3">
        <div>
          <label class="label"><span class="label-text">Hotel Signer Name</span></label>
          <input id="hotelSignerName" type="text" class="input input-bordered w-full" placeholder="e.g. Juan Dela Cruz" />
        </div>
        <div>
          <label class="label"><span class="label-text">Hotel Signer Email</span></label>
          <input id="hotelSignerEmail" type="email" class="input input-bordered w-full" placeholder="e.g. juan@example.com" />
        </div>
        <div>
          <label class="label"><span class="label-text">Vendor Signer Name</span></label>
          <input id="vendorSignerName" type="text" class="input input-bordered w-full" placeholder="e.g. Maria Santos" />
        </div>
        <div>
          <label class="label"><span class="label-text">Vendor Signer Email</span></label>
          <input id="vendorSignerEmail" type="email" class="input input-bordered w-full" placeholder="e.g. maria@example.com" />
        </div>
      </div>
      <div class="modal-action">
        <button class="btn btn-outline" onclick="closeESignModal()">Cancel</button>
        <button class="btn btn-primary" onclick="sendForESign()">Send</button>
      </div>
    </div>
  </div>

  <!-- Pen Signature Modal -->
  <div id="penSignModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <h3 class="font-bold text-lg mb-4">Draw Your Signature</h3>
      <div class="border rounded-lg p-3 bg-gray-50">
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center gap-2">
            <label class="text-sm">Pen Color</label>
            <input id="penColor" type="color" value="#000000" class="w-8 h-8 border rounded">
            <label class="text-sm ml-4">Thickness</label>
            <input id="penSize" type="range" min="1" max="8" value="3" class="range range-xs w-40">
          </div>
          <div class="flex items-center gap-2">
            <button class="btn btn-outline btn-sm" onclick="clearPenCanvas()"><i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>Clear</button>
          </div>
        </div>
        <div class="bg-white border rounded-lg overflow-hidden">
          <canvas id="penCanvas" width="900" height="220" style="touch-action:none; display:block; width:100%; height:220px; cursor: crosshair;"></canvas>
        </div>
      </div>
      <div class="modal-action">
        <button class="btn btn-outline" onclick="closePenSignModal()">Cancel</button>
        <button class="btn btn-primary" onclick="insertPenSignature()">Insert to Document</button>
      </div>
    </div>
  </div>

  <script>
    let quill;
    let documentId = {{ $document->id ?? 'null' }};
    let autoSaveInterval;
    let isDirty = false;
    // Expose templates to client for robust loading via URL param
    const TEMPLATES = @json($templates ?? []);

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
        ['pen'], // custom pen button
        ['clean']
      ];

      quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
          toolbar: toolbarOptions
        },
        placeholder: 'Start typing your legal document...'
      });

      // Hook custom pen button to open signature modal
      const penBtn = document.querySelector('.ql-pen');
      if (penBtn) {
        penBtn.title = 'Sign with Pen';
        penBtn.innerHTML = '🖊';
        penBtn.style.fontSize = '14px';
        penBtn.addEventListener('click', openPenSignModal);
      }

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

      // Robust URL-param based template loader (fallback if server-side condition missed)
      try {
        const params = new URLSearchParams(window.location.search);
        const key = params.get('template');
        if (key && typeof key === 'string' && TEMPLATES[key]) {
          const tpl = TEMPLATES[key];
          loadTemplateContent(key, tpl.title, tpl.content);
        }
      } catch (e) {}
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
        
        // Template loaded successfully
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

    function saveDocument() {
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
          showNotification('Document saved successfully!', 'success');
          updateLastSaved('Saved at ' + new Date().toLocaleTimeString());
          setTimeout(() => {
            window.location.href = '{{ route("legal.legal_documents") }}';
          }, 1500);
        } else {
          showNotification('Error saving document: ' + (data.message || 'Unknown error'), 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving document', 'error');
      });
    }

    function exportDocument(format) {
      if (!documentId) {
        showNotification('Please save the document first before exporting', 'error');
        return;
      }

      window.open(`{{ route("legal.documents.export", ":id") }}?format=${format}`.replace(':id', documentId), '_blank');
    }

    // Insert a reusable signature block at the end of the document
    function insertSignatureBlock() {
      const today = new Date().toISOString().slice(0, 10);
      const block = `
        <div style="page-break-inside: avoid; margin-top: 32px;">
          <p><strong>IN WITNESS WHEREOF</strong>, the parties have executed this document on <u>${today}</u>.</p>
          <table style="width:100%; margin-top:24px; font-size:12pt;">
            <tr>
              <td style="width:50%; vertical-align:top; padding-right:16px;">
                <div style="margin-bottom:48px;">
                  <div style="border-top:1px solid #000; width:260px; margin-top:40px;"></div>
                  <div><strong>Authorized Signatory</strong></div>
                  <div>For: <strong>{{ '{' }}{HotelLegalName}{{ '}' }}</strong></div>
                  <div>Name: __________________________</div>
                  <div>Title: __________________________</div>
                  <div>Date: __________________________</div>
                </div>
              </td>
              <td style="width:50%; vertical-align:top; padding-left:16px;">
                <div style="margin-bottom:48px;">
                  <div style="border-top:1px solid #000; width:260px; margin-top:40px;"></div>
                  <div><strong>Authorized Signatory</strong></div>
                  <div>For: <strong>{{ '{' }}{ServiceProviderLegalName}{{ '}' }}</strong></div>
                  <div>Name: __________________________</div>
                  <div>Title: __________________________</div>
                  <div>Date: __________________________</div>
                </div>
              </td>
            </tr>
          </table>
        </div>`;

      const range = quill.getSelection(true);
      // Move cursor to end then paste
      quill.setSelection(quill.getLength()-1, 0);
      quill.clipboard.dangerouslyPasteHTML(quill.getLength()-1, block);
      isDirty = true;
      updateLastSaved('Signature block inserted');
    }

    function openESignModal() {
      if (!documentId) {
        showNotification('Save or approve the document first', 'error');
        return;
      }
      document.getElementById('esignModal').classList.add('modal-open');
    }
    function closeESignModal() {
      document.getElementById('esignModal').classList.remove('modal-open');
    }
    function sendForESign() {
      const hotelName = document.getElementById('hotelSignerName').value.trim();
      const hotelEmail = document.getElementById('hotelSignerEmail').value.trim();
      const vendorName = document.getElementById('vendorSignerName').value.trim();
      const vendorEmail = document.getElementById('vendorSignerEmail').value.trim();
      if (!hotelName || !hotelEmail || !vendorName || !vendorEmail) {
        showNotification('Please provide all signer details', 'error');
        return;
      }
      const formData = new FormData();
      formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
      formData.append('hotel_signer_name', hotelName);
      formData.append('hotel_signer_email', hotelEmail);
      formData.append('vendor_signer_name', vendorName);
      formData.append('vendor_signer_email', vendorEmail);

      fetch(`{{ url('/legal/documents') }}/${documentId}/send-esign`, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          showNotification('E‑signature request sent', 'success');
          closeESignModal();
        } else {
          showNotification(d.message || 'Failed to send for e‑signature', 'error');
        }
      })
      .catch(() => showNotification('Failed to send for e‑signature', 'error'));
    }

    // Pen signature functions
    let penCanvas, penCtx, drawing = false, lastX = 0, lastY = 0;
    function openPenSignModal(){
      const modal = document.getElementById('penSignModal');
      modal.classList.add('modal-open');
      setTimeout(() => initPenCanvas(), 0);
    }
    function closePenSignModal(){
      document.getElementById('penSignModal').classList.remove('modal-open');
    }
    function initPenCanvas(){
      penCanvas = document.getElementById('penCanvas');
      if (!penCanvas) return;
      penCtx = penCanvas.getContext('2d');
      penCtx.lineCap = 'round';
      penCtx.lineJoin = 'round';
      // Mouse events
      penCanvas.onmousedown = (e)=>{ drawing = true; [lastX,lastY]=getPos(e); };
      penCanvas.onmousemove = (e)=>{ if(!drawing) return; drawLine(e); };
      penCanvas.onmouseup = ()=> drawing=false;
      penCanvas.onmouseleave = ()=> drawing=false;
      // Touch events
      penCanvas.addEventListener('touchstart', (e)=>{ e.preventDefault(); drawing=true; [lastX,lastY]=getPos(e.touches[0]); });
      penCanvas.addEventListener('touchmove', (e)=>{ e.preventDefault(); if(!drawing) return; drawLine(e.touches[0]); });
      penCanvas.addEventListener('touchend', ()=> drawing=false);
    }
    function getPos(e){
      const rect = penCanvas.getBoundingClientRect();
      return [ (e.clientX-rect.left)* (penCanvas.width/rect.width), (e.clientY-rect.top)* (penCanvas.height/rect.height) ];
    }
    function drawLine(e){
      const [x,y]=getPos(e);
      penCtx.strokeStyle = document.getElementById('penColor').value;
      penCtx.lineWidth = document.getElementById('penSize').value;
      penCtx.beginPath();
      penCtx.moveTo(lastX,lastY);
      penCtx.lineTo(x,y);
      penCtx.stroke();
      [lastX,lastY]=[x,y];
    }
    function clearPenCanvas(){
      if (!penCtx) return; penCtx.clearRect(0,0,penCanvas.width, penCanvas.height);
    }
    function insertPenSignature(){
      if (!penCanvas) return;
      const dataUrl = penCanvas.toDataURL('image/png');
      const html = `<p><img src="${dataUrl}" style="max-width:300px; height:auto;" /></p>`;
      quill.clipboard.dangerouslyPasteHTML(quill.getLength()-1, html);
      isDirty = true;
      updateLastSaved('Signature inserted');
      closePenSignModal();
    }

    // Notification function
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.className = `alert alert-${type === 'error' ? 'error' : type === 'success' ? 'success' : 'info'} fixed bottom-4 right-4 z-50 max-w-sm`;
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


    // Generate proper legal templates
    function generateLegalTemplate(documentType, title) {
      const currentDate = new Date().toLocaleDateString();
      const companyName = "SOLIERA HOTEL";
      
      switch(documentType) {
        case 'contract':
        case 'employment contract':
          return generateEmploymentContractTemplate(title, currentDate, companyName);
        case 'policy':
        case 'hr policy':
          return generatePolicyTemplate(title, currentDate, companyName);
        case 'agreement':
        case 'service agreement':
          return generateAgreementTemplate(title, currentDate, companyName);
        case 'notice':
        case 'legal notice':
          return generateNoticeTemplate(title, currentDate, companyName);
        case 'memorandum':
        case 'memo':
          return generateMemorandumTemplate(title, currentDate, companyName);
        case 'license':
        case 'permit':
          return generateLicenseTemplate(title, currentDate, companyName);
        case 'subpoena':
          return generateSubpoenaTemplate(title, currentDate, companyName);
        case 'affidavit':
          return generateAffidavitTemplate(title, currentDate, companyName);
        case 'cease and desist':
        case 'cease desist':
          return generateCeaseDesistTemplate(title, currentDate, companyName);
        case 'legal brief':
        case 'brief':
          return generateLegalBriefTemplate(title, currentDate, companyName);
        case 'financial':
        case 'financial document':
          return generateFinancialTemplate(title, currentDate, companyName);
        case 'compliance':
        case 'compliance document':
          return generateComplianceTemplate(title, currentDate, companyName);
        case 'report':
        case 'legal report':
          return generateReportTemplate(title, currentDate, companyName);
        default:
          return generateGeneralTemplate(title, currentDate, companyName);
      }
    }

    // Employment Contract Template
    function generateEmploymentContractTemplate(title, date, company) {
      return `${title.toUpperCase()}

${company}
${date}

EMPLOYMENT CONTRACT

This Employment Contract ("Agreement") is entered into on ${date} between ${company} ("Company") and [EMPLOYEE_NAME] ("Employee").

1. POSITION AND DUTIES
   Employee shall serve as [POSITION_TITLE] and shall perform all duties and responsibilities associated with this position as assigned by the Company.

2. COMPENSATION
   Employee shall receive a base salary of $[SALARY_AMOUNT] per [PAY_PERIOD], payable in accordance with the Company's standard payroll practices.

3. WORK SCHEDULE
   Employee's regular work schedule shall be [WORK_HOURS] per week, Monday through Friday, from [START_TIME] to [END_TIME].

4. BENEFITS
   Employee shall be entitled to participate in the Company's benefit programs, including but not limited to:
   - Health insurance
   - Dental insurance
   - Retirement plan
   - Paid time off

5. TERMINATION
   This Agreement may be terminated by either party with [NOTICE_PERIOD] written notice, or immediately for cause.

6. CONFIDENTIALITY
   Employee agrees to maintain the confidentiality of all Company information and trade secrets.

7. NON-COMPETE
   Employee agrees not to work for competing businesses within [RESTRICTION_PERIOD] of termination.

8. GOVERNING LAW
   This Agreement shall be governed by the laws of [STATE/COUNTRY].

IN WITNESS WHEREOF, the parties have executed this Agreement as of the date first written above.

COMPANY:                           EMPLOYEE:
_________________________          _________________________
[COMPANY_REPRESENTATIVE]           [EMPLOYEE_NAME]
Title: [TITLE]                     Date: _______________
Date: _______________`;
    }

    // Policy Template
    function generatePolicyTemplate(title, date, company) {
      return `${title.toUpperCase()}

${company}
Effective Date: ${date}

POLICY STATEMENT

1. PURPOSE
   This policy establishes guidelines for [POLICY_SUBJECT] to ensure compliance with applicable laws and regulations.

2. SCOPE
   This policy applies to all employees, contractors, and third parties associated with ${company}.

3. POLICY STATEMENT
   [DETAILED_POLICY_CONTENT]

4. PROCEDURES
   a. [PROCEDURE_1]
   b. [PROCEDURE_2]
   c. [PROCEDURE_3]

5. COMPLIANCE
   Violations of this policy may result in disciplinary action, up to and including termination.

6. REVIEW
   This policy shall be reviewed annually and updated as necessary.

APPROVED BY:
_________________________
[APPROVER_NAME]
[APPROVER_TITLE]
Date: _______________`;
    }

    // Agreement Template
    function generateAgreementTemplate(title, date, company) {
      return `${title.toUpperCase()}

AGREEMENT

This Agreement is made and entered into on ${date} between ${company} ("Party A") and [PARTY_B_NAME] ("Party B").

1. RECITALS
   WHEREAS, Party A desires to [PURPOSE_A];
   WHEREAS, Party B desires to [PURPOSE_B];
   NOW, THEREFORE, the parties agree as follows:

2. TERMS AND CONDITIONS
   a. [TERM_1]
   b. [TERM_2]
   c. [TERM_3]

3. PAYMENT TERMS
   Payment shall be made as follows: [PAYMENT_DETAILS]

4. TERM
   This Agreement shall commence on [START_DATE] and continue until [END_DATE].

5. TERMINATION
   Either party may terminate this Agreement with [NOTICE_PERIOD] written notice.

6. GOVERNING LAW
   This Agreement shall be governed by the laws of [JURISDICTION].

IN WITNESS WHEREOF, the parties have executed this Agreement.

PARTY A:                          PARTY B:
_________________________          _________________________
[PARTY_A_REPRESENTATIVE]          [PARTY_B_REPRESENTATIVE]
Title: [TITLE_A]                  Title: [TITLE_B]
Date: _______________             Date: _______________`;
    }

    // Notice Template
    function generateNoticeTemplate(title, date, company) {
      return `${title.toUpperCase()}

${company}
Date: ${date}

NOTICE

TO: [RECIPIENT_NAME]
FROM: [SENDER_NAME]
SUBJECT: [NOTICE_SUBJECT]

This notice serves to inform you that [NOTICE_CONTENT].

Please be advised that [IMPORTANT_INFORMATION].

If you have any questions regarding this notice, please contact [CONTACT_PERSON] at [CONTACT_INFO].

Sincerely,
[SENDER_NAME]
[SENDER_TITLE]
${company}`;
    }

    // Memorandum Template
    function generateMemorandumTemplate(title, date, company) {
      return `${title.toUpperCase()}

MEMORANDUM

TO: [RECIPIENT_NAME]
FROM: [SENDER_NAME]
DATE: ${date}
SUBJECT: [MEMORANDUM_SUBJECT]

1. BACKGROUND
   [BACKGROUND_INFORMATION]

2. DISCUSSION
   [DISCUSSION_POINTS]

3. RECOMMENDATION
   [RECOMMENDED_ACTION]

4. NEXT STEPS
   [FOLLOW_UP_ACTIONS]

Please acknowledge receipt of this memorandum.

[SENDER_NAME]
[SENDER_TITLE]
${company}`;
    }

    // License Template
    function generateLicenseTemplate(title, date, company) {
      return `${title.toUpperCase()}

LICENSE AGREEMENT

This License Agreement ("Agreement") is entered into on ${date} between ${company} ("Licensor") and [LICENSEE_NAME] ("Licensee").

1. GRANT OF LICENSE
   Licensor hereby grants to Licensee a [TYPE_OF_LICENSE] license to [LICENSED_ACTIVITY].

2. TERM
   This license shall commence on [START_DATE] and continue until [END_DATE] unless terminated earlier.

3. RESTRICTIONS
   Licensee shall not:
   a. [RESTRICTION_1]
   b. [RESTRICTION_2]
   c. [RESTRICTION_3]

4. FEES
   Licensee shall pay Licensor [FEE_AMOUNT] on [PAYMENT_SCHEDULE].

5. TERMINATION
   This Agreement may be terminated by either party with [NOTICE_PERIOD] written notice.

6. GOVERNING LAW
   This Agreement shall be governed by the laws of [JURISDICTION].

LICENSOR:                        LICENSEE:
_________________________          _________________________
[LICENSOR_REPRESENTATIVE]         [LICENSEE_REPRESENTATIVE]
Title: [TITLE]                    Title: [TITLE]
Date: _______________             Date: _______________`;
    }

    // Subpoena Template
    function generateSubpoenaTemplate(title, date, company) {
      return `${title.toUpperCase()}

SUBPOENA

TO: [RECIPIENT_NAME]
ADDRESS: [RECIPIENT_ADDRESS]

YOU ARE HEREBY COMMANDED to appear before [COURT_NAME] on [APPEARANCE_DATE] at [APPEARANCE_TIME] to testify in the matter of [CASE_TITLE].

YOU ARE FURTHER COMMANDED to bring with you the following documents:
[LIST_OF_DOCUMENTS]

FAILURE TO COMPLY with this subpoena may result in contempt of court proceedings.

DATED: ${date}

[SIGNATURE]
[COURT_CLERK_NAME]
Court Clerk
[COURT_NAME]`;
    }

    // Affidavit Template
    function generateAffidavitTemplate(title, date, company) {
      return `${title.toUpperCase()}

AFFIDAVIT

I, [AFFIANT_NAME], being duly sworn, depose and say:

1. I am [AFFIANT_DESCRIPTION] and have personal knowledge of the facts set forth herein.

2. [FACT_1]

3. [FACT_2]

4. [FACT_3]

5. The foregoing statements are true and correct to the best of my knowledge, information, and belief.

FURTHER AFFIANT SAYETH NOT.

[AFFIANT_NAME]
Date: ${date}

SUBSCRIBED AND SWORN to before me this [DAY] day of [MONTH], [YEAR].

[NOTARY_NAME]
Notary Public
My Commission Expires: [EXPIRATION_DATE]`;
    }

    // Cease and Desist Template
    function generateCeaseDesistTemplate(title, date, company) {
      return `${title.toUpperCase()}

CEASE AND DESIST NOTICE

TO: [RECIPIENT_NAME]
FROM: ${company}
DATE: ${date}

Dear [RECIPIENT_NAME]:

We are writing to demand that you immediately CEASE AND DESIST from the following activities:

[VIOLATION_DESCRIPTION]

This conduct constitutes [LEGAL_VIOLATION] and is causing irreparable harm to our client.

DEMAND FOR IMMEDIATE ACTION:
1. Immediately cease all [VIOLATION_ACTIVITY]
2. Provide written confirmation of compliance within [TIME_FRAME]
3. [ADDITIONAL_DEMANDS]

LEGAL CONSEQUENCES:
Failure to comply with this demand will result in immediate legal action seeking:
- Injunctive relief
- Monetary damages
- Attorney's fees and costs
- Any other relief available under law

This letter is without prejudice to any other rights or remedies available to our client.

Sincerely,

[ATTORNEY_NAME]
[LAW_FIRM_NAME]
[CONTACT_INFORMATION]`;
    }

    // Legal Brief Template
    function generateLegalBriefTemplate(title, date, company) {
      return `${title.toUpperCase()}

LEGAL BRIEF

IN THE MATTER OF: [CASE_TITLE]
COURT: [COURT_NAME]
CASE NO: [CASE_NUMBER]
DATE: ${date}

TABLE OF CONTENTS
I. STATEMENT OF FACTS
II. ISSUES PRESENTED
III. ARGUMENT
IV. CONCLUSION

I. STATEMENT OF FACTS
[FACTUAL_BACKGROUND]

II. ISSUES PRESENTED
1. [ISSUE_1]
2. [ISSUE_2]
3. [ISSUE_3]

III. ARGUMENT
A. [ARGUMENT_1]
   [SUPPORTING_EVIDENCE_1]

B. [ARGUMENT_2]
   [SUPPORTING_EVIDENCE_2]

C. [ARGUMENT_3]
   [SUPPORTING_EVIDENCE_3]

IV. CONCLUSION
Based on the foregoing, [CONCLUSION_STATEMENT].

Respectfully submitted,

[ATTORNEY_NAME]
[LAW_FIRM_NAME]
[CONTACT_INFORMATION]`;
    }

    // Financial Document Template
    function generateFinancialTemplate(title, date, company) {
      return `${title.toUpperCase()}

FINANCIAL STATEMENT

${company}
As of ${date}

ASSETS
Current Assets:
- Cash and Cash Equivalents: $[CASH_AMOUNT]
- Accounts Receivable: $[AR_AMOUNT]
- Inventory: $[INVENTORY_AMOUNT]
- Other Current Assets: $[OTHER_CURRENT_AMOUNT]

Fixed Assets:
- Property, Plant & Equipment: $[PPE_AMOUNT]
- Less: Accumulated Depreciation: $[DEPRECIATION_AMOUNT]
- Net Fixed Assets: $[NET_PPE_AMOUNT]

TOTAL ASSETS: $[TOTAL_ASSETS]

LIABILITIES
Current Liabilities:
- Accounts Payable: $[AP_AMOUNT]
- Accrued Expenses: $[ACCRUED_AMOUNT]
- Short-term Debt: $[ST_DEBT_AMOUNT]

Long-term Liabilities:
- Long-term Debt: $[LT_DEBT_AMOUNT]
- Other Long-term Liabilities: $[OTHER_LT_AMOUNT]

TOTAL LIABILITIES: $[TOTAL_LIABILITIES]

EQUITY
- Share Capital: $[SHARE_CAPITAL]
- Retained Earnings: $[RETAINED_EARNINGS]
- Other Equity: $[OTHER_EQUITY]

TOTAL EQUITY: $[TOTAL_EQUITY]

TOTAL LIABILITIES AND EQUITY: $[TOTAL_LIABILITIES_EQUITY]

Prepared by: [PREPARER_NAME]
Date: ${date}`;
    }

    // Compliance Document Template
    function generateComplianceTemplate(title, date, company) {
      return `${title.toUpperCase()}

COMPLIANCE CERTIFICATE

${company}
Date: ${date}

I, [CERTIFIER_NAME], [TITLE] of ${company}, hereby certify that:

1. COMPLIANCE STATUS
   The company is in compliance with all applicable laws and regulations including:
   - [REGULATION_1]
   - [REGULATION_2]
   - [REGULATION_3]

2. INTERNAL CONTROLS
   The company maintains adequate internal controls to ensure compliance with:
   - Financial reporting requirements
   - Operational procedures
   - Risk management protocols

3. AUDIT FINDINGS
   The most recent audit conducted on [AUDIT_DATE] found:
   - [AUDIT_FINDING_1]
   - [AUDIT_FINDING_2]

4. REMEDIATION ACTIONS
   Any identified deficiencies have been addressed as follows:
   - [REMEDIATION_ACTION_1]
   - [REMEDIATION_ACTION_2]

5. CERTIFICATION
   To the best of my knowledge and belief, the information contained in this certificate is true and accurate.

[CERTIFIER_NAME]
[TITLE]
Date: ${date}`;
    }

    // Report Template
    function generateReportTemplate(title, date, company) {
      return `${title.toUpperCase()}

LEGAL REPORT

${company}
Report Date: ${date}
Prepared by: [AUTHOR_NAME]

EXECUTIVE SUMMARY
[EXECUTIVE_SUMMARY_CONTENT]

1. INTRODUCTION
   [INTRODUCTION_CONTENT]

2. METHODOLOGY
   [METHODOLOGY_DESCRIPTION]

3. FINDINGS
   a. [FINDING_1]
   b. [FINDING_2]
   c. [FINDING_3]

4. ANALYSIS
   [ANALYSIS_CONTENT]

5. RECOMMENDATIONS
   a. [RECOMMENDATION_1]
   b. [RECOMMENDATION_2]
   c. [RECOMMENDATION_3]

6. CONCLUSION
   [CONCLUSION_CONTENT]

7. APPENDICES
   - Appendix A: [APPENDIX_A_DESCRIPTION]
   - Appendix B: [APPENDIX_B_DESCRIPTION]

[AUTHOR_NAME]
[AUTHOR_TITLE]
Date: ${date}`;
    }

    // General Template
    function generateGeneralTemplate(title, date, company) {
      return `${title.toUpperCase()}

${company}
Date: ${date}

DOCUMENT

1. INTRODUCTION
   [INTRODUCTION_CONTENT]

2. MAIN CONTENT
   [MAIN_CONTENT]

3. CONCLUSION
   [CONCLUSION_CONTENT]

APPROVED BY:
_________________________
[APPROVER_NAME]
[APPROVER_TITLE]
Date: _______________`;
    }
  </script>

  @include('partials.soliera_js')
</body>
</html>
