@extends('layouts.app')

@section('title', 'Create Policy')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Create Company Policy</h1>
                <a href="{{ route('legal.policies') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Policies
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <form action="{{ route('legal.policies.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Basic Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="policy_code" class="block text-sm font-medium text-gray-700">Policy Code *</label>
                        <input type="text" name="policy_code" id="policy_code" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="e.g., HR-001, LEGAL-002">
                        @error('policy_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category *</label>
                        <select name="category" id="category" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select Category</option>
                            <option value="HR">HR</option>
                            <option value="Legal">Legal</option>
                            <option value="Finance">Finance</option>
                            <option value="Operations">Operations</option>
                            <option value="IT">IT</option>
                            <option value="Security">Security</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="title" class="block text-sm font-medium text-gray-700">Policy Title *</label>
                    <input type="text" name="title" id="title" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Enter policy title">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                    <textarea name="description" id="description" rows="3" required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="Brief description of the policy"></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Policy Content -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Policy Content</h3>
                
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Policy Content *</label>
                    <textarea name="content" id="content" rows="10" required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="Enter the full policy content here..."></textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Dates and Department -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Effective Dates & Department</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="effective_date" class="block text-sm font-medium text-gray-700">Effective Date *</label>
                        <input type="date" name="effective_date" id="effective_date" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('effective_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="review_date" class="block text-sm font-medium text-gray-700">Review Date</label>
                        <input type="date" name="review_date" id="review_date"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('review_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                        <input type="text" name="department" id="department"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="e.g., Human Resources">
                        @error('department')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Keywords and Related Laws -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Keywords & Related Laws</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="keywords" class="block text-sm font-medium text-gray-700">Keywords</label>
                        <textarea name="keywords" id="keywords" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                  placeholder="Enter keywords separated by commas (e.g., employment, benefits, leave)"></textarea>
                        <p class="mt-1 text-sm text-gray-500">Keywords help with AI search and policy linking</p>
                    </div>
                    
                    <div>
                        <label for="related_laws" class="block text-sm font-medium text-gray-700">Related Philippine Laws</label>
                        <textarea name="related_laws" id="related_laws" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                  placeholder="Enter related laws separated by commas (e.g., Labor Code, RA 6713)"></textarea>
                        <p class="mt-1 text-sm text-gray-500">Reference relevant Philippine laws and regulations</p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('legal.policies') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Policy
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-generate policy code based on category
document.getElementById('category').addEventListener('change', function() {
    const category = this.value;
    const policyCodeField = document.getElementById('policy_code');
    
    if (category && !policyCodeField.value) {
        const prefix = category.toUpperCase();
        const randomNumber = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        policyCodeField.value = `${prefix}-${randomNumber}`;
    }
});

// Set default effective date to today
document.getElementById('effective_date').value = new Date().toISOString().split('T')[0];
</script>
@endsection
