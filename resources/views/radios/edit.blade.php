@extends('layouts.app')
@section('content')
@include('layouts.header')
@section('title', 'Edit Radio Station')

<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-6" style="color: #0a2164">Edit Radio Station: {{ $radio->name }}</h2>

            <form action="{{ route('radios.update', $radio) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Logo -->
                    <div class="col-span-2">
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Radio Logo</label>
                        <div class="mt-1 flex items-center space-x-4">
                            <div class="flex-shrink-0 h-16 w-16 rounded-full overflow-hidden bg-gray-200">
                                @if($radio->logo_path)
                                <img id="logo-preview" class="h-full w-full object-cover"
                                    src="{{ asset('storage/' . $radio->logo_path) }}" alt="Radio Logo">
                                @else
                                <div id="logo-preview" class="h-full w-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-500 text-xs">No logo</span>
                                </div>
                                @endif
                            </div>
                            <label for="logo" class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                {{ $radio->logo_path ? 'Change Logo' : 'Upload Logo' }}
                            </label>
                            <input id="logo" name="logo" type="file" class="sr-only" accept="image/*">
                            @if($radio->logo_path)
                            <button type="button" onclick="removeLogo()" class="ml-3 text-sm text-red-600 hover:text-red-800">
                                Remove Logo
                            </button>
                            @endif
                        </div>
                        <input type="hidden" id="remove-logo" name="remove_logo" value="0">
                        @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- Name -->
                    <div class="col-span-1">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Radio Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $radio->name) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                            required>
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div class="col-span-1">
                        <label for="Country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                        <input type="text" name="Country" id="Country" value="{{ old('Country', $radio->Country) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                            required>
                        @error('Country')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="col-span-1">
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $radio->phone_number) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        @error('phone_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Manager -->
                    <div class="col-span-1">
                        <label for="manager_id" class="block text-sm font-medium text-gray-700 mb-1">Manager *</label>
                        <select name="manager_id" id="manager_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                            required>
                            <option value="">Select a Manager</option>
                            @foreach ($managers as $manager)
                            <option value="{{ $manager->id }}"
                                {{ old('manager_id', $radio->manager_id) == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('manager_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" name="address" id="address" value="{{ old('address', $radio->address) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">{{ old('description', $radio->description) }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-span-1">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select name="status" id="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                            required>
                            <option value="active" {{ old('status', $radio->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $radio->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('radios.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                        style="background-color: #0a2164">
                        Update Radio Station
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function removeLogo() {
        document.getElementById('remove-logo').value = '1';
        const preview = document.getElementById('logo-preview');
        preview.innerHTML = `<svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>`;
    }

    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('logo-preview');
                preview.innerHTML = `<img src="${event.target.result}" class="h-full w-full object-cover">`;
                document.getElementById('remove-logo').value = '0';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection

@endsection