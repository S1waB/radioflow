<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RadioFlow - Streaming Radio Platform</title>
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

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8">
                    <a href="#home" class="hover:text-gray-300 transition duration-200">Home</a>
                    <a href="#features" class="hover:text-gray-300 transition duration-200">Features</a>
                    <a href="#about" class="hover:text-gray-300 transition duration-200">About Us</a>
                    <a href="#contact" class="hover:text-gray-300 transition duration-200">Contact</a>
                    @auth
                    <a href="{{ route('dashboard') }}" class="bg-white text-primary px-4 py-2 rounded-md font-medium">Dashboard</a>
                    @else
                    <a href="{{ route('login') }}" class="bg-white text-primary px-4 py-2 rounded-md font-medium">Login</a>
                    <a href="{{ route('radio-demand.create') }}" class="bg-white text-primary px-4 py-2 rounded-md font-medium hover:bg-primary-700">
                        Request Radio
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    <!-- Home Section -->

    <section id="home">
        <div class="container mx-auto px-4 py-12">
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <div class="flex flex-col lg:flex-row items-center justify-between gap-12">
                <!-- Left Column - Text Content -->
                <div class="lg:w-1/2 text-center lg:text-left">
                    <h1 class="text-4xl md:text-5xl font-bold text-primary mb-6 leading-tight">
                        Welcome to <span class="text-primary">RadioFlow</span>
                    </h1>

                    <p class="text-xl text-gray-600 mb-8">
                        Welcome to RadioFlow, the all-in-one platform to manage your radio station
                        from show planning and team coordination to episode scheduling, guest tracking,
                        and real-time collaboration.<br>
                        Empowering every role : animateurs, technicians, digital staff,
                        and admins , to work seamlessly and efficiently.
                    </p>

                    @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary px-8 py-3 rounded-lg font-medium transition duration-200 inline-block">
                        Accéder au tableau de bord
                    </a>
                    @else
                    <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                        <a href="{{ route('login') }}" class="btn-primary px-8 py-3 rounded-lg font-medium transition duration-200">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="btn-outline px-8 py-3 rounded-lg font-medium transition duration-200">
                            register
                        </a>
                        <a href="{{ route('radio-demand.create') }}"
                            class="bg-primary hover:bg-primary text-white px-8 py-3 rounded-lg font-medium transition duration-200">
                            Request Radio Space
                        </a>
                    </div>
                    @endauth
                </div>

                <!-- Right Column - Image -->
                <div class="lg:w-1/2 flex justify-center">
                    <div class="relative w-full max-w-md">
                        <div class="absolute -inset-4 bg-primary rounded-2xl opacity-10 blur-lg"></div>
                        <div class="relative bg-white p-6 rounded-2xl shadow-xl border border-gray-100">
                            <img src="{{ asset('images/radioflow logo.png')}}"
                                alt="RadioFlow streaming platform interface"
                                class="rounded-lg w-full h-auto">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Features Section -->
    <section id="features">
        <div class="bg-primary text-white py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Why Choose RadioFlow?</h2>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white bg-opacity-10 p-6 rounded-xl backdrop-blur-sm">
                        <div class="text-4xl mb-4">
                            <i class="fas fa-microphone-alt"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Simplified Management</h3>
                        <p class="text-white text-opacity-80">Centralize your radio shows, schedules, and content in one easy-to-use platform.</p>
                    </div>

                    <div class="bg-white bg-opacity-10 p-6 rounded-xl backdrop-blur-sm">
                        <div class="text-4xl mb-4">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Real-Time Control</h3>
                        <p class="text-white text-opacity-80">Easily manage live shows, tracks, and team access in real time.</p>
                    </div>

                    <div class="bg-white bg-opacity-10 p-6 rounded-xl backdrop-blur-sm">
                        <div class="text-4xl mb-4">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">User-Friendly Interface</h3>
                        <p class="text-white text-opacity-80">Designed for all skill levels, with a clean dashboard and intuitive navigation.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- About Us Section -->
    <section id="about" class="bg-white py-16">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2">
                    <img src="{{ asset('images/radioflow-logo-white.png')}}"
                        alt="RadioFlow team"
                        class="rounded-lg shadow-xl w-full">
                </div>
                <div class="lg:w-1/2">
                    <h2 class="text-3xl font-bold text-primary mb-6">About Us</h2>
                    <p class="text-gray-600 mb-4">
                        RadioFlow is a smart and innovative web application designed to streamline the daily operations of radio stations by organizing teams, tasks, and show planning — all in one place.

                        Launched in 2025 and proudly developed by Siwar Bouhalwen, who also serves as our CEO, RadioFlow empowers every department in a radio station — from hosts (animateurs/animatrices) to technical teams, digital creators, production crews, administrative staff, and station admins — with the right tools to collaborate efficiently and create high-quality content. </p>

                    <div class="flex flex-wrap gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-600">Role-Based Access & Permissions</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-600"> Smart Scheduling System</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-600">Support Available</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="bg-gray-50 py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-primary mb-12">Contact Us</h2>

            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
                <div class="md:flex">
                    <!-- Map Section -->
                    <div class="md:w-1/2">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6360.57480700761!2d9.99262335!3d37.1458651!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12e2dc1dc1d5875d%3A0xb57dedf69eed50dd!2sAl%20Khitmin!5e0!3m2!1sfr!2stn!4v1753196275401!5m2!1sfr!2stn"
                            width="100%"
                            height="100%"
                            style="min-height: 300px; border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>

                    <!-- Contact Info -->
                    <div class="md:w-1/2 p-8 bg-primary text-white">
                        <h3 class="text-xl font-semibold mb-6">Our Contact Information</h3>

                        <div class="space-y-6">
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt mt-1 mr-4 text-lg"></i>
                                <div>
                                    <h4 class="font-semibold">Address</h4>
                                    <p>7081 Alia , khitmine <br>Bizerte</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <i class="fas fa-phone-alt mt-1 mr-4 text-lg"></i>
                                <div>
                                    <h4 class="font-semibold">Phone</h4>
                                    <p>+216 56 502 592</p>
                                    <p class="text-sm opacity-80">Monday-Friday, 9am-5pm EST</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <i class="fas fa-envelope mt-1 mr-4 text-lg"></i>
                                <div>
                                    <h4 class="font-semibold">Email</h4>
                                    <p>contact@radioflow.com</p>
                                    <p class="text-sm opacity-80">Support: support@radioflow.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <div class="bg-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-primary mb-6">Ready to Take Your Radio Station to the Next Level?</h2>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                Manage your entire station with ease using RadioFlow
                <br> the smart solution built for modern broadcasters.
            </p>
            <a href="{{ route('radio-demand.create') }}" class="btn-primary px-8 py-3 rounded-lg font-medium text-lg transition duration-200 inline-block">
                Get Started for Free
            </a>
        </div>
    </div>
    <!-- Footer -->
    @include('layouts.footer')
</body>

</html>