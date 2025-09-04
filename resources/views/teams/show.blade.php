@extends('layouts.app')

@section('title', $team->name)

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6">
    <!-- Team Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold" style="color: #0a2164">{{ $team->name }}</h1>
            <p class="text-gray-600">{{ $team->radio->name }}</p>
        </div>
        <div class="flex space-x-2">
            <!-- Edit Team Button with Modal -->
            <div x-data="{ editTeamModal: false }">
                <button @click="editTeamModal = true" class="btn btn-primary">
                    <i class="fas fa-edit mr-2"></i> Edit Team
                </button>

                <!-- Edit Team Modal -->
                <div x-show="editTeamModal" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                    <div class="bg-white w-11/12 md:w-2/3 lg:w-1/2 rounded-md shadow-lg p-6 relative">
                        <button @click="editTeamModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>

                        <h3 class="text-lg font-semibold mb-4">Edit Team</h3>
                        <form action="{{ route('teams.update', $team) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Team Name *</label>
                                <input type="text" name="name" value="{{ $team->name }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">{{ $team->description }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text gray-700 mb-1">Radio</label>
                                <select name="radio_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                    @foreach(\App\Models\Radio::all() as $radio)
                                    <option value="{{ $radio->id }}" {{ $team->radio_id == $radio->id ? 'selected' : '' }}>{{ $radio->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="editTeamModal = false" class="btn btn-outline">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Team</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <a href="{{ route('radios.show', $team->radio) }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Radio
            </a>

            <!-- Button to view all tasks for this team -->
            <a href="{{ route('tasks.index', ['team_id' => $team->id]) }}" class="btn btn-primary">
                <i class="fas fa-tasks mr-2"></i> View All Tasks
            </a>
        </div>
    </div>

    <!-- Team Details Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Description</h3>
                    <p class="text-gray-600">{{ $team->description ?? 'No description provided' }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Team Details</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Created:</span> {{ $team->created_at->format('M d, Y') }}</p>
                        <p><span class="font-medium">Total Members:</span> {{ $team->users->count() }}</p>
                        <p><span class="font-medium">Active Members:</span> {{ $team->users->where('status', 'active')->count() }}</p>
                        <p><span class="font-medium">Total Tasks:</span> {{ $team->tasks->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search for Members -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="relative">
                    <input type="text" id="memberSearch" placeholder="Search members..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                </div>

                <select id="statusFilter" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="desactive">Inactive</option>
                </select>

                <select id="roleFilter" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">All Roles</option>
                    @foreach(\App\Models\Role::all() as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div x-data="{ addMemberModal: false }">
                <button @click="addMemberModal = true" class="btn btn-primary">
                    <i class="fas fa-user-plus mr-2"></i> Add Member
                </button>

                <!-- Add Member Modal -->
                <div x-show="addMemberModal" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                    <div class="bg-white w-11/12 md:w-1/2 lg:w-1/3 rounded-md shadow-lg p-6 relative">
                        <button @click="addMemberModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>

                        <h3 class="text-lg font-semibold mb-4">Add Member to Team</h3>
                        <form action="{{ route('teams.add-member', $team) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Select Member</label>
                                <select name="user_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                    <option value="">Choose a member</option>
                                    @foreach($availableMembers as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="addMemberModal = false" class="btn btn-outline">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Member</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Team Members ({{ $team->users->count() }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasks Assigned</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="membersTable">
                    @forelse($team->users as $user)
                    <tr class="member-row" data-status="{{ $user->status }}" data-role="{{ $user->role_id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('/default-user.png') }}" alt="{{ $user->name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->role->name ?? 'No role' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->tasks->where('team_id', $team->id)->count() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <!-- Edit User Button with Modal -->
                                <div x-data="{ editUserModal: false }">
                                    <button @click="editUserModal = true" class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Edit User Modal -->
                                    <div x-show="editUserModal" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                                        <div class="bg-white w-11/12 md:w-2/3 lg:w-1/2 rounded-md shadow-lg p-6 relative">
                                            <button @click="editUserModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                                                <i class="fas fa-times"></i>
                                            </button>

                                            <h3 class="text-lg font-semibold mb-4">Edit User: {{ $user->name }}</h3>
                                            <form action="{{ route('users.update', $user) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                                        <input type="text" name="name" value="{{ $user->name }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                                        <input type="email" name="email" value="{{ $user->email }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                                        <select name="role_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                            @foreach(\App\Models\Role::all() as $role)
                                                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                                        <select name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                            <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="desactive" {{ $user->status === 'desactive' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                                        <input type="text" name="phone_number" value="{{ $user->phone_number }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Radio</label>
                                                        <select name="radio_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                            <option value="">No Radio</option>
                                                            @foreach(\App\Models\Radio::all() as $radio)
                                                            <option value="{{ $radio->id }}" {{ $user->radio_id == $radio->id ? 'selected' : '' }}>{{ $radio->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" @click="editUserModal = false" class="btn btn-outline">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update User</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Remove Member Form - FIXED (POST method instead of DELETE) -->
                                <form action="{{ route('teams.remove-member', $team) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Remove from team" onclick="return confirm('Remove {{ $user->name }} from this team?')">
                                        <i class="fas fa-user-times"></i>
                                    </button>
                                </form>

                                <!-- Status Change Toggle -->
                                <form action="{{ route('users.change-status', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                        class="{{ $user->status === 'active' ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }} ml-2"
                                        title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas {{ $user->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                </form>

                                <!-- Delete User Button -->
                                <form action="{{ route('users.destroy', $user) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete user" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No members in this team yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Team Tasks Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Recent Team Tasks</h3>

            <div x-data="{ createTaskModal: false }">
                <button @click="createTaskModal = true" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> New Task
                </button>

                <!-- Create Task Modal -->
                <div x-show="createTaskModal" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                    <div class="bg-white w-11/12 md:w-2/3 lg:w-1/2 rounded-md shadow-lg p-6 relative">
                        <button @click="createTaskModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>

                        <h3 class="text-lg font-semibold mb-4">Create New Task</h3>
                        <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" id="taskForm">
                            @csrf
                            <input type="hidden" name="team_id" value="{{ $team->id }}">

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                <input type="text" name="title" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                                <textarea name="description" rows="3" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"></textarea>
                                @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Assign to</label>
                                    <select name="assigned_to" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                        <option value="">Unassigned</option>
                                        @foreach($team->users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                                    <input type="datetime-local" name="deadline" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                    @error('deadline')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- File Upload Section -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Documents</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="task_docs" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-light focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                                <span>Upload files</span>
                                                <input id="task_docs" name="task_docs[]" type="file" multiple class="sr-only">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLSX, JPG, PNG up to 10MB</p>
                                    </div>
                                </div>
                                <div id="file-list" class="mt-2 text-sm text-gray-500"></div>
                                @error('task_docs.*')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="createTaskModal = false" class="btn btn-outline">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="submitTaskBtn">Create Task</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($team->tasks->take(5) as $task)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ $task->description }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($task->assigned)
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="{{ $task->assigned->profile_photo_path ? asset('storage/' . $task->assigned->profile_photo_path) : asset('/default-user.png') }}" alt="{{ $task->assigned->name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $task->assigned->name }}</div>
                                </div>
                                @else
                                <div class="text-sm text-gray-500">Unassigned</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <!-- Status Dropdown -->
                            <form action="{{ route('tasks.update-status', $task) }}" method="POST">
                                @csrf
                               
                                <select name="status" class="border rounded px-2 py-1 text-xs 
            {{ $task->status === 'todo' ? 'bg-gray-100 text-gray-800' : '' }}
            {{ $task->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
            {{ $task->status === 'done' ? 'bg-green-100 text-green-800' : '' }}
            {{ $task->status === 'late' ? 'bg-red-100 text-red-800' : '' }}
            {{ $task->status === 'expired' ? 'bg-gray-800 text-gray-100' : '' }}"
                                    onchange="this.form.submit()">
                                    <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>Todo</option>
                                    <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Done</option>
                                    <option value="late" {{ $task->status === 'late' ? 'selected' : '' }}>Late</option>
                                    <option value="expired" {{ $task->status === 'expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('M d, Y H:i') : 'No deadline' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <!-- View Task Button -->
                                <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-900" title="View Task">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Edit Task Button -->
                                <a href="{{ route('tasks.edit', $task) }}" class="text-blue-600 hover:text-blue-900" title="Edit Task">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Delete Task Button -->
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this task?')" title="Delete Task">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No tasks assigned to this team yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($team->tasks->count() > 5)
        <div class="px-6 py-4 border-t border-gray-200 text-center">
            <a href="{{ route('tasks.index', ['team_id' => $team->id]) }}" class="text-primary hover:text-primary-light font-medium">
                View all {{ $team->tasks->count() }} tasks
            </a>
        </div>
        @endif
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

<script>
    // Filter functionality for members table
    document.addEventListener('DOMContentLoaded', function() {
        const memberSearch = document.getElementById('memberSearch');
        const statusFilter = document.getElementById('statusFilter');
        const roleFilter = document.getElementById('roleFilter');
        const memberRows = document.querySelectorAll('.member-row');

        function filterMembers() {
            const searchValue = memberSearch.value.toLowerCase();
            const statusValue = statusFilter.value;
            const roleValue = roleFilter.value;

            memberRows.forEach(row => {
                const name = row.querySelector('.text-sm.font-medium').textContent.toLowerCase();
                const status = row.getAttribute('data-status');
                const role = row.getAttribute('data-role');

                const matchesSearch = name.includes(searchValue);
                const matchesStatus = statusValue === '' || status === statusValue;
                const matchesRole = roleValue === '' || role === roleValue;

                if (matchesSearch && matchesStatus && matchesRole) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        memberSearch.addEventListener('input', filterMembers);
        statusFilter.addEventListener('change', filterMembers);
        roleFilter.addEventListener('change', filterMembers);

        // File upload preview
        document.getElementById('task_docs').addEventListener('change', function(e) {
            const fileList = document.getElementById('file-list');
            fileList.innerHTML = '';

            if (this.files.length > 0) {
                fileList.innerHTML = '<p class="font-medium">Selected files:</p>';
                Array.from(this.files).forEach(file => {
                    fileList.innerHTML += `<p class="truncate">• ${file.name} (${(file.size/1024/1024).toFixed(2)} MB)</p>`;
                });
            }
        });

        // Form validation for task creation
        document.getElementById('taskForm').addEventListener('submit', function(e) {
            const title = this.querySelector('input[name="title"]').value.trim();
            const description = this.querySelector('textarea[name="description"]').value.trim();

            if (!title) {
                e.preventDefault();
                alert('Please enter a task title');
                return false;
            }

            if (!description) {
                e.preventDefault();
                alert('Please enter a task description');
                return false;
            }
        });

        // Add loading indicator for status changes
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {
                this.disabled = true;
                this.form.submit();
            });
        });
    });
</script>

@endsection