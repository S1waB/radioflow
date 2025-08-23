<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<nav class="shadow-lg" style="background-color: #0a2164" x-data="{ mobileMenuOpen: false, userDropdownOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side - Logo and main menu -->
            <div class="flex items-center">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="text-white flex items-center">
                        <i class="fas fa-broadcast-tower text-2xl mr-2"></i>
                        <span class="text-xl font-bold">RadioFlow</span>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-4">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')"
                        class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </x-nav-link>

                    @if(auth()->user()->isAdmin())
                    <x-nav-link href="{{ route('users.index') }}"
                        class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md">
                        <i class="fas fa-users mr-2"></i> Users
                    </x-nav-link>

                    <x-nav-link href="{{ route('radios.index') }}" :active="request()->routeIs('radios.*')"
                        class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md">
                        <i class="fas fa-broadcast-tower mr-2"></i> Radios
                    </x-nav-link>

                    <x-nav-link href="{{ route('roles.index') }}" :active="request()->routeIs('roles.*')"
                        class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md">
                        <i class="fas fa-user-tag mr-2"></i> Roles
                    </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Right side - User dropdown -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <div class="ml-3 relative">
                    <button @click="userDropdownOpen = !userDropdownOpen"
                        class="flex items-center text-sm text-white focus:outline-none">
                        <div class="mr-2 text-right">
                            <div class="font-medium">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-white text-opacity-70">{{ Auth::user()->role->name }}</div>
                        </div>
                        @auth
                        <div class="flex items-center">
                            @if(Auth::user()->profile_photo_path)
                            <img class="h-8 w-8 rounded-full object-cover border-2 border-white"
                                src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                alt="{{ Auth::user()->name }}">
                            @else
                            <div class="h-8 w-8 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center font-semibold text-sm uppercase border-2 border-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            @endif
                            <i class="fas fa-chevron-down ml-1 text-xs transition-transform duration-200"
                                :class="{ 'transform rotate-180': userDropdownOpen }"></i>
                        </div>
                        @endauth
                    </button>

                    <div x-show="userDropdownOpen"
                        @click.away="userDropdownOpen = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <x-dropdown-link href="{{ route('profile.show') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <button type="submit" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-white hover:bg-white hover:bg-opacity-20 focus:outline-none">
                    <i class="fas fa-bars" x-show="!mobileMenuOpen"></i>
                    <i class="fas fa-times" x-show="mobileMenuOpen"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="sm:hidden" x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}"
                :active="request()->routeIs('dashboard')"
                class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </x-responsive-nav-link>

            @if(auth()->user()->role->name === 'admin')
            <x-responsive-nav-link href="{{ route('users.index') }}"
                :active="request()->routeIs('users.*')"
                class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md">
                <i class="fas fa-users mr-2"></i> Users
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('radios.index') }}"
                :active="request()->routeIs('radios.*')"
                class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md">
                <i class="fas fa-broadcast-tower mr-2"></i> Radios
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('roles.index') }}"
                :active="request()->routeIs('roles.*')"
                class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md">
                <i class="fas fa-user-tag mr-2"></i> Roles
            </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-3 border-t border-white border-opacity-20" x-data="{ mobileUserDropdownOpen: false }">
            <div class="flex items-center px-5">
                <div class="flex-shrink-0">
                    <img class="h-10 w-10 rounded-full object-cover border-2 border-white"
                        src="{{ Auth::user()->profile_photo_url }}"
                        alt="{{ Auth::user()->name }}">
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-white text-opacity-70">{{ Auth::user()->role->name }}</div>
                </div>
                <button @click="mobileUserDropdownOpen = !mobileUserDropdownOpen"
                    class="ml-auto text-white focus:outline-none">
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                        :class="{ 'transform rotate-180': mobileUserDropdownOpen }"></i>
                </button>
            </div>

            <div x-show="mobileUserDropdownOpen"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="mt-3 px-2 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}"
                    class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md">
                    <i class="fas fa-user mr-2"></i> Profile
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}"
                        @click.prevent="$root.submit();"
                        class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>