<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document Disposal Queue - Soliera</title>
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

                <!-- Page Header -->
                <div class="pb-5 border-b border-base-300">
                    <h1 class="text-2xl font-semibold text-gray-900">Document Disposal Queue</h1>
                    <p class="mt-2 text-sm text-gray-600">Documents that have reached their retention period and are ready for disposal.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <!-- Total Expired -->
                    <div class="card bg-base-100 shadow-xl border-l-4 border-l-error">
                        <div class="card-body p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div class="avatar placeholder">
                                    <div class="bg-error text-error-content rounded-full w-12 h-12">
                                        <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                                    </div>
                                </div>
                                <div class="badge badge-error badge-outline">Expired</div>
                            </div>
                            <div class="text-center">
                                <h2 class="card-title text-4xl font-bold text-error justify-center mb-2">{{ $stats['expired'] ?? 0 }}</h2>
                                <p class="text-base-content/70">Total Expired</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Disposal -->
                    <div class="card bg-base-100 shadow-xl border-l-4 border-l-warning">
                        <div class="card-body p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div class="avatar placeholder">
                                    <div class="bg-warning text-warning-content rounded-full w-12 h-12">
                                        <i data-lucide="clock" class="w-6 h-6"></i>
                                    </div>
                                </div>
                                <div class="badge badge-warning badge-outline">Pending</div>
                            </div>
                            <div class="text-center">
                                <h2 class="card-title text-4xl font-bold text-warning justify-center mb-2">{{ $stats['pending_disposal'] ?? 0 }}</h2>
                                <p class="text-base-content/70">Pending Disposal</p>
                            </div>
                        </div>
                    </div>

                    <!-- Disposed -->
                    <div class="card bg-base-100 shadow-xl border-l-4 border-l-success">
                        <div class="card-body p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div class="avatar placeholder">
                                    <div class="bg-success text-success-content rounded-full w-12 h-12">
                                        <i data-lucide="check-circle" class="w-6 h-6"></i>
                                    </div>
                                </div>
                                <div class="badge badge-success badge-outline">Disposed</div>
                            </div>
                            <div class="text-center">
                                <h2 class="card-title text-4xl font-bold text-success justify-center mb-2">{{ $stats['disposed'] ?? 0 }}</h2>
                                <p class="text-base-content/70">Total Disposed</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Expired Documents</h3>
                            <div class="text-sm text-gray-500">
                                Showing {{ $documents->count() ?? 0 }} expired documents
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="text-left py-4 px-4 font-semibold text-gray-700">Document Name/Title</th>
                                        <th class="text-center py-4 px-4 font-semibold text-gray-700">Document Type</th>
                                        <th class="text-center py-4 px-4 font-semibold text-gray-700">Department/Owner</th>
                                        <th class="text-center py-4 px-4 font-semibold text-gray-700">Date Created/Submitted</th>
                                        <th class="text-center py-4 px-4 font-semibold text-gray-700">Confidentiality Level</th>
                                        <th class="text-center py-4 px-4 font-semibold text-gray-700">Retention Period</th>
                                        <th class="text-center py-4 px-4 font-semibold text-gray-700">Status</th>
                                        <th class="text-center py-4 px-4 font-semibold text-gray-700">Expiration Date</th>
                                        <th class="text-center py-4 px-4 font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($documents ?? collect() as $document)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="py-4 px-4">
                                                <div class="flex items-center space-x-3">
                                                    <div class="avatar placeholder">
                                                        <div class="bg-red-100 text-red-800 rounded-full w-10 h-10 flex items-center justify-center">
                                                            <span class="text-sm font-semibold">
                                                                {{ substr($document->title ?? 'UN', 0, 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-semibold text-gray-900">{{ $document->title ?? 'Untitled Document' }}</h4>
                                                        <p class="text-sm text-gray-500">#{{ $document->document_uid ?? 'DOC-0000' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($document->category ?? 'general') }}</span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span class="text-sm font-medium text-gray-700">{{ $document->department ?? 'N/A' }}</span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span class="text-sm text-gray-600">
                                                    {{ $document->created_at ? $document->created_at->format('M d, Y') : 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span class="text-xs font-medium px-2 py-1 rounded-full
                                                    {{ $document->confidentiality === 'restricted' ? 'bg-red-100 text-red-800' : 
                                                       ($document->confidentiality === 'internal' ? 'bg-yellow-100 text-yellow-800' : 
                                                       'bg-green-100 text-green-800') }}">
                                                    {{ ucfirst($document->confidentiality ?? 'public') }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ $document->retention_policy ?? 'Default (2 years)' }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span class="text-xs font-medium bg-red-100 text-red-800 px-2 py-1 rounded-full">
                                                    {{ ucfirst($document->status ?? 'unknown') }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                @if($document->retention_until)
                                                    <span class="text-sm text-red-600 font-medium">
                                                        {{ $document->retention_until->format('M d, Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-sm text-gray-400">Not set</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <!-- View Button -->
                                                    <a href="{{ route('document.show', $document->id) }}" 
                                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200" 
                                                       title="View Document">
                                                        <i data-lucide="search" class="w-4 h-4"></i>
                                                    </a>
                                                    
                                                    <!-- Edit Button -->
                                                    <a href="{{ route('document.edit', $document->id) }}" 
                                                       class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-200" 
                                                       title="Edit Document">
                                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                                    </a>
                                                    
                                                    <!-- Delete Button -->
                                                    <button onclick="confirmDisposal({{ $document->id }})" 
                                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200" 
                                                            title="Delete Document">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="py-12 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                        <i data-lucide="check-circle" class="w-10 h-10 text-gray-400"></i>
                                                    </div>
                                                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No Documents Ready for Disposal</h3>
                                                    <p class="text-gray-500 text-sm mb-4">All documents are within their retention period.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if(isset($documents) && $documents->hasPages())
                            <div class="flex justify-center p-6 border-t border-gray-200">
                                {{ $documents->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>

    @include('partials.soliera_js')
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Disposal confirmation modal
        function confirmDisposal(documentId) {
            const modal = document.createElement('div');
            modal.className = 'modal modal-open';
            modal.innerHTML = `
                <div class="modal-box w-11/12 max-w-md bg-white text-gray-800 rounded-xl shadow-2xl" onclick="event.stopPropagation()">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                                <i data-lucide="trash-2" class="w-6 h-6 text-red-600"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Dispose Document</h3>
                                <p class="text-sm text-gray-500">This action cannot be undone</p>
                            </div>
                        </div>
                        <button onclick="closeModal()" class="btn btn-sm btn-circle btn-ghost">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="mb-6">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Permanent Deletion</h4>
                                    <p class="text-sm text-gray-600">Are you sure you want to dispose of this document? This will permanently delete the document and its file.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button onclick="closeModal()" class="btn btn-outline btn-sm hover:btn-primary transition-all duration-300 shadow-sm hover:shadow-md">
                            <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                            Cancel
                        </button>
                        <button onclick="disposeDocument(${documentId})" class="btn btn-error btn-sm hover:btn-error-focus transition-all duration-300 shadow-sm hover:shadow-md transform hover:scale-105" id="confirmDisposeBtn">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                            <span id="disposeBtnText">Dispose Document</span>
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            lucide.createIcons();
            
            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }

        // Close modal
        function closeModal() {
            const modal = document.querySelector('.modal');
            if (modal) {
                modal.remove();
            }
        }

        // Dispose document
        function disposeDocument(documentId) {
            const confirmBtn = document.getElementById('confirmDisposeBtn');
            const btnText = document.getElementById('disposeBtnText');
            
            // Show loading state
            confirmBtn.disabled = true;
            btnText.textContent = 'Disposing...';
            confirmBtn.innerHTML = `
                <i class="loading loading-spinner loading-sm mr-2"></i>
                <span>Disposing...</span>
            `;
            
            fetch(`/document/${documentId}/dispose`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    closeModal();
                    
                    // Show success notification
                    showEnhancedToast('Document disposed successfully!', 'success', 'trash-2', 'Document has been permanently removed from the system.');
                    
                    // Reload page to update statistics and table
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    throw new Error(data.message || 'Failed to dispose document');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Reset button state
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = `
                    <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                    <span>Dispose Document</span>
                `;
                lucide.createIcons();
                
                // Show error notification
                showEnhancedToast('Error disposing document: ' + error.message, 'error', 'alert-circle', 'Please try again or contact support if the issue persists.');
            });
        }

        // Enhanced toast notification function
        function showEnhancedToast(title, type = 'info', icon = 'info', description = '') {
            // Create toast container if it doesn't exist
            let toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toastContainer';
                toastContainer.className = 'fixed bottom-4 right-4 z-50 space-y-3';
                document.body.appendChild(toastContainer);
            }
            
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} shadow-xl max-w-sm transform transition-all duration-500 translate-x-full opacity-0`;
            
            // Set icon based on type
            const iconMap = {
                'success': 'check-circle',
                'error': 'alert-circle',
                'warning': 'alert-triangle',
                'info': 'info'
            };
            
            const finalIcon = icon || iconMap[type] || 'info';
            
            toast.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <i data-lucide="${finalIcon}" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-sm">${title}</h4>
                        ${description ? `<p class="text-xs opacity-90 mt-1">${description}</p>` : ''}
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="btn btn-ghost btn-xs p-1">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            `;
            
            // Add to container
            toastContainer.appendChild(toast);
            
            // Recreate Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
                toast.classList.add('translate-x-0', 'opacity-100');
            }, 100);
            
            // Auto remove after duration
            const duration = type === 'error' ? 6000 : 4000;
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 500);
                }
            }, duration);
        }
    </script>
</body>
</html>
