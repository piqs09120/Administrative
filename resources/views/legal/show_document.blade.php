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

        <!-- Page Header with Back Button -->
        <div class="mb-6">
          <div class="flex items-center gap-4">
            <button onclick="window.history.back()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </button>
            <span class="text-sm font-medium text-gray-600">BACK</span>
          </div>
        </div>

        <!-- Document Details Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden max-w-4xl mx-auto">
          <!-- Document Header -->
          <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-8 text-white">
          <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $document->title }}</h1>
                <div class="flex items-center gap-2">
                  @php
                    $statusConfig = [
                      'active' => ['class' => 'bg-green-500 text-white', 'text' => 'Active'],
                      'pending_review' => ['class' => 'bg-yellow-500 text-white', 'text' => 'Pending Review'],
                      'archived' => ['class' => 'bg-gray-500 text-white', 'text' => 'Archived'],
                      'draft' => ['class' => 'bg-blue-500 text-white', 'text' => 'Draft'],
                      'approved' => ['class' => 'bg-green-500 text-white', 'text' => 'Approved'],
                      'declined' => ['class' => 'bg-red-500 text-white', 'text' => 'Declined']
                    ];
                    $status = $document->status ?? 'active';
                    $config = $statusConfig[$status] ?? $statusConfig['active'];
                  @endphp
                  <span class="px-3 py-1 rounded-full text-sm font-medium {{ $config['class'] }}">
                    {{ $config['text'] }}
                  </span>
                </div>
              </div>
              <div class="text-right">
                <div class="flex items-center gap-2 mb-2">
                  <i data-lucide="clock" class="w-4 h-4"></i>
                  <span class="text-sm">Request History</span>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                  <i data-lucide="folder" class="w-6 h-6"></i>
                </div>
            </div>
            </div>
          </div>

          <!-- Document Information -->
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <!-- Left Column -->
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-gray-600 mb-1">Uploaded By</label>
                  <p class="text-gray-800">{{ $document->uploader->employee_name ?? $document->uploader->name ?? 'Unknown' }}</p>
                </div>
                
                <div>
                  <label class="block text-sm font-semibold text-gray-600 mb-1">Category</label>
                  <p class="text-gray-800">{{ ucfirst(str_replace('_', ' ', $document->category ?? 'contract')) }}</p>
                </div>
                  </div>
                  
              <!-- Right Column -->
              <div class="space-y-4">
                  <div>
                  <label class="block text-sm font-semibold text-gray-600 mb-1">File Path</label>
                  <p class="text-gray-800 font-mono text-sm">{{ $document->file_path ?? 'N/A' }}</p>
                  </div>
                  
                  <div>
                  <label class="block text-sm font-semibold text-gray-600 mb-1">Upload Date</label>
                  <p class="text-gray-800">{{ $document->created_at->format('M d, Y H:i') }}</p>
                </div>
              </div>
            </div>
            
            <!-- Last Updated -->
            <div class="mb-8">
              <label class="block text-sm font-semibold text-gray-600 mb-1">Last Updated</label>
              <p class="text-gray-800">{{ $document->updated_at->format('M d, Y H:i') }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200">
              <!-- AI Analysis Button -->
              <button onclick="aiAnalysis({{ $document->id }})" 
                      class="btn btn-primary bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700">
                <i data-lucide="brain" class="w-5 h-5 mr-2"></i>
                AI ANALYSIS
              </button>

              <!-- Edit Button -->
              <button onclick="editDocument({{ $document->id }})" 
                      class="btn btn-outline border-gray-300 hover:bg-gray-50">
                <i data-lucide="edit" class="w-5 h-5 mr-2"></i>
                EDIT
              </button>

              <!-- Approve Button (only for pending documents) -->
              @if($document->status !== 'approved' && $document->status !== 'declined')
              <button onclick="approveDocument({{ $document->id }})" 
                      class="btn btn-success bg-green-600 hover:bg-green-700 border-green-600 hover:border-green-700">
                <i data-lucide="check" class="w-5 h-5 mr-2"></i>
                APPROVE
              </button>
              @endif

              <!-- Decline Button (only for pending documents) -->
              @if($document->status !== 'approved' && $document->status !== 'declined')
              <button onclick="declineDocument({{ $document->id }})" 
                      class="btn btn-error bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700">
                <i data-lucide="x" class="w-5 h-5 mr-2"></i>
                DECLINE
              </button>
              @endif

              <!-- Download Button -->
              <button onclick="downloadDocument({{ $document->id }})" 
                      class="btn btn-outline border-gray-300 hover:bg-gray-50">
                <i data-lucide="download" class="w-5 h-5 mr-2"></i>
                DOWNLOAD
              </button>

              <!-- Archive Button -->
              <button onclick="archiveDocument({{ $document->id }})" 
                      class="btn btn-warning bg-orange-500 hover:bg-orange-600 border-orange-500 hover:border-orange-600">
                <i data-lucide="archive" class="w-5 h-5 mr-2"></i>
                ARCHIVE
              </button>

              <!-- Delete Button (only for administrators) -->
              @if(auth()->user()->role === 'Administrator' || auth()->user()->role === 'Super Admin')
              <button onclick="deleteDocument({{ $document->id }})" 
                      class="btn btn-error bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700">
                <i data-lucide="trash-2" class="w-5 h-5 mr-2"></i>
                DELETE
              </button>
              @endif
            </div>
                </div>
              </div>
              
        <!-- Request History Section (placeholder) -->
        <div class="max-w-4xl mx-auto mt-6">
          <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="text-center py-8">
              <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="folder" class="w-8 h-8 text-gray-400"></i>
              </div>
              <h3 class="text-lg font-semibold text-gray-600 mb-2">No request history</h3>
              <p class="text-gray-500">This document has no associated request history.</p>
            </div>
          </div>
        </div>
      </main>
          </div>
        </div>
        
  <!-- AI Analysis Modal -->
  <div id="aiAnalysisModal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="brain" class="w-8 h-8 text-purple-500"></i>
          AI Document Analysis
        </h3>
        <button onclick="closeAiAnalysisModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
            </div>
            
      <div id="aiAnalysisContent" class="space-y-6">
        <!-- Loading State -->
        <div id="aiLoading" class="text-center py-12">
          <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-purple-100">
            <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-purple-500"></i>
          </div>
          <h3 class="text-lg font-semibold mb-2 text-gray-700">Analyzing Document...</h3>
          <p class="text-gray-500">AI is processing your document</p>
              </div>
              
        <!-- Analysis Results -->
        <div id="aiResults" class="hidden space-y-6">
          <!-- Document Info -->
          <div class="bg-gray-50 rounded-lg p-4">
            <h4 class="font-semibold text-gray-800 mb-3">Document Summary</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-gray-600">Category:</span>
                <span class="font-semibold text-blue-900 ml-2" id="aiCategory">—</span>
              </div>
              <div>
                <span class="text-gray-600">Confidence:</span>
                <span class="font-semibold ml-2" id="aiConfidence">—</span>
              </div>
            </div>
          </div>

          <!-- AI Analysis Results -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="font-semibold text-blue-800 mb-2 flex items-center gap-2">
              <i data-lucide="file-text" class="w-4 h-4"></i>
              Analysis Summary
            </h4>
            <p class="text-blue-700 text-sm" id="aiSummary">—</p>
        </div>

          <!-- Legal Assessment -->
          <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <h4 class="font-semibold text-orange-800 mb-2 flex items-center gap-2">
              <i data-lucide="scale" class="w-4 h-4"></i>
              Legal Assessment
            </h4>
            <p class="text-orange-700 text-sm" id="aiLegalImplications">—</p>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-4 mt-6 pt-6 border-t border-gray-200">
        <button onclick="closeAiAnalysisModal()" class="btn btn-outline">Close</button>
      </div>
    </div>
  </div>

  <!-- Toast Notification Container -->
  <div id="toastContainer" class="fixed bottom-4 right-4 z-50 space-y-2"></div>
  
  @include('partials.soliera_js')
  
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // AI Analysis function
    function aiAnalysis(documentId) {
      // Show modal
      document.getElementById('aiAnalysisModal').classList.add('modal-open');
      
      // Show loading state
      document.getElementById('aiLoading').classList.remove('hidden');
      document.getElementById('aiResults').classList.add('hidden');
      
      // Perform AI analysis
      fetch(`/document/${documentId}/analyze-ajax`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('AI Analysis response:', data);
        if (data.success) {
          displayAiAnalysisResults(data.analysis);
        } else {
          throw new Error(data.message || 'Analysis failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showError('AI Analysis Failed', error.message || 'Unable to analyze document');
      });
    }
    
    function displayAiAnalysisResults(analysis) {
      // Update category
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
        'general': 'General Document'
      };
      
      const actualCategory = analysis.category || 'general';
      const displayCategory = categoryDisplayNames[actualCategory] || actualCategory.charAt(0).toUpperCase() + actualCategory.slice(1);
      
      document.getElementById('aiCategory').textContent = displayCategory;
      
      // Update confidence
      let confidenceText = '';
      if (analysis.confidence !== undefined && analysis.confidence !== null) {
        if (typeof analysis.confidence === 'number') {
          const percentage = Math.round(analysis.confidence * 100);
          confidenceText = `${percentage}%`;
        } else {
          confidenceText = analysis.confidence;
        }
      } else {
        confidenceText = 'High (90%)';
      }
      
      document.getElementById('aiConfidence').textContent = confidenceText;
      
      // Update summary and legal implications
      document.getElementById('aiSummary').textContent = analysis.summary || 'AI analysis completed successfully.';
      document.getElementById('aiLegalImplications').textContent = analysis.legal_implications || 'No specific legal implications identified.';
      
      // Show results
      document.getElementById('aiLoading').classList.add('hidden');
      document.getElementById('aiResults').classList.remove('hidden');
    }
    
    function closeAiAnalysisModal() {
      document.getElementById('aiAnalysisModal').classList.remove('modal-open');
    }
    
    // Edit document function
    function editDocument(documentId) {
      const button = event.target.closest('button');
      if (!button) return;
      
      // Show loading state
      const originalHTML = button.innerHTML;
      button.innerHTML = '<i class="loading loading-spinner w-5 h-5 mr-2"></i>EDITING...';
      button.disabled = true;
      
      // Check if edit route exists, otherwise redirect to legal documents edit
      const editUrl = `/legal/documents/${documentId}/edit`;
      
      setTimeout(() => {
        window.location.href = editUrl;
      }, 500);
    }
    
    // Download document function
    function downloadDocument(documentId) {
      const button = event.target.closest('button');
      if (!button) return;
      
      // Show loading state
      const originalHTML = button.innerHTML;
      button.innerHTML = '<i class="loading loading-spinner w-5 h-5 mr-2"></i>DOWNLOADING...';
      button.disabled = true;
      
      // Make download request
      fetch(`/legal/documents/${documentId}/download`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(async response => {
        if (response.ok) {
          const contentType = response.headers.get('content-type') || '';
          
          if (contentType.includes('application/json')) {
            const data = await response.json();
            if (data.success && data.download_url) {
              window.location.href = data.download_url;
              showToast('Download started successfully!', 'success');
            } else {
              throw new Error(data.message || 'Download failed');
            }
          } else {
            // Direct file download
            const contentDisposition = response.headers.get('content-disposition');
            if (contentDisposition && contentDisposition.includes('attachment')) {
              const blob = await response.blob();
              const url = window.URL.createObjectURL(blob);
              const a = document.createElement('a');
              a.href = url;
              a.download = contentDisposition.split('filename=')[1]?.replace(/"/g, '') || 'document';
              document.body.appendChild(a);
              a.click();
              window.URL.revokeObjectURL(url);
              document.body.removeChild(a);
              showToast('Document downloaded successfully!', 'success');
            }
          }
        } else {
          throw new Error(`Download failed with status ${response.status}`);
        }
      })
      .catch(error => {
        console.error('Download error:', error);
        showToast('Error downloading document: ' + error.message, 'error');
      })
      .finally(() => {
        // Restore button
        button.innerHTML = originalHTML;
        button.disabled = false;
      });
    }
    
    // Archive document function
    function archiveDocument(documentId) {
      const button = event.target.closest('button');
      if (!button) return;
      
      if (!confirm('Are you sure you want to archive this document? It will be moved to the archived documents section.')) {
        return;
      }
      
      // Show loading state
      const originalHTML = button.innerHTML;
      button.innerHTML = '<i class="loading loading-spinner w-5 h-5 mr-2"></i>ARCHIVING...';
      button.disabled = true;
      
      // Make archive request
      fetch(`/legal/documents/${documentId}/archive`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`Archive failed with status ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          showToast('Document archived successfully!', 'success');
          // Redirect back to documents list after 2 seconds
          setTimeout(() => {
            window.location.href = '{{ route("legal.legal_documents") }}';
          }, 2000);
        } else {
          throw new Error(data.message || 'Archive failed');
        }
      })
      .catch(error => {
        console.error('Archive error:', error);
        showToast('Error archiving document: ' + error.message, 'error');
      })
      .finally(() => {
        // Restore button
        button.innerHTML = originalHTML;
        button.disabled = false;
      });
    }
    
    // Approve document function
    function approveDocument(documentId) {
      // Create approval modal
      const modal = document.createElement('div');
      modal.className = 'modal modal-open';
      modal.innerHTML = `
        <div class="modal-box">
          <h3 class="font-bold text-lg text-green-600">Approve Document</h3>
          <p class="py-4">Are you sure you want to approve this document?</p>
          <div class="form-control">
            <label class="label">
              <span class="label-text">Approval Notes (Optional)</span>
            </label>
            <textarea id="approvalNotes" class="textarea textarea-bordered" placeholder="Add approval notes..."></textarea>
          </div>
          <div class="modal-action">
            <button class="btn" onclick="closeApprovalModal()">Cancel</button>
            <button class="btn btn-success" onclick="confirmApproval(${documentId})">
              <i data-lucide="check" class="w-4 h-4 mr-2"></i>
              Approve
            </button>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      lucide.createIcons();
    }

    function closeApprovalModal() {
      const modal = document.querySelector('.modal');
      if (modal) {
        modal.remove();
      }
    }

    function confirmApproval(documentId) {
      const notes = document.getElementById('approvalNotes').value;
      
      fetch(`/legal/documents/${documentId}/approve-doc`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          notes: notes
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          closeApprovalModal();
          showToast('Document approved successfully!', 'success');
          setTimeout(() => {
            location.reload();
          }, 2000);
        } else {
          showToast('Error: ' + data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while approving the document.', 'error');
      });
    }

    // Decline document function
    function declineDocument(documentId) {
      // Create decline modal
      const modal = document.createElement('div');
      modal.className = 'modal modal-open';
      modal.innerHTML = `
        <div class="modal-box">
          <h3 class="font-bold text-lg text-red-600">Decline Document</h3>
          <p class="py-4">Please provide a reason for declining this document:</p>
          <div class="form-control">
            <label class="label">
              <span class="label-text">Decline Reason *</span>
            </label>
            <textarea id="declineReason" class="textarea textarea-bordered" placeholder="Enter reason for declining..." required></textarea>
          </div>
          <div class="modal-action">
            <button class="btn" onclick="closeDeclineModal()">Cancel</button>
            <button class="btn btn-error" onclick="confirmDecline(${documentId})">
              <i data-lucide="x" class="w-4 h-4 mr-2"></i>
              Decline
            </button>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      lucide.createIcons();
    }

    function closeDeclineModal() {
      const modal = document.querySelector('.modal');
      if (modal) {
        modal.remove();
      }
    }

    function confirmDecline(documentId) {
      const reason = document.getElementById('declineReason').value.trim();
      
      if (!reason) {
        showToast('Please provide a reason for declining the document.', 'error');
        return;
      }
      
      fetch(`/legal/documents/${documentId}/decline-doc`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          reason: reason
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          closeDeclineModal();
          showToast('Document declined successfully!', 'success');
          setTimeout(() => {
            location.reload();
          }, 2000);
        } else {
          showToast('Error: ' + data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while declining the document.', 'error');
      });
    }
    
    // Delete document function
    function deleteDocument(documentId) {
      const button = event.target.closest('button');
      if (!button) return;
      
      if (!confirm('Are you sure you want to delete this legal document? This action cannot be undone and will permanently remove the document from the system.')) {
        return;
      }
      
      // Show loading state
      const originalHTML = button.innerHTML;
      button.innerHTML = '<i class="loading loading-spinner w-5 h-5 mr-2"></i>DELETING...';
      button.disabled = true;
      
      // Make delete request
        fetch(`/legal/documents/${documentId}`, {
          method: 'DELETE',
          headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => {
        if (!response.ok) {
          throw new Error(`Delete failed with status ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          showToast('Document deleted successfully!', 'success');
          // Redirect back to documents list after 2 seconds
          setTimeout(() => {
            window.location.href = '{{ route("legal.legal_documents") }}';
          }, 2000);
          } else {
          throw new Error(data.message || 'Delete failed');
          }
        })
        .catch(error => {
        console.error('Delete error:', error);
        showToast('Error deleting document: ' + error.message, 'error');
      })
      .finally(() => {
        // Restore button
        button.innerHTML = originalHTML;
        button.disabled = false;
      });
    }
    
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
    
    // Error display function
    function showError(title, message) {
      document.getElementById('aiLoading').innerHTML = `
        <div class="text-center py-12">
          <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-red-100">
            <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
          </div>
          <h3 class="text-lg font-semibold mb-2 text-red-700">${title}</h3>
          <p class="text-red-500">${message}</p>
        </div>
      `;
      lucide.createIcons();
    }
    
    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
      const aiAnalysisModal = document.getElementById('aiAnalysisModal');
      if (event.target === aiAnalysisModal) {
        closeAiAnalysisModal();
      }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeAiAnalysisModal();
      }
    });
  </script>
</body>
</html>