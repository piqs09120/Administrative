<script>
  // Initialize lucide icons
  lucide.createIcons();
  
  // Check if mobile view
  function isMobileView() {
    return window.innerWidth < 768; // Tailwind's md breakpoint
  }

  // Toggle sidebar function
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarLogo = document.getElementById('sidebar-logo');
    const sonlyLogo = document.getElementById('sonly');
    if (!sidebar) { return; }
    
    if (isMobileView()) {
      // Mobile behavior - toggle visibility
      if (sidebar.classList.contains('translate-x-0')) {
        // Closing sidebar on mobile
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
      } else {
        // Opening sidebar on mobile
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
      }
          } else {
        // Desktop behavior - toggle between expanded (w-64) and collapsed (w-20)
        const currentlyCollapsed = sidebar.classList.contains('w-20');
        const nextCollapsed = !currentlyCollapsed;
        sidebar.classList.remove('w-64', 'w-20');
        sidebar.classList.add(nextCollapsed ? 'w-20' : 'w-64');
        localStorage.setItem('sidebarCollapsed', nextCollapsed);
        
        // Update text visibility based on collapsed state
        document.querySelectorAll('.sidebar-text').forEach(text => {
          text.classList.toggle('hidden', nextCollapsed);
        });
        
        // Toggle logos based on collapsed state
        if (nextCollapsed) {
          sidebarLogo.classList.add('hidden');
          sonlyLogo.classList.remove('hidden');
        } else {
          sidebarLogo.classList.remove('hidden');
          sonlyLogo.classList.add('hidden');
        }
      }
    
    // Update dropdown indicators
    updateDropdownIndicators();
  }

  // Update dropdown indicators
  function updateDropdownIndicators() {
    const sidebar = document.getElementById('sidebar');
    const isCollapsed = sidebar.classList.contains('w-20') && !isMobileView();
    const dropdownIcons = document.querySelectorAll('.dropdown-icon');
    
    dropdownIcons.forEach(icon => {
      if (isCollapsed) {
        const isOpen = icon.closest('.collapse').querySelector('input[type="checkbox"]').checked;
        icon.setAttribute('data-lucide', isOpen ? 'minus' : 'plus');
      } else {
        const isOpen = icon.closest('.collapse').querySelector('input[type="checkbox"]').checked;
        icon.setAttribute('data-lucide', isOpen ? 'chevron-down' : 'chevron-right');
      }
    });
    
    // Recreate all icons after updating attributes
    lucide.createIcons();
  }

  // Handle window resize
  function handleResize() {
    const sidebar = document.getElementById('sidebar');
    const sidebarLogo = document.getElementById('sidebar-logo');
    const sonlyLogo = document.getElementById('sonly');
    if (!sidebar) { return; }
    
    if (isMobileView()) {
      // On mobile, ensure proper transform classes and show full logo
      if (!sidebar.classList.contains('translate-x-0')) {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
      }
      sidebarLogo.classList.remove('hidden');
      sonlyLogo.classList.add('hidden');
          } else {
        // On desktop, apply the saved collapsed state
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        sidebar.classList.remove('-translate-x-full', 'translate-x-0');
        sidebar.classList.toggle('w-64', !isCollapsed);
        sidebar.classList.toggle('w-20', isCollapsed);
        
        document.querySelectorAll('.sidebar-text').forEach(text => {
          text.classList.toggle('hidden', isCollapsed);
        });
        
        // Toggle logos based on collapsed state
        if (isCollapsed) {
          sidebarLogo.classList.add('hidden');
          sonlyLogo.classList.remove('hidden');
        } else {
          sidebarLogo.classList.remove('hidden');
          sonlyLogo.classList.add('hidden');
        }
      }
    
    updateDropdownIndicators();
  }

  // Initialize sidebar
  function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarLogo = document.getElementById('sidebar-logo');
    const sonlyLogo = document.getElementById('sonly');
    if (!sidebar) { return; }
    
    if (isMobileView()) {
      // Start hidden on mobile with full logo
      sidebar.classList.add('-translate-x-full');
      sidebarLogo.classList.remove('hidden');
      sonlyLogo.classList.add('hidden');
    } else {
      // Start with saved state on desktop
      const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
      sidebar.classList.add(isCollapsed ? 'w-20' : 'w-64');
      
      document.querySelectorAll('.sidebar-text').forEach(text => {
        text.classList.toggle('hidden', isCollapsed);
      });
      
      // Toggle logos based on collapsed state
      if (isCollapsed) {
        sidebarLogo.classList.add('hidden');
        sonlyLogo.classList.remove('hidden');
      } else {
        sidebarLogo.classList.remove('hidden');
        sonlyLogo.classList.add('hidden');
      }
    }
    
    setTimeout(() => {
      sidebar.classList.add('loaded');
    }, 50);
    
    // Set up event listeners
    document.querySelectorAll('.collapse input[type="checkbox"]').forEach(checkbox => {
      checkbox.addEventListener('change', updateDropdownIndicators);
    });
    
    window.addEventListener('resize', handleResize);
    updateDropdownIndicators();
  }

 function displayPhilippineTime() {
  // Create a date object for Philippine time (UTC+8)
  const options = {
    timeZone: 'Asia/Manila',
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: true
  };

  // Get the formatted date and time string
  const philippineDateTime = new Date().toLocaleString('en-PH', options);
  
  // Update the element with the current time
  const timeElement = document.getElementById('philippineTime');
  if (timeElement) {
    timeElement.textContent = philippineDateTime;
  }
}

