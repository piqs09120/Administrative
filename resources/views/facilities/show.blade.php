<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Facility Details - {{ $facility->name }}</title>
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

        <!-- Back button and title -->
        <div class="flex items-center mb-6">
          <a href="{{ route('facilities.index') }}" class="btn btn-ghost btn-sm mr-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back
          </a>
          <h1 class="text-3xl font-bold text-gray-800">Facility Details</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Facility Info -->
          <div class="lg:col-span-2">
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <div class="flex items-center justify-between mb-6">
                  <h2 class="card-title text-2xl flex items-center">
                    <i data-lucide="building" class="w-6 h-6 text-blue-500 mr-3"></i>
                    {{ $facility->name }}
                  </h2>
                  <div class="badge badge-lg badge-{{ $facility->status === 'available' ? 'success' : 'error' }}">
                    {{ ucfirst($facility->status) }}
                  </div>
                </div>

                @if($facility->location)
                  <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500">Location</label>
                    <div class="flex items-center gap-2 mt-1">
                      <i data-lucide="map-pin" class="w-4 h-4 text-gray-400"></i>
                      <span>{{ $facility->location }}</span>
                    </div>
                  </div>
                @endif

                @if($facility->description)
                  <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500">Description</label>
                    <p class="mt-1 text-gray-700">{{ $facility->description }}</p>
                  </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t">
                  <div>
                    <label class="text-sm font-medium text-gray-500">Total Reservations</label>
                    <p class="font-semibold text-lg">{{ $facility->reservations->count() }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-500">Last Updated</label>
                    <p class="text-sm">{{ $facility->updated_at->format('M d, Y H:i') }}</p>
                  </div>
                </div>

                <div class="card-actions mt-6">
                  @if($facility->status === 'available')
                    <a href="{{ route('facility_reservations.create') }}?facility={{ $facility->id }}" class="btn btn-primary">
                      <i data-lucide="calendar-plus" class="w-4 h-4 mr-2"></i>Reserve This Facility
                    </a>
                  @endif
                  <a href="{{ route('facilities.edit', $facility->id) }}" class="btn btn-outline">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>Edit Facility
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Reservations -->
          <div class="lg:col-span-1">
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4 flex items-center">
                  <i data-lucide="calendar-clock" class="w-5 h-5 text-purple-500 mr-2"></i>
                  Recent Reservations
                </h3>
                @if($facility->reservations->count() > 0)
                  <div class="space-y-3">
                    @foreach($facility->reservations->sortByDesc('start_time')->take(5) as $reservation)
                      <div class="border-l-4 p-3 rounded-r-md @if($reservation->status === 'approved') border-green-500 bg-green-50 @elseif($reservation->status === 'denied') border-red-500 bg-red-50 @else border-yellow-500 bg-yellow-50 @endif">
                        <div class="flex justify-between items-start">
                          <div>
                            <p class="font-semibold text-sm">{{ $reservation->reserver->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($reservation->start_time)->format('M d, H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}</p>
                          </div>
                          <div class="badge badge-sm badge-outline badge-{{ $reservation->status === 'pending' ? 'warning' : ($reservation->status === 'approved' ? 'success' : 'error') }}">
                            {{ ucfirst($reservation->status) }}
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div class="text-center py-8">
                    <i data-lucide="calendar-off" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                    <p class="text-gray-500 text-sm">No reservations for this facility yet.</p>
                  </div>
                @endif
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
