<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Soliera Hotel - Department Login</title>
    
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite('resources/css/app.css')
</head>
<body>
   <section class="relative w-full h-screen">

  <!-- Background image with overlay -->
  <div class="absolute inset-0 bg-cover bg-center z-0" style="background-image: url('{{ asset('images/defaults/hotel3.jpg') }}');"></div>
    <div class="absolute inset-0 bg-black/40 z-10"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/70 z-10"></div>
  
  <!-- Content container -->
<div class="relative z-10 w-full h-full flex justify-center items-center  p-4">
  <div class="w-1/2 flex justify-center items-center max-md:hidden">
  <div class="max-w-lg p-8">
    <!-- Hotel & Restaurant Illustration -->
    <div class="text-center mb-8">
      <a href="/">
      <img data-aos = "zoom-in" data-aos-delay = "100"  class="w-full max-h-52 hover:scale-105 transition-all" src="{{asset('images/logo/logofinal.png')}}" alt="">
      </a>
      <h1 data-aos = "zoom-in-up" data-aos-delay="200" class="text-3xl font-bold text-white mb-2">Welcome to <span class="text-[#F7B32B]">Soliera<span> Hotel & Restaurant</h1>
      <p data-aos = "zoom-in-up" data-aos-delay="300" class="text-white/80">  Savor The Stay, Dine With Elegance</p>
    </div>


    

  </div>
</div>
  
  <div class="w-1/2 flex justify-center items-center max-md:w-full">
      <div   class="max-w-md w-full bg-white/10 backdrop-blur-lg p-6 rounded-xl shadow-2xl border border-white/20">
    <!-- Card Header -->
    <div class="mb-6 text-center flex justify-center items-center flex-col">
       
      <h2 class="text-2xl font-bold text-white">Sign in to your account</h2>
      <p class="text-white/80 mt-1">Enter your credentials to continue</p>
    </div>
    
    <!-- Card Body -->
    <div>
      <form action="/loginuser" method="POST">
        <!-- Email Input -->
        @csrf
        <div class="mb-4">
          <label class="block text-white/90 text-sm font-medium mb-2" for="email">
            Employee ID
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-white/50" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
              </svg>
            </div>
            <input 
              id="email" 
              type="text" 
              class="w-full pl-10 pr-3 py-3 bg-white/5 border border-white/20 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent placeholder-white/50" 
              placeholder="Your ID"
              required
              name="employee_id"
            >
          </div>
        </div>
        
        <!-- Password Input with Toggle -->
        <div class="mb-6">
          <label class="block text-white/90 text-sm font-medium mb-2" for="password">
            Password
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </div>
            <input 
              id="password" 
              type="password" 
              class="w-full pl-10 pr-10 py-3 bg-white/5 border border-white/20 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent placeholder-white/50" 
              placeholder="••••••••"
              required
              name="password"
            >
            <button 
              type="button" 
              class="absolute inset-y-0 right-0 flex items-center pr-3 text-white/50 hover:text-white focus:outline-none"
              onclick="togglePasswordVisibility()"
            >
              <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
              </svg>
              <svg id="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center">
            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-white/30 rounded bg-white/10">
            <label for="remember-me" class="ml-2 block text-sm text-white/80">
              Remember me
            </label>
          </div>
          <div class="text-sm">
            <a href="#" class="font-medium text-blue-400 hover:text-blue-300">
              Forgot password?
            </a>
          </div>
        </div>
        
        <!-- Sign In Button -->
        <button 
          type="submit" 
          class="w-full btn-primary btn"
        >
          Sign in
        </button>
      </form>
      
      <!-- Social Login or Register Link -->
      <div class="mt-6">
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-white/20"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-transparent text-white/70">
              Don't have an account?
            </span>
          </div>
        </div>
        
        <div class="mt-4 text-center">
          <a href="/terms" class="font-medium text-blue-400 hover:text-blue-300">
            Create new account
          </a>
        </div>
      </div>
    </div>
  </div>
  </div>
  
 

</div>


   </section>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script>
    AOS.init({
        duration: 1000,
        once: true
    });
</script>

<script>
function togglePasswordVisibility() {
  const passwordInput = document.getElementById('password');
  const eyeIcon = document.getElementById('eye-icon');
  const eyeSlashIcon = document.getElementById('eye-slash-icon');

  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    eyeIcon.classList.add('hidden');
    eyeSlashIcon.classList.remove('hidden');
  } else {
    passwordInput.type = 'password';
    eyeIcon.classList.remove('hidden');
    eyeSlashIcon.classList.add('hidden');
  }
}


</script>


</body>
</html>
