@extends('layouts.app')

@section('title', $emission->name)

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6" x-data="emissionPage()">

    <!-- Emission Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#0a2164]">{{ $emission->name }}</h1>
        <button @click="addEpisodeModal = true" class="btn btn-primary flex items-center">
            <i class="fas fa-plus mr-2"></i> Add New Episode
        </button>
    </div>

    <!-- Seasons Filter -->
    <form method="GET" class="mb-4 flex gap-2">
        <select name="season" class="rounded-md border-gray-300 p-2">
            <option value="">All Seasons</option>
            @foreach($emission->seasons as $season)
            <option value="{{ $season->id }}" {{ request('season') == $season->id ? 'selected' : '' }}>
                {{ $season->name }}
            </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-outline">Filter</button>
    </form>

    <!-- Episodes Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-10">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left">Episode Title</th>
                    <th class="px-4 py-2 text-left">Season</th>
                    <th class="px-4 py-2 text-left">Air Date</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($episodes as $episode)
                <tr>
                    <td class="px-4 py-2">{{ $episode->title }}</td>
                    <td class="px-4 py-2">{{ $episode->season->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $episode->air_date?->format('M d, Y') ?? 'N/A' }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ route('episodes.show', $episode) }}" class="btn btn-outline text-sm">
                            <i class="fas fa-eye mr-1"></i> Show
                        </a>
                        <a href="{{ route('episodes.edit', $episode) }}" class="btn btn-primary text-sm">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <form action="{{ route('episodes.destroy', $episode) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-red text-sm">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">No episodes found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Add Episode Modal -->
    <div x-show="addEpisodeModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white rounded-md shadow-lg p-6 w-full max-w-lg relative">
            <button @click="addEpisodeModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
            <h3 class="text-lg font-semibold mb-4">Add New Episode</h3>
            <form action="{{ route('radios.emissions.seasons.episodes.store', ['emission' => $emission->id, 'season' => $lastSeason->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="emission_id" value="{{ $emission->id }}">
                <input type="hidden" name="season_id" value="{{ $lastSeason->id }}">
                <div>
                    <label class="block text-sm font-medium mb-1">Title *</label>
                    <input type="text" name="title" required class="block w-full rounded-md border-gray-300 p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Season</label>
                    <select name="season_id" class="block w-full rounded-md border-gray-300 p-2">
                        @foreach($emission->seasons as $season)
                        <option value="{{ $season->id }}">{{ $season->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Air Date</label>
                    <input type="date" name="air_date" class="block w-full rounded-md border-gray-300 p-2">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="addEpisodeModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Episode</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bottom Space: Tasks, Members, Materials, Seasons -->
    <div class="mt-10 space-y-6">

        {{-- Tasks Section --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold mb-2">Tasks for this Emission</h3>
            <div class="flex gap-2 mb-4">
                <input type="text" x-model="taskSearch" placeholder="Search tasks..." class="border rounded-md p-2 w-full">
                <button @click="showTaskModal = true" class="btn btn-primary flex items-center">
                    <i class="fas fa-plus mr-1"></i> Add Task
                </button>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Title</th>
                        <th class="px-4 py-2 text-left">Assigned To</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Deadline</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="task in filteredTasks()" :key="task.id">
                        <tr>
                            <td class="px-4 py-2" x-text="task.title"></td>
                            <td class="px-4 py-2" x-text="task.assigned_name"></td>
                            <td class="px-4 py-2" x-text="task.status"></td>
                            <td class="px-4 py-2" x-text="task.deadline"></td>
                            <td class="px-4 py-2 flex gap-2">
                                <button @click="editTask(task)" class="btn btn-primary btn-sm">Edit</button>
                                <button @click="deleteTask(task.id)" class="btn btn-red btn-sm">Delete</button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredTasks().length === 0">
                        <td colspan="5" class="text-center text-gray-500 py-2">No tasks found.</td>
                    </tr>
                </tbody>
            </table>

            <!-- Add/Edit Task Modal -->
            <div x-show="showTaskModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white rounded-md shadow-lg p-6 w-full max-w-lg relative">
                    <button @click="showTaskModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                    <h3 class="text-lg font-semibold mb-4" x-text="taskForm.id ? 'Edit Task' : 'Add Task'"></h3>
                    <form @submit.prevent="submitTask" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Title *</label>
                            <input type="text" x-model="taskForm.title" required class="block w-full rounded-md border-gray-300 p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Assigned To</label>
                            <select x-model="taskForm.assigned_to" class="block w-full rounded-md border-gray-300 p-2">
                                <template x-for="member in members" :key="member.id">
                                    <option :value="member.id" x-text="member.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select x-model="taskForm.status" class="block w-full rounded-md border-gray-300 p-2">
                                <option value="todo">To Do</option>
                                <option value="pending">Pending</option>
                                <option value="done">Done</option>
                                <option value="late">Late</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Deadline</label>
                            <input type="datetime-local" x-model="taskForm.deadline" class="block w-full rounded-md border-gray-300 p-2">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showTaskModal = false" class="btn btn-outline">Cancel</button>
                            <button type="submit" class="btn btn-primary" x-text="taskForm.id ? 'Update' : 'Create'"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Members Section --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold mb-2">Members</h3>
            <ul>
                @foreach($emission->members as $member)
                <li>{{ $member->name }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Materials Section --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold mb-2">Materials</h3>
            <ul>
                @foreach($emission->materials as $material)
                <li class="flex justify-between items-center">
                    <span>{{ $material->name }}</span>
                    <div class="flex gap-2">
                        <a href="{{ asset('storage/' . $material->file) }}" download class="btn btn-outline btn-sm">Download</a>
                        <form action="{{ route('materials.destroy', $material) }}" method="POST" onsubmit="return confirm('Delete this material?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-red btn-sm">Delete</button>
                        </form>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>

        {{-- Seasons Section --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold mb-2">Seasons</h3>
            <ul class="list-disc pl-5">
                @foreach($emission->seasons as $season)
                <li class="flex justify-between items-center">
                    {{ $season->name }}
                    @if($loop->last)
                    <form action="{{ route('radios.emissions.seasons.destroy',['emission' => $emission->id, 'season' => $season->id]) }}" method="POST" onsubmit="return confirm('Delete last season?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-red btn-sm ml-2">Delete</button>
                    </form>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function emissionPage() {
        return {
            addEpisodeModal: false,
            showTaskModal: false,
            taskSearch: '',
            taskForm: {},
            members: @json($emission->members),
            tasks: @json($emission->tasks),
            filteredTasks() {
                if (this.taskSearch === '') return this.tasks;
                return this.tasks.filter(t => t.title.toLowerCase().includes(this.taskSearch.toLowerCase()));
            },
            editTask(task) {
                this.taskForm = {
                    ...task
                };
                this.showTaskModal = true;
            },
            deleteTask(id) {
                if (confirm('Are you sure to delete this task?')) {
                    // send delete request via fetch or redirect
                    window.location.href = '/tasks/' + id + '/delete';
                }
            },
            submitTask() {
                if (this.taskForm.id) {
                    // update task
                    alert('Update logic here');
                } else {
                    // create task
                    alert('Create logic here');
                }
                this.showTaskModal = false;
            }
        }
    }
</script>
@endsection