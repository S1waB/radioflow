@extends('layouts.app')

@section('title', $emission->name)

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6" x-data="emissionPage()">

    <!-- Emission Details Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center gap-6">
            <div>
                @if($emission->logo_path)
                <img src="{{ asset('storage/' . $emission->logo_path) }}" alt="{{ $emission->name }}" class="w-32 h-32 object-cover rounded">
                @else
                <div class="w-32 h-32 bg-gray-200 rounded flex items-center justify-center text-gray-500">No Logo</div>
                @endif
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-[#0a2164]">{{ $emission->name }}</h2>
                <p class="text-gray-600 mt-1">{{ $emission->description ?? 'No description' }}</p>
                <p class="text-gray-500 mt-2">Type: {{ $emission->type ?? 'N/A' }}</p>
                <p class="text-gray-500">Duration: {{ $emission->duration_minutes ?? 'N/A' }} mins</p>
            </div>
            <div class="flex flex-col gap-2">
                <button @click="addEpisodeModal = true" class="btn btn-primary">New Episode</button>
            </div>
        </div>
    </div>

    <!-- Episodes List -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left">Title</th>
                    <th class="px-4 py-2 text-left">Season</th>
                    <th class="px-4 py-2 text-left">Duration</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($episodes as $episode)
                <tr>
                    <td class="px-4 py-2">{{ $episode->title }}</td>
                    <td class="px-4 py-2">{{ $episode->season->number ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $episode->duration_minutes ?? 'N/A' }} mins</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('episodes.show', $episode->id) }}" class="btn btn-outline btn-sm">Show</a>
                    </td>
                </tr>
                @endforeach
                @if($episodes->isEmpty())
                <tr>
                    <td colspan="4" class="text-center text-gray-500 py-2">No episodes found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Add Episode Modal -->
    <div x-show="addEpisodeModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white rounded-md shadow-lg p-6 w-full max-w-lg relative">
            <button @click="addEpisodeModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">&times;</button>
            <h3 class="text-lg font-semibold mb-4">Add New Episode</h3>
            <form method="POST" action="{{ route('radios.emissions.seasons.episodes.store', [$emission->id, $lastSeason->id]) }}">
                @csrf
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" name="title" required class="block w-full border rounded-md p-2">
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">Season</label>
                    <select name="season_id" required class="block w-full border rounded-md p-2">
                        <option value="">Select Season</option>
                        @foreach($emission->seasons as $season)
                        <option value="{{ $season->id }}">{{ $season->number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" class="block w-full border rounded-md p-2">
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" rows="3" class="block w-full border rounded-md p-2"></textarea>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="addEpisodeModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Episode</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function emissionPage() {
        return {
            addEpisodeModal: false
        }
    }
</script>

@endsection