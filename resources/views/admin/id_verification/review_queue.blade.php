<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ID Verification Review Queue</title>
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

                <!-- Page Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i data-lucide="clipboard-check" class="w-6 h-6 text-blue-600"></i>
                        ID Verification Review Queue
                    </h2>
                    <div class="border-b border-gray-200 mt-3"></div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-l-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Pending</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $stats['total_pending'] }}</p>
                            </div>
                            <i data-lucide="clock" class="w-12 h-12 text-yellow-500 opacity-50"></i>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-l-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Low Confidence</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $stats['low_confidence'] }}</p>
                            </div>
                            <i data-lucide="alert-triangle" class="w-12 h-12 text-red-500 opacity-50"></i>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-l-orange-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Medium Confidence</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $stats['medium_confidence'] }}</p>
                            </div>
                            <i data-lucide="help-circle" class="w-12 h-12 text-orange-500 opacity-50"></i>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-l-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Today's Reviews</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $stats['today_reviews'] }}</p>
                            </div>
                            <i data-lucide="calendar" class="w-12 h-12 text-blue-500 opacity-50"></i>
                        </div>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <form method="GET" action="{{ route('id_verification.review_queue') }}" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search by name, email, or ID number..." 
                                   class="input input-bordered w-full">
                        </div>
                        
                        <select name="confidence" class="select select-bordered">
                            <option value="">All Confidence Levels</option>
                            <option value="low" {{ request('confidence') === 'low' ? 'selected' : '' }}>Low (&lt;60%)</option>
                            <option value="medium" {{ request('confidence') === 'medium' ? 'selected' : '' }}>Medium (60-84%)</option>
                        </select>

                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                            Search
                        </button>

                        @if(request()->hasAny(['search', 'confidence']))
                            <a href="{{ route('id_verification.review_queue') }}" class="btn btn-ghost">
                                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                Clear
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Review Queue Table -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    @if($pendingReviews->count() > 0)
                        <!-- Bulk Actions -->
                        <div class="bg-blue-900 text-white px-6 py-4 flex items-center justify-between">
                            <h3 class="text-lg font-bold flex items-center">
                                <i data-lucide="list" class="w-5 h-5 mr-2 text-yellow-400"></i>
                                Pending Verifications
                            </h3>
                            <div class="flex gap-2">
                                <button onclick="selectAll()" class="btn btn-sm btn-ghost text-white">
                                    Select All
                                </button>
                                <button onclick="bulkApprove()" class="btn btn-sm btn-success" id="bulkApproveBtn" disabled>
                                    <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                                    Bulk Approve
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="table w-full">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()" class="checkbox checkbox-sm"></th>
                                        <th>Visitor Info</th>
                                        <th>ID Details</th>
                                        <th>Scores</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingReviews as $review)
                                        <tr class="hover:bg-gray-50">
                                            <td>
                                                <input type="checkbox" class="review-checkbox checkbox checkbox-sm" 
                                                       value="{{ $review->id }}" 
                                                       onchange="updateBulkActions()">
                                            </td>
                                            <td>
                                                <div class="flex items-center gap-3">
                                                    <div class="avatar placeholder">
                                                        <div class="bg-blue-100 text-blue-600 rounded-full w-10 h-10">
                                                            <span class="text-lg">{{ substr($review->form_name ?? '?', 0, 1) }}</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold">{{ $review->form_name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $review->form_email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-sm">
                                                    <div><strong>Type:</strong> {{ strtoupper(str_replace('_', ' ', $review->form_id_type)) }}</div>
                                                    <div><strong>Number:</strong> {{ $review->form_id_number }}</div>
                                                    <div class="text-xs text-gray-500"><strong>Method:</strong> {{ strtoupper($review->parse_method) }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="space-y-1">
                                                    <!-- Match Score -->
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs text-gray-600">Match:</span>
                                                        <div class="badge badge-sm {{ $review->match_score >= 85 ? 'badge-success' : ($review->match_score >= 60 ? 'badge-warning' : 'badge-error') }}">
                                                            {{ round($review->match_score, 1) }}%
                                                        </div>
                                                    </div>
                                                    <!-- Confidence -->
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs text-gray-600">Conf:</span>
                                                        <div class="badge badge-sm {{ $review->overall_confidence >= 85 ? 'badge-success' : ($review->overall_confidence >= 60 ? 'badge-warning' : 'badge-error') }}">
                                                            {{ round($review->overall_confidence, 1) }}%
                                                        </div>
                                                    </div>
                                                    <!-- Quality -->
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs text-gray-600">Quality:</span>
                                                        <div class="badge badge-sm {{ $review->quality_passed ? 'badge-success' : 'badge-error' }}">
                                                            {{ $review->quality_passed ? 'Pass' : 'Fail' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="badge {{ $review->status_badge_color === 'warning' ? 'badge-warning' : ($review->status_badge_color === 'error' ? 'badge-error' : 'badge-success') }}">
                                                    {{ $review->confidence_level }}
                                                </div>
                                                @if($review->philid_verified)
                                                    <div class="badge badge-info badge-sm mt-1">PhilID ✓</div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-sm">
                                                    <div>{{ $review->created_at->format('M d, Y') }}</div>
                                                    <div class="text-xs text-gray-500">{{ $review->created_at->format('h:i A') }}</div>
                                                    <div class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="flex gap-2">
                                                    <a href="{{ route('id_verification.review_details', $review->id) }}" 
                                                       class="btn btn-sm btn-primary" title="Review Details">
                                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                                    </a>
                                                    <form action="{{ route('id_verification.approve', $review->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" title="Quick Approve"
                                                                onclick="return confirm('Approve this verification?')">
                                                            <i data-lucide="check" class="w-4 h-4"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('id_verification.reject', $review->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="reason" value="Quick rejection from queue">
                                                        <button type="submit" class="btn btn-sm btn-error" title="Quick Reject"
                                                                onclick="return confirm('Reject this verification?')">
                                                            <i data-lucide="x" class="w-4 h-4"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="p-6">
                            {{ $pendingReviews->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i data-lucide="check-circle-2" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">No Pending Reviews</h3>
                            <p class="text-gray-500">All verifications have been processed!</p>
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    @include('partials.soliera_js')
    
    <script>
        // Bulk actions
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAllCheckbox');
            const checkboxes = document.querySelectorAll('.review-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkActions();
        }

        function selectAll() {
            document.getElementById('selectAllCheckbox').checked = true;
            toggleSelectAll();
        }

        function updateBulkActions() {
            const checked = document.querySelectorAll('.review-checkbox:checked');
            const bulkBtn = document.getElementById('bulkApproveBtn');
            bulkBtn.disabled = checked.length === 0;
            bulkBtn.textContent = checked.length > 0 ? `Approve (${checked.length})` : 'Bulk Approve';
        }

        function bulkApprove() {
            const checked = Array.from(document.querySelectorAll('.review-checkbox:checked')).map(cb => cb.value);
            
            if (checked.length === 0) {
                alert('Please select at least one verification to approve.');
                return;
            }

            if (!confirm(`Approve ${checked.length} verification(s)? QR passes will be generated and sent.`)) {
                return;
            }

            // Submit bulk approve
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("id_verification.bulk_approve") }}';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            checked.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }

        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>



