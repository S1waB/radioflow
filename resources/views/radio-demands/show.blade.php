@extends('layouts.app')

@section('content')
@include('layouts.header')
@section('title', 'Radio Demand Details')

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">{{ $demand->radio_name }}</h2>
                <span class="px-3 py-1 text-sm rounded-full {{ $demand->status_badge }}">
                    {{ Str::title(str_replace('_', ' ', $demand->status)) }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Submitted on {{ $demand->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>

        <div class="p-6">
            <!-- Radio Station Info -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Radio Station Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="flex items-center space-x-4 mb-6">
                            @if($demand->logo_path)
                            <div class="flex-shrink-0 h-20 w-20">
                                <img class="h-20 w-20 rounded-md" src="{{ asset('storage/' . $demand->logo_path) }}" alt="{{ $demand->radio_name }} logo">
                            </div>
                            @endif
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">{{ $demand->radio_name }}</h4>
                                <p class="text-sm text-gray-500">Founded: {{ $demand->founding_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                        
                        <div class="prose max-w-none text-sm text-gray-500">
                            <h5 class="text-sm font-medium text-gray-700">Description:</h5>
                            <p class="mt-1">{{ $demand->description }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Manager Information</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Name</p>
                                    <p class="text-sm">{{ $demand->manager_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Email</p>
                                    <p class="text-sm">{{ $demand->manager_email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Phone</p>
                                    <p class="text-sm">{{ $demand->manager_phone }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Team Members ({{ count(json_decode($demand->team_members, true)) }})</h3>
                
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Phone</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach(json_decode($demand->team_members, true) as $member)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $member['name'] }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $member['email'] }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $member['phone'] }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $member['role'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Admin Actions -->
            <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('radio-demands.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
                
                <!-- Status Update Dropdown -->
                <div class="relative inline-block text-left">
                    <div>
                        <button type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="status-menu" aria-expanded="false" aria-haspopup="true">
                            Update Status
                            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="status-menu">
                        <div class="py-1" role="none">
                            <form action="{{ route('radio-demands.update-status', $demand) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" name="status" value="pending" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">Pending</button>
                                <button type="submit" name="status" value="in_process" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">In Process</button>
                                <button type="submit" name="status" value="approved" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">Approved</button>
                                <button type="submit" name="status" value="rejected" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">Rejected</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('radio-demands.destroy', $demand) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this demand?')">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide status dropdown
document.getElementById('status-menu').addEventListener('click', function() {
    const dropdown = this.nextElementSibling;
    dropdown.classList.toggle('hidden');
});

// Close dropdown when clicking outside
window.addEventListener('click', function(e) {
    if (!e.target.matches('#status-menu')) {
        const dropdown = document.querySelector('[role="menu"]');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
        }
    }
});
</script>
@endsection