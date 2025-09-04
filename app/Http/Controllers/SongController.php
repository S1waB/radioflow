<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Radio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SongController extends Controller
{
    // List songs
    public function index(Radio $radio, Request $request)
    {
        $query = Song::where('radio_id', $radio->id);

        // Optional search
        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        // Optional filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $songs = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('songs.index', compact('radio', 'songs'));
    }

    // Store new song
    public function store(Request $request, Radio $radio)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|url',
            'note' => 'nullable|string',
            'file' => 'nullable|file|mimes:mp3,wav,ogg',
            'status' => 'required|in:accepted,rejected,pending',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName(); // Keep original name
            $data['file'] = $file->storeAs('songs', $filename, 'public');
        }

        $data['suggester_id'] = auth()->id();
        $data['radio_id'] = $radio->id;

        Song::create($data);

        return back()->with('success', 'Song created successfully.');
    }

    // Update existing song
    public function update(Radio $radio, $songId, Request $request)
    {
        $song = Song::where('radio_id', $radio->id)->findOrFail($songId);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|url',
            'note' => 'nullable|string',
            'file' => 'nullable|file|mimes:mp3,wav,ogg',
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        if ($request->hasFile('file')) {
            if ($song->file) {
                Storage::disk('public')->delete($song->file); // Delete old file
            }
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $data['file'] = $file->storeAs('songs', $filename, 'public');
        }

        $song->update($data);

        return back()->with('success', 'Song updated successfully.');
    }

    // Delete song
    public function destroy(Radio $radio, $songId)
    {
        $song = Song::where('radio_id', $radio->id)->findOrFail($songId);

        if ($song->file) {
            Storage::disk('public')->delete($song->file);
        }

        $song->delete();

        return back()->with('success', 'Song deleted successfully.');
    }

    // Show song details (for AJAX)
    public function show(Radio $radio, Song $song)
    {
        return response()->json([
            'title' => $song->title,
            'file' => $song->file,
            'url' => $song->url,
            'note' => $song->note,
            'status' => $song->status,
            'suggester_name' => $song->suggester->name ?? '',
            'created_at' => $song->created_at,
        ]);
    }
}
