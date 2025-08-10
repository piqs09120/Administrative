<div class="bg-[#001f54] pt-5 pb-4 flex flex-col fixed md:relative h-full transition-all duration-300 ease-in-out shadow-xl transform -translate-x-full md:transform-none md:translate-x-0 w-64" id="sidebar">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between flex-shrink-0 px-4 mb-6 text-center">
      <h1 class="text-xl font-bold text-white items-center gap-2">
         <img id="sidebar-logo" src="{{asset('images/logo/logofinal.png')}}" alt="" >
         <img id="sonly" class="hidden w-full h-25" src="{{asset('images/logo/sonly.png')}}" alt="">
        
      </h1>
    </div>

     
    
    <!-- Navigation Menu -->
    <div class="flex-1 flex flex-col overflow-y-auto">
      <nav class="flex-1 px-2 space-y-1">
        <!-- Section Label -->
        <div class="px-4 py-2">
          <span class="text-xs font-semibold uppercase tracking-wider text-blue-300 sidebar-text">Main Menu</span>
        </div>
        
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="block">
          <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group {{ request()->routeIs('dashboard') ? 'bg-blue-700' : '' }}">
            <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
              <i data-lucide="home" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
            </div>
            <span class="ml-3 sidebar-text">Dashboard</span>
          </div>
        </a>

        <!-- Section Label -->
        <div class="px-4 py-2 mt-4">
          <span class="text-xs font-semibold uppercase tracking-wider text-blue-300 sidebar-text">Management</span>
        </div>
        
        <!-- Legal Management -->
        <div class="collapse group">
          <input type="checkbox" class="peer" {{ request()->routeIs('legal.*') ? 'checked' : '' }} /> 
          <div class="collapse-title flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all peer-checked:bg-blue-600/50 text-white group {{ request()->routeIs('legal.*') ? 'bg-blue-700' : '' }}">
            <div class="flex items-center">
              <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                <i data-lucide="gavel" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
              </div>
              <span class="ml-3 sidebar-text">Legal Management</span>
            </div>
            <i class="w-4 h-4 text-blue-200 transform transition-transform duration-200 peer-checked:rotate-90 dropdown-icon" data-lucide="chevron-down"></i>
          </div>
          <div class="collapse-content pl-14 pr-4 py-1 space-y-1"> 
            <a href="{{ route('legal.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white {{ request()->routeIs('legal.index') ? 'bg-blue-700 text-white' : '' }}">
              <span class="flex items-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4 text-[#F7B32B]"></i>
                View Legal Cases
              </span>
            </a>
            <a href="{{ route('legal.create') }}" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white {{ request()->routeIs('legal.create') ? 'bg-blue-700 text-white' : '' }}">
              <span class="flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4 text-[#F7B32B]"></i>
                Add New Case
              </span>
            </a>
          </div>
        </div>

        <!-- Document Management -->
        <div class="collapse group">
          <input type="checkbox" class="peer" {{ request()->routeIs('document.*') ? 'checked' : '' }} /> 
          <div class="collapse-title flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all peer-checked:bg-blue-600/50 text-white group {{ request()->routeIs('document.*') ? 'bg-blue-700' : '' }}">
            <div class="flex items-center">
              <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                <i data-lucide="file-text" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
              </div>
              <span class="ml-3 sidebar-text">Document Management</span>
            </div>
            <i class="w-4 h-4 text-blue-200 transform transition-transform duration-200 peer-checked:rotate-90 dropdown-icon" data-lucide="chevron-down"></i>
          </div>
          <div class="collapse-content pl-14 pr-4 py-1 space-y-1"> 
            <a href="{{ route('document.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white {{ request()->routeIs('document.index') ? 'bg-blue-700 text-white' : '' }}">
              <span class="flex items-center gap-2">
                <i data-lucide="folder-open" class="w-4 h-4 text-[#F7B32B]"></i>
                View Documents
              </span>
            </a>
            <a href="{{ route('document.create') }}" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white {{ request()->routeIs('document.create') ? 'bg-blue-700 text-white' : '' }}">
              <span class="flex items-center gap-2">
                <i data-lucide="upload" class="w-4 h-4 text-[#F7B32B]"></i>
                Add New Document
              </span>
            </a>
          </div>
        </div>

        <!-- Visitor Management -->
        <div class="collapse group">
          <input type="checkbox" class="peer" {{ request()->routeIs('visitor.*') ? 'checked' : '' }} /> 
          <div class="collapse-title flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all peer-checked:bg-blue-600/50 text-white group {{ request()->routeIs('visitor.*') ? 'bg-blue-700' : '' }}">
            <div class="flex items-center">
              <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                <i data-lucide="users" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
              </div>
              <span class="ml-3 sidebar-text">Visitor Management</span>
            </div>
            <i class="w-4 h-4 text-blue-200 transform transition-transform duration-200 peer-checked:rotate-90 dropdown-icon" data-lucide="chevron-down"></i>
          </div>
          <div class="collapse-content pl-14 pr-4 py-1 space-y-1"> 
            <a href="{{ route('visitor.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white {{ request()->routeIs('visitor.index') ? 'bg-blue-700 text-white' : '' }}">
              <span class="flex items-center gap-2">
                <i data-lucide="list" class="w-4 h-4 text-[#F7B32B]"></i>
                View Visitors
              </span>
            </a>
            <a href="{{ route('visitor.create') }}" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white {{ request()->routeIs('visitor.create') ? 'bg-blue-700 text-white' : '' }}">
              <span class="flex items-center gap-2">
                <i data-lucide="user-plus" class="w-4 h-4 text-[#F7B32B]"></i>
                Add New Visitor
              </span>
            </a>
          </div>
        </div>

        <!-- Facilities Management -->
        <div class="collapse group">
          <input type="checkbox" class="peer" {{ request()->routeIs('facilities.*') ? 'checked' : '' }} /> 
          <div class="collapse-title flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all peer-checked:bg-blue-600/50 text-white group {{ request()->routeIs('facilities.*') ? 'bg-blue-700' : '' }}">
            <div class="flex items-center">
              <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                <i data-lucide="building" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
              </div>
              <span class="ml-3 sidebar-text">Facilities Management</span>
            </div>
            <i class="w-4 h-4 text-blue-200 transform transition-transform duration-200 peer-checked:rotate-90 dropdown-icon" data-lucide="chevron-down"></i>
          </div>
          <div class="collapse-content pl-14 pr-4 py-1 space-y-1"> 
            <a href="{{ route('facilities.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white {{ request()->routeIs('facilities.index') ? 'bg-blue-700 text-white' : '' }}">
              <span class="flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 text-[#F7B32B]"></i>
                View Facilities
              </span>
            </a>
            <a href="{{ route('facilities.create') }}" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white {{ request()->routeIs('facilities.create') ? 'bg-blue-700 text-white' : '' }}">
              <span class="flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-[#F7B32B]"></i>
                Add New Facility
              </span>
            </a>
          </div>
        </div>

        <!-- Section Label -->
        <div class="px-4 py-2 mt-4">
          <span class="text-xs font-semibold uppercase tracking-wider text-blue-300 sidebar-text">System</span>
        </div>
        
        <!-- Access Protection -->
        <div class="collapse group">
          <input type="checkbox" class="peer" {{ request()->routeIs('access.*') ? 'checked' : '' }} /> 
          <div class="collapse-title flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all peer-checked:bg-blue-600/50 text-white group {{ request()->routeIs('access.*') ? 'bg-blue-700' : '' }}">
            <div class="flex items-center">
              <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
                <i data-lucide="shield" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
              </div>
              <span class="ml-3 sidebar-text">User Management</span>
            </div>
            <i class="w-4 h-4 text-blue-200 transform transition-transform duration-200 peer-checked:rotate-90 dropdown-icon" data-lucide="chevron-down"></i>
          </div>
          <div class="collapse-content pl-14 pr-4 py-1 space-y-1"> 
            <a href="{{ route('access.logs') }}" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white {{ request()->routeIs('access.logs') ? 'bg-blue-700 text-white' : '' }}">
              <span class="flex items-center gap-2">
                <i data-lucide="activity" class="w-4 h-4 text-[#F7B32B]"></i>
                Access Logs
              </span>
            </a>
            <a href="{{ route('access.users') }}" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white {{ request()->routeIs('access.users') ? 'bg-blue-700 text-white' : '' }}">
              <span class="flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4 text-[#F7B32B]"></i>
                Access Control
              </span>
            </a>
            <a href="{{ route('access.roles') }}" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white {{ request()->routeIs('access.roles') ? 'bg-blue-700 text-white' : '' }}">
              <span class="flex items-center gap-2">
                <i data-lucide="user-check" class="w-4 h-4 text-[#F7B32B]"></i>
                Role Management
              </span>
            </a>
            <a href="{{ route('access.security') }}" class="block px-3 py-2 text-sm rounded-lg transition-all hover:bg-blue-600/30 text-blue-100 hover:text-white {{ request()->routeIs('access.security') ? 'bg-blue-700 text-white' : '' }}">
              <span class="flex items-center gap-2">
                <i data-lucide="lock" class="w-4 h-4 text-[#F7B32B]"></i>
                Security Settings
              </span>
            </a>
          </div>
        </div>

        <!-- Help & Support -->
        <a href="#" class="block">
          <div class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all hover:bg-blue-600/50 text-white group">
            <div class="p-1.5 rounded-lg bg-blue-800/30 group-hover:bg-blue-700/50 transition-colors">
              <i data-lucide="help-circle" class="w-5 h-5 text-[#F7B32B] group-hover:text-white"></i>
            </div>
            <span class="ml-3 sidebar-text">Help & Support</span>
          </div>
        </a>
      </nav>
    </div>
  </div>
  <div class="sidebar-overlay" onclick="toggleSidebar()"></div>


