<nav x-data="{ open: false }" class="bg-white shadow-sm border-b border-gray-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-utensils text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-800">Restaurant Admin</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Dashboard
                    </a>
                    
                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isRestaurantManager())
                        <a href="{{ route('admin.restaurants.index') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.restaurants.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            <i class="fas fa-store mr-2"></i>
                            Restoranlar
                        </a>
                        
                        <a href="{{ route('admin.orders.index') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Buyurtmalar
                        </a>
                        
                        <a href="{{ route('admin.bots.index') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bots.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            <i class="fab fa-telegram mr-2"></i>
                            Bot sozlamalari
                        </a>
                        
                        <a href="#" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            <i class="fas fa-users mr-2"></i>
                            Kuryerlar
                        </a>
                        
                        <a href="#" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Statistika
                        </a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <span>{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" 
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-user mr-2"></i>
                                Profil
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
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
                        class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('dashboard') }}" 
               class="block px-3 py-2 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                <i class="fas fa-tachometer-alt mr-2"></i>
                Dashboard
            </a>
            
            @if(Auth::user()->isSuperAdmin() || Auth::user()->isRestaurantManager())
                <a href="{{ route('admin.restaurants.index') }}" 
                   class="block px-3 py-2 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('admin.restaurants.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    <i class="fas fa-store mr-2"></i>
                    Restoranlar
                </a>
                
                <a href="{{ route('admin.orders.index') }}" 
                   class="block px-3 py-2 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Buyurtmalar
                </a>
                
                <a href="{{ route('admin.bots.index') }}" 
                   class="block px-3 py-2 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bots.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    <i class="fab fa-telegram mr-2"></i>
                    Bot sozlamalari
                </a>
                
                <a href="#" 
                   class="block px-3 py-2 text-base font-medium rounded-lg transition-colors text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                    <i class="fas fa-users mr-2"></i>
                    Kuryerlar
                </a>
                
                <a href="#" 
                   class="block px-3 py-2 text-base font-medium rounded-lg transition-colors text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Statistika
                </a>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1 px-4">
                <a href="{{ route('profile.edit') }}" 
                   class="block px-3 py-2 text-base font-medium rounded-lg transition-colors text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                    <i class="fas fa-user mr-2"></i>
                    Profil
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="block w-full text-left px-3 py-2 text-base font-medium rounded-lg transition-colors text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Chiqish
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
