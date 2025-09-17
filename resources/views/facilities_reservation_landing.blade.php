<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Soliera Facilities Reservation System</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <style>
        * {
            scroll-behavior: smooth;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
        }
        
        .feature-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .navbar {
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            background-color: rgba(30, 58, 138, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #F7B32B, #FFD700);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
        }
        
        .form-section {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(248, 250, 252, 0.9));
            backdrop-filter: blur(10px);
        }
        
        .floating-label {
            transition: all 0.3s ease;
        }
        
        .form-input:focus + .floating-label,
        .form-input:not(:placeholder-shown) + .floating-label {
            transform: translateY(-1.5rem) scale(0.85);
            color: #3B82F6;
        }
        
        .notification-slide {
            animation: slideInRight 0.3s ease-out;
        }
        
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
        
        .form-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .facility-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .facility-card:hover {
            transform: translateY(-5px);
            border-color: #3b82f6;
            box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.1);
        }

        .calendar-day {
            transition: all 0.2s ease;
        }
        
        .calendar-day:hover {
            background-color: #dbeafe;
            transform: scale(1.05);
        }
        
        .calendar-day.selected {
            background-color: #3b82f6;
            color: white;
        }
        
        .calendar-day.available {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .calendar-day.unavailable {
            background-color: #fef2f2;
            color: #dc2626;
        }
        
        /* Style for disabled options in select */
        select option:disabled {
            color: #9ca3af !important;
            background-color: #f3f4f6 !important;
            font-style: italic;
        }
        
        select option:disabled:hover {
            background-color: #f3f4f6 !important;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav id="mainNav" class="navbar fixed top-0 w-full z-50 bg-transparent">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-white">
                        <span class="gradient-text">SOLIERA</span>
                    </h1>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#features" class="text-white hover:text-yellow-400 transition-colors">Features</a>
                    <a href="#reserve" class="text-white hover:text-yellow-400 transition-colors">Reserve Facility</a>
                    <a href="#facilities" class="text-white hover:text-yellow-400 transition-colors">Our Facilities</a>
                    <a href="#contact" class="text-white hover:text-yellow-400 transition-colors">Contact</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-white hover:text-yellow-400 transition-colors">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        
        <!-- Floating Elements -->
        <div class="absolute top-20 left-10 floating-animation">
            <i class="fas fa-building text-yellow-400 text-4xl opacity-20"></i>
        </div>
        <div class="absolute top-40 right-20 floating-animation" style="animation-delay: 2s;">
            <i class="fas fa-calendar-alt text-yellow-400 text-3xl opacity-20"></i>
        </div>
        <div class="absolute bottom-40 left-20 floating-animation" style="animation-delay: 4s;">
            <i class="fas fa-clock text-yellow-400 text-5xl opacity-20"></i>
        </div>
        
        <div class="text-center px-4 z-10 relative max-w-6xl mx-auto">
            <h1 data-aos="fade-up" class="text-5xl md:text-7xl font-bold text-white mb-6">
                Facilities
                <span class="block gradient-text">Reservation</span>
            </h1>
            
            <p data-aos="fade-up" data-aos-delay="200" class="text-xl md:text-2xl text-gray-200 max-w-3xl mx-auto mb-8">
                Book and manage facility reservations with ease. From conference rooms to event spaces, 
                reserve the perfect venue for your needs with our streamlined booking system.
            </p>
            
            <div data-aos="fade-up" data-aos-delay="400" class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#reserve" class="bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all transform hover:scale-105">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Reserve Now
                </a>
                <a href="#facilities" class="border-2 border-white text-white hover:bg-white hover:text-gray-900 px-8 py-4 rounded-lg text-lg font-semibold transition-all">
                    <i class="fas fa-building mr-2"></i>
                    View Facilities
                </a>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <i class="fas fa-chevron-down text-white text-2xl"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Powerful Features
                </h2>
                <p data-aos="fade-up" data-aos-delay="200" class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need to manage facility reservations efficiently and effectively
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div data-aos="fade-up" data-aos-delay="100" class="feature-card bg-white p-8 rounded-xl">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-calendar-check text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Easy Booking</h3>
                        <p class="text-gray-600">
                            Simple and intuitive booking process with real-time availability checking and instant confirmation.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 2 -->
                <div data-aos="fade-up" data-aos-delay="200" class="feature-card bg-white p-8 rounded-xl">
                    <div class="text-center">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-clock text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Real-time Availability</h3>
                        <p class="text-gray-600">
                            Check facility availability in real-time and avoid double bookings with our smart scheduling system.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 3 -->
                <div data-aos="fade-up" data-aos-delay="300" class="feature-card bg-white p-8 rounded-xl">
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-bell text-purple-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Smart Notifications</h3>
                        <p class="text-gray-600">
                            Get automated reminders and updates about your reservations via email and SMS notifications.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 4 -->
                <div data-aos="fade-up" data-aos-delay="400" class="feature-card bg-white p-8 rounded-xl">
                    <div class="text-center">
                        <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-chart-bar text-yellow-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Usage Analytics</h3>
                        <p class="text-gray-600">
                            Track facility usage patterns and generate detailed reports for better resource management.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 5 -->
                <div data-aos="fade-up" data-aos-delay="500" class="feature-card bg-white p-8 rounded-xl">
                    <div class="text-center">
                        <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-mobile-alt text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Mobile Friendly</h3>
                        <p class="text-gray-600">
                            Access and manage your reservations from any device with our fully responsive design.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 6 -->
                <div data-aos="fade-up" data-aos-delay="600" class="feature-card bg-white p-8 rounded-xl">
                    <div class="text-center">
                        <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-users text-indigo-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Multi-user Support</h3>
                        <p class="text-gray-600">
                            Support for multiple users with role-based access control and permission management.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section id="facilities" class="py-20 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Our Facilities
                </h2>
                <p data-aos="fade-up" data-aos-delay="200" class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Discover our range of modern facilities available for reservation
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($facilities as $facility)
                <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}" class="facility-card bg-white p-6 rounded-xl shadow-lg">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-building text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $facility->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ $facility->description ?? 'Modern facility for your needs' }}</p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span><i class="fas fa-users mr-1"></i> {{ $facility->capacity ?? 'N/A' }} people</span>
                            <span><i class="fas fa-wifi mr-1"></i> {{ $facility->amenities ?? 'Standard' }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">No facilities available at the moment</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Reservation Form Section -->
    <section id="reserve" class="py-20 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    New Request
                </h2>
                <p data-aos="fade-up" data-aos-delay="200" class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Submit a new facility management request
                </p>
            </div>
            
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <!-- Request Details Section -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-paper-plane text-blue-600 mr-3"></i>
                            Request Details
                        </h3>
                        
                        <form id="reservationForm" class="space-y-6">
                            @csrf
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Request Type -->
                                <div class="space-y-2">
                                    <label for="request_type" class="block text-sm font-semibold text-gray-700">
                                        Request Type <span class="text-red-500">*</span>
                                    </label>
                                    <select id="request_type" name="request_type" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white">
                                        <option value="">Select request type</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="reservation">Reservation</option>
                                        <option value="equipment_request">Equipment Request</option>
                                    </select>
                                </div>
                                
                                <!-- Department -->
                                <div class="space-y-2">
                                    <label for="department" class="block text-sm font-semibold text-gray-700">
                                        Department <span class="text-red-500">*</span>
                                    </label>
                                    <select id="department" name="department" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white">
                                        <option value="">Select department</option>
                                        <option value="Human Resources">Human Resources</option>
                                        <option value="Finance">Finance</option>
                                        <option value="Logistic">Logistic</option>
                                        <option value="Core 1">Core 1</option>
                                        <option value="Core 2">Core 2</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                
                                <!-- Priority Level -->
                                <div class="space-y-2">
                                    <label for="priority" class="block text-sm font-semibold text-gray-700">
                                        Priority Level <span class="text-red-500">*</span>
                                    </label>
                                    <select id="priority" name="priority" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white">
                                        <option value="">Select priority</option>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                                
                                <!-- Location -->
                                <div class="space-y-2">
                                    <label for="location" class="block text-sm font-semibold text-gray-700">
                                        Location <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="location" name="location" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white"
                                           placeholder="e.g., Room 302, Conference Room B">
                                </div>
                                
                                <!-- Facility Selection (if reservation type) -->
                                <div class="space-y-2" id="facility_selection" style="display: none;">
                                    <label for="facility_id" class="block text-sm font-semibold text-gray-700">
                                        Select Facility <span class="text-red-500">*</span>
                                    </label>
                                    <select id="facility_id" name="facility_id"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white">
                                        <option value="">Choose a facility</option>
                                        @foreach($facilities as $facility)
                                            @php
                                                $occupiedLabel = '';
                                                if ($facility->status === 'occupied') {
                                                    $in = null; $until = null;
                                                    // Prefer an active reservation window
                                                    $activeRes = \App\Models\FacilityReservation::where('facility_id', $facility->id)
                                                        ->where('status', 'approved')
                                                        ->where('start_time', '<=', now())
                                                        ->where(function($q){ $q->whereNull('end_time')->orWhere('end_time', '>=', now()); })
                                                        ->latest('start_time')
                                                        ->first();
                                                    if ($activeRes) {
                                                        $in = $activeRes->start_time; $until = $activeRes->end_time;
                                                    } else {
                                                        // Last approved request as last-known start
                                                        $req = \App\Models\FacilityRequest::where('facility_id',$facility->id)
                                                            ->where('status','approved')
                                                            ->latest('requested_datetime')->first();
                                                        if ($req) { $in = $req->requested_datetime; $until = $req->requested_end_datetime; }
                                                    }
                                                    $inFmt = $in ? \Carbon\Carbon::parse($in)->format('M d, Y h:i A') : 'Unknown';
                                                    $untilFmt = $until ? \Carbon\Carbon::parse($until)->format('M d, Y h:i A') : 'Unknown';
                                                    $occupiedLabel = " (In: {$inFmt} • Until: {$untilFmt})";
                                                }
                                            @endphp
                                            @if($facility->status === 'available')
                                                <option value="{{ $facility->id }}">✅ {{ $facility->name }}</option>
                                            @else
                                                <option value="" disabled class="text-gray-400 bg-gray-100">❌ {{ $facility->name }} - Occupied{{ $occupiedLabel }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <span class="inline-flex items-center mr-3"><span class="text-green-500 mr-1">✅</span> Available</span>
                                        <span class="inline-flex items-center"><span class="text-red-500 mr-1">❌</span> Occupied</span>
                                    </p>
                                </div>
                                
                                <!-- Equipment Request Section (shown for equipment_request) -->
                                <div class="space-y-2 md:col-span-2" id="equipment_section" style="display: none;">
                                    <label class="block text-sm font-semibold text-gray-700">
                                        Equipment Details <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid md:grid-cols-3 gap-4">
                                        <div class="space-y-2 md:col-span-2">
                                            <label class="block text-xs font-semibold text-gray-600">Equipment Item</label>
                                            <select id="equipment_item" name="equipment_item" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 bg-white">
                                                <option value="Vacuum cleaners">Vacuum cleaners (upright, canister, robotic)</option>
                                                <option value="Floor polishing/buffing machines">Floor polishing/buffing machines</option>
                                                <option value="Laundry equipment">Laundry equipment (washing machines, dryers, steam irons)</option>
                                                <option value="Cleaning carts / trolleys">Cleaning carts / trolleys</option>
                                                <option value="Linen storage racks">Linen storage racks</option>
                                                <option value="Disinfecting sprayers">Disinfecting sprayers</option>
                                                <option value="Housekeeping radios / communication devices">Housekeeping radios / communication devices</option>
                                            </select>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-xs font-semibold text-gray-600">Quantity</label>
                                            <input type="number" id="equipment_quantity" name="equipment_quantity" min="1" value="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 bg-white">
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500">Selected items will be routed to Logistics for preparation and inventory tracking.</p>
                                </div>
                                
                                <!-- Requested Date & Time -->
                                <div class="space-y-2">
                                    <label for="requested_datetime" class="block text-sm font-semibold text-gray-700">
                                        Requested Date & Time <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="datetime-local" id="requested_datetime" name="requested_datetime" required
                                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white">
                                    </div>
                                </div>
                                <!-- Reservation End (Reservation only) -->
                                <div class="space-y-2" id="reservation_end_wrapper" style="display: none;">
                                    <label for="requested_end_datetime" class="block text-sm font-semibold text-gray-700">
                                        Until (End Date & Time)
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-clock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="datetime-local" id="requested_end_datetime" name="requested_end_datetime"
                                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="space-y-2">
                                <label for="description" class="block text-sm font-semibold text-gray-700">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <textarea id="description" name="description" rows="4" required
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white resize-none"
                                          placeholder="Provide detailed description of the request..."></textarea>
                            </div>
                            
                            <!-- Contact Information -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="contact_name" class="block text-sm font-semibold text-gray-700">
                                        Contact Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="contact_name" name="contact_name" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white"
                                           placeholder="Your full name">
                                </div>
                                
                                <div class="space-y-2">
                                    <label for="contact_email" class="block text-sm font-semibold text-gray-700">
                                        Contact Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="contact_email" name="contact_email" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white"
                                           placeholder="your.email@example.com">
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex justify-end space-x-4 pt-6">
                                <button type="button" 
                                        class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold transition-all duration-200">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-all duration-200 flex items-center">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Submit Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="py-20 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Why Choose Our System?
                </h2>
                <p data-aos="fade-up" data-aos-delay="200" class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Experience the benefits of modern facility reservation management
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <h3 class="text-3xl font-bold text-gray-900 mb-6">Streamlined Reservation Process</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-1"></i>
                            <span class="text-gray-700">Quick and easy facility booking process</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-1"></i>
                            <span class="text-gray-700">Real-time availability checking</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-1"></i>
                            <span class="text-gray-700">Instant confirmation and notifications</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-1"></i>
                            <span class="text-gray-700">Automated conflict resolution</span>
                        </li>
                    </ul>
                </div>
                
                <div data-aos="fade-left" class="relative">
                    <div class="bg-white p-8 rounded-xl shadow-lg">
                        <div class="text-center">
                            <i class="fas fa-calendar-check text-6xl text-blue-600 mb-4"></i>
                            <h4 class="text-2xl font-bold text-gray-900 mb-2">100+</h4>
                            <p class="text-gray-600">Successful Reservations</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 hero-gradient">
        <div class="container mx-auto px-4 text-center">
            <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold text-white mb-6">
                Ready to Get Started?
            </h2>
            <p data-aos="fade-up" data-aos-delay="200" class="text-xl text-gray-200 max-w-3xl mx-auto mb-8">
                Join the modern era of facility management. Start booking your facilities today.
            </p>
            <div data-aos="fade-up" data-aos-delay="400">
                <a href="{{ route('facility_reservations.index') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all transform hover:scale-105 inline-block">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Access Reservation System
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4">
                        <span class="gradient-text">SOLIERA</span>
                    </h3>
                    <p class="text-gray-400 mb-4">
                        Advanced facility reservation system for modern organizations. 
                        Efficient, user-friendly, and reliable.
                    </p>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#facilities" class="text-gray-400 hover:text-white transition-colors">Facilities</a></li>
                        <li><a href="{{ route('facility_reservations.index') }}" class="text-gray-400 hover:text-white transition-colors">Reservations</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <p class="text-gray-400">Email: info@soliera.com</p>
                    <p class="text-gray-400">Phone: +1 (555) 123-4567</p>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; 2025 Soliera. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNav');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Show/hide facility or equipment fields based on request type
        document.getElementById('request_type').addEventListener('change', function() {
            const facilitySelection = document.getElementById('facility_selection');
            const facilitySelect = document.getElementById('facility_id');
            const equipmentSection = document.getElementById('equipment_section');
            const reservationEnd = document.getElementById('reservation_end_wrapper');
            
            if (this.value === 'reservation') {
                facilitySelection.style.display = 'block';
                facilitySelect.required = true;
                equipmentSection.style.display = 'none';
                reservationEnd.style.display = 'block';
                // clear equipment requireds
                document.getElementById('equipment_item').required = false;
                document.getElementById('equipment_quantity').required = false;
            } else {
                facilitySelection.style.display = 'none';
                facilitySelect.required = false;
                facilitySelect.value = '';
                reservationEnd.style.display = 'none';
                if (this.value === 'equipment_request') {
                    equipmentSection.style.display = 'block';
                    document.getElementById('equipment_item').required = true;
                    document.getElementById('equipment_quantity').required = true;
                } else {
                    equipmentSection.style.display = 'none';
                    document.getElementById('equipment_item').required = false;
                    document.getElementById('equipment_quantity').required = false;
                }
            }
        });

        // Initialize visibility on load based on current request type
        (function initTypeVisibility(){
            const sel = document.getElementById('request_type');
            if (sel && sel.value) {
                const event = new Event('change');
                sel.dispatchEvent(event);
            }
        })();
        // (Delivery method removed)
        
        // Form validation and submission
        document.getElementById('reservationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i>Submitting...';
            submitBtn.disabled = true;
            
            // Collect form data
            const formData = new FormData(form);
            
            // Validate required fields
            const requestType = formData.get('request_type');
            const department = formData.get('department');
            const priority = formData.get('priority');
            const location = formData.get('location');
            const description = formData.get('description');
            const contactName = formData.get('contact_name');
            const contactEmail = formData.get('contact_email');
            const requestedDateTime = formData.get('requested_datetime');
            
            if (!requestType || !department || !priority || !location || !description || !contactName || !contactEmail || !requestedDateTime) {
                showNotification('Please fill in all required fields', 'error');
                resetButton();
                return;
            }
            
            // If reservation, check if facility is selected and available
            if (requestType === 'reservation') {
                const facilityId = formData.get('facility_id');
                const facilitySelect = document.getElementById('facility_id');
                const selectedOption = facilitySelect.options[facilitySelect.selectedIndex];
                
                if (!facilityId) {
                    showNotification('Please select a facility for reservation', 'error');
                    resetButton();
                    return;
                }
                
                // Check if selected facility is disabled (occupied)
                if (selectedOption.disabled) {
                    showNotification('The selected facility is currently occupied. Please choose another facility.', 'error');
                    resetButton();
                    return;
                }
            }
            
            // Submit form via AJAX
            fetch('{{ route("facility_reservations.store_request") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(async response => {
                let data = null;
                try {
                    data = await response.json();
                } catch (err) {
                    throw new Error('INVALID_JSON_RESPONSE');
                }
                
                if (response.ok && data) {
                    showNotification('Request submitted successfully! You will receive a confirmation email shortly.', 'success');
                    form.reset();
                    // Hide facility selection after reset
                    document.getElementById('facility_selection').style.display = 'none';
                    // Redirect to New Request list tab based on type
                    if (data.view_url) {
                      setTimeout(() => { window.location.href = data.view_url; }, 600);
                    }
                    return;
                }
                
                const message = (data && (data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : ''))) || 'Error submitting reservation. Please try again.';
                showNotification(message, 'error');
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                resetButton();
            });
            
            function resetButton() {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            }
        });
        
        // Notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed bottom-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-3"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 5000);
        }

        // Function to refresh facility dropdown (called from monitoring page)
        function refreshFacilityDropdown() {
            // Reload the page to get updated facility statuses
            window.location.reload();
        }
    </script>
</body>
</html>
