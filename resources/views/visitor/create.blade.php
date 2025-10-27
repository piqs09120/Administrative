<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Visitors - Review & Actions</title>
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
        <!-- Toast notifications in bottom right corner -->
        @if(session('success'))
          <div class="toast toast-bottom toast-end z-50">
            <div class="alert alert-success shadow-lg">
              <i data-lucide="check-circle" class="w-5 h-5"></i>
              <span>{{ session('success') }}</span>
            </div>
          </div>
        @endif

        @if(session('error'))
          <div class="toast toast-bottom toast-end z-50">
            <div class="alert alert-error shadow-lg">
              <i data-lucide="alert-circle" class="w-5 h-5"></i>
              <span>{{ session('error') }}</span>
            </div>
          </div>
        @endif

        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-3">
            <h1 class="text-2xl font-semibold text-gray-800">New Visitors (Pending Review)</h1>
          </div>
        </div>

        <div class="border-b border-gray-200 mb-6"></div>

        <x-table-card :title="'New Visitors (Pending Review)'" :pagination="method_exists($pendingVisitors, 'links') ? $pendingVisitors->links() : null">
          <table class="table table-zebra w-full text-sm">
                <thead>
                  <tr class="text-gray-600">
                    <th>Name</th>
                    <th>Email</th>
                    <th>Purpose</th>
                    <th>Department</th>
                    <th>Registered At</th>
                    <th class="text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($pendingVisitors as $visitor)
                    <tr class="hover:bg-gray-50 transition-all duration-200">
                      <td class="font-medium">{{ $visitor->name }}</td>
                      <td>{{ $visitor->email ?? 'N/A' }}</td>
                      <td>{{ \Illuminate\Support\Str::limit($visitor->purpose ?? 'N/A', 40) }}</td>
                      <td>{{ $visitor->department ?? ($visitor->facility->name ?? 'N/A') }}</td>
                      <td>{{ $visitor->created_at?->format('M d, Y h:i A') }}</td>
                      <td>
                        <div class="flex justify-end gap-3">
                          <button type="button" class="p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110" data-visitor-id="{{ $visitor->id }}" title="View" style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);" onmouseover="this.style.background='#E6940F'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'" onmouseout="this.style.background='#F7A923'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                            <i data-lucide="eye" class="w-4 h-4" style="fill: none;"></i>
                          </button>
                          <a href="/test-id-verification?visitor_id={{ $visitor->id }}" class="p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110" title="ID Verification" style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);" onmouseover="this.style.background='#E6940F'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'" onmouseout="this.style.background='#F7A923'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                            <i data-lucide="shield-check" class="w-4 h-4" style="fill: none;"></i>
                          </a>
                          <form action="{{ route('visitor.approve', $visitor->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110" title="Approve" style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);" onmouseover="this.style.background='#E6940F'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'" onmouseout="this.style.background='#F7A923'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                              <i data-lucide="check" class="w-4 h-4" style="fill: none;"></i>
                            </button>
                          </form>
                          <form action="{{ route('visitor.decline', $visitor->id) }}" method="POST" onsubmit="return confirm('Decline this visitor?');">
                            @csrf
                            <button type="submit" class="p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110" title="Decline" style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);" onmouseover="this.style.background='#E6940F'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'" onmouseout="this.style.background='#F7A923'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                              <i data-lucide="x" class="w-4 h-4" style="fill: none;"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center text-gray-500 py-6">No pending visitors.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
        </x-table-card>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')

  <script>
    function setupDarkMode() {
      const toggle = document.getElementById('darkModeToggle');
      const sunIcon = document.getElementById('sunIcon');
      const moonIcon = document.getElementById('moonIcon');
      function updateIcons() {
        if(document.documentElement.classList.contains('dark')) {
          sunIcon && sunIcon.classList.remove('hidden');
          moonIcon && moonIcon.classList.add('hidden');
        } else {
          sunIcon && sunIcon.classList.add('hidden');
          moonIcon && moonIcon.classList.remove('hidden');
        }
      }
      const isDarkMode = localStorage.getItem('darkMode') === 'true';
      if (isDarkMode) {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark');
      }
      updateIcons();
      if (toggle) {
        toggle.addEventListener('click', function() {
          if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            document.body.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
          } else {
            document.documentElement.classList.add('dark');
            document.body.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
          }
          updateIcons();
        });
      }
    }

    function updateDateTime() {
      const now = new Date();
      const dateElement = document.getElementById('currentDate');
      const timeElement = document.getElementById('currentTime');
      const dateOptions = { weekday: 'short', month: 'short', day: 'numeric' };
      const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
      if (dateElement) dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
      if (timeElement) timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
      setupDarkMode();
      updateDateTime();
      setInterval(updateDateTime, 1000);

      // Auto-hide toast notifications after 5 seconds
      setTimeout(() => {
        document.querySelectorAll('.toast').forEach(toast => {
          toast.style.opacity = '0';
          toast.style.transition = 'opacity 0.5s ease-out';
          setTimeout(() => toast.remove(), 500);
        });
      }, 5000);

      // Attach click handlers for view buttons
      document.querySelectorAll('.btn-view-visitor').forEach(btn => {
        btn.addEventListener('click', async function() {
          const id = this.getAttribute('data-visitor-id');
          try {
            const res = await fetch(`{{ route('visitor.details', '') }}/${id}`);
            if (!res.ok) throw new Error('Failed to load visitor');
            const visitor = await res.json();
            openVisitorPreviewModal(visitor);
          } catch (e) {
            console.error(e);
            alert('Unable to load visitor details.');
          }
        });
      });
    });

    function openVisitorPreviewModal(visitor) {
      const modal = document.getElementById('visitorPreviewModal');
      if (!modal) return;
      // Fill contents
      modal.querySelector('[data-field="name"]').textContent = visitor.name ?? '-';
      modal.querySelector('[data-field="email"]').textContent = visitor.email ?? '-';
      modal.querySelector('[data-field="contact"]').textContent = visitor.contact ?? '-';
      modal.querySelector('[data-field="purpose"]').textContent = visitor.purpose ?? '-';
      modal.querySelector('[data-field="department"]').textContent = visitor.department ?? '-';
      modal.querySelector('[data-field="host"]').textContent = visitor.host_employee ?? '-';
      modal.querySelector('[data-field="company"]').textContent = visitor.company ?? '-';
      modal.querySelector('[data-field="id_type"]').textContent = visitor.id_type ?? '-';
      modal.querySelector('[data-field="id_number"]').textContent = visitor.id_number ?? '-';
      modal.querySelector('[data-field="vehicle_plate"]').textContent = visitor.vehicle_plate ?? '-';
      modal.querySelector('[data-field="time_in"]').textContent = visitor.time_in ? new Date(visitor.time_in).toLocaleString() : 'Not checked in';
      modal.querySelector('[data-field="time_out"]').textContent = visitor.time_out ? new Date(visitor.time_out).toLocaleString() : 'IN';

      // Open
      modal.classList.add('modal-open');
      document.body.classList.add('modal-open');
    }

    function closeVisitorPreviewModal() {
      const modal = document.getElementById('visitorPreviewModal');
      if (!modal) return;
      modal.classList.remove('modal-open');
      document.body.classList.remove('modal-open');
    }
  </script>

  <!-- Visitor Preview Modal -->
  <div id="visitorPreviewModal" class="modal" onclick="closeVisitorPreviewModal()">
    <div class="modal-box max-w-3xl" onclick="event.stopPropagation()">
      <div class="modal-header flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold"><i data-lucide="user" class="w-5 h-5 mr-2 inline"></i>Visitor Preview</h3>
        <button class="btn btn-ghost btn-sm" onclick="closeVisitorPreviewModal()"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><span class="font-semibold">Name:</span> <span data-field="name">-</span></div>
        <div><span class="font-semibold">Email:</span> <span data-field="email">-</span></div>
        <div><span class="font-semibold">Contact:</span> <span data-field="contact">-</span></div>
        <div><span class="font-semibold">Purpose:</span> <span data-field="purpose">-</span></div>
        <div><span class="font-semibold">Department:</span> <span data-field="department">-</span></div>
        <div><span class="font-semibold">Host:</span> <span data-field="host">-</span></div>
        <div><span class="font-semibold">Company:</span> <span data-field="company">-</span></div>
        <div><span class="font-semibold">ID Type:</span> <span data-field="id_type">-</span></div>
        <div><span class="font-semibold">ID Number:</span> <span data-field="id_number">-</span></div>
        <div><span class="font-semibold">Vehicle Plate:</span> <span data-field="vehicle_plate">-</span></div>
        <div><span class="font-semibold">Time In:</span> <span data-field="time_in">-</span></div>
        <div><span class="font-semibold">Time Out:</span> <span data-field="time_out">-</span></div>
      </div>
      <div class="modal-footer mt-6 flex justify-end gap-2">
        <button class="btn btn-ghost" onclick="closeVisitorPreviewModal()">Close</button>
        <a class="btn btn-primary" href="#" onclick="event.preventDefault(); closeVisitorPreviewModal();">OK</a>
      </div>
    </div>
  </div>
</body>
</html>
