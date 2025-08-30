<!DOCTYPE html>
<html lang="en" data-theme = "light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Soliera Hotel</title>
</head>

   <style>
          *{
            scroll-behavior: smooth
          }
       
        .text-outline {
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }
        
        /* Initial transparent nav */
        .navbar {
            transition: all 0.3s ease;
        }
        
        /* Scrolled state */
        .navbar.scrolled {
            background-color: #001f54 !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar.scrolled .btn-ghost {
            color: white !important;
        }
        
        .navbar.scrolled .menu-horizontal a {
            color: white !important;
        }
    </style>

<body>
    <!-- Navigation -->
    @include('landing.nav')
    <!-- Hero Section -->
    @include('landing.hero')

    <!-- Rooms Section -->
    @include('landing.room')

<!-- Restaurant Section -->
    @include('landing.restaurant')


    <!-- Amenities Section -->
    @include('landing.ameneties')


    <!-- Testimonials -->
    @include('landing.testimonials')

 
    {{-- Contacts --}}
    @include('landing.contacts')

    <!-- Footer -->
    @include('landing.footer')
</body>

    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNav');
            const heroHeight = document.querySelector('.hero').offsetHeight;
            
            if (window.scrollY > heroHeight * 0.8) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script>
    AOS.init({
        duration: 1000,
        once: true
    });
</script>
</html>
