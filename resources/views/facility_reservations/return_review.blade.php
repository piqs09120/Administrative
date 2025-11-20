<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Return Inspection - Facility Reservation #{{ $reservation->id }}</title>
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
        <!-- Back Button -->
        <div class="mb-4">
          <a href="{{ route('facility_reservations.show', $reservation->id) }}" class="btn btn-sm btn-ghost">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back to Reservation
          </a>
        </div>

        <!-- Page Header -->
        <div class="mb-6">
          <h1 class="text-3xl font-bold" style="color: var(--color-charcoal-ink);">Return Inspection</h1>
          <p class="text-gray-600 mt-2">Facility: {{ $reservation->facility->name }} | Reservation #{{ $reservation->id }}</p>
        </div>
        <div class="border-b border-gray-200 mb-6"></div>

        <!-- Inspection Form -->
        <div class="card bg-white shadow-xl max-w-3xl mx-auto">
          <div class="card-body">
            <form action="{{ route('facility_reservations.return_inspection', $reservation->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              
              <!-- Damage Flag -->
              <div class="form-control mb-6">
                <label class="label cursor-pointer justify-start gap-4">
                  <input type="hidden" name="damage_flag" value="0">
                  <input type="checkbox" name="damage_flag" id="damage_flag" value="1" class="checkbox checkbox-warning" 
                         onchange="toggleDamageFields(this.checked)">
                  <span class="label-text text-lg font-semibold">Has Damage?</span>
                </label>
                <p class="text-sm text-gray-500 mt-2">Check this box if the facility was damaged during the reservation period</p>
              </div>

              <!-- Damage Cost (shown only if damage_flag is checked) -->
              <div id="damage_cost_field" class="form-control mb-6 hidden">
                <label for="damage_cost" class="label">
                  <span class="label-text font-semibold">Estimated Damage Cost (₱)</span>
                </label>
                <input type="number" 
                       name="damage_cost" 
                       id="damage_cost" 
                       class="input input-bordered w-full" 
                       min="0" 
                       step="0.01"
                       placeholder="Enter estimated cost (e.g., 15000.00)">
                <p class="text-sm text-gray-500 mt-2">
                  <i data-lucide="info" class="w-4 h-4 inline"></i>
                  If cost is ₱10,000 or more, this will automatically escalate to Legal Management
                </p>
              </div>

              <!-- Inspection Notes -->
              <div class="form-control mb-6">
                <label for="inspection_notes" class="label">
                  <span class="label-text font-semibold">Inspection Notes</span>
                </label>
                <textarea name="inspection_notes" 
                          id="inspection_notes" 
                          class="textarea textarea-bordered h-32" 
                          placeholder="Describe the condition of the facility, any damages found, or other observations..."></textarea>
              </div>

              <!-- Damage Photos (optional) -->
              <div id="damage_photos_field" class="form-control mb-6 hidden">
                <label for="damage_photos" class="label">
                  <span class="label-text font-semibold">Damage Photos (Optional)</span>
                </label>
                <input type="file" 
                       name="damage_photos[]" 
                       id="damage_photos" 
                       class="file-input file-input-bordered w-full" 
                       multiple 
                       accept="image/*,.pdf">
                <p class="text-sm text-gray-500 mt-2">You can upload multiple photos or PDF files</p>
              </div>

              <!-- Submit Button -->
              <div class="flex gap-3 justify-end mt-6">
                <a href="{{ route('facility_reservations.show', $reservation->id) }}" class="btn btn-ghost">
                  Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                  <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                  Submit Inspection
                </button>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  <script>
    lucide.createIcons();
    
    function toggleDamageFields(checked) {
      const damageCostField = document.getElementById('damage_cost_field');
      const damagePhotosField = document.getElementById('damage_photos_field');
      
      if (checked) {
        damageCostField.classList.remove('hidden');
        damagePhotosField.classList.remove('hidden');
        document.getElementById('damage_cost').required = true;
      } else {
        damageCostField.classList.add('hidden');
        damagePhotosField.classList.add('hidden');
        document.getElementById('damage_cost').required = false;
        document.getElementById('damage_cost').value = '';
      }
    }
  </script>
</body>
</html>
