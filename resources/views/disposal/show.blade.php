<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Disposed Document Details - Soliera</title>
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
        <!-- Back button and title -->
        <div class="flex items-center mb-6">
          <a href="{{ route('disposal.index') }}" class="btn btn-ghost btn-sm mr-4" style="color: var(--color-regal-navy);">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>Back to Disposal History
          </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Document Information -->
          <div class="lg:col-span-2">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <div class="flex justify-between items-start mb-4">
                  <h2 class="card-title text-2xl" style="color: var(--color-charcoal-ink);">{{ $disposedDocument->document_title }}</h2>
                  <div class="badge badge-lg badge-error">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>
                    Disposed
                  </div>
                </div>

                @if($disposedDocument->document_description)
                  <div class="mb-4">
                    <h3 class="font-semibold text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">Description</h3>
                    <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $disposedDocument->document_description }}</p>
                  </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1" style="color: var(--color-charcoal-ink);">File Name</h3>
                    <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $disposedDocument->file_name ?? 'Unknown' }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1" style="color: var(--color-charcoal-ink);">File Type</h3>
                    <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $disposedDocument->file_type ?? 'Unknown' }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1" style="color: var(--color-charcoal-ink);">File Size</h3>
                    <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $disposedDocument->formatted_file_size }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1" style="color: var(--color-charcoal-ink);">Category</h3>
                    <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $disposedDocument->document_category ?? 'Uncategorized' }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1" style="color: var(--color-charcoal-ink);">Department</h3>
                    <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $disposedDocument->document_department ?? 'Unknown' }}</p>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-700 mb-1" style="color: var(--color-charcoal-ink);">Author</h3>
                    <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $disposedDocument->document_author ?? 'Unknown' }}</p>
                  </div>
                </div>

                <!-- AI Analysis -->
                @if($disposedDocument->ai_analysis && count($disposedDocument->ai_analysis) > 0)
                  <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-3" style="color: var(--color-charcoal-ink);">AI Analysis</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                      @if(isset($disposedDocument->ai_analysis['tags']))
                        <div class="mb-3">
                          <h4 class="text-sm font-semibold mb-2">Tags</h4>
                          <div class="flex flex-wrap gap-2">
                            @foreach($disposedDocument->ai_analysis['tags'] as $tag)
                              <span class="badge badge-outline badge-sm">{{ $tag }}</span>
                            @endforeach
                          </div>
                        </div>
                      @endif
                      
                      @if(isset($disposedDocument->ai_analysis['compliance_status']))
                        <div class="mb-3">
                          <h4 class="text-sm font-semibold mb-2">Compliance Status</h4>
                          <span class="badge badge-{{ $disposedDocument->ai_analysis['compliance_status'] === 'compliant' ? 'success' : 'error' }} badge-sm">
                            {{ ucfirst(str_replace('_', ' ', $disposedDocument->ai_analysis['compliance_status'])) }}
                          </span>
                        </div>
                      @endif
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>

          <!-- Disposal Information -->
          <div class="lg:col-span-1">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4" style="color: var(--color-charcoal-ink);">
                  <i data-lucide="trash-2" class="w-5 h-5 mr-2" style="color: var(--color-danger-red);"></i>
                  Disposal Information
                </h3>

                <div class="space-y-4">
                  <div>
                    <h4 class="font-semibold text-sm mb-1" style="color: var(--color-charcoal-ink);">Disposal Reason</h4>
                    <span class="badge {{ $disposedDocument->disposal_reason === 'auto_expired' ? 'badge-warning' : 'badge-info' }} badge-sm">
                      {{ $disposedDocument->disposal_reason_display }}
                    </span>
                  </div>

                  <div>
                    <h4 class="font-semibold text-sm mb-1" style="color: var(--color-charcoal-ink);">Previous Status</h4>
                    <p class="text-sm" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ ucfirst(str_replace('_', ' ', $disposedDocument->previous_status)) }}</p>
                  </div>

                  <div>
                    <h4 class="font-semibold text-sm mb-1" style="color: var(--color-charcoal-ink);">Disposed At</h4>
                    <p class="text-sm" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $disposedDocument->disposed_at->format('M d, Y H:i:s') }}</p>
                  </div>

                  <div>
                    <h4 class="font-semibold text-sm mb-1" style="color: var(--color-charcoal-ink);">Disposed By</h4>
                    <p class="text-sm" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $disposedDocument->disposer?->name ?? 'System' }}</p>
                  </div>

                  @if($disposedDocument->retention_until)
                    <div>
                      <h4 class="font-semibold text-sm mb-1" style="color: var(--color-charcoal-ink);">Retention Until</h4>
                      <p class="text-sm" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $disposedDocument->retention_until->format('M d, Y') }}</p>
                    </div>
                  @endif

                  @if($disposedDocument->retention_policy)
                    <div>
                      <h4 class="font-semibold text-sm mb-1" style="color: var(--color-charcoal-ink);">Retention Policy</h4>
                      <p class="text-sm" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $disposedDocument->retention_policy }}</p>
                    </div>
                  @endif

                  @if($disposedDocument->confidentiality_level)
                    <div>
                      <h4 class="font-semibold text-sm mb-1" style="color: var(--color-charcoal-ink);">Confidentiality Level</h4>
                      <span class="badge {{ $disposedDocument->confidentiality_badge_class }} badge-sm">
                        {{ ucfirst($disposedDocument->confidentiality_level) }}
                      </span>
                    </div>
                  @endif

                  @if($disposedDocument->ip_address)
                    <div>
                      <h4 class="font-semibold text-sm mb-1" style="color: var(--color-charcoal-ink);">IP Address</h4>
                      <p class="text-sm font-mono" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $disposedDocument->ip_address }}</p>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Lifecycle Log -->
        @if($disposedDocument->lifecycle_log && count($disposedDocument->lifecycle_log) > 0)
          <div class="mt-8">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <h3 class="card-title text-xl mb-6 flex items-center" style="color: var(--color-charcoal-ink);">
                  <i data-lucide="activity" class="w-6 h-6 mr-3" style="color: var(--color-regal-navy);"></i>
                  Document Lifecycle History
                </h3>

                <div class="timeline">
                  @foreach($disposedDocument->lifecycle_log as $index => $log)
                    @php
                      $stepIcons = [
                        'uploaded' => 'upload',
                        'sanitization_started' => 'file-search',
                        'sanitization_completed' => 'check-circle',
                        'sanitization_failed' => 'x-circle',
                        'classification_started' => 'brain',
                        'classification_completed' => 'check-circle',
                        'classification_failed' => 'x-circle',
                        'routing_decision_started' => 'git-branch',
                        'routed_to_module' => 'arrow-right',
                        'routed_to_fr' => 'building',
                        'routed_to_lm' => 'scale',
                        'routed_to_vm' => 'users',
                        'auto_scheduled_successfully' => 'calendar-check',
                        'manual_reservation_created' => 'calendar-plus',
                        'legal_case_created' => 'file-text',
                        'archived_non_actionable' => 'archive',
                        'archived_with_legal_flag' => 'flag',
                        'status_update' => 'refresh-cw',
                        'lifecycle_completed' => 'check-circle-2',
                        'auto_deleted_expired' => 'trash',
                        'manually_disposed' => 'trash'
                      ];
                      $icon = $stepIcons[$log['step']] ?? 'circle';
                      
                      $stepColors = [
                        'uploaded' => 'regal-navy',
                        'sanitization_completed' => 'modern-teal',
                        'classification_completed' => 'modern-teal',
                        'routed_to_module' => 'golden-ember',
                        'auto_scheduled_successfully' => 'modern-teal',
                        'legal_case_created' => 'golden-ember',
                        'lifecycle_completed' => 'modern-teal',
                        'sanitization_failed' => 'danger-red',
                        'classification_failed' => 'danger-red',
                        'auto_deleted_expired' => 'danger-red',
                        'manually_disposed' => 'danger-red'
                      ];
                      $color = $stepColors[$log['step']] ?? 'charcoal-ink';
                    @endphp
                    
                    <div class="flex items-start space-x-4 {{ !$loop->last ? 'mb-6' : '' }}">
                      <!-- Timeline Dot -->
                      <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center border-2" style="background-color: color-mix(in srgb, var(--color-{{ $color }}), white 85%); border-color: var(--color-{{ $color }});">
                          <i data-lucide="{{ $icon }}" class="w-5 h-5" style="color: var(--color-{{ $color }});"></i>
                        </div>
                        @if(!$loop->last)
                          <div class="w-0.5 h-12 mt-2" style="background-color: var(--color-snow-mist);"></div>
                        @endif
                      </div>
                      
                      <!-- Timeline Content -->
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                          <h4 class="font-semibold text-sm" style="color: var(--color-charcoal-ink);">
                            {{ ucfirst(str_replace('_', ' ', $log['step'])) }}
                          </h4>
                          <span class="text-xs" style="color: var(--color-charcoal-ink); opacity: 0.6;">
                            {{ \Carbon\Carbon::parse($log['timestamp'])->format('M d, Y H:i:s') }}
                          </span>
                        </div>
                        
                        @if(isset($log['details']) && !empty($log['details']))
                          <div class="mt-2 p-3 rounded-lg text-xs" style="background-color: color-mix(in srgb, var(--color-snow-mist), white 50%); color: var(--color-charcoal-ink);">
                            @foreach($log['details'] as $key => $value)
                              @if(is_string($value) || is_numeric($value))
                                <div class="mb-1">
                                  <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> 
                                  {{ $value }}
                                </div>
                              @endif
                            @endforeach
                          </div>
                        @endif
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        @endif
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
  </script>
</body>
</html>
