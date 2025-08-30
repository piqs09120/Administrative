<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Archived Documents - Soliera</title>
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
      <main class="flex-1 overflow-y-auto p-8">
        <h1 class="text-3xl font-bold mb-6" style="color: var(--color-charcoal-ink);">Archived Documents</h1>

        <div class="bg-white rounded-xl shadow-md">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-semibold" style="color: var(--color-charcoal-ink);">Archived Documents List</h2>
              <div class="w-full max-w-sm">
                <input id="searchInput" type="text" placeholder="Search documents..." class="input input-bordered w-full" />
              </div>
            </div>
            <div class="overflow-x-auto">
              <table class="table w-full">
                <thead>
                  <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left py-4 px-6 font-semibold text-gray-700">Document Title</th>
                    <th class="text-left py-4 px-6 font-semibold text-gray-700">Department</th>
                    <th class="text-left py-4 px-6 font-semibold text-gray-700">Archived Date</th>
                    <th class="text-left py-4 px-6 font-semibold text-gray-700">Category</th>
                    <th class="text-center py-4 px-6 font-semibold text-gray-700">Status</th>
                    <th class="text-center py-4 px-6 font-semibold text-gray-700">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($documents as $document)
                    <tr data-row>
                      <td class="font-medium">{{ $document->title }}</td>
                      <td>{{ $document->department ?? '—' }}</td>
                      <td>{{ optional($document->updated_at)->format('M d, Y') }}</td>
                      <td>{{ ucfirst(str_replace('_',' ', $document->category ?? 'general')) }}</td>
                      <td>
                        <div class="badge badge-neutral">{{ ucfirst($document->status ?? 'archived') }}</div>
                      </td>
                      <td>
                        <div class="flex gap-2">
                          <button class="btn btn-xs" onclick="unarchiveDocument({{ $document->id }})">Unarchive</button>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center py-8">
                        <div class="flex flex-col items-center gap-2">
                          <i data-lucide="archive" class="w-8 h-8 text-gray-400"></i>
                          <span>No archived documents found</span>
                        </div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Archive Confirmation Modal -->
  <div id="archiveModal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Confirm Archive</h3>
      <p class="py-4">Are you sure you want to archive this document? You can restore it anytime from the Archived Documents section.</p>
      <div class="modal-action">
        <button class="btn btn-ghost" onclick="closeArchiveModal()">Cancel</button>
        <button class="btn btn-warning" onclick="confirmArchive()">Archive Document</button>
      </div>
    </div>
  </div>

  <!-- Unarchive Confirmation Modal -->
  <div id="unarchiveModal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Confirm Unarchive</h3>
      <p class="py-4">Are you sure you want to restore this document? It will be moved back to the active documents list.</p>
      <div class="modal-action">
        <button class="btn btn-ghost" onclick="closeUnarchiveModal()">Cancel</button>
        <button class="btn btn-success" onclick="confirmUnarchive()">Restore Document</button>
      </div>
    </div>
  </div>

  <!-- Permanent Delete Confirmation Modal -->
  <div id="permanentDeleteModal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Confirm Permanent Deletion</h3>
      <p class="py-4 text-red-600 font-semibold">⚠️ WARNING: This action cannot be undone!</p>
      <p class="py-4">Are you absolutely sure you want to permanently delete this archived document? This will remove it from the system completely.</p>
      <div class="modal-action">
        <button class="btn btn-ghost" onclick="closePermanentDeleteModal()">Cancel</button>
        <button class="btn btn-error" onclick="confirmPermanentDelete()">Permanently Delete</button>
      </div>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Modal state variables
    let documentToArchive = null;
    let documentToUnarchive = null;
    let documentToDelete = null;

    // Archive functions
    function archiveDocument(documentId) {
      documentToArchive = documentId;
      document.getElementById('archiveModal').classList.add('modal-open');
    }

    function closeArchiveModal() {
      document.getElementById('archiveModal').classList.remove('modal-open');
      documentToArchive = null;
    }

    function confirmArchive() {
      if (!documentToArchive) return;
      
      fetch(`/document/${documentToArchive}/archive`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message and reload page
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while archiving the document');
      })
      .finally(() => {
        closeArchiveModal();
      });
    }

    // Unarchive functions
    function unarchiveDocument(documentId) {
      documentToUnarchive = documentId;
      document.getElementById('unarchiveModal').classList.add('modal-open');
    }

    function closeUnarchiveModal() {
      document.getElementById('unarchiveModal').classList.remove('modal-open');
      documentToUnarchive = null;
    }

    function confirmUnarchive() {
      if (!documentToUnarchive) return;
      
      fetch(`/document/${documentToUnarchive}/unarchive`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message and reload page
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while unarchiving the document');
      })
      .finally(() => {
        closeUnarchiveModal();
      });
    }

    // Permanent delete functions
    function permanentlyDeleteDocument(documentId) {
      documentToDelete = documentId;
      document.getElementById('permanentDeleteModal').classList.add('modal-open');
    }

    function closePermanentDeleteModal() {
      document.getElementById('permanentDeleteModal').classList.remove('modal-open');
      documentToDelete = null;
    }

    function confirmPermanentDelete() {
      if (!documentToDelete) return;
      
      fetch(`/document/${documentToDelete}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message and reload page
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the document');
      })
      .finally(() => {
        closePermanentDeleteModal();
      });
    }

    // Document actions
    function viewDocument(documentId) {
      window.location.href = `/document/${documentId}`;
    }

    function downloadDocument(documentId) {
      window.location.href = `/document/${documentId}/download`;
    }

    // Filter functions
    function clearFilters() {
      document.getElementById('searchInput').value = '';
      document.getElementById('categoryFilter').value = '';
      document.getElementById('sourceFilter').value = '';
      filterDocuments();
    }

    function filterDocuments() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const categoryFilter = document.getElementById('categoryFilter').value;
      const sourceFilter = document.getElementById('sourceFilter').value;
      
      const rows = document.querySelectorAll('tbody tr');
      
      rows.forEach(row => {
        let showRow = true;
        
        // Search filter
        if (searchTerm) {
          const title = row.querySelector('td:first-child .font-medium')?.textContent?.toLowerCase() || '';
          const description = row.querySelector('td:first-child .text-sm')?.textContent?.toLowerCase() || '';
          if (!title.includes(searchTerm) && !description.includes(searchTerm)) {
            showRow = false;
          }
        }
        
        // Category filter
        if (categoryFilter && showRow) {
          const category = row.querySelector('td:nth-child(2) .badge')?.textContent?.toLowerCase() || '';
          if (category !== categoryFilter.replace('_', ' ')) {
            showRow = false;
          }
        }
        
        // Source filter
        if (sourceFilter && showRow) {
          const source = row.querySelector('td:nth-child(3) .badge')?.textContent?.toLowerCase() || '';
          if (source !== sourceFilter.replace('_', ' ')) {
            showRow = false;
          }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
      });
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('searchInput').addEventListener('input', filterDocuments);
      document.getElementById('categoryFilter').addEventListener('change', filterDocuments);
      document.getElementById('sourceFilter').addEventListener('change', filterDocuments);
    });
  </script>
</body>
</html>
