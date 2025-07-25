<div class="bg-gradient-to-b from-blue-900 via-blue-800 to-blue-700 border-r border-blue-600 pt-5 pb-4 flex flex-col w-64 transition-all duration-300 ease-in-out shadow-xl" id="sidebar">
  <div class="flex items-center flex-shrink-0 px-4 animate-fadeIn">
    <h1 class="text-xl font-bold text-white sidebar-text">Soliera Restaurant</h1>
  </div>
  <div class="mt-5 flex-1 flex flex-col overflow-y-auto">
    <nav class="flex-1 px-2 space-y-1">
      <!-- Dashboard -->
      <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg sidebar-item hover:bg-blue-600 hover:bg-opacity-50 text-white {{ request()->routeIs('dashboard') ? 'bg-blue-700' : '' }}">
        <i data-lucide="home" class="w-5 h-5 text-blue-200"></i>
        <span class="ml-3 sidebar-text">Dashboard</span>
      </a>
      
      <!-- Legal Management -->
      <div class="collapse group">
        <input type="checkbox" class="peer" {{ request()->routeIs('legal.*') ? 'checked' : '' }} /> 
        <div class="collapse-title flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg peer-checked:bg-blue-600 peer-checked:bg-opacity-50 text-white sidebar-item {{ request()->routeIs('legal.*') ? 'bg-blue-700' : '' }}">
          <div class="flex items-center">
            <i data-lucide="gavel" class="w-5 h-5 text-blue-200"></i>
            <span class="ml-3 sidebar-text">Legal Management</span>
          </div>
          <i data-lucide="chevron-right" class="w-4 h-4 text-blue-200 transform transition-transform duration-200 peer-checked:rotate-90"></i>
        </div>
        <div class="collapse-content pl-10 pr-4 py-1 space-y-1"> 
          <a href="{{ route('legal.index') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-600 hover:bg-opacity-30 text-blue-100 sidebar-item {{ request()->routeIs('legal.index') ? 'bg-blue-700 text-white' : '' }}">View Legal Cases</a>
          <a href="{{ route('legal.create') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-600 hover:bg-opacity-30 text-blue-100 sidebar-item {{ request()->routeIs('legal.create') ? 'bg-blue-700 text-white' : '' }}">Add New Case</a>
        </div>
      </div>
      
      <!-- Document Management -->
      <div class="collapse group">
        <input type="checkbox" class="peer" {{ request()->routeIs('document.*') ? 'checked' : '' }} /> 
        <div class="collapse-title flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg peer-checked:bg-blue-600 peer-checked:bg-opacity-50 text-white sidebar-item {{ request()->routeIs('document.*') ? 'bg-blue-700' : '' }}">
          <div class="flex items-center">
            <i data-lucide="file-text" class="w-5 h-5 text-blue-200"></i>
            <span class="ml-3 sidebar-text">Document Management</span>
          </div>
          <i data-lucide="chevron-right" class="w-4 h-4 text-blue-200 transform transition-transform duration-200 peer-checked:rotate-90"></i>
        </div>
        <div class="collapse-content pl-10 pr-4 py-1 space-y-1"> 
          <a href="{{ route('document.index') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-600 hover:bg-opacity-30 text-blue-100 sidebar-item {{ request()->routeIs('document.index') ? 'bg-blue-700 text-white' : '' }}">View Documents</a>
          <a href="{{ route('document.create') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-600 hover:bg-opacity-30 text-blue-100 sidebar-item {{ request()->routeIs('document.create') ? 'bg-blue-700 text-white' : '' }}">Add New Document</a>
        </div>
      </div>
      
      <!-- Visitor Management -->
      <div class="collapse group">
        <input type="checkbox" class="peer" {{ request()->routeIs('visitor.*') ? 'checked' : '' }} /> 
        <div class="collapse-title flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg peer-checked:bg-blue-600 peer-checked:bg-opacity-50 text-white sidebar-item {{ request()->routeIs('visitor.*') ? 'bg-blue-700' : '' }}">
          <div class="flex items-center">
            <i data-lucide="users" class="w-5 h-5 text-blue-200"></i>
            <span class="ml-3 sidebar-text">Visitor Management</span>
          </div>
          <i data-lucide="chevron-right" class="w-4 h-4 text-blue-200 transform transition-transform duration-200 peer-checked:rotate-90"></i>
        </div>
        <div class="collapse-content pl-10 pr-4 py-1 space-y-1"> 
          <a href="{{ route('visitor.index') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-600 hover:bg-opacity-30 text-blue-100 sidebar-item {{ request()->routeIs('visitor.index') ? 'bg-blue-700 text-white' : '' }}">View Visitors</a>
          <a href="{{ route('visitor.create') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-600 hover:bg-opacity-30 text-blue-100 sidebar-item {{ request()->routeIs('visitor.create') ? 'bg-blue-700 text-white' : '' }}">Add New Visitor</a>
        </div>
      </div>
      
      <!-- Facilities Management -->
      <div class="collapse group">
        <input type="checkbox" class="peer" {{ request()->routeIs('facilities.*') ? 'checked' : '' }} /> 
        <div class="collapse-title flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg peer-checked:bg-blue-600 peer-checked:bg-opacity-50 text-white sidebar-item {{ request()->routeIs('facilities.*') ? 'bg-blue-700' : '' }}">
          <div class="flex items-center">
            <i data-lucide="building" class="w-5 h-5 text-blue-200"></i>
            <span class="ml-3 sidebar-text">Facilities Management</span>
          </div>
          <i data-lucide="chevron-right" class="w-4 h-4 text-blue-200 transform transition-transform duration-200 peer-checked:rotate-90"></i>
        </div>
        <div class="collapse-content pl-10 pr-4 py-1 space-y-1"> 
          <a href="{{ route('facilities.index') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-600 hover:bg-opacity-30 text-blue-100 sidebar-item {{ request()->routeIs('facilities.index') ? 'bg-blue-700 text-white' : '' }}">View Facilities</a>
          <a href="{{ route('facilities.create') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-600 hover:bg-opacity-30 text-blue-100 sidebar-item {{ request()->routeIs('facilities.create') ? 'bg-blue-700 text-white' : '' }}">Add New Facility</a>
        </div>
      </div>
      
      <!-- Access Protection -->
      <div class="collapse group">
        <input type="checkbox" class="peer" {{ request()->routeIs('access.*') ? 'checked' : '' }} /> 
        <div class="collapse-title flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg peer-checked:bg-blue-600 peer-checked:bg-opacity-50 text-white sidebar-item {{ request()->routeIs('access.*') ? 'bg-blue-700' : '' }}">
          <div class="flex items-center">
            <i data-lucide="shield" class="w-5 h-5 text-blue-200"></i>
            <span class="ml-3 sidebar-text">User Management</span>
          </div>
          <i data-lucide="chevron-right" class="w-4 h-4 text-blue-200 transform transition-transform duration-200 peer-checked:rotate-90"></i>
        </div>
        <div class="collapse-content pl-10 pr-4 py-1 space-y-1"> 
          <a href="{{ route('access.logs') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-600 hover:bg-opacity-30 text-blue-100 sidebar-item {{ request()->routeIs('access.logs') ? 'bg-blue-700 text-white' : '' }}">Access Logs</a>
          <a href="{{ route('access.users') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-600 hover:bg-opacity-30 text-blue-100 sidebar-item {{ request()->routeIs('access.users') ? 'bg-blue-700 text-white' : '' }}">User Management</a>
          <a href="{{ route('access.roles') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-600 hover:bg-opacity-30 text-blue-100 sidebar-item {{ request()->routeIs('access.roles') ? 'bg-blue-700 text-white' : '' }}">Role Management</a>
          <a href="{{ route('access.security') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-600 hover:bg-opacity-30 text-blue-100 sidebar-item {{ request()->routeIs('access.security') ? 'bg-blue-700 text-white' : '' }}">Security Settings</a>
        </div>
      </div>
      
      <!-- Compliance -->
      <!-- (Removed) -->
      
      <!-- Finance -->
      <!-- (Removed) -->
      
      <!-- Notifications -->
      <!-- (Removed) -->
      
      <!-- Profile -->
      <!-- (Removed) -->
      
      <!-- Help & Support -->
      <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg sidebar-item hover:bg-blue-600 hover:bg-opacity-50 text-white">
        <i data-lucide="help-circle" class="w-5 h-5 text-blue-200"></i>
        <span class="ml-3 sidebar-text">Help & Support</span>
      </div>
    </nav>
  </div>
</div> 
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide && window.lucide.createIcons) {
      window.lucide.createIcons();
    }
  });
</script> 