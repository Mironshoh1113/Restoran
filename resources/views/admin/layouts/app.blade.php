<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark:bg-gray-900" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ForkNow') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.1/css/all.css" crossorigin="anonymous">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Tailwind Config -->
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
                    },
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                        'bounce-in': 'bounceIn 0.6s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' }
                        },
                        bounceIn: {
                            '0%': { transform: 'scale(0.3)', opacity: '0' },
                            '50%': { transform: 'scale(1.05)' },
                            '70%': { transform: 'scale(0.9)' },
                            '100%': { transform: 'scale(1)', opacity: '1' }
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
            background-color: #111827 !important;
            color: #f9fafb !important;
        }
        
        .dark .bg-white {
            background-color: #1f2937 !important;
        }
        
        .dark .bg-gray-50 {
            background-color: #111827 !important;
        }
        
        .dark .bg-gray-100 {
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
        
        .dark .text-gray-900 {
            color: #f9fafb !important;
        }
        
        .dark .text-gray-800 {
            color: #f3f4f6 !important;
        }
        
        .dark .text-gray-700 {
            color: #d1d5db !important;
        }
        
        .dark .text-gray-600 {
            color: #9ca3af !important;
        }
        
        .dark .text-gray-500 {
            color: #6b7280 !important;
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
        
        .dark .shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3), 0 1px 2px 0 rgba(0, 0, 0, 0.2) !important;
        }
        
        .dark .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2) !important;
        }
        
        .dark .shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2) !important;
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
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .dark ::-webkit-scrollbar-track {
            background: #374151 !important;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        .dark ::-webkit-scrollbar-thumb {
            background: #6b7280 !important;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af !important;
        }
        
        /* Line clamp utilities */
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Custom animations */
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        .animate-bounce-in {
            animation: bounceIn 0.6s ease-out;
        }
        
        /* Card hover effects */
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .dark .card-hover:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2) !important;
        }
        
        /* Loading spinner */
        .spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Modal backdrop blur */
        .modal-backdrop {
            backdrop-filter: blur(4px);
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        /* Custom button styles */
        .btn-primary {
            @apply bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
        }
        
        .btn-secondary {
            @apply bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
        }
        
        .btn-success {
            @apply bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
        }
        
        .btn-danger {
            @apply bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
        }
        
        .btn-warning {
            @apply bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
        }
        
        /* Status badges */
        .status-new {
            @apply bg-blue-100 text-blue-800;
        }
        
        .status-preparing {
            @apply bg-yellow-100 text-yellow-800;
        }
        
        .status-on-way {
            @apply bg-purple-100 text-purple-800;
        }
        
        .status-delivered {
            @apply bg-green-100 text-green-800;
        }
        
        .status-cancelled {
            @apply bg-red-100 text-red-800;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900">
        <!-- Navigation -->
        @include('layouts.navigation')

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Page Content -->
            <main class="p-6">
                @if(session('success'))
                    <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-200 px-4 py-3 rounded-lg flex items-center animate-fade-in">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-800 text-red-700 dark:text-red-200 px-4 py-3 rounded-lg flex items-center animate-fade-in">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center space-x-3">
            <div class="spinner"></div>
            <span class="text-gray-700 dark:text-gray-300">Yuklanmoqda...</span>
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
            
            // Use PageLifecycle to ensure dark mode persists during page lifecycle events
            if ('onpagehide' in window) {
                window.addEventListener('pagehide', function(event) {
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
        
        // Global JavaScript functions
        window.showLoading = function() {
            document.getElementById('loading-overlay').classList.remove('hidden');
        }
        
        window.hideLoading = function() {
            document.getElementById('loading-overlay').classList.add('hidden');
        }
        
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });
        
        // Utility functions
        window.utils = {
            formatCurrency: function(amount, currency = 'so\'m') {
                return new Intl.NumberFormat('uz-UZ').format(amount) + ' ' + currency;
            },
            
            showNotification: function(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
                
                const colors = {
                    success: 'bg-green-500 text-white',
                    error: 'bg-red-500 text-white',
                    warning: 'bg-yellow-500 text-white',
                    info: 'bg-blue-500 text-white'
                };
                
                notification.className += ` ${colors[type]}`;
                notification.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : type === 'warning' ? 'exclamation-triangle' : 'info'}-circle"></i>
                        <span>${message}</span>
                    </div>
                `;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 100);
                
                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 5000);
            }
        };
    </script>
    
    @stack('scripts')
</body>
</html> 