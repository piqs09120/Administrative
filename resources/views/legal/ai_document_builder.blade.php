<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>AI Document Builder - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
</head>
<body class="bg-base-100">
  <div class="flex h-screen overflow-hidden">
    @include('partials.sidebarr')
    <div class="flex flex-col flex-1 overflow-hidden">
      @include('partials.navbar')
      <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
        <div class="max-w-6xl mx-auto">
          <!-- Header -->
          <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">AI Document Builder</h1>
            <p class="text-gray-600">Create legal documents with AI-powered suggestions and proper structure</p>
          </div>

          <!-- Document Type Selection -->
          <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
              <i data-lucide="file-plus" class="w-5 h-5 text-blue-600"></i>
              Select Document Type
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <button onclick="selectDocumentType('employment_contract')" 
                      class="document-type-btn p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all text-left">
                <div class="flex items-center gap-3">
                  <i data-lucide="user-check" class="w-8 h-8 text-blue-600"></i>
                  <div>
                    <h3 class="font-semibold">Employment Contract</h3>
                    <p class="text-sm text-gray-600">Legally sound employment agreements</p>
                  </div>
                </div>
              </button>
              
              <button onclick="selectDocumentType('service_contract')" 
                      class="document-type-btn p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all text-left">
                <div class="flex items-center gap-3">
                  <i data-lucide="handshake" class="w-8 h-8 text-green-600"></i>
                  <div>
                    <h3 class="font-semibold">Service Contract</h3>
                    <p class="text-sm text-gray-600">Service agreements and contracts</p>
                  </div>
                </div>
              </button>
              
              <button onclick="selectDocumentType('guest_agreement')" 
                      class="document-type-btn p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all text-left">
                <div class="flex items-center gap-3">
                  <i data-lucide="users" class="w-8 h-8 text-purple-600"></i>
                  <div>
                    <h3 class="font-semibold">Guest Agreement</h3>
                    <p class="text-sm text-gray-600">Visitor and guest access agreements</p>
                  </div>
                </div>
              </button>
              
              <button onclick="selectDocumentType('vendor_agreement')" 
                      class="document-type-btn p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all text-left">
                <div class="flex items-center gap-3">
                  <i data-lucide="truck" class="w-8 h-8 text-orange-600"></i>
                  <div>
                    <h3 class="font-semibold">Vendor Agreement</h3>
                    <p class="text-sm text-gray-600">Supplier and vendor contracts</p>
                  </div>
                </div>
              </button>
              
              <button onclick="selectDocumentType('hr_policy')" 
                      class="document-type-btn p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all text-left">
                <div class="flex items-center gap-3">
                  <i data-lucide="file-text" class="w-8 h-8 text-red-600"></i>
                  <div>
                    <h3 class="font-semibold">HR Policy</h3>
                    <p class="text-sm text-gray-600">Human resources policies</p>
                  </div>
                </div>
              </button>
              
              <button onclick="selectDocumentType('custom')" 
                      class="document-type-btn p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all text-left">
                <div class="flex items-center gap-3">
                  <i data-lucide="edit" class="w-8 h-8 text-gray-600"></i>
                  <div>
                    <h3 class="font-semibold">Custom Document</h3>
                    <p class="text-sm text-gray-600">Create your own document</p>
                  </div>
                </div>
              </button>
            </div>
          </div>

          <!-- Document Builder (Hidden Initially) -->
          <div id="documentBuilder" class="hidden">
            <div class="bg-white rounded-xl shadow-lg p-6">
              <!-- Document Header -->
              <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                  <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i data-lucide="file-edit" class="w-5 h-5 text-blue-600"></i>
                    <span id="documentTypeTitle">Document Builder</span>
                  </h2>
                  <div class="flex gap-2">
                    <button onclick="generateAIContent()" class="btn btn-primary btn-sm">
                      <i data-lucide="sparkles" class="w-4 h-4 mr-2"></i>
                      AI Suggestions
                    </button>
                    <button onclick="previewDocument()" class="btn btn-info btn-sm">
                      <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                      Preview Document
                    </button>
                    <button onclick="saveDocument()" class="btn btn-success btn-sm">
                      <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                      Save Draft
                    </button>
                    <button onclick="submitDocument()" class="btn btn-warning btn-sm">
                      <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                      Submit for Review
                    </button>
                  </div>
                </div>
                
                <!-- Document Metadata -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                  <div>
                    <label class="label">
                      <span class="label-text font-medium">Document Title</span>
                    </label>
                    <input type="text" id="documentTitle" class="input input-bordered w-full" placeholder="Enter document title">
                  </div>
                  <div>
                    <label class="label">
                      <span class="label-text font-medium">Department</span>
                    </label>
                    <select id="documentDepartment" class="select select-bordered w-full">
                      <option value="Legal">Legal</option>
                      <option value="Human Resources">Human Resources</option>
                      <option value="Operations">Operations</option>
                      <option value="Finance">Finance</option>
                      <option value="Administrative">Administrative</option>
                    </select>
                  </div>
                  <div>
                    <label class="label">
                      <span class="label-text font-medium">Priority</span>
                    </label>
                    <select id="documentPriority" class="select select-bordered w-full">
                      <option value="normal">Normal</option>
                      <option value="high">High</option>
                      <option value="urgent">Urgent</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- AI-Generated Document Sections -->
              <div id="documentSections" class="space-y-6">
                <!-- Sections will be dynamically generated here -->
              </div>

              <!-- AI Suggestions Panel -->
              <div id="aiSuggestionsPanel" class="hidden mt-6 bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <h3 class="font-semibold text-purple-800 flex items-center gap-2">
                    <i data-lucide="brain" class="w-5 h-5"></i>
                    AI Suggestions
                  </h3>
                  <button onclick="previewDocument()" class="btn btn-info btn-sm">
                    <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                    View Final Document
                  </button>
                </div>
                <div id="aiSuggestionsContent" class="text-sm text-gray-700">
                  <!-- AI suggestions will be loaded here -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Preview Document Modal -->
  <div id="previewModal" class="modal">
    <div class="modal-box w-11/12 max-w-6xl">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold flex items-center gap-2">
          <i data-lucide="eye" class="w-5 h-5 text-blue-600"></i>
          Document Preview
        </h3>
        <button onclick="closePreview()" class="btn btn-sm btn-circle btn-ghost">✕</button>
      </div>
      
      <div class="bg-gray-50 p-4 rounded-lg mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
          <div>
            <span class="font-medium text-gray-600">Document Title:</span>
            <span id="previewTitle" class="ml-2"></span>
          </div>
          <div>
            <span class="font-medium text-gray-600">Department:</span>
            <span id="previewDepartment" class="ml-2"></span>
          </div>
          <div>
            <span class="font-medium text-gray-600">Priority:</span>
            <span id="previewPriority" class="ml-2"></span>
          </div>
        </div>
      </div>
      
      <div class="bg-white border rounded-lg p-6 max-h-96 overflow-y-auto">
        <div id="previewContent" class="prose max-w-none">
          <!-- Preview content will be inserted here -->
        </div>
      </div>
      
      <div class="modal-action">
        <button onclick="closePreview()" class="btn btn-ghost">Close</button>
        <button onclick="saveDocument()" class="btn btn-success">
          <i data-lucide="save" class="w-4 h-4 mr-2"></i>
          Save Draft
        </button>
        <button onclick="submitDocument()" class="btn btn-warning">
          <i data-lucide="send" class="w-4 h-4 mr-2"></i>
          Submit for Review
        </button>
      </div>
    </div>
  </div>

  <script>
    let currentDocumentType = null;
    let documentSections = {};

    // Document type configurations
    const documentTypes = {
      employment_contract: {
        title: 'Employment Contract Builder',
        sections: [
          { id: 'parties', title: '1. Parties', required: true, aiPrompt: 'Generate parties section for employment contract' },
          { id: 'position', title: '2. Position & Start Date', required: true, aiPrompt: 'Generate position and start date section' },
          { id: 'compensation', title: '3. Compensation', required: true, aiPrompt: 'Generate compensation section with salary, benefits, allowances' },
          { id: 'work_schedule', title: '4. Work Schedule & Location', required: true, aiPrompt: 'Generate work schedule and location section' },
          { id: 'duties', title: '5. Duties & Performance', required: true, aiPrompt: 'Generate duties and performance expectations section' },
          { id: 'benefits', title: '6. Benefits', required: false, aiPrompt: 'Generate benefits section' },
          { id: 'termination', title: '7. Termination', required: true, aiPrompt: 'Generate termination clause section' },
          { id: 'confidentiality', title: '8. Confidentiality', required: false, aiPrompt: 'Generate confidentiality clause' },
          { id: 'signatures', title: '9. Signatures', required: true, aiPrompt: 'Generate signature section' }
        ]
      },
      service_contract: {
        title: 'Service Contract Builder',
        sections: [
          { id: 'parties', title: '1. Parties', required: true, aiPrompt: 'Generate parties section for service contract' },
          { id: 'services', title: '2. Services Description', required: true, aiPrompt: 'Generate services description section' },
          { id: 'payment', title: '3. Payment Terms', required: true, aiPrompt: 'Generate payment terms and billing section' },
          { id: 'performance', title: '4. Performance Standards', required: true, aiPrompt: 'Generate performance standards and SLA section' },
          { id: 'liability', title: '5. Liability & Insurance', required: true, aiPrompt: 'Generate liability and insurance section' },
          { id: 'termination', title: '6. Termination', required: true, aiPrompt: 'Generate termination clause' },
          { id: 'signatures', title: '7. Signatures', required: true, aiPrompt: 'Generate signature section' }
        ]
      },
      guest_agreement: {
        title: 'Guest Agreement Builder',
        sections: [
          { id: 'parties', title: '1. Parties', required: true, aiPrompt: 'Generate parties section for guest agreement' },
          { id: 'accommodation', title: '2. Accommodation Terms', required: true, aiPrompt: 'Generate accommodation terms section' },
          { id: 'responsibilities', title: '3. Guest Responsibilities', required: true, aiPrompt: 'Generate guest responsibilities section' },
          { id: 'facility_rules', title: '4. Facility Rules', required: true, aiPrompt: 'Generate facility rules section' },
          { id: 'liability', title: '5. Liability', required: true, aiPrompt: 'Generate liability section' },
          { id: 'signatures', title: '6. Signatures', required: true, aiPrompt: 'Generate signature section' }
        ]
      },
      vendor_agreement: {
        title: 'Vendor Agreement Builder',
        sections: [
          { id: 'parties', title: '1. Parties', required: true, aiPrompt: 'Generate parties section for vendor agreement' },
          { id: 'supply_terms', title: '2. Supply Terms', required: true, aiPrompt: 'Generate supply terms section' },
          { id: 'pricing', title: '3. Pricing & Payment', required: true, aiPrompt: 'Generate pricing and payment section' },
          { id: 'quality', title: '4. Quality Standards', required: true, aiPrompt: 'Generate quality standards section' },
          { id: 'delivery', title: '5. Delivery Terms', required: true, aiPrompt: 'Generate delivery terms section' },
          { id: 'termination', title: '6. Termination', required: true, aiPrompt: 'Generate termination clause' },
          { id: 'signatures', title: '7. Signatures', required: true, aiPrompt: 'Generate signature section' }
        ]
      },
      hr_policy: {
        title: 'HR Policy Builder',
        sections: [
          { id: 'purpose', title: '1. Purpose', required: true, aiPrompt: 'Generate purpose section for HR policy' },
          { id: 'scope', title: '2. Scope', required: true, aiPrompt: 'Generate scope section' },
          { id: 'policy_statement', title: '3. Policy Statement', required: true, aiPrompt: 'Generate policy statement section' },
          { id: 'procedures', title: '4. Procedures', required: true, aiPrompt: 'Generate procedures section' },
          { id: 'compliance', title: '5. Compliance', required: true, aiPrompt: 'Generate compliance section' },
          { id: 'approval', title: '6. Approval', required: true, aiPrompt: 'Generate approval section' }
        ]
      },
      custom: {
        title: 'Custom Document Builder',
        sections: [
          { id: 'introduction', title: '1. Introduction', required: true, aiPrompt: 'Generate introduction section' },
          { id: 'main_content', title: '2. Main Content', required: true, aiPrompt: 'Generate main content section' },
          { id: 'conclusion', title: '3. Conclusion', required: true, aiPrompt: 'Generate conclusion section' }
        ]
      }
    };

    function selectDocumentType(type) {
      currentDocumentType = type;
      const config = documentTypes[type];
      
      // Update UI
      document.getElementById('documentTypeTitle').textContent = config.title;
      document.getElementById('documentBuilder').classList.remove('hidden');
      
      // Generate document sections
      generateDocumentSections(config.sections);
      
      // Set default title
      document.getElementById('documentTitle').value = config.title.replace(' Builder', '');
      
      // Scroll to builder
      document.getElementById('documentBuilder').scrollIntoView({ behavior: 'smooth' });
    }

    function generateDocumentSections(sections) {
      const container = document.getElementById('documentSections');
      container.innerHTML = '';

      sections.forEach((section, index) => {
        const sectionHtml = `
          <div class="border border-gray-200 rounded-lg p-4" data-section-id="${section.id}">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
                ${section.title}
                ${section.required ? '<span class="text-red-500 text-sm">*</span>' : ''}
              </h3>
              <div class="flex gap-2">
                <button onclick="generateAISection('${section.id}', '${section.aiPrompt}')" 
                        class="btn btn-sm btn-outline btn-primary">
                  <i data-lucide="sparkles" class="w-4 h-4 mr-1"></i>
                  AI Generate
                </button>
                <button onclick="clearSection('${section.id}')" 
                        class="btn btn-sm btn-outline btn-error">
                  <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>
                  Clear
                </button>
              </div>
            </div>
            <textarea id="section_${section.id}" 
                      class="textarea textarea-bordered w-full h-32" 
                      placeholder="Click 'AI Generate' to get AI suggestions for this section..."></textarea>
            <div class="mt-2 text-xs text-gray-500">
              <span id="word_count_${section.id}">0 words</span>
            </div>
          </div>
        `;
        container.innerHTML += sectionHtml;
        
        // Add word count functionality
        document.getElementById(`section_${section.id}`).addEventListener('input', function() {
          const wordCount = this.value.trim().split(/\s+/).filter(word => word.length > 0).length;
          document.getElementById(`word_count_${section.id}`).textContent = `${wordCount} words`;
        });
      });
    }

    async function generateAISection(sectionId, prompt) {
      const textarea = document.getElementById(`section_${sectionId}`);
      const button = event.target.closest('button');
      
      // Show loading state
      button.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-1 animate-spin"></i> Generating...';
      button.disabled = true;
      
      try {
        const response = await fetch('/legal/ai-generate-section', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            section_id: sectionId,
            prompt: prompt,
            document_type: currentDocumentType,
            context: {
              title: document.getElementById('documentTitle').value,
              department: document.getElementById('documentDepartment').value,
              priority: document.getElementById('documentPriority').value
            }
          })
        });
        
        const data = await response.json();
        
        if (data.success) {
          textarea.value = data.content;
          // Trigger word count update
          textarea.dispatchEvent(new Event('input'));
        } else {
          alert('Error generating content: ' + data.message);
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error generating content. Please try again.');
      } finally {
        // Reset button
        button.innerHTML = '<i data-lucide="sparkles" class="w-4 h-4 mr-1"></i> AI Generate';
        button.disabled = false;
      }
    }

    async function generateAIContent() {
      const sections = document.querySelectorAll('[data-section-id]');
      const panel = document.getElementById('aiSuggestionsPanel');
      const content = document.getElementById('aiSuggestionsContent');
      
      panel.classList.remove('hidden');
      content.innerHTML = '<div class="flex items-center gap-2"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Generating AI suggestions...</div>';
      
      try {
        const response = await fetch('/legal/ai-generate-document', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            document_type: currentDocumentType,
            title: document.getElementById('documentTitle').value,
            department: document.getElementById('documentDepartment').value,
            priority: document.getElementById('documentPriority').value,
            sections: Array.from(sections).map(section => ({
              id: section.dataset.sectionId,
              content: document.getElementById(`section_${section.dataset.sectionId}`).value
            }))
          })
        });
        
        const data = await response.json();
        
        if (data.success) {
          content.innerHTML = `
            <div class="space-y-3">
              <div class="p-3 bg-green-100 border border-green-300 rounded">
                <h4 class="font-semibold text-green-800 mb-2">Document Structure Suggestions:</h4>
                <p class="text-sm text-green-700">${data.suggestions.structure || 'No specific structure suggestions.'}</p>
              </div>
              <div class="p-3 bg-blue-100 border border-blue-300 rounded">
                <h4 class="font-semibold text-blue-800 mb-2">Content Recommendations:</h4>
                <p class="text-sm text-blue-700">${data.suggestions.content || 'No specific content suggestions.'}</p>
              </div>
              <div class="p-3 bg-purple-100 border border-purple-300 rounded">
                <h4 class="font-semibold text-purple-800 mb-2">Legal Compliance Notes:</h4>
                <p class="text-sm text-purple-700">${data.suggestions.compliance || 'No specific compliance notes.'}</p>
              </div>
            </div>
          `;
        } else {
          content.innerHTML = `<div class="text-red-600">Error: ${data.message}</div>`;
        }
      } catch (error) {
        console.error('Error:', error);
        content.innerHTML = '<div class="text-red-600">Error generating suggestions. Please try again.</div>';
      }
    }

    function clearSection(sectionId) {
      document.getElementById(`section_${sectionId}`).value = '';
      document.getElementById(`word_count_${sectionId}`).textContent = '0 words';
    }

    async function saveDocument() {
      const documentData = {
        title: document.getElementById('documentTitle').value,
        department: document.getElementById('documentDepartment').value,
        priority: document.getElementById('documentPriority').value,
        type: currentDocumentType,
        sections: {}
      };

      // Collect all section content
      document.querySelectorAll('[data-section-id]').forEach(section => {
        const sectionId = section.dataset.sectionId;
        documentData.sections[sectionId] = document.getElementById(`section_${sectionId}`).value;
      });

      try {
        const response = await fetch('/legal/save-ai-document', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(documentData)
        });
        
        const data = await response.json();
        
        if (data.success) {
          alert('Document saved as draft successfully!');
        } else {
          alert('Error saving document: ' + data.message);
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error saving document. Please try again.');
      }
    }

    async function submitDocument() {
      const documentData = {
        title: document.getElementById('documentTitle').value,
        department: document.getElementById('documentDepartment').value,
        priority: document.getElementById('documentPriority').value,
        type: currentDocumentType,
        sections: {}
      };

      // Collect all section content
      document.querySelectorAll('[data-section-id]').forEach(section => {
        const sectionId = section.dataset.sectionId;
        documentData.sections[sectionId] = document.getElementById(`section_${sectionId}`).value;
      });

      try {
        const response = await fetch('/legal/submit-ai-document', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(documentData)
        });
        
        const data = await response.json();
        
        if (data.success) {
          alert('Document submitted for review successfully!');
          window.location.href = '/legal/documents';
        } else {
          alert('Error submitting document: ' + data.message);
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error submitting document. Please try again.');
      }
    }

    // Preview Document Functions
    function previewDocument() {
      const title = document.getElementById('documentTitle').value || 'Untitled Document';
      const department = document.getElementById('documentDepartment').value || 'Legal';
      const priority = document.getElementById('documentPriority').value || 'Normal';
      
      // Update preview header
      document.getElementById('previewTitle').textContent = title;
      document.getElementById('previewDepartment').textContent = department;
      document.getElementById('previewPriority').textContent = priority;
      
      // Generate preview content
      let previewContent = `<h1>${title}</h1>`;
      previewContent += `<div class="text-sm text-gray-600 mb-6">`;
      previewContent += `<p><strong>Department:</strong> ${department}</p>`;
      previewContent += `<p><strong>Priority:</strong> ${priority}</p>`;
      previewContent += `<p><strong>Generated:</strong> ${new Date().toLocaleDateString()}</p>`;
      previewContent += `</div>`;
      
      // Add all sections
      document.querySelectorAll('[data-section-id]').forEach(section => {
        const sectionId = section.dataset.sectionId;
        const sectionTitle = section.querySelector('h3').textContent;
        const sectionContent = document.getElementById(`section_${sectionId}`).value;
        
        if (sectionContent.trim()) {
          previewContent += `<h2>${sectionTitle}</h2>`;
          previewContent += `<div class="mb-4">${sectionContent.replace(/\n/g, '<br>')}</div>`;
        }
      });
      
      // Update preview content
      document.getElementById('previewContent').innerHTML = previewContent;
      
      // Show modal
      document.getElementById('previewModal').classList.add('modal-open');
    }
    
    function closePreview() {
      document.getElementById('previewModal').classList.remove('modal-open');
    }

    // Initialize Lucide icons
    document.addEventListener('DOMContentLoaded', function() {
      lucide.createIcons();
    });
  </script>
</body>
</html>
