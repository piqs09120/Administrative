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
                    <h1 class="text-2xl font-bold text-white">
                        <span class="gradient-text">SOLIERA</span>
                    </h1>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#features" class="text-white hover:text-yellow-400 transition-colors">Features</a>
                    <a href="#register" class="text-white hover:text-yellow-400 transition-colors">Register Visitor</a>
                    <a href="#benefits" class="text-white hover:text-yellow-400 transition-colors">Benefits</a>
                    <a href="#contact" class="text-white hover:text-yellow-400 transition-colors">Contact</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-white hover:text-yellow-400 transition-colors">Login</a>
                    <a href="{{ route('visitor.index') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Get Started
                    </a>
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
                                    <div class="relative">
                                        <i class="fas fa-id-card absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <select id="id_type" name="id_type" required
                                                class="form-input w-full pl-10 pr-10 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50 appearance-none">
                                            <option value="">Select ID Type</option>
                                            <option value="Passport">Passport</option>
                                            <option value="Driver's License">Driver's License</option>
                                            <option value="National ID">National ID</option>
                                            <option value="Company ID">Company ID</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                    </div>
                                </div>
                                
                                <!-- ID/Passport Number -->
                                <div class="space-y-2 md:col-span-2">
                                    <label for="id_number" class="block text-sm font-semibold text-gray-700">
                                        ID/Passport Number <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-id-badge absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="text" id="id_number" name="id_number" required
                                               class="form-input w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50"
                                               placeholder="Enter ID or passport number">
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
                                
                                <!-- Arrival Date -->
                                <div class="space-y-2">
                                    <label for="arrival_date" class="block text-sm font-semibold text-gray-700">
                                        Arrival Date <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="date" id="arrival_date" name="arrival_date" required
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
                                
                                <!-- Company -->
                                <div class="space-y-2">
                                    <label for="company" class="block text-sm font-semibold text-gray-700">
                                        Company/Organization
                                    </label>
                                    <input type="text" id="company" name="company"
                                           class="form-input w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50"
                                           placeholder="Company or organization name">
                                </div>
                                
                                <!-- Vehicle Plate -->
                                <div class="space-y-2">
                                    <label for="vehicle_plate" class="block text-sm font-semibold text-gray-700">
                                        Vehicle Plate Number
                                    </label>
                                    <input type="text" id="vehicle_plate" name="vehicle_plate"
                                           class="form-input w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50"
                                           placeholder="Vehicle plate number (if applicable)">
                                </div>
                                
                                <!-- Special Requirements -->
                                <div class="space-y-2 md:col-span-2">
                                    <label for="special_requirements" class="block text-sm font-semibold text-gray-700">
                                        Special Requirements
                                    </label>
                                    <textarea id="special_requirements" name="special_requirements" rows="3"
                                              class="form-input w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-yellow-50 resize-none"
                                              placeholder="Any special accommodation needs, accessibility requirements, etc."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="text-center pt-6">
                            <button type="submit" 
                                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-12 py-4 rounded-lg text-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center mx-auto">
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
        
        // Visitor Registration Form Handling
        document.getElementById('visitorRegistrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
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
            const arrivalDate = formData.get('arrival_date');
            const arrivalTime = formData.get('arrival_time');
            if (arrivalDate && arrivalTime) {
                formData.append('time_in', `${arrivalDate} ${arrivalTime}`);
            }
            if (formData.get('special_requirements')) {
                formData.append('special_instructions', formData.get('special_requirements'));
            }
            
            // Submit form via AJAX to public endpoint
            fetch('{{ route("visitor.public_store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                // Send cookies for the session so CSRF validation passes
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
                    // Keep user on landing page, show success with link to New Visitors
                    showNotification('Visitor registered! Open New Visitors to review.', 'success');
                    // Optional: provide quick link
                    setTimeout(() => {
                        const a = document.createElement('a');
                        a.href = data.redirect || '{{ route('visitor.create') }}';
                        a.target = '_blank';
                        a.rel = 'noopener noreferrer';
                        a.click();
                    }, 500);
                    form.reset();
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
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
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
    </script>
</body>
</html>
