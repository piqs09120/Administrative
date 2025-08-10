<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dashboard - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
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
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white flex items-center">
                    <i data-lucide="layout-dashboard" class="w-7 h-7 mr-2 text-blue-700"></i>Dashboard
                </h1>
                <div class="flex space-x-2">
                    <a href="{{ route('reservations.create') }}" class="btn btn-outline btn-sm flex items-center">
                        <i data-lucide="calendar-plus" class="w-4 h-4 mr-1"></i>New Reservation
                </a>
                    <a href="{{ route('orders.create') }}" class="btn btn-outline btn-sm flex items-center">
                        <i data-lucide="shopping-cart" class="w-4 h-4 mr-1"></i>Add Order
                </a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline btn-sm flex items-center">
                        <i data-lucide="boxes" class="w-4 h-4 mr-1"></i>Update Inventory
                </a>
                    <a href="{{ route('finance.reports') }}" class="btn btn-outline btn-sm flex items-center">
                        <i data-lucide="bar-chart-3" class="w-4 h-4 mr-1"></i>View Reports
                    </a>
                </div>
            </div>
            <div class="card bg-base-100 shadow-xl mb-8">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4 flex items-center">
                        <i data-lucide="activity" class="w-5 h-5 mr-2 text-blue-600"></i>Reservations & Stats
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Today's Reservations -->
                        <div class="relative bg-blue-50 dark:bg-blue-900 p-6 rounded-xl border-l-8 border-blue-500 shadow-md hover:shadow-xl transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                                <p class="text-sm text-blue-700 dark:text-blue-200 font-medium flex items-center gap-1">
                                    <i data-lucide="calendar-days" class="w-4 h-4"></i>
                            Today's Reservations
                        </p>
                                <h3 class="text-2xl font-bold mt-2 text-blue-900 dark:text-blue-100">
                            {{ $todaysReservations ?? '24' }}
                        </h3>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-2 flex items-center">
                                    <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i>
                            +12% from yesterday
                        </p>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-800 dark:text-blue-300">
                                <i data-lucide="check-circle" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Revenue Overview -->
                    <div class="relative bg-green-50 dark:bg-green-900 p-6 rounded-xl border-l-8 border-green-500 shadow-md hover:shadow-xl transition-all duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-green-700 dark:text-green-200 font-medium flex items-center gap-1">
                                    <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                                    Revenue Overview
                                </p>
                                <h3 class="text-2xl font-bold mt-2 text-green-900 dark:text-green-100">
                                    ₱{{ $revenueToday ?? '12,500' }}
                                </h3>
                                <p class="text-sm text-blue-600 dark:text-blue-400 mt-2 flex items-center">
                                    <i data-lucide="arrow-up-right" class="w-4 h-4 mr-1"></i>
                                    +8% from yesterday
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600 dark:bg-green-800 dark:text-green-300">
                                <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>
                    <!-- User Activity -->
                    <div class="relative bg-purple-50 dark:bg-purple-900 p-6 rounded-xl border-l-8 border-purple-500 shadow-md hover:shadow-xl transition-all duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-purple-700 dark:text-purple-200 font-medium flex items-center gap-1">
                                    <i data-lucide="users" class="w-4 h-4"></i>
                                    User Activity
                                </p>
                                <h3 class="text-2xl font-bold mt-2 text-purple-900 dark:text-purple-100">
                                    {{ $activeUsers ?? '18' }} Active
                                </h3>
                                <p class="text-sm text-purple-600 dark:text-purple-400 mt-2 flex items-center">
                                    <i data-lucide="activity" class="w-4 h-4 mr-1"></i>
                                    +3 new users today
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600 dark:bg-purple-800 dark:text-purple-300">
                                <i data-lucide="user-check" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Inventory Alerts -->
                    <div class="relative bg-red-50 dark:bg-red-900 p-6 rounded-xl border-l-8 border-red-500 shadow-md hover:shadow-xl transition-all duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-red-700 dark:text-red-200 font-medium flex items-center gap-1">
                                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                    Inventory Alerts
                                </p>
                                <h3 class="text-2xl font-bold mt-2 text-red-900 dark:text-red-100">
                                    {{ $inventoryAlerts ?? '3' }} Items
                                </h3>
                                <p class="text-sm text-red-600 dark:text-red-400 mt-2 flex items-center">
                                    <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                                    Low stock items
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-red-100 text-red-600 dark:bg-red-800 dark:text-red-300">
                                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    </div>
  </div>

  @include('partials.soliera_js')
</body>
</html>
