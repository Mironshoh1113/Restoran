<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark:bg-gray-900">
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
        
        /* Ensure transitions work */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease !important;
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
        // Initialize dark mode immediately
        (function() {
            const darkMode = localStorage.getItem('darkMode') === 'true';
            if (darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
        
        // Global dark mode toggle function
        window.toggleDarkMode = function() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);
            
            // Force re-render of all elements
            const allElements = document.querySelectorAll('*');
            allElements.forEach(element => {
                // Trigger reflow for each element
                element.style.display = 'none';
                element.offsetHeight;
                element.style.display = '';
            });
            
            // Force body re-render
            document.body.style.display = 'none';
            document.body.offsetHeight;
            document.body.style.display = '';
            
            // Force html re-render
            document.documentElement.style.display = 'none';
            document.documentElement.offsetHeight;
            document.documentElement.style.display = '';
            
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
</body>
</html> 