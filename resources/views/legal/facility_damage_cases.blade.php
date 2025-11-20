<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Facility Damage Cases - Legal Management</title>
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
      <main class="flex-1 overflow-y-auto p-8">
        <!-- Page Header -->
        <div class="mb-6">
          <h1 class="text-3xl font-bold" style="color: var(--color-charcoal-ink);">Facility Damage Cases</h1>
          <p class="text-gray-600 mt-2">Manage and process facility damage cases escalated from facility reservations</p>
        </div>
        <div class="border-b border-gray-200 mb-6"></div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <x-stat-card 
            title="Total Cases" 
            :value="$stats['total'] ?? 0" 
            icon="fa-gavel" 
            iconColor="text-yellow-400" 
            bgColor="bg-blue-900" />
          
          <x-stat-card 
            title="Pending" 
            :value="$stats['pending'] ?? 0" 
            icon="fa-clock" 
            iconColor="text-yellow-400" 
            bgColor="bg-blue-900" />
          
          <x-stat-card 
            title="Ongoing" 
            :value="$stats['ongoing'] ?? 0" 
            icon="fa-spinner" 
            iconColor="text-yellow-400" 
            bgColor="bg-blue-900" />
          
          <x-stat-card 
            title="Total Damage Cost" 
            :value="'₱' . number_format($stats['total_damage_cost'] ?? 0, 2)" 
            icon="fa-dollar-sign" 
            iconColor="text-yellow-400" 
            bgColor="bg-blue-900" />
        </div>

        <!-- Filters -->
        <div class="card bg-white shadow-xl mb-6">
          <div class="card-body">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       class="input input-bordered w-full input-sm"
                       placeholder="Case number, title, facility...">
              </div>
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="select select-bordered w-full select-sm">
                  <option value="">All Status</option>
                  <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                  <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                  <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
              </div>
              <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select name="priority" id="priority" class="select select-bordered w-full select-sm">
                  <option value="">All Priority</option>
                  <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                  <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                  <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                  <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
              </div>
              <div class="flex items-end">
                <button type="submit" class="btn btn-primary btn-sm w-full">
                  <i data-lucide="search" class="w-4 h-4 mr-2"></i>Filter
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Cases Table -->
        <div class="card bg-white shadow-xl">
          <div class="card-body">
            @if($cases->count() > 0)
              <x-table-card :title="'Facility Damage Cases'" :pagination="$cases->links('pagination::tailwind')">
                <table class="table w-full">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="text-left py-4 px-4 font-semibold text-gray-700">Case Number</th>
                      <th class="text-left py-4 px-4 font-semibold text-gray-700">Facility</th>
                      <th class="text-left py-4 px-4 font-semibold text-gray-700">Renter</th>
                      <th class="text-center py-4 px-4 font-semibold text-gray-700">Damage Cost</th>
                      <th class="text-center py-4 px-4 font-semibold text-gray-700">Priority</th>
                      <th class="text-center py-4 px-4 font-semibold text-gray-700">Status</th>
                      <th class="text-center py-4 px-4 font-semibold text-gray-700">Date</th>
                      <th class="text-center py-4 px-4 font-semibold text-gray-700">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($cases as $case)
                      @php
                        $metadata = $case->metadata ?? [];
                        $facilityName = $metadata['facility_name'] ?? 'Unknown Facility';
                        $reserverName = $metadata['reserver_name'] ?? 'Unknown';
                        $damageCost = $metadata['damage_cost'] ?? 0;
                      @endphp
                      <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-4">
                          <div class="font-semibold text-blue-600">{{ $case->case_number }}</div>
                          <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($case->case_title, 50) }}</div>
                        </td>
                        <td class="py-4 px-4">
                          <div class="font-medium">{{ $facilityName }}</div>
                          @if(isset($metadata['facility_id']))
                            <div class="text-sm text-gray-500">ID: {{ $metadata['facility_id'] }}</div>
                          @endif
                        </td>
                        <td class="py-4 px-4">
                          <div class="font-medium">{{ $reserverName }}</div>
                          @if(isset($metadata['reserved_by']))
                            <div class="text-sm text-gray-500">Reservation #{{ $metadata['facility_reservation_id'] ?? 'N/A' }}</div>
                          @endif
                        </td>
                        <td class="py-4 px-4 text-center">
                          <div class="font-semibold text-red-600">₱{{ number_format($damageCost, 2) }}</div>
                        </td>
                        <td class="py-4 px-4 text-center">
                          @php
                            $priorityColors = [
                              'urgent' => 'bg-red-500 text-white',
                              'high' => 'bg-orange-500 text-white',
                              'medium' => 'bg-yellow-500 text-white',
                              'low' => 'bg-green-500 text-white',
                            ];
                            $priorityColor = $priorityColors[$case->priority] ?? 'bg-gray-500 text-white';
                          @endphp
                          <span class="px-2 py-1 rounded-full text-white text-xs {{ $priorityColor }}">
                            {{ ucfirst($case->priority) }}
                          </span>
                        </td>
                        <td class="py-4 px-4 text-center">
                          @php
                            $statusColors = [
                              'pending' => 'bg-yellow-500 text-white',
                              'ongoing' => 'bg-blue-500 text-white',
                              'completed' => 'bg-green-500 text-white',
                              'rejected' => 'bg-red-500 text-white',
                            ];
                            $statusColor = $statusColors[$case->status] ?? 'bg-gray-500 text-white';
                          @endphp
                          <span class="px-2 py-1 rounded-full text-white text-xs {{ $statusColor }}">
                            {{ ucfirst($case->status) }}
                          </span>
                        </td>
                        <td class="py-4 px-4 text-center">
                          <div class="text-sm">{{ $case->created_at->format('M d, Y') }}</div>
                          <div class="text-xs text-gray-500">{{ $case->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="py-4 px-4 text-center">
                          <a href="{{ route('legal.facility_damage_cases.show', $case->id) }}" 
                             class="btn btn-sm btn-primary">
                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                          </a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </x-table-card>
            @else
              <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <i data-lucide="file-x" class="w-10 h-10 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-600 mb-2">No Facility Damage Cases Found</h3>
                <p class="text-gray-500 text-sm">No facility damage cases have been escalated to Legal yet.</p>
              </div>
            @endif
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    lucide.createIcons();
  </script>
</body>
</html>

