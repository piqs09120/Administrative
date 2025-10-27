@extends('layouts.app')

@section('title', 'Company Policies')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Company Policies</h1>
                <a href="{{ route('legal.policies.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create Policy
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category" id="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Categories</option>
                            <option value="HR" {{ request('category') == 'HR' ? 'selected' : '' }}>HR</option>
                            <option value="Legal" {{ request('category') == 'Legal' ? 'selected' : '' }}>Legal</option>
                            <option value="Finance" {{ request('category') == 'Finance' ? 'selected' : '' }}>Finance</option>
                            <option value="Operations" {{ request('category') == 'Operations' ? 'selected' : '' }}>Operations</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Policies Table -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($policies->count() > 0)
                    <x-table-card :title="'Company Policies'" :pagination="$policies->links()">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Policy Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($policies as $policy)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $policy->policy_code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $policy->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $policy->category }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $policy->status_color }}">
                                            {{ ucfirst($policy->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $policy->effective_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewPolicy({{ $policy->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">View</button>
                                        <button onclick="editPolicy({{ $policy->id }})" class="text-blue-600 hover:text-blue-900">Edit</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-table-card>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-file-alt text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No policies found</h3>
                        <p class="text-gray-500 mb-4">Get started by creating your first company policy.</p>
                        <a href="{{ route('legal.policies.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create Policy
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Policy View Modal -->
<div id="policyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Policy Details</h3>
                <button onclick="closePolicyModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="policyContent" class="max-h-96 overflow-y-auto">
                <!-- Policy content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewPolicy(policyId) {
    // This would load policy details via AJAX
    document.getElementById('policyModal').classList.remove('hidden');
    document.getElementById('policyContent').innerHTML = '<div class="text-center py-4">Loading...</div>';
    
    // Simulate loading policy details
    setTimeout(() => {
        document.getElementById('policyContent').innerHTML = `
            <div class="space-y-4">
                <div>
                    <h4 class="font-medium text-gray-900">Policy Code:</h4>
                    <p class="text-gray-600">POL-2025-001</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Title:</h4>
                    <p class="text-gray-600">Sample Policy Title</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Description:</h4>
                    <p class="text-gray-600">This is a sample policy description that explains the purpose and scope of the policy.</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Content:</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700">This is the detailed policy content that would be displayed here.</p>
                    </div>
                </div>
            </div>
        `;
    }, 500);
}

function editPolicy(policyId) {
    // Redirect to edit page
    window.location.href = `/legal/policies/${policyId}/edit`;
}

function closePolicyModal() {
    document.getElementById('policyModal').classList.add('hidden');
}
</script>
@endsection
