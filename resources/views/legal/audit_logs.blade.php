@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Audit Logs</h1>
                <div class="flex space-x-4">
                    <button onclick="exportLogs()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                    <button onclick="refreshLogs()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label for="action_type" class="block text-sm font-medium text-gray-700">Action Type</label>
                        <select name="action_type" id="action_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Actions</option>
                            <option value="document_upload" {{ request('action_type') == 'document_upload' ? 'selected' : '' }}>Document Upload</option>
                            <option value="complaint_filed" {{ request('action_type') == 'complaint_filed' ? 'selected' : '' }}>Complaint Filed</option>
                            <option value="violation_reported" {{ request('action_type') == 'violation_reported' ? 'selected' : '' }}>Violation Reported</option>
                            <option value="ai_analysis" {{ request('action_type') == 'ai_analysis' ? 'selected' : '' }}>AI Analysis</option>
                            <option value="ai_classification" {{ request('action_type') == 'ai_classification' ? 'selected' : '' }}>AI Classification</option>
                            <option value="ai_violation_detection" {{ request('action_type') == 'ai_violation_detection' ? 'selected' : '' }}>AI Violation Detection</option>
                            <option value="ai_compliance_check" {{ request('action_type') == 'ai_compliance_check' ? 'selected' : '' }}>AI Compliance Check</option>
                        </select>
                    </div>
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700">User ID</label>
                        <input type="text" name="user_id" id="user_id" value="{{ request('user_id') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="Enter user ID">
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Audit Logs Table -->
        <x-table-card :title="'Audit Logs'" :pagination="($logs->count() > 0) ? $logs->links() : null">
            <!-- Table -->
            <table class="table w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left py-4 px-4 font-semibold text-gray-700">Action Type</th>
                        <th class="text-left py-4 px-4 font-semibold text-gray-700">User</th>
                        <th class="text-left py-4 px-4 font-semibold text-gray-700">Entity</th>
                        <th class="text-left py-4 px-4 font-semibold text-gray-700">Description</th>
                        <th class="text-left py-4 px-4 font-semibold text-gray-700">Timestamp</th>
                        <th class="text-left py-4 px-4 font-semibold text-gray-700">IP Address</th>
                        <th class="text-left py-4 px-4 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="py-4 px-4 text-sm font-medium text-gray-900">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $log->action_type_color }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action_type)) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-900">
                            <div>
                                <div class="font-medium">{{ $log->user_name }}</div>
                                <div class="text-gray-500 text-sm">{{ $log->user_role }}</div>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-500">
                            @if($log->entity_type && $log->entity_id)
                                <div>
                                    <div class="font-medium">{{ ucfirst($log->entity_type) }}</div>
                                    <div class="text-gray-500">{{ $log->entity_id }}</div>
                                </div>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-500 max-w-xs">
                            <div class="truncate">{{ $log->description ?? 'No description available' }}</div>
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-500">{{ $log->formatted_timestamp }}</td>
                        <td class="py-4 px-4 text-sm text-gray-500">{{ $log->ip_address ?? 'N/A' }}</td>
                        <td class="py-4 px-4 text-sm font-medium">
                            <button onclick="viewLogDetails({{ $log->id }})" class="text-indigo-600 hover:text-indigo-900">View Details</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center">
                            <i class="fas fa-clipboard-list text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No audit logs found</h3>
                            <p class="text-gray-500 mb-4">No logs match your current filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-card>
    </div>
</div>

<!-- Log Details Modal -->
<div id="logModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Audit Log Details</h3>
                <button onclick="closeLogModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="logContent" class="max-h-96 overflow-y-auto">
                <!-- Log details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function refreshLogs() {
    location.reload();
}

function exportLogs() {
    // This would generate and download an export of the audit logs
    const params = new URLSearchParams(window.location.search);
    const exportUrl = '{{ route("legal.audit_logs") }}?export=1&' + params.toString();
    window.open(exportUrl, '_blank');
}

function viewLogDetails(logId) {
    document.getElementById('logModal').classList.remove('hidden');
    document.getElementById('logContent').innerHTML = '<div class="text-center py-4">Loading...</div>';
    
    // This would load log details via AJAX
    setTimeout(() => {
        document.getElementById('logContent').innerHTML = `
            <div class="space-y-4">
                <div>
                    <h4 class="font-medium text-gray-900">Action Type:</h4>
                    <p class="text-gray-600">Document Upload</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">User:</h4>
                    <p class="text-gray-600">John Doe (Legal Officer)</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Entity:</h4>
                    <p class="text-gray-600">Document #12345</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Description:</h4>
                    <p class="text-gray-600">Document 'Contract Agreement' uploaded and analyzed</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">AI Result:</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <pre class="text-sm text-gray-700">{"classification": "Contract", "confidence": 95, "risk_level": "low"}</pre>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Next Steps:</h4>
                    <ul class="list-disc list-inside text-gray-600">
                        <li>Review AI analysis results</li>
                        <li>Check compliance status</li>
                        <li>Assign to legal officer if needed</li>
                    </ul>
                </div>
            </div>
        `;
    }, 500);
}

function closeLogModal() {
    document.getElementById('logModal').classList.add('hidden');
}
</script>
@endsection
