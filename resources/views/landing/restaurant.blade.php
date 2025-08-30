<section id="restaurant" class="py-20 bg-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black/5 z-0"></div>
    <div class="max-w-7xl mx-auto px-4 relative z-10">
        <!-- Restaurant Intro -->
        <div class="text-center mb-16" data-aos="fade-up" data-aos-delay="100">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">
                <span class="text-[#F7B32B]">Gourmet</span> Dining Experience
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Indulge in culinary excellence at Soliera's award-winning restaurants
            </p>
        </div>

        <!-- Restaurant Image + Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Restaurant Image -->
            <div class="relative w-full h-[600px] rounded-xl overflow-hidden shadow-2xl" data-aos="fade-right" data-aos-delay="200">
                <div class="absolute inset-0 bg-cover bg-center"
                     style="background-image: url('{{asset('images/defaults/rooms/resto/resto2.png')}}')">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-8 text-white">
                    <h3 class="text-3xl font-bold">Soliera Restaurant</h3>
                    <p class="text-amber-300">Signature Fine Dining</p>
                </div>
            </div>

            <!-- Restaurant Details -->
            <div data-aos="fade-left" data-aos-delay="300">
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Culinary Excellence</h3>
                    <p class="text-gray-600 mb-6">
                        Our Michelin-starred chefs create unforgettable dining experiences using locally-sourced ingredients and innovative techniques.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-amber-600 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-gray-700">Breakfast buffet 6:30 AM - 10:30 AM</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-amber-600 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-gray-700">Lunch service 12:00 PM - 3:00 PM</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-amber-600 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-gray-700">Dinner service 6:00 PM - 11:00 PM</span>
                        </li>
                    </ul>
                </div>
                <a href="#contact" class="btn btn-outline border-[#F7B32B] text-black hover:bg-[#F7B32B] hover:text-white px-8 py-3">
                    Make Reservation
                </a>
            </div>
        </div>

        <!-- Sample Foods & Menus -->
        <div class="mt-20" data-aos="fade-up" data-aos-delay="400">
            <h3 class="text-3xl font-bold text-center mb-10">Menu <span class="text-[#F7B32B]">Highlights</span></h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="card bg-base-100 shadow-md" data-aos="zoom-in" data-aos-delay="100">
        <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?auto=format&fit=crop&w=400&q=80" alt="Signature Steak" class="w-full h-40 object-cover rounded-t-lg">
        <div class="card-body items-center text-center">
          <i class="fas fa-utensils text-4xl" style="color: #F7B32B;"></i>
          <h3 class="card-title">Signature Steak</h3>
          <p>Grilled premium steak cooked to perfection.</p>
          <p class="font-semibold text-amber-600 mt-2">₱1,200</p>
        </div>
      </div>

      <!-- Food Card 2 -->
      <div class="card bg-base-100 shadow-md" data-aos="zoom-in" data-aos-delay="150">
        <img src="https://images.unsplash.com/photo-1553621042-f6e147245754?auto=format&fit=crop&w=400&q=80" alt="Seafood Platter" class="w-full h-40 object-cover rounded-t-lg">
        <div class="card-body items-center text-center">
          <i class="fas fa-utensils text-4xl" style="color: #F7B32B;"></i>
          <h3 class="card-title">Seafood Platter</h3>
          <p>Fresh assortment of seafood with local flavors.</p>
          <p class="font-semibold text-amber-600 mt-2">₱1,500</p>
        </div>
      </div>

      <!-- Food Card 3 -->
      <div class="card bg-base-100 shadow-md" data-aos="zoom-in" data-aos-delay="200">
        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80" alt="Vegetarian Delight" class="w-full h-40 object-cover rounded-t-lg">
        <div class="card-body items-center text-center">
          <i class="fas fa-utensils text-4xl" style="color: #F7B32B;"></i>
          <h3 class="card-title">Vegetarian Delight</h3>
          <p>A mix of fresh vegetables and herbs in a savory sauce.</p>
          <p class="font-semibold text-amber-600 mt-2">₱850</p>
        </div>
      </div>

      <!-- Food Card 4 -->
      <div class="card bg-base-100 shadow-md" data-aos="zoom-in" data-aos-delay="250">
        <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=400&q=80" alt="Classic Burger" class="w-full h-40 object-cover rounded-t-lg">
        <div class="card-body items-center text-center">
          <i class="fas fa-utensils text-4xl" style="color: #F7B32B;"></i>
          <h3 class="card-title">Classic Burger</h3>
          <p>Juicy beef burger with fresh lettuce and special sauce.</p>
          <p class="font-semibold text-amber-600 mt-2">₱650</p>
        </div>
      </div>

      <!-- Food Card 5 -->
      <div class="card bg-base-100 shadow-md" data-aos="zoom-in" data-aos-delay="300">
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80" alt="Pasta Primavera" class="w-full h-40 object-cover rounded-t-lg">
        <div class="card-body items-center text-center">
          <i class="fas fa-utensils text-4xl" style="color: #F7B32B;"></i>
          <h3 class="card-title">Pasta Primavera</h3>
          <p>Fresh pasta with seasonal vegetables and herbs.</p>
          <p class="font-semibold text-amber-600 mt-2">₱900</p>
        </div>
      </div>

      <!-- Food Card 6 -->
      <div class="card bg-base-100 shadow-md" data-aos="zoom-in" data-aos-delay="350">
        <img src="https://images.unsplash.com/photo-1525755662778-989d0524087e?auto=format&fit=crop&w=400&q=80" alt="Dessert Platter" class="w-full h-40 object-cover rounded-t-lg">
        <div class="card-body items-center text-center">
          <i class="fas fa-utensils text-4xl" style="color: #F7B32B;"></i>
          <h3 class="card-title">Dessert Platter</h3>
          <p>Assortment of cakes and pastries to end your meal sweetly.</p>
          <p class="font-semibold text-amber-600 mt-2">₱700</p>
        </div>
      </div>
            </div>
        </div>
    </div>
</section>
