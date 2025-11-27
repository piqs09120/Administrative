<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Soliera Visitor Management System</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <style>
        * {
            scroll-behavior: smooth;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #001f54 0%, #003d7a 50%, #0056b3 100%);
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
            background-color: rgba(0, 31, 84, 0.95) !important;
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

    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav id="mainNav" class="navbar fixed top-0 w-full z-50 bg-transparent">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <img src="{{ asset('images/logo/logofinal.png') }}" alt="SOLIERA Logo" class="h-12 md:h-16 w-auto object-contain">
                </div>
                <div class="hidden md:flex space-x-8 mx-auto">
                    <a href="#features" class="text-white hover:text-yellow-400 transition-colors">Features</a>
                    <a href="#register" class="text-white hover:text-yellow-400 transition-colors">Register Visitor</a>
                    <a href="#benefits" class="text-white hover:text-yellow-400 transition-colors">Benefits</a>
                    <a href="#contact" class="text-white hover:text-yellow-400 transition-colors">Contact</a>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Spacer to keep navigation centered -->
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        
        <!-- Floating Elements -->
        <div class="absolute top-20 left-10 floating-animation">
            <i class="fas fa-user-check text-yellow-400 text-4xl opacity-20"></i>
        </div>
        <div class="absolute top-40 right-20 floating-animation" style="animation-delay: 2s;">
            <i class="fas fa-shield-alt text-yellow-400 text-3xl opacity-20"></i>
        </div>
        <div class="absolute bottom-40 left-20 floating-animation" style="animation-delay: 4s;">
            <i class="fas fa-qrcode text-yellow-400 text-5xl opacity-20"></i>
        </div>
        
        <div class="text-center px-4 z-10 relative max-w-6xl mx-auto">
            <h1 data-aos="fade-up" class="text-5xl md:text-7xl font-bold text-white mb-6">
                Visitor Management
                <span class="block gradient-text">System</span>
            </h1>
            
            <p data-aos="fade-up" data-aos-delay="200" class="text-xl md:text-2xl text-gray-200 max-w-3xl mx-auto mb-8">
                Streamline your visitor experience with our comprehensive digital pass system. 
                Secure, efficient, and user-friendly visitor management for modern facilities.
            </p>
            
            <div data-aos="fade-up" data-aos-delay="400" class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('visitor.index') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all transform hover:scale-105">
                    <i class="fas fa-rocket mr-2"></i>
                    Start Managing Visitors
                </a>
                <a href="#features" class="border-2 border-white text-white hover:bg-white hover:text-gray-900 px-8 py-4 rounded-lg text-lg font-semibold transition-all">
                    <i class="fas fa-info-circle mr-2"></i>
                    Learn More
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
                    Everything you need to manage visitors efficiently and securely
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div data-aos="fade-up" data-aos-delay="100" class="feature-card bg-white p-8 rounded-xl">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-qrcode text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Digital Passes</h3>
                        <p class="text-gray-600">
                            Generate secure QR code passes for visitors with real-time validation and expiration tracking.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 2 -->
                <div data-aos="fade-up" data-aos-delay="200" class="feature-card bg-white p-8 rounded-xl">
                    <div class="text-center">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-clock text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Real-time Tracking</h3>
                        <p class="text-gray-600">
                            Monitor visitor check-ins and check-outs in real-time with comprehensive activity logs.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 3 -->
                <div data-aos="fade-up" data-aos-delay="300" class="feature-card bg-white p-8 rounded-xl">
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-shield-alt text-purple-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Security First</h3>
                        <p class="text-gray-600">
                            Advanced security features with visitor verification and access control management.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 4 -->
                <div data-aos="fade-up" data-aos-delay="400" class="feature-card bg-white p-8 rounded-xl">
                    <div class="text-center">
                        <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-chart-bar text-yellow-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Analytics & Reports</h3>
                        <p class="text-gray-600">
                            Comprehensive visitor analytics and detailed reports for better facility management.
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
                            Fully responsive design that works seamlessly on all devices and screen sizes.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 6 -->
                <div data-aos="fade-up" data-aos-delay="600" class="feature-card bg-white p-8 rounded-xl">
                    <div class="text-center">
                        <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-cogs text-indigo-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Easy Integration</h3>
                        <p class="text-gray-600">
                            Simple setup and integration with existing systems and workflows.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Visitor Registration Form Section -->
    <section id="register" class="py-20 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Register a Visitor
                </h2>
                <p data-aos="fade-up" data-aos-delay="200" class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Complete all required fields to register a new visitor and generate their digital pass
                </p>
            </div>
            
            <div class="max-w-4xl mx-auto">
                <div class="form-card rounded-2xl shadow-2xl p-8 md:p-12">
                    <form id="visitorRegistrationForm" class="space-y-8">
                        @csrf
                        
                        <!-- Personal Information Section -->
                        <div class="border-b border-gray-200 pb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-user text-blue-600 mr-3"></i>
                                Personal Information
                            </h3>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Full Name -->
                                <div class="space-y-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" required
                                           class="form-input w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50"
                                           placeholder="Enter visitor's full name">
                                </div>
                                
                                <!-- Email Address -->
                                <div class="space-y-2">
                                    <label for="email" class="block text-sm font-semibold text-gray-700">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="email" id="email" name="email" required
                                               class="form-input w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50"
                                               placeholder="visitor@example.com">
                                    </div>
                                </div>
                                
                                <!-- Phone Number -->
                                <div class="space-y-2">
                                    <label for="contact" class="block text-sm font-semibold text-gray-700">
                                        Phone Number <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-phone absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="tel" id="contact" name="contact" required
                                               class="form-input w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50"
                                               placeholder="+1 (555) 000-0000">
                                    </div>
                                </div>
                                
                                <!-- ID Type -->
                                <div class="space-y-2">
                                    <label for="id_type" class="block text-sm font-semibold text-gray-700">
                                        ID Type <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <div class="relative" style="width: 220px;">
                                        <i class="fas fa-id-card absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <select id="id_type" name="id_type" required
                                                class="form-input w-full pl-10 pr-10 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50 appearance-none">
                                            <option value="">Select ID Type</option>
                                            <optgroup label="Primary IDs">
                                                <option value="philnational_id" data-accept="image/*">Philippine National ID (PhilSys)</option>
                                                <option value="passport" data-accept="image/*">Philippine Passport</option>
                                                <option value="drivers_license" data-accept="image/*">Driver's License</option>
                                            </optgroup>
                                            <optgroup label="Government IDs">
                                                <option value="umid" data-accept="image/*">Unified Multipurpose ID (UMID)</option>
                                                <option value="postal_id" data-accept="image/*">Postal ID</option>
                                                <option value="voters_id" data-accept="image/*">Voter's ID</option>
                                                <option value="sss_id" data-accept="image/*">SSS ID</option>
                                                <option value="gsis_id" data-accept="image/*">GSIS ID</option>
                                                <option value="tin_id" data-accept="image/*">TIN ID</option>
                                            </optgroup>
                                            <optgroup label="Professional IDs">
                                                <option value="prc_id" data-accept="image/*">Professional Regulation Commission (PRC)</option>
                                                <option value="barangay_id" data-accept="image/*">Barangay ID</option>
                                                <option value="senior_citizen_id" data-accept="image/*">Senior Citizen ID</option>
                                                <option value="pwd_id" data-accept="image/*">PWD ID</option>
                                            </optgroup>
                                            <optgroup label="Other Valid IDs">
                                                <option value="company_id" data-accept="image/*">Company ID</option>
                                                <option value="school_id" data-accept="image/*">School ID</option>
                                                <option value="other_id" data-accept="image/*">Other Valid ID</option>
                                            </optgroup>
                                        </select>
                                        <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                        </div>
                                        <label for="id_image_upload" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                                            <i class="fas fa-upload"></i>
                                            <span class="font-semibold">Upload ID</span>
                                            <input type="file" id="id_image_upload" name="id_image" accept="image/*" class="hidden">
                                        </label>
                                    </div>
                                    
                                    <!-- File Preview -->
                                    <div id="id_file_preview" class="hidden mt-3">
                                        <div class="inline-flex items-center gap-2 bg-white border-2 border-gray-300 rounded-lg px-4 py-2 shadow-sm">
                                            <i class="fas fa-file-image text-blue-600"></i>
                                            <span id="id_file_name" class="text-sm text-gray-700 font-medium"></span>
                                            <button type="button" id="remove_id_file" class="ml-2 text-red-500 hover:text-red-700 transition-colors">
                                                <i class="fas fa-times-circle text-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Visit Details Section -->
                        <div class="border-b border-gray-200 pb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-calendar-alt text-green-600 mr-3"></i>
                                Visit Details
                            </h3>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Purpose of Visit -->
                                <div class="space-y-2">
                                    <label for="purpose" class="block text-sm font-semibold text-gray-700">
                                        Purpose of Visit <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="purpose" name="purpose" required
                                           class="form-input w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50"
                                           placeholder="e.g. Business Meeting, Interview">
                                </div>
                                
                                <!-- Host Name -->
                                <div class="space-y-2">
                                    <label for="host_employee" class="block text-sm font-semibold text-gray-700">
                                        Host Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="host_employee" name="host_employee" required
                                           class="form-input w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50"
                                           placeholder="Name of the person to visit">
                                </div>
                                
                                <!-- Host Department -->
                                <div class="space-y-2">
                                    <label for="department" class="block text-sm font-semibold text-gray-700">
                                        Host Department
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-building absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <select id="department" name="department"
                                                class="form-input w-full pl-10 pr-10 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50 appearance-none">
                                            <option value="">Select department</option>
                                            <option value="hr1">HR1</option>
                                            <option value="hr2">HR2</option>
                                            <option value="hr3">HR3</option>
                                            <option value="hr4">HR4</option>
                                            <option value="finance">Finance</option>
                                            <option value="logistic_1">Logistic 1</option>
                                            <option value="logistic_2">Logistic 2</option>
                                            <option value="core_1">Core 1</option>
                                            <option value="core_2">Core 2</option>
                                        </select>
                                        <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                    </div>
                                </div>
                                
                                <!-- Visit Type -->
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-4">
                                        Visit Type <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-all duration-200 bg-yellow-50">
                                            <input type="radio" name="visit_type" value="immediate" class="mr-3 text-blue-600" checked>
                                            <div>
                                                <div class="font-semibold text-gray-900">Immediate Visit</div>
                                                <div class="text-sm text-gray-600">Visit today or soon</div>
                                            </div>
                                        </label>
                                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-all duration-200 bg-yellow-50">
                                            <input type="radio" name="visit_type" value="preschedule" class="mr-3 text-blue-600">
                                            <div>
                                                <div class="font-semibold text-gray-900">Pre-Schedule Visit</div>
                                                <div class="text-sm text-gray-600">Schedule for a future date</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Arrival Date -->
                                <div class="space-y-2" id="arrival_date_container">
                                    <label for="arrival_date" class="block text-sm font-semibold text-gray-700">
                                        Arrival Date <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="date" id="arrival_date" name="arrival_date" required
                                               class="form-input w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50">
                                    </div>
                                </div>

                                <!-- Scheduled Date (for pre-schedule) -->
                                <div class="space-y-2 hidden" id="scheduled_date_container">
                                    <label for="scheduled_date" class="block text-sm font-semibold text-gray-700">
                                        Scheduled Date <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="date" id="scheduled_date" name="scheduled_date"
                                               class="form-input w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50">
                                    </div>
                                </div>
                                
                                <!-- Expected Arrival Time -->
                                <div class="space-y-2">
                                    <label for="arrival_time" class="block text-sm font-semibold text-gray-700">
                                        Expected Arrival Time <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-clock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="time" id="arrival_time" name="arrival_time" required
                                               class="form-input w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50">
                                    </div>
                                </div>

                                <!-- Expected Date Out -->
                                <div class="space-y-2">
                                    <label for="expected_date_out" class="block text-sm font-semibold text-gray-700">
                                        Expected Date Out
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="date" id="expected_date_out" name="expected_date_out"
                                               class="form-input w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50">
                                    </div>
                                </div>

                                <!-- Expected Time Out -->
                                <div class="space-y-2">
                                    <label for="expected_time_out" class="block text-sm font-semibold text-gray-700">
                                        Expected Time Out
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-clock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="time" id="expected_time_out" name="expected_time_out"
                                               class="form-input w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Consent Line -->
                        <div id="consentContainer" class="mt-6 text-sm text-gray-700 space-y-2">
                            <p id="preConsentMessage">
                                I have read and agree to Soliera’s
                                <a href="#" class="privacy-link text-blue-600 underline hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 rounded">Data Privacy Policy</a>.
                            </p>
                            <label id="inlineConsentLabel" for="agree" class="hidden flex items-start gap-3">
                                <input type="checkbox" id="agree" name="agree" required disabled class="mt-1 checkbox checkbox-primary checkbox-sm focus:ring focus:ring-offset-1 focus:ring-[#F7A923]">
                                <span>
                                    I have read and agree to Soliera’s
                                    <a href="#" class="privacy-link text-blue-600 underline hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 rounded">Data Privacy Policy</a>.
                                </span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center pt-6">
                            <button type="submit" id="registerBtn"
                                    class="bg-gradient-to-r from-[#F7A923] to-[#E6940F] hover:from-[#E6940F] hover:to-[#D2840E] text-[#2C3E50] px-12 py-4 rounded-lg text-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center mx-auto"
                                    disabled>
                                <i class="fas fa-user-plus mr-3"></i>
                                Register Visitor
                            </button>
                        </div>
                    </form>
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
                    Experience the benefits of modern visitor management
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <h3 class="text-3xl font-bold text-gray-900 mb-6">Streamlined Visitor Experience</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-1"></i>
                            <span class="text-gray-700">Quick and easy visitor registration process</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-1"></i>
                            <span class="text-gray-700">Instant digital pass generation</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-1"></i>
                            <span class="text-gray-700">Real-time status updates</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-1"></i>
                            <span class="text-gray-700">Automated notifications</span>
                        </li>
                    </ul>
                </div>
                
                <div data-aos="fade-left" class="relative">
                    <div class="bg-white p-8 rounded-xl shadow-lg">
                        <div class="text-center">
                            <i class="fas fa-users text-6xl text-blue-600 mb-4"></i>
                            <h4 class="text-2xl font-bold text-gray-900 mb-2">40+</h4>
                            <p class="text-gray-600">Total Visitors Managed</p>
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
                Join the modern era of visitor management. Start streamlining your visitor experience today.
            </p>
            <div data-aos="fade-up" data-aos-delay="400">
                <a href="{{ route('visitor.index') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all transform hover:scale-105 inline-block">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Access Visitor Management
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
                        Advanced visitor management system for modern facilities. 
                        Secure, efficient, and user-friendly.
                    </p>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#benefits" class="text-gray-400 hover:text-white transition-colors">Benefits</a></li>
                        <li><a href="{{ route('visitor.index') }}" class="text-gray-400 hover:text-white transition-colors">Dashboard</a></li>
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

    <!-- Privacy Modal (restored simpler styling) -->
    <dialog id="privacyModal" class="modal">
        <div class="modal-box w-11/12 max-w-3xl space-y-5 bg-white text-gray-700 leading-7 max-h-[85vh]">
            <div class="space-y-2 text-center">
                <h3 class="text-2xl font-semibold text-gray-900">Data Privacy Policy</h3>
                <p class="text-sm text-gray-500">Please read the full policy below before agreeing.</p>
            </div>
            <div id="privacyScrollArea" class="border border-gray-200 rounded-xl px-5 py-4 overflow-y-auto max-h-[55vh] text-sm space-y-5">
                <section class="space-y-3">
                    <p><strong>Soliera Hospitality Group (“Soliera”, “we”, “our”)</strong> is committed to protecting the privacy of our guests, visitors, suppliers, and business partners.</p>
                    <p>This Data Privacy Policy outlines how we collect, use, store, disclose, and protect personal data in accordance with the Data Privacy Act of 2012 (Republic Act No. 10173), its Implementing Rules and Regulations, and relevant issuances of the National Privacy Commission (NPC).</p>
                    <p>The policy covers information submitted through our visitor-management web application, at the front desk, at hotel access points, and through any Soliera-managed digital or on-premises systems.</p>
                </section>
                <section class="space-y-2">
                    <h4 class="font-semibold text-gray-900">1. Personal Data We Collect</h4>
                    <p>Depending on your transaction with us, we may request:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Identifying data</strong>: full name, preferred name, date of birth, sex, nationality, civil status, company/organization, job title.</li>
                        <li><strong>Contact details</strong>: address, email, mobile/landline number, emergency contact.</li>
                        <li><strong>Government-issued documents</strong>: ID type and number, ID image or copy, passport/visa information.</li>
                        <li><strong>Visit particulars</strong>: arrival/departure schedules, host department, visitor purpose/type, vehicle/plate number, special assistance requests.</li>
                        <li><strong>Security and monitoring data</strong>: QR/RFID passes, access logs, CCTV footage, incident reports, system metadata (IP address, browser, device ID).</li>
                    </ul>
                    <p>We do not knowingly collect personal data of minors without parent or guardian consent.</p>
                </section>
                <section class="space-y-2">
                    <h4 class="font-semibold text-gray-900">2. Purpose and Legal Basis</h4>
                    <p>Personal data is processed only for legitimate purposes such as facilitating reservations and visitor passes, complying with statutory requirements, ensuring safety and security, maintaining audit trails, sending service advisories, and handling inquiries or legal claims. Processing relies on lawful bases including your consent, contract fulfillment, legal obligations, protection of vital interests, legitimate business interests, or legal claims.</p>
                </section>
                <section class="space-y-2">
                    <h4 class="font-semibold text-gray-900">3. Sharing and Disclosure</h4>
                    <p>We do not sell personal data. We may share information with authorized Soliera personnel, affiliates, service providers, government agencies, emergency responders, insurers, or legal counsel when required by law or to protect life, health, property, or legitimate rights. Recipients are required to implement adequate safeguards.</p>
                </section>
                <section class="space-y-2">
                    <h4 class="font-semibold text-gray-900">4. Data Retention</h4>
                    <p>Information is retained only as long as necessary or as required by law (e.g., visitor logs for at least one year, financial records for ten years, CCTV footage for up to 30 days). Once retention periods lapse, records are securely disposed of through anonymization, wiping, shredding, or other NPC-compliant methods.</p>
                </section>
                <section class="space-y-2">
                    <h4 class="font-semibold text-gray-900">5. Data Security</h4>
                    <p>Safeguards include role-based access control, MFA, encryption, firewalls, IDS, antivirus, vulnerability scanning, CCTV monitoring, incident response protocols, privacy-by-design controls, and staff confidentiality undertakings.</p>
                </section>
                <section class="space-y-2">
                    <h4 class="font-semibold text-gray-900">6. Your Rights</h4>
                    <p>You may be informed, access, rectify, or request deletion of your personal data; withdraw consent; object to processing; request data portability (when feasible); seek indemnification for damages; and file a complaint with the National Privacy Commission, subject to RA 10173 limitations.</p>
                </section>
                <section class="space-y-2">
                    <h4 class="font-semibold text-gray-900">7. Contact Our DPO</h4>
                    <p>For privacy-related concerns, contact our Data Protection Officer via <a href="mailto:solierahotelandrestaurant@gmail.com" class="text-blue-600 underline">solierahotelandrestaurant@gmail.com</a> or (+63) 2 8123 4567. Office: Soliera Hospitality Group, 28F Soliera Tower, Ayala Avenue, Makati City, Philippines.</p>
                </section>
                <section class="space-y-2">
                    <h4 class="font-semibold text-gray-900">8. Consent &amp; Updates</h4>
                    <p>By submitting your information, visiting our premises, or using our services, you acknowledge that you have read, understood, and agree to this policy. You may withdraw consent anytime, subject to legal limitations. Policy updates will be announced through official channels and take effect upon posting.</p>
                </section>
            </div>
            <div class="space-y-3 text-xs text-gray-500">
                <p>You may withdraw consent or request access, correction, or deletion of your data by contacting our Front Desk or emailing our Data Protection Officer.</p>
                <label class="flex items-start gap-3 text-sm text-gray-700 select-none pt-2 border-t border-gray-200">
                    <input type="checkbox" id="privacyModalCheckbox" class="mt-1 checkbox checkbox-sm" disabled>
                    <span>I confirm that I have read and agree to the Data Privacy Policy above.</span>
                </label>
            </div>
            <div class="modal-action justify-between">
                <form method="dialog">
                    <button class="btn btn-ghost">Close</button>
                </form>
                <button type="button" id="privacyModalAgree" class="btn bg-gradient-to-r from-[#F7A923] to-[#E6940F] text-[#2C3E50] border-none shadow-md hover:from-[#E6940F] hover:to-[#D2840E] disabled:bg-gray-200 disabled:text-gray-500" disabled>
                    Agree &amp; Continue
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button aria-label="Close data privacy policy modal">close</button>
        </form>
    </dialog>

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
        
        // ID Upload Handler
        const idImageUpload = document.getElementById('id_image_upload');
        const idFilePreview = document.getElementById('id_file_preview');
        const idFileName = document.getElementById('id_file_name');
        const removeIdFile = document.getElementById('remove_id_file');

        if (idImageUpload) {
            idImageUpload.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    idFileName.textContent = file.name;
                    idFilePreview.classList.remove('hidden');
                }
            });
        }

        if (removeIdFile) {
            removeIdFile.addEventListener('click', function() {
                idImageUpload.value = '';
                idFilePreview.classList.add('hidden');
                idFileName.textContent = '';
            });
        }
        
        // Visit Type Toggle
        document.querySelectorAll('input[name="visit_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const arrivalDateContainer = document.getElementById('arrival_date_container');
                const scheduledDateContainer = document.getElementById('scheduled_date_container');
                const arrivalDateInput = document.getElementById('arrival_date');
                const scheduledDateInput = document.getElementById('scheduled_date');
                
                if (this.value === 'preschedule') {
                    arrivalDateContainer.classList.add('hidden');
                    scheduledDateContainer.classList.remove('hidden');
                    arrivalDateInput.removeAttribute('required');
                    scheduledDateInput.setAttribute('required', 'required');
                    scheduledDateInput.setAttribute('min', new Date().toISOString().split('T')[0]);
                } else {
                    arrivalDateContainer.classList.remove('hidden');
                    scheduledDateContainer.classList.add('hidden');
                    arrivalDateInput.setAttribute('required', 'required');
                    scheduledDateInput.removeAttribute('required');
                }
            });
        });

        // Consent gating
        const inlineConsentLabel = document.getElementById('inlineConsentLabel');
        const preConsentMessage = document.getElementById('preConsentMessage');
        const agreeCheckbox = document.getElementById('agree');
        const registerBtn = document.getElementById('registerBtn');
        const privacyModal = document.getElementById('privacyModal');
        const privacyLinks = document.querySelectorAll('.privacy-link');
        const privacyScrollArea = document.getElementById('privacyScrollArea');
        const privacyModalCheckbox = document.getElementById('privacyModalCheckbox');
        const privacyModalAgree = document.getElementById('privacyModalAgree');
        let privacyAccepted = false;

        function updateRegisterButtonState() {
            if (registerBtn) {
                registerBtn.disabled = !(privacyAccepted && agreeCheckbox && agreeCheckbox.checked);
            }
        }

        updateRegisterButtonState();

        if (agreeCheckbox) {
            agreeCheckbox.addEventListener('change', updateRegisterButtonState);
        }

        function resetPrivacyModalState() {
            if (privacyScrollArea) {
                privacyScrollArea.scrollTop = 0;
            }
            if (privacyModalCheckbox) {
                privacyModalCheckbox.checked = false;
                privacyModalCheckbox.disabled = true;
            }
            if (privacyModalAgree) {
                privacyModalAgree.disabled = true;
            }
        }

        function revealInlineConsent() {
            if (!privacyAccepted) {
                privacyAccepted = true;
                if (preConsentMessage) {
                    preConsentMessage.classList.add('hidden');
                }
                if (inlineConsentLabel) {
                    inlineConsentLabel.classList.remove('hidden');
                }
                if (agreeCheckbox) {
                    agreeCheckbox.disabled = false;
                    agreeCheckbox.checked = true;
                }
            }
            updateRegisterButtonState();
        }

        const docEl = document.documentElement;
        function lockBodyScroll() {
            if (document.body.dataset.prevOverflow === undefined) {
                document.body.dataset.prevOverflow = document.body.style.overflow || '';
            }
            if (docEl.dataset.prevOverflow === undefined) {
                docEl.dataset.prevOverflow = docEl.style.overflow || '';
            }
            document.body.style.overflow = 'hidden';
            docEl.style.overflow = 'hidden';
        }

        function unlockBodyScroll() {
            if (document.body.dataset.prevOverflow !== undefined) {
                document.body.style.overflow = document.body.dataset.prevOverflow;
                delete document.body.dataset.prevOverflow;
            } else {
                document.body.style.removeProperty('overflow');
            }

            if (docEl.dataset.prevOverflow !== undefined) {
                docEl.style.overflow = docEl.dataset.prevOverflow;
                delete docEl.dataset.prevOverflow;
            } else {
                docEl.style.removeProperty('overflow');
            }
        }

        function openModal(dialog) {
            if (dialog && typeof dialog.showModal === 'function') {
                dialog.showModal();
                lockBodyScroll();
            }
        }

        function handleLinkActivation(linkElement, dialog) {
            if (!linkElement) return;
            linkElement.addEventListener('click', (event) => {
                event.preventDefault();
                if (dialog === privacyModal) {
                    resetPrivacyModalState();
                }
                openModal(dialog);
            });
            linkElement.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    if (dialog === privacyModal) {
                        resetPrivacyModalState();
                    }
                    openModal(dialog);
                }
            });
        }

        privacyLinks.forEach(link => handleLinkActivation(link, privacyModal));

        if (privacyModal) {
            privacyModal.addEventListener('close', unlockBodyScroll);
            privacyModal.addEventListener('cancel', () => {
                unlockBodyScroll();
            });
        }

        function checkPrivacyScrollPosition() {
            if (!privacyScrollArea || !privacyModalCheckbox) return;
            const { scrollTop, scrollHeight, clientHeight } = privacyScrollArea;
            const atBottom = Math.ceil(scrollTop + clientHeight) >= scrollHeight - 8;
            if (atBottom) {
                privacyModalCheckbox.disabled = false;
            } else if (!privacyModalCheckbox.checked) {
                privacyModalCheckbox.disabled = true;
            }
        }

        if (privacyScrollArea) {
            privacyScrollArea.addEventListener('scroll', checkPrivacyScrollPosition, { passive: true });
        }

        if (privacyModalCheckbox) {
            privacyModalCheckbox.addEventListener('change', () => {
                if (privacyModalAgree) {
                    privacyModalAgree.disabled = !privacyModalCheckbox.checked;
                }
            });
        }

        if (privacyModalAgree) {
            privacyModalAgree.addEventListener('click', (event) => {
                event.preventDefault();
                revealInlineConsent();
                if (privacyModal && typeof privacyModal.close === 'function') {
                    privacyModal.close();
                }
            });
        }

        // Visitor Registration Form Handling
        document.getElementById('visitorRegistrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            if (!privacyAccepted || !agreeCheckbox || !agreeCheckbox.checked) {
                showNotification('Please review and accept the Data Privacy Policy before registering.', 'error');
                return;
            }

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i>Registering...';
            submitBtn.disabled = true;
            
            // Collect form data
            const formData = new FormData(form);
            // Ensure CSRF token is present in payload
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrf && !formData.get('_token')) {
                formData.append('_token', csrf);
            }
            
            // Add additional fields that might be needed
            const visitType = formData.get('visit_type');
            const arrivalDate = formData.get('arrival_date');
            const arrivalTime = formData.get('arrival_time');
            const scheduledDate = formData.get('scheduled_date');
            
            if (visitType === 'preschedule') {
                // For pre-scheduled visits
                if (scheduledDate && arrivalTime) {
                    formData.append('scheduled_date', scheduledDate);
                    formData.append('scheduled_time', arrivalTime);
                    formData.append('status', 'scheduled');
                }
            } else {
                // For immediate visits
                if (arrivalDate && arrivalTime) {
                    formData.append('time_in', `${arrivalDate} ${arrivalTime}`);
                }
            }
            
            // Submit form via AJAX to public endpoint
            fetch('{{ route("visitor.public_store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                    // Note: Do NOT set Content-Type when sending FormData with files
                    // Browser will automatically set it to multipart/form-data with boundary
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
                if (response.ok && data && data.success) {
                    // Success: stay on landing page only; do not open admin tab
                    if (visitType === 'preschedule') {
                        showNotification('Visitor pre-scheduled successfully! You will receive an email with your QR pass and code for the scheduled date.', 'success');
                    } else {
                        showNotification('Visitor registered! Entry added to New Visitors queue.', 'success');
                    }
                    form.reset();
                    if (agreeCheckbox) {
                        agreeCheckbox.disabled = false;
                        agreeCheckbox.checked = true;
                    }
                    // Clear file preview
                    if (idFilePreview) {
                        idFilePreview.classList.add('hidden');
                    }
                    if (idFileName) {
                        idFileName.textContent = '';
                    }
                    updateRegisterButtonState();
                    return;
                }
                const message = (data && (data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : ''))) || 'Error registering visitor. Please try again.';
                showNotification(message, 'error');
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
        });
        
        // Notification function
        // Use global showNotification with progress bar
        if (typeof window.showNotification === 'undefined' || window.showNotification.toString().indexOf('progressBar') === -1) {
          window.showNotification = function(message, type = 'info', duration = 3000) {
            if (!document.getElementById('notification-progress-style')) {
              const style = document.createElement('style');
              style.id = 'notification-progress-style';
              style.textContent = `
                @keyframes progressBar {
                  from { width: 100%; }
                  to { width: 0%; }
                }
                @keyframes slideInRight {
                  from { transform: translateX(100%); opacity: 0; }
                  to { transform: translateX(0); opacity: 1; }
                }
              `;
              document.head.appendChild(style);
            }

            const notification = document.createElement('div');
            const alertType = type === 'error' ? 'error' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info';
            notification.className = `alert alert-${alertType} fixed bottom-4 right-4 z-[9999] max-w-sm shadow-lg relative overflow-hidden`;
            notification.style.cssText = 'position: fixed; bottom: 1rem; right: 1rem; z-index: 9999; max-width: 24rem; animation: slideInRight 0.3s ease-out;';
            
            const iconMap = { 'success': 'check-circle', 'error': 'alert-circle', 'warning': 'alert-triangle', 'info': 'info' };
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
            
            document.body.appendChild(notification);
            notification.offsetHeight;
            
            if (window.lucide && window.lucide.createIcons) {
              window.lucide.createIcons();
            }
            
            setTimeout(() => {
              notification.style.opacity = '0';
              notification.style.transition = 'opacity 0.3s ease-out';
              setTimeout(() => {
                if (notification.parentNode) notification.remove();
              }, 300);
            }, duration);
          };
        }

    </script>
</body>
</html>
