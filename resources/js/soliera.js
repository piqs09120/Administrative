   // Initialize Lucide icons
    lucide.createIcons();
    
    // Toggle sidebar function with smooth transition
    // COMMENTED OUT - Conflicts with Alpine.js hamburger menu
    // function toggleSidebar() {
    //   const sidebar = document.getElementById('sidebar');
    //   sidebar.classList.toggle('w-64');
    //   sidebar.classList.toggle('w-0');
    //   sidebar.classList.toggle('opacity-0');
    //   sidebar.classList.toggle('overflow-hidden');
    // }

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