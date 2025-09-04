@extends('layouts.app')

@section('title', $task->title)

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6">
    <!-- Task Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold" style="color: #0a2164">{{ $task->title }}</h1>
            <p class="text-gray-600">
                @if($task->team)
                Team: <a href="{{ route('teams.show', $task->team) }}" class="text-#0a2164 hover:underline" style="color: #0a2164">
                    {{ $task->team->name }}
                </a>
                @else
                No team assigned
                @endif
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>

            <!-- Edit Task Button with Modal -->
            <div x-data="{ editTaskModal: false }">
                <button @click="editTaskModal = true" class="btn btn-primary">
                    <i class="fas fa-edit mr-2"></i> Edit Task
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Team (optional)</label>
                                    <select name="team_id" id="team-select" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" onchange="updateTeamMembers(this.value)">
                                        <option value="">No Team</option>
                                        @foreach($teams as $teamOption)
                                        <option value="{{ $teamOption->id }}" {{ $task->team_id == $teamOption->id ? 'selected' : '' }}>{{ $teamOption->name }}</option>
                                        @endforeach
                                    </select>
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
        </div>
    </div>

    <!-- Task Details Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Description</h3>
                    <p class="text-gray-600">{{ $task->description ?? 'No description provided' }}</p>

                    <!-- Status Update Form -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Update Status</h3>
                        <form action="{{ route('tasks.update-status', $task) }}" method="POST">
                            @csrf
                            <div class="flex items-center space-x-2">
                                <select name="status" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                                    <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>Todo</option>
                                    <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Done</option>
                                    <option value="late" {{ $task->status === 'late' ? 'selected' : '' }}>Late</option>
                                    <option value="expired" {{ $task->status === 'expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Task Details</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Status:</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $task->status === 'todo' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $task->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $task->status === 'done' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $task->status === 'late' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $task->status === 'expired' ? 'bg-gray-800 text-gray-100' : '' }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </p>
                        <p><span class="font-medium">Priority:</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </p>
                        <p><span class="font-medium">Assigned to:</span>
                            @if($task->assigned)
                            {{ $task->assigned->name }}
                            @else
                            Unassigned
                            @endif
                        </p>
                        <p><span class="font-medium">Deadline:</span>
                            {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('M d, Y H:i') : 'No deadline' }}
                        </p>
                        <p><span class="font-medium">Created:</span> {{ $task->created_at->format('M d, Y') }}</p>
                        <p><span class="font-medium">Owner:</span> {{ $task->owner->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Attachments Section -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Attachments</h3>

        <!-- Add More Files Button -->
        <div x-data="{ uploadMore: false }">
            <button @click="uploadMore = true" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Add More Files
            </button>

            <!-- Add More Files Modal -->
            <div x-show="uploadMore" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white w-11/12 md:w-2/3 lg:w-1/2 rounded-md shadow-lg p-6 relative">
                    <button @click="uploadMore = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>

                    <h3 class="text-lg font-semibold mb-4">Add More Files</h3>
                    <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Hidden fields to preserve other task data -->
                        <input type="hidden" name="title" value="{{ $task->title }}">
                        <input type="hidden" name="description" value="{{ $task->description }}">
                        <input type="hidden" name="assigned_to" value="{{ $task->assigned_to }}">
                        <input type="hidden" name="team_id" value="{{ $task->team_id }}">
                        <input type="hidden" name="deadline" value="{{ $task->deadline }}">
                        <input type="hidden" name="status" value="{{ $task->status }}">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Add Additional Files</label>
                            <input type="file" name="task_docs[]" multiple class="block w-full">
                            <p class="text-xs text-gray-500 mt-1">Multiple files allowed (PDF, DOC, DOCX, XLSX, JPG, PNG)</p>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" @click="uploadMore = false" class="btn btn-outline">Cancel</button>
                            <button type="submit" class="btn btn-primary">Upload Files</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="p-6">
        <!-- Current Attachments -->
        @if($task->task_docs && count(json_decode($task->task_docs, true)) > 0)
        <div class="mb-6">
            <h4 class="text-md font-semibold text-gray-700 mb-3">Current Files</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach(json_decode($task->task_docs, true) as $index => $file)
                <div class="flex items-center justify-between p-3 border rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-file text-gray-400 mr-3"></i>
                        <span class="text-sm text-gray-600 truncate">
                            {{ basename($file) }}
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ asset('storage/' . $file) }}" download class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-download"></i>
                        </a>
                        <!-- Remove File Form -->
                        <form action="{{ route('tasks.remove-file', $task) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="file_path" value="{{ $file }}">
                            <button type="submit" onclick="return confirm('Are you sure you want to remove this file?')" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <p class="text-gray-500">No attachments yet.</p>
        @endif
    </div>
</div>

    <!-- Comments Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Comments</h3>

            <!-- Add Comment Button with Modal -->
            <div x-data="{ addCommentModal: false }">
                <button @click="addCommentModal = true" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Comment
                </button>

                <!-- Add Comment Modal -->
                <div x-show="addCommentModal" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50">
                    <div class="bg-white w-11/12 md:w-2/3 lg:w-1/2 rounded-md shadow-lg p-6 relative">
                        <button @click="addCommentModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>

                        <h3 class="text-lg font-semibold mb-4">Add Comment</h3>
                        <form action="{{ route('tasks.comment', $task) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                                <textarea name="comment" rows="3" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary" placeholder="Write your comment here..."></textarea>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="addCommentModal = false" class="btn btn-outline">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Comment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6">
            @if($task->comments->count() > 0)
            <div class="space-y-4">
                @foreach($task->comments as $comment)
                <div class="border-b border-gray-200 pb-4 last:border-b-0">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 h-10 w-10">
                            <img class="h-10 w-10 rounded-full" src="{{ $comment->user->profile_photo_path ? asset('storage/' . $comment->user->profile_photo_path) : asset('/default-user.png') }}" alt="{{ $comment->user->name }}">
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</h4>
                                <span class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ $comment->comment }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center">No comments yet.</p>
            @endif
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

@section('scripts')
<script>
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
</script>
@endsection