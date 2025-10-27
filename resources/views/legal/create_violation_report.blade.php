@extends('layouts.app')

@section('title', 'Report Violation')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Report Violation</h1>
                <a href="{{ route('legal.violation_reports') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Reports
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <form action="{{ route('legal.violation_reports.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Reporter Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reporter Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="reporter_name" class="block text-sm font-medium text-gray-700">Reporter Name *</label>
                        <input type="text" name="reporter_name" id="reporter_name" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               placeholder="Enter your full name">
                        @error('reporter_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="reporter_department" class="block text-sm font-medium text-gray-700">Department *</label>
                        <input type="text" name="reporter_department" id="reporter_department" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               placeholder="Enter your department">
                        @error('reporter_department')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Violator Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Violator Information (Optional)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="violator_name" class="block text-sm font-medium text-gray-700">Violator Name</label>
                        <input type="text" name="violator_name" id="violator_name"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               placeholder="Enter violator's name (if known)">
                        @error('violator_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="violator_department" class="block text-sm font-medium text-gray-700">Violator Department</label>
                        <input type="text" name="violator_department" id="violator_department"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               placeholder="Enter violator's department">
                        @error('violator_department')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Violation Details -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Violation Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="violation_type" class="block text-sm font-medium text-gray-700">Violation Type *</label>
                        <select name="violation_type" id="violation_type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="">Select Violation Type</option>
                            <option value="Policy Violation">Policy Violation</option>
                            <option value="Legal Violation">Legal Violation</option>
                            <option value="Safety Violation">Safety Violation</option>
                            <option value="Ethics Violation">Ethics Violation</option>
                            <option value="Code of Conduct Violation">Code of Conduct Violation</option>
                            <option value="Financial Violation">Financial Violation</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('violation_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="severity" class="block text-sm font-medium text-gray-700">Severity Level</label>
                        <select name="severity" id="severity"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                        @error('severity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="violation_description" class="block text-sm font-medium text-gray-700">Violation Description *</label>
                    <textarea name="violation_description" id="violation_description" rows="5" required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                              placeholder="Please provide a detailed description of the violation..."></textarea>
                    @error('violation_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Incident Details -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Incident Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="incident_date" class="block text-sm font-medium text-gray-700">Incident Date *</label>
                        <input type="date" name="incident_date" id="incident_date" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        @error('incident_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="incident_location" class="block text-sm font-medium text-gray-700">Incident Location *</label>
                        <input type="text" name="incident_location" id="incident_location" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               placeholder="e.g., Office, Conference Room, etc.">
                        @error('incident_location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="incident_details" class="block text-sm font-medium text-gray-700">Detailed Incident Description *</label>
                    <textarea name="incident_details" id="incident_details" rows="4" required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                              placeholder="Provide specific details about what happened, when, where, and who was involved..."></textarea>
                    @error('incident_details')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Witnesses -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Witnesses (Optional)</h3>
                
                <div id="witnesses-container">
                    <div class="witness-entry grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <input type="text" name="witnesses[0][name]" placeholder="Witness Name"
                               class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <input type="text" name="witnesses[0][department]" placeholder="Department"
                               class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <input type="text" name="witnesses[0][contact]" placeholder="Contact"
                               class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                </div>
                
                <button type="button" onclick="addWitness()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Witness
                </button>
            </div>

            <!-- Evidence Documents -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Evidence Documents (Optional)</h3>
                
                <div class="mb-4">
                    <label for="evidence_files" class="block text-sm font-medium text-gray-700">Upload Evidence Files</label>
                    <input type="file" name="evidence_files[]" id="evidence_files" multiple
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <p class="mt-1 text-sm text-gray-500">You can upload multiple files (PDF, DOC, DOCX, JPG, PNG)</p>
                </div>
            </div>

            <!-- Confidentiality Notice -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Confidentiality Notice</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>Your violation report will be handled with strict confidentiality. All information provided will be used solely for investigation purposes and will not be shared with unauthorized personnel.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('legal.violation_reports') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-flag mr-2"></i>Submit Report
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let witnessCount = 1;

function addWitness() {
    const container = document.getElementById('witnesses-container');
    const witnessEntry = document.createElement('div');
    witnessEntry.className = 'witness-entry grid grid-cols-1 md:grid-cols-3 gap-4 mb-4';
    witnessEntry.innerHTML = `
        <input type="text" name="witnesses[${witnessCount}][name]" placeholder="Witness Name"
               class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
        <input type="text" name="witnesses[${witnessCount}][department]" placeholder="Department"
               class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
        <input type="text" name="witnesses[${witnessCount}][contact]" placeholder="Contact"
               class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
    `;
    container.appendChild(witnessEntry);
    witnessCount++;
}

// Set default incident date to today
document.getElementById('incident_date').value = new Date().toISOString().split('T')[0];
</script>
@endsection
