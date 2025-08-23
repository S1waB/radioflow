@extends('layouts.app')

@section('title', $radio->name)

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold" style="color: #0a2164">{{ $radio->name }}</h1>
        <div class="flex space-x-2">
            @can('update', $radio)
                <a href="{{ route('radios.edit', $radio) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-2"></i> Edit Radio
                </a>
            @endcan
            <a href="{{ route('radios.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Radio Details Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="md:flex">
            <!-- Logo Column -->
            <div class="md:w-1/4 p-6 flex justify-center">
                <div class="w-48 h-48 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                    @if($radio->logo_path)
                        <img src="{{ asset('storage/' . $radio->logo_path) }}" alt="{{ $radio->name }} logo" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-radio text-blue-600 text-5xl"></i>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Details Column -->
            <div class="md:w-3/4 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Description</h3>
                        <p class="text-gray-600">{{ $radio->description ?? 'No description provided' }}</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Country</h3>
                        <p class="text-gray-600">{{ $radio->Country }}</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Phone Number</h3>
                        <p class="text-gray-600">{{ $radio->phone_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Address</h3>
                        <p class="text-gray-600">{{ $radio->address ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Manager</h3>
                        <p class="text-gray-600">
                            {{ $radio->manager->name ?? 'N/A' }}
                            @if($radio->manager)
                                <span class="text-sm text-gray-500">({{ $radio->manager->email }})</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Status</h3>
                        <span class="px-3 py-1 rounded-full text-sm {{ $radio->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($radio->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Team Members</h2>
                <p class="text-sm text-gray-600">Total active members: {{ $activeMembers->count() }}</p>
            </div>
            @can('update', $radio)
                <button onclick="document.getElementById('add-member-modal').classList.remove('hidden')" 
                        class="btn btn-primary">
                    <i class="fas fa-user-plus mr-2"></i> Add Member
                </button>
            @endcan
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($activeMembers as $member)
                <div class="px-6 py-4 flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden bg-gray-100">
                            @if($member->profile_photo_path)
                                <img src="{{ asset('storage/' . $member->profile_photo_path) }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-md font-medium text-gray-900">{{ $member->name }}</h4>
                            <p class="text-sm text-gray-500">
                                <span class="inline-block">
                                    <form id="role-form-{{ $member->id }}" action="{{ route('users.update-role', $member) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="role_id" onchange="document.getElementById('role-form-{{ $member->id }}').submit()" 
                                                class="text-sm border-0 bg-transparent p-0 focus:ring-0 focus:border-0 {{ $member->role ? 'text-gray-700' : 'text-gray-400' }}">
                                            <option value="" {{ !$member->role ? 'selected' : '' }}>No role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" {{ $member->role && $member->role->id == $role->id ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </span>
                                • {{ $member->email }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        @can('update', $member)
                            <a href="{{ route('users.edit', $member) }}" 
                               class="text-blue-600 hover:text-blue-900 text-sm font-medium"
                               title="Edit User">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endcan
                        
                        @can('update', $radio)
                            @if($member->id != $radio->manager_id)
                                <form action="{{ route('radios.remove-member', $radio) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $member->id }}">
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 text-sm font-medium"
                                            onclick="return confirm('Are you sure you want to remove this member?')"
                                            title="Remove Member">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </div>
            @empty
                <div class="px-6 py-4 text-center text-gray-500">
                    No active team members found.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Member Modal -->
@can('update', $radio)
<div id="add-member-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Add Team Member</h3>
            <button onclick="document.getElementById('add-member-modal').classList.add('hidden')" 
                    class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button type="button" id="existing-tab" 
                            class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm border-primary text-primary">
                        Existing User
                    </button>
                    <button type="button" id="new-tab" 
                            class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        New User
                    </button>
                </nav>
            </div>
            
            <!-- Tab Contents -->
            <div>
                <!-- Existing User Tab -->
                <div id="existing-tab-content">
                    <form action="{{ route('radios.add-member', $radio) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- Inactive members (previously part of this radio) -->
                        @if($inactiveMembers->isNotEmpty())
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Former Members</label>
                                <select name="user_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                    <option value="">Select a former member</option>
                                    @foreach($inactiveMembers as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        
                        <!-- Available users (not in any radio) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Available Users</label>
                            <select name="user_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                <option value="">Select an available user</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex justify-end space-x-2">
                            <button type="button" 
                                    onclick="document.getElementById('add-member-modal').classList.add('hidden')" 
                                    class="btn btn-outline">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus mr-2"></i> Add Member
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- New User Tab -->
                <div id="new-tab-content" class="hidden">
                    <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="radio_id" value="{{ $radio->id }}">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select name="role_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex justify-end space-x-2">
                            <button type="button" 
                                    onclick="document.getElementById('add-member-modal').classList.add('hidden')" 
                                    class="btn btn-outline">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab switching functionality
    document.getElementById('existing-tab').addEventListener('click', function() {
        document.getElementById('existing-tab').classList.add('border-primary', 'text-primary');
        document.getElementById('existing-tab').classList.remove('border-transparent', 'text-gray-500');
        document.getElementById('new-tab').classList.add('border-transparent', 'text-gray-500');
        document.getElementById('new-tab').classList.remove('border-primary', 'text-primary');
        
        document.getElementById('existing-tab-content').classList.remove('hidden');
        document.getElementById('new-tab-content').classList.add('hidden');
    });
    
    document.getElementById('new-tab').addEventListener('click', function() {
        document.getElementById('new-tab').classList.add('border-primary', 'text-primary');
        document.getElementById('new-tab').classList.remove('border-transparent', 'text-gray-500');
        document.getElementById('existing-tab').classList.add('border-transparent', 'text-gray-500');
        document.getElementById('existing-tab').classList.remove('border-primary', 'text-primary');
        
        document.getElementById('new-tab-content').classList.remove('hidden');
        document.getElementById('existing-tab-content').classList.add('hidden');
    });
</script>
@endcan

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

@if(session('error'))
    <div x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-4"
        class="fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center justify-between max-w-md">
        <span>{{ session('error') }}</span>
        <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif
@endsection