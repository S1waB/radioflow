<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - RadioFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        .form-bg {
            background-color: #f0f4ff;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 font-sans">
    <!-- Navigation Bar -->
    <nav class="bg-primary text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-broadcast-tower text-2xl"></i>
                    <span class="text-xl font-bold">RadioFlow</span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('login') }}" class="btn-outline px-4 py-2 rounded-md font-medium text-white hover:bg-white hover:text-primary">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Register Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto form-bg rounded-xl shadow-md overflow-hidden">
                <div class="p-8">
                    <div class="flex justify-center mb-6">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-broadcast-tower text-3xl text-primary"></i>
                            <span class="text-2xl font-bold text-primary">RadioFlow</span>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-center text-primary mb-6">Create your account</h2>

                    @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 text-red-600 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('register.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input id="name" name="name" type="text" required autofocus
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                    value="{{ old('name') }}">
                                @error('name')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input id="email" name="email" type="email" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                    value="{{ old('email') }}">
                                @error('email')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input id="phone_number" name="phone_number" type="tel"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                    value="{{ old('phone_number') }}">
                                @error('phone_number')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input id="address" name="address" type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                    value="{{ old('address') }}">
                                @error('address')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Role Selection -->
                            <div>
                                <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <select id="role_id" name="role_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                                    <option value="">Select a role</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Radio Station Selection (optional) -->
                            <div>
                                <label for="radio_id" class="block text-sm font-medium text-gray-700 mb-1">Radio Station (if applicable)</label>
                                <select id="radio_id" name="radio_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                                    <option value="">None</option>
                                    @foreach($radios as $radio)
                                    <option value="{{ $radio->id }}" {{ old('radio_id') == $radio->id ? 'selected' : '' }}>
                                        {{ $radio->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('radio_id')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Bio -->
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Short Bio</label>
                            <textarea id="bio" name="bio"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">{{ old('bio') }}</textarea>
                            @error('bio')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input id="password" name="password" type="password" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                                @error('password')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                            </div>
                        </div>

                        <div>
                            <button type="submit"
                                class="w-full btn-primary px-4 py-2 rounded-lg font-medium transition duration-200">
                                Register
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-primary font-medium hover:underline">
                                Sign in
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.footer')
</body>
</html>