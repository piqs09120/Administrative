<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Soliera Hotel - OTP Verification</title>
    
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
<div class="relative z-10 w-full min-h-screen flex justify-center items-center p-4 sm:p-6">
  <!-- OTP Card -->
  <div class="w-full max-w-md bg-white/10 backdrop-blur-lg p-6 sm:p-8 rounded-xl shadow-2xl border border-white/20">
    <!-- Card Header -->
    <div class="mb-6 sm:mb-8 text-center flex justify-center items-center flex-col">
      <div class="bg-white/10 p-3 rounded-full mb-3 sm:mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 sm:h-8 sm:w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
      </div>
      <h2 class="text-xl sm:text-2xl font-bold text-white">OTP Verification</h2>
      <p class="text-sm sm:text-base text-white/80 mt-1 sm:mt-2">Enter the 6-digit code sent to your device</p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
      <div class="mb-4 p-3 bg-red-500/20 border border-red-500/30 rounded-lg">
        @foreach($errors->all() as $error)
          <p class="text-red-300 text-sm">{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <!-- Success Messages -->
    @if(session('success'))
      <div class="mb-4 p-3 bg-green-500/20 border border-green-500/30 rounded-lg">
        <p class="text-green-300 text-sm">{{ session('success') }}</p>
      </div>
    @endif
    
    <!-- OTP Form -->
    <div>
      <form id="otpForm" action="{{ route('verify.otp') }}" method="POST">
        @csrf
        <!-- OTP Input Boxes - Adjusted for mobile -->
        <div class="flex justify-between mb-6 sm:mb-8 gap-2 sm:gap-3">
          <input type="text" name="otp1" maxlength="1" 
            class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center bg-white/5 border-2 border-white/20 text-white rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 otp-input" 
            oninput="moveToNext(this, 'otp2')" 
            autocomplete="off"
            required
            inputmode="numeric"
            pattern="[0-9]*"
          >
          <input type="text" name="otp2" maxlength="1" 
            class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center bg-white/5 border-2 border-white/20 text-white rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 otp-input" 
            oninput="moveToNext(this, 'otp3')" 
            autocomplete="off"
            required
            inputmode="numeric"
            pattern="[0-9]*"
          >
          <input type="text" name="otp3" maxlength="1" 
            class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center bg-white/5 border-2 border-white/20 text-white rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 otp-input" 
            oninput="moveToNext(this, 'otp4')" 
            autocomplete="off"
            required
            inputmode="numeric"
            pattern="[0-9]*"
          >
          <input type="text" name="otp4" maxlength="1" 
            class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center bg-white/5 border-2 border-white/20 text-white rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 otp-input" 
            oninput="moveToNext(this, 'otp5')" 
            autocomplete="off"
            required
            inputmode="numeric"
            pattern="[0-9]*"
          >
          <input type="text" name="otp5" maxlength="1" 
            class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center bg-white/5 border-2 border-white/20 text-white rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 otp-input" 
            oninput="moveToNext(this, 'otp6')" 
            autocomplete="off"
            required
            inputmode="numeric"
            pattern="[0-9]*"
          >
          <input type="text" name="otp6" maxlength="1" 
            class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center bg-white/5 border-2 border-white/20 text-white rounded-lg focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 otp-input" 
            oninput="moveToNext(this, '')" 
            autocomplete="off"
            required
            inputmode="numeric"
            pattern="[0-9]*"
          >
        </div>
        
        <!-- Timer and Resend -->
        <div class="flex items-center justify-center mb-6 sm:mb-8">
          <p id="countdown" class="text-sm sm:text-base text-white/80">Resend OTP in 02:00</p>
          <button id="resendBtn" type="button" class="ml-2 text-sm sm:text-base font-medium text-blue-400 hover:text-blue-300 hidden" onclick="resendOTP()">
            Resend
          </button>
        </div>
        
        <!-- Verify Button -->
        <button 
          type="submit" 
          class="w-full btn-primary btn"
        >
          Verify
        </button>
      </form>
      
      <!-- Back to Login -->
      <div class="mt-4 sm:mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm sm:text-base font-medium text-blue-400 hover:text-blue-300 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back to Login
        </a>
      </div>
    </div>
  </div>
</div>

</section>

<script>
// OTP Input Navigation
function moveToNext(current, nextFieldId) {
  if (current.value.length >= current.maxLength) {
    if (nextFieldId) {
      document.getElementsByName(nextFieldId)[0].focus();
    }
  }
  
  // Auto-submit if last field is filled
  if (current.name === 'otp6' && current.value.length === 1) {
    document.getElementById('otpForm').submit();
  }
}

// Handle paste event for OTP
document.addEventListener('DOMContentLoaded', function() {
  const otpInputs = document.querySelectorAll('.otp-input');
  
  // Handle paste event
  document.getElementById('otpForm').addEventListener('paste', function(e) {
    e.preventDefault();
    const pasteData = e.clipboardData.getData('text/plain').trim();
    if (/^\d{6}$/.test(pasteData)) {
      for (let i = 0; i < 6; i++) {
        otpInputs[i].value = pasteData[i];
      }
      otpInputs[5].focus();
    }
  });
  
  // Handle backspace/delete
  otpInputs.forEach((input, index) => {
    input.addEventListener('keydown', function(e) {
      if (e.key === 'Backspace' && !this.value && index > 0) {
        otpInputs[index - 1].focus();
      }
    });
  });
});

// Countdown Timer
let timeLeft = 120; // 2 minutes in seconds
const countdownEl = document.getElementById('countdown');
const resendBtn = document.getElementById('resendBtn');

function updateCountdown() {
  const minutes = Math.floor(timeLeft / 60);
  const seconds = timeLeft % 60;
  countdownEl.textContent = `Resend OTP in ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  
  if (timeLeft <= 0) {
    clearInterval(timer);
    countdownEl.classList.add('hidden');
    resendBtn.classList.remove('hidden');
  } else {
    timeLeft--;
  }
}

// Start the countdown
updateCountdown();
const timer = setInterval(updateCountdown, 1000);

// Resend OTP function
function resendOTP() {
  // Simulate resend OTP
  alert('New OTP has been sent to your device');
  
  // Reset countdown
  timeLeft = 120;
  countdownEl.textContent = `Resend OTP in 02:00`;
  countdownEl.classList.remove('hidden');
  resendBtn.classList.add('hidden');
  
  // Restart timer
  clearInterval(timer);
  updateCountdown();
  const newTimer = setInterval(updateCountdown, 1000);
  
  // Clear OTP fields
  document.querySelectorAll('.otp-input').forEach(input => {
    input.value = '';
  });
  document.getElementsByName('otp1')[0].focus();
}
</script>
</body>
</html>
