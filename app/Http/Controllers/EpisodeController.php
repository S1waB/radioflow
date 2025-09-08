<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Emission;
use App\Models\Season;
use App\Models\Guest;
use App\Models\Song;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EpisodeController extends Controller
{
    // ------------------- EPISODES CRUD -------------------
    public function index(Season $season)
    {
        $episodes = $season->episodes()->with(['songs', 'guests', 'materials'])->get();
        return view('episodes.index', compact('season', 'episodes'));
    }

    public function store(Request $request, Emission $emission, Season $season)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'aired_on' => 'nullable|date',
            'duration_minutes' => 'nullable|integer',
            'description' => 'nullable|string',
            'conducteur' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('conducteur')) {
            $validated['conducteur_path'] = $request->file('conducteur')->store('conducteurs', 'public');
        }

        $episode = $season->episodes()->create($validated);

        return redirect()->back()->with('success', 'Episode created successfully!');
    }


    public function show(Season $season, Episode $episode)
    {
        $episode->load(['songs', 'guests', 'materials']);
        return view('episodes.show', compact('season', 'episode'));
    }

    public function update(Request $request, Season $season, Episode $episode)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'aired_on' => 'nullable|date',
            'duration_minutes' => 'nullable|integer',
            'description' => 'nullable|string',
            'conducteur' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('conducteur')) {
            if ($episode->conducteur_path) {
                Storage::disk('public')->delete($episode->conducteur_path);
            }
            $validated['conducteur_path'] = $request->file('conducteur')->store('conducteurs', 'public');
        }

        $episode->update($validated);

        return back()->with('success', 'Episode updated successfully!');
    }

    public function destroy(Season $season, Episode $episode)
    {
        if ($episode->conducteur_path) {
            Storage::disk('public')->delete($episode->conducteur_path);
        }
        $episode->delete();

        return back()->with('success', 'Episode deleted successfully!');
    }

    // ------------------- EPISODE GUESTS -------------------
    public function addGuest(Request $request, Season $season, Episode $episode)
    {
        $validated = $request->validate([
            'guest_id' => 'required|exists:guests,id',
        ]);

        $episode->guests()->syncWithoutDetaching([$validated['guest_id']]);

        return back()->with('success', 'Guest added to episode!');
    }

    public function removeGuest(Season $season, Episode $episode, Guest $guest)
    {
        $episode->guests()->detach($guest->id);
        return back()->with('success', 'Guest removed from episode!');
    }

    // ------------------- EPISODE SONGS -------------------
    public function addSong(Request $request, Season $season, Episode $episode)
    {
        $validated = $request->validate([
            'song_id' => 'required|exists:songs,id',
        ]);

        $episode->songs()->syncWithoutDetaching([$validated['song_id']]);

        return back()->with('success', 'Song added to episode!');
    }

    public function removeSong(Season $season, Episode $episode, Song $song)
    {
        $episode->songs()->detach($song->id);
        return back()->with('success', 'Song removed from episode!');
    }

    // ------------------- MATERIALS -------------------
    public function addMaterial(Request $request, Season $season, Episode $episode)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store('materials', 'public');

        $episode->materials()->create([
            'file_path' => $path,
            'type' => $file->getClientOriginalExtension(),
        ]);

        return back()->with('success', 'Material uploaded!');
    }

    public function removeMaterial(Season $season, Episode $episode, Material $material)
    {
        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return back()->with('success', 'Material deleted!');
    }
}
