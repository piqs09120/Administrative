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
            <header class="bg-white dark:bg-gray-800 border-b border-blue-600 px-6 py-3 flex items-center justify-between shadow-sm overflow-visible z-50">
                <div class="flex items-center">
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="mr-4 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-blue-700 transition-colors">
                        <i data-lucide="menu" class="w-6 h-6 text-blue-900"></i>
                    </button>
                    <h2 class="text-lg font-medium text-gray-800 dark:text-white">
                        @yield('page-title', 'Dashboard')
                    </h2>
                </div>
                <div class="flex items-center gap-4 overflow-visible">
                    <div class="relative">
                        <input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-colors" />
                        <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <!-- DateTime -->
                    <div x-data="{ date: '', time: '' }" x-init="setInterval(() => { const now = new Date(); date = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }); time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }); }, 1000)" class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700 px-3 py-1 rounded-lg transition-colors">
                        <div class="flex items-center gap-1 text-gray-600 dark:text-gray-300">
                            <i data-lucide="calendar" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                            <span class="text-sm font-medium" x-text="date"></span>
                        </div>
                        <div class="h-4 w-px bg-gray-300 dark:bg-gray-600"></div>
                        <div class="flex items-center gap-1 text-gray-600 dark:text-gray-300">
                            <i data-lucide="clock" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                            <span class="text-sm font-medium" x-text="time"></span>
                        </div>
                    </div>
                    <!-- Dark mode toggle -->
                    <button @click="darkMode = !darkMode" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <template x-if="darkMode">
                            <i data-lucide="sun" class="w-5 h-5 text-yellow-400"></i>
                        </template>
                        <template x-if="!darkMode">
                            <i data-lucide="moon" class="w-5 h-5 text-gray-600"></i>
                        </template>
                    </button>
                    <!-- User Dropdown -->
                    <div class="dropdown dropdown-end z-50">
                        <label tabindex="0" class="p-2 cursor-pointer">
                            <div class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center">
                                <i data-lucide="user" class="w-2.5 h-2.5"></i>
                            </div>
                        </label>
                        <ul tabindex="0" class="dropdown-content menu mt-1 z-50 w-36 bg-white shadow border text-sm">
                            <li>
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-2 py-1">
                                    <i data-lucide="user" class="w-2.5 h-2.5 mr-1"></i>Profile
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center px-2 py-1">
                                    <i data-lucide="settings" class="w-2.5 h-2.5 mr-1"></i>Settings
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center px-2 py-1 w-full text-left">
                                        <i data-lucide="log-out" class="w-2.5 h-2.5 mr-1"></i>Sign out
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