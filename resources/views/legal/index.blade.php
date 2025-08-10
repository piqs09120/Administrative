<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Legal Management - Soliera</title>
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Pending Requests -->
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-blue-100 text-sm font-medium">Pending Requests</p>
                <p class="text-3xl font-bold">{{ $pendingRequests->count() }}</p>
              </div>
              <div class="bg-blue-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="clock" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Pending Facility Reservations -->
          <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-green-100 text-sm font-medium">Pending Reservations</p>
                <p class="text-3xl font-bold">{{ $pendingFacilityReservations->count() }}</p>
              </div>
              <div class="bg-green-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="calendar" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Approved Today -->
          <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-purple-100 text-sm font-medium">Approved Today</p>
                <p class="text-3xl font-bold">{{ $approvedFacilityReservations->where('updated_at', '>=', now()->startOfDay())->count() }}</p>
              </div>
              <div class="bg-purple-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="check-circle" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Denied Today -->
          <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-orange-100 text-sm font-medium">Denied Today</p>
                <p class="text-3xl font-bold">{{ $deniedFacilityReservations->where('updated_at', '>=', now()->startOfDay())->count() }}</p>
              </div>
              <div class="bg-orange-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="x-circle" class="w-8 h-8"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 mb-8">
          <a href="{{ route('legal.pending') }}" class="btn btn-primary btn-lg">
            <i data-lucide="clock" class="w-5 h-5 mr-2"></i>
            View Pending Requests
          </a>
          <a href="{{ route('legal.approved') }}" class="btn btn-success btn-lg">
            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
            View Approved
          </a>
          <a href="{{ route('legal.denied') }}" class="btn btn-error btn-lg">
            <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
            View Denied
          </a>
        </div>

        <!-- Pending Requests Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
              <i data-lucide="clock" class="w-6 h-6 text-yellow-500 mr-3"></i>
              Pending Approval Requests
              <span class="badge badge-warning badge-lg ml-3">{{ $pendingRequests->count() }}</span>
            </h2>
          </div>

          @if($pendingRequests->count() > 0)
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr class="bg-gray-50">
                    <th class="font-semibold text-gray-700">Document</th>
                    <th class="font-semibold text-gray-700">Requested By</th>
                    <th class="font-semibold text-gray-700">Request Date</th>
                    <th class="font-semibold text-gray-700">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pendingRequests as $request)
                    <tr class="hover:bg-gray-50 transition-colors">
                      <td>
                        <div>
                          <div class="font-semibold text-gray-800">{{ $request->document->title }}</div>
                          <div class="text-sm text-gray-500">{{ Str::limit($request->document->description, 50) }}</div>
                        </div>
                      </td>
                      <td class="font-medium">{{ $request->requester->name ?? 'Unknown' }}</td>
                      <td class="text-gray-600">{{ $request->created_at->format('M d, Y H:i') }}</td>
                      <td>
                        <div class="flex space-x-2">
                          <a href="{{ route('legal.show', $request->id) }}" class="btn btn-sm btn-outline">
                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                          </a>
                          <button onclick="approveRequest({{ $request->id }})" class="btn btn-sm btn-success">
                            <i data-lucide="check" class="w-4 h-4 mr-1"></i>Approve
                          </button>
                          <button onclick="denyRequest({{ $request->id }})" class="btn btn-sm btn-error">
                            <i data-lucide="x" class="w-4 h-4 mr-1"></i>Deny
                          </button>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-12">
              <i data-lucide="check-circle" class="w-16 h-16 text-green-300 mx-auto mb-4"></i>
              <h3 class="text-lg font-semibold text-gray-600 mb-2">No Pending Requests</h3>
              <p class="text-gray-500">All document release requests have been processed.</p>
            </div>
          @endif
        </div>

        <!-- Pending Facility Reservation Requests Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
              <i data-lucide="calendar" class="w-6 h-6 text-yellow-500 mr-3"></i>
              Pending Facility Reservation Requests
              <span class="badge badge-warning badge-lg ml-3">{{ $pendingFacilityReservations->count() }}</span>
            </h2>
          </div>

          @if($pendingFacilityReservations->count() > 0)
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr class="bg-gray-50">
                    <th class="font-semibold text-gray-700">Facility</th>
                    <th class="font-semibold text-gray-700">Reserved By</th>
                    <th class="font-semibold text-gray-700">Start</th>
                    <th class="font-semibold text-gray-700">End</th>
                    <th class="font-semibold text-gray-700">Purpose</th>
                    <th class="font-semibold text-gray-700">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pendingFacilityReservations as $reservation)
                    <tr class="hover:bg-gray-50 transition-colors">
                      <td class="font-medium">{{ $reservation->facility->name ?? 'N/A' }}</td>
                      <td class="font-medium">{{ $reservation->reserver->name ?? 'N/A' }}</td>
                      <td class="text-gray-600">{{ \Carbon\Carbon::parse($reservation->start_time)->format('M d, Y H:i') }}</td>
                      <td class="text-gray-600">{{ \Carbon\Carbon::parse($reservation->end_time)->format('M d, Y H:i') }}</td>
                      <td>
                        <span class="badge badge-outline">{{ $reservation->purpose ?? '-' }}</span>
                      </td>
                      <td>
                        <form action="/facility_reservations/{{ $reservation->id }}/approve" method="POST" class="inline">
                          @csrf
                          <input type="text" name="remarks" class="input input-bordered input-sm mr-2" placeholder="Remarks (optional)">
                          <button type="submit" class="btn btn-success btn-sm">
                            <i data-lucide="check" class="w-4 h-4 mr-1"></i>Approve
                          </button>
                        </form>
                        <form action="/facility_reservations/{{ $reservation->id }}/deny" method="POST" class="inline">
                          @csrf
                          <input type="text" name="remarks" class="input input-bordered input-sm mr-2" placeholder="Remarks (optional)">
                          <button type="submit" class="btn btn-error btn-sm">
                            <i data-lucide="x" class="w-4 h-4 mr-1"></i>Deny
                          </button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-12">
              <i data-lucide="check-circle" class="w-16 h-16 text-green-300 mx-auto mb-4"></i>
              <h3 class="text-lg font-semibold text-gray-600 mb-2">No Pending Facility Reservations</h3>
              <p class="text-gray-500">All facility reservation requests have been processed.</p>
            </div>
          @endif
        </div>

        <!-- Recently Approved Facility Reservations -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
          <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
            <i data-lucide="check-circle" class="w-6 h-6 text-green-500 mr-3"></i>
            Recently Approved Facility Reservations
          </h3>
          @if($approvedFacilityReservations->count() > 0)
            <div class="space-y-4">
              @foreach($approvedFacilityReservations as $reservation)
                <div class="border-l-4 border-green-500 pl-4 py-3 bg-green-50 rounded-r-lg">
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="font-semibold text-gray-800">{{ $reservation->facility->name ?? 'N/A' }}</p>
                      <p class="text-sm text-gray-600">
                        Reserved by: {{ $reservation->reserver->name ?? 'N/A' }}
                      </p>
                      <p class="text-sm text-gray-600">
                        Approved by: {{ $reservation->approver->name ?? 'N/A' }}
                      </p>
                      <p class="text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($reservation->updated_at)->format('M d, Y H:i') }}
                      </p>
                    </div>
                    <div class="flex items-center space-x-2">
                      <div class="badge badge-success badge-lg">Approved</div>
                      <button onclick="deleteFacilityReservation({{ $reservation->id }})" class="btn btn-error btn-sm">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                    </div>
                  </div>
                  @if($reservation->remarks)
                    <div class="text-sm text-gray-600 mt-2 italic">
                      "{{ $reservation->remarks }}"
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-8">
              <p class="text-gray-500">No recently approved facility reservations</p>
            </div>
          @endif
        </div>

        <!-- Recently Denied Facility Reservations -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
          <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
            <i data-lucide="x-circle" class="w-6 h-6 text-red-500 mr-3"></i>
            Recently Denied Facility Reservations
          </h3>
          @if($deniedFacilityReservations->count() > 0)
            <div class="space-y-4">
              @foreach($deniedFacilityReservations as $reservation)
                <div class="border-l-4 border-red-500 pl-4 py-3 bg-red-50 rounded-r-lg">
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="font-semibold text-gray-800">{{ $reservation->facility->name ?? 'N/A' }}</p>
                      <p class="text-sm text-gray-600">
                        Reserved by: {{ $reservation->reserver->name ?? 'N/A' }}
                      </p>
                      <p class="text-sm text-gray-600">
                        Denied by: {{ $reservation->approver->name ?? 'N/A' }}
                      </p>
                      <p class="text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($reservation->updated_at)->format('M d, Y H:i') }}
                      </p>
                    </div>
                    <div class="flex items-center space-x-2">
                      <div class="badge badge-error badge-lg">Denied</div>
                      <button onclick="deleteFacilityReservation({{ $reservation->id }})" class="btn btn-error btn-sm">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                      </button>
                    </div>
                  </div>
                  @if($reservation->remarks)
                    <div class="text-sm text-gray-600 mt-2 italic">
                      "{{ $reservation->remarks }}"
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-8">
              <p class="text-gray-500">No recently denied facility reservations</p>
            </div>
          @endif
        </div>

        <!-- Legal Document Folders -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-2xl font-bold text-gray-800 mb-2">Legal Document Folders</h3>
              <p class="text-gray-600">Browse legal documents by AI classification. Documents are automatically organized by category.</p>
            </div>
            <div class="flex space-x-3">
              <a href="{{ route('legal.create') }}" class="btn btn-warning">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                ADD NEW CASE
              </a>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Memorandums -->
            <a href="{{ route('legal.category', 'memorandums') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="folder" class="w-8 h-8 text-blue-600"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2">Memorandums</h4>
                <p class="text-2xl font-bold text-blue-600">{{ \App\Models\Document::where('category', 'memorandum')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Contracts -->
            <a href="{{ route('legal.category', 'contracts') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="folder" class="w-8 h-8 text-green-600"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2">Contracts</h4>
                <p class="text-2xl font-bold text-green-600">{{ \App\Models\Document::where('category', 'contract')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Subpoenas -->
            <a href="{{ route('legal.category', 'subpoenas') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="folder" class="w-8 h-8 text-red-600"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2">Subpoenas</h4>
                <p class="text-2xl font-bold text-red-600">{{ \App\Models\Document::where('category', 'subpoena')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Affidavits -->
            <a href="{{ route('legal.category', 'affidavits') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="folder" class="w-8 h-8 text-purple-600"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2">Affidavits</h4>
                <p class="text-2xl font-bold text-purple-600">{{ \App\Models\Document::where('category', 'affidavit')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Cease & Desist -->
            <a href="{{ route('legal.category', 'cease-desist') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="folder" class="w-8 h-8 text-orange-600"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2">Cease & Desist</h4>
                <p class="text-2xl font-bold text-orange-600">{{ \App\Models\Document::where('category', 'cease_desist')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Legal Notices -->
            <a href="{{ route('legal.category', 'legal-notices') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="folder" class="w-8 h-8 text-yellow-600"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2">Legal Notices</h4>
                <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Document::where('category', 'legal_notice')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Policies -->
            <a href="{{ route('legal.category', 'policies') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="folder" class="w-8 h-8 text-indigo-600"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2">Policies</h4>
                <p class="text-2xl font-bold text-indigo-600">{{ \App\Models\Document::where('category', 'policy')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Legal Briefs -->
            <a href="{{ route('legal.category', 'legal-briefs') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="folder" class="w-8 h-8 text-pink-600"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2">Legal Briefs</h4>
                <p class="text-2xl font-bold text-pink-600">{{ \App\Models\Document::where('category', 'legal_brief')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Financial Documents -->
            <a href="{{ route('legal.category', 'financial') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="folder" class="w-8 h-8 text-emerald-600"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2">Financial</h4>
                <p class="text-2xl font-bold text-emerald-600">{{ \App\Models\Document::where('category', 'financial')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Compliance Documents -->
            <a href="{{ route('legal.category', 'compliance') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="folder" class="w-8 h-8 text-teal-600"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2">Compliance</h4>
                <p class="text-2xl font-bold text-teal-600">{{ \App\Models\Document::where('category', 'compliance')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Reports -->
            <a href="{{ route('legal.category', 'reports') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-cyan-100 rounded-full flex items-center justify-center mb-4">
                  <i data-lucide="folder" class="w-8 h-8 text-cyan-600"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2">Reports</h4>
                <p class="text-2xl font-bold text-cyan-600">{{ \App\Models\Document::where('category', 'report')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    // Dark mode functionality
    function setupDarkMode() {
      const toggle = document.getElementById('darkModeToggle');
      const sunIcon = document.getElementById('sunIcon');
      const moonIcon = document.getElementById('moonIcon');
      
      function updateIcons() {
        if(document.documentElement.classList.contains('dark')) {
          sunIcon.classList.remove('hidden');
          moonIcon.classList.add('hidden');
        } else {
          sunIcon.classList.add('hidden');
          moonIcon.classList.remove('hidden');
        }
      }
      
      // Initial state
      const isDarkMode = localStorage.getItem('darkMode') === 'true';
      if (isDarkMode) {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark');
      }
      updateIcons();
      
      toggle.addEventListener('click', function() {
        console.log('Dark mode toggle clicked!');
        
        // Direct toggle without relying on global function
        if (document.documentElement.classList.contains('dark')) {
          // Switch to light mode
          document.documentElement.classList.remove('dark');
          document.body.classList.remove('dark');
          localStorage.setItem('darkMode', 'false');
          console.log('Switched to LIGHT mode');
        } else {
          // Switch to dark mode
          document.documentElement.classList.add('dark');
          document.body.classList.add('dark');
          localStorage.setItem('darkMode', 'true');
          console.log('Switched to DARK mode');
        }
        
        updateIcons();
      });
    }

    // Real-time date and time
    function updateDateTime() {
      const now = new Date();
      const dateElement = document.getElementById('currentDate');
      const timeElement = document.getElementById('currentTime');
      
      const dateOptions = { weekday: 'short', month: 'short', day: 'numeric' };
      const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
      
      dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
      timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
    }

    // Search functionality
    function setupSearch() {
      const searchInput = document.getElementById('searchInput');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const tableRows = document.querySelectorAll('tbody tr');
          
          tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      setupDarkMode();
      updateDateTime();
      setupSearch();
      
      // Update time every second
      setInterval(updateDateTime, 1000);
    });

    function approveRequest(requestId) {
      if (confirm('Are you sure you want to approve this request?')) {
        // Add your approval logic here
        console.log('Approving request:', requestId);
      }
    }

    function denyRequest(requestId) {
      if (confirm('Are you sure you want to deny this request?')) {
        // Add your denial logic here
        console.log('Denying request:', requestId);
      }
    }

    function deleteFacilityReservation(reservationId) {
      if (confirm('Are you sure you want to delete this facility reservation? This action cannot be undone.')) {
        // Create a form to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/facility_reservations/${reservationId}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        // Add method override for DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
      }
    }
  </script>
</body>
</html> 