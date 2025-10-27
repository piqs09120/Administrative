@extends('layouts.app')

@section('title', 'File Complaint')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">File Employee Complaint</h1>
                <a href="{{ route('legal.complaints') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Complaints
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <form action="{{ route('legal.complaints.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Complainant Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Complainant Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="complainant_name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                        <input type="text" name="complainant_name" id="complainant_name" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                               placeholder="Enter your full name">
                        @error('complainant_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="complainant_department" class="block text-sm font-medium text-gray-700">Department *</label>
                        <input type="text" name="complainant_department" id="complainant_department" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                               placeholder="Enter your department">
                        @error('complainant_department')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="complainant_email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                        <input type="email" name="complainant_email" id="complainant_email" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                               placeholder="Enter your email address">
                        @error('complainant_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="complainant_contact" class="block text-sm font-medium text-gray-700">Contact Number *</label>
                        <input type="text" name="complainant_contact" id="complainant_contact" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                               placeholder="Enter your contact number">
                        @error('complainant_contact')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Complaint Details -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Complaint Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="complaint_type" class="block text-sm font-medium text-gray-700">Complaint Type *</label>
                        <select name="complaint_type" id="complaint_type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            <option value="">Select Complaint Type</option>
                            <option value="Harassment">Harassment</option>
                            <option value="Discrimination">Discrimination</option>
                            <option value="Wage and Hour Violations">Wage and Hour Violations</option>
                            <option value="Workplace Safety">Workplace Safety</option>
                            <option value="Policy Violation">Policy Violation</option>
                            <option value="Unfair Treatment">Unfair Treatment</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('complaint_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority Level</label>
                        <select name="priority" id="priority"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="complaint_description" class="block text-sm font-medium text-gray-700">Complaint Description *</label>
                    <textarea name="complaint_description" id="complaint_description" rows="5" required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                              placeholder="Please provide a detailed description of your complaint..."></textarea>
                    @error('complaint_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Incident Details -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Incident Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="incident_date" class="block text-sm font-medium text-gray-700">Incident Date</label>
                        <input type="date" name="incident_date" id="incident_date"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        @error('incident_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="incident_location" class="block text-sm font-medium text-gray-700">Incident Location</label>
                        <input type="text" name="incident_location" id="incident_location"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                               placeholder="e.g., Office, Conference Room, etc.">
                        @error('incident_location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="incident_details" class="block text-sm font-medium text-gray-700">Detailed Incident Description</label>
                    <textarea name="incident_details" id="incident_details" rows="4"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
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
                               class="border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <input type="text" name="witnesses[0][department]" placeholder="Department"
                               class="border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <input type="text" name="witnesses[0][contact]" placeholder="Contact"
                               class="border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                    </div>
                </div>
                
                <button type="button" onclick="addWitness()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Witness
                </button>
            </div>

            <!-- Confidentiality Notice -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Confidentiality Notice</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Your complaint will be handled with strict confidentiality. All information provided will be used solely for investigation purposes and will not be shared with unauthorized personnel.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('legal.complaints') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Complaint
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
               class="border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
        <input type="text" name="witnesses[${witnessCount}][department]" placeholder="Department"
               class="border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
        <input type="text" name="witnesses[${witnessCount}][contact]" placeholder="Contact"
               class="border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
    `;
    container.appendChild(witnessEntry);
    witnessCount++;
}

// Set default incident date to today
document.getElementById('incident_date').value = new Date().toISOString().split('T')[0];
</script>
@endsection
