@extends('layouts.app')

@section('title', 'Icon Responsive Test')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 text-center">Icon Responsive Test Page</h1>
    
    <!-- Test Section 1: Header Icons -->
    <div class="card bg-base-100 shadow-xl mb-8">
        <div class="card-body">
            <h2 class="card-title text-2xl mb-4">Header Icons Test</h2>
            <div class="flex flex-wrap gap-4 items-center">
                <button class="btn btn-primary">
                    <i data-lucide="home" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                    <span>Home</span>
                </button>
                <button class="btn btn-secondary">
                    <i data-lucide="settings" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                    <span>Settings</span>
                </button>
                <button class="btn btn-accent">
                    <i data-lucide="user" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                    <span>User</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Test Section 2: Card Icons -->
    <div class="card bg-base-100 shadow-xl mb-8">
        <div class="card-body">
            <h2 class="card-title text-2xl mb-4">Card Icons Test</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="card bg-primary text-primary-content">
                    <div class="card-body text-center">
                        <i data-lucide="folder" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer mx-auto mb-2"></i>
                        <h3 class="card-title justify-center">Documents</h3>
                    </div>
                </div>
                <div class="card bg-secondary text-secondary-content">
                    <div class="card-body text-center">
                        <i data-lucide="users" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer mx-auto mb-2"></i>
                        <h3 class="card-title justify-center">Users</h3>
                    </div>
                </div>
                <div class="card bg-accent text-accent-content">
                    <div class="card-body text-center">
                        <i data-lucide="calendar" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer mx-auto mb-2"></i>
                        <h3 class="card-title justify-center">Calendar</h3>
                    </div>
                </div>
                <div class="card bg-success text-success-content">
                    <div class="card-body text-center">
                        <i data-lucide="check-circle" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer mx-auto mb-2"></i>
                        <h3 class="card-title justify-center">Success</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Section 3: Different Icon Sizes -->
    <div class="card bg-base-100 shadow-xl mb-8">
        <div class="card-body">
            <h2 class="card-title text-2xl mb-4">Different Icon Sizes Test</h2>
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <span class="w-24">Extra Small:</span>
                    <i data-lucide="star" class="text-sm md:text-base lg:text-lg transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                </div>
                <div class="flex items-center gap-4">
                    <span class="w-24">Small:</span>
                    <i data-lucide="star" class="text-lg md:text-xl lg:text-2xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                </div>
                <div class="flex items-center gap-4">
                    <span class="w-24">Medium:</span>
                    <i data-lucide="star" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                </div>
                <div class="flex items-center gap-4">
                    <span class="w-24">Large:</span>
                    <i data-lucide="star" class="text-2xl md:text-3xl lg:text-4xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                </div>
                <div class="flex items-center gap-4">
                    <span class="w-24">Extra Large:</span>
                    <i data-lucide="star" class="text-3xl md:text-4xl lg:text-5xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                </div>
                <div class="flex items-center gap-4">
                    <span class="w-24">2X Large:</span>
                    <i data-lucide="star" class="text-4xl md:text-5xl lg:text-6xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Section 4: Hover Effects -->
    <div class="card bg-base-100 shadow-xl mb-8">
        <div class="card-body">
            <h2 class="card-title text-2xl mb-4">Hover Effects Test</h2>
            <div class="flex flex-wrap gap-4">
                <button class="btn btn-outline">
                    <i data-lucide="heart" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-red-500 cursor-pointer"></i>
                    <span>Heart</span>
                </button>
                <button class="btn btn-outline">
                    <i data-lucide="thumbs-up" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-green-500 cursor-pointer"></i>
                    <span>Like</span>
                </button>
                <button class="btn btn-outline">
                    <i data-lucide="bookmark" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-yellow-500 cursor-pointer"></i>
                    <span>Bookmark</span>
                </button>
                <button class="btn btn-outline">
                    <i data-lucide="share" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-blue-500 cursor-pointer"></i>
                    <span>Share</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Test Section 5: Responsive Breakpoint Test -->
    <div class="card bg-base-100 shadow-xl mb-8">
        <div class="card-body">
            <h2 class="card-title text-2xl mb-4">Responsive Breakpoint Test</h2>
            <p class="text-sm text-gray-600 mb-4">Resize your browser window to see how icons adapt to different screen sizes.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 border rounded-lg">
                    <h3 class="font-semibold mb-2">Mobile (< 768px)</h3>
                    <i data-lucide="smartphone" class="text-xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                    <p class="text-xs mt-2">text-xl</p>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <h3 class="font-semibold mb-2">Tablet (768px - 1024px)</h3>
                    <i data-lucide="tablet" class="text-2xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                    <p class="text-xs mt-2">text-2xl</p>
                </div>
                <div class="text-center p-4 border rounded-lg">
                    <h3 class="font-semibold mb-2">Desktop (> 1024px)</h3>
                    <i data-lucide="monitor" class="text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
                    <p class="text-xs mt-2">text-3xl</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="alert alert-info">
        <i data-lucide="info" class="text-xl md:text-2xl lg:text-3xl transition-all duration-300 ease-in-out hover:text-accent cursor-pointer"></i>
        <div>
            <h3 class="font-bold">Testing Instructions:</h3>
            <ul class="list-disc list-inside mt-2 space-y-1">
                <li>Resize your browser window to test responsive behavior</li>
                <li>Hover over icons to see transition effects</li>
                <li>Test on different devices (mobile, tablet, desktop)</li>
                <li>Check that all icons maintain proper alignment</li>
                <li>Verify cursor changes to pointer on hover</li>
            </ul>
        </div>
    </div>
</div>

<script>
// Initialize lucide icons
document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) {
        lucide.createIcons();
    }
});
</script>
@endsection