<style>
  /* Mobile styles */
  @media (max-width: 767px) {
    #sidebar {
      z-index: 40;
      width: 16rem; /* w-64 equivalent */
      left: 0;
      top: 0;
      bottom: 0;
      transition: transform 0.3s ease;
    }
    
    #sidebar.translate-x-0 {
      transform: translateX(0);
    }
    
    #sidebar.-translate-x-full {
      transform: translateX(-100%);
    }
    
    /* Optional overlay */
    .sidebar-overlay {
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      background: rgba(0,0,0,0.5);
      z-index: 30;
      display: none;
    }
    
    #sidebar.translate-x-0 + .sidebar-overlay {
      display: block;
    }
  }

  /* Desktop styles */
  .w-20 .sidebar-text {
    display: none;
  }
  
  .w-20 .flex.items-center {
    justify-content: center;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
  }
  
  .w-20 .collapse-title {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    justify-content: center;
  }
  
  .w-20 .collapse-content {
    display: none;
  }
  
  .w-20 .text-xs.uppercase {
    display: none;
  }
  
  .w-20 .p-1.5.rounded-lg {
    margin-right: 0;
  }
  
  .w-20 #sonly {
    width: 3rem;
    height: 3rem;
    margin: 0 auto;
  }
  
  /* Hide dropdown icons when collapsed */
  .w-20 .dropdown-icon {
    display: none;
  }
  
  #sidebar-logo {
  opacity: 0;
  transition: opacity 0.3s ease;
}

#sidebar.loaded #sidebar-logo {
  opacity: 1;
}

</style> 