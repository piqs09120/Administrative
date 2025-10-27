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
        
        .file-upload-container {
            position: relative;
        }
        
        .file-upload-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
            background-color: #f9fafb;
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 120px;
        }
        
        .file-upload-label:hover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        
        .upload-icon {
            font-size: 2rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        
        .upload-text {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.25rem;
        }
        
        .upload-hint {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .file-preview {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background-color: #f3f4f6;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .file-info {
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        
        .file-info span:first-child {
            font-weight: 600;
            color: #374151;
        }
        
        .file-info span:last-child {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .remove-file-btn {
            background-color: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .remove-file-btn:hover {
            background-color: #dc2626;
        }
        
        .validation-indicator {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .validation-indicator.valid {
            background-color: #10b981;
            color: white;
        }
        
        .validation-indicator.invalid {
            background-color: #ef4444;
            color: white;
        }
        
        .validation-indicator.pending {
            background-color: #f59e0b;
            color: white;
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
                                </div>
                                
                                <!-- ID/Passport Number -->
                                <div class="space-y-2">
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

                                <!-- ID Document Upload -->
                                <div class="space-y-2">
                                    <label for="id_document" class="block text-sm font-semibold text-gray-700">
                                        ID Document Upload <span class="text-red-500">*</span>
                                    </label>
                                    <div class="file-upload-container">
                                        <input type="file" name="id_document" id="id_document" 
                                               class="file-upload-input" accept="image/*,.pdf" required>
                                        <label for="id_document" class="file-upload-label">
                                            <i class="fas fa-upload upload-icon"></i>
                                            <span class="upload-text" id="upload_text">Upload ID Document</span>
                                            <span class="upload-hint" id="upload_hint">Supported: JPG, PNG, PDF (Max 5MB)</span>
                                        </label>
                                        <div class="file-preview" id="id_document_preview" style="display: none;">
                                            <img id="preview_image" src="" alt="ID Document Preview" style="max-width: 200px; max-height: 150px;">
                                            <div class="file-info">
                                                <span id="file_name"></span>
                                                <span id="file_size"></span>
                                            </div>
                                            <button type="button" onclick="removeFilePreview()" class="remove-file-btn">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- ID Type Specific Instructions -->
                                    <div id="id_instructions" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                                        <div class="flex items-start">
                                            <i class="fas fa-info-circle text-blue-600 mr-2 mt-1"></i>
                                            <div>
                                                <p class="text-sm text-blue-800 font-medium" id="id_instruction_title">Please upload the correct ID document</p>
                                                <p class="text-xs text-blue-700 mt-1" id="id_instruction_text">Make sure the document is clear and readable</p>
                                            </div>
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
                                    class="bg-gradient-to-r from-[#F7A923] to-[#E6940F] hover:from-[#E6940F] hover:to-[#D2840E] text-[#2C3E50] px-12 py-4 rounded-lg text-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center mx-auto">
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
                    // Success: stay on landing page only; do not open admin tab
                    if (visitType === 'preschedule') {
                        showNotification('Visitor pre-scheduled successfully! You will receive an email with your QR pass and code for the scheduled date.', 'success');
                    } else {
                        showNotification('Visitor registered! Entry added to New Visitors queue.', 'success');
                    }
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

        // ID Document Upload Functions
        function removeFilePreview() {
            const fileInput = document.getElementById('id_document');
            const preview = document.getElementById('id_document_preview');
            
            fileInput.value = '';
            preview.style.display = 'none';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // ID Type Instructions Mapping
        const idTypeInstructions = {
            'philnational_id': {
                title: 'Philippine National ID (PhilSys)',
                text: 'Upload a clear photo of your Philippine National ID card. Make sure all text is readable.'
            },
            'passport': {
                title: 'Philippine Passport',
                text: 'Upload a clear photo of your passport information page. Ensure passport number and personal details are visible.'
            },
            'drivers_license': {
                title: 'Driver\'s License',
                text: 'Upload a clear photo of your driver\'s license. Both front and back sides are acceptable.'
            },
            'umid': {
                title: 'Unified Multipurpose ID (UMID)',
                text: 'Upload a clear photo of your UMID card. Make sure the ID number and personal information are visible.'
            },
            'postal_id': {
                title: 'Postal ID',
                text: 'Upload a clear photo of your Postal ID. Ensure the ID number and address are readable.'
            },
            'voters_id': {
                title: 'Voter\'s ID',
                text: 'Upload a clear photo of your Voter\'s ID. Make sure the voter\'s number is visible.'
            },
            'sss_id': {
                title: 'SSS ID',
                text: 'Upload a clear photo of your SSS ID card. Ensure the SSS number is readable.'
            },
            'gsis_id': {
                title: 'GSIS ID',
                text: 'Upload a clear photo of your GSIS ID card. Make sure the GSIS number is visible.'
            },
            'tin_id': {
                title: 'TIN ID',
                text: 'Upload a clear photo of your TIN ID card. Ensure the TIN number is readable.'
            },
            'prc_id': {
                title: 'Professional Regulation Commission (PRC)',
                text: 'Upload a clear photo of your PRC ID. Make sure the PRC number and profession are visible.'
            },
            'barangay_id': {
                title: 'Barangay ID',
                text: 'Upload a clear photo of your Barangay ID. Ensure the barangay name and ID number are readable.'
            },
            'senior_citizen_id': {
                title: 'Senior Citizen ID',
                text: 'Upload a clear photo of your Senior Citizen ID. Make sure the ID number is visible.'
            },
            'pwd_id': {
                title: 'PWD ID',
                text: 'Upload a clear photo of your PWD ID. Ensure the ID number and disability type are readable.'
            },
            'company_id': {
                title: 'Company ID',
                text: 'Upload a clear photo of your Company ID. Make sure the company name and employee details are visible.'
            },
            'school_id': {
                title: 'School ID',
                text: 'Upload a clear photo of your School ID. Ensure the school name and student information are readable.'
            },
            'other_id': {
                title: 'Other Valid ID',
                text: 'Upload a clear photo of your valid ID document. Make sure all important information is visible.'
            }
        };

        // ID Type Change Handler
        function handleIdTypeChange() {
            const idTypeSelect = document.getElementById('id_type');
            const instructionsDiv = document.getElementById('id_instructions');
            const instructionTitle = document.getElementById('id_instruction_title');
            const instructionText = document.getElementById('id_instruction_text');
            const uploadText = document.getElementById('upload_text');
            const fileInput = document.getElementById('id_document');
            
            if (idTypeSelect.value && idTypeInstructions[idTypeSelect.value]) {
                const instruction = idTypeInstructions[idTypeSelect.value];
                
                // Show instructions
                instructionTitle.textContent = instruction.title;
                instructionText.textContent = instruction.text;
                instructionsDiv.classList.remove('hidden');
                
                // Update upload text
                uploadText.textContent = `Upload ${instruction.title}`;
                
                // Clear any existing file
                fileInput.value = '';
                removeFilePreview();
                
                // Set file input accept attribute based on ID type
                fileInput.setAttribute('accept', 'image/*,.pdf');
                
            } else {
                // Hide instructions if no ID type selected
                instructionsDiv.classList.add('hidden');
                uploadText.textContent = 'Upload ID Document';
            }
        }

        // Enhanced File Upload Handler with AI Validation
        function handleFileUpload(event) {
            const file = event.target.files[0];
            const idTypeSelect = document.getElementById('id_type');
            const idNumberInput = document.getElementById('id_number');
            
            if (!file) return;

            // Check if ID type is selected
            if (!idTypeSelect.value) {
                alert('Please select an ID type first before uploading a document.');
                event.target.value = '';
                return;
            }

            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                event.target.value = '';
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please upload a valid image (JPG, PNG) or PDF file');
                event.target.value = '';
                return;
            }

            // Get selected ID type instruction
            const selectedIdType = idTypeSelect.value;
            const instruction = idTypeInstructions[selectedIdType];
            
            // Show AI validation loading message
            showNotification('Analyzing document with AI... Please wait.', 'info');
            
            // Show preview immediately
            const preview = document.getElementById('id_document_preview');
            const previewImage = document.getElementById('preview_image');
            const fileName = document.getElementById('file_name');
            const fileSize = document.getElementById('file_size');

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewImage.style.display = 'none';
            }

            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            preview.style.display = 'flex';
            
            // Perform AI validation via AJAX
            validateIdDocumentWithAI(file, selectedIdType, idNumberInput.value);
        }

        // AI Validation Function
        function validateIdDocumentWithAI(file, idType, idNumber) {
            const formData = new FormData();
            formData.append('id_document', file);
            formData.append('id_type', idType);
            formData.append('id_number', idNumber);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('/api/validate-id-document', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.validation.is_valid) {
                        showNotification(`✅ Document validated! This appears to be a valid ${data.validation.id_type_name} (Score: ${data.validation.score.toFixed(2)}, Confidence: ${data.validation.confidence.toFixed(1)}%)`, 'success');
                        
                        // Add validation indicator to the preview
                        const preview = document.getElementById('id_document_preview');
                        const validationIndicator = document.createElement('div');
                        validationIndicator.className = 'validation-indicator valid';
                        validationIndicator.innerHTML = '<i class="fas fa-check-circle"></i> Validated';
                        preview.appendChild(validationIndicator);
                        
                    } else {
                        let statusMessage = '';
                        if (data.validation.status === 'review') {
                            statusMessage = '⚠️ Document requires manual review. ';
                        } else {
                            statusMessage = '❌ Document validation failed! ';
                        }
                        
                        showNotification(`${statusMessage}This does not appear to be a valid ${data.validation.id_type_name}. (Score: ${data.validation.score.toFixed(2)}, Confidence: ${data.validation.confidence.toFixed(1)}%)`, 'error');
                        
                        // Clear the file input
                        document.getElementById('id_document').value = '';
                        removeFilePreview();
                        
                        // Show detailed validation message
                        if (data.validation.reasons && data.validation.reasons.length > 0) {
                            let errorMessage = 'Validation failed:\n';
                            errorMessage += data.validation.reasons.join('\n');
                            
                            if (data.validation.predicted_id_type && data.validation.predicted_id_type !== idType) {
                                errorMessage += `\n\nDetected ID type: ${data.validation.predicted_id_type}`;
                                errorMessage += `\nSelected ID type: ${idType}`;
                                errorMessage += '\n\nPlease select the correct ID type or upload the correct document.';
                            }
                            
                            alert(errorMessage);
                        }
                    }
                } else {
                    showNotification('⚠️ Validation service temporarily unavailable. Document uploaded but not validated.', 'warning');
                }
            })
            .catch(error => {
                console.error('Validation error:', error);
                showNotification('⚠️ Validation service temporarily unavailable. Document uploaded but not validated.', 'warning');
            });
        }

        // Initialize file upload event listener
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('id_document');
            const idTypeSelect = document.getElementById('id_type');
            
            if (fileInput) {
                fileInput.addEventListener('change', handleFileUpload);
            }
            
            if (idTypeSelect) {
                idTypeSelect.addEventListener('change', handleIdTypeChange);
            }
        });
    </script>
</body>
</html>
