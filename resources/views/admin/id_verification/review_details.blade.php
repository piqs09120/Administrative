<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Review ID Verification - {{ $log->form_name }}</title>
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
                <!-- Back Button -->
                <div class="mb-4">
                    <a href="{{ route('id_verification.review_queue') }}" class="btn btn-ghost btn-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Back to Queue
                    </a>
                </div>

                <!-- Page Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-6 h-6 text-blue-600"></i>
                        ID Verification Review
                    </h2>
                    <div class="border-b border-gray-200 mt-3"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Visitor Information Card -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <i data-lucide="user" class="w-5 h-5 mr-2 text-blue-600"></i>
                                Visitor Information
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">Full Name</label>
                                    <p class="text-lg">{{ $log->form_name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">Email</label>
                                    <p class="text-lg">{{ $log->form_email }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">ID Type</label>
                                    <p class="text-lg">{{ strtoupper(str_replace('_', ' ', $log->form_id_type)) }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">ID Number</label>
                                    <p class="text-lg font-mono">{{ $log->form_id_number }}</p>
                                </div>
                                @if($log->form_date_of_birth)
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">Date of Birth</label>
                                    <p class="text-lg">{{ $log->form_date_of_birth->format('M d, Y') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- ID Document -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <i data-lucide="file-image" class="w-5 h-5 mr-2 text-blue-600"></i>
                                ID Document
                            </h3>
                            @if($log->id_document_path)
                                <div class="bg-gray-100 rounded-lg p-4">
                                    <img src="{{ Storage::url($log->id_document_path) }}" 
                                         alt="ID Document" 
                                         class="max-w-full h-auto rounded-lg border-2 border-gray-300"
                                         onclick="openImageModal(this.src)">
                                    <p class="text-xs text-gray-500 mt-2 text-center">Click to view full size</p>
                                </div>
                            @else
                                <p class="text-gray-500">No document uploaded</p>
                            @endif
                        </div>

                        <!-- Extracted Data vs Form Data -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <i data-lucide="git-compare" class="w-5 h-5 mr-2 text-blue-600"></i>
                                Data Comparison
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="table w-full">
                                    <thead>
                                        <tr>
                                            <th>Field</th>
                                            <th>Form Data</th>
                                            <th>Extracted Data</th>
                                            <th>Match</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="font-semibold">Name</td>
                                            <td>{{ $log->form_name }}</td>
                                            <td>{{ $log->extracted_name ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $nameScore = $log->component_scores['name'] ?? 0;
                                                @endphp
                                                <div class="badge {{ $nameScore >= 80 ? 'badge-success' : ($nameScore >= 60 ? 'badge-warning' : 'badge-error') }}">
                                                    {{ round($nameScore, 1) }}%
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-semibold">ID Number</td>
                                            <td class="font-mono">{{ $log->form_id_number }}</td>
                                            <td class="font-mono">{{ $log->extracted_id_number ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $idScore = $log->component_scores['id_number'] ?? 0;
                                                @endphp
                                                <div class="badge {{ $idScore >= 80 ? 'badge-success' : ($idScore >= 60 ? 'badge-warning' : 'badge-error') }}">
                                                    {{ round($idScore, 1) }}%
                                                </div>
                                            </td>
                                        </tr>
                                        @if($log->form_date_of_birth)
                                        <tr>
                                            <td class="font-semibold">Date of Birth</td>
                                            <td>{{ $log->form_date_of_birth->format('M d, Y') }}</td>
                                            <td>{{ $log->extracted_date_of_birth ? $log->extracted_date_of_birth->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $dobScore = $log->component_scores['date_of_birth'] ?? 0;
                                                @endphp
                                                <div class="badge {{ $dobScore >= 90 ? 'badge-success' : ($dobScore >= 60 ? 'badge-warning' : 'badge-error') }}">
                                                    {{ round($dobScore, 1) }}%
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Quality Check Results -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <i data-lucide="check-square" class="w-5 h-5 mr-2 text-blue-600"></i>
                                Quality Checks
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach(['resolution', 'sharpness', 'glare', 'edges_detected', 'tamper_detection', 'color_consistency'] as $check)
                                    @php
                                        $passed = ($log->quality_metrics[$check] ?? false);
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="{{ $passed ? 'check-circle' : 'x-circle' }}" 
                                           class="w-5 h-5 {{ $passed ? 'text-green-500' : 'text-red-500' }}"></i>
                                        <span class="text-sm">{{ ucfirst(str_replace('_', ' ', $check)) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if(!empty($log->quality_issues))
                                <div class="mt-4 p-3 bg-red-50 border-l-4 border-red-500 rounded">
                                    <h4 class="font-semibold text-red-800 mb-2">Issues Found:</h4>
                                    <ul class="list-disc list-inside text-sm text-red-700">
                                        @foreach($log->quality_issues as $issue)
                                            <li>{{ $issue }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- Mismatches -->
                        @if(!empty($log->mismatch_reasons))
                            <div class="bg-white rounded-xl shadow-lg p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                    <i data-lucide="alert-triangle" class="w-5 h-5 mr-2 text-yellow-600"></i>
                                    Discrepancies Found
                                </h3>
                                <div class="space-y-2">
                                    @foreach($log->mismatch_reasons as $reason)
                                        <div class="flex items-start gap-2 p-3 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                                            <i data-lucide="info" class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5"></i>
                                            <span class="text-sm text-yellow-800">{{ $reason }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Right Column: Scores & Actions -->
                    <div class="space-y-6">
                        <!-- Verification Scores -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Verification Scores</h3>
                            
                            <!-- Match Score -->
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-semibold">Match Score</span>
                                    <span class="text-2xl font-bold {{ $log->match_score >= 85 ? 'text-green-600' : ($log->match_score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ round($log->match_score, 1) }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full {{ $log->match_score >= 85 ? 'bg-green-600' : ($log->match_score >= 60 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                         style="width: {{ $log->match_score }}%"></div>
                                </div>
                            </div>

                            <!-- Overall Confidence -->
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-semibold">Overall Confidence</span>
                                    <span class="text-2xl font-bold {{ $log->overall_confidence >= 85 ? 'text-green-600' : ($log->overall_confidence >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ round($log->overall_confidence, 1) }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full {{ $log->overall_confidence >= 85 ? 'bg-green-600' : ($log->overall_confidence >= 60 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                         style="width: {{ $log->overall_confidence }}%"></div>
                                </div>
                            </div>

                            <!-- Extraction Confidence -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-semibold">Extraction Quality</span>
                                    <span class="text-2xl font-bold {{ $log->extraction_confidence >= 85 ? 'text-green-600' : ($log->extraction_confidence >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ round($log->extraction_confidence, 1) }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full {{ $log->extraction_confidence >= 85 ? 'bg-green-600' : ($log->extraction_confidence >= 60 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                         style="width: {{ $log->extraction_confidence }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Metadata -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Metadata</h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <span class="text-gray-600">Parse Method:</span>
                                    <span class="font-semibold ml-2">{{ strtoupper($log->parse_method) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Quality Passed:</span>
                                    <span class="ml-2">
                                        <div class="badge {{ $log->quality_passed ? 'badge-success' : 'badge-error' }} badge-sm">
                                            {{ $log->quality_passed ? 'Yes' : 'No' }}
                                        </div>
                                    </span>
                                </div>
                                @if($log->philid_verified)
                                <div>
                                    <span class="text-gray-600">PhilID Verified:</span>
                                    <span class="ml-2">
                                        <div class="badge badge-info badge-sm">✓ Verified</div>
                                    </span>
                                </div>
                                @endif
                                <div>
                                    <span class="text-gray-600">Submitted:</span>
                                    <span class="font-semibold ml-2">{{ $log->created_at->format('M d, Y H:i A') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">IP Address:</span>
                                    <span class="font-mono text-xs ml-2">{{ $log->ip_address }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Review Actions -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Review Actions</h3>
                            
                            <!-- Approve Form -->
                            <form action="{{ route('id_verification.approve', $log->id) }}" method="POST" class="mb-3">
                                @csrf
                                <textarea name="notes" class="textarea textarea-bordered w-full mb-2" 
                                          placeholder="Approval notes (optional)" rows="3"></textarea>
                                <button type="submit" class="btn btn-success w-full">
                                    <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                                    Approve & Generate QR Pass
                                </button>
                            </form>

                            <!-- Reject Form -->
                            <form action="{{ route('id_verification.reject', $log->id) }}" method="POST" class="mb-3">
                                @csrf
                                <textarea name="reason" class="textarea textarea-bordered w-full mb-2" 
                                          placeholder="Rejection reason (required)" rows="3" required></textarea>
                                <button type="submit" class="btn btn-error w-full">
                                    <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                                    Reject Verification
                                </button>
                            </form>

                            <!-- Request Info Form -->
                            <form action="{{ route('id_verification.request_info', $log->id) }}" method="POST">
                                @csrf
                                <textarea name="message" class="textarea textarea-bordered w-full mb-2" 
                                          placeholder="Information request message (required)" rows="3" required></textarea>
                                <button type="submit" class="btn btn-warning w-full">
                                    <i data-lucide="help-circle" class="w-4 h-4 mr-2"></i>
                                    Request More Information
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-box max-w-4xl">
            <button onclick="closeImageModal()" class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>
            <img id="modalImage" src="" alt="Full Size ID Document" class="w-full h-auto">
        </div>
    </div>

    @include('partials.soliera_js')
    
    <script>
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.add('modal-open');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.remove('modal-open');
        }

        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>



