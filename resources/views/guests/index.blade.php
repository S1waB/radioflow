@extends('layouts.app')

@section('title', $radio->name . ' Guests')

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6" x-data="{ createGuestModal: false, editGuestModal: false, deleteGuestModal: false, guestData: {} }">

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold" style="color:#0a2164">{{ $radio->name }} - Guests</h1>
        <button @click="createGuestModal = true"
            class="bg-[#0a2164] hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
            + Add Guest
        </button>
    </div>

    <!-- Search -->
    <form method="GET" action="{{ route('radios.guests.index', $radio) }}" class="mb-4 flex space-x-2">
        <input type="text" name="search" value="{{ $search }}"
            placeholder="Search guests..."
            class="border rounded px-3 py-2 w-full">
        <button type="submit" class="bg-[#0a2164] text-white px-4 py-2 rounded">Search</button>
    </form>

    <!-- Guests Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="px-4 py-2">Photo</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Phone</th>
                    <th class="px-4 py-2">Address</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guests as $guest)
                <tr class="border-b">
                    <td class="px-4 py-2">
                        @if($guest->profile_photo)
                        <img src="{{ asset('storage/'.$guest->profile_photo) }}" class="w-10 h-10 rounded-full object-cover">

                        @else
                        <div class="h-10 w-10 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center font-semibold text-sm uppercase">
                            {{ strtoupper(substr($guest->first_name, 0, 1)) }}
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $guest->first_name }} {{ $guest->last_name }}</td>
                    <td class="px-4 py-2">{{ $guest->email }}</td>
                    <td class="px-4 py-2">{{ $guest->phone_number ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $guest->address ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $guest->description ?? '-' }}</td>
                    <td class="px-4 py-2 flex space-x-2">
                        <!-- Edit -->
                        <button
                            @click="editGuestModal = true; guestData = {{ $guest->toJson() }}"
                            class="text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-edit"></i>
                        </button>
                        <!-- Delete -->
                        <button
                            @click="deleteGuestModal = true; guestData = {{ $guest->toJson() }}"
                            class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-2 text-center text-gray-500">No guests found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $guests->links() }}
    </div>

    <!-- Create Modal -->
    <div x-show="createGuestModal" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
            <h2 class="text-lg font-bold mb-4">Add Guest</h2>
            <form method="POST" action="{{ route('radios.guests.store', $radio) }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="first_name" placeholder="First Name" class="border p-2 rounded" required>
                    <input type="text" name="last_name" placeholder="Last Name" class="border p-2 rounded" required>
                    <input type="email" name="email" placeholder="Email" class="border p-2 rounded" required>
                    <input type="text" name="phone_number" placeholder="Phone" class="border p-2 rounded">
                    <input type="text" name="address" placeholder="Address" class="border p-2 rounded col-span-2">
                    <textarea name="description" placeholder="Description" class="border p-2 rounded col-span-2"></textarea>
                    <input type="file" name="profile_photo" class="col-span-2">
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" @click="createGuestModal=false" class="px-4 py-2 border rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#0a2164] text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="editGuestModal" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
            <h2 class="text-lg font-bold mb-4">Edit Guest</h2>
            <form method="POST" :action="`{{ route('radios.guests.update', [$radio->id, 'guestId']) }}`.replace('guestId', guestData.id)"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="first_name" x-model="guestData.first_name" class="border p-2 rounded" required>
                    <input type="text" name="last_name" x-model="guestData.last_name" class="border p-2 rounded" required>
                    <input type="email" name="email" x-model="guestData.email" class="border p-2 rounded" required>
                    <input type="text" name="phone_number" x-model="guestData.phone_number" class="border p-2 rounded">
                    <input type="text" name="address" x-model="guestData.address" class="border p-2 rounded col-span-2">
                    <textarea name="description" x-model="guestData.description" class="border p-2 rounded col-span-2"></textarea>
                    <input type="file" name="profile_photo" class="col-span-2">
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" @click="editGuestModal=false" class="px-4 py-2 border rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#0a2164] text-white rounded">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-show="deleteGuestModal" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <h2 class="text-lg font-bold mb-4">Delete Guest</h2>
            <p>
                Are you sure you want to delete
                <span class="font-bold" x-text="guestData.first_name + ' ' + guestData.last_name"></span>?
            </p>

            <form method="POST"
                :action="`{{ route('radios.guests.destroy', [$radio->id, 'guestId']) }}`.replace('guestId', guestData.id)">
                @csrf
                @method('DELETE')

                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" @click="deleteGuestModal=false" class="px-4 py-2 border rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
                </div>
            </form>
        </div>
    </div>


</div>
@endsection