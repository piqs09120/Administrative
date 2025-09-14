<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Reports - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
</head>
<body class="bg-gray-50">
  <div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    @include('partials.sidebarr')
    <!-- Main content -->
    <div class="flex flex-col flex-1 overflow-hidden">
      <!-- Header -->
      @include('partials.navbar')

      <!-- Main content area -->
      <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
        <!-- Page Header -->
        <div class="pb-5 mb-6 animate-fadeIn">
          <div class="border-b-2 border-gray-500 w-full"></div>
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
              <a href="{{ route('document.index') }}" class="btn btn-ghost btn-sm" title="Back to Documents">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
              </a>
              <div>
                <h1 class="text-2xl font-semibold bg-white bg-clip-text text-[#191970]" style="color: var(--color-charcoal-ink);">Document Reports</h1>
                <p class="text-gray-600">Analytics and insights for document management</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <button onclick="refreshReports()" class="btn btn-outline">
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>Refresh
              </button>
              <button onclick="exportReports()" class="btn btn-primary">
                <i data-lucide="download" class="w-4 h-4 mr-2"></i>Export
              </button>
            </div>
          </div>
        </div>

        <!-- Empty Content Area -->
        <div class="card bg-white shadow-xl">
          <div class="card-body text-center py-20">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <i data-lucide="bar-chart" class="w-10 h-10 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">Reports Content Removed</h3>
            <p class="text-gray-500 text-sm">Content has been cleared as requested.</p>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    function refreshReports() {
      window.location.reload();
    }

    function exportReports() {
      // Function removed - no content to export
    }
  </script>
</body>
</html>
