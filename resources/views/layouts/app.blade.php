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
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-6 flex items-center justify-between shadow-sm overflow-visible z-50">
                <div class="flex items-center">
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="mr-4 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i data-lucide="menu" class="w-6 h-6 text-gray-600 dark:text-gray-300"></i>
                    </button>
                    <h2 class="text-3xl font-bold text-gray-800 dark:text-white ml-4">
                        @yield('page-title', 'Dashboard')
                    </h2>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Search Bar -->
                    <div class="relative">
                        <input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-colors w-64" />
                        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                    </div>
                    
                    <!-- Date and Time -->
                    <div class="flex items-center space-x-2 bg-gray-100 dark:bg-gray-700 px-4 py-3 rounded-lg">
                        <i data-lucide="calendar" class="w-5 h-5 text-blue-500"></i>
                        <span class="text-base font-medium text-gray-700 dark:text-gray-300" x-data="{ date: '' }" x-init="setInterval(() => { const now = new Date(); date = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }); }, 1000)" x-text="date"></span>
                        <div class="w-px h-5 bg-gray-300 dark:bg-gray-600"></div>
                        <i data-lucide="clock" class="w-5 h-5 text-green-500"></i>
                        <span class="text-base font-medium text-gray-700 dark:text-gray-300" x-data="{ time: '' }" x-init="setInterval(() => { const now = new Date(); time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }); }, 1000)" x-text="time"></span>
                    </div>
                    
                    <!-- Blue Moon Icon (Dark Mode Toggle) -->
                    <button @click="darkMode = !darkMode" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <template x-if="darkMode">
                            <i data-lucide="sun" class="w-6 h-6 text-yellow-500"></i>
                        </template>
                        <template x-if="!darkMode">
                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                <i data-lucide="moon" class="w-4 h-4 text-white"></i>
                            </div>
                        </template>
                    </button>
                    
                    <!-- User Profile Icon -->
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                            <i data-lucide="user" class="w-6 h-6 text-gray-600 dark:text-gray-300"></i>
                        </div>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-white dark:bg-gray-800 rounded-lg w-52 border border-gray-200 dark:border-gray-700">
                            <li>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
                                    <i data-lucide="user" class="w-4 h-4 text-gray-600"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
                                    <i data-lucide="settings" class="w-4 h-4 text-gray-600"></i>
                                    <span>Settings</span>
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors w-full text-left">
                                        <i data-lucide="log-out" class="w-4 h-4 text-gray-600"></i>
                                        <span>Sign out</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
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