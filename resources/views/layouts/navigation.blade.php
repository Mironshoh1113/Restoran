<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 shadow-sm border-bottom border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4">
        <div class="d-flex justify-between align-items-center" style="height:64px;">
            <div class="d-flex align-items-center">
                <div class="shrink-0 d-flex align-items-center">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                        <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl d-flex align-items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
                            </svg>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-xl fw-bold" style="background:linear-gradient(90deg,#ea580c,#dc2626);-webkit-background-clip:text;background-clip:text;color:transparent;">ForkNow</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 fw-medium">Restaurant Management</span>
                        </div>
                    </a>
                </div>

                <div class="d-none d-sm-flex ms-3 gap-1">
                    <a href="{{ route('dashboard') }}" 
                       class="px-3 py-2 text-sm fw-medium rounded transition-colors {{ request()->routeIs('dashboard') ? 'bg-orange-50 text-orange-700 border' : 'text-gray-600' }} text-decoration-none">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isRestaurantManager())
                        <a href="{{ route('admin.restaurants.index') }}" 
                           class="px-3 py-2 text-sm fw-medium rounded transition-colors {{ request()->routeIs('admin.restaurants.*') ? 'bg-orange-50 text-orange-700 border' : 'text-gray-600' }} text-decoration-none">
                            <i class="fas fa-store me-2"></i>
                            Restoranlar
                        </a>
                        <a href="{{ route('admin.orders.index') }}" 
                           class="px-3 py-2 text-sm fw-medium rounded transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-orange-50 text-orange-700 border' : 'text-gray-600' }} text-decoration-none">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Buyurtmalar
                        </a>
                        <a href="{{ route('admin.bots.index') }}" 
                           class="px-3 py-2 text-sm fw-medium rounded transition-colors {{ request()->routeIs('admin.bots.*') ? 'bg-orange-50 text-orange-700 border' : 'text-gray-600' }} text-decoration-none">
                            <i class="fab fa-telegram me-2"></i>
                            Bot sozlamalari
                        </a>
                        <a href="{{ route('admin.couriers.index') }}" class="px-3 py-2 text-sm fw-medium rounded text-gray-600 text-decoration-none">
                            <i class="fas fa-users me-2"></i>
                            Kuryerlar
                        </a>
                        <a href="{{ route('dashboard.stats') }}" class="px-3 py-2 text-sm fw-medium rounded text-gray-600 text-decoration-none">
                            <i class="fas fa-chart-bar me-2"></i>
                            Statistika
                        </a>
                    @endif
                </div>
            </div>

            <div class="d-none d-sm-flex align-items-center ms-3">
                <button id="navDarkModeToggle" class="btn btn-light border me-3">
                    <i id="navMoonIcon" class="fa-regular fa-moon"></i>
                    <i id="navSunIcon" class="fa-regular fa-sun d-none text-warning"></i>
                </button>

                <div x-data="{ open: false }" class="position-relative">
                    <button @click="open = !open" class="d-flex align-items-center gap-2 px-3 py-2 text-sm fw-medium bg-white border rounded">
                        <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-circle d-flex align-items-center justify-center">
                            <span class="text-white fw-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <span>{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </button>

                    <div x-show="open" @click.away="open = false" class="position-absolute end-0 mt-2 bg-white rounded shadow border z-3" style="min-width:12rem;">
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="d-block px-3 py-2 text-sm text-decoration-none text-gray-700">
                                <i class="fas fa-user me-2"></i>Profil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="d-block w-100 text-start px-3 py-2 text-sm btn btn-link text-decoration-none text-gray-700">
                                    <i class="fas fa-sign-out-alt me-2"></i>Chiqish
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-sm-none">
                <button @click="open = ! open" class="btn btn-light"><i class="fas fa-bars"></i></button>
            </div>
        </div>
    </div>

    <div :class="{'d-block': open, 'd-none': ! open}" class="d-none d-sm-none">
        <div class="pt-2 pb-3 px-4">
            <div class="d-flex align-items-center justify-content-between px-3 py-2">
                <span class="text-sm fw-medium">Rejim:</span>
                <button id="navDarkModeToggleMobile" class="btn btn-light">
                    <i id="navMoonIconMobile" class="fa-regular fa-moon"></i>
                    <i id="navSunIconMobile" class="fa-regular fa-sun d-none text-warning"></i>
                </button>
            </div>

            <a href="{{ route('dashboard') }}" class="d-block px-3 py-2 text-base fw-medium rounded text-decoration-none {{ request()->routeIs('dashboard') ? 'bg-orange-50 text-orange-700 border' : 'text-gray-600' }}">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            @if(Auth::user()->isSuperAdmin() || Auth::user()->isRestaurantManager())
                <a href="{{ route('admin.restaurants.index') }}" class="d-block px-3 py-2 text-base fw-medium rounded text-decoration-none {{ request()->routeIs('admin.restaurants.*') ? 'bg-orange-50 text-orange-700 border' : 'text-gray-600' }}">
                    <i class="fas fa-store me-2"></i>Restoranlar
                </a>
                <a href="{{ route('admin.orders.index') }}" class="d-block px-3 py-2 text-base fw-medium rounded text-decoration-none {{ request()->routeIs('admin.orders.*') ? 'bg-orange-50 text-orange-700 border' : 'text-gray-600' }}">
                    <i class="fas fa-shopping-cart me-2"></i>Buyurtmalar
                </a>
                <a href="{{ route('admin.bots.index') }}" class="d-block px-3 py-2 text-base fw-medium rounded text-decoration-none {{ request()->routeIs('admin.bots.*') ? 'bg-orange-50 text-orange-700 border' : 'text-gray-600' }}">
                    <i class="fab fa-telegram me-2"></i>Bot sozlamalari
                </a>
                <a href="{{ route('admin.couriers.index') }}" class="d-block px-3 py-2 text-base fw-medium rounded text-decoration-none text-gray-600">
                    <i class="fas fa-users me-2"></i>Kuryerlar
                </a>
                <a href="{{ route('dashboard.stats') }}" class="d-block px-3 py-2 text-base fw-medium rounded text-decoration-none text-gray-600">
                    <i class="fas fa-chart-bar me-2"></i>Statistika
                </a>
            @endif
        </div>

        <div class="pt-3 pb-2 border-top">
            <div class="px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-circle d-flex align-items-center justify-center">
                        <span class="text-white fw-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <div class="fw-medium">{{ Auth::user()->name }}</div>
                        <div class="text-muted small">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>
            <div class="mt-2 px-4">
                <a href="{{ route('profile.edit') }}" class="d-block px-3 py-2 text-base fw-medium rounded text-decoration-none text-gray-600">
                    <i class="fas fa-user me-2"></i>Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="d-block w-100 text-start px-3 py-2 text-base fw-medium btn btn-link text-decoration-none text-gray-600">
                        <i class="fas fa-sign-out-alt me-2"></i>Chiqish
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navToggle = document.getElementById('navDarkModeToggle');
        const navMoonIcon = document.getElementById('navMoonIcon');
        const navSunIcon = document.getElementById('navSunIcon');
        const navToggleMobile = document.getElementById('navDarkModeToggleMobile');
        const navMoonIconMobile = document.getElementById('navMoonIconMobile');
        const navSunIconMobile = document.getElementById('navSunIconMobile');
        function updateNavToggleIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            if (navMoonIcon && navSunIcon) navMoonIcon.classList.toggle('d-none', isDark); navSunIcon.classList.toggle('d-none', !isDark); }
            if (navMoonIconMobile && navSunIconMobile) navMoonIconMobile.classList.toggle('d-none', isDark); navSunIconMobile.classList.toggle('d-none', !isDark); }
        }
        if (navToggle) navToggle.addEventListener('click', function(){ window.toggleDarkMode(); updateNavToggleIcons(); });
        if (navToggleMobile) navToggleMobile.addEventListener('click', function(){ window.toggleDarkMode(); updateNavToggleIcons(); });
        updateNavToggleIcons();
    });
</script>
