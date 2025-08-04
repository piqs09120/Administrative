   // Initialize Lucide icons
    lucide.createIcons();
    
    // Toggle sidebar function with smooth transition
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const sidebarTexts = document.querySelectorAll('.sidebar-text');
  const sidebarToggle = document.querySelector('.sidebar-toggle i');
  const isCollapsed = sidebar.classList.contains('collapsed');
  
  if (isCollapsed) {
    // Expand sidebar
    sidebar.classList.remove('collapsed');
    sidebar.classList.add('w-64');
    sidebar.classList.remove('w-16');
    sidebarTexts.forEach(text => text.style.opacity = '1');
    sidebarToggle.setAttribute('data-lucide', 'chevron-left');
  } else {
    // Collapse sidebar
    sidebar.classList.add('collapsed');
    sidebar.classList.remove('w-64');
    sidebar.classList.add('w-16');
    sidebarTexts.forEach(text => text.style.opacity = '0');
    sidebarToggle.setAttribute('data-lucide', 'chevron-right');
  }
  
  // Recreate icons
  if (window.lucide && window.lucide.createIcons) {
    window.lucide.createIcons();
  }
  
  // Store state in localStorage
  localStorage.setItem('sidebarCollapsed', !isCollapsed);
}

// Dark mode toggle function
function toggleDarkMode() {
  console.log('Dark mode toggle clicked!');
  const html = document.documentElement;
  const body = document.body;
  const sunIcon = document.getElementById('sunIcon');
  const moonIcon = document.getElementById('moonIcon');
  
  console.log('Current dark mode state:', html.classList.contains('dark'));
  console.log('Sun icon found:', !!sunIcon);
  console.log('Moon icon found:', !!moonIcon);
  
  if (html.classList.contains('dark')) {
    // Switch to light mode
    html.classList.remove('dark');
    body.classList.remove('dark');
    localStorage.setItem('darkMode', 'false');
    console.log('Switched to LIGHT mode');
    
    // Update icons
    if (sunIcon) sunIcon.classList.add('hidden');
    if (moonIcon) moonIcon.classList.remove('hidden');
  } else {
    // Switch to dark mode
    html.classList.add('dark');
    body.classList.add('dark');
    localStorage.setItem('darkMode', 'true');
    console.log('Switched to DARK mode');
    
    // Update icons
    if (sunIcon) sunIcon.classList.remove('hidden');
    if (moonIcon) moonIcon.classList.add('hidden');
  }
  
  console.log('New dark mode state:', html.classList.contains('dark'));
}

    // Initialize sidebar state on page load
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM Content Loaded - Initializing dark mode...');
  
  const sidebar = document.getElementById('sidebar');
  const sidebarTexts = document.querySelectorAll('.sidebar-text');
  const sidebarToggle = document.querySelector('.sidebar-toggle i');
  const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
  
  if (isCollapsed) {
    sidebar.classList.add('collapsed');
    sidebar.classList.remove('w-64');
    sidebar.classList.add('w-16');
    sidebarTexts.forEach(text => text.style.opacity = '0');
    sidebarToggle.setAttribute('data-lucide', 'chevron-right');
  } else {
    sidebar.classList.remove('collapsed');
    sidebar.classList.add('w-64');
    sidebar.classList.remove('w-16');
    sidebarTexts.forEach(text => text.style.opacity = '1');
    sidebarToggle.setAttribute('data-lucide', 'chevron-left');
  }
  
  // Initialize dark mode
  const darkModeToggle = document.getElementById('darkModeToggle');
  const sunIcon = document.getElementById('sunIcon');
  const moonIcon = document.getElementById('moonIcon');
  
  console.log('Dark mode toggle button found:', !!darkModeToggle);
  console.log('Sun icon found:', !!sunIcon);
  console.log('Moon icon found:', !!moonIcon);
  
  if (darkModeToggle) {
    // Set initial state based on localStorage
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    console.log('Initial dark mode from localStorage:', isDarkMode);
    
    if (isDarkMode) {
      document.documentElement.classList.add('dark');
      document.body.classList.add('dark');
      if (sunIcon) sunIcon.classList.remove('hidden');
      if (moonIcon) moonIcon.classList.add('hidden');
      console.log('Set initial state to DARK mode');
    } else {
      document.documentElement.classList.remove('dark');
      document.body.classList.remove('dark');
      if (sunIcon) sunIcon.classList.add('hidden');
      if (moonIcon) moonIcon.classList.remove('hidden');
      console.log('Set initial state to LIGHT mode');
    }
    
    // Add event listener
    darkModeToggle.addEventListener('click', function(e) {
      e.preventDefault();
      console.log('Dark mode button clicked!');
      toggleDarkMode();
    });
    
    console.log('Event listener attached to dark mode toggle');
  } else {
    console.error('Dark mode toggle button not found!');
  }
  
  // Recreate icons
  if (window.lucide && window.lucide.createIcons) {
    window.lucide.createIcons();
  }
});

    // Add ripple effect to buttons
    document.querySelectorAll('.btn').forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        let x = e.clientX - e.target.getBoundingClientRect().left;
        let y = e.clientY - e.target.getBoundingClientRect().top;
        
        let ripple = document.createElement('span');
        ripple.classList.add('ripple');
        ripple.style.left = `${x}px`;
        ripple.style.top = `${y}px`;
        this.appendChild(ripple);
        
        setTimeout(() => {
          ripple.remove();
        }, 1000);
      });
    });