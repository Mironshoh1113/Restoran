<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'ForkNow') }} - Restaurant Management System</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

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

        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
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
            
            .dark .bg-white\/90 {
                background-color: rgba(31, 41, 55, 0.9) !important;
            }
            
            .dark .bg-gray-800\/90 {
                background-color: rgba(31, 41, 55, 0.9) !important;
            }
            
            .dark .border-gray-200 {
                border-color: #374151 !important;
            }
            
            .dark .border-gray-700 {
                border-color: #4b5563 !important;
            }
            
            .dark .text-gray-600 {
                color: #9ca3af !important;
            }
            
            .dark .text-gray-300 {
                color: #d1d5db !important;
            }
            
            .dark .text-gray-400 {
                color: #9ca3af !important;
            }
            
            .dark .text-gray-500 {
                color: #6b7280 !important;
            }
            
            .dark .hover\:text-orange-600:hover {
                color: #ea580c !important;
            }
            
            .dark .hover\:text-orange-400:hover {
                color: #fb923c !important;
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
            
            .dark .text-gray-200 {
                color: #e5e7eb !important;
            }
            
            .dark .text-gray-100 {
                color: #f3f4f6 !important;
            }
            
            .dark .text-gray-50 {
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
            
            /* Footer dark mode fixes */
            .dark footer {
                background-color: #1f2937 !important;
            }
            
            .dark footer .text-gray-400 {
                color: #d1d5db !important;
            }
            
            .dark footer .text-white {
                color: #ffffff !important;
            }
            
            .dark footer .border-gray-800 {
                border-color: #374151 !important;
            }
            
            .dark footer a:hover {
                color: #ffffff !important;
            }
            
            /* Additional footer dark mode overrides */
            html.dark footer {
                background-color: #1f2937 !important;
            }
            
            html.dark footer * {
                color: inherit !important;
            }
            
            html.dark footer .text-gray-400 {
                color: #d1d5db !important;
            }
            
            html.dark footer .text-white {
                color: #ffffff !important;
            }
            
            html.dark footer .border-gray-800 {
                border-color: #374151 !important;
            }
            
            html.dark footer a:hover {
                color: #ffffff !important;
            }
            
            /* Force footer styles in dark mode */
            body.dark footer,
            .dark footer,
            html.dark footer {
                background-color: #1f2937 !important;
                color: #ffffff !important;
            }
            
            body.dark footer .text-gray-400,
            .dark footer .text-gray-400,
            html.dark footer .text-gray-400 {
                color: #d1d5db !important;
            }
            
            body.dark footer .text-white,
            .dark footer .text-white,
            html.dark footer .text-white {
                color: #ffffff !important;
            }
            
            body.dark footer .border-gray-800,
            .dark footer .border-gray-800,
            html.dark footer .border-gray-800 {
                border-color: #374151 !important;
            }
            
            /* Ensure transitions work */
            * {
                transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease !important;
            }
        </style>
        
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
    </head>
    <body class="font-sans antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
        <!-- Navigation -->
        <nav class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                                        ForkNow
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Restaurant Management</span>
                                </div>
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden md:flex items-center space-x-8 ml-10">
                            <a href="#features" class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Xususiyatlar</a>
                            <a href="#pricing" class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Narxlar</a>
                            <a href="#contact" class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Aloqa</a>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Dark Mode Toggle -->
                        <button id="welcomeDarkModeToggle" 
                                class="p-2 rounded-lg bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200 dark:border-gray-700 shadow-lg hover:shadow-xl transition-all">
                            <svg id="welcomeMoonIcon" class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            <svg id="welcomeSunIcon" class="w-5 h-5 text-yellow-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </button>

                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Kirish</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all shadow-lg">
                                    Ro'yxatdan o'tish
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="bg-gradient-to-br from-orange-50 via-white to-red-50 py-20 lg:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-6">
                        Restoranlaringizni 
                        <span class="bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                            raqamlashtiring
                        </span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                        ForkNow bilan restoranlaringizni boshqarish oson va samarali bo'ladi. 
                        Buyurtmalar, menyu, mijozlar va kuryerlar barchasi bir joyda.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:from-orange-600 hover:to-red-600 transition-all shadow-lg hover:shadow-xl">
                            Bepul sinab ko'ring
                        </a>
                        <a href="#demo" class="border-2 border-orange-500 text-orange-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-orange-500 hover:text-white transition-all">
                            Demo ko'rish
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        Nima uchun ForkNow?
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Zamonaviy restoran boshqaruvi uchun barcha kerakli vositalar
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 p-8 rounded-2xl border border-orange-100">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Oson buyurtma boshqaruvi</h3>
                        <p class="text-gray-600">
                            Buyurtmalarni real vaqtda qabul qiling, kuryerlarga yuboring va mijozlar bilan aloqada bo'ling.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 p-8 rounded-2xl border border-orange-100">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Menyu boshqaruvi</h3>
                        <p class="text-gray-600">
                            Mahsulotlaringizni osongina qo'shing, tahrirlang va narxlarni yangilang.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 p-8 rounded-2xl border border-orange-100">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Batafsil hisobotlar</h3>
                        <p class="text-gray-600">
                            Savdo, mijozlar va kuryerlar haqida batafsil ma'lumotlar va statistikalar.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 p-8 rounded-2xl border border-orange-100">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Mobil ilova</h3>
                        <p class="text-gray-600">
                            Android va iOS qurilmalarida ishlaydigan mobil ilova bilan har joydan boshqaring.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 p-8 rounded-2xl border border-orange-100">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Tezkor ishlash</h3>
                        <p class="text-gray-600">
                            Zamonaviy texnologiyalar asosida qurilgan tez va ishonchli tizim.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 p-8 rounded-2xl border border-orange-100">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Xavfsizlik</h3>
                        <p class="text-gray-600">
                            Ma'lumotlaringiz to'liq himoyalangan va faqat sizga tegishli.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        Narxlar
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Har xil o'lchamdagi restoranlar uchun mos narxlar
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Basic Plan -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-lg">
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Boshlang'ich</h3>
                            <div class="text-4xl font-bold text-orange-600 mb-2">$29</div>
                            <div class="text-gray-600">oyiga</div>
                        </div>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"></path>
                                </svg>
                                Kunlik 50 ta buyurtma
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"></path>
                                </svg>
                                Asosiy hisobotlar
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"></path>
                                </svg>
                                Email qo'llab-quvvatlash
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white py-3 rounded-lg font-semibold text-center block hover:from-orange-600 hover:to-red-600 transition-all">
                            Tanlash
                        </a>
                    </div>

                    <!-- Pro Plan -->
                    <div class="bg-gradient-to-br from-orange-500 to-red-500 p-8 rounded-2xl text-white relative">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-yellow-400 text-gray-900 px-4 py-2 rounded-full text-sm font-semibold">
                                Eng mashhur
                            </span>
                        </div>
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold mb-4">Professional</h3>
                            <div class="text-4xl font-bold mb-2">$79</div>
                            <div class="text-orange-100">oyiga</div>
                        </div>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-white mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"></path>
                                </svg>
                                Cheksiz buyurtmalar
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-white mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"></path>
                                </svg>
                                Batafsil hisobotlar
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-white mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"></path>
                                </svg>
                                Telegram bot integratsiyasi
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-white mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"></path>
                                </svg>
                                24/7 qo'llab-quvvatlash
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="w-full bg-white text-orange-600 py-3 rounded-lg font-semibold text-center block hover:bg-gray-50 transition-all">
                            Tanlash
                        </a>
                    </div>

                    <!-- Enterprise Plan -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-lg">
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Korxona</h3>
                            <div class="text-4xl font-bold text-orange-600 mb-2">$199</div>
                            <div class="text-gray-600">oyiga</div>
                        </div>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"></path>
                                </svg>
                                Ko'p filiallar
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"></path>
                                </svg>
                                API integratsiyasi
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"></path>
                                </svg>
                                Maxsus yechimlar
                            </li>
                        </ul>
                        <a href="#contact" class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white py-3 rounded-lg font-semibold text-center block hover:from-orange-600 hover:to-red-600 transition-all">
                            Bog'lanish
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        Biz bilan bog'laning
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Savollaringiz bormi? Bizga yozing, tezda javob beramiz
                    </p>
                </div>

                <div class="grid lg:grid-cols-2 gap-12">
                    <!-- Contact Info -->
                    <div>
                        <div class="space-y-8">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Email</h3>
                                    <p class="text-gray-600">info@forknow.uz</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Telefon</h3>
                                    <p class="text-gray-600">+998 90 123 45 67</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Manzil</h3>
                                    <p class="text-gray-600">Toshkent shahri, O'zbekiston</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-gray-50 p-8 rounded-2xl">
                        <form class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ism</label>
                                <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Xabar</label>
                                <textarea rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white py-3 rounded-lg font-semibold hover:from-orange-600 hover:to-red-600 transition-all">
                                Yuborish
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 dark:bg-gray-800 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-white dark:text-white">ForkNow</span>
                        </div>
                        <p class="text-gray-400 dark:text-gray-300">
                            Restoranlaringizni raqamlashtirish uchun zamonaviy yechimlar.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-white dark:text-white">Mahsulot</h3>
                        <ul class="space-y-2 text-gray-400 dark:text-gray-300">
                            <li><a href="#" class="hover:text-white dark:hover:text-white transition-colors">Xususiyatlar</a></li>
                            <li><a href="#" class="hover:text-white dark:hover:text-white transition-colors">Narxlar</a></li>
                            <li><a href="#" class="hover:text-white dark:hover:text-white transition-colors">API</a></li>
                            <li><a href="#" class="hover:text-white dark:hover:text-white transition-colors">Hujjatlar</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-white dark:text-white">Kompaniya</h3>
                        <ul class="space-y-2 text-gray-400 dark:text-gray-300">
                            <li><a href="#" class="hover:text-white dark:hover:text-white transition-colors">Haqida</a></li>
                            <li><a href="#" class="hover:text-white dark:hover:text-white transition-colors">Blog</a></li>
                            <li><a href="#" class="hover:text-white dark:hover:text-white transition-colors">Karyera</a></li>
                            <li><a href="#" class="hover:text-white dark:hover:text-white transition-colors">Yangiliklar</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-white dark:text-white">Qo'llab-quvvatlash</h3>
                        <ul class="space-y-2 text-gray-400 dark:text-gray-300">
                            <li><a href="#" class="hover:text-white dark:hover:text-white transition-colors">Yordam markazi</a></li>
                            <li><a href="#" class="hover:text-white dark:hover:text-white transition-colors">Aloqa</a></li>
                            <li><a href="#" class="hover:text-white dark:hover:text-white transition-colors">Status</a></li>
                            <li><a href="#" class="hover:text-white dark:hover:text-white transition-colors">Xavfsizlik</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 dark:border-gray-700 mt-8 pt-8 text-center text-gray-400 dark:text-gray-300">
                    <p>&copy; 2024 ForkNow. Barcha huquqlar himoyalangan.</p>
                </div>
            </div>
        </footer>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('app', () => ({
                    mobileMenu: false
                }))
            })
            
            // Welcome page dark mode toggle
            document.addEventListener('DOMContentLoaded', function() {
                const welcomeToggle = document.getElementById('welcomeDarkModeToggle');
                const welcomeMoonIcon = document.getElementById('welcomeMoonIcon');
                const welcomeSunIcon = document.getElementById('welcomeSunIcon');
                
                if (welcomeToggle) {
                    welcomeToggle.addEventListener('click', function() {
                        const isDark = window.toggleDarkMode();
                        updateWelcomeToggleIcons();
                    });
                }
                
                function updateWelcomeToggleIcons() {
                    const isDark = document.documentElement.classList.contains('dark');
                    
                    if (welcomeMoonIcon && welcomeSunIcon) {
                        if (isDark) {
                            welcomeMoonIcon.classList.add('hidden');
                            welcomeSunIcon.classList.remove('hidden');
                        } else {
                            welcomeMoonIcon.classList.remove('hidden');
                            welcomeSunIcon.classList.add('hidden');
                        }
                    }
                }
                
                // Initialize icons
                updateWelcomeToggleIcons();
            });
        </script>
    </body>
</html>
