@extends('layouts.app')

@section('title', $radio->name)

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6">

    <!-- Radio Header -->
    <div class="flex flex-wrap items-center justify-between mb-6 gap-2">
        <h1 class="text-2xl font-bold text-[#0a2164]">{{ $radio->name }}</h1>

        <div class="flex flex-wrap gap-2">
            <!-- Edit Radio -->
            <a href="{{ route('radios.edit', $radio) }}" class="btn btn-primary flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit Radio
            </a>

            <!-- Back to List -->
            <a href="{{ route('radios.index') }}" class="btn btn-outline flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>

            <a href="{{ route('radios.emissions.index', ['radio' => $radio->id]) }}"
                class="btn btn-outline flex items-center">
                <i class="fas fa-microphone mr-2"></i> Emissions
            </a>


            <!-- View All Guests -->
            <a href="{{ route('radios.guests.index', $radio) }}" class="btn btn-outline flex items-center">
                <i class="fas fa-user-friends mr-2"></i> Guests
            </a>
            <!-- View All Songs -->
            <a href="{{ route('radios.songs.index', $radio) }}" class="btn btn-outline flex items-center">
                <i class="fas fa-music mr-2"></i> Songs
            </a>
            <!-- View Teams Modal -->
            <div x-data="{ viewTeamsModal: false }" class="relative">
                <button @click="viewTeamsModal = true" class="btn btn-outline flex items-center">
                    <i class="fas fa-users mr-2"></i> View Teams
                </button>

                <div x-show="viewTeamsModal" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                    <div class="bg-white w-11/12 md:w-3/4 lg:w-2/3 rounded-md shadow-lg p-6 relative max-h-96 overflow-y-auto">
                        <button @click="viewTeamsModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>

                        <h3 class="text-lg font-semibold mb-4">Teams in {{ $radio->name }}</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($radio->teams as $team)
                            <div class="border rounded-lg p-4">
                                <h4 class="font-semibold text-lg mb-2">
                                    <a href="{{ route('teams.show', $team) }}" class="text-blue-600 hover:underline">
                                        {{ $team->name }}
                                    </a>
                                </h4>
                                <p class="text-gray-600 text-sm mb-3">{{ $team->description ?? 'No description' }}</p>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-blue-600 font-medium">{{ $team->users_count ?? $team->users()->count() }} members</span>
                                    <span class="text-gray-500">Created: {{ $team->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if($radio->teams->count() === 0)
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-users text-4xl mb-3"></i>
                            <p>No teams created yet for this radio station.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- View Roles -->
            <a href="{{ route('roles.index', ['radio_id' => $radio->id]) }}" class="btn btn-outline flex items-center">
                <i class="fas fa-id-badge mr-2"></i> View Roles
            </a>
        </div>
    </div>


    <!-- Radio Details Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="md:flex">
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
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-700">Teams</h3>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($radio->teams as $team)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                {{ $team->name }} ({{ $team->users_count ?? $team->users()->count() }})
                            </span>
                            @endforeach
                            @if($radio->teams->count() === 0)
                            <span class="text-gray-500">No teams created yet</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">

        <!-- Filters  -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-4 space-y-4 md:space-y-0">
            <form method="GET" action="{{ route('radios.show', $radio) }}" class="flex flex-wrap items-center space-y-2 md:space-y-0 md:space-x-2 w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name/email..." class="rounded-md border-gray-300 shadow-sm p-2 w-full md:w-auto flex-grow">

                <select name="role" class="rounded-md border-gray-300 shadow-sm p-2 w-full md:w-auto">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="rounded-md border-gray-300 shadow-sm p-2 w-full md:w-auto">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="desactive" {{ request('status') == 'desactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <div class="flex space-x-2 w-full md:w-auto">
                    <button type="submit" class="btn btn-primary w-full md:w-auto">
                        <i class="fas fa-search mr-2"></i> Filter
                    </button>
                    <a href="{{ route('radios.show', $radio) }}" class="btn btn-outline w-full md:w-auto">
                        <i class="fas fa-sync-alt mr-2"></i> Reset
                    </a>
                </div>
            </form>


            <div class="flex flex-wrap gap-2 w-full md:w-auto justify-start md:justify-end">
                <!-- Create Team Button -->
                <div x-data="{ createTeamModal: false }" class="w-full sm:w-auto">
                    <button @click="createTeamModal = true" class="btn btn-primary w-full">
                        <i class="fas fa-plus mr-2"></i> Create Team
                    </button>

                    <!-- Create Team Modal -->
                    <div x-show="createTeamModal" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                        <div class="bg-white w-11/12 md:w-1/2 lg:w-1/3 rounded-md shadow-lg p-6 relative">
                            <button @click="createTeamModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times"></i>
                            </button>

                            <h3 class="text-lg font-semibold mb-4">Create New Team</h3>
                            <form action="{{ route('teams.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="radio_id" value="{{ $radio->id }}">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Team Name *</label>
                                    <input type="text" name="name" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"></textarea>
                                </div>

                                <div class="flex justify-end space-x-2">
                                    <button type="button" @click="createTeamModal = false" class="btn btn-outline">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i> Create Team
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div x-data="{ addMemberModal: false }">

                    <!-- Button to open modal -->
                    <button @click="addMemberModal = true"
                        class="btn btn-primary w-full">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Member
                    </button>

                    <!-- Modal -->
                    <div x-show="addMemberModal"
                        x-transition
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-lg relative">
                            <!-- Close button -->
                            <button @click="addMemberModal = false"
                                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>

                            <h2 class="text-xl font-bold mb-4">Add New Member</h2>

                            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="radio_id" value="{{ $team->radio_id }}">
                                <input type="hidden" name="teams[]" value="{{ $team->id }}">

                                <div class="mb-3">
                                    <label class="block font-medium">Name</label>
                                    <input type="text" name="name" class="w-full border px-3 py-2 rounded-md" required>
                                </div>

                                <div class="mb-3">
                                    <label class="block font-medium">Email</label>
                                    <input type="email" name="email" class="w-full border px-3 py-2 rounded-md" required>
                                </div>

                                <div class="mb-3 grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block font-medium">Password</label>
                                        <input type="password" name="password" class="w-full border px-3 py-2 rounded-md" required>
                                    </div>
                                    <div>
                                        <label class="block font-medium">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="w-full border px-3 py-2 rounded-md" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="block font-medium">Phone</label>
                                    <input type="text" name="phone_number" class="w-full border px-3 py-2 rounded-md">
                                </div>

                                <div class="mb-3">
                                    <label class="block font-medium">Role</label>
                                    <select name="role_id" class="w-full border px-3 py-2 rounded-md" required>
                                        @foreach($roles as $role)
                                        @if(!in_array($role->name, ['admin', 'superadmin']))
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="block font-medium">Status</label>
                                    <select name="status" class="w-full border px-3 py-2 rounded-md" required>
                                        <option value="active">Active</option>
                                        <option value="desactive">Desactive</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="block font-medium">Profile Photo</label>
                                    <input type="file" name="profile_photo" class="w-full">
                                </div>

                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="addMemberModal = false"
                                        class="btn btn-outline">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i> Create member
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>



            </div>
        </div>

        <!-- Team Members List -->
        <div class="divide-y divide-gray-200">
            @forelse($filteredMembers as $member)
            <div class="px-6 py-4 flex justify-between items-center" x-data="{ editModal: false }">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden bg-gray-100">
                        <img src="{{ $member->profile_photo_path ? asset('storage/' . $member->profile_photo_path) : asset('/default-user.png') }}" alt="{{ $member->name }}" class="h-full w-full object-cover" />
                    </div>
                    <div>
                        <h6 class="text-md font-medium text-gray-900">{{ $member->name }}</h6>
                        <p class="text-sm text-gray-500">
                            {{ $member->role->name ?? 'No role' }} • {{ $member->email }}
                        </p>
                        <!-- Display member's teams -->
                        @if($member->teams->count() > 0)
                        <div class="mt-1">
                            <span class="text-xs text-gray-400">Teams: </span>
                            @foreach($member->teams as $team)
                            <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full mr-1">
                                {{ $team->name }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex space-x-2">
                    <!-- Edit Member -->
                    <button type="button" @click="editModal = true" class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-edit" title="Edit"></i>
                    </button>

                    <!-- Toggle Status -->
                    <form action="{{ route('users.change-status', $member) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                            class="{{ $member->status === 'active' ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }} ml-2"
                            title="{{ $member->status === 'active' ? 'Deactivate' : 'Activate' }}">
                            <i class="fas {{ $member->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                        </button>
                    </form>

                    <!-- Delete Member -->
                    <form action="{{ route('users.destroy', $member) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-red-600 hover:text-red-900 ml-2"
                            onclick="return confirm('Are you sure you want to delete this user?')"
                            title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>

                <!-- Edit Modal -->
                <div x-show="editModal" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-start pt-20 z-50">
                    <div class="bg-white w-11/12 md:w-1/2 lg:w-1/3 rounded-md shadow-lg p-5 relative">
                        <button @click="editModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>

                        <h3 class="text-lg font-semibold mb-4">Edit Member</h3>

                        <form action="{{ route('users.update', $member) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" name="name" value="{{ $member->name }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ $member->email }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-gray-400 text-xs">(leave blank to keep current)</span></label>
                                <input type="password" name="password" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <select name="role_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                    <option value="">No role</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $member->role && $member->role->id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teams</label>
                                <select name="teams[]" multiple class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                    @foreach($radio->teams as $team)
                                    <option value="{{ $team->id }}" {{ $member->teams->contains($team->id) ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple teams</p>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="editModal = false" class="btn btn-outline">Cancel</button>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i> Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-4 text-center text-gray-500">No members found.</div>
            @endforelse
        </div>

    </div>
</div>

<!-- Session Messages -->
@if(session('success') || session('error'))
<div x-data="{ show: true }" x-show="show" x-transition class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center justify-between max-w-md">
    <span>{{ session('success') ?? session('error') }}</span>
    <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
        <i class="fas fa-times"></i>
    </button>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

@endsection