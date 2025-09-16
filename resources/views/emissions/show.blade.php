@extends('layouts.app')

@section('title', $emission->name)

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6" x-data="emissionPage()">

    <!-- Emission Details -->
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
                <p class="text-gray-700 mt-2">
                    <strong>Current Season:</strong>
                    {{ $lastSeason ? 'Season ' . $lastSeason->number : 'N/A' }}
                </p>
            </div>
            <div class="flex flex-col gap-2">
                <button @click="addEpisodeModal = true" class="btn btn-primary">New Episode</button>
                <button @click="addSeasonModal = true" class="btn btn-primary">New Season</button>
                <button @click="seasonListModal = true" class="btn btn-outline">All Seasons</button>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <form method="GET" class="flex gap-2 flex-1">
            <input type="text" name="search" placeholder="Search episodes..." value="{{ request('search') }}" class="block w-full border rounded-md p-2">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <form method="GET" class="flex gap-2">
            <select name="season_id" onchange="this.form.submit()" class="block border rounded-md p-2">
                <option value="">All Seasons</option>
                @foreach($seasons as $season)
                <option value="{{ $season->id }}" {{ request('season_id') == $season->id ? 'selected' : '' }}>
                    Season {{ $season->number }} ({{ $season->episodes->count() }} eps)
                </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Episodes Table -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left">Episode #</th>
                    <th class="px-4 py-2 text-left">Season</th>
                    <th class="px-4 py-2 text-left">Description</th>
                    <th class="px-4 py-2 text-left">Aired On</th>
                    <th class="px-4 py-2 text-left">Duration</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($episodes as $episode)
                <tr>
                    <td class="px-4 py-2">{{ $episode->number }}</td>
                    <td class="px-4 py-2">Season {{ $episode->season->number ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $episode->description ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $episode->aired_on }} {{ $episode->time }}</td>
                    <td class="px-4 py-2">{{ $episode->duration_minutes ?? 'N/A' }} mins</td>
                    <td class="px-4 py-2 flex gap-2">

                        <!-- Show Modal Trigger -->
                        <button @click="
                            showEpisodeModal = true;
                            selectedEpisode = @js($episode);
                            selectedEpisode.datetime = '{{ $episode->aired_on }}T{{ substr($episode->time,0,5) }}';
                        " title="Show" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </button>

                        <!-- Edit Modal Trigger -->
                        <button @click="
                            editEpisodeModal = true;
                            selectedEpisode = @js($episode);
                            selectedEpisode.datetime = '{{ $episode->aired_on }}T{{ substr($episode->time,0,5) }}';
                        " title="Edit" class="text-green-600 hover:text-green-800">
                            <i class="fas fa-pen"></i>
                        </button>

                        <!-- Delete Action -->
                        <form method="POST" action="{{ route('radios.emissions.seasons.episodes.destroy', [
                            'emission' => $episode->season->emission,
                            'season' => $episode->season,
                            'episode' => $episode
                        ]) }}" onsubmit="return confirm('Delete this episode?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-gray-500 py-2">No episodes found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Show Episode Modal -->
    <div x-show="showEpisodeModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white rounded-md shadow-lg p-6 w-full max-w-lg relative">
            <button @click="showEpisodeModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">&times;</button>
            <h3 class="text-lg font-semibold mb-4">Episode Details</h3>
            <div>
                <p><strong>Number:</strong> <span x-text="selectedEpisode.number"></span></p>
                <p><strong>Season:</strong> <span x-text="selectedEpisode.season?.number"></span></p>
                <p><strong>Aired On:</strong> <span x-text="selectedEpisode.aired_on + ' ' + selectedEpisode.time"></span></p>
                <p><strong>Duration:</strong> <span x-text="selectedEpisode.duration_minutes + ' mins'"></span></p>
                <p><strong>Description:</strong> <span x-text="selectedEpisode.description"></span></p>
            </div>
        </div>
    </div>


    <!-- Edit Episode Modal -->
    <div x-show="editEpisodeModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white rounded-md shadow-lg p-6 w-full max-w-lg relative">
            <button @click="editEpisodeModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">&times;</button>
            <h3 class="text-lg font-semibold mb-4">Edit Episode</h3>

            <form :action="`/radios/${selectedEpisode.season.radio_id}/emissions/${selectedEpisode.season.emission_id}/seasons/${selectedEpisode.season.id}/episodes/${selectedEpisode.id}`" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">Episode Number</label>
                    <input type="text" x-model="selectedEpisode.number" class="block w-full border rounded-md p-2 bg-gray-100" readonly>
                </div>

                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">Date & Time</label>
                    <input type="datetime-local" name="aired_datetime" x-model="selectedEpisode.datetime" class="block w-full border rounded-md p-2" required>
                </div>

                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" x-model="selectedEpisode.duration_minutes" class="block w-full border rounded-md p-2" required>
                </div>

                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" x-model="selectedEpisode.description" class="block w-full border rounded-md p-2"></textarea>
                </div>

                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">Conducteur (PDF, DOC, etc.)</label>
                    <input type="file" name="conducteur" class="block w-full border rounded-md p-2" accept=".pdf,.doc,.docx">
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="editEpisodeModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Episode</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


<!-- Add Episode Modal -->
<div x-show="addEpisodeModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-md shadow-lg p-6 w-full max-w-lg relative">
        <button @click="addEpisodeModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">&times;</button>
        <h3 class="text-lg font-semibold mb-4">Add New Episode</h3>

        <form method="POST" action="{{ route('radios.emissions.seasons.episodes.store', [$emission->id, $lastSeason->id]) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="season_id" value="{{ $lastSeason->id }}">

            <div class="mb-2">
                <label class="block text-sm font-medium mb-1">Episode Number</label>
                <input type="text" value="{{ ($lastSeason?->episodes()->count() ?? 0) + 1 }}" class="block w-full border rounded-md p-2 bg-gray-100" readonly>
            </div>

            <div class="mb-2">
                <label class="block text-sm font-medium mb-1">Date & Time</label>
                <input type="datetime-local" name="aired_on" required class="block w-full border rounded-md p-2">
            </div>

            <div class="mb-2">
                <label class="block text-sm font-medium mb-1">Duration (minutes)</label>
                <input type="number" name="duration_minutes" required min="10" class="block w-full border rounded-md p-2">
            </div>

            <div class="mb-2">
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea name="description" rows="3" class="block w-full border rounded-md p-2"></textarea>
            </div>

            <div class="mb-2">
                <label class="block text-sm font-medium mb-1">Conducteur (PDF, DOC, etc.)</label>
                <input type="file" name="conducteur" class="block w-full border rounded-md p-2" accept=".pdf,.doc,.docx">
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="addEpisodeModal = false" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Episode</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Season Modal -->
<div x-show="addSeasonModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-md shadow-lg p-6 w-full max-w-md relative">
        <button @click="addSeasonModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">&times;</button>
        <h3 class="text-lg font-semibold mb-4">Add New Season</h3>
        <form method="POST" action="{{ route('radios.emissions.seasons.store', [$emission->id]) }}">
            @csrf
            <div class="mb-2">
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea name="description" rows="3" class="block w-full border rounded-md p-2"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="addSeasonModal = false" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Season</button>
            </div>
        </form>
    </div>
</div>

<!-- Seasons List Modal -->
<div x-show="seasonListModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-md shadow-lg p-6 w-full max-w-lg relative">
        <button @click="seasonListModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">&times;</button>
        <h3 class="text-lg font-semibold mb-4">Seasons</h3>
        <ul class="space-y-2">
            @foreach($seasons as $season)
            <li class="flex justify-between items-center border-b pb-2">
                <span>Season {{ $season->number }}</span>
                @if($loop->first && $seasons->count() > 1)
                <form method="POST" action="{{ route('radios.emissions.seasons.destroy', [$emission->id, $season->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
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
            addSeasonModal: false,
            seasonListModal: false,
            editEpisodeModal: false,
            showEpisodeModal: false,
            selectedEpisode: {}, // البيانات للعرض والتعديل
        }
    }
</script>

@endsection