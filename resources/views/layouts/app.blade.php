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
    
    <!-- DaisyUI and Tailwind -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/soliera.css', 'resources/css/sidebar-collapse.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen overflow-visible">
    <div class="flex w-full min-h-screen transition-colors duration-300 ease-in-out overflow-visible">
        <!-- Sidebar -->
        @include('partials.sidebarr')
        <!-- Main Content -->
        <div class="flex-1 flex flex-col dark:bg-gray-900 transition-all duration-300 ease-in-out overflow-visible">
            <!-- Header -->
            @include('partials.navbar')
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('scripts')
</body>
</html>