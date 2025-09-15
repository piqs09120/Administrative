<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify OTP - Soliera Hotel</title>
    
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
<div class="relative z-10 w-full h-full flex justify-center items-center p-4">
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
      <div class="max-w-md w-full bg-white/10 backdrop-blur-lg p-6 rounded-xl shadow-2xl border border-white/20">
    <!-- Card Header -->
    <div class="mb-6 text-center flex justify-center items-center flex-col">
      <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mb-4">
        <i class="bx bx-shield-check text-3xl text-blue-400"></i>
      </div>
      <h2 class="text-2xl font-bold text-white">Verify Your Identity</h2>
      <p class="text-white/80 mt-1">Enter the OTP code sent to your email</p>
      
      <!-- Success Message -->
      @if(session('success'))
        <div class="mt-4 p-3 bg-green-500/20 border border-green-500/30 rounded-lg">
          <p class="text-green-400 text-sm">{{ session('success') }}</p>
        </div>
      @endif
    </div>
    
    <!-- Card Body -->
    <div>
      <form action="{{ route('otp.verify.submit') }}" method="POST" id="otpForm">
        @csrf
        <input type="hidden" name="employee_id" value="{{ session('otp_employee_id') }}">
        <!-- Debug info -->
        <input type="hidden" name="debug_info" value="form_submitted">
        
        <!-- OTP Input -->
        <div class="mb-6">
          <label class="block text-white/90 text-sm font-medium mb-2" for="otp_code">
            Enter OTP Code
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="bx bx-key text-white/50 text-xl"></i>
            </div>
            <input 
              id="otp_code" 
              type="text" 
              class="w-full pl-12 pr-3 py-3 bg-white/5 border border-white/20 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent placeholder-white/50 text-center text-2xl tracking-widest @error('otp_code') border-red-500 @enderror" 
              placeholder="------"
              required
              name="otp_code"
              maxlength="6"
              pattern="[0-9]{6}"
              autocomplete="off"
              value="{{ old('otp_code') }}"
            >
          </div>
          <p class="text-white/60 text-xs mt-2 text-center">
            Check your email for the 6-digit code
          </p>
          @error('otp_code')
            <p class="text-red-400 text-xs mt-1 text-center">{{ $message }}</p>
          @enderror
        </div>
        
        <!-- Timer -->
        <div class="mb-6 text-center">
          <div class="text-white/80 text-sm">
            Code expires in: <span id="timer" class="text-blue-400 font-bold">10:00</span>
          </div>
        </div>
        
        <!-- Verify Button -->
        <button 
          type="submit" 
          class="w-full btn-primary btn mb-4 disabled:opacity-50 disabled:cursor-not-allowed"
          id="verifyBtn"
          disabled
          onclick="console.log('Button clicked! OTP value:', document.getElementById('otp_code').value)"
        >
          <i class="bx bx-check-circle mr-2"></i>
          Verify OTP
        </button>
        
        <!-- Resend OTP -->
        <div class="text-center">
          <button 
            type="button" 
            class="text-blue-400 hover:text-blue-300 text-sm font-medium"
            onclick="resendOTP()"
            id="resendBtn"
            disabled
          >
            Resend OTP (<span id="resendTimer">60</span>s)
          </button>
        </div>
        
        
      </form>
      
      <!-- Back to Login -->
      <div class="mt-6 text-center">
        <a href="/employeelogin" class="text-white/60 hover:text-white text-sm">
          <i class="bx bx-arrow-back mr-1"></i>
          Back to Login
        </a>
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
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing OTP verification...');
    
    // Auto-focus on OTP input
    const otpInput = document.getElementById('otp_code');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const timerElement = document.getElementById('timer');
    const resendTimerElement = document.getElementById('resendTimer');
    const otpForm = document.getElementById('otpForm');
    
    if (otpInput) otpInput.focus();
    
    // Debug: Log session data
    console.log('OTP Session Data:', {
        employee_id: '{{ session("otp_employee_id") }}',
        user_data: @json(session('otp_user_data'))
    });
    
    // Debug: Log form action URL
    console.log('Form action URL:', otpForm ? otpForm.action : 'Form not found');
    console.log('Form method:', otpForm ? otpForm.method : 'Form not found');
    
    // Timer for OTP expiry (10 minutes)
    let timeLeft = 600; // 10 minutes in seconds
    const timer = setInterval(() => {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        if (timerElement) {
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
        
        if (timeLeft <= 0) {
            clearInterval(timer);
            if (timerElement) {
                timerElement.textContent = 'Expired';
                timerElement.className = 'text-red-400 font-bold';
            }
            if (otpInput) otpInput.disabled = true;
            if (verifyBtn) verifyBtn.disabled = true;
        }
        timeLeft--;
    }, 1000);
    
    // Resend timer (60 seconds)
    let resendTimeLeft = 60;
    const resendTimer = setInterval(() => {
        if (resendTimerElement) {
            resendTimerElement.textContent = resendTimeLeft;
        }
        
        if (resendTimeLeft <= 0) {
            clearInterval(resendTimer);
            if (resendBtn) {
                resendBtn.disabled = false;
                resendBtn.innerHTML = 'Resend OTP';
            }
        }
        resendTimeLeft--;
    }, 1000);
    
    // Auto-format OTP input (numbers only)
    if (otpInput) {
        otpInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Enable/disable button based on input length
            if (verifyBtn) {
                if (this.value.length === 6) {
                    verifyBtn.disabled = false;
                    verifyBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    verifyBtn.disabled = true;
                    verifyBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }
        });
        
        // Auto-submit when 6 digits are entered - DISABLED
        // User must manually click the Verify OTP button
        otpInput.addEventListener('input', function(e) {
            if (this.value.length === 6) {
                console.log('6 digits entered, button enabled - ready for manual submit');
                // No auto-submit - user must click the button
            }
        });
        
        // Handle Enter key press - DISABLED
        // User must manually click the Verify OTP button
        otpInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.value.length === 6) {
                e.preventDefault();
                console.log('Enter key pressed, but auto-submit is disabled');
                // No auto-submit - user must click the button
            }
        });
    }
    
    // Add click event listener to verify button
    if (verifyBtn) {
        verifyBtn.addEventListener('click', function(e) {
            console.log('Verify button clicked!');
            console.log('Button disabled:', this.disabled);
            console.log('OTP value:', otpInput ? otpInput.value : 'N/A');
            
            // Prevent default form submission
            e.preventDefault();
            
            // Use force submit logic
            const otpCode = otpInput ? otpInput.value : '';
            
            if (!otpCode) {
                showNotification('Please enter an OTP code first', 'error');
                return;
            }
            
            console.log('Force submitting form with OTP:', otpCode);
            
            // Create a new form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("otp.verify.submit") }}';
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Add employee_id
            const empIdInput = document.createElement('input');
            empIdInput.type = 'hidden';
            empIdInput.name = 'employee_id';
            empIdInput.value = '{{ session("otp_employee_id") }}';
            form.appendChild(empIdInput);
            
            // Add OTP code
            const otpCodeInput = document.createElement('input');
            otpCodeInput.type = 'hidden';
            otpCodeInput.name = 'otp_code';
            otpCodeInput.value = otpCode;
            form.appendChild(otpCodeInput);
            
            // Submit the form
            document.body.appendChild(form);
            form.submit();
        });
    }
    
    // Form submission is now handled by the verify button click event
    
    // Resend OTP function
    window.resendOTP = function() {
        console.log('Resend OTP clicked');
        if (resendBtn && resendBtn.disabled) return;
        
        if (resendBtn) {
            resendBtn.disabled = true;
            resendBtn.innerHTML = 'Sending...';
        }
        
        fetch('/resend-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                employee_id: '{{ session("otp_employee_id") }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Resend response:', data);
            if (data.success) {
                // Reset timers
                timeLeft = 600;
                resendTimeLeft = 60;
                clearInterval(timer);
                clearInterval(resendTimer);
                
                // Restart timers
                const newTimer = setInterval(() => {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    if (timerElement) {
                        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                    }
                    
                    if (timeLeft <= 0) {
                        clearInterval(newTimer);
                        if (timerElement) {
                            timerElement.textContent = 'Expired';
                            timerElement.className = 'text-red-400 font-bold';
                        }
                        if (otpInput) otpInput.disabled = true;
                        if (verifyBtn) verifyBtn.disabled = true;
                    }
                    timeLeft--;
                }, 1000);
                
                const newResendTimer = setInterval(() => {
                    if (resendTimerElement) {
                        resendTimerElement.textContent = resendTimeLeft;
                    }
                    
                    if (resendTimeLeft <= 0) {
                        clearInterval(newResendTimer);
                        if (resendBtn) {
                            resendBtn.disabled = false;
                            resendBtn.innerHTML = 'Resend OTP';
                        }
                    }
                    resendTimeLeft--;
                }, 1000);
                
                // Show success message
                showNotification('New OTP sent to your email!', 'success');
            } else {
                showNotification(data.message || 'Failed to resend OTP', 'error');
                if (resendBtn) {
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = 'Resend OTP';
                }
            }
        })
        .catch(error => {
            console.error('Resend Error:', error);
            showNotification('Failed to resend OTP', 'error');
            if (resendBtn) {
                resendBtn.disabled = false;
                resendBtn.innerHTML = 'Resend OTP';
            }
        });
    };
    
    
    
    // Show notification function
    window.showNotification = function(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    };
    
    console.log('OTP verification initialized successfully!');
});
</script>

</body>
</html>
