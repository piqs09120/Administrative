<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Facility Reservations Calendar - Soliera</title>
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

      <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-3">
            <a href="{{ route('facility_reservations.index') }}" class="btn btn-ghost btn-sm" title="Back to Reservations">
              <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <h1 class="text-2xl font-bold">Reservations Calendar</h1>
          </div>
          <div class="flex items-center gap-2">
            <a href="{{ route('facility_reservations.create') }}" class="btn btn-primary btn-sm">
              <i data-lucide="calendar-plus" class="w-4 h-4 mr-1"></i>
              New Reservation
            </a>
          </div>
        </div>

        <div class="card bg-white shadow-lg mb-4">
          <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Facility</label>
                <select id="facilitySelect" class="select select-bordered w-full">
                  @foreach($facilities as $facility)
                    <option value="{{ $facility->id }}" {{ ($selectedFacility && $selectedFacility->id === $facility->id) ? 'selected' : '' }}>
                      {{ $facility->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Start Date</label>
                <input type="date" id="startDate" value="{{ $startDate }}" class="input input-bordered w-full" />
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">End Date</label>
                <input type="date" id="endDate" value="{{ $endDate }}" class="input input-bordered w-full" />
              </div>
              <div class="flex items-end">
                <button id="applyBtn" class="btn btn-primary w-full">
                  <i data-lucide="refresh-cw" class="w-4 h-4 mr-1"></i>
                  Apply
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="card bg-white shadow-lg">
          <div class="card-body">
            <div class="flex items-center justify-between mb-2">
              <h2 class="card-title text-lg">{{ $selectedFacility?->name ?? 'Select a facility' }}</h2>
              <span class="text-sm text-slate-500">Showing {{ $startDate }} to {{ $endDate }}</span>
            </div>

            @if(!empty($calendar))
              <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                  <thead>
                    <tr>
                      <th class="text-left">Date</th>
                      <th class="text-left">Day</th>
                      <th class="text-left">Reservations</th>
                      <th class="text-left">Utilization (hrs)</th>
                      <th class="text-left">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($calendar as $day)
                      <tr>
                        <td>{{ $day['formatted_date'] }}</td>
                        <td>{{ $day['day_of_week'] }}</td>
                        <td>
                          @if(count($day['reservations']) === 0)
                            <span class="text-slate-400">No reservations</span>
                          @else
                            <div class="space-y-2">
                              @foreach($day['reservations'] as $res)
                                <div class="p-2 rounded border border-slate-200">
                                  <div class="flex items-center justify-between">
                                    <div class="font-medium">{{ $res['reserver'] }}</div>
                                    <span class="badge badge-outline">{{ ucfirst($res['status']) }}</span>
                                  </div>
                                  <div class="text-sm text-slate-600">{{ $res['purpose'] ?? 'No purpose' }}</div>
                                  <div class="text-xs text-slate-500">{{ $res['start_time'] }} - {{ $res['end_time'] }} ({{ $res['duration_hours'] }}h)</div>
                                  <div class="mt-2">
                                    <a href="{{ route('facility_reservations.show', $res['id']) }}" class="btn btn-ghost btn-xs">
                                      <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                      View
                                    </a>
                                  </div>
                                </div>
                              @endforeach
                            </div>
                          @endif
                        </td>
                        <td>{{ $day['utilization_hours'] }}</td>
                        <td>
                          @if($day['is_available'])
                            <span class="badge badge-success">Available</span>
                          @else
                            <span class="badge badge-warning">Partially Booked</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="text-center py-12">
                <i data-lucide="calendar" class="w-12 h-12 mx-auto mb-3 text-slate-400"></i>
                <p class="text-slate-500">Select a facility to view its calendar.</p>
              </div>
            @endif
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }

      const facilitySelect = document.getElementById('facilitySelect');
      const startDate = document.getElementById('startDate');
      const endDate = document.getElementById('endDate');
      const applyBtn = document.getElementById('applyBtn');

      function reloadCalendar() {
        const facilityId = facilitySelect.value;
        const params = new URLSearchParams({ start_date: startDate.value, end_date: endDate.value });
        const base = `{{ route('facility_reservations.calendar') }}`;
        const url = facilityId ? `${base}/${facilityId}?${params}` : `${base}?${params}`;
        window.location.href = url;
      }

      if (applyBtn) {
        applyBtn.addEventListener('click', reloadCalendar);
      }
      if (facilitySelect) {
        facilitySelect.addEventListener('change', reloadCalendar);
      }
    });
  </script>
</body>
</html>


