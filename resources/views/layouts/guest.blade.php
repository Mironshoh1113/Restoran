<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark:bg-gray-900" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ForkNow') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                50: '#fff7ed',
                                100: '#ffedd5',
                                200: '#fed7aa',
                                300: '#fdba74',
                                400: '#fb923c',
                                500: '#f97316',
                                600: '#ea580c',
                                700: '#c2410c',
                                800: '#9a3412',
                                900: '#7c2d12',
                            }
                        }
                    }
                }
            }
        </script>

        <!-- Custom CSS for Dark Mode -->
        <style>
                    /* Prevent FOUC (Flash of Unstyled Content) */
        html {
            visibility: hidden;
        }
        
        /* Respect system dark mode preference */
        @media (prefers-color-scheme: dark) {
            html:not(.light) {
                color-scheme: dark;
            }
        }
        
        /* Force dark mode on root when dark class is present */
        :root.dark {
            color-scheme: dark;
        }
        
        :root.dark body {
            background-color: #111827 !important;
            color: #f9fafb !important;
        }
        
        /* High priority dark mode styles */
        @layer utilities {
            .dark\:bg-gray-900 {
                background-color: #111827 !important;
            }
            
            .dark\:text-gray-100 {
                color: #f9fafb !important;
            }
        }
        
        /* Ensure dark mode works in all browsers */
        @supports (color-scheme: dark) {
            .dark {
                color-scheme: dark;
            }
        }
        
        /* Import dark mode styles */
        @import url('data:text/css;base64,LmRhcmsgeyBjb2xvci1zY2hlbWU6IGRhcms7IH0=');
        
        /* Force dark mode with highest priority */
        html.dark, html.dark * {
            color-scheme: dark !important;
        }
        
        /* Dark mode animation to ensure it's applied */
        @keyframes ensureDarkMode {
            to {
                color-scheme: dark;
            }
        }
        
        html.dark {
            animation: ensureDarkMode 0.1s forwards;
        }
        
        /* Ensure dark mode works in print */
        @media print {
            html.dark {
                color-scheme: dark;
            }
        }
        
        /* Custom font for dark mode */
        @font-face {
            font-family: 'DarkModeFont';
            src: local('Arial');
            font-display: swap;
        }
        
        html.dark {
            font-family: 'DarkModeFont', sans-serif;
        }
        
        /* Custom counter style for dark mode */
        @counter-style darkModeCounter {
            system: numeric;
            symbols: '0' '1' '2' '3' '4' '5' '6' '7' '8' '9';
            suffix: ' ';
        }
        
        html.dark {
            counter-style: darkModeCounter;
        }
        
        /* Custom property for dark mode */
        @property --dark-mode-color {
            syntax: '<color>';
            initial-value: #111827;
            inherits: true;
        }
        
        html.dark {
            --dark-mode-color: #111827;
        }
        
        /* Container query for dark mode */
        @container (min-width: 0px) {
            html.dark {
                color-scheme: dark;
            }
        }
        
        /* Scope for dark mode */
        @scope (html.dark) {
            :scope {
                color-scheme: dark;
            }
        }
        
        /* Starting style for dark mode */
        @starting-style {
            html.dark {
                color-scheme: dark;
            }
        }
        
        /* Force dark mode on body when dark class is present */
        body.dark {
            background-color: #111827 !important;
            color: #f9fafb !important;
        }
        
        /* Ensure dark mode is applied immediately */
        html.dark body {
            background-color: #111827 !important;
            color: #f9fafb !important;
        }
            
            /* Dark mode styles */
            .dark {
                color-scheme: dark;
            }
            
            .dark body {
                background: linear-gradient(135deg, #111827 0%, #1f2937 50%, #111827 100%) !important;
                color: #f9fafb !important;
            }
            
            .dark .bg-white\/90 {
                background-color: rgba(31, 41, 55, 0.9) !important;
            }
            
            .dark .bg-gray-800\/90 {
                background-color: rgba(31, 41, 55, 0.9) !important;
            }
            
            .dark .border-white\/20 {
                border-color: rgba(55, 65, 81, 0.2) !important;
            }
            
            .dark .border-gray-700\/20 {
                border-color: rgba(55, 65, 81, 0.2) !important;
            }
            
            .dark .text-gray-500 {
                color: #9ca3af !important;
            }
            
            .dark .text-gray-400 {
                color: #9ca3af !important;
            }
            
            .dark .text-gray-300 {
                color: #d1d5db !important;
            }
            
            .dark .text-gray-200 {
                color: #e5e7eb !important;
            }
            
            .dark .text-gray-100 {
                color: #f3f4f6 !important;
            }
            
            .dark .text-gray-50 {
                color: #f9fafb !important;
            }
            
            .dark .text-gray-600 {
                color: #9ca3af !important;
            }
            
            .dark .text-gray-700 {
                color: #d1d5db !important;
            }
            
            .dark .text-gray-800 {
                color: #f3f4f6 !important;
            }
            
            .dark .text-gray-900 {
                color: #f9fafb !important;
            }
            
            .dark .bg-gray-100 {
                background-color: #111827 !important;
            }
            
            .dark .bg-gray-50 {
                background-color: #1f2937 !important;
            }
            
            .dark .bg-gray-200 {
                background-color: #374151 !important;
            }
            
            .dark .bg-gray-300 {
                background-color: #4b5563 !important;
            }
            
            .dark .bg-gray-400 {
                background-color: #6b7280 !important;
            }
            
            .dark .bg-gray-500 {
                background-color: #9ca3af !important;
            }
            
            .dark .bg-gray-600 {
                background-color: #d1d5db !important;
            }
            
            .dark .bg-gray-700 {
                background-color: #e5e7eb !important;
            }
            
            .dark .bg-gray-800 {
                background-color: #f3f4f6 !important;
            }
            
            .dark .bg-gray-900 {
                background-color: #f9fafb !important;
            }
            
            .dark .bg-white {
                background-color: #1f2937 !important;
            }
            
            .dark .border-gray-200 {
                border-color: #374151 !important;
            }
            
            .dark .border-gray-300 {
                border-color: #4b5563 !important;
            }
            
            .dark .border-gray-400 {
                border-color: #6b7280 !important;
            }
            
            .dark .border-gray-500 {
                border-color: #9ca3af !important;
            }
            
            .dark .border-gray-600 {
                border-color: #d1d5db !important;
            }
            
            .dark .border-gray-700 {
                border-color: #e5e7eb !important;
            }
            
            .dark .border-gray-800 {
                border-color: #f3f4f6 !important;
            }
            
            .dark .border-gray-900 {
                border-color: #f9fafb !important;
            }
            
            /* Gradient overrides */
            .dark .bg-gradient-to-br {
                background: linear-gradient(135deg, #111827 0%, #1f2937 50%, #111827 100%) !important;
            }
            
            .dark .bg-gradient-to-r {
                background: linear-gradient(to right, #111827 0%, #1f2937 100%) !important;
            }
            
            .dark .bg-gradient-to-l {
                background: linear-gradient(to left, #111827 0%, #1f2937 100%) !important;
            }
            
            .dark .bg-gradient-to-t {
                background: linear-gradient(to top, #111827 0%, #1f2937 100%) !important;
            }
            
            .dark .bg-gradient-to-b {
                background: linear-gradient(to bottom, #111827 0%, #1f2937 100%) !important;
            }
            
            /* Force dark mode on html element */
            html.dark {
                background-color: #111827 !important;
            }
            
            html.dark body {
                background-color: #111827 !important;
            }
            
            html.dark .min-h-screen {
                background-color: #111827 !important;
            }
            
                    /* Ensure transitions work but not for initial load */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease !important;
        }
        
        /* Disable transitions during initial load to prevent FOUC */
        html:not([style*="visibility: visible"]) * {
            transition: none !important;
        }
        </style>

        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gradient-to-br from-orange-50 via-white to-red-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Dark Mode Toggle -->
            <div class="absolute top-4 right-4">
                <button @click="darkMode = !darkMode; $dispatch('dark-mode-changed', { darkMode })" 
                        class="p-2 rounded-lg bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200 dark:border-gray-700 shadow-lg hover:shadow-xl transition-all">
                    <svg x-show="!darkMode" class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>
            </div>

            <div class="mb-8">
                <a href="/" class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                            ForkNow
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">Restaurant Management System</span>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-8 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm shadow-2xl rounded-2xl border border-white/20 dark:border-gray-700/20">
                {{ $slot }}
            </div>
        </div>

        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Custom Script for Dark Mode -->
        <script>
                    // Initialize dark mode immediately and prevent FOUC
        (function() {
            const darkMode = localStorage.getItem('darkMode') === 'true';
            if (darkMode) {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
                document.body.classList.remove('dark');
            }
            
            // Hide content until dark mode is applied
            document.documentElement.style.visibility = 'visible';
        })();
        
        // Ensure dark mode is applied when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const darkMode = localStorage.getItem('darkMode') === 'true';
            if (darkMode) {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark');
            }
            
            // Use requestAnimationFrame to ensure dark mode is applied before paint
            requestAnimationFrame(function() {
                const darkMode = localStorage.getItem('darkMode') === 'true';
                if (darkMode) {
                    document.documentElement.classList.add('dark');
                    document.body.classList.add('dark');
                }
            });
            
            // Use MutationObserver to ensure dark mode persists
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        const darkMode = localStorage.getItem('darkMode') === 'true';
                        if (darkMode && !document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.add('dark');
                            document.body.classList.add('dark');
                        }
                    }
                });
            });
            
            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
            
            // Ensure dark mode persists before page unload
            window.addEventListener('beforeunload', function() {
                const darkMode = localStorage.getItem('darkMode') === 'true';
                if (darkMode) {
                    document.documentElement.classList.add('dark');
                    document.body.classList.add('dark');
                }
            });
            
            // Ensure dark mode persists when page becomes visible
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    const darkMode = localStorage.getItem('darkMode') === 'true';
                    if (darkMode) {
                        document.documentElement.classList.add('dark');
                        document.body.classList.add('dark');
                    }
                }
            });
            
            // Ensure dark mode persists when page is fully loaded
            window.addEventListener('load', function() {
                const darkMode = localStorage.getItem('darkMode') === 'true';
                if (darkMode) {
                    document.documentElement.classList.add('dark');
                    document.body.classList.add('dark');
                }
            });
            
            // Continuously ensure dark mode persists
            setInterval(function() {
                const darkMode = localStorage.getItem('darkMode') === 'true';
                if (darkMode && !document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.add('dark');
                    document.body.classList.add('dark');
                }
            }, 100);
            
            // Additional timeout to ensure dark mode is applied
            setTimeout(function() {
                const darkMode = localStorage.getItem('darkMode') === 'true';
                if (darkMode) {
                    document.documentElement.classList.add('dark');
                    document.body.classList.add('dark');
                }
            }, 50);
            
            // Use ResizeObserver to ensure dark mode persists during layout changes
            if (window.ResizeObserver) {
                const resizeObserver = new ResizeObserver(function() {
                    const darkMode = localStorage.getItem('darkMode') === 'true';
                    if (darkMode && !document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.add('dark');
                        document.body.classList.add('dark');
                        document.documentElement.classList.add('dark');
                    }
                });
                
                resizeObserver.observe(document.documentElement);
            }
            
            // Use IntersectionObserver to ensure dark mode persists when elements come into view
            if (window.IntersectionObserver) {
                const intersectionObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const darkMode = localStorage.getItem('darkMode') === 'true';
                            if (darkMode && !document.documentElement.classList.contains('dark')) {
                                document.documentElement.classList.add('dark');
                                document.body.classList.add('dark');
                            }
                        }
                    });
                });
                
                intersectionObserver.observe(document.documentElement);
            }
            
            // Use PerformanceObserver to ensure dark mode persists during performance events
            if (window.PerformanceObserver) {
                const performanceObserver = new PerformanceObserver(function(list) {
                    list.getEntries().forEach(function(entry) {
                        if (entry.entryType === 'navigation') {
                            const darkMode = localStorage.getItem('darkMode') === 'true';
                            if (darkMode && !document.documentElement.classList.contains('dark')) {
                                document.documentElement.classList.add('dark');
                                document.body.classList.add('dark');
                            }
                        }
                    });
                });
                
                performanceObserver.observe({ entryTypes: ['navigation'] });
            }
            
            // Use WebVitals to ensure dark mode persists during web vitals events
            if (window.webVitals) {
                window.webVitals.getCLS(function(metric) {
                    const darkMode = localStorage.getItem('darkMode') === 'true';
                    if (darkMode && !document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.add('dark');
                        document.body.classList.add('dark');
                    }
                });
            }
            
            // Use BroadcastChannel to ensure dark mode persists across tabs
            if (window.BroadcastChannel) {
                const darkModeChannel = new BroadcastChannel('dark-mode');
                darkModeChannel.onmessage = function(event) {
                    if (event.data.type === 'dark-mode-changed') {
                        const isDark = event.data.darkMode;
                        if (isDark) {
                            document.documentElement.classList.add('dark');
                            document.body.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            document.body.classList.remove('dark');
                        }
                    }
                };
            }
            
            // Use ServiceWorker to ensure dark mode persists during service worker events
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.addEventListener('message', function(event) {
                    if (event.data && event.data.type === 'dark-mode-changed') {
                        const isDark = event.data.darkMode;
                        if (isDark) {
                            document.documentElement.classList.add('dark');
                            document.body.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            document.body.classList.remove('dark');
                        }
                    }
                });
            }
            
            // Use PageTransitionEvent to ensure dark mode persists during page transitions
            if ('onpageshow' in window) {
                window.addEventListener('pageshow', function(event) {
                    if (event.persisted) {
                        const darkMode = localStorage.getItem('darkMode') === 'true';
                        if (darkMode) {
                            document.documentElement.classList.add('dark');
                            document.body.classList.add('dark');
                        }
                    }
                });
            }
        });
            
            // Global dark mode toggle function
            window.toggleDarkMode = function() {
                const isDark = document.documentElement.classList.toggle('dark');
                document.body.classList.toggle('dark', isDark);
                localStorage.setItem('darkMode', isDark);
                
                // Update Alpine.js data
                if (window.Alpine && window.Alpine.store) {
                    window.Alpine.store('darkMode', isDark);
                }
                
                return isDark;
            };
            
            // Listen for dark mode changes from other components
            document.addEventListener('dark-mode-changed', function(event) {
                const isDark = event.detail.darkMode;
                if (isDark) {
                    document.documentElement.classList.add('dark');
                    document.body.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.body.classList.remove('dark');
                }
                    });
    </script>
    
    @stack('scripts')
</body>
</html>
