<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Legal Case - Soliera</title>
  <link rel="icon" href="swt.jpg" type="image/x-icon">
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

        <!-- Back button -->
        <div class="flex items-center mb-6">
          <a href="{{ route('legal.case_deck') }}" class="btn btn-ghost btn-sm mr-4" style="color: var(--color-regal-navy);">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>Back
          </a>
        </div>

        <!-- Upload New Document Modal Style Layout -->
        <div class="bg-white rounded-xl shadow-lg p-8 max-w-6xl mx-auto">
          <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Add New Legal Case</h1>
            <button onclick="window.history.back()" class="btn btn-ghost btn-sm">
              <i data-lucide="x" class="w-5 h-5"></i>
            </button>
          </div>

          @if($errors->any())
            <div class="alert alert-error mb-6">
              <i data-lucide="alert-circle" class="w-5 h-5"></i>
              <ul>
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('legal.store') }}" method="POST" id="legalCaseForm">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <!-- Left Column: Form Fields -->
              <div class="space-y-6">
                <!-- Case Title -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Case Title*
                  </label>
                  <input type="text" name="case_title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                         value="{{ old('case_title') }}" placeholder="Enter case title" required 
                         style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                  <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    Enter a descriptive title for the legal case
                  </p>
                </div>

                <!-- Case Description -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Case Description
                  </label>
                  <textarea name="case_description" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                            rows="4" placeholder="Brief description of the legal case..." 
                            style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">{{ old('case_description') }}</textarea>
                  <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    Provide a brief description of the case
                  </p>
                </div>

                <!-- Case Type -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Case Type*
                  </label>
                  <select name="case_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required
                          style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                    <option value="">Select case type</option>
                    <option value="contract_dispute" {{ old('case_type') == 'contract_dispute' ? 'selected' : '' }}>Contract Dispute</option>
                    <option value="employment_law" {{ old('case_type') == 'employment_law' ? 'selected' : '' }}>Employment Law</option>
                    <option value="intellectual_property" {{ old('case_type') == 'intellectual_property' ? 'selected' : '' }}>Intellectual Property</option>
                    <option value="regulatory_compliance" {{ old('case_type') == 'regulatory_compliance' ? 'selected' : '' }}>Regulatory Compliance</option>
                    <option value="litigation" {{ old('case_type') == 'litigation' ? 'selected' : '' }}>Litigation</option>
                    <option value="other" {{ old('case_type') == 'other' ? 'selected' : '' }}>Other</option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    Select the type of legal case
                  </p>
                </div>

                <!-- Priority -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Priority*
                  </label>
                  <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required
                          style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                    <option value="">Select priority</option>
                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    Set the priority level for this case
                  </p>
                </div>

                <!-- Assigned To -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Assigned To
                  </label>
                  <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                    <option value="">Select assignee</option>
                    <option value="legal_team" {{ old('assigned_to') == 'legal_team' ? 'selected' : '' }}>Legal Team</option>
                    <option value="senior_counsel" {{ old('assigned_to') == 'senior_counsel' ? 'selected' : '' }}>Senior Counsel</option>
                    <option value="external_counsel" {{ old('assigned_to') == 'external_counsel' ? 'selected' : '' }}>External Counsel</option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    Assign the case to a team member
                  </p>
                </div>

                <!-- Employee Involved -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Employee Involved
                  </label>
                  <input type="text" name="employee_involved" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                         value="{{ old('employee_involved') }}" placeholder="Enter employee name or ID"
                         style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                  <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    Name or employee ID of the person involved
                  </p>
                </div>

                <!-- Incident Date -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Incident Date
                  </label>
                  <input type="datetime-local" name="incident_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                         value="{{ old('incident_date') }}"
                         style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                  <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    When did the incident occur?
                  </p>
                </div>

                <!-- Incident Location -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2" style="color: var(--color-charcoal-ink);">
                    Incident Location
                  </label>
                  <input type="text" name="incident_location" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                         value="{{ old('incident_location') }}" placeholder="Enter location where incident occurred"
                         style="color: var(--color-charcoal-ink); background-color: var(--color-white); border-color: var(--color-snow-mist);">
                  <p class="mt-1 text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">
                    Where did the incident occur?
                  </p>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 pt-6 border-t border-gray-200">
              <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                <i data-lucide="upload" class="w-5 h-5"></i>
                ADD CASE
              </button>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize Lucide icons
      lucide.createIcons();
    });
  </script>
</body>
</html>