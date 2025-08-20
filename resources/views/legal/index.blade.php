<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Legal Management - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
</head>
<body class="bg-base-100">
  <div class="flex h-screen overflow-hidden">
    @include('partials.sidebarr')
    <div class="flex flex-col flex-1 overflow-hidden">
      @include('partials.navbar')
      <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
        <div class="flex items-center mb-6">
          <h1 class="text-3xl font-bold" style="color: var(--color-charcoal-ink);">Legal Management</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="card bg-white shadow-xl lg:col-span-1" style="background-color: var(--color-white);">
            <div class="card-body">
              <h2 class="card-title text-lg" style="color: var(--color-charcoal-ink);">Pending Legal Review Tasks</h2>
              <ul class="mt-3 space-y-2">
                @forelse($pendingLegalReviewTasks as $task)
                  <li class="p-3 rounded border" style="border-color: var(--color-snow-mist);">
                    <div class="flex justify-between items-center">
                      <div>
                        <div class="font-medium">{{ $task->facilityReservation->facility->name ?? 'Facility' }}</div>
                        <div class="text-sm opacity-80">Requester: {{ $task->facilityReservation->reserver->name ?? 'N/A' }}</div>
                      </div>
                      <a href="{{ route('facility_reservations.legal_review', $task->facilityReservation->id) }}" class="btn btn-sm btn-outline" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">Review</a>
                    </div>
                  </li>
                @empty
                  <li class="text-sm opacity-70">No pending tasks</li>
                @endforelse
              </ul>
            </div>
          </div>

          <div class="card bg-white shadow-xl lg:col-span-2" style="background-color: var(--color-white);">
            <div class="card-body">
              <h2 class="card-title text-lg" style="color: var(--color-charcoal-ink);">Recent Approved/Flagged</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                <div>
                  <h3 class="font-semibold mb-2">Approved</h3>
                  <ul class="space-y-2">
                    @forelse($approvedLegalReviewTasks as $task)
                      <li class="text-sm">#{{ $task->id }} — {{ $task->facilityReservation->facility->name ?? 'Facility' }} ({{ $task->updated_at->format('M d, Y') }})</li>
                    @empty
                      <li class="text-sm opacity-70">None</li>
                    @endforelse
                  </ul>
                </div>
                <div>
                  <h3 class="font-semibold mb-2">Flagged</h3>
                  <ul class="space-y-2">
                    @forelse($flaggedLegalReviewTasks as $task)
                      <li class="text-sm">#{{ $task->id }} — {{ $task->facilityReservation->facility->name ?? 'Facility' }} ({{ $task->updated_at->format('M d, Y') }})</li>
                    @empty
                      <li class="text-sm opacity-70">None</li>
                    @endforelse
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
  @include('partials.soliera_js')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
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
                <p class="text-green-100 text-sm font-medium">Pending Legal Reviews (FR)</p>
                <p class="text-3xl font-bold">{{ $pendingLegalReviewTasks->count() }}</p>
              </div>
              <div class="bg-green-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="calendar" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Approved Legal Reviews Today (FR) -->
          <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-purple-100 text-sm font-medium">Approved Legal Reviews (FR)</p>
                <p class="text-3xl font-bold">{{ $approvedLegalReviewTasks->count() }}</p>
              </div>
              <div class="bg-purple-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="check-circle" class="w-8 h-8"></i>
              </div>
            </div>
          </div>

          <!-- Flagged Legal Reviews (FR) -->
          <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-orange-100 text-sm font-medium">Flagged Legal Reviews (FR)</p>
                <p class="text-3xl font-bold">{{ $flaggedLegalReviewTasks->count() }}</p>
              </div>
              <div class="bg-orange-400 bg-opacity-30 p-3 rounded-full">
                <i data-lucide="x-circle" class="w-8 h-8"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 mb-8">
          <a href="{{ route('legal.pending') }}" class="btn btn-primary btn-lg" style="background-color: var(--color-regal-navy); color: var(--color-white); border-color: var(--color-regal-navy);">
            <i data-lucide="clock" class="w-5 h-5 mr-2"></i>
            View All Pending Legal Requests (DM)
          </a>
          
        </div>

        <!-- Pending Document Release Requests Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center" style="color: var(--color-charcoal-ink);">
              <i data-lucide="clock" class="w-6 h-6 text-yellow-500 mr-3" style="color: var(--color-golden-ember);"></i>
              Pending Document Release Requests
              <span class="badge badge-warning badge-lg ml-3" style="background-color: var(--color-golden-ember); color: var(--color-white);">{{ $pendingRequests->count() }}</span>
            </h2>
          </div>

          @if($pendingRequests->count() > 0)
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr class="bg-gray-50" style="background-color: var(--color-snow-mist);">
                    <th class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Document</th>
                    <th class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Requested By</th>
                    <th class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Request Date</th>
                    <th class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pendingRequests as $request)
                    <tr class="hover:bg-gray-50 transition-colors" style="color: var(--color-charcoal-ink);">
                      <td>
                        <div>
                          <div class="font-semibold text-gray-800" style="color: var(--color-charcoal-ink);">{{ $request->document->title }}</div>
                          <div class="text-sm text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">{{ Str::limit($request->document->description, 50) }}</div>
                        </div>
                      </td>
                      <td class="font-medium" style="color: var(--color-charcoal-ink);">{{ $request->requester->name ?? 'Unknown' }}</td>
                      <td class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ $request->created_at->format('M d, Y H:i') }}</td>
                      <td>
                        <div class="flex space-x-2">
                          <a href="{{ route('legal.show', $request->id) }}" class="btn btn-sm btn-outline" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">
                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                          </a>
                          <button onclick="approveRequest({{ $request->id }})" class="btn btn-sm btn-success" style="background-color: var(--color-modern-teal); color: var(--color-white); border-color: var(--color-modern-teal);">
                            <i data-lucide="check" class="w-4 h-4 mr-1"></i>Approve
                          </button>
                          <button onclick="denyRequest({{ $request->id }})" class="btn btn-sm btn-error" style="background-color: var(--color-danger-red); color: var(--color-white); border-color: var(--color-danger-red);">
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
            <div class="text-center py-12" style="color: var(--color-charcoal-ink); opacity: 0.7;">
              <i data-lucide="check-circle" class="w-16 h-16 text-green-300 mx-auto mb-4" style="color: var(--color-modern-teal); opacity: 0.5;"></i>
              <h3 class="text-lg font-semibold text-gray-600 mb-2" style="color: var(--color-charcoal-ink);">No Pending Document Release Requests</h3>
              <p class="text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">All document release requests have been processed.</p>
            </div>
          @endif
        </div>

        <!-- Pending Facility Reservation Legal Review Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center" style="color: var(--color-charcoal-ink);">
              <i data-lucide="scale" class="w-6 h-6 text-yellow-500 mr-3" style="color: var(--color-golden-ember);"></i>
              Pending Facility Reservation Legal Reviews
              <span class="badge badge-warning badge-lg ml-3" style="background-color: var(--color-golden-ember); color: var(--color-white);">{{ $pendingLegalReviewTasks->count() }}</span>
            </h2>
          </div>

          @if($pendingLegalReviewTasks->count() > 0)
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr class="bg-gray-50" style="background-color: var(--color-snow-mist);">
                    <th class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Reservation ID</th>
                    <th class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Facility</th>
                    <th class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Requested By</th>
                    <th class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Request Date</th>
                    <th class="font-semibold text-gray-700" style="color: var(--color-charcoal-ink);">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pendingLegalReviewTasks as $task)
                    @php
                      $reservation = $task->facilityReservation;
                    @endphp
                    @if($reservation)
                      <tr class="hover:bg-gray-50 transition-colors" style="color: var(--color-charcoal-ink);">
                        <td class="font-medium" style="color: var(--color-charcoal-ink);">{{ $reservation->id }}</td>
                        <td class="font-medium" style="color: var(--color-charcoal-ink);">{{ $reservation->facility->name ?? 'N/A' }}</td>
                        <td class="font-medium" style="color: var(--color-charcoal-ink);">{{ $reservation->reserver->name ?? 'N/A' }}</td>
                        <td class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ \Carbon\Carbon::parse($task->created_at)->format('M d, Y H:i') }}</td>
                        <td>
                          <div class="flex space-x-2">
                            <a href="{{ route('facility_reservations.legal_review', $task->facility_reservation_id) }}" class="btn btn-sm btn-outline" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">
                              <i data-lucide="eye" class="w-4 h-4 mr-1"></i>Review
                            </a>
                          </div>
                        </td>
                      </tr>
                    @endif
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-12" style="color: var(--color-charcoal-ink); opacity: 0.7;">
              <i data-lucide="check-circle" class="w-16 h-16 text-green-300 mx-auto mb-4" style="color: var(--color-modern-teal); opacity: 0.5;"></i>
              <h3 class="text-lg font-semibold text-gray-600 mb-2" style="color: var(--color-charcoal-ink);">No Pending Facility Reservation Legal Reviews</h3>
              <p class="text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.7;">All facility reservation legal review tasks have been processed.</p>
            </div>
          @endif
        </div>

        <!-- Legal Document Folders -->
        <div class="bg-white rounded-xl shadow-lg p-6" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-2xl font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Legal Document Folders</h3>
              <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Browse legal documents by AI classification. Documents are automatically organized by category.</p>
            </div>
            <div class="flex space-x-3">
              <a href="{{ route('legal.create') }}" class="btn btn-warning" style="background-color: var(--color-golden-ember); color: var(--color-white); border-color: var(--color-golden-ember);">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                ADD NEW CASE
              </a>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Memorandums -->
            <a href="{{ route('legal.category', 'memorandums') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4" style="background-color: color-mix(in srgb, var(--color-regal-navy), white 80%);">
                  <i data-lucide="folder" class="w-8 h-8 text-blue-600" style="color: var(--color-regal-navy);"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Memorandums</h4>
                <p class="text-2xl font-bold text-blue-600" style="color: var(--color-regal-navy);">{{ \App\Models\Document::where('category', 'memorandum')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Contracts -->
            <a href="{{ route('legal.category', 'contracts') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4" style="background-color: color-mix(in srgb, var(--color-modern-teal), white 80%);">
                  <i data-lucide="folder" class="w-8 h-8 text-green-600" style="color: var(--color-modern-teal);"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Contracts</h4>
                <p class="text-2xl font-bold text-green-600" style="color: var(--color-modern-teal);">{{ \App\Models\Document::where('category', 'contract')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Subpoenas -->
            <a href="{{ route('legal.category', 'subpoenas') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4" style="background-color: color-mix(in srgb, var(--color-danger-red), white 80%);">
                  <i data-lucide="folder" class="w-8 h-8 text-red-600" style="color: var(--color-danger-red);"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Subpoenas</h4>
                <p class="text-2xl font-bold text-red-600" style="color: var(--color-danger-red);">{{ \App\Models\Document::where('category', 'subpoena')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Affidavits -->
            <a href="{{ route('legal.category', 'affidavits') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-4" style="background-color: color-mix(in srgb, var(--color-purple), white 80%);">
                  <i data-lucide="folder" class="w-8 h-8 text-purple-600" style="color: var(--color-purple);"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Affidavits</h4>
                <p class="text-2xl font-bold text-purple-600" style="color: var(--color-purple);">{{ \App\Models\Document::where('category', 'affidavit')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Cease & Desist -->
            <a href="{{ route('legal.category', 'cease-desist') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-4" style="background-color: color-mix(in srgb, var(--color-golden-ember), white 80%);">
                  <i data-lucide="folder" class="w-8 h-8 text-orange-600" style="color: var(--color-golden-ember);"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Cease & Desist</h4>
                <p class="text-2xl font-bold text-orange-600" style="color: var(--color-golden-ember);">{{ \App\Models\Document::where('category', 'cease_desist')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Legal Notices -->
            <a href="{{ route('legal.category', 'legal-notices') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-4" style="background-color: color-mix(in srgb, var(--color-golden-ember), white 80%);">
                  <i data-lucide="folder" class="w-8 h-8 text-yellow-600" style="color: var(--color-golden-ember);"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Legal Notices</h4>
                <p class="text-2xl font-bold text-yellow-600" style="color: var(--color-golden-ember);">{{ \App\Models\Document::where('category', 'legal_notice')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Policies -->
            <a href="{{ route('legal.category', 'policies') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4" style="background-color: color-mix(in srgb, var(--color-purple), white 80%);">
                  <i data-lucide="folder" class="w-8 h-8 text-indigo-600" style="color: var(--color-purple);"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Policies</h4>
                <p class="text-2xl font-bold text-indigo-600" style="color: var(--color-purple);">{{ \App\Models\Document::where('category', 'policy')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Legal Briefs -->
            <a href="{{ route('legal.category', 'legal-briefs') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mb-4" style="background-color: color-mix(in srgb, var(--color-pink), white 80%);">
                  <i data-lucide="folder" class="w-8 h-8 text-pink-600" style="color: var(--color-pink);"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Legal Briefs</h4>
                <p class="text-2xl font-bold text-pink-600" style="color: var(--color-pink);">{{ \App\Models\Document::where('category', 'legal_brief')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Financial Documents -->
            <a href="{{ route('legal.category', 'financial') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-4" style="background-color: color-mix(in srgb, var(--color-emerald), white 80%);">
                  <i data-lucide="folder" class="w-8 h-8 text-emerald-600" style="color: var(--color-emerald);"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Financial</h4>
                <p class="text-2xl font-bold text-emerald-600" style="color: var(--color-emerald);">{{ \App\Models\Document::where('category', 'financial')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Compliance Documents -->
            <a href="{{ route('legal.category', 'compliance') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mb-4" style="background-color: color-mix(in srgb, var(--color-teal), white 80%);">
                  <i data-lucide="folder" class="w-8 h-8 text-teal-600" style="color: var(--color-teal);"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Compliance</h4>
                <p class="text-2xl font-bold text-teal-600" style="color: var(--color-teal);">{{ \App\Models\Document::where('category', 'compliance')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>

            <!-- Reports -->
            <a href="{{ route('legal.category', 'reports') }}" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer block" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-cyan-100 rounded-full flex items-center justify-center mb-4" style="background-color: color-mix(in srgb, var(--color-cyan), white 80%);">
                  <i data-lucide="folder" class="w-8 h-8 text-cyan-600" style="color: var(--color-cyan);"></i>
                </div>
                <h4 class="font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Reports</h4>
                <p class="text-2xl font-bold text-cyan-600" style="color: var(--color-cyan);">{{ \App\Models\Document::where('category', 'report')->where('source', 'legal_management')->count() }} documents</p>
              </div>
            </a>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    // Real-time date and time
    function updateDateTime() {
      const now = new Date();
      const dateElement = document.getElementById('currentDate');
      const timeElement = document.getElementById('currentTime');
      
      const dateOptions = { weekday: 'short', month: 'short', day: 'numeric' };
      const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
      
      if (dateElement) dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
      if (timeElement) timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
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
      updateDateTime();
      setupSearch();
      
      // Update time every second
      setInterval(updateDateTime, 1000);
    });

    // Functions to handle approve/deny from current page (for DocumentReleaseRequests)
    function approveRequest(requestId) {
      if (confirm('Are you sure you want to approve this document release request?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/legal/${requestId}/approve`;
        form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
        document.body.appendChild(form);
        form.submit();
      }
    }

    function denyRequest(requestId) {
      const reason = prompt('Please provide a reason for denying this document release request:');
      if (reason !== null && reason.trim() !== '') {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/legal/${requestId}/deny`;
        form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="reason" value="${reason}">`;
        document.body.appendChild(form);
        form.submit();
      } else if (reason !== null) {
        alert('Denial reason cannot be empty.');
      }
    }

    // Function to handle deletion of Facility Reservations (if applicable, though typically done from FR module)
    function deleteFacilityReservation(reservationId) {
      if (confirm('Are you sure you want to delete this facility reservation? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/facility_reservations/${reservationId}`;
        form.innerHTML = `
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    }
  </script>
</body>
</html> 