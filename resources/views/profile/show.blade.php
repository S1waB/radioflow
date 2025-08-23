<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RadioFlow - Streaming Radio Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script
        src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"
        defer></script>
    <style>
        .bg-primary {
            background-color: #0a2164;
        }

        .text-primary {
            color: #0a2164;
        }

        .border-primary {
            border-color: #0a2164;
        }

        .btn-primary {
            background-color: #0a2164;
            color: white;
        }

        .btn-primary:hover {
            background-color: #081a52;
        }

        .btn-outline {
            border: 2px solid #0a2164;
            color: #0a2164;
        }

        .btn-outline:hover {
            background-color: #0a2164;
            color: white;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50 font-sans">
    <!-- Navigation Bar -->
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

    <!-- Profile Page Content -->
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-primary mb-6">Profile</h1>
        <div class="bg-white shadow rounded-lg p-6">
            <form
                action="{{ route('profile.update') }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Profile Photo at the top -->
                <div class="mb-6 flex items-center">
                    @if(Auth::user()->profile_photo_path)
                    <img
                        src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                        class="h-24 w-24 rounded-full object-cover border-2 border-primary mr-6"
                        alt="Profile photo" />
                    @else
                    <div
                        class="h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center font-bold text-3xl text-gray-700 border-2 border-primary mr-6">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    @endif
                    <div class="flex-1">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1">Change Profile Photo</label>
                        <input
                            type="file"
                            name="profile_photo"
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" />
                    </div>
                </div>

                <!-- Grid 3 columns for fields -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Name -->
                    <div>
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700">Name</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', Auth::user()->name) }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-primary focus:border-primary" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label
                            for="email"
                            class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email', Auth::user()->email) }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-primary focus:border-primary" />
                    </div>

                    <!-- Role (Read-only) -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700">Role</label>
                        <input
                            type="text"
                            value="{{ Auth::user()->role->name }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 bg-gray-100 text-gray-600 cursor-not-allowed"
                            disabled />
                    </div>

                    <!-- Address -->
                    <div>
                        <label
                            for="address"
                            class="block text-sm font-medium text-gray-700">Address</label>
                        <input
                            type="text"
                            name="address"
                            id="address"
                            value="{{ old('address', Auth::user()->address) }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-primary focus:border-primary" />
                    </div>

                    <!-- Radio Name (Read-only) -->
                    <div>
                        <label
                            for="radio_name"
                            class="block text-sm font-medium text-gray-700">Radio Name</label>
                        <input
                            type="text"
                            id="radio_name"
                            value="{{ Auth::user()->radio ? Auth::user()->radio->name : 'No radio assigned' }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 bg-gray-100 text-gray-600 cursor-not-allowed"
                            disabled />
                    </div>

                    <!-- New Password -->
                    <div>
                        <label
                            for="password"
                            class="block text-sm font-medium text-gray-700">New Password</label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            autocomplete="new-password"
                            placeholder="Leave blank to keep current password"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-primary focus:border-primary" />
                    </div>

                    <!-- Current Password (NEW) -->
                    <div>
                        <label
                            for="current_password"
                            class="block text-sm font-medium text-gray-700">Current Password</label>
                        <input
                            type="password"
                            name="current_password"
                            id="current_password"
                            autocomplete="current-password"
                            placeholder="Enter your current password"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-primary focus:border-primary" />
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label
                            for="password_confirmation"
                            class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            autocomplete="new-password"
                            placeholder="Confirm new password"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-primary focus:border-primary" />
                    </div>

                    <!-- Bio (full width) -->
                    <div class="md:col-span-3">
                        <label
                            for="bio"
                            class="block text-sm font-medium text-gray-700">Bio</label>
                        <textarea
                            name="bio"
                            id="bio"
                            rows="4"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-primary focus:border-primary">{{ old('bio', Auth::user()->bio) }}</textarea>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="mt-6">
                    <button
                        type="submit"
                        class="btn-primary px-4 py-2 rounded-md">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    @include('layouts.footer')
</body>

</html>