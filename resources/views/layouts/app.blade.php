<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark:bg-gray-900">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ForkNow') }}</title>

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
            
            .dark .bg-gray-100 {
                background-color: #111827 !important;
            }
            
            .dark .bg-gray-50 {
                background-color: #1f2937 !important;
            }
            
            .dark .bg-gray-900 {
                background-color: #111827 !important;
            }
            
            .dark .bg-gray-800 {
                background-color: #1f2937 !important;
            }
            
            .dark .bg-gray-700 {
                background-color: #374151 !important;
            }
            
            .dark .bg-gray-600 {
                background-color: #4b5563 !important;
            }
            
            .dark .bg-gray-500 {
                background-color: #6b7280 !important;
            }
            
            .dark .bg-gray-400 {
                background-color: #9ca3af !important;
            }
            
            .dark .bg-gray-300 {
                background-color: #d1d5db !important;
            }
            
            .dark .bg-gray-200 {
                background-color: #e5e7eb !important;
            }
            
            .dark .bg-gray-100 {
                background-color: #f3f4f6 !important;
            }
            
            .dark .bg-gray-50 {
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
            
            /* Specific background overrides */
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
            
            /* Ensure transitions work */
            * {
                transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease !important;
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
        </style>
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
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
        </script>
    </body>
</html>
