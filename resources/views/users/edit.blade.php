@extends('layouts.app')
@section('content')
@include('layouts.header')
@section('title', 'Edit User')

<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Edit User: {{ $user->name }}</h2>
            </div>

            <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="px-6 py-4 space-y-6">
                    <!-- Profile Photo Section -->
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 h-16 w-16 rounded-full overflow-hidden bg-gray-200">
                            @if($user->profile_photo_path)
                            <img id="profile-photo-preview" class="h-full w-full object-cover"
                                src="{{ asset('storage/'. $user->profile_photo_path) }}" alt="Profile photo">
                            @else
                            <div id="profile-photo-preview" class="h-full w-full bg-gray-300 flex items-center justify-center">
                                <span class="text-gray-500 text-xs">No photo</span>
                            </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Profile Photo</label>
                            <input type="file" name="profile_photo" id="profile-photo" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0">
                            @error('profile_photo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Group 1: Name, Email, Role -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700">Role *</label>
                            <select name="role_id" id="role_id" required
                                class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Group 2: Radio, Phone, Status -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="radio_id" class="block text-sm font-medium text-gray-700">Radio Station</label>
                            <select name="radio_id" id="radio_id"
                                class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                <option value="">Select Radio Station</option>
                                @foreach($radios as $radio)
                                <option value="{{ $radio->id }}" {{ old('radio_id', $user->radio_id) == $radio->id ? 'selected' : '' }}>{{ $radio->name }}</option>
                                @endforeach
                            </select>
                            @error('radio_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            @error('phone_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-2 flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="active" {{ old('status', $user->status) === 'active' ? 'checked' : '' }}
                                        class="focus:ring-primary text-primary border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="inactive" {{ old('status', $user->status) === 'inactive' ? 'checked' : '' }}
                                        class="focus:ring-primary text-primary border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Inactive</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Bio (full width) -->
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                        <textarea name="bio" id="bio" rows="3"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>


                <div class="px-6 py-3 bg-gray-50 text-right">
                    <a href="{{ route('users.index') }}" class="btn btn-outline mr-2">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('profile-photo').addEventListener('change', function(e) {
        const preview = document.getElementById('profile-photo-preview');
        const file = e.target.files[0];

        if (file) {
            if (file.size > 2048 * 1024) { // 2MB in KB
                alert('File is too large! Max 2MB allowed.');
                this.value = ''; // Clear the file input
                return;
            }

            if (!file.type.match('image.*')) {
                alert('Please select an image file');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                // Create new image element if preview was a div
                if (preview.tagName === 'DIV') {
                    const newImg = document.createElement('img');
                    newImg.id = 'profile-photo-preview';
                    newImg.className = 'h-full w-full object-cover';
                    newImg.src = event.target.result;
                    preview.parentNode.replaceChild(newImg, preview);
                } else {
                    preview.src = event.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>

@endsection