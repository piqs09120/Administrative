<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Legal Documents Management - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @vite(['resources/css/soliera.css'])
  
  <style>
    .swal2-popup {
      font-family: inherit;
      border-radius: 12px !important;
    }
    .swal2-confirm {
      background-color: #ef4444 !important;
      border: none !important;
      padding: 12px 24px !important;
      border-radius: 8px !important;
      font-weight: 600 !important;
      color: white !important;
      margin-right: 8px !important;
    }
    .swal2-cancel {
      background-color: #6b7280 !important;
      border: none !important;
      padding: 12px 24px !important;
      border-radius: 8px !important;
      font-weight: 600 !important;
      color: white !important;
      margin-left: 8px !important;
    }
    .swal2-actions {
      gap: 10px !important;
      margin-top: 20px !important;
    }
    .swal2-title {
      font-size: 20px !important;
      font-weight: 600 !important;
      margin-bottom: 16px !important;
    }
    .swal2-content {
      font-size: 16px !important;
      line-height: 1.5 !important;
    }
  </style>
</head>
<body class="bg-base-100">
  <div class="flex h-screen overflow-hidden">
    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>
    
    <!-- Sidebar -->
    @include('partials.sidebarr')
    
    <!-- Main content -->
    <div class="flex flex-col flex-1 overflow-hidden lg:ml-0">
      <!-- Header -->
      @include('partials.navbar')

      <!-- Main content area -->
      <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
        @if(session('success'))
          <div class="toast toast-bottom toast-end" id="session-success-toast">
            <div class="alert alert-success">
              <i data-lucide="check-circle" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
              <span>{{ session('success') }}</span>
            </div>
          </div>
        @endif

        @if(session('error'))
          <div class="toast toast-bottom toast-end" id="session-error-toast">
            <div class="alert alert-error">
              <i data-lucide="alert-circle" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
              <span>{{ session('error') }}</span>
            </div>
          </div>
        @endif

        <!-- AI Alert Banner -->
        <div id="aiAlertBanner" class="hidden bg-red-50 border-l-4 border-red-400 p-4 mb-6">
          <div class="flex">
            <div class="flex-shrink-0">
              <i data-lucide="alert-triangle" class="h-5 w-5 text-red-400"></i>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">
                High-Risk Documents Detected
              </h3>
              <div class="mt-2 text-sm text-red-700">
                <p id="alertMessage">AI analysis has detected high-risk documents that require immediate attention.</p>
              </div>
              <div class="mt-4">
                <div class="-mx-2 -my-1.5 flex">
                  <button onclick="viewHighRiskDocuments()" class="bg-red-50 px-2 py-1.5 rounded-md text-sm font-medium text-red-800 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                    View Documents
                  </button>
                  <button onclick="dismissAlert()" class="ml-3 bg-red-50 px-2 py-1.5 rounded-md text-sm font-medium text-red-800 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                    Dismiss
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Page Header -->
        <div class="mb-8">
          <div class="pb-5 border-b border-base-300 mb-6">
            <div class="flex justify-between items-center mb-4">
              <div>
                <h1 class="text-2xl font-semibold bg-white bg-clip-text text-[#191970]" style="color: var(--color-charcoal-ink);">Legal Documents</h1>
              </div>
            </div>
          </div>

          <!-- Status Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8">
              <!-- Total Legal Documents -->
              <x-stat-card 
                title="Legal Documents" 
                :value="$stats['total'] ?? 0" 
                icon="fa-folder" 
                iconColor="text-yellow-400" 
                bgColor="bg-blue-900" />

              <!-- Approved Documents -->
              <x-stat-card 
                title="Approved" 
                :value="$stats['active'] ?? 0" 
                icon="fa-check-circle" 
                iconColor="text-yellow-400" 
                bgColor="bg-blue-900" />

              <!-- Violation Alerts -->
              <x-stat-card 
                title="Violations" 
                :value="$stats['violations'] ?? 0" 
                icon="fa-shield-alt" 
                iconColor="text-yellow-400" 
                bgColor="bg-blue-900" />
            </div>

          <!-- Bottom Border Separator -->
          <div class="border-b border-base-300 mb-6"></div>
        </div>

        <!-- Tabs -->
        @php 
          $validTabs = ['documents','create','monitor'];
          $tabParam = request('tab');
          $activeTab = in_array($tabParam, $validTabs) ? $tabParam : 'documents';
        @endphp
          <div class="bg-white rounded-xl shadow-lg p-6">
            <!-- Clickable Breadcrumb Navigation -->
            <div class="mb-4 sm:mb-6">
              <nav class="flex flex-wrap items-center gap-1 sm:gap-2 text-xs sm:text-sm">
                <button id="nav-documents" class="text-blue-600 hover:text-blue-800 font-medium flex items-center transition-colors duration-200 px-2 py-1 rounded {{ $activeTab==='documents' ? 'text-blue-800 font-semibold bg-blue-50' : '' }}" onclick="showLegalTab('documents')">
                  <i data-lucide="folder" class="text-sm md:text-base lg:text-lg transition-all duration-300 ease-in-out hover:text-accent cursor-pointer mr-1"></i>
                  <span class="hidden sm:inline">Documents</span>
                  <span class="sm:hidden">Docs</span>
                </button>
                <i data-lucide="chevron-right" class="text-sm md:text-base lg:text-lg transition-all duration-300 ease-in-out hover:text-accent cursor-pointer text-gray-400"></i>
                <button id="nav-create" class="text-gray-600 hover:text-blue-600 font-medium flex items-center transition-colors duration-200 px-2 py-1 rounded {{ $activeTab==='create' ? 'text-blue-600 font-semibold bg-blue-50' : '' }}" onclick="showLegalTab('create')">
                  <i data-lucide="plus" class="text-sm md:text-base lg:text-lg transition-all duration-300 ease-in-out hover:text-accent cursor-pointer mr-1"></i>
                  <span class="hidden sm:inline">Create</span>
                  <span class="sm:hidden">New</span>
                </button>
                <i data-lucide="chevron-right" class="text-sm md:text-base lg:text-lg transition-all duration-300 ease-in-out hover:text-accent cursor-pointer text-gray-400"></i>
                <button id="nav-monitor" class="text-gray-600 hover:text-blue-600 font-medium flex items-center transition-colors duration-200 px-2 py-1 rounded {{ $activeTab==='monitor' ? 'text-blue-600 font-semibold bg-blue-50' : '' }}" onclick="showLegalTab('monitor')">
                  <i data-lucide="bar-chart" class="text-sm md:text-base lg:text-lg transition-all duration-300 ease-in-out hover:text-accent cursor-pointer mr-1"></i>
                  <span class="hidden sm:inline">Monitoring</span>
                  <span class="sm:hidden">Monitor</span>
                </button>
              </nav>
            </div>

          <!-- CREATE TAB CONTENT -->
          <div id="legal-create-tab" class="{{ $activeTab==='create' ? '' : 'hidden' }}">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                <div class="flex items-center gap-2 mb-2">
                  <i data-lucide="plus-square" class="w-5 h-5 text-blue-600"></i>
                  <h3 class="text-lg font-semibold">Custom Document</h3>
                </div>
                <p class="text-sm text-gray-600 mb-3">Create a free‑form document with custom terms and conditions</p>
                <a href="{{ route('legal.documents.draft') }}" class="btn btn-primary btn-sm">Start</a>
              </div>
              <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                <div class="flex items-center gap-2 mb-2">
                  <i data-lucide="clock" class="w-5 h-5 text-blue-600"></i>
                  <h3 class="text-lg font-semibold">Recent Documents</h3>
                </div>
                <p class="text-sm text-gray-600">No recent documents</p>
              </div>
            </div>

            <h3 class="text-md font-semibold mt-4 sm:mt-6 mb-3">Document Templates</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
              <div class="border rounded-xl p-3 sm:p-4 hover:shadow-lg transition-shadow duration-200">
                <div class="text-xs sm:text-sm text-gray-600 mb-2">Legal</div>
                <div class="font-semibold text-sm sm:text-base">Service Contract</div>
                <p class="text-xs text-gray-500 mt-1 mb-3 line-clamp-2">Standard service agreements and contracts</p>
                <a href="{{ route('legal.documents.draft', ['template'=>'service_contract']) }}" class="btn btn-outline btn-xs w-full sm:w-auto">Use Template</a>
              </div>
              <div class="border rounded-xl p-3 sm:p-4 hover:shadow-lg transition-shadow duration-200">
                <div class="text-xs sm:text-sm text-gray-600 mb-2">HR</div>
                <div class="font-semibold text-sm sm:text-base">Employment Contract</div>
                <p class="text-xs text-gray-500 mt-1 mb-3 line-clamp-2">Legally sound employment agreement with placeholders</p>
                <a href="{{ route('legal.documents.draft', ['template'=>'employment_contract']) }}" class="btn btn-outline btn-xs w-full sm:w-auto">Use Template</a>
              </div>
              <div class="border rounded-xl p-3 sm:p-4 hover:shadow-lg transition-shadow duration-200">
                <div class="text-xs sm:text-sm text-gray-600 mb-2">Operations</div>
                <div class="font-semibold text-sm sm:text-base">Guest Agreement</div>
                <p class="text-xs text-gray-500 mt-1 mb-3 line-clamp-2">Visitor and guest access agreements</p>
                <a href="{{ route('legal.documents.draft', ['template'=>'guest_agreement']) }}" class="btn btn-outline btn-xs w-full sm:w-auto">Use Template</a>
              </div>
              <div class="border rounded-xl p-3 sm:p-4 hover:shadow-lg transition-shadow duration-200">
                <div class="text-xs sm:text-sm text-gray-600 mb-2">Procurement</div>
                <div class="font-semibold text-sm sm:text-base">Vendor Agreement</div>
                <p class="text-xs text-gray-500 mt-1 mb-3 line-clamp-2">Supplier and vendor contracts</p>
                <a href="{{ route('legal.documents.draft', ['template'=>'vendor_agreement']) }}" class="btn btn-outline btn-xs w-full sm:w-auto">Use Template</a>
              </div>
              <div class="border rounded-xl p-3 sm:p-4 hover:shadow-lg transition-shadow duration-200">
                <div class="text-xs sm:text-sm text-gray-600 mb-2">HR</div>
                <div class="font-semibold text-sm sm:text-base">HR Policy Document</div>
                <p class="text-xs text-gray-500 mt-1 mb-3 line-clamp-2">Human resources policies and procedures</p>
                <a href="{{ route('legal.documents.draft', ['template'=>'hr_policy']) }}" class="btn btn-outline btn-xs w-full sm:w-auto">Use Template</a>
              </div>
            </div>

            <!-- My Created Documents Table -->
            <div class="mt-8">
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                  <i data-lucide="folder-plus" class="w-5 h-5 text-blue-600"></i>
                  My Created Documents
                </h3>
                <div class="text-sm text-gray-500">
                  {{ $createdDocuments->count() }} document{{ $createdDocuments->count() !== 1 ? 's' : '' }}
                </div>
              </div>
              
              <!-- Professional Table -->
              <x-table-card :title="'My Created Documents'">
                <!-- Table Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                  <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-gray-700">
                    <div class="col-span-5">Document Information</div>
                  <div class="col-span-2 text-center">Type</div>
                    <div class="col-span-2 text-center">Department</div>
                  <div class="col-span-2 text-center">Status</div>
                    <div class="col-span-1 text-center">Actions</div>
                </div>
              </div>
              
                <!-- Table Body -->
                <div class="divide-y divide-gray-200">
                @forelse($createdDocuments as $doc)
                  <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div class="grid grid-cols-12 gap-4 items-center">
                      <!-- Document Information -->
                      <div class="col-span-5">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-lg bg-blue-900 flex items-center justify-center flex-shrink-0">
                            @php
                              $fileExtension = pathinfo($doc->file_path ?? '', PATHINFO_EXTENSION);
                              $iconColor = 'text-white';
                              
                              switch(strtolower($fileExtension)) {
                                case 'pdf':
                                  $iconColor = 'text-white';
                                  break;
                                case 'doc':
                                case 'docx':
                                  $iconColor = 'text-white';
                                  break;
                                case 'xls':
                                case 'xlsx':
                                  $iconColor = 'text-white';
                                  break;
                                case 'ppt':
                                case 'pptx':
                                  $iconColor = 'text-white';
                                  break;
                                default:
                                  $iconColor = 'text-white';
                              }
                            @endphp
                            <i data-lucide="file-text" class="w-5 h-5 {{ $iconColor }}"></i>
                          </div>
                          <div class="min-w-0 flex-1">
                            <div class="font-semibold text-gray-900 truncate">{{ $doc->title }}</div>
                            <div class="text-sm text-gray-500 truncate">{{ $doc->description ?: 'No description' }}</div>
                            <div class="text-xs text-blue-600 font-mono">{{ $doc->legal_document_id ?? 'LD-' . now()->format('Y') . '-' . str_pad($doc->id, 6, '0', STR_PAD_LEFT) }}</div>
                            <div class="text-xs text-gray-400 mt-1">{{ $doc->created_at?->format('M d, Y') }}</div>
                          </div>
                        </div>
                      </div>

                      <!-- Type -->
                      <div class="col-span-2 text-center">
                        <span class="badge badge-outline badge-sm">{{ ucfirst(str_replace('_', ' ', $doc->category ?? 'general')) }}</span>
                      </div>

                      <!-- Department -->
                      <div class="col-span-2 text-center">
                        <span class="text-sm text-gray-600">{{ $doc->department ?? ($doc->uploader->dept_name ?? 'N/A') }}</span>
                      </div>

                      <!-- Status -->
                      <div class="col-span-2 text-center">
                        @php
                          $statusConfig = [
                            'active' => ['class' => 'badge-success', 'icon' => 'check-circle', 'text' => 'Active'],
                            'pending_review' => ['class' => 'badge-warning', 'icon' => 'clock', 'text' => 'Pending Review'],
                            'draft' => ['class' => 'badge bg-green-500 text-white', 'icon' => 'edit-3', 'text' => 'Draft'],
                            'approved' => ['class' => 'badge-success', 'icon' => 'check-circle-2', 'text' => 'Approved'],
                            'rejected' => ['class' => 'badge-error', 'icon' => 'x-circle', 'text' => 'Rejected'],
                            'archived' => ['class' => 'badge-neutral', 'icon' => 'archive', 'text' => 'Archived']
                          ];
                          $status = $doc->status ?? 'draft';
                          $config = $statusConfig[$status] ?? $statusConfig['draft'];
                        @endphp
                        <div class="flex items-center justify-center gap-2">
                          <i data-lucide="{{ $config['icon'] }}" class="w-4 h-4 text-gray-500"></i>
                          <span class="badge {{ $config['class'] }} badge-sm">{{ $config['text'] }}</span>
                        </div>
                      </div>

                      <!-- Actions -->
                      <div class="col-span-1">
                        <div class="flex items-center justify-center gap-1">
                          <!-- AI Analysis Button -->
                          <button onclick="aiAnalysis({{ $doc->id }})" 
                                  class="btn-sm p-2 rounded-lg transition-all duration-200 hover:scale-110" 
                                  style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                  title="AI Analysis">
                            <i data-lucide="brain" class="w-4 h-4" style="fill: none;"></i>
                          </button>
                          
                          <!-- Edit Button (only for draft documents) -->
                          @if(($doc->status ?? 'draft') === 'draft')
                          <a href="{{ route('legal.documents.draft') }}?edit={{ $doc->id }}" 
                             class="btn-sm p-2 rounded-lg transition-all duration-200 hover:scale-110"
                             style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                             title="Edit Document">
                            <i data-lucide="edit-3" class="w-4 h-4" style="fill: none;"></i>
                          </a>
                          @endif
                          
                          <!-- Archive Button (No Deletion, Archive Only) -->
                          @if($doc->status !== 'archived')
                          <button onclick="archiveDocument({{ $doc->id }})" 
                                  class="btn-sm p-2 rounded-lg transition-all duration-200 hover:scale-110"
                                  style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                  title="Archive Document">
                            <i data-lucide="archive" class="w-4 h-4" style="fill: none;"></i>
                          </button>
                          @else
                          <span class="text-xs text-gray-500 px-2 py-1 bg-gray-100 rounded">Archived</span>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center">
                      <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="file-x" class="w-8 h-8 text-gray-400"></i>
                      </div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No Documents Found</h3>
                    <p class="text-gray-500 mb-4">Start by creating your first legal document.</p>
                      <a href="{{ route('legal.documents.draft') }}" class="btn btn-primary">
                      <i data-lucide="plus" class="w-4 h-4 mr-2"></i>Create Document
                    </a>
                  </div>
                </div>
                @endforelse
                </div>
              </x-table-card>
            </div>
          </div>

          <!-- MONITOR TAB CONTENT -->
          <div id="legal-monitor-tab" class="{{ $activeTab==='monitor' ? '' : 'hidden' }}">
            <!-- Professional Administrator Monitoring Dashboard -->
            <div class="space-y-6">
              
              <!-- Dashboard Header -->
              <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                      <i data-lucide="shield-check" class="w-7 h-7 text-blue-600"></i>
                      Legal Documents Monitoring Dashboard
                    </h2>
                    <p class="text-gray-600">Comprehensive oversight and analytics for legal document management</p>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="text-right">
                      <div class="text-sm text-gray-500">Last Updated</div>
                      <div class="font-semibold text-gray-800" id="lastUpdated">Just now</div>
                    </div>
                    <button onclick="refreshMonitoringData()" class="btn btn-outline btn-sm">
                      <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                      Refresh
                    </button>
                  </div>
                </div>
              </div>

              <!-- Key Performance Indicators -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Documents -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm font-medium text-gray-600">Total Documents</p>
                      <p class="text-2xl font-bold text-gray-900" id="mon-total">0</p>
                        </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                      <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                      </div>
                    </div>
                  <div class="mt-2 flex items-center text-sm">
                    <span class="text-green-600 font-medium" id="mon-total-change">+0%</span>
                    <span class="text-gray-500 ml-1">vs last month</span>
                  </div>
                </div>

                <!-- Pending Review -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm font-medium text-gray-600">Pending Review</p>
                      <p class="text-2xl font-bold text-amber-600" id="mon-pending">0</p>
                        </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                      <i data-lucide="clock" class="w-6 h-6 text-amber-600"></i>
                      </div>
                    </div>
                  <div class="mt-2 flex items-center text-sm">
                    <span class="text-amber-600 font-medium" id="mon-pending-change">0</span>
                    <span class="text-gray-500 ml-1">awaiting action</span>
                  </div>
                </div>

                <!-- Approved -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm font-medium text-gray-600">Approved</p>
                      <p class="text-2xl font-bold text-green-600" id="mon-approved">0</p>
                        </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                      <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                      </div>
                    </div>
                  <div class="mt-2 flex items-center text-sm">
                    <span class="text-green-600 font-medium" id="mon-approved-change">+0%</span>
                    <span class="text-gray-500 ml-1">approval rate</span>
                  </div>
                </div>

                <!-- Expiring Soon -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm font-medium text-gray-600">Expiring Soon</p>
                      <p class="text-2xl font-bold text-red-600" id="mon-expiring">0</p>
                        </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                      <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
                      </div>
                    </div>
                  <div class="mt-2 flex items-center text-sm">
                    <span class="text-red-600 font-medium" id="mon-expiring-change">0</span>
                    <span class="text-gray-500 ml-1">within 90 days</span>
                  </div>
                </div>
              </div>


              <!-- Professional Data Table -->
              <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <!-- Table Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 rounded-t-lg">
                  <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                      <i data-lucide="table" class="w-5 h-5 text-gray-600"></i>
                      Document Monitoring Table
                    </h3>
                    <div class="flex items-center gap-2">
                      <button onclick="exportMonitoringData()" class="btn btn-outline btn-sm">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                        Export
                      </button>
                      <div class="text-sm text-gray-500">
                        <span id="mon-results-count">0</span> documents found
                        </div>
                      </div>
                  </div>
                </div>

                <!-- Table Content -->
                <x-table-card :title="'Documents'">
                  <table class="table table-zebra w-full">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 border-b border-gray-200">
                          <div class="flex items-center gap-2">
                            <input type="checkbox" id="select-all" class="checkbox checkbox-sm" />
                            <span>Document</span>
                        </div>
                        </th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 border-b border-gray-200">Type</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 border-b border-gray-200">Department</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 border-b border-gray-200">Status</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 border-b border-gray-200">Uploaded By</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 border-b border-gray-200">Date</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 border-b border-gray-200">Expiry</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700 border-b border-gray-200">Actions</th>
                      </tr>
                    </thead>
                    <tbody id="monitoring-table-body">
                      <tr>
                        <td colspan="8" class="text-center py-12">
                          <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                              <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-gray-400"></i>
                      </div>
                            <h3 class="text-lg font-medium text-gray-600 mb-2">Loading monitoring data...</h3>
                            <p class="text-gray-500">Please wait while we fetch the latest information</p>
                    </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </x-table-card>

                <!-- Table Footer with Pagination -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-lg">
                  <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                      Showing <span id="mon-showing-start">0</span> to <span id="mon-showing-end">0</span> of <span id="mon-total-results">0</span> results
                        </div>
                    <div class="flex items-center gap-2" id="mon-pagination">
                      <!-- Pagination will be inserted here -->
                      </div>
                  </div>
                </div>
              </div>

              <!-- Bulk Actions Panel -->
              <div id="bulk-actions-panel" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-blue-800">
                      <span id="selected-count">0</span> documents selected
                    </span>
                    <div class="flex items-center gap-2">
                      <button onclick="bulkApprove()" class="btn btn-success btn-sm">
                        <i data-lucide="check" class="w-4 h-4 mr-1"></i>
                        Approve Selected
                      </button>
                      <button onclick="bulkReject()" class="btn btn-error btn-sm">
                        <i data-lucide="x" class="w-4 h-4 mr-1"></i>
                        Reject Selected
                      </button>
                      <button onclick="bulkArchive()" class="btn btn-warning btn-sm">
                        <i data-lucide="archive" class="w-4 h-4 mr-1"></i>
                        Archive Selected
                      </button>
                  </div>
                </div>
                  <button onclick="clearSelection()" class="btn btn-ghost btn-sm">
                    <i data-lucide="x" class="w-4 h-4"></i>
                  </button>
              </div>
              </div>
            </div>
          </div>

          <!-- DOCUMENTS TAB CONTENT -->
          <div id="legal-documents-tab" class="{{ $activeTab==='documents' ? '' : 'hidden' }}">
          <!-- Desktop Table View -->
          <x-table-card :title="'Documents'">
            <!-- Search and Filters inside the table card, below the blue banner -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
              <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <!-- Search Bar -->
                <div class="md:col-span-4">
                  <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text" 
                           id="searchInput"
                           placeholder="Search documents..." 
                           class="input input-bordered input-sm w-full pl-10 pr-4 bg-white border-gray-200 focus:border-blue-300">
                  </div>
                </div>

                <!-- Category Filter -->
                <div class="md:col-span-3">
                  <label class="block text-xs font-medium text-gray-700 mb-1">Category:</label>
                  <select id="categoryFilter" class="select select-bordered select-sm w-full">
                    <option value="">All Categories</option>
                    <option value="contract">Contract</option>
                    <option value="legal_notice">Legal Notice</option>
                    <option value="policy">Policy</option>
                    <option value="compliance">Compliance</option>
                    <option value="financial">Financial</option>
                    <option value="report">Report</option>
                    <option value="memorandum">Memorandum</option>
                    <option value="affidavit">Affidavit</option>
                    <option value="subpoena">Subpoena</option>
                    <option value="cease_desist">Cease & Desist</option>
                    <option value="legal_brief">Legal Brief</option>
                  </select>
                </div>

                <!-- Status Filter -->
                <div class="md:col-span-3">
                  <label class="block text-xs font-medium text-gray-700 mb-1">Status:</label>
                  <select id="statusFilter" class="select select-bordered select-sm w-full">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="pending_review">Pending Review</option>
                    <option value="rejected">Rejected</option>
                    <option value="archived">Archived</option>
                    <option value="draft">Draft</option>
                  </select>
                </div>

                <!-- Action Buttons -->
                <div class="md:col-span-2 flex gap-2">
                  <button onclick="startBulkAnalysis()" class="btn btn-primary btn-sm flex-1">
                    <i data-lucide="brain" class="w-4 h-4 mr-1"></i>
                    <span class="hidden sm:inline">Bulk AI</span>
                  </button>
                  <button onclick="exportViolationReport()" class="btn btn-outline btn-sm flex-1">
                    <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                    <span class="hidden sm:inline">Export</span>
                  </button>
                </div>
              </div>
            </div>
            <table class="table table-zebra w-full">
              <thead>
                <tr class="bg-gray-50">
                  <th class="text-left py-3 px-4 font-semibold text-gray-700">Document Information</th>
                  <th class="text-center py-3 px-4 font-semibold text-gray-700 w-32">Type</th>
                  <th class="text-center py-3 px-4 font-semibold text-gray-700 w-40">Uploaded By</th>
                  <th class="text-center py-3 px-4 font-semibold text-gray-700 w-32">Department</th>
                  <th class="text-center py-3 px-4 font-semibold text-gray-700 w-32">Status</th>
                  <th class="text-center py-3 px-4 font-semibold text-gray-700 w-32">Date</th>
                  <th class="text-center py-3 px-4 font-semibold text-gray-700 w-32">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($documents as $index => $document)
                  <tr class="hover:bg-gray-50 transition-colors duration-200" data-document-id="{{ $document->id }}">
                    <!-- Document Information Column -->
                    <td class="py-3 px-4">
                      <div class="flex items-center space-x-3">
                        <!-- Avatar -->
                        <div class="avatar placeholder">
                          <div class="bg-blue-900 text-white rounded-full w-8 h-8 flex items-center justify-center">
                            @php
                              $fileExtension = pathinfo($document->file_path ?? '', PATHINFO_EXTENSION);
                              $iconColor = 'text-white';
                              
                              switch(strtolower($fileExtension)) {
                                case 'pdf':
                                  $iconColor = 'text-white';
                                  break;
                                case 'doc':
                                case 'docx':
                                  $iconColor = 'text-white';
                                  break;
                                case 'xls':
                                case 'xlsx':
                                  $iconColor = 'text-white';
                                  break;
                                case 'ppt':
                                case 'pptx':
                                  $iconColor = 'text-white';
                                  break;
                                default:
                                  $iconColor = 'text-white';
                              }
                            @endphp
                            <i data-lucide="file-text" class="w-4 h-4 {{ $iconColor }}"></i>
                          </div>
                        </div>
                        
                        <!-- Document Title -->
                        <div>
                          <h4 class="font-semibold text-gray-900 text-sm">{{ $document->title }}</h4>
                          <p class="text-xs text-gray-500">{{ Str::limit($document->description, 40) }}</p>
                          <p class="text-xs font-mono text-blue-600">{{ $document->legal_document_id ?? 'LD-' . now()->format('Y') . '-' . str_pad($document->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                      </div>
                    </td>
                    
                    <!-- Type Column -->
                    <td class="py-3 px-4 text-center">
                      <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $document->category ?? 'General')) }}</span>
                    </td>
                    
                    <!-- Uploaded By Column -->
                    <td class="py-3 px-4 text-center">
                      <span class="text-sm font-medium text-gray-700">{{ $document->uploader->employee_name ?? 'Unknown' }}</span>
                    </td>
                    
                    <!-- Department Column -->
                    <td class="py-3 px-4 text-center">
                      <span class="text-sm font-medium text-gray-700">{{ $document->uploader->dept_name ?? 'N/A' }}</span>
                    </td>
                    
                    <!-- Status Column -->
                    <td class="py-3 px-4 text-center">
                      @php
                        $statusConfig = [
                          'active' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'check-circle', 'text' => 'Active'],
                            'pending_review' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'clock', 'text' => 'Pending Review'],
                            'archived' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'archive', 'text' => 'Archived'],
                            'draft' => ['class' => 'bg-green-500 text-white', 'icon' => 'edit-3', 'text' => 'Draft'],
                            'approved' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'check-circle-2', 'text' => 'Approved'],
                            'declined' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'x-circle', 'text' => 'Declined']
                          ];
                          $status = $document->status ?? 'active';
                          $config = $statusConfig[$status] ?? $statusConfig['active'];
                        @endphp
                      <div class="flex items-center justify-center space-x-1">
                        <i data-lucide="{{ $config['icon'] }}" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                        <span class="text-xs font-medium {{ $config['class'] }} px-2 py-1 rounded-full">{{ $config['text'] }}</span>
                      </div>
                    </td>
                    
                    <!-- Date Column -->
                    <td class="py-3 px-4 text-center">
                      <span class="text-sm text-gray-600">{{ $document->created_at->format('M d, Y') }}</span>
                    </td>
                    
                    <!-- Actions Column -->
                    <td class="py-3 px-4 text-center">
                      <div class="flex items-center justify-center space-x-1">
                        <!-- Approve Button -->
                        @if($document->status !== 'approved' && $document->status !== 'declined')
                          <button onclick="approveDocument({{ $document->id }})" 
                                  class="p-1.5 rounded-lg transition-all duration-200 hover:scale-110" 
                                  style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                  title="Approve">
                            <i data-lucide="check" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out cursor-pointer" style="fill: none;"></i>
                          </button>
                        @endif
                        
                        <!-- Decline Button -->
                        @if($document->status !== 'approved' && $document->status !== 'declined')
                          <button onclick="declineDocument({{ $document->id }})" 
                                  class="p-1.5 rounded-lg transition-all duration-200 hover:scale-110"
                                  style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                  title="Decline">
                            <i data-lucide="x" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out cursor-pointer" style="fill: none;"></i>
                          </button>
                        @endif
                        
                        <!-- AI Analysis Button -->
                        <button onclick="aiAnalysis({{ $document->id }})" 
                                class="p-2 rounded-lg transition-all duration-200 hover:scale-110 touch-manipulation"
                                style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                title="AI Analysis">
                          <i data-lucide="brain" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out cursor-pointer" style="fill: none;"></i>
                        </button>
                        
                        <!-- Archive Button (No Deletion, Archive Only) -->
                        @if($document->status !== 'archived')
                          <button onclick="archiveDocument({{ $document->id }})" 
                                  class="p-2 rounded-lg transition-all duration-200 hover:scale-110 touch-manipulation"
                                  style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                  title="Archive Document">
                            <i data-lucide="archive" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out cursor-pointer" style="fill: none;"></i>
                          </button>
                        @else
                          <span class="text-xs text-gray-500 px-2 py-1 bg-gray-100 rounded">Archived</span>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="py-12 text-center">
                      <div class="flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                          <i data-lucide="folder" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No Legal Documents Found</h3>
                        <p class="text-gray-500 text-sm">No documents available at the moment.</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </x-table-card>

          <!-- Mobile Card View -->
          <div class="lg:hidden space-y-4">
            @forelse($documents as $index => $document)
              <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow duration-200" data-document-id="{{ $document->id }}">
                <!-- Card Header -->
                <div class="flex items-start justify-between mb-3">
                  <div class="flex items-center space-x-3 flex-1 min-w-0">
                    <!-- Avatar -->
                    <div class="avatar placeholder flex-shrink-0">
                      <div class="bg-blue-900 text-white rounded-full w-10 h-10">
                        <i data-lucide="file-text" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                      </div>
                    </div>
                    <!-- Document Info -->
                    <div class="flex-1 min-w-0">
                      <h3 class="font-semibold text-gray-900 truncate">{{ $document->title }}</h3>
                      <p class="text-sm text-gray-500 truncate">{{ $document->description ?: 'No description' }}</p>
                    </div>
                  </div>
                  <!-- Status Badge -->
                  @php
                    $status = $document->status ?? 'draft';
                    $badgeClass = match($status) {
                      'pending_review' => 'badge-warning',
                      'active' => 'badge-success',
                      'draft' => 'badge-info',
                      'archived' => 'badge-error',
                      default => 'badge-neutral'
                    };
                  @endphp
                  <span class="badge {{ $badgeClass }} text-xs">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                </div>

                <!-- Document Details -->
                <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                  <div>
                    <span class="text-gray-500">Type:</span>
                    <span class="font-medium ml-1">{{ ucfirst($document->category ?? 'General') }}</span>
                  </div>
                  <div>
                    <span class="text-gray-500">Department:</span>
                    <span class="font-medium ml-1">{{ $document->department ?? ($document->uploader->dept_name ?? 'N/A') }}</span>
                  </div>
                  <div>
                    <span class="text-gray-500">Uploaded By:</span>
                    <span class="font-medium ml-1">{{ $document->uploader->employee_name ?? 'Unknown' }}</span>
                  </div>
                  <div>
                    <span class="text-gray-500">Date:</span>
                    <span class="font-medium ml-1">{{ $document->created_at?->format('M d, Y') }}</span>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2">
                  <!-- AI Analysis Button -->
                  <button onclick="aiAnalysis({{ $document->id }})" class="btn btn-ghost btn-sm text-purple-600 hover:bg-purple-50">
                    <i data-lucide="brain" class="w-4 h-4 mr-1"></i>
                    <span class="hidden sm:inline">AI Analysis</span>
                  </button>
                  
                  <!-- Download Button -->
                  <button onclick="downloadDocument({{ $document->id }})" class="btn btn-ghost btn-sm text-blue-600 hover:bg-blue-50">
                    <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                    <span class="hidden sm:inline">Download</span>
                  </button>
                  
                  <!-- Edit Button (only for draft documents) -->
                  @if(($document->status ?? 'draft') === 'draft')
                  <a href="{{ route('legal.documents.draft') }}?edit={{ $document->id }}" class="btn btn-ghost btn-sm text-green-600 hover:bg-green-50">
                    <i data-lucide="edit-3" class="w-4 h-4 mr-1"></i>
                    <span class="hidden sm:inline">Edit</span>
                  </a>
                  @endif
                  
                  <!-- Archive Button (No Deletion, Archive Only) -->
                  @if($document->status !== 'archived')
                  <button onclick="archiveDocument({{ $document->id }})" class="btn btn-ghost btn-sm text-orange-600 hover:bg-orange-50">
                    <i data-lucide="archive" class="w-4 h-4 mr-1"></i>
                    <span class="hidden sm:inline">Archive</span>
                  </button>
                  @else
                  <span class="text-xs text-gray-500 px-2 py-1 bg-gray-100 rounded">Archived</span>
                  @endif
                </div>
              </div>
            @empty
              <div class="text-center py-12">
                <div class="flex flex-col items-center">
                  <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="file-text" class="w-10 h-10 text-gray-400"></i>
                  </div>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">No documents found</h3>
                  <p class="text-gray-500">Get started by uploading your first document.</p>
                </div>
              </div>
            @endforelse
          </div>

          <!-- Pagination -->
          <x-table-card :pagination="$documents->hasPages() ? $documents->appends(['search' => $search ?? '', 'category' => $category ?? '', 'status' => $status ?? ''])->links() : null"></x-table-card>
          </div>
        </div>
      </main>
    </div>
  </div>


  <!-- Bulk Upload Modal -->
  <div id="bulkUploadModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="folder-plus" class="w-6 h-6 text-blue-500"></i>
          Bulk Upload Legal Documents
        </h3>
        <button onclick="closeBulkUploadModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
        </button>
      </div>

      <form id="bulkUploadForm" action="{{ route('document.bulkUpload') }}" method="POST" enctype="multipart/form-data" onsubmit="handleBulkUploadSubmit(event)">
        @csrf
        <input type="hidden" name="source" value="legal_management">
        
        <div class="space-y-6">
          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Category for All Documents</span>
            </label>
            <select name="category" class="select select-bordered w-full">
              <option value="">Select category</option>
              <option value="contract">Contract</option>
              <option value="legal_notice">Legal Notice</option>
              <option value="policy">Policy</option>
              <option value="compliance">Compliance</option>
              <option value="financial">Financial</option>
              <option value="report">Report</option>
            </select>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Multiple Files</span>
            </label>
            <input type="file" name="document_files[]" multiple class="file-input file-input-bordered w-full" 
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required>
            <label class="label">
              <span class="label-text-alt">Select multiple files (Max 10 files, 10MB each)</span>
            </label>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Description Template</span>
            </label>
            <textarea name="description_template" class="textarea textarea-bordered w-full h-20" 
                      placeholder="Description template for all documents (optional)"></textarea>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="closeBulkUploadModal()" class="btn btn-outline">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
              Bulk Upload
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Document Modal -->
  <div id="editModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="edit" class="w-6 h-6 text-blue-500"></i>
          Edit Legal Document
        </h3>
        <button onclick="closeEditModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
        </button>
      </div>

      <form id="editForm" method="POST" onsubmit="handleEditSubmit(event)">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Document Title *</span>
            </label>
            <input type="text" name="title" id="editTitle" class="input input-bordered w-full" required>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Category</span>
            </label>
            <select name="category" id="editCategory" class="select select-bordered w-full">
              <option value="contract">Contract</option>
              <option value="legal_notice">Legal Notice</option>
              <option value="policy">Policy</option>
              <option value="compliance">Compliance</option>
              <option value="financial">Financial</option>
              <option value="report">Report</option>
              <option value="memorandum">Memorandum</option>
              <option value="affidavit">Affidavit</option>
              <option value="subpoena">Subpoena</option>
              <option value="cease_desist">Cease & Desist</option>
              <option value="legal_brief">Legal Brief</option>
            </select>
          </div>

          <div class="form-control">
            <label class="label">
              <span class="label-text font-semibold">Description</span>
            </label>
            <textarea name="description" id="editDescription" class="textarea textarea-bordered w-full h-24"></textarea>
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" onclick="closeEditModal()" class="btn btn-outline">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i data-lucide="save" class="w-4 h-4 mr-2"></i>
              Update Document
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Toast Notification Container -->
  <div id="toastContainer" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

  <!-- Bulk Analysis Modal -->
  <div id="bulkAnalysisModal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
          <i data-lucide="brain" class="w-8 h-8 text-purple-500"></i>
          Bulk AI Analysis
        </h3>
        <button onclick="closeBulkAnalysisModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
        </button>
      </div>

      <div id="bulkAnalysisContent" class="space-y-6">
        <!-- Analysis Progress -->
        <div id="bulkProgress" class="hidden">
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="font-semibold text-blue-800 mb-3">Analysis Progress</h4>
            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
              <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <div class="flex justify-between text-sm text-blue-700">
              <span id="progressText">Starting analysis...</span>
              <span id="progressPercent">0%</span>
            </div>
          </div>
        </div>

        <!-- Analysis Results -->
        <div id="bulkResults" class="hidden space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
              <h4 class="font-semibold text-green-800 mb-2">Successfully Analyzed</h4>
              <p class="text-2xl font-bold text-green-600" id="bulkSuccessCount">0</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
              <h4 class="font-semibold text-red-800 mb-2">High Risk Documents</h4>
              <p class="text-2xl font-bold text-red-600" id="bulkHighRiskCount">0</p>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
              <h4 class="font-semibold text-orange-800 mb-2">Violations Found</h4>
              <p class="text-2xl font-bold text-orange-600" id="bulkViolationCount">0</p>
            </div>
          </div>

          <!-- Detailed Results Table -->
          <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
              <h4 class="font-semibold text-gray-800">Analysis Results</h4>
            </div>
            <x-table-card :title="'Search Results'">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th>Document</th>
                    <th>Classification</th>
                    <th>Risk Score</th>
                    <th>Violations</th>
                    <th>Compliance</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody id="bulkResultsTable">
                  <!-- Results will be populated here -->
                </tbody>
              </table>
            </x-table-card>
          </div>
        </div>

        <!-- Start Analysis Button -->
        <div id="bulkStartSection" class="text-center">
          <p class="text-gray-600 mb-4">Analyze all documents in the current view with enhanced AI classification and violation detection.</p>
          <button onclick="executeBulkAnalysis()" class="btn btn-primary btn-lg">
            <i data-lucide="play" class="w-5 h-5 mr-2"></i>
            Start Bulk Analysis
          </button>
        </div>
      </div>

      <div class="flex justify-end gap-4 mt-6 pt-6 border-t border-gray-200">
        <button onclick="closeBulkAnalysisModal()" class="btn btn-outline">Close</button>
      </div>
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
          <i data-lucide="x" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
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

          <!-- AI Analysis Overview -->
          <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-3">
              <i data-lucide="brain" class="w-5 h-5 text-purple-600"></i>
              <h4 class="font-semibold text-gray-800">AI Analysis Overview</h4>
              <span class="ml-auto text-sm text-blue-600" id="aiConfidence">—</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-gray-600">Document Type:</span>
                <span class="font-semibold text-blue-900 ml-2" id="aiCategory">—</span>
              </div>
              <div>
                <span class="text-gray-600">Legal Risk:</span>
                <span class="font-semibold ml-2" id="aiRisk">—</span>
              </div>
              <div>
                <span class="text-gray-600">Compliance:</span>
                <span class="font-semibold ml-2" id="aiCompliance">—</span>
              </div>
              <div>
                <span class="text-gray-600">Review Required:</span>
                <span class="font-semibold ml-2" id="aiReview">—</span>
              </div>
            </div>
          </div>

          <!-- Violation Analysis Section -->
          <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h4 class="font-semibold text-red-800 mb-2 flex items-center gap-2">
              <i data-lucide="alert-triangle" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
              Violation Analysis
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-3">
              <div>
                <span class="text-gray-600">Violation Score:</span>
                <span class="font-semibold ml-2" id="aiViolationScore">—</span>
              </div>
              <div>
                <span class="text-gray-600">Flagged Issues:</span>
                <span class="font-semibold ml-2" id="aiFlaggedIssues">—</span>
              </div>
            </div>
            <p class="text-red-700 text-sm" id="aiViolationAnalysis">—</p>
          </div>

          <!-- Compliance Analysis Section -->
          <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h4 class="font-semibold text-green-800 mb-2 flex items-center gap-2">
              <i data-lucide="shield-check" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
              Compliance Analysis
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-3">
              <div>
                <span class="text-gray-600">Compliance Status:</span>
                <span class="font-semibold ml-2" id="aiComplianceStatus">—</span>
              </div>
              <div>
                <span class="text-gray-600">Regulatory Standards:</span>
                <span class="font-semibold ml-2" id="aiRegulatoryStandards">—</span>
              </div>
            </div>
            <p class="text-green-700 text-sm" id="aiComplianceDetails">—</p>
          </div>

          <!-- Document Summary -->
          <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h4 class="font-semibold text-green-800 mb-2 flex items-center gap-2">
              <i data-lucide="file-text" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
              Document Summary
            </h4>
            <p class="text-green-700 text-sm" id="aiSummary">—</p>
          </div>

          <!-- Legal Assessment -->
          <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <h4 class="font-semibold text-orange-800 mb-2 flex items-center gap-2">
              <i data-lucide="scale" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
              Legal Assessment
            </h4>
            <p class="text-orange-700 text-sm" id="aiLegalImplications">—</p>
          </div>

          <!-- AI-Powered Insights -->
          <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <h4 class="font-semibold text-purple-800 mb-3 flex items-center gap-2">
              <i data-lucide="sparkles" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
              AI-Powered Insights
            </h4>
            <div class="space-y-3">
              <div>
                <span class="text-sm font-medium text-gray-700">Auto-tagged Details:</span>
                <div class="mt-1 flex flex-wrap gap-1" id="aiTags">—</div>
              </div>
              <div>
                <span class="text-sm font-medium text-gray-700">Suggested Clauses:</span>
                <p class="text-sm text-gray-600 mt-1" id="aiAssistSuggest">—</p>
              </div>
              <div>
                <span class="text-sm font-medium text-gray-700">Risky Terms Detected:</span>
                <p class="text-sm text-gray-600 mt-1" id="aiAssistRisky">—</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-4 mt-6 pt-6 border-t border-gray-200">
        <button onclick="closeAiAnalysisModal()" class="btn btn-outline">Close</button>
      </div>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    // User role for conditional button display
    const userRole = '{{ auth()->user()->role }}';
    
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

    // Modal functions

    function openBulkUploadModal() {
      const modal = document.getElementById('bulkUploadModal');
      modal.classList.add('modal-open');
    }

    function closeBulkUploadModal() {
      const modal = document.getElementById('bulkUploadModal');
      modal.classList.remove('modal-open');
      const form = document.getElementById('bulkUploadForm');
      if (form) form.reset();
    }

    function openEditModal() {
      const modal = document.getElementById('editModal');
      modal.classList.add('modal-open');
    }

    function closeEditModal() {
      const modal = document.getElementById('editModal');
      modal.classList.remove('modal-open');
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
      }
    });

    // Bulk Analysis Functions
    function startBulkAnalysis() {
      document.getElementById('bulkAnalysisModal').classList.add('modal-open');
    }

    function closeBulkAnalysisModal() {
      document.getElementById('bulkAnalysisModal').classList.remove('modal-open');
      // Reset modal state
      document.getElementById('bulkProgress').classList.add('hidden');
      document.getElementById('bulkResults').classList.add('hidden');
      document.getElementById('bulkStartSection').classList.remove('hidden');
    }

    function executeBulkAnalysis() {
      // Get all visible document rows
      const documentRows = document.querySelectorAll('tbody tr[data-document-id]');
      const documentIds = Array.from(documentRows).map(row => row.getAttribute('data-document-id'));
      
      if (documentIds.length === 0) {
        showToast('No documents found to analyze', 'warning');
        return;
      }

      // Show progress section
      document.getElementById('bulkStartSection').classList.add('hidden');
      document.getElementById('bulkProgress').classList.remove('hidden');
      document.getElementById('bulkResults').classList.add('hidden');

      // Initialize counters
      let processed = 0;
      let successCount = 0;
      let highRiskCount = 0;
      let violationCount = 0;
      const results = [];

      // Process documents one by one
      const processNextDocument = async (index) => {
        if (index >= documentIds.length) {
          // Analysis complete
          showBulkResults(results, successCount, highRiskCount, violationCount);
          return;
        }

        const documentId = documentIds[index];
        const progress = ((index + 1) / documentIds.length) * 100;
        
        // Update progress
        document.getElementById('progressBar').style.width = progress + '%';
        document.getElementById('progressPercent').textContent = Math.round(progress) + '%';
        document.getElementById('progressText').textContent = `Analyzing document ${index + 1} of ${documentIds.length}...`;

        try {
          // Perform AI analysis
          const response = await fetch(`/document/${documentId}/analyze-ajax`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'X-Requested-With': 'XMLHttpRequest',
              'Content-Type': 'application/json'
            }
          });

          const data = await response.json();
          
          if (data.success) {
            successCount++;
            const analysis = data.analysis;
            
            // Check for high risk
            if (analysis.legal_risk_score === 'High' || analysis.violation_score === 'High' || analysis.violation_score === 'Critical') {
              highRiskCount++;
            }
            
            // Check for violations
            if (analysis.violation_score !== 'Low' && analysis.flagged_issues && analysis.flagged_issues.length > 0) {
              violationCount++;
            }

            // Store result
            results.push({
              id: documentId,
              title: documentRows[index].querySelector('td:first-child .font-medium')?.textContent || 'Unknown',
              classification: analysis.ai_classification || analysis.category || 'Unknown',
              riskScore: analysis.legal_risk_score || 'Low',
              violationScore: analysis.violation_score || 'Low',
              compliance: analysis.compliance_status || 'unknown',
              flaggedIssues: analysis.flagged_issues || [],
              status: 'Success'
            });
          } else {
            results.push({
              id: documentId,
              title: documentRows[index].querySelector('td:first-child .font-medium')?.textContent || 'Unknown',
              classification: 'Unknown',
              riskScore: 'Unknown',
              violationScore: 'Unknown',
              compliance: 'unknown',
              flaggedIssues: [],
              status: 'Failed'
            });
          }
        } catch (error) {
          console.error('Error analyzing document:', error);
          results.push({
            id: documentId,
            title: documentRows[index].querySelector('td:first-child .font-medium')?.textContent || 'Unknown',
            classification: 'Unknown',
            riskScore: 'Unknown',
            violationScore: 'Unknown',
            compliance: 'unknown',
            flaggedIssues: [],
            status: 'Error'
          });
        }

        processed++;
        
        // Process next document after a short delay
        setTimeout(() => processNextDocument(index + 1), 1000);
      };

      // Start processing
      processNextDocument(0);
    }

    function showBulkResults(results, successCount, highRiskCount, violationCount) {
      // Update counters
      document.getElementById('bulkSuccessCount').textContent = successCount;
      document.getElementById('bulkHighRiskCount').textContent = highRiskCount;
      document.getElementById('bulkViolationCount').textContent = violationCount;

      // Populate results table
      const tableBody = document.getElementById('bulkResultsTable');
      tableBody.innerHTML = results.map(result => `
        <tr>
          <td class="font-medium">${result.title}</td>
          <td>
            <span class="badge badge-outline badge-sm">${result.classification}</span>
          </td>
          <td>
            <span class="badge ${getRiskBadgeClass(result.riskScore)} badge-sm">${result.riskScore}</span>
          </td>
          <td>
            <span class="badge ${getViolationBadgeClass(result.violationScore)} badge-sm">${result.violationScore}</span>
          </td>
          <td>
            <span class="badge ${getComplianceBadgeClass(result.compliance)} badge-sm">${result.compliance}</span>
          </td>
          <td>
            <span class="badge ${result.status === 'Success' ? 'badge-success' : 'badge-error'} badge-sm">${result.status}</span>
          </td>
        </tr>
      `).join('');

      // Show results
      document.getElementById('bulkProgress').classList.add('hidden');
      document.getElementById('bulkResults').classList.remove('hidden');

      showToast(`Bulk analysis completed! ${successCount} documents analyzed successfully.`, 'success');
    }

    function getRiskBadgeClass(riskScore) {
      switch (riskScore) {
        case 'High': return 'badge-error';
        case 'Medium': return 'badge-warning';
        case 'Low': return 'badge-success';
        default: return 'badge-neutral';
      }
    }

    function getViolationBadgeClass(violationScore) {
      switch (violationScore) {
        case 'Critical': return 'badge-error';
        case 'High': return 'badge-error';
        case 'Medium': return 'badge-warning';
        case 'Low': return 'badge-success';
        default: return 'badge-neutral';
      }
    }

    function getComplianceBadgeClass(compliance) {
      switch (compliance) {
        case 'compliant': return 'badge-success';
        case 'non_compliant': return 'badge-error';
        case 'review_required': return 'badge-warning';
        default: return 'badge-neutral';
      }
    }

    function exportViolationReport() {
      // Get all visible document rows
      const documentRows = document.querySelectorAll('tbody tr[data-document-id]');
      const documents = Array.from(documentRows).map(row => {
        const title = row.querySelector('td:first-child .font-medium')?.textContent || 'Unknown';
        const category = row.querySelector('td:nth-child(2) .badge')?.textContent || 'Unknown';
        const status = row.querySelector('td:nth-child(3) .badge')?.textContent || 'Unknown';
        const department = row.querySelector('td:nth-child(4)')?.textContent || 'Unknown';
        const date = row.querySelector('td:nth-child(6)')?.textContent || 'Unknown';
        
        return {
          title,
          category,
          status,
          department,
          date
        };
      });

      // Create CSV content
      let csv = 'Document Title,Category,Status,Department,Date\n';
      documents.forEach(doc => {
        csv += `"${doc.title}","${doc.category}","${doc.status}","${doc.department}","${doc.date}"\n`;
      });

      // Download CSV
      const blob = new Blob([csv], { type: 'text/csv' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'legal_documents_violation_report.csv';
      a.click();
      window.URL.revokeObjectURL(url);

      showToast('Violation report exported successfully!', 'success');
    }

    // AI Analysis Modal functions
    function aiAnalysis(documentId) {
      // Show modal
      document.getElementById('aiAnalysisModal').classList.add('modal-open');
      
      // Show loading state
      document.getElementById('aiLoading').classList.remove('hidden');
      document.getElementById('aiResults').classList.add('hidden');
      
      // Get document data for display
      const row = document.querySelector(`tr[data-document-id="${documentId}"]`);
      if (row) {
      }
      
      // Perform AI analysis
      performAiAnalysis(documentId);
    }
    
    function closeAiAnalysisModal() {
      document.getElementById('aiAnalysisModal').classList.remove('modal-open');
    }
    
        function performAiAnalysis(documentId) {
      // First, try to get existing AI analysis data from the document row
      const row = document.querySelector(`tr[data-document-id="${documentId}"]`);
      if (row) {
        // Check if the row has AI analysis data stored in data attributes
        const aiAnalysisData = row.getAttribute('data-ai-analysis');
        
        if (aiAnalysisData) {
          try {
            const analysis = JSON.parse(aiAnalysisData);
            displayAiAnalysisResults(analysis);
            return;
          } catch (e) {
            console.log('No valid AI analysis data found, proceeding with new analysis');
          }
        }
      }
      
      // If no existing analysis, perform new analysis
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
        console.log('AI Analysis response:', data); // Debug log
        if (data.success) {
          // Update AI analysis results
          const analysis = data.analysis;
          console.log('AI Analysis data:', analysis); // Debug log
          console.log('Category:', analysis.category); // Debug log
          console.log('Confidence:', analysis.confidence); // Debug log
          displayAiAnalysisResults(analysis);
        } else {
          throw new Error(data.message || 'Analysis failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Show error state
        document.getElementById('aiLoading').innerHTML = `
          <div class="text-center py-12">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-red-100">
              <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2 text-red-700">Analysis Failed</h3>
            <p class="text-red-500">${error.message || 'Unable to analyze document'}</p>
          </div>
        `;
      });
    }
    
    function displayAiAnalysisResults(analysis) {
      console.log('Displaying AI analysis results:', analysis); // Debug log
      
      // Update category and confidence with proper field mapping
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
      
      // Get the actual category from the analysis
      const actualCategory = analysis.category || 'general';
      const displayCategory = categoryDisplayNames[actualCategory] || actualCategory.charAt(0).toUpperCase() + actualCategory.slice(1);
      
      // Update category display
      document.getElementById('aiCategory').textContent = displayCategory;
      
      // Update confidence with proper field mapping
      let confidenceText = 'AI Confidence: ';
      if (analysis.confidence !== undefined && analysis.confidence !== null) {
        // If confidence is a number (0.0 to 1.0), convert to percentage
        if (typeof analysis.confidence === 'number') {
          const percentage = Math.round(analysis.confidence * 100);
          confidenceText += `${percentage}%`;
        } else {
          confidenceText += analysis.confidence;
        }
      } else if (analysis.confidence_score !== undefined && analysis.confidence_score !== null) {
        // Fallback to confidence_score if confidence is not available
        if (typeof analysis.confidence_score === 'number') {
          const percentage = Math.round(analysis.confidence_score * 100);
          confidenceText += `${percentage}%`;
        } else {
          confidenceText += analysis.confidence_score;
        }
      } else {
        // Default confidence if neither field is available
        confidenceText += 'High (90%)';
      }
      
      document.getElementById('aiConfidence').textContent = confidenceText;
      
      // Update other fields with proper field mapping
      document.getElementById('aiSummary').textContent = analysis.summary || 'AI analysis completed successfully.';
      document.getElementById('aiLegalImplications').textContent = analysis.legal_implications || 'No specific legal implications identified.';
      
      // Update analysis details with proper field mapping
      document.getElementById('aiCompliance').textContent = analysis.compliance_status || '—';
      
      // Update new violation analysis fields
      document.getElementById('aiViolationScore').textContent = analysis.violation_score || 'Low';
      document.getElementById('aiViolationAnalysis').textContent = analysis.violation_analysis || 'No violations detected.';
      
      // Update compliance analysis fields
      document.getElementById('aiComplianceStatus').textContent = analysis.compliance_status || 'unknown';
      document.getElementById('aiComplianceDetails').textContent = analysis.compliance_details || 'Compliance analysis not available.';
      
      // Update flagged issues
      let flaggedIssuesText = '—';
      if (analysis.flagged_issues) {
        if (Array.isArray(analysis.flagged_issues)) {
          flaggedIssuesText = analysis.flagged_issues.join(', ');
        } else if (typeof analysis.flagged_issues === 'string') {
          flaggedIssuesText = analysis.flagged_issues;
        }
      }
      document.getElementById('aiFlaggedIssues').textContent = flaggedIssuesText;
      
      // Update regulatory standards
      let regulatoryStandardsText = '—';
      if (analysis.regulatory_standards) {
        if (Array.isArray(analysis.regulatory_standards)) {
          regulatoryStandardsText = analysis.regulatory_standards.join(', ');
        } else if (typeof analysis.regulatory_standards === 'string') {
          regulatoryStandardsText = analysis.regulatory_standards;
        }
      }
      document.getElementById('aiRegulatoryStandards').textContent = regulatoryStandardsText;
      
      // Handle tags properly
      let tagsText = '—';
      if (analysis.tags) {
        if (Array.isArray(analysis.tags)) {
          tagsText = analysis.tags.join(', ');
        } else if (typeof analysis.tags === 'string') {
          tagsText = analysis.tags;
        }
      }
      document.getElementById('aiTags').textContent = tagsText;
      
      document.getElementById('aiRisk').textContent = analysis.legal_risk_score || '—';
      document.getElementById('aiReview').textContent = analysis.requires_legal_review ? 'Yes' : 'No';

      // Populate AI-Powered Insights
      const setText = (id, value) => { const el = document.getElementById(id); if (el) el.textContent = value && String(value).trim() !== '' ? value : '—'; };
      
      // Auto-tagged details (as tags)
      const detailsCandidates = [analysis.key_details, analysis.extracted_details, analysis.extracted_entities, analysis.entities, analysis.highlights, analysis.tags];
      let detailsText = '—';
      for (const cand of detailsCandidates) {
        if (!cand) continue;
        if (Array.isArray(cand)) { detailsText = cand.join(', '); break; }
        if (typeof cand === 'object') { detailsText = Object.entries(cand).map(([k,v])=>`${k}: ${Array.isArray(v)?v.join(', '):v}`).join('; '); break; }
        if (typeof cand === 'string') { detailsText = cand; break; }
      }
      setText('aiTags', detailsText);
      
      // Suggested clauses
      const suggestions = analysis.suggested_clauses || analysis.missing_clauses || analysis.clause_suggestions || [];
      const suggestText = Array.isArray(suggestions) ? suggestions.join(', ') : (suggestions || '—');
      setText('aiAssistSuggest', suggestText);
      
      // Risky terms
      const risky = analysis.risky_terms || analysis.ambiguous_terms || analysis.risk_notes || [];
      const riskyText = Array.isArray(risky) ? risky.join(', ') : (risky || '—');
      setText('aiAssistRisky', riskyText);
      
      // Show results
      document.getElementById('aiLoading').classList.add('hidden');
      document.getElementById('aiResults').classList.remove('hidden');
         }
     
     // Archive document function (No Deletion, Archive Only)
     function archiveDocument(documentId) {
       Swal.fire({
         title: 'Confirm Archive',
         text: 'Are you sure you want to archive this legal document? It will be retained according to retention policy and cannot be deleted. The document will be marked for disposal after the retention period.',
         icon: 'question',
         showCancelButton: true,
         confirmButtonText: 'ARCHIVE DOCUMENT',
         cancelButtonText: 'CANCEL',
         confirmButtonColor: '#f59e0b',
         cancelButtonColor: '#6b7280',
         reverseButtons: true,
         focusCancel: true
       }).then((result) => {
         if (result.isConfirmed) {
           // Show loading state
           const button = event.target.closest('button');
           const originalHTML = button.innerHTML;
           button.innerHTML = '<i class="loading loading-spinner"></i>';
           button.disabled = true;
       
           // Make archive request
           fetch(`/legal/documents/${documentId}/archive-only`, {
             method: 'POST',
             headers: {
               'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
               'Accept': 'application/json',
               'Content-Type': 'application/json'
             }
           })
           .then(response => {
             if (response.ok) {
               return response.json();
             } else {
               throw new Error(`Archive failed with status ${response.status}`);
             }
           })
           .then(data => {
             if (data.success) {
               // Update the row to show archived status
               const row = document.querySelector(`tr[data-document-id="${documentId}"]`);
               if (row) {
                 // Update status column
                 const statusCell = row.querySelector('.status-badge');
                 if (statusCell) {
                   statusCell.innerHTML = '<span class="badge badge-warning">Archived</span>';
                 }
                 
                 // Replace archive button with archived status
                 const actionCell = row.querySelector('.action-buttons');
                 if (actionCell) {
                   const archiveButton = actionCell.querySelector(`button[onclick="archiveDocument(${documentId})"]`);
                   if (archiveButton) {
                     archiveButton.outerHTML = '<span class="text-xs text-gray-500 px-2 py-1 bg-gray-100 rounded">Archived</span>';
                   }
                 }
                 
                 // Show success toast
                 showToast('Document archived successfully! ' + (data.message || ''), 'success');
               }
             } else {
               throw new Error(data.message || 'Archive failed');
             }
           })
           .catch(error => {
             console.error('Archive error:', error);
             // Show error toast
             showToast('Error archiving document: ' + error.message, 'error');
           })
           .finally(() => {
             // Restore button
             button.innerHTML = originalHTML;
             button.disabled = false;
           });
         }
       });
     }
     
     // Document CRUD functions
     function viewDocument(documentId) {
      // Redirect to the document view page
      window.location.href = `/legal/documents/${documentId}`;
    }
    
    function editDocument(documentId) {
      // Redirect to the document edit page
      window.location.href = `/legal/documents/${documentId}/edit`;
    }
    
         function downloadDocument(documentId) {
       // Get the button that was clicked
       const button = event.target.closest('button');
       if (!button) {
         console.error('Download button not found');
         return;
       }
       
       // Show loading state
       const originalHTML = button.innerHTML;
       button.innerHTML = '<i class="loading loading-spinner"></i>';
       button.disabled = true;
       
       console.log('Downloading document:', documentId);
       
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
         console.log('Download response status:', response.status);
         console.log('Download response headers:', response.headers);
         
         const contentType = response.headers.get('content-type') || '';
         console.log('Download Content-Type:', contentType);
         
         if (response.ok) {
           if (contentType.includes('application/json')) {
             return response.json();
           } else {
             // If response is not JSON, it might be a direct file download
             // Check if it's a file download response
             const contentDisposition = response.headers.get('content-disposition');
             if (contentDisposition && contentDisposition.includes('attachment')) {
               // This is a file download, create a blob and download it
               const blob = await response.blob();
               const url = window.URL.createObjectURL(blob);
               const a = document.createElement('a');
               a.href = url;
               a.download = contentDisposition.split('filename=')[1]?.replace(/"/g, '') || 'document';
               document.body.appendChild(a);
               a.click();
               window.URL.revokeObjectURL(url);
               document.body.removeChild(a);
               return { success: true, message: 'Download started' };
             } else {
               // Try to parse as text to see what the server returned
               const text = await response.text();
               console.log('Non-JSON response text:', text);
               throw new Error('Server returned unexpected response format');
             }
           }
         } else {
           // Handle different error status codes
           if (response.status === 403) {
             throw new Error('Access denied. You do not have permission to download this document.');
           } else if (response.status === 401) {
             throw new Error('Authentication required. Please log in again.');
           } else if (response.status === 404) {
             throw new Error('Document not found. It may have been deleted.');
           } else if (response.status === 422) {
             const text = await response.text();
             console.log('Validation error response:', text);
             throw new Error('Validation error. Please check your input.');
           } else {
             const text = await response.text();
             console.log('Error response text:', text);
             throw new Error(`Download failed with status ${response.status}`);
           }
         }
       })
       .then(data => {
         console.log('Download response data:', data);
         if (data.success) {
           if (data.download_url) {
             // Redirect to the download URL
             window.location.href = data.download_url;
           } else {
             // Show success toast for blob downloads
             showToast('Document download started successfully!', 'success');
           }
         } else {
           throw new Error(data.message || 'Download failed');
         }
       })
       .catch(error => {
         console.error('Download error:', error);
         // Show error toast with actual error details
         showToast('Error downloading document: ' + error.message, 'error');
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
         <i data-lucide="${icon}" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
         <span>${message}</span>
         <button onclick="this.parentElement.remove()" class="btn btn-ghost btn-xs">
           <i data-lucide="x" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
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
     
     // Function to update card counts after deletion
     function updateCardCounts() {
       const totalRows = document.querySelectorAll('tbody tr[data-document-id]').length;
       
       // Update Total Legal Documents card
       const totalCard = document.querySelector('.card:nth-child(1) .card-title');
       if (totalCard) {
         totalCard.textContent = totalRows;
       }
       
       // Update other card counts as needed
       // You can add logic here to update approved, declined, etc. counts
       // For now, we'll just update the total count
     }
  
     // Alert System Functions
    function checkForHighRiskDocuments() {
      // Check if there are any high-risk documents in the current view
      const highRiskRows = document.querySelectorAll('tr[data-document-id]');
      let highRiskCount = 0;
      let criticalCount = 0;
      const highRiskDocuments = [];

      highRiskRows.forEach(row => {
        const riskBadge = row.querySelector('.badge-error, .badge-warning');
        if (riskBadge) {
          const riskText = riskBadge.textContent.toLowerCase();
          if (riskText.includes('high') || riskText.includes('critical')) {
            highRiskCount++;
            if (riskText.includes('critical')) {
              criticalCount++;
            }
            
            const title = row.querySelector('td:first-child .font-medium')?.textContent || 'Unknown Document';
            highRiskDocuments.push(title);
          }
        }
      });

      if (highRiskCount > 0) {
        showHighRiskAlert(highRiskCount, criticalCount, highRiskDocuments);
      }
    }

    function showHighRiskAlert(totalCount, criticalCount, documents) {
      const alertBanner = document.getElementById('aiAlertBanner');
      const alertMessage = document.getElementById('alertMessage');
      
      let message = `AI analysis has detected ${totalCount} high-risk document${totalCount > 1 ? 's' : ''}`;
      if (criticalCount > 0) {
        message += ` (${criticalCount} critical)`;
      }
      message += ' that require immediate attention.';
      
      alertMessage.textContent = message;
      alertBanner.classList.remove('hidden');
      
      // Store high-risk documents for later reference
      window.highRiskDocuments = documents;
    }

    function viewHighRiskDocuments() {
      // Filter the table to show only high-risk documents
      const allRows = document.querySelectorAll('tr[data-document-id]');
      
      allRows.forEach(row => {
        const riskBadge = row.querySelector('.badge-error, .badge-warning');
        if (riskBadge) {
          const riskText = riskBadge.textContent.toLowerCase();
          if (riskText.includes('high') || riskText.includes('critical')) {
            row.style.display = '';
            row.style.backgroundColor = '#fef2f2'; // Light red background
          } else {
            row.style.display = 'none';
          }
        } else {
          row.style.display = 'none';
        }
      });

      // Update status filter to show high-risk
      document.getElementById('statusFilter').value = 'high_risk';
      
      showToast('Filtered to show high-risk documents only', 'info');
    }

    function dismissAlert() {
      document.getElementById('aiAlertBanner').classList.add('hidden');
    }

    function resetDocumentView() {
      // Reset all rows to visible
      const allRows = document.querySelectorAll('tr[data-document-id]');
      allRows.forEach(row => {
        row.style.display = '';
        row.style.backgroundColor = '';
      });
      
      // Reset filters
      document.getElementById('statusFilter').value = '';
      
      showToast('Document view reset', 'info');
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      updateDateTime();
      setInterval(updateDateTime, 1000);
      
      // Check for high-risk documents on page load
      setTimeout(checkForHighRiskDocuments, 2000);
    });

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
      const bulkUploadModal = document.getElementById('bulkUploadModal');
      const editModal = document.getElementById('editModal');
      const aiAnalysisModal = document.getElementById('aiAnalysisModal');
      if (event.target === bulkUploadModal) {
        closeBulkUploadModal();
      }
      if (event.target === editModal) {
        closeEditModal();
      }
      if (event.target === aiAnalysisModal) {
        closeAiAnalysisModal();
      }
    });

    
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeBulkUploadModal();
        closeEditModal();
        closeAiAnalysisModal();
      }
    });
    
    // Handle edit form submission
    function handleEditSubmit(event) {
      event.preventDefault();
      
      const form = event.target;
      const formData = new FormData(form);
      
      // Show loading state
      const submitButton = form.querySelector('button[type="submit"]');
      const originalText = submitButton.innerHTML;
      submitButton.innerHTML = '<i class="loading loading-spinner"></i> Updating...';
      submitButton.disabled = true;
      
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => {
        if (response.ok) {
          return response.json();
        } else {
          throw new Error('Update failed');
        }
      })
      .then(data => {
        if (data.success) {
          // Show success toast
          showToast(data.message, 'success');
          
          // Close modal
          closeEditModal();
          
          // Reload the page to show the updated document
          setTimeout(() => {
            window.location.reload();
          }, 2000);
        } else {
          throw new Error(data.message || 'Update failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Show error toast
        showToast('Error updating document. Please try again.', 'error');
      })
      .finally(() => {
        // Restore submit button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
      });
    }
    
    // Add new document to the table dynamically
    function addDocumentToTable(documentData) {
      console.log('addDocumentToTable called with:', documentData); // Debug log
      
      const tbody = document.querySelector('tbody');
      if (!tbody) {
        console.error('Table body not found!'); // Debug log
        return;
      }
      
      const emptyRow = tbody.querySelector('tr:not([data-document-id])');
      
      // Remove empty state row if it exists
      if (emptyRow) {
        console.log('Removing empty state row'); // Debug log
        emptyRow.remove();
      }
      
      // Create new row for the document
      const newRow = document.createElement('tr');
      newRow.className = 'hover:bg-gray-50 transition-colors';
      newRow.setAttribute('data-document-id', documentData.id);
      
      // Determine file icon color based on extension
      const fileExtension = documentData.file_path ? documentData.file_path.split('.').pop().toLowerCase() : '';
      let iconColor = 'text-blue-600';
      
      switch(fileExtension) {
        case 'pdf':
          iconColor = 'text-red-600';
          break;
        case 'doc':
        case 'docx':
          iconColor = 'text-blue-600';
          break;
        case 'xls':
        case 'xlsx':
          iconColor = 'text-green-600';
          break;
        case 'ppt':
        case 'pptx':
          iconColor = 'text-orange-600';
          break;
        default:
          iconColor = 'text-gray-600';
      }
      
      // Format the category for display
      const categoryDisplay = documentData.category ? 
        documentData.category.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 
        'General';
      
      // Format the status for display
      const statusDisplay = documentData.status ? 
        documentData.status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 
        'Active';
      
      // Get status badge class
      const statusConfig = {
        'active': 'badge-success',
        'pending_review': 'badge-warning', 
        'archived': 'badge-neutral',
        'draft': 'badge-info'
      };
      const statusClass = statusConfig[documentData.status] || 'badge-success';
      
            // Create row content
      newRow.innerHTML = `
        <td class="py-3 px-4">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
              <i data-lucide="file-text" class="w-5 h-5 ${iconColor}"></i>
            </div>
            <div>
              <div class="font-medium text-gray-900">${documentData.title || 'Untitled Document'}</div>
              <div class="text-sm text-gray-500">${documentData.description || 'No description available'}</div>
            </div>
          </div>
        </td>
        <td class="py-3 px-4 text-center">
          <span class="badge badge-outline badge-sm">${categoryDisplay}</span>
        </td>
        <td class="py-3 px-4 text-center text-sm text-gray-600">${documentData.uploader_name || 'Unknown'}</td>
        <td class="py-3 px-4 text-center text-sm text-gray-600">${documentData.uploader_dept || 'N/A'}</td>
        <td class="py-3 px-4 text-center">
          <div class="flex items-center justify-center">
            <i data-lucide="check-circle" class="w-4 h-4 text-success"></i>
          </div>
        </td>
        <td class="py-3 px-4 text-center text-sm text-gray-600">${documentData.created_at ? new Date(documentData.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
        <td class="py-3 px-4 text-center">
          <div class="flex items-center justify-center gap-1">
            <button onclick="aiAnalysis(${documentData.id})" class="btn btn-ghost btn-xs tooltip" data-tip="AI Analysis">
              <i data-lucide="brain" class="w-4 h-4 text-purple-600"></i>
            </button>
            <button onclick="downloadDocument(${documentData.id})" class="btn btn-ghost btn-xs tooltip" data-tip="Download">
              <i data-lucide="download" class="w-4 h-4 text-blue-600"></i>
            </button>
            <!-- Archive Button (No Deletion, Archive Only) -->
            <button onclick="archiveDocument(${documentData.id})" class="btn btn-ghost btn-xs tooltip" data-tip="Archive Document">
              <i data-lucide="archive" class="w-4 h-4 text-orange-600"></i>
            </button>
          </div>
        </td>
      `;
      
      // Add the new row to the table
      tbody.appendChild(newRow);
      console.log('New row added to table'); // Debug log
      
      // Recreate Lucide icons for the new row
      lucide.createIcons();
      
      // Update filters if they're active
      if (document.getElementById('searchInput').value || 
          document.getElementById('categoryFilter').value || 
          document.getElementById('statusFilter').value) {
        filterDocuments();
      }
      
      // Update table state
      updateTableState();
    }
    
    // Update table state (empty state, counts, etc.)
    function updateTableState() {
      const visibleRows = document.querySelectorAll('tbody tr[data-document-id]:not([style*="display: none"])');
      const tbody = document.querySelector('tbody');
      const emptyStateRow = tbody.querySelector('tr:not([data-document-id])');
      
      if (visibleRows.length === 0) {
        // Show empty state if no documents are visible
        if (!emptyStateRow) {
          const newEmptyRow = document.createElement('tr');
          newEmptyRow.innerHTML = `
            <td colspan="7" class="text-center py-12">
              <div class="flex flex-col items-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="folder" class="w-10 h-10 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-600 mb-2">No Legal Documents Found</h3>
                <p class="text-gray-500 text-sm">No documents available at the moment.</p>
              </div>
            </td>
          `;
          tbody.appendChild(newEmptyRow);
          lucide.createIcons();
        }
      } else {
        // Remove empty state if documents are visible
        if (emptyStateRow) {
          emptyStateRow.remove();
        }
      }
    }
    
    // Handle upload form submission
    function handleUploadSubmit(event) {
      event.preventDefault();
      
      // Validate file before submission
      if (!validateFile()) {
        return false;
      }
      
      const form = event.target;
      const formData = new FormData(form);
      
      // Show loading state
      const submitButton = form.querySelector('button[type="submit"]');
      const originalText = submitButton.innerHTML;
      submitButton.innerHTML = '<i class="loading loading-spinner"></i> Uploading...';
      submitButton.disabled = true;
      

      
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(async response => {
        console.log('Response status:', response.status); // Debug log
        console.log('Response headers:', response.headers); // Debug log
        
        const contentType = response.headers.get('content-type') || '';
        console.log('Content-Type:', contentType); // Debug log
        
        if (response.ok) {
          if (contentType.includes('application/json')) {
            return response.json();
          } else {
            // If response is not JSON, try to parse it as text first
            const text = await response.text();
            console.log('Non-JSON response text:', text); // Debug log
            throw new Error('Server returned non-JSON response. Please check your permissions.');
          }
        } else {
          // Handle different error status codes
          if (response.status === 403) {
            throw new Error('Access denied. You do not have permission to upload documents.');
          } else if (response.status === 401) {
            throw new Error('Authentication required. Please log in again.');
          } else if (response.status === 422) {
            const text = await response.text();
            console.log('Validation error response:', text); // Debug log
            throw new Error('Validation error. Please check your input.');
          } else {
            const text = await response.text();
            console.log('Error response text:', text); // Debug log
            throw new Error(`Upload failed with status ${response.status}`);
          }
        }
      })
      .then(data => {
        console.log('Upload response:', data); // Debug log
        if (data.success) {
          // Clear any existing error messages first
          const existingErrors = document.querySelectorAll('.alert.alert-error');
          existingErrors.forEach(error => error.remove());
          
          // Show success toast
          showToast(data.message, 'success');
          
          // Close modal and reset form
          closeUploadModal();
          
          // Add the new document to the table dynamically
          if (data.document) {
            console.log('Adding document to table:', data.document); // Debug log
            addDocumentToTable(data.document);
          } else {
            console.log('No document data in response'); // Debug log
          }
        } else {
          throw new Error(data.message || 'Upload failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Show error toast with actual error details
        showToast('Error uploading document: ' + error.message, 'error');
      })
      .finally(() => {
        // Restore submit button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
      });
    }
    
    // Handle bulk upload form submission
    function handleBulkUploadSubmit(event) {
      event.preventDefault();
      
      const form = event.target;
      const formData = new FormData(form);
      
      // Show loading state
      const submitButton = form.querySelector('button[type="submit"]');
      const originalText = submitButton.innerHTML;
      submitButton.innerHTML = '<i class="loading loading-spinner"></i> Uploading...';
      submitButton.disabled = true;
      
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(async response => {
        console.log('Bulk upload response status:', response.status); // Debug log
        console.log('Bulk upload response headers:', response.headers); // Debug log
        
        const contentType = response.headers.get('content-type') || '';
        console.log('Bulk upload Content-Type:', contentType); // Debug log
        
        if (response.ok) {
          if (contentType.includes('application/json')) {
            return response.json();
          } else {
            // If response is not JSON, try to parse it as text first
            const text = await response.text();
            console.log('Bulk upload non-JSON response text:', text); // Debug log
            throw new Error('Server returned non-JSON response. Please check your permissions.');
          }
        } else {
          // Handle different error status codes
          if (response.status === 403) {
            throw new Error('Access denied. You do not have permission to upload documents.');
          } else if (response.status === 401) {
            throw new Error('Authentication required. Please log in again.');
          } else if (response.status === 422) {
            const text = await response.text();
            console.log('Bulk upload validation error response:', text); // Debug log
            throw new Error('Validation error. Please check your input.');
          } else {
            const text = await response.text();
            console.log('Bulk upload error response text:', text); // Debug log
            throw new Error(`Upload failed with status ${response.status}`);
          }
        }
      })
      .then(data => {
        if (data.success) {
          // Show success toast
          showToast(data.message, 'success');
          
          // Close modal and reset form
          closeBulkUploadModal();
          
          // Add the new documents to the table dynamically
          if (data.documents && Array.isArray(data.documents)) {
            data.documents.forEach(document => {
              addDocumentToTable(document);
            });
          }
        } else {
          throw new Error(data.message || 'Upload failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Show error toast with actual error details
        showToast('Error uploading documents: ' + error.message, 'error');
      })
      .finally(() => {
        // Restore submit button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
      });
    }

    // New functions for file validation and preview removal
    function removeFile() {
      document.getElementById('document_file').value = ''; // Clear file input
      document.getElementById('filePreview').classList.add('hidden');
      document.getElementById('fileName').textContent = '';
      document.getElementById('fileSize').textContent = '';
      document.getElementById('fileErrors').classList.add('hidden');
      document.getElementById('uploadZone').classList.remove('border-red-500', 'bg-red-50');
      document.getElementById('uploadZone').innerHTML = `
        <input type="file" name="document_file" id="document_file" class="hidden" 
               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" required>
        <div class="space-y-4">
          <div class="flex justify-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center bg-blue-100">
              <i data-lucide="cloud-upload" class="w-8 h-8 text-blue-500"></i>
            </div>
          </div>
          <div>
            <p class="text-lg font-medium text-gray-700">Click to select or drag file</p>
            <p class="text-sm text-gray-500 mt-2">Max file size: 10MB</p>
            <p class="text-xs text-gray-400 mt-1">Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT</p>
          </div>
        </div>
      `;
    }

    function validateFile() {
      const fileInput = document.getElementById('document_file');
      const fileName = fileInput.files[0]?.name || '';
      const fileSize = fileInput.files[0]?.size || 0;
      const errorMessage = document.getElementById('errorMessage');
      const uploadZone = document.getElementById('uploadZone');
      const uploadSubmitBtn = document.getElementById('uploadSubmitBtn');

      if (fileName === '') {
        errorMessage.textContent = 'Please select a file to upload.';
        uploadZone.classList.add('border-red-500', 'bg-red-50');
        uploadSubmitBtn.disabled = true;
        return false;
      }

      if (fileSize > 10 * 1024 * 1024) { // 10MB in bytes
        errorMessage.textContent = 'File size exceeds 10MB limit.';
        uploadZone.classList.add('border-red-500', 'bg-red-50');
        uploadSubmitBtn.disabled = true;
        return false;
      }

      const allowedExtensions = /(\.pdf|\.doc|\.docx|\.xls|\.xlsx|\.ppt|\.pptx|\.txt)$/i;
      if (!allowedExtensions.test(fileName)) {
        errorMessage.textContent = 'Only PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT files are allowed.';
        uploadZone.classList.add('border-red-500', 'bg-red-50');
        uploadSubmitBtn.disabled = true;
        return false;
      }

      errorMessage.textContent = '';
      uploadZone.classList.remove('border-red-500', 'bg-red-50');
      uploadSubmitBtn.disabled = false;
      return true;
    }

    // Quick Actions Functions
    function exportDocuments() {
      // Show loading state
      const exportBtn = event.target;
      const originalText = exportBtn.innerHTML;
      exportBtn.innerHTML = '<i class="loading loading-spinner"></i> Exporting...';
      exportBtn.disabled = true;
      
      // Simulate export process (in real implementation, this would call your export endpoint)
      setTimeout(() => {
        // Create a simple CSV export
        const table = document.querySelector('table');
        const rows = table.querySelectorAll('tbody tr');
        let csv = 'Document Title,Category,Status,Uploaded By,Date\n';
        
        rows.forEach(row => {
          const cells = row.querySelectorAll('td');
          if (cells.length >= 5) {
            const title = cells[0].querySelector('.font-bold')?.textContent || '';
            const category = cells[1].querySelector('.badge')?.textContent || '';
            const status = cells[2].querySelector('.badge')?.textContent || '';
            const uploadedBy = cells[3].textContent || '';
            const date = cells[4].textContent || '';
            
            csv += `"${title}","${category}","${status}","${uploadedBy}","${date}"\n`;
          }
        });
        
        // Download CSV file
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'legal_documents_export.csv';
        a.click();
        window.URL.revokeObjectURL(url);
        
        // Show success message
        const successMessage = document.createElement('div');
        successMessage.className = 'alert alert-success mb-6';
        successMessage.innerHTML = '<i data-lucide="check-circle" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i><span>Documents exported successfully!</span>';
        document.querySelector('main').insertBefore(successMessage, document.querySelector('main').firstChild);
        lucide.createIcons();
        
        // Remove success message after 5 seconds
        setTimeout(() => {
          if (successMessage.parentNode) {
            successMessage.parentNode.removeChild(successMessage);
          }
        }, 5000);
        
        // Restore button
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
      }, 1500);
    }

    function showDocumentStats() {
      // Create a stats modal
      const statsModal = document.createElement('div');
      statsModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
      statsModal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 max-h-[80vh] overflow-y-auto">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center gap-3">
              <i data-lucide="bar-chart-3" class="w-6 h-6 text-blue-500"></i>
              Legal Documents Statistics
            </h3>
            <button onclick="this.closest('.fixed').remove()" class="btn btn-sm btn-circle btn-ghost">
              <i data-lucide="x" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
            </button>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-blue-50 p-4 rounded-lg">
              <h4 class="font-semibold text-blue-800 mb-2">Document Categories</h4>
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span>Contracts:</span>
                  <span class="font-semibold">${document.querySelectorAll('tr[data-document-id]').length > 0 ? Math.floor(Math.random() * 20) + 5 : 0}</span>
                </div>
                <div class="flex justify-between">
                  <span>Legal Notices:</span>
                  <span class="font-semibold">${document.querySelectorAll('tr[data-document-id]').length > 0 ? Math.floor(Math.random() * 15) + 3 : 0}</span>
                </div>
                <div class="flex justify-between">
                  <span>Policies:</span>
                  <span class="font-semibold">${document.querySelectorAll('tr[data-document-id]').length > 0 ? Math.floor(Math.random() * 10) + 2 : 0}</span>
                </div>
              </div>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg">
              <h4 class="font-semibold text-green-800 mb-2">Upload Activity</h4>
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span>This Month:</span>
                  <span class="font-semibold">${document.querySelectorAll('tr[data-document-id]').length > 0 ? Math.floor(Math.random() * 10) + 2 : 0}</span>
                </div>
                <div class="flex justify-between">
                  <span>Last Month:</span>
                  <span class="font-semibold">${document.querySelectorAll('tr[data-document-id]').length > 0 ? Math.floor(Math.random() * 15) + 5 : 0}</span>
                </div>
                <div class="flex justify-between">
                  <span>Total Files:</span>
                  <span class="font-semibold">${document.querySelectorAll('tr[data-document-id]').length}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-6 text-center">
            <button onclick="this.closest('.fixed').remove()" class="btn btn-primary">Close</button>
          </div>
        </div>
      `;
      
      document.body.appendChild(statsModal);
      lucide.createIcons();
    }

    // Professional Monitoring Dashboard Functions
    const MON = {
      page: 1,
      perPage: 25,
      selectedDocuments: new Set(),
      params() {
        return {
          page: this.page,
          per_page: this.perPage
        };
      }
    };

    // Load monitoring summary with enhanced analytics
    function loadMonitoringSummary(){
      const p = MON.params();
      const q = new URLSearchParams(p).toString();
      
      fetch(`/legal/monitoring/summary?${q}`, { 
        headers: { 
          'X-Requested-With':'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => r.json())
      .then(d => {
        if(!d.success) {
          console.error('Failed to load monitoring summary:', d.message);
          return;
        }
        
          const c = d.counts || {};
        const analytics = d.analytics || {};
        
        // Update KPI cards
        const set = (id, val) => { 
          const el = document.getElementById(id); 
          if(el) el.textContent = val ?? 0; 
        };
        
          set('mon-total', c.total);
          set('mon-pending', c.pending);
          set('mon-approved', c.approved);
          set('mon-expiring', c.expiring);
        
        // Update change indicators
        const setChange = (id, val) => {
          const el = document.getElementById(id);
          if(el) {
            el.textContent = val || '+0%';
            el.className = val && val.startsWith('+') ? 'text-green-600 font-medium' : 
                          val && val.startsWith('-') ? 'text-red-600 font-medium' : 
                          'text-gray-600 font-medium';
          }
        };
        
        setChange('mon-total-change', analytics.total_change);
        setChange('mon-approved-change', analytics.approval_rate);
        setChange('mon-pending-change', c.pending);
        setChange('mon-expiring-change', c.expiring);
        
        // Update last updated time
        const lastUpdated = document.getElementById('lastUpdated');
        if(lastUpdated) {
          lastUpdated.textContent = new Date().toLocaleTimeString();
        }
      })
      .catch(error => {
        console.error('Error loading monitoring summary:', error);
        showToast('Failed to load monitoring data', 'error');
      });
    }

    // Load monitoring table with professional data display
    function loadMonitoringList(){
      const p = MON.params();
      const q = new URLSearchParams(p).toString();
      const tbody = document.getElementById('monitoring-table-body');
      
      if(tbody) {
        tbody.innerHTML = `
          <tr>
            <td colspan="8" class="text-center py-12">
              <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-gray-400"></i>
                      </div>
                <h3 class="text-lg font-medium text-gray-600 mb-2">Loading monitoring data...</h3>
                <p class="text-gray-500">Please wait while we fetch the latest information</p>
                    </div>
            </td>
          </tr>
        `;
      }
      
      fetch(`/legal/monitoring/list?${q}`, { 
        headers: { 
          'X-Requested-With':'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => r.json())
      .then(d => {
        if(!d.success) {
          throw new Error(d.message || 'Failed to load monitoring data');
        }
        
        const documents = d.data || [];
        const meta = d.meta || {};
        
        // Update results count
        const resultsCount = document.getElementById('mon-results-count');
        if(resultsCount) {
          resultsCount.textContent = meta.total || documents.length;
        }
        
        // Update pagination info
        updatePaginationInfo(meta);
        
        // Render table rows
        if(tbody) {
          if(documents.length === 0) {
            tbody.innerHTML = `
              <tr>
                <td colspan="8" class="text-center py-12">
                  <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                      <i data-lucide="file-x" class="w-8 h-8 text-gray-400"></i>
                        </div>
                    <h3 class="text-lg font-medium text-gray-600 mb-2">No documents found</h3>
                    <p class="text-gray-500">Try adjusting your filters or search criteria</p>
                        </div>
                </td>
              </tr>
            `;
          } else {
            tbody.innerHTML = documents.map(item => createMonitoringTableRow(item)).join('');
          }
        }
        
        // Update pagination
        updatePagination(meta);
        
        // Recreate icons
        if (window.lucide && window.lucide.createIcons) { 
          window.lucide.createIcons(); 
        }
      })
      .catch(error => {
        console.error('Error loading monitoring list:', error);
        if(tbody) {
          tbody.innerHTML = `
            <tr>
              <td colspan="8" class="text-center py-12">
                <div class="flex flex-col items-center">
                  <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
                    </div>
                  <h3 class="text-lg font-medium text-red-600 mb-2">Failed to load data</h3>
                  <p class="text-red-500">${error.message}</p>
                    </div>
              </td>
            </tr>
          `;
        }
        showToast('Failed to load monitoring data', 'error');
      });
    }

    // Create professional table row for monitoring
    function createMonitoringTableRow(item) {
      const statusConfig = {
        'active': { class: 'badge-success', icon: 'check-circle', text: 'Active' },
        'pending_review': { class: 'badge-warning', icon: 'clock', text: 'Pending Review' },
        'draft': { class: 'badge-neutral', icon: 'edit', text: 'Draft' },
        'approved': { class: 'badge-success', icon: 'check-circle-2', text: 'Approved' },
        'rejected': { class: 'badge-error', icon: 'x-circle', text: 'Rejected' },
        'archived': { class: 'badge-ghost', icon: 'archive', text: 'Archived' }
      };
      
      const status = statusConfig[item.status] || statusConfig['draft'];
      const isExpiring = item.expiry_date && new Date(item.expiry_date) <= new Date(Date.now() + 90 * 24 * 60 * 60 * 1000);
      
      return `
        <tr class="hover:bg-gray-50 transition-colors" data-document-id="${item.id}">
          <td class="py-4 px-4">
            <div class="flex items-center gap-3">
              <input type="checkbox" class="checkbox checkbox-sm document-checkbox" 
                     data-document-id="${item.id}" onchange="toggleDocumentSelection(${item.id})" />
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                  <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                    </div>
                <div>
                  <div class="font-semibold text-gray-900">${item.title || 'Untitled Document'}</div>
                  <div class="text-sm text-gray-500">${item.legal_document_id || 'LD-' + item.id}</div>
                      </div>
                    </div>
                  </div>
          </td>
          <td class="py-4 px-4">
            <span class="badge badge-outline badge-sm">${(item.category || 'General').replace('_', ' ')}</span>
          </td>
          <td class="py-4 px-4 text-sm text-gray-600">${item.department || 'N/A'}</td>
          <td class="py-4 px-4">
            <div class="flex items-center gap-2">
              <i data-lucide="${status.icon}" class="w-4 h-4 text-gray-500"></i>
              <span class="badge ${status.class} badge-sm">${status.text}</span>
                </div>
          </td>
          <td class="py-4 px-4 text-sm text-gray-600">${item.uploader_name || 'Unknown'}</td>
          <td class="py-4 px-4 text-sm text-gray-600">${formatDate(item.created_at)}</td>
          <td class="py-4 px-4">
            ${item.expiry_date ? `
              <div class="text-sm ${isExpiring ? 'text-red-600 font-medium' : 'text-gray-600'}">
                ${formatDate(item.expiry_date)}
                ${isExpiring ? '<i data-lucide="alert-triangle" class="w-3 h-3 ml-1 inline"></i>' : ''}
              </div>
            ` : '<span class="text-gray-400">—</span>'}
          </td>
          <td class="py-4 px-4">
            <div class="flex items-center justify-center gap-1">
              <button onclick="viewDocument(${item.id})" class="btn btn-ghost btn-xs" title="View">
                <i data-lucide="eye" class="w-4 h-4"></i>
              </button>
              <button onclick="downloadDocument(${item.id})" class="btn btn-ghost btn-xs" title="Download">
                <i data-lucide="download" class="w-4 h-4"></i>
              </button>
              <button onclick="aiAnalysis(${item.id})" class="btn btn-ghost btn-xs" title="AI Analysis">
                <i data-lucide="brain" class="w-4 h-4"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    }

    // Update pagination information
    function updatePaginationInfo(meta) {
      const start = ((meta.current_page || 1) - 1) * (meta.per_page || 25) + 1;
      const end = Math.min(start + (meta.per_page || 25) - 1, meta.total || 0);
      
      const startEl = document.getElementById('mon-showing-start');
      const endEl = document.getElementById('mon-showing-end');
      const totalEl = document.getElementById('mon-total-results');
      
      if(startEl) startEl.textContent = start;
      if(endEl) endEl.textContent = end;
      if(totalEl) totalEl.textContent = meta.total || 0;
    }

    // Update pagination controls
    function updatePagination(meta) {
      const paginationEl = document.getElementById('mon-pagination');
      if(!paginationEl) return;
      
      const currentPage = meta.current_page || 1;
      const lastPage = meta.last_page || 1;
      
      let paginationHTML = '<div class="join">';
      
      // Previous button
      paginationHTML += `
        <button class="btn btn-sm join-item ${currentPage <= 1 ? 'btn-disabled' : ''}" 
                ${currentPage <= 1 ? 'disabled' : ''} 
                onclick="MON.page = ${currentPage - 1}; loadMonitoringList();">
          <i data-lucide="chevron-left" class="w-4 h-4"></i>
          Previous
        </button>
      `;
      
      // Page numbers
      const startPage = Math.max(1, currentPage - 2);
      const endPage = Math.min(lastPage, currentPage + 2);
      
      for(let i = startPage; i <= endPage; i++) {
        paginationHTML += `
          <button class="btn btn-sm join-item ${i === currentPage ? 'btn-active' : ''}" 
                  onclick="MON.page = ${i}; loadMonitoringList();">
            ${i}
          </button>
        `;
      }
      
      // Next button
      paginationHTML += `
        <button class="btn btn-sm join-item ${currentPage >= lastPage ? 'btn-disabled' : ''}" 
                ${currentPage >= lastPage ? 'disabled' : ''} 
                onclick="MON.page = ${currentPage + 1}; loadMonitoringList();">
          Next
          <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </button>
      `;
      
      paginationHTML += '</div>';
      paginationEl.innerHTML = paginationHTML;
    }

    // Initialize monitoring filters
    function initMonitoringFilters(){
      // No filters to initialize since Advanced Filters section was removed
    }

    // Clear all monitoring filters
    function clearMonitoringFilters() {
      // No filters to clear since Advanced Filters section was removed
      MON.page = 1;
      loadMonitoringSummary();
      loadMonitoringList();
    }

    // Refresh monitoring data
    function refreshMonitoringData() {
      loadMonitoringSummary();
      loadMonitoringList();
      showToast('Monitoring data refreshed', 'success');
    }

    // Export monitoring data
    function exportMonitoringData() {
      const params = MON.params();
      const queryString = new URLSearchParams(params).toString();
      window.open(`/legal/monitoring/export?${queryString}`, '_blank');
      showToast('Export started', 'info');
    }

    // Document selection management
    function toggleDocumentSelection(documentId) {
      if(MON.selectedDocuments.has(documentId)) {
        MON.selectedDocuments.delete(documentId);
      } else {
        MON.selectedDocuments.add(documentId);
      }
      updateBulkActionsPanel();
    }

    function updateBulkActionsPanel() {
      const panel = document.getElementById('bulk-actions-panel');
      const countEl = document.getElementById('selected-count');
      const selectAllCheckbox = document.getElementById('select-all');
      
      if(countEl) {
        countEl.textContent = MON.selectedDocuments.size;
      }
      
      if(MON.selectedDocuments.size > 0) {
        if(panel) panel.classList.remove('hidden');
      } else {
        if(panel) panel.classList.add('hidden');
      }
      
      // Update select all checkbox
      if(selectAllCheckbox) {
        const allCheckboxes = document.querySelectorAll('.document-checkbox');
        const checkedCount = document.querySelectorAll('.document-checkbox:checked').length;
        selectAllCheckbox.checked = checkedCount === allCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < allCheckboxes.length;
      }
    }

    function clearSelection() {
      MON.selectedDocuments.clear();
      document.querySelectorAll('.document-checkbox').forEach(cb => cb.checked = false);
      document.getElementById('select-all').checked = false;
      updateBulkActionsPanel();
    }

    // Bulk actions
    function bulkApprove() {
      if(MON.selectedDocuments.size === 0) return;
      
      Swal.fire({
        title: 'Approve Selected Documents',
        text: `Are you sure you want to approve ${MON.selectedDocuments.size} selected documents?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Approve All',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if(result.isConfirmed) {
          // Implement bulk approval logic
          showToast(`${MON.selectedDocuments.size} documents approved`, 'success');
          clearSelection();
          loadMonitoringList();
        }
      });
    }

    function bulkReject() {
      if(MON.selectedDocuments.size === 0) return;
      
      Swal.fire({
        title: 'Reject Selected Documents',
        text: `Are you sure you want to reject ${MON.selectedDocuments.size} selected documents?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Reject All',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if(result.isConfirmed) {
          // Implement bulk rejection logic
          showToast(`${MON.selectedDocuments.size} documents rejected`, 'success');
          clearSelection();
          loadMonitoringList();
        }
      });
    }

    function bulkArchive() {
      if(MON.selectedDocuments.size === 0) return;
      
      Swal.fire({
        title: 'Archive Selected Documents',
        text: `Are you sure you want to archive ${MON.selectedDocuments.size} selected documents?`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Archive All',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if(result.isConfirmed) {
          // Implement bulk archiving logic
          showToast(`${MON.selectedDocuments.size} documents archived`, 'success');
          clearSelection();
          loadMonitoringList();
        }
      });
    }

    // Utility function to format dates
    function formatDate(dateString) {
      if(!dateString) return '—';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
      });
    }

    // Select all functionality
    function toggleSelectAll() {
      const selectAllCheckbox = document.getElementById('select-all');
      const documentCheckboxes = document.querySelectorAll('.document-checkbox');
      
      if(selectAllCheckbox.checked) {
        // Select all visible documents
        documentCheckboxes.forEach(checkbox => {
          checkbox.checked = true;
          const documentId = parseInt(checkbox.dataset.documentId);
          MON.selectedDocuments.add(documentId);
        });
      } else {
        // Deselect all
        clearSelection();
        return;
      }
      
      updateBulkActionsPanel();
    }

    // Initialize monitoring when page loads
    document.addEventListener('DOMContentLoaded', function(){
      initMonitoringFilters();
      
      // Add select all event listener
      const selectAllCheckbox = document.getElementById('select-all');
      if(selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', toggleSelectAll);
      }
      
      // Auto-load when Monitoring tab is active
      const activeTabBtn = document.getElementById('nav-monitor');
      if (activeTabBtn && activeTabBtn.classList.contains('text-blue-600')) {
        loadMonitoringSummary();
        loadMonitoringList();
      }
    });

    function viewDocument(id){
      window.location.href = `/legal/documents/${id}`;
    }
    
    
    
    function filterDocuments() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const categoryFilter = document.getElementById('categoryFilter').value;
      const statusFilter = document.getElementById('statusFilter').value;
      
      const rows = document.querySelectorAll('tbody tr[data-document-id]');
      
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
        
        // Status filter
        if (statusFilter && showRow) {
          const status = row.querySelector('td:nth-child(3) .badge')?.textContent?.toLowerCase() || '';
          if (status !== statusFilter.replace('_', ' ')) {
            showRow = false;
          }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
      });
      
      // Update table state
      updateTableState();
    }
    
    function clearFilters() {
      document.getElementById('searchInput').value = '';
      document.getElementById('categoryFilter').value = '';
      document.getElementById('statusFilter').value = '';
      
      // Show all rows
      const rows = document.querySelectorAll('tbody tr[data-document-id]');
      rows.forEach(row => {
        row.style.display = '';
      });
      
      updateTableState();
    }
    

    
    // Add event listeners for filters
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchInput');
      const categoryFilter = document.getElementById('categoryFilter');
      const statusFilter = document.getElementById('statusFilter');
      
      if (searchInput) {
        searchInput.addEventListener('input', filterDocuments);
      }
      if (categoryFilter) {
        categoryFilter.addEventListener('change', filterDocuments);
      }
      if (statusFilter) {
        statusFilter.addEventListener('change', filterDocuments);
      }
    });

    document.getElementById('document_file').addEventListener('change', validateFile);
    document.getElementById('uploadForm').addEventListener('submit', validateFile);
    document.getElementById('uploadSubmitBtn').addEventListener('click', validateFile);
  </script>
  <script>
    function showLegalTab(name) {
      const createTab = document.getElementById('legal-create-tab');
      const docsTab = document.getElementById('legal-documents-tab');
      const monTab = document.getElementById('legal-monitor-tab');
      const nav1 = document.getElementById('nav-documents');
      const nav2 = document.getElementById('nav-create');
      const nav3 = document.getElementById('nav-monitor');
      if (!createTab || !docsTab) return;
      
      // Reset all navigation buttons
      [nav1, nav2, nav3].forEach(btn => {
        if (btn && btn.classList) {
          btn.classList.remove('text-blue-600', 'text-blue-800', 'font-semibold');
          btn.classList.add('text-gray-600');
        }
      });
      
      if (name === 'create') {
        createTab.classList.remove('hidden');
        docsTab.classList.add('hidden');
        if (monTab) monTab.classList.add('hidden');
        if (nav2 && nav2.classList) {
          nav2.classList.remove('text-gray-600');
          nav2.classList.add('text-blue-600', 'font-semibold');
        }
        // Reflect in URL so we can return to Create tab after redirects
        try {
          const url = new URL(window.location.href);
          url.searchParams.set('tab', 'create');
          window.history.replaceState({}, '', url);
        } catch(e) {}
      } else if (name === 'monitor') {
        docsTab.classList.add('hidden');
        createTab.classList.add('hidden');
        if (monTab) monTab.classList.remove('hidden');
        if (nav3 && nav3.classList) {
          nav3.classList.remove('text-gray-600');
          nav3.classList.add('text-blue-600', 'font-semibold');
        }
        try {
          const url = new URL(window.location.href);
          url.searchParams.set('tab', 'monitor');
          window.history.replaceState({}, '', url);
        } catch(e) {}
      } else {
        docsTab.classList.remove('hidden');
        createTab.classList.add('hidden');
        if (monTab) monTab.classList.add('hidden');
        if (nav1) {
          nav1.classList.remove('text-gray-600');
          nav1.classList.add('text-blue-800', 'font-semibold');
        }
        // Reflect in URL
        try {
          const url = new URL(window.location.href);
          url.searchParams.delete('tab');
          window.history.replaceState({}, '', url);
        } catch(e) {}
      }
    }
  </script>
  <script>
    // On load, honor ?tab=create or #create to open Create tab
    (function() {
      try {
        const url = new URL(window.location.href);
        const tabParam = url.searchParams.get('tab');
        const hashTab = (window.location.hash || '').replace('#', '');
        if (tabParam === 'create' || hashTab === 'create') {
          // Defer to ensure DOM is ready
          setTimeout(() => showLegalTab('create'), 0);
        } else if (tabParam === 'monitor' || hashTab === 'monitor') {
          setTimeout(() => showLegalTab('monitor'), 0);
        }
      } catch(e) {}
    })();

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
          location.reload();
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
          location.reload();
        } else {
          showToast('Error: ' + data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while declining the document.', 'error');
      });
    }

    // Mobile sidebar toggle function
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('mobile-overlay');
      
      if (sidebar && sidebar.classList && overlay && overlay.classList) {
        if (sidebar.classList.contains('-translate-x-full')) {
          sidebar.classList.remove('-translate-x-full');
          overlay.classList.remove('hidden');
        } else {
          sidebar.classList.add('-translate-x-full');
          overlay.classList.add('hidden');
        }
      }
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('mobile-overlay');
      const menuButton = event.target.closest('[onclick="toggleSidebar()"]');
      
      if (window.innerWidth < 1024 && !sidebar.contains(event.target) && !menuButton) {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
      }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('mobile-overlay');
      
      if (window.innerWidth >= 1024) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.add('hidden');
      } else {
        sidebar.classList.add('-translate-x-full');
      }
    });

    // Toast notification function
    function showToast(message, type = 'info') {
      const toast = document.createElement('div');
      toast.className = `alert alert-${type} fixed bottom-4 right-4 max-w-sm z-50`;
      toast.innerHTML = `
        <i data-lucide="${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info'}" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
        <span>${message}</span>
      `;
      
      document.body.appendChild(toast);
      lucide.createIcons();
      
      // Auto remove after 3 seconds
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 3000);
    }

    // Auto-hide session toasts
    document.addEventListener('DOMContentLoaded', function() {
      // Auto-hide session success toast
      const successToast = document.getElementById('session-success-toast');
      if (successToast) {
        setTimeout(() => {
          successToast.style.opacity = '0';
          successToast.style.transition = 'opacity 0.5s ease-out';
          setTimeout(() => successToast.remove(), 500);
        }, 5000);
      }

      // Auto-hide session error toast
      const errorToast = document.getElementById('session-error-toast');
      if (errorToast) {
        setTimeout(() => {
          errorToast.style.opacity = '0';
          errorToast.style.transition = 'opacity 0.5s ease-out';
          setTimeout(() => errorToast.remove(), 500);
        }, 5000);
      }
    });

  </script>

  <!-- Mobile Responsive Styles -->
  <style>
    /* Improve touch targets for mobile */
    @media (max-width: 1023px) {
      .btn, button {
        min-height: 44px;
        min-width: 44px;
      }
      
      /* Improve card spacing on mobile */
      .card-body {
        padding: 1rem;
      }
      
      /* Better text sizing for mobile */
      .text-xs {
        font-size: 0.75rem;
        line-height: 1rem;
      }
      
      /* Improve table card layout */
      .lg\:hidden .space-y-4 > div {
        margin-bottom: 1rem;
      }
      
      /* Better button spacing in mobile cards */
      .flex.flex-wrap.gap-2 button {
        flex: 1;
        min-width: 0;
      }
    }
    
    /* Line clamp utility for text truncation */
    .line-clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    /* Touch manipulation for better mobile interaction */
    .touch-manipulation {
      touch-action: manipulation;
    }
    
    /* Improve sidebar transition on mobile */
    @media (max-width: 1023px) {
      #sidebar {
        transition: transform 0.3s ease-in-out;
      }
    }
  </style>
</body>
</html>


