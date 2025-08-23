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
    <main>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Request Your Radio Station Space</h1>

                <form action="{{ route('radio-demand.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Radio Station Info -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 text-gray-700">Radio Station Information</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="radio_name" class="block text-sm font-medium text-gray-700 mb-1">Radio Name *</label>
                                <input type="text" id="radio_name" name="radio_name" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="founding_date" class="block text-sm font-medium text-gray-700 mb-1">Founding Date *</label>
                                <input type="date" id="founding_date" name="founding_date" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                                <textarea id="description" name="description" rows="3" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>

                            <div>
                                <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                                <input type="file" id="logo" name="logo" accept="image/*"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Manager Info -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 text-gray-700">Manager Information</h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="manager_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" id="manager_name" name="manager_name" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="manager_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" id="manager_email" name="manager_email" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="manager_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                                <input type="tel" id="manager_phone" name="manager_phone" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Team Members -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 text-gray-700">Team Members (Minimum 5)</h2>

                        <div id="team-members-container">
                            <!-- Team member fields will be added here by JavaScript -->
                        </div>

                        <button type="button" id="add-team-member" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Add Team Member
                        </button>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Please ensure all information is accurate as this will be our only way to contact you.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('team-members-container');
                const addButton = document.getElementById('add-team-member');
                let memberCount = 0;

                function addTeamMemberField() {
                    memberCount++;
                    const memberDiv = document.createElement('div');
                    memberDiv.className = 'team-member bg-gray-50 p-4 rounded-md mb-4';
                    memberDiv.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="team_members_${memberCount}_name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="team_members[${memberCount}][name]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="team_members_${memberCount}_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="team_members[${memberCount}][email]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="team_members_${memberCount}_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                    <input type="tel" name="team_members[${memberCount}][phone]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="team_members_${memberCount}_role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select name="team_members[${memberCount}][role]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="button" class="mt-2 text-sm text-red-600 hover:text-red-800 remove-member">Remove</button>
        `;

                    container.appendChild(memberDiv);

                    // Add event listener to remove button
                    memberDiv.querySelector('.remove-member').addEventListener('click', function() {
                        if (document.querySelectorAll('.team-member').length > 5) {
                            container.removeChild(memberDiv);
                        } else {
                            alert('You must have at least 5 team members.');
                        }
                    });
                }

                // Add initial 5 team members
                for (let i = 0; i < 5; i++) {
                    addTeamMemberField();
                }

                // Add more team members when button is clicked
                addButton.addEventListener('click', addTeamMemberField);
            });
        </script>

</html>