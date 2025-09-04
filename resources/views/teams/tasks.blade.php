@extends('layouts.app')

@section('title', $team->name . ' - Tasks')

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6">
    <!-- Team Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold" style="color: #0a2164">{{ $team->name }} - Tasks</h1>
            <p class="text-gray-600">{{ $team->radio->name }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('teams.show', $team) }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Team
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="relative">
                    <input type="text" placeholder="Search tasks..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                
                <select class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">All Statuses</option>
                    <option value="todo">To Do</option>
                    <option value="pending">Pending</option>
                    <option value="done">Done</option>
                    <option value="late">Late</option>
                    <option value="expired">Expired</option>
                </select>
                
                <select class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">All Members</option>
                    @foreach($team->users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            
            @can('create', App\Models\Task::class)
            <div x-data="{ createTaskModal: false }">
                <button @click="createTaskModal = true" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> New Task
                </button>

                <!-- Create Task Modal -->
                <div x-show="createTaskModal" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                    <div class="bg-white w-11/12 md:w-2/3 lg:w-1/2 rounded-md shadow-lg p-6 relative">
                        <button @click="createTaskModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>

                        <h3 class="text-lg font-semibold mb-4">Create New Task</h3>
                        <form action="{{ route('tasks.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="team_id" value="{{ $team->id }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                    <input type="text" name="title" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                    <select name="priority" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"></textarea>
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
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                                    <input type="datetime-local" name="deadline" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Attachments</label>
                                <input type="file" name="task_docs[]" multiple class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="createTaskModal = false" class="btn btn-outline">Cancel</button>
                                <button type="submit" class="btn btn-primary">Create Task</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($team->tasks as $task)
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
                                    <div class="text-sm text-gray-500">{{ $task->assigned->email }}</div>
                                </div>
                                @else
                                <div class="text-sm text-gray-500">Unassigned</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $task->status === 'todo' ? 'task-todo' : '' }}
                                {{ $task->status === 'pending' ? 'task-pending' : '' }}
                                {{ $task->status === 'done' ? 'task-done' : '' }}
                                {{ $task->status === 'late' ? 'task-late' : '' }}
                                {{ $task->status === 'expired' ? 'task-expired' : '' }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('M d, Y H:i') : 'No deadline' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @can('update', $task)
                                <a href="{{ route('tasks.edit', $task) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                
                                @can('delete', $task)
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this task?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                                
                                <!-- Status Change Dropdown -->
                             
                                <div x-data="{ statusDropdown: false }" class="relative">
                                    <button @click="statusDropdown = !statusDropdown" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    
                                    <div x-show="statusDropdown" @click.away="statusDropdown = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1" role="menu" aria-orientation="vertical">
                                            <form action="{{ route('tasks.update', $task) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="todo">
                                                <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left" role="menuitem">Mark as To Do</button>
                                            </form>
                                            <form action="{{ route('tasks.update', $task) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left" role="menuitem">Mark as Pending</button>
                                            </form>
                                            <form action="{{ route('tasks.update', $task) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="done">
                                                <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left" role="menuitem">Mark as Done</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                              
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No tasks assigned to this team yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Team Members Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Team Members ({{ $team->users->count() }})</h3>
           
            <div x-data="{ addMemberModal: false }">
                <button @click="addMemberModal = true" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-plus mr-1"></i> Add Member
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

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Tasks</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($team->users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('/default-user.png') }}" alt="{{ $user->name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->role->name ?? 'No role' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->tasks->where('team_id', $team->id)->count() }} tasks
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @can('update', $team)
                                <form action="{{ route('teams.remove-member', $team) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Remove {{ $user->name }} from this team?')">
                                        <i class="fas fa-user-times"></i> Remove
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No members in this team yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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

@endsection