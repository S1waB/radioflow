@extends('layouts.app')

@section('title', $radio->name . ' Songs')

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6">
    <!-- Header and Add Song Button -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $radio->name }} Songs</h1>
        <button data-modal-target="createSongModal" data-modal-toggle="createSongModal"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-md btn btn-primary text-white text-sm font-medium hover:bg-primary transition-all shadow-md mt-4 md:mt-0">
            <i class="fas fa-plus mr-1"></i> Add New Song
        </button>
    </div>

    <!-- Search / Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('radios.songs.index', $radio) }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" placeholder="Search songs..." value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>
            <div>
                <select name="status" class="block w-full py-2 pl-3 pr-10 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('radios.songs.index', $radio) }}" class="btn btn-outline flex items-center">
                    <i class="fas fa-sync-alt mr-1"></i> Reset
                </a>
            </div>

        </form>

    </div>

    <!-- Songs Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Suggester</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($songs as $song)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $song->title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($song->file)
                            <a href="{{ asset('storage/' . $song->file) }}" download class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-download"></i>
                                @else
                                <p>No File</p>
                                @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($song->url)
                            <a href="{{ $song->url }}" target="_blank" class="text-blue-600 hover:underline">Link</a>
                            @else
                            <p>No URL</p>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-500">{{ $song->note }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 capitalize">{{ $song->status }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $song->suggester->name }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <!-- View -->
                                <button
                                    onclick="loadSongData({{ $song->id }})"
                                    data-modal-target="viewSongModal"
                                    data-modal-toggle="viewSongModal"
                                    class="text-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>


                                </button>
                                <button data-modal-target="editSongModal-{{ $song->id }}" data-modal-toggle="editSongModal-{{ $song->id }}"
                                    class="text-blue-600 hover:text-blue-900" title="Edit"><i class="fas fa-edit"></i></button>
                                <form action="{{ route('radios.songs.destroy',[$radio->id, $song->id]) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this song?')"
                                        class="text-red-600 hover:text-red-900" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>

                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div id="editSongModal-{{ $song->id }}" tabindex="-1" aria-hidden="true"
                        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
                            <h2 class="text-xl font-bold mb-4">Edit Song</h2>
                            <form action="{{ route('radios.songs.update', [$radio, $song]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Title</label>
                                    <input type="text" name="title" value="{{ $song->title }}" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">URL</label>
                                    <input type="url" name="url" value="{{ $song->url }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Note</label>
                                    <textarea name="note" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $song->note }}</textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">File</label>
                                    <input type="file" name="file" class="mt-1 block w-full">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="pending" {{ $song->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="accepted" {{ $song->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                        <option value="rejected" {{ $song->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="flex justify-end space-x-2">
                                    <button type="button" data-modal-hide="editSongModal-{{ $song->id }}"
                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No songs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $songs->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createSongModal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
        <h2 class="text-xl font-bold mb-4">Add New Song</h2>
        <form action="{{ route('radios.songs.store', $radio) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">URL</label>
                <input type="url" name="url"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Note</label>
                <textarea name="note" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">File</label>
                <input type="file" name="file" class="mt-1 block w-full">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="pending">Pending</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" data-modal-hide="createSongModal"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- View Song Modal -->
<div id="viewSongModal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-music text-indigo-600"></i>
                <span id="viewSongTitle">Song Details</span>
            </h2>
            <button type="button" data-modal-hide="viewSongModal"
                class="text-gray-500 hover:text-gray-700 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Song Info -->
        <div class="space-y-3 text-sm text-gray-700">
            <p><strong class="text-gray-900">🎵 Title:</strong> <span id="viewSongTitleText"></span></p>
            <p><strong class="text-gray-900">🔗 URL:</strong> <span id="viewSongUrl"></span></p>
            <p><strong class="text-gray-900">📂 File:</strong> <span id="viewSongFile"></span></p>
            <p><strong class="text-gray-900">📝 Note:</strong> <span id="viewSongNote"></span></p>
            <p><strong class="text-gray-900">📌 Status:</strong>
                <span id="viewSongStatus" class="px-2 py-1 rounded text-white text-xs"></span>
            </p>
            <p><strong class="text-gray-900">🙋 Suggester:</strong> <span id="viewSongSuggester"></span></p>
            <p><strong class="text-gray-900">📅 Created At:</strong> <span id="viewSongCreatedAt"></span></p>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-2 mt-6 border-t pt-4">
            <a id="viewSongDownload" href="#" download
                class="btn btn-primary">
                <i class="fas fa-download"></i> Download
            </a>
            <button type="button" data-modal-hide="viewSongModal"
                class="btn btn-outline flex items-center">
                Close
            </button>
        </div>
    </div>
</div>




<!-- Toast -->
@if(session('success'))
<div x-data="{ show: true }" x-show="show"
    x-transition class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center justify-between max-w-md">
    <span>{{ session('success') }}</span>
    <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
        <i class="fas fa-times"></i>
    </button>
</div>
@endif

<script>
    function loadSongData(id) {
        const url = "{{ route('radios.songs.show', [$radio->id, '__SONG_ID__']) }}".replace('__SONG_ID__', id);

        fetch(url)
            .then(res => res.json())
            .then(data => {
                document.getElementById('viewSongTitle').innerText = data.title || 'Song Details';
                document.getElementById('viewSongTitleText').innerText = data.title || '';
                document.getElementById('viewSongNote').innerText = data.note || '';
                document.getElementById('viewSongSuggester').innerText = data.suggester_name || '';
                document.getElementById('viewSongCreatedAt').innerText =
                    new Date(data.created_at).toLocaleString();

                // Status with badge colors
                const statusEl = document.getElementById('viewSongStatus');
                statusEl.innerText = data.status || '';
                statusEl.className = "px-2 py-1 rounded text-xs";
                if (data.status === 'accepted') {
                    statusEl.classList.add("bg-green-600");
                } else if (data.status === 'rejected') {
                    statusEl.classList.add("bg-red-600");
                } else {
                    statusEl.classList.add("bg-yellow-500");
                }

                // File download link
                const fileEl = document.getElementById('viewSongFile');
                const downloadBtn = document.getElementById('viewSongDownload');
                fileEl.innerHTML = '';
                downloadBtn.classList.add("hidden");

                if (data.file) {
                    const url = `{{ asset('storage') }}/${data.file}`;
                    fileEl.innerHTML = `<a href="${url}" target="_blank" class="text-indigo-600 hover:underline">View File</a>`;
                    downloadBtn.href = url;
                    downloadBtn.download = data.title || 'song';
                    downloadBtn.classList.remove("hidden");
                } else {
                    fileEl.innerText = "No file available";
                }

                // URL link
                const urlEl = document.getElementById('viewSongUrl');
                urlEl.innerHTML = '';
                if (data.url) {
                    urlEl.innerHTML = `<a href="${data.url}" target="_blank" class="text-blue-600 hover:underline">Open Link</a>`;
                } else {
                    urlEl.innerText = "No URL provided";
                }
            });
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
@endsection