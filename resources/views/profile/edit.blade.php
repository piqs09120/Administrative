<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Profile - Soliera</title>
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
        @if(session('success'))
          <div class="alert alert-success mb-6">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-error mb-6">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <span>{{ session('error') }}</span>
          </div>
        @endif

        <!-- Profile Content -->
        <div class="max-w-4xl mx-auto space-y-6">
          <!-- Update Profile Information -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
              <i data-lucide="user" class="w-6 h-6 text-blue-600 mr-3"></i>
              <h2 class="text-xl font-semibold text-gray-900">Update Profile Information</h2>
            </div>
            <div class="max-w-2xl">
              @include('profile.partials.update-profile-information-form')
            </div>
          </div>

          <!-- Update Password -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
              <i data-lucide="lock" class="w-6 h-6 text-green-600 mr-3"></i>
              <h2 class="text-xl font-semibold text-gray-900">Update Password</h2>
            </div>
            <div class="max-w-2xl">
              @include('profile.partials.update-password-form')
            </div>
          </div>

          <!-- Delete Account -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
              <i data-lucide="trash-2" class="w-6 h-6 text-red-600 mr-3"></i>
              <h2 class="text-xl font-semibold text-gray-900">Delete Account</h2>
            </div>
            <div class="max-w-2xl">
              @include('profile.partials.delete-user-form')
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')
</body>
</html>
