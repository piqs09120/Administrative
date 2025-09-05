<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile - {{ $account->employee_name }}</title>
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
      <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
        <div class="flex items-center mb-6">
          <a href="{{ route('access.users') }}" class="btn btn-ghost btn-sm mr-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back
          </a>
          <h1 class="text-3xl font-bold text-gray-800">User Profile</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-1">
            <div class="card bg-white shadow-xl">
              <div class="card-body items-center text-center">
                <div class="avatar mb-4">
                  <div class="w-24 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($account->employee_name) }}&background=random" alt="Avatar" />
                  </div>
                </div>
                <h2 class="card-title text-2xl mb-1">{{ $account->employee_name }}</h2>
                <p class="text-gray-600">{{ $account->email ?? '—' }}</p>
                <div class="badge mt-3 {{ strtolower($account->status)==='active' ? 'badge-success' : 'badge-error' }}">
                  {{ ucfirst($account->status ?? 'inactive') }}
                </div>
              </div>
            </div>
          </div>

          <div class="lg:col-span-2">
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h3 class="card-title text-lg mb-4 flex items-center"><i data-lucide="id-card" class="w-5 h-5 mr-2"></i>Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <div class="text-sm text-gray-500">Employee ID</div>
                    <div class="font-medium">{{ $account->employee_id ?? '—' }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Department</div>
                    <div class="font-medium">{{ $account->dept_name ?? '—' }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Role</div>
                    <div class="font-medium">{{ $account->role ?? 'Staff' }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Created</div>
                    <div class="font-medium">{{ optional($account->created_at)->format('M d, Y') }}</div>
                  </div>
                </div>

                @if($laravelUser)
                <div class="mt-6">
                  <h3 class="card-title text-lg mb-2 flex items-center"><i data-lucide="user" class="w-5 h-5 mr-2"></i>Application Account</h3>
                  <div class="text-sm text-gray-600">Linked User ID: {{ $laravelUser->id }} | Email: {{ $laravelUser->email }}</div>
                </div>
                @endif

                <div class="card-actions justify-end mt-6">
                  <a href="{{ route('access.users') }}" class="btn btn-outline">Close</a>
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


