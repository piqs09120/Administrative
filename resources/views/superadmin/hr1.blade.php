<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Human Resources 1 - Soliera</title>
    <link rel="icon" href="swt.jpg" type="image/x-icon">
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
                <!-- Page Header -->
                <div class="mb-8">
                    <div class="mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 mb-2">Human Resources 1</h1>
                            <p class="text-gray-600">Manage HR module 1 operations</p>
                        </div>
                    </div>
                </div>

                <!-- Content Placeholder -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="users" class="w-10 h-10 text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">Human Resources 1 Module</h3>
                        <p class="text-gray-500 text-sm mb-4">This module is under development</p>
                        <div class="badge badge-info">Super Admin Access Only</div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    @include('partials.soliera_js')
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>

