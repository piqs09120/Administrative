<!DOCTYPE html>
<html lang="en">
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
          <a href="{{ route('facilities.index') }}" class="btn btn-ghost btn-sm mr-4" style="color: var(--color-regal-navy);">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2" style="color: var(--color-regal-navy);"></i>Back
          </a>
          <h1 class="text-3xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Facility Details</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Facility Info -->
          <div class="lg:col-span-2">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <div class="flex items-center justify-between mb-6">
                  <h2 class="card-title text-2xl flex items-center">
                    <i data-lucide="building" class="w-6 h-6 mr-3" style="color: var(--color-regal-navy);"></i>
                    <span style="color: var(--color-charcoal-ink);">{{ $facility->name }}</span>
                  </h2>
                  <div class="badge badge-lg" style="background-color: {{ $facility->status === 'available' ? 'var(--color-modern-teal)' : 'var(--color-danger-red)' }}; color: var(--color-white);">
                    {{ ucfirst($facility->status) }}
                  </div>
                </div>

                @if($facility->location)
                  <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Location</label>
                    <div class="flex items-center gap-2 mt-1">
                      <i data-lucide="map-pin" class="w-4 h-4" style="color: var(--color-modern-teal);"></i>
                      <span style="color: var(--color-charcoal-ink);">{{ $facility->location }}</span>
                    </div>
                  </div>
                @endif

                @if($facility->description)
                  <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Description</label>
                    <p class="mt-1 text-gray-700" style="color: var(--color-charcoal-ink);">{{ $facility->description }}</p>
                  </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t" style="border-color: var(--color-snow-mist);">
                  <div>
                    <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Total Reservations</label>
                    <p class="font-semibold text-lg" style="color: var(--color-charcoal-ink);">{{ $facility->reservations->count() }}</p>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-500" style="color: var(--color-charcoal-ink);">Last Updated</label>
                    <p class="text-sm" style="color: var(--color-charcoal-ink);">{{ $facility->updated_at->format('M d, Y H:i') }}</p>
                  </div>
                </div>

                <div class="card-actions mt-6">
                  @if($facility->status === 'available')
                    <a href="{{ route('facility_reservations.create') }}?facility={{ $facility->id }}" class="btn btn-primary" style="background-color: var(--color-regal-navy); color: var(--color-white); border-color: var(--color-regal-navy);">
                      <i data-lucide="calendar-plus" class="w-4 h-4 mr-2"></i>Reserve This Facility
                    </a>
                  @endif
                  <a href="{{ route('facilities.edit', $facility->id) }}" class="btn btn-outline" style="color: var(--color-regal-navy); border-color: var(--color-regal-navy);">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>Edit Facility
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Reservations -->
          <div class="lg:col-span-1">
            <div class="card bg-white shadow-xl" style="background-color: var(--color-white); border-color: var(--color-snow-mist);">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4 flex items-center" style="color: var(--color-charcoal-ink);">
                  <i data-lucide="calendar-clock" class="w-5 h-5 mr-2" style="color: var(--color-modern-teal);"></i>
                  Recent Reservations
                </h3>
                @if($facility->reservations->count() > 0)
                  <div class="space-y-3">
                    @foreach($facility->reservations->sortByDesc('start_time')->take(5) as $reservation)
                      <div class="border-l-4 p-3 rounded-r-md" style="border-color: {{ $reservation->status === 'approved' ? 'var(--color-modern-teal)' : ($reservation->status === 'denied' ? 'var(--color-danger-red)' : 'var(--color-golden-ember)') }}; background-color: {{ $reservation->status === 'approved' ? 'color-mix(in srgb, var(--color-modern-teal), white 90%)' : ($reservation->status === 'denied' ? 'color-mix(in srgb, var(--color-danger-red), white 90%)' : 'color-mix(in srgb, var(--color-golden-ember), white 90%)') }};">
                        <div class="flex justify-between items-start">
                          <div>
                            <p class="font-semibold text-sm" style="color: var(--color-charcoal-ink);">{{ $reservation->reserver->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500" style="color: var(--color-charcoal-ink); opacity: 0.8;">{{ \Carbon\Carbon::parse($reservation->start_time)->format('M d, H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}</p>
                          </div>
                          <div class="badge badge-sm badge-outline" style="color: {{ $reservation->status === 'pending' ? 'var(--color-golden-ember)' : ($reservation->status === 'approved' ? 'var(--color-modern-teal)' : 'var(--color-danger-red)') }}; border-color: {{ $reservation->status === 'pending' ? 'var(--color-golden-ember)' : ($reservation->status === 'approved' ? 'var(--color-modern-teal)' : 'var(--color-danger-red)') }};">
                            {{ ucfirst($reservation->status) }}
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div class="text-center py-8">
                    <i data-lucide="calendar-off" class="w-12 h-12 mx-auto mb-4" style="color: var(--color-charcoal-ink); opacity: 0.5;"></i>
                    <p class="text-gray-500 text-sm" style="color: var(--color-charcoal-ink); opacity: 0.7;">No reservations for this facility yet.</p>
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
