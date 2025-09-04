@extends('layouts.app')

@section('title', 'Radio Management')

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6">
    <!-- Header and Create Button -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-2xl font-bold" style="color: #0a2164">Radio Stations Management</h1>
        <div class="flex space-x-2 mt-4 md:mt-0">
            <a href="{{ route('radio-demands.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-white text-sm font-medium hover:transition-all shadow-md" style="background-color: #0a2164">
                <i class="fas fa-list mr-2"></i>
                View Demands
            </a>
            <a href="{{ route('radios.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-white text-sm font-medium hover:transition-all shadow-md" style="background-color: #0a2164">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Radio
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('radios.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" placeholder="Search by name, country, manager..."
                        value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="desactive" {{ request('status') == 'desactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('radios.index') }}" class="btn btn-outline flex-1">
                    <i class="fas fa-sync-alt mr-2"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Radios Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manager</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($radios as $radio)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden bg-gray-100">
                                @if($radio->logo_path)
                                <img src="{{ asset('storage/' . $radio->logo_path) }}" alt="{{ $radio->name }} logo" class="h-full w-full object-cover">
                                @else
                                <div class="h-full w-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-radio text-blue-600"></i>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $radio->name }}</div>
                            <div class="text-sm text-gray-500">{{ $radio->phone_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $radio->Country }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $radio->manager ? $radio->manager->name : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $radio->teams->count() }} <!-- Fixed: changed 'team' to 'teams' -->
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 text-xs rounded-full {{ $radio->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($radio->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <!-- View Button -->
                                <a href="{{ route('radios.show', $radio) }}"
                                    class="text-blue-600 hover:text-blue-900"
                                    title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Edit Button -->
                                <a href="{{ route('radios.edit', $radio) }}"
                                    class="text-blue-600 hover:text-blue-900"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Toggle Status Button -->
                                <form action="{{ route('radios.change-status', $radio) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                        class="{{ $radio->status === 'active' ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }}"
                                        title="{{ $radio->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas {{ $radio->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                </form>

                                <!-- Delete Button -->
                                <form action="{{ route('radios.destroy', $radio) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Are you sure you want to delete this radio station?')"
                                        title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500"> <!-- Fixed: colspan from 6 to 7 -->
                            No radio stations found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $radios->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Success Message Toast -->
@if(session('success'))
<div x-data="{ show: true }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-4"
    class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center justify-between max-w-md">
    <span>{{ session('success') }}</span>
    <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
        <i class="fas fa-times"></i>
    </button>
</div>
@endif
@endsection