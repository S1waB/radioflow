@extends('layouts.app')
@section('title', 'Create New User')
@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Create New User</h2>
            </div>
            
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="px-6 py-4 space-y-6">
                    <!-- Profile Photo -->
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 h-16 w-16 rounded-full overflow-hidden bg-gray-200">
                            <img id="profile-photo-preview" class="h-full w-full object-cover" src="{{ asset('images/default-user.png') }}" alt="Profile photo preview">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Profile Photo</label>
                            <input type="file" name="profile_photo" id="profile-photo" class="mt-1 block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        </div>
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                        <input type="password" name="password" id="password" required
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <!-- Radio -->
                    <div>
                        <label for="radio_id" class="block text-sm font-medium text-gray-700">Radio Station</label>
                        <select name="radio_id" id="radio_id"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select Radio Station</option>
                            @foreach($radios as $radio)
                                <option value="{{ $radio->id }}" {{ old('radio_id') == $radio->id ? 'selected' : '' }}>
                                    {{ $radio->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('radio_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700">Role *</label>
                        <select name="role_id" id="role_id" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" data-radio="{{ $role->radio_id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teams Assignment -->
                    <div>
                        <label for="teams" class="block text-sm font-medium text-gray-700">Assign to Teams</label>
                        <select name="teams[]" id="teams" multiple
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">First select a radio station</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" data-radio="{{ $team->radio_id }}" 
                                    {{ in_array($team->id, old('teams', [])) ? 'selected' : '' }} 
                                    style="display: none;">
                                    {{ $team->name }} ({{ $team->radio->name ?? 'No radio' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple teams</p>
                        @error('teams')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('phone_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                        <textarea name="bio" id="bio" rows="3"
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('bio') }}</textarea>
                        @error('bio')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center">
                                <input id="status-active" name="status" type="radio" value="active" 
                                       {{ old('status', 'active') === 'active' ? 'checked' : '' }}
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                <label for="status-active" class="ml-3 block text-sm font-medium text-gray-700">Active</label>
                            </div>
                            <div class="flex items-center">
                                <input id="status-desactive" name="status" type="radio" value="desactive"
                                       {{ old('status') === 'desactive' ? 'checked' : '' }}
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                <label for="status-desactive" class="ml-3 block text-sm font-medium text-gray-700">Inactive</label>
                            </div>
                        </div>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="px-6 py-3 bg-gray-50 text-right">
                    <a href="{{ route('users.index') }}" class="btn btn-outline mr-2">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Profile photo preview
    document.getElementById('profile-photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('profile-photo-preview').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Dynamic filtering for teams and roles based on selected radio
    document.addEventListener('DOMContentLoaded', function() {
        const radioSelect = document.getElementById('radio_id');
        const teamSelect = document.getElementById('teams');
        const roleSelect = document.getElementById('role_id');

        const teamOptions = teamSelect.querySelectorAll('option[data-radio]');
        const roleOptions = roleSelect.querySelectorAll('option[data-radio]');

        function filterOptions() {
            const selectedRadioId = radioSelect.value;

            // Filter Teams
            teamOptions.forEach(option => {
                if (option.value === '') return;
                if (selectedRadioId && option.getAttribute('data-radio') === selectedRadioId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                    option.selected = false;
                }
            });
            if (!selectedRadioId) {
                teamOptions.forEach(option => option.style.display = 'none');
                teamSelect.querySelector('option[value=""]').style.display = 'block';
            } else {
                teamSelect.querySelector('option[value=""]').style.display = 'none';
            }

            // Filter Roles
            roleOptions.forEach(option => {
                if (option.value === '') return;
                if (selectedRadioId && option.getAttribute('data-radio') === selectedRadioId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                    option.selected = false;
                }
            });
            if (!selectedRadioId) {
                roleOptions.forEach(option => option.style.display = 'none');
                roleSelect.querySelector('option[value=""]').style.display = 'block';
            } else {
                roleSelect.querySelector('option[value=""]').style.display = 'block';
            }
        }

        radioSelect.addEventListener('change', filterOptions);

        @if(old('radio_id'))
            filterOptions();
        @endif
    });
</script>
@endsection
