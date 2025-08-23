@extends('layouts.app')

@section('content')
@include('layouts.header')
@section('title', 'Radio Demands Management')

<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-2xl font-bold" style="color: #0a2164">Radio Station Demands</h1>
        <a href="{{ route('radios.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-white text-sm font-medium hover:transition-all shadow-md mt-4 md:mt-0" style="background-color: #0a2164">
            <i class="fas fa-radio mr-2"></i>
            View Radio Stations
        </a>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('radio-demands.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search Input -->
            <div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" placeholder="Search by radio name..."
                        value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_process" {{ request('status') == 'in_process' ? 'selected' : '' }}>In Process</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('radio-demands.index') }}" class="btn btn-outline flex-1">
                    <i class="fas fa-sync-alt mr-2"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Demands Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Radio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manager</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Founded</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($demands as $demand)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($demand->logo_path)
                                <div class="flex-shrink-0 h-10 w-10 mr-3">
                                    <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $demand->logo_path) }}" alt="">
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $demand->radio_name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($demand->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $demand->manager_name }}</div>
                            <div class="text-sm text-gray-500">{{ $demand->manager_email }}</div>
                            <div class="text-sm text-gray-500">{{ $demand->manager_phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if(is_array($demand->team_members))
                            {{ count($demand->team_members) }}
                            @else
                            {{ count(json_decode($demand->team_members, true)) }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $demand->founding_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $demand->status_badge }}">
                                {{ Str::title(str_replace('_', ' ', $demand->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center space-x-2">

                                <!-- View Button -->
                                <a href="{{ route('radio-demands.show', $demand) }}"
                                    class="text-blue-600 hover:text-blue-900"
                                    title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if(!$demand->isProcessed())
                                <!-- Status Update Form - Only show if not already approved/rejected -->
                                <form action="{{ route('radio-demands.update-status', $demand) }}" method="POST"
                                    class="flex items-center space-x-1"
                                    x-data="{ status: '{{ $demand->status }}' }"
                                    @submit.prevent="if (['approved', 'rejected'].includes(status)) { if (confirm('This will create accounts and cannot be undone. Proceed?')) { $el.submit(); } } else { $el.submit(); }">
                                    @csrf
                                    @method('PUT')
                                    <select name="status"
                                        x-model="status"
                                        class="border border-gray-300 rounded-md px-2 py-1 text-xs focus:outline-none"
                                        :class="{ 'bg-yellow-100': status === 'pending', 
                          'bg-blue-100': status === 'in_process',
                          'bg-green-100': status === 'approved',
                          'bg-red-100': status === 'rejected' }">
                                        <option value="pending" class="bg-yellow-100" {{ $demand->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_process" class="bg-blue-100" {{ $demand->status === 'in_process' ? 'selected' : '' }}>In Process</option>
                                        <option value="approved" class="bg-green-100" {{ $demand->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" class="bg-red-100" {{ $demand->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                    <button type="submit"
                                        class="bg-indigo-600 text-white px-2 py-1 rounded text-xs hover:bg-indigo-700"
                                        :class="{ 'bg-green-600': status === 'approved', 
                          'bg-red-600': status === 'rejected' }">
                                        Update
                                    </button>
                                </form>
                                @else
                                <!-- Show current status if already processed -->
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $demand->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $demand->status)) }}
                                    </span>
                                    <span class="text-xs text-gray-500">(Cannot be changed)</span>
                                </div>
                                @endif

                                <!-- Delete Button -->
                                <form action="{{ route('radio-demands.destroy', $demand) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Are you sure you want to delete this demand?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No radio demands found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $demands->appends(request()->query())->links() }}
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
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<script>
    // Show/hide status dropdown
    document.querySelectorAll('[id^="status-menu-"]').forEach(button => {
        button.addEventListener('click', function() {
            const dropdown = this.nextElementSibling;
            dropdown.classList.toggle('hidden');
        });
    });

    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        if (!e.target.matches('[id^="status-menu-"]')) {
            document.querySelectorAll('[role="menu"]').forEach(menu => {
                if (!menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection