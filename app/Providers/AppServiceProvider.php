<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('fluxAppearance', function () {
            return <<<'HTML'
            <!-- Navbar Header -->
            <div class="bg-white border-b border-gray-200 px-6 py-3">
                <div class="flex items-center justify-between">
                    <!-- Left Side - Search Bar -->
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" placeholder="Search..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <!-- Right Side - Date/Time, Moon, Profile -->
                    <div class="flex items-center space-x-4">
                        <!-- Date and Time Display -->
                        <div class="flex items-center space-x-2 bg-gray-100 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span id="currentDate" class="text-sm font-medium text-gray-700"></span>
                            <div class="w-px h-4 bg-gray-300"></div>
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="currentTime" class="text-sm font-medium text-gray-700"></span>
                        </div>
                        
                        <!-- Moon Icon (Dark Mode Toggle) -->
                        <button id="darkModeToggle" class="p-2 rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition-colors">
                            <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13.66l-.71.71M4.05 19.07l-.71.71M21 12h-1M4 12H3m16.66 5.66l-.71-.71M4.05 4.93l-.71-.71M12 8a4 4 0 100 8 4 4 0 000-8z" />
                            </svg>
                            <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="white" viewBox="0 0 24 24" stroke="white">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
                            </svg>
                        </button>
                        
                        <!-- Profile Icon -->
                        <button class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggle = document.getElementById('darkModeToggle');
                const sunIcon = document.getElementById('sunIcon');
                const moonIcon = document.getElementById('moonIcon');
                const currentDate = document.getElementById('currentDate');
                const currentTime = document.getElementById('currentTime');
                
                // Update date and time
                function updateDateTime() {
                    const now = new Date();
                    
                    // Format date (e.g., "Mon, Aug 4")
                    const dateOptions = { weekday: 'short', month: 'short', day: 'numeric' };
                    currentDate.textContent = now.toLocaleDateString('en-US', dateOptions);
                    
                    // Format time (e.g., "12:30 AM")
                    const timeOptions = { hour: 'numeric', minute: '2-digit', hour12: true };
                    currentTime.textContent = now.toLocaleTimeString('en-US', timeOptions);
                }
                
                // Update date and time every second
                updateDateTime();
                setInterval(updateDateTime, 1000);
                
                function updateIcons() {
                    if(document.documentElement.classList.contains('dark')) {
                        sunIcon.classList.remove('hidden');
                        moonIcon.classList.add('hidden');
                    } else {
                        sunIcon.classList.add('hidden');
                        moonIcon.classList.remove('hidden');
                    }
                }
                
                function setDarkMode(enabled) {
                    if(enabled) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    }
                    updateIcons();
                }
                
                // Initial state
                if(localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    setDarkMode(true);
                } else {
                    setDarkMode(false);
                }
                
                toggle.addEventListener('click', function() {
                    setDarkMode(!document.documentElement.classList.contains('dark'));
                });
            });
            </script>
            HTML;
        });
    }
} 