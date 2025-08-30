   <section id="about" class="hero min-h-screen flex items-center justify-center text-white relative overflow-hidden">
    <!-- Parallax Background Layers -->
    <div class="absolute inset-0 bg-black/40 z-10"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/70 z-10"></div>
 <div class="parallax-bg absolute inset-0 bg-cover bg-center" 
     style="background-image: url('{{ asset('images/defaults/hotel3.jpg') }}');">
</div>
    
    <!-- Hero Content -->
    <div class="text-center px-4 z-20 relative max-w-6xl mx-auto">
        <!-- Typewriter Effect for First Line -->
       
       
        
        
        <!-- Emphasized Hotel Name -->

        <div class="relative inline-block">
    <!-- 5 Star Icons -->
    <div class="flex justify-center mb-5 gap-5 animate-fade-in opacity-0" style="animation-delay: 2s;">
        <!-- Using Heroicons (SVG) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 .587l3.668 7.571 8.332 1.151-6.064 5.879 1.48 8.295L12 18.896l-7.416 4.587 1.48-8.295L.0 9.309l8.332-1.151z"/>
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 .587l3.668 7.571 8.332 1.151-6.064 5.879 1.48 8.295L12 18.896l-7.416 4.587 1.48-8.295L.0 9.309l8.332-1.151z"/>
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 .587l3.668 7.571 8.332 1.151-6.064 5.879 1.48 8.295L12 18.896l-7.416 4.587 1.48-8.295L.0 9.309l8.332-1.151z"/>
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 .587l3.668 7.571 8.332 1.151-6.064 5.879 1.48 8.295L12 18.896l-7.416 4.587 1.48-8.295L.0 9.309l8.332-1.151z"/>
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 .587l3.668 7.571 8.332 1.151-6.064 5.879 1.48 8.295L12 18.896l-7.416 4.587 1.48-8.295L.0 9.309l8.332-1.151z"/>
        </svg>
    </div>
        <div class="relative inline-block">
            <h2 data-aos = "zoom-in" data-aos-delay = "100" class="text-3xl md:text-5xl font-bold animate-fade-in opacity-0" style="animation-delay: 2.4s;">
                <span class="text-[#F7B32B]">SOLIERA</span> 
                <span>HOTEL & RESTAURANT</span>
            </h2>
           <h3  data-aos = "zoom-in-up" data-aos-delay = "200" class="text-xl md:text-2xl font-semibold text-white tracking-wide italic drop-shadow-sm">
  Savor The Stay, Dine With Elegance
</h3>



<p data-aos = "zoom-in-up" data-aos-delay = "300" class="text-lg md:text-xl text-gray-200 max-w-2xl mx-auto mt-6 animate-fade-in opacity-0" style="animation-delay: 2.6s;">
    Welcome to Soliera — where luxury meets comfort. 
    Experience world-class hospitality, exquisite dining, and unforgettable stays in the heart of the city.
</p>
            <div class="absolute -bottom-2 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-amber-400 to-transparent animate-underline" style="animation-delay: 2.8s;"></div>
        </div>
        
        <!-- CTA Buttons -->
        <div class="mt-12 animate-fade-in opacity-0" style="animation-delay: 3.2s;">
            <a data-aos="slide-right" href="#rooms" class="btn btn-outline btn-lg text-white border-white hover:bg-white hover:text-black mr-4 transform hover:scale-105 transition-all duration-300">
                Explore Rooms
            </a>
            <a data-aos="slide-left" href="#booking" class="btn btn-outline btn-lg text-white border-white hover:bg-white hover:text-black transform hover:scale-105 transition-all duration-300">
                Book Now
            </a>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 animate-bounce opacity-0" style="animation-delay: 4s;">
        <div class="w-8 h-12 border-2 border-white rounded-full flex justify-center">
            <div class="w-1 h-3 bg-white mt-2 rounded-full animate-scroll-indicator"></div>
        </div>
    </div>

    <style>
        /* Typewriter Effect */
        .typewriter {
            overflow: hidden;
            white-space: nowrap;
            border-right: 3px solid white;
            animation: typing 1.5s steps(20, end) forwards, blink-caret 0.75s step-end 3;
        }
        
        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }
        
        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: white }
        }
        
        /* Fade-in Animation */
        .animate-fade-in {
            animation: fadeIn 1s ease-in forwards;
        }
        
        @keyframes fadeIn {
            to { opacity: 1; }
        }
        
        /* Underline Animation */
        .animate-underline {
            animation: underlineGrow 1s ease-out forwards;
            transform: scaleX(0);
            transform-origin: center;
        }
        
        @keyframes underlineGrow {
            to { transform: scaleX(1); }
        }
        
        /* Scroll Indicator */
        .animate-scroll-indicator {
            animation: scrollIndicator 2s infinite;
        }
        
        @keyframes scrollIndicator {
            0% { transform: translateY(0); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateY(12px); opacity: 0; }
        }
        
        /* Parallax Effect */
        .parallax-bg {
            will-change: transform;
            transition: transform 0.4s ease-out;
        }
    </style>

    <script>
        // Parallax Effect
        document.addEventListener('scroll', function() {
            const parallaxBg = document.querySelector('.parallax-bg');
            const scrollPosition = window.pageYOffset;
            parallaxBg.style.transform = `translateY(${scrollPosition * 0.3}px)`;
        });
        
        // Initialize animations when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // This ensures all elements with animate-fade-in class will animate
            const animatedElements = document.querySelectorAll('.animate-fade-in, .animate-underline');
            animatedElements.forEach(el => {
                // Already handled by CSS animations with delays
            });
        });
    </script>
</section>