// Initial call to display the time
displayPhilippineTime();

// Update the time every second
setInterval(displayPhilippineTime, 1000);

// Add event listener to ensure the function runs after DOM is loaded
 // Initialize when DOM loads
 document.addEventListener('DOMContentLoaded', initSidebar);

 // Global Notification Function with Progress Bar Animation
 // This function is available across all pages that include soliera_js
 if (typeof window.showNotification === 'undefined') {
   window.showNotification = function(message, type = 'info', duration = 3000) {
     // Remove any existing notification progress style if it exists
     if (!document.getElementById('notification-progress-style')) {
       const style = document.createElement('style');
       style.id = 'notification-progress-style';
       style.textContent = `
         @keyframes progressBar {
           from {
             width: 100%;
           }
           to {
             width: 0%;
           }
         }
       `;
       document.head.appendChild(style);
     }

     // Create notification element
     const notification = document.createElement('div');
     const alertType = type === 'error' ? 'error' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info';
     notification.className = `alert alert-${alertType} fixed bottom-4 right-4 z-[9999] max-w-sm shadow-lg relative overflow-hidden`;
     notification.style.cssText = 'position: fixed; bottom: 1rem; right: 1rem; z-index: 9999; max-width: 24rem; animation: slideInRight 0.3s ease-out;';
     
     // Set icon based on type
     const iconMap = {
       'success': 'check-circle',
       'error': 'alert-circle',
       'warning': 'alert-triangle',
       'info': 'info'
     };
     const icon = iconMap[type] || 'info';
     
     notification.innerHTML = `
       <div class="flex items-center gap-2 px-4 py-3">
         <i data-lucide="${icon}" class="w-5 h-5"></i>
         <span>${message}</span>
       </div>
       <div class="absolute bottom-0 left-0 right-0 h-1 bg-black/20">
         <div class="notification-progress h-full bg-white/50" style="width: 100%; animation: progressBar ${duration}ms linear forwards;"></div>
       </div>
     `;
     
     // Add slide-in animation if not exists
     if (!document.getElementById('notification-slide-style')) {
       const slideStyle = document.createElement('style');
       slideStyle.id = 'notification-slide-style';
       slideStyle.textContent = `
         @keyframes slideInRight {
           from {
             transform: translateX(100%);
             opacity: 0;
           }
           to {
             transform: translateX(0);
             opacity: 1;
           }
         }
       `;
       document.head.appendChild(slideStyle);
     }
     
     document.body.appendChild(notification);
     
     // Force reflow to ensure animation starts
     notification.offsetHeight;
     
     // Initialize Lucide icons
     if (window.lucide && window.lucide.createIcons) {
       window.lucide.createIcons();
     }
     
     // Auto remove after duration
     setTimeout(() => {
       notification.style.opacity = '0';
       notification.style.transition = 'opacity 0.3s ease-out';
       setTimeout(() => {
         if (notification.parentNode) {
           notification.remove();
         }
       }, 300);
     }, duration);
   };
 }
</script>

{{-- Legal Consent Modal Component (hidden across Legal Management pages) --}}
@if (!request()->is('legal*'))
  <x-legal-consent :open="false" />
@endif