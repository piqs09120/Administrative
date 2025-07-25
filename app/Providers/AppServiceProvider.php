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
            <div id="appearance-toggle" class="fixed top-4 right-4 z-50">
                <button id="darkModeToggle" class="p-2 rounded-full bg-blue-700 text-white shadow hover:bg-blue-800 transition">
                    <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13.66l-.71.71M4.05 19.07l-.71.71M21 12h-1M4 12H3m16.66 5.66l-.71-.71M4.05 4.93l-.71-.71M12 8a4 4 0 100 8 4 4 0 000-8z" /></svg>
                    <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" /></svg>
                </button>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggle = document.getElementById('darkModeToggle');
                const sunIcon = document.getElementById('sunIcon');
                const moonIcon = document.getElementById('moonIcon');
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