<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Hotel & Restaurant Management System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest" defer></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen overflow-visible" x-data="{ sidebarCollapsed: false, darkMode: false, showLogout: false }" :class="{ 'dark': darkMode, 'sidebar-collapsed': sidebarCollapsed }">
    <div class="flex w-full min-h-screen transition-colors duration-300 ease-in-out overflow-visible">
        <!-- Sidebar -->
        @include('partials.sidebarr')
        <!-- Main Content -->
        <div class="flex-1 flex flex-col dark:bg-gray-900 transition-all duration-300 ease-in-out overflow-visible">
            <!-- Header -->
            @include('partials.navbar')
            <!-- Logout Modal -->
            <!-- (Removed old logout modal) -->
            <!-- Page Content -->
            <main class="flex-1 p-6">
                @if(session('success'))
                    <div class="alert alert-success mb-4">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error mb-4">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <!-- Alpine.js for interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
      function lucideInit() {
        if (window.lucide && window.lucide.createIcons) {
          window.lucide.createIcons();
        }
      }
      document.addEventListener('DOMContentLoaded', lucideInit);
      document.addEventListener('alpine:init', lucideInit);
      document.addEventListener('htmx:afterSwap', lucideInit);
      window.addEventListener('pageshow', lucideInit);
      window.addEventListener('popstate', lucideInit);
    </script>
    <style>
    .sidebar-collapsed #sidebar {
    width: 5rem !important;
}
.sidebar-collapsed .sidebar-text {
    display: none !important;
}
</style>
</body>
</html>