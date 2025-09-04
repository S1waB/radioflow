@extends('layouts.app')

@section('title', 'Tasks Management')

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold" style="color: #0a2164">Tasks Management</h1>
        <div x-data="{ createTaskModal: false }">
            <button @click="createTaskModal = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-2 text-white text-sm font-medium hover:transition-all shadow-md" style="background-color: #0a2164">
                <i class="fas fa-plus mr-2"></i> New Task
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
                        <input type="hidden" name="team_id" id="create-team-id" value="{{ request('team_id') ?? '' }}">

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
                            @if(request('team_id'))
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Team</label>
                                <p class="text-sm text-gray-600">
                                    {{ \App\Models\Team::find(request('team_id'))->name ?? 'Unknown Team' }}
                                </p>
                            </div>
                            @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Team (optional)</label>
                                <select name="team_id" id="create-team-select" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" onchange="updateTeamMembers('create', this.value)">
                                    <option value="">No Team</option>
                                    @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assign To *</label>
                                <select name="assigned_to" id="create-assigned-to-select" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                    <option value="">Select User</option>
                                    @if(request('team_id') && $team = \App\Models\Team::find(request('team_id')))
                                    @foreach($team->users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                    @else
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @error('assigned_to')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deadline (optional)</label>
                                <input type="datetime-local" name="deadline" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                @error('deadline')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                    <option value="todo">Todo</option>
                                    <option value="pending">Pending</option>
                                    <option value="done">Done</option>
                                    <option value="late">Late</option>
                                    <option value="expired">Expired</option>
                                </select>
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
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-2 text-white text-sm font-medium hover:transition-all shadow-md" style="background-color: #0a2164">
                                <i class="fas fa-plus mr-2"></i> Create Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="team_id" value="{{ request('team_id') }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tasks..." class="w-full border rounded-md p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border rounded-md p-2">
                    <option value="">All Statuses</option>
                    <option value="todo" {{ request('status') == 'todo' ? 'selected' : '' }}>Todo</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                <select name="assigned_to" class="w-full border rounded-md p-2">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4 flex justify-end space-x-2">
                <a href="{{ route('tasks.index') }}" class="btn btn-outline">Clear Filters</a>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-2 text-white text-sm font-medium hover:transition-all shadow-md" style="background-color: #0a2164">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider>Deadline</th>
                    <th scope=" col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tasks as $task)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($task->description, 50) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="text-sm font-medium text-gray-900">{{ $task->assigned->name }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $task->team->name ?? 'No Team' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form action="{{ route('tasks.update-status', $task) }}" method="POST">
                            @csrf
                            <select name="status" class="status-select border rounded px-2 py-1 text-xs 
                                {{ $task->status === 'todo' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $task->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $task->status === 'done' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $task->status === 'late' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $task->status === 'expired' ? 'bg-gray-800 text-gray-100' : '' }}"
                                onchange="this.form.submit()">
                                @php
                                $statusOrder = ['todo', 'pending', 'done', 'late', 'expired'];
                                $currentStatusIndex = array_search($task->status, $statusOrder);
                                @endphp

                                @foreach($statusOrder as $index => $status)
                                <option value="{{ $status }}"
                                    {{ $task->status === $status ? 'selected' : '' }}
                                    {{ $index < $currentStatusIndex ? 'disabled' : '' }}>
                                    {{ ucfirst($status) }}
                                    @if($index < $currentStatusIndex)
                                        (Not allowed)
                                        @endif
                                        </option>
                                        @endforeach
                            </select>
                        </form>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('M d, Y H:i') : 'No deadline' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <!-- View Button -->
                            <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                <i class="fas fa-eye"></i>
                            </a>

                            <!-- Edit Task Button with Modal -->
                            <div x-data="{ editTaskModal: false }">
                                <button @click="editTaskModal = true" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Edit Task Modal -->
                                <div x-show="editTaskModal" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                                    <div class="bg-white w-11/12 md:w-2/3 lg:w-1/2 rounded-md shadow-lg p-6 relative">
                                        <button @click="editTaskModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-times"></i>
                                        </button>

                                        <h3 class="text-lg font-semibold mb-4">Edit Task</h3>
                                        <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                                <input type="text" name="title" value="{{ $task->title }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                            </div>

                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                                                <textarea name="description" rows="3" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">{{ $task->description }}</textarea>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                <div>
                                                    <input type="hidden" name="team_id" value="{{ $task->team_id }}">
                                                    @if($task->team)
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Team</label>
                                                        <p class="text-sm text-gray-600">{{ $task->team->name }}</p>
                                                    </div>
                                                    @endif

                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Assign To *</label>
                                                    <select name="assigned_to" id="assigned-to-select" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                        <option value="">Select User</option>
                                                        <!-- Team members will be populated dynamically -->
                                                        @if($task->team && $task->team->users->count() > 0)
                                                        @foreach($task->team->users as $user)
                                                        <option value="{{ $user->id }}" {{ $task->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                        @endforeach
                                                        @else
                                                        <!-- Fallback to all users if no team is selected -->
                                                        @foreach($users as $user)
                                                        <option value="{{ $user->id }}" {{ $task->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                                                    <input type="datetime-local" name="deadline" value="{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d\TH:i') : '' }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                                    <select name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                                        <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>Todo</option>
                                                        <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Done</option>
                                                        <option value="late" {{ $task->status === 'late' ? 'selected' : '' }}>Late</option>
                                                        <option value="expired" {{ $task->status === 'expired' ? 'selected' : '' }}>Expired</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Add New Files</label>
                                                <input type="file" name="task_docs[]" multiple class="block w-full">
                                                <p class="text-xs text-gray-500 mt-1">Multiple files allowed (PDF, DOC, DOCX, XLSX, JPG, PNG)</p>
                                            </div>

                                            <div class="flex justify-end space-x-2">
                                                <button type="button" @click="editTaskModal = false" class="btn btn-outline">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Task</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>


                            <!-- Comments Button with Modal -->
                            <div x-data="{ commentModal: false }">
                                <button @click="commentModal = true" class="text-gray-600 hover:text-gray-900" title="Comments">
                                    <i class="fas fa-comment"></i>
                                </button>

                                <!-- Comments Modal -->
                                <div x-show="commentModal" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                                    <div class="bg-white w-11/12 md:w-1/2 lg:w-1/3 rounded-md shadow-lg p-5 relative">
                                        <button @click="commentModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <h3 class="text-lg font-semibold mb-4">Comments for {{ $task->title }}</h3>
                                        <div class="max-h-64 overflow-y-auto mb-4 space-y-2">
                                            @forelse($task->comments as $comment)
                                            <div class="bg-gray-100 p-2 rounded-md">
                                                <strong>{{ $comment->user->name }}:</strong> {{ $comment->comment }}
                                                <span class="text-xs text-gray-500 float-right">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            @empty
                                            <p class="text-gray-500 text-center py-4">No comments yet.</p>
                                            @endforelse
                                        </div>
                                        <form action="{{ route('tasks.comment', $task) }}" method="POST" class="space-y-2">
                                            @csrf
                                            <textarea name="comment" required rows="3" placeholder="Write comment..." class="w-full border rounded-md p-2"></textarea>
                                            <div class="flex justify-end space-x-2">
                                                <button type="button" @click="commentModal = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md">Cancel</button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-paper-plane mr-2"></i> Add Comment
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Button -->
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this task?')" class="text-red-600 hover:text-red-900" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        No tasks found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $tasks->appends(request()->query())->links() }}
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
    // File upload preview
    document.addEventListener('DOMContentLoaded', function() {
        const taskDocsInput = document.getElementById('task_docs');
        if (taskDocsInput) {
            taskDocsInput.addEventListener('change', function(e) {
                const fileList = document.getElementById('file-list');
                fileList.innerHTML = '';

                if (this.files.length > 0) {
                    fileList.innerHTML = '<p class="font-medium">Selected files:</p>';
                    Array.from(this.files).forEach(file => {
                        fileList.innerHTML += `<p class="truncate">• ${file.name} (${(file.size/1024/1024).toFixed(2)} MB)</p>`;
                    });
                }
            });
        }

        // If team_id is provided in URL, automatically populate the team field in create modal
        @if(request('team_id'))
        document.getElementById('create-team-id').value = "{{ request('team_id') }}";
        updateTeamMembers('create', "{{ request('team_id') }}");
        @endif
    });


    function updateTeamMembers(teamId) {
        const assignedToSelect = document.getElementById('assigned-to-select');
        const currentAssigned = "{{ $task->assigned_to }}"; // Current assigned user ID

        // Clear current options except the first one
        while (assignedToSelect.options.length > 1) {
            assignedToSelect.remove(1);
        }

        if (teamId) {
            // Fetch team members via AJAX
            fetch(`/teams/${teamId}/members`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(members => {
                    if (members.length > 0) {
                        members.forEach(member => {
                            const option = document.createElement('option');
                            option.value = member.id;
                            option.textContent = member.name;
                            // Preselect the currently assigned user if they're in this team
                            if (member.id == currentAssigned) {
                                option.selected = true;
                            }
                            assignedToSelect.appendChild(option);
                        });

                        // If no user is selected yet, select the first one
                        if (!currentAssigned && assignedToSelect.options.length > 1) {
                            assignedToSelect.options[1].selected = true;
                        }
                    } else {
                        // If no members in team, show all users
                        populateAllUsers(currentAssigned);
                    }
                })
                .catch(error => {
                    console.error('Error fetching team members:', error);
                    // Fallback to all users if there's an error
                    populateAllUsers(currentAssigned);
                });
        } else {
            // If no team selected, show all users
            populateAllUsers(currentAssigned);
        }
    }

    function populateAllUsers(currentAssigned) {
        const assignedToSelect = document.getElementById('assigned-to-select');

        // Clear current options except the first one
        while (assignedToSelect.options.length > 1) {
            assignedToSelect.remove(1);
        }

        // Add all users
        @foreach($users as $user)
        const option = document.createElement('option');
        option.value = "{{ $user->id }}";
        option.textContent = "{{ $user->name }}";
        if ("{{ $user->id }}" == currentAssigned) {
            option.selected = true;
        }
        assignedToSelect.appendChild(option);
        @endforeach
    }

    // Initialize the modal with correct team members when opened
    document.addEventListener('DOMContentLoaded', function() {
        const editTaskButton = document.querySelector('[x-data="{ editTaskModal: false }"] button');
        if (editTaskButton) {
            editTaskButton.addEventListener('click', function() {
                // Get the current team ID
                const teamSelect = document.getElementById('team-select');
                const teamId = teamSelect.value;

                // Small delay to ensure modal is open before updating
                setTimeout(() => updateTeamMembers(teamId), 100);
            });
        }

        // Also update when page loads if there's a team selected
        const initialTeamSelect = document.getElementById('team-select');
        if (initialTeamSelect && initialTeamSelect.value) {
            updateTeamMembers(initialTeamSelect.value);
        }
    });
    // Load task data for edit modal
    function loadTaskData(taskId) {
        console.log('Loading task data for:', taskId);

        // This ensures the team members dropdown is populated correctly
        const teamId = document.querySelector(`#edit-task-form-${taskId} input[name="team_id"]`).value;
        if (teamId) {
            updateTeamMembers('edit', teamId, taskId);
        }

        // Small delay to ensure modal is fully rendered
        setTimeout(() => {
            // Focus on the first input field for better UX
            const firstInput = document.querySelector(`#edit-task-form-${taskId} input`);
            if (firstInput) firstInput.focus();
        }, 100);
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection