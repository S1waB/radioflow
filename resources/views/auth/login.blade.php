<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RadioFlow</title>
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
                    <a href="{{ route('register') }}" class="btn-outline px-4 py-2 rounded-md font-medium text-white hover:bg-white hover:text-primary">register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Login Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-8">
                    <div class="flex justify-center mb-6">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-broadcast-tower text-3xl text-primary"></i>
                            <span class="text-2xl font-bold text-primary">RadioFlow</span>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-center text-primary mb-6">Sign in to your account</h2>

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 text-red-600 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mb-4 p-4 bg-green-50 text-green-600 rounded-lg">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input id="email" name="email" type="email" required autofocus
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                value="{{ old('email') }}">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input id="password" name="password" type="password" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember_me" name="remember" type="checkbox"
                                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                                    Remember me
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <div>
                            <button type="submit"
                                class="w-full btn-primary px-4 py-2 rounded-lg font-medium transition duration-200">
                                Sign in
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-primary font-medium hover:underline">
                                register now
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