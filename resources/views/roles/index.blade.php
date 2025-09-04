@extends('layouts.app')
@section('title', 'Role Management')
@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6">
    <!-- Header and Create Button -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Role Management</h1>
        <button data-modal-target="createRoleModal" data-modal-toggle="createRoleModal"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-md btn btn-primary text-white text-sm font-medium hover:bg-primary transition-all shadow-md mt-4 md:mt-0">
            <i class="fas fa-plus mr-1"></i> Add New Role
        </button>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('roles.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" placeholder="Search roles..."
                        value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            <!-- Hierarchy filter -->
            <div>
                <select name="level" class="block w-full py-2 pl-3 pr-10 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">All Levels</option>
                    @foreach(range(1,10) as $lvl)
                    <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>Level {{ $lvl }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-primary text-sm" style="background-color: #0a2164">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('roles.index') }}" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm">
                    <i class="fas fa-sync-alt mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Roles Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hierarchy Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Users</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($roles as $role)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $role->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $role->description ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $role->hierarchy_level }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $role->users->count() }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <button data-modal-target="editRoleModal-{{ $role->id }}" data-modal-toggle="editRoleModal-{{ $role->id }}"
                                    class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this role?')"
                                        class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div id="editRoleModal-{{ $role->id }}" tabindex="-1" aria-hidden="true"
                        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
                            <h2 class="text-xl font-bold mb-4">Edit Role</h2>
                            <form action="{{ route('roles.update', $role) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Role Name</label>
                                    <input type="text" name="name" value="{{ $role->name }}" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $role->description }}</textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Hierarchy Level</label>
                                    <input type="number" name="hierarchy_level" value="{{ $role->hierarchy_level }}" required min="1"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div class="flex justify-end space-x-2">
                                    <button type="button" data-modal-hide="editRoleModal-{{ $role->id }}"
                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                        Update
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No roles found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $roles->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createRoleModal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
        <h2 class="text-xl font-bold mb-4">Create New Role</h2>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            <!-- Hidden Radio ID -->
            <input type="hidden" name="radio_id" value="{{ request('radio_id') ?? $radio->id ?? '' }}">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Role Name</label>
                <input type="text" name="name" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Hierarchy Level</label>
                <input type="number" name="hierarchy_level" value="7" required min="1"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" data-modal-hide="createRoleModal"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Create
                </button>
            </div>
        </form>

    </div>
</div>

<!-- Toast -->
@if(session('success'))
<div x-data="{ show: true }" x-show="show"
    x-transition class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center justify-between max-w-md">
    <span>{{ session('success') }}</span>
    <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
        <i class="fas fa-times"></i>
    </button>
</div>
@endif

<!-- Flowbite (required for modals) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
@endsection