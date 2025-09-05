@extends('layouts.app')

@section('title', 'Emissions of ' . $radio->name)

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6" x-data="emissionsPage()">
    <!-- Header & Create Button -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold" style="color: #0a2164">Emissions of {{ $radio->name }}</h1>
        <button @click="showCreate = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-2 text-white text-sm font-medium hover:transition-all shadow-md" style="background-color: #0a2164">
            <i class="fas fa-plus mr-2"></i> Add New Emission
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..." class="w-full border rounded-md p-2">
            <select name="type" class="w-full border rounded-md p-2">
                <option value="">All Types</option>
                @foreach($types as $type)
                <option value="{{ $type }}" @selected(request('type')==$type)>{{ $type }}</option>
                @endforeach
            </select>
            <select name="animateur_id" class="w-full border rounded-md p-2">
                <option value="">All Animateurs</option>
                @foreach($animateurs ?? [] as $user)
                <option value="{{ $user->id }}" @selected(request('animateur_id')==$user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-2 text-white text-sm font-medium hover:transition-all shadow-md" style="background-color: #0a2164">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
                <a href="{{ route('radios.emissions.index', $radio) }}" class="btn btn-outline">Clear</a>
            </div>
        </form>
    </div>

    <!-- Emissions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Animateur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($emissions as $emission)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex-shrink-0 h-10 w-10">
                            @if($emission->logo_path)
                            <img class="h-10 w-10 rounded-full object-cover"
                                src="{{ asset('storage/' . $emission->logo_path) }}"
                                alt="{{ $emission->name }}">
                            @else
                            <div class="h-10 w-10 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center font-semibold text-sm uppercase">
                                {{ strtoupper(substr($emission->name, 0, 1)) }}
                            </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $emission->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $emission->type }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $emission->animateur->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $emission->duration_minutes }}</td>
                    <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                        <a href="{{ route('radios.emissions.show', [$radio, $emission]) }}" class="text-blue-600 hover:text-blue-900"><i class="fas fa-eye"></i></a>

                        <button @click="openEditModal({{ $emission->id }}, '{{ $emission->name }}', '{{ $emission->type }}', {{ $emission->animateur_id }}, {{ $emission->duration_minutes ?? 0 }}, '{{ $emission->description }}')" class="text-yellow-600 hover:text-yellow-900"><i class="fas fa-edit"></i></button>

                        <button @click="openDeleteModal({{ $emission->id }}, '{{ $emission->name }}')" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No emissions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $emissions->appends(request()->query())->links() }}
    </div>

    <!-- ------------------- CREATE MODAL ------------------- -->
    <div x-show="showCreate" x-transition class="fixed inset-0 flex items-center justify-center bg-gray-600 bg-opacity-50 z-50">
        <div class="bg-white w-11/12 md:w-2/3 lg:w-1/2 rounded-md shadow-lg p-6 relative">
            <button @click="showCreate=false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i></button>
            <h3 class="text-lg font-semibold mb-4">Create New Emission</h3>
            <form :action="createAction" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" required class="block w-full border rounded-md p-2">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <input type="text" name="type" required class="block w-full border rounded-md p-2">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Animateur *</label>
                    <select name="animateur_id" required class="block w-full border rounded-md p-2">
                        @foreach($animateurs ?? [] as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" class="block w-full border rounded-md p-2">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" class="block w-full border rounded-md p-2"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    <input type="file" name="logo" class="block w-full border rounded-md p-2">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="showCreate=false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ------------------- EDIT MODAL ------------------- -->
    <div x-show="showEdit" x-transition class="fixed inset-0 flex items-center justify-center bg-gray-600 bg-opacity-50 z-50">
        <div class="bg-white w-11/12 md:w-2/3 lg:w-1/2 rounded-md shadow-lg p-6 relative">
            <button @click="closeModals()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i></button>
            <h3 class="text-lg font-semibold mb-4">Edit Emission</h3>
            <form :action="`{{ route('radios.emissions.update', [$radio, '___id___']) }}`.replace('___id___', editData.id)" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" x-model="editData.name" class="block w-full border rounded-md p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <input type="text" name="type" x-model="editData.type" class="block w-full border rounded-md p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Animateur *</label>
                    <select name="animateur_id" x-model="editData.animateur_id" class="block w-full border rounded-md p-2" required>
                        @foreach($animateurs ?? [] as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" x-model="editData.duration_minutes" class="block w-full border rounded-md p-2">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" x-model="editData.description" class="block w-full border rounded-md p-2"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    <input type="file" name="logo" class="block w-full border rounded-md p-2">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="closeModals()" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ------------------- DELETE MODAL ------------------- -->
    <div x-show="showDelete" x-transition class="fixed inset-0 flex items-center justify-center bg-gray-600 bg-opacity-50 z-50">
        <div class="bg-white w-11/12 md:w-1/3 rounded-md shadow-lg p-6 relative">
            <button @click="closeModals()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i></button>
            <h3 class="text-lg font-semibold mb-4">Delete Emission</h3>
            <p>Are you sure you want to delete <strong x-text="deleteData.name"></strong>?</p>
            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" @click="closeModals()" class="btn btn-outline">Cancel</button>
                <form :action="deleteAction" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function emissionsPage() {
        return {
            showCreate: false,
            showEdit: false,
            showDelete: false,
            editData: {},
            deleteData: {},
            createAction: "{{ route('radios.emissions.store', $radio) }}",
            editAction: '',
            deleteAction: '',

            openEditModal(id, name, type, animateur_id, duration, description) {
                this.editData = {
                    id,
                    name,
                    type,
                    animateur_id,
                    duration_minutes: duration,
                    description
                };
                this.editAction = `/radios/{{ $radio->id }}/emissions/${id}`;
                this.showEdit = true;
            },
            openDeleteModal(id, name) {
                this.deleteData = {
                    id,
                    name
                };
    this.deleteAction = "{{ route('radios.emissions.destroy', ['radio' => $radio->id, 'emission' => 'EMISSION_ID']) }}".replace('EMISSION_ID', id);
                this.showDelete = true;
            },
            closeModals() {
                this.showCreate = false;
                this.showEdit = false;
                this.showDelete = false;
                this.editData = {};
                this.deleteData = {};
            }
        }
    }
</script>
@endsection