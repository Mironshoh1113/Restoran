<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
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
                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Dashboard
                    </a>
                    
                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isRestaurantManager())
                        <a href="{{ route('admin.restaurants.index') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.restaurants.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <i class="fas fa-store mr-2"></i>
                            Restoranlar
                        </a>
                        
                        <a href="{{ route('admin.orders.index') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Buyurtmalar
                        </a>
                        
                        <a href="{{ route('admin.bots.index') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bots.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <i class="fab fa-telegram mr-2"></i>
                            Bot sozlamalari
                        </a>
                        
                        <a href="{{ route('admin.user-subscriptions.index') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.user-subscriptions.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <i class="fas fa-users mr-2"></i>
                            Foydalanuvchi obunalari
                        </a>
                    @endif

                    @if(Auth::user()->isSuperAdmin())
                        <a href="{{ route('super.plans.index') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('super.plans.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <i class="fas fa-layer-group mr-2"></i>
                            Tariflar
                        </a>
                        					<a href="{{ route('super.subscriptions.index') }}" 
					   class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('super.subscriptions.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
						<i class="fas fa-id-card mr-2"></i>
						Obunalar
					</a>
					<a href="{{ route('super.contact-messages.index') }}" 
					   class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('super.contact-messages.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
						<i class="fas fa-envelope mr-2"></i>
						Kontakt xabarlari
					</a>
				@endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Dark Mode Toggle -->
                <button id="navDarkModeToggle" 
                        class="p-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-all mr-4">
                    <svg id="navMoonIcon" class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <svg id="navSunIcon" class="w-5 h-5 text-yellow-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                        <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <span>{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500"></i>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" 
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                                <i class="fas fa-user mr-2"></i>
                                Profil
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Chiqish
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" 
                        class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <!-- Dark Mode Toggle for Mobile -->
            <div class="flex items-center justify-between px-3 py-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Rejim:</span>
                <button id="navDarkModeToggleMobile" 
                        class="p-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-all">
                    <svg id="navMoonIconMobile" class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <svg id="navSunIconMobile" class="w-5 h-5 text-yellow-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>
            </div>
            
            <a href="{{ route('dashboard') }}" 
               class="block px-3 py-2 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                <i class="fas fa-tachometer-alt mr-2"></i>
                Dashboard
            </a>
            
            @if(Auth::user()->isSuperAdmin() || Auth::user()->isRestaurantManager())
                <a href="{{ route('admin.restaurants.index') }}" 
                   class="block px-3 py-2 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('admin.restaurants.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-store mr-2"></i>
                    Restoranlar
                </a>
                
                <a href="{{ route('admin.orders.index') }}" 
                   class="block px-3 py-2 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Buyurtmalar
                </a>
                
                <a href="{{ route('admin.bots.index') }}" 
                   class="block px-3 py-2 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bots.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <i class="fab fa-telegram mr-2"></i>
                    Bot sozlamalari
                </a>
            @endif

            @if(Auth::user()->isSuperAdmin())
                <a href="{{ route('super.plans.index') }}" 
                   class="block px-3 py-2 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('super.plans.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-layer-group mr-2"></i>
                    Tariflar
                </a>
                				<a href="{{ route('super.subscriptions.index') }}" 
				   class="block px-3 py-2 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('super.subscriptions.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
					<i class="fas fa-id-card mr-2"></i>
					Obunalar
				</a>
				<a href="{{ route('super.contact-messages.index') }}" 
				   class="block px-3 py-2 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('super.contact-messages.*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-700' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700' }}">
					<i class="fas fa-envelope mr-2"></i>
					Kontakt xabarlari
				</a>
			@endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700">
            <div class="px-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1 px-4">
                <a href="{{ route('profile.edit') }}" 
                   class="block px-3 py-2 text-base font-medium rounded-lg transition-colors text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-user mr-2"></i>
                    Profil
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="block w-full text-left px-4 py-2 text-base font-medium rounded-lg transition-colors text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Chiqish
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    // Initialize navigation dark mode toggles
    document.addEventListener('DOMContentLoaded', function() {
        // Desktop toggle
        const navToggle = document.getElementById('navDarkModeToggle');
        const navMoonIcon = document.getElementById('navMoonIcon');
        const navSunIcon = document.getElementById('navSunIcon');
        
        // Mobile toggle
        const navToggleMobile = document.getElementById('navDarkModeToggleMobile');
        const navMoonIconMobile = document.getElementById('navMoonIconMobile');
        const navSunIconMobile = document.getElementById('navSunIconMobile');
        
        if (navToggle) {
            navToggle.addEventListener('click', function() {
                const isDark = window.toggleDarkMode();
                updateNavToggleIcons();
            });
        }
        
        if (navToggleMobile) {
            navToggleMobile.addEventListener('click', function() {
                const isDark = window.toggleDarkMode();
                updateNavToggleIcons();
            });
        }
        
        function updateNavToggleIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            
            // Desktop icons
            if (navMoonIcon && navSunIcon) {
                if (isDark) {
                    navMoonIcon.classList.add('hidden');
                    navSunIcon.classList.remove('hidden');
                } else {
                    navMoonIcon.classList.remove('hidden');
                    navSunIcon.classList.add('hidden');
                }
            }
            
            // Mobile icons
            if (navMoonIconMobile && navSunIconMobile) {
                if (isDark) {
                    navMoonIconMobile.classList.add('hidden');
                    navSunIconMobile.classList.remove('hidden');
                } else {
                    navMoonIconMobile.classList.remove('hidden');
                    navSunIconMobile.classList.add('hidden');
                }
            }
        }
        
        // Initialize icons
        updateNavToggleIcons();
    });
</script>
