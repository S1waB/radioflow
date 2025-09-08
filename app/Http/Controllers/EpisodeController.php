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
use Carbon\Carbon;

class EpisodeController extends Controller
{
    // ------------------- EPISODES LIST -------------------
   public function index($radioId, $emissionId)
{
    $emission = Emission::with('seasons')->findOrFail($emissionId);
    $seasons = $emission->seasons()->orderBy('number', 'desc')->get();

    $episodes = Episode::with('season')
        ->whereHas('season', function($q) use ($emissionId) {
            $q->where('emission_id', $emissionId);
        });

    // Filter by season if provided
    if ($seasonId = request('season_id')) {
        $episodes->where('season_id', $seasonId);
    }

    // Filter by episode number if needed
    if ($number = request('number')) {
        $episodes->where('number', $number);
    }

    $episodes = $episodes->orderBy('number', 'asc')->paginate(10)->withQueryString();

    return view('episodes.index', compact('emission', 'episodes', 'seasons'));
}


    // ------------------- CREATE EPISODE -------------------
    public function store(Request $request, Emission $emission, Season $season)
    {
        $validated = $request->validate([
            'aired_on' => 'required|date', // datetime-local input
            'duration_minutes' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'conducteur' => 'nullable|file|mimes:pdf,doc,docx',
        ]);

        // Parse datetime-local into date and time
        $dateTime = \Carbon\Carbon::parse($request->input('aired_on'));
        $validated['aired_on'] = $dateTime->toDateString(); // YYYY-MM-DD
        $validated['time'] = $dateTime->format('H:i:s');   // HH:MM:SS

        // Automatically set episode number
        $validated['number'] = $season->episodes()->count() + 1;

        // Upload conducteur file
        if ($request->hasFile('conducteur')) {
            $validated['conducteur_path'] = $request->file('conducteur')->store('conducteurs', 'public');
        }

        $season->episodes()->create($validated);

        return redirect()->back()->with('success', 'Episode created successfully!');
    }

    // ------------------- SHOW EPISODE (FOR EDIT MODAL) -------------------
    public function show(Season $season, Episode $episode)
    {
        $episode->load(['songs', 'guests', 'materials']);
        return view('emissions.episode', compact('season', 'episode'));
    }


   


    // ------------------- UPDATE EPISODE -------------------
    public function update(Request $request, Season $season, Episode $episode)
    {
        $validated = $request->validate([
            'season_id' => 'required|exists:seasons,id',
            'aired_on' => 'required|date',
            'duration_minutes' => 'required|integer|min:10',
            'description' => 'nullable|string',
            'conducteur' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Parse datetime
        $aired = Carbon::parse($validated['aired_on']);
        $validated['aired_on'] = $aired->toDateString();
        $validated['time'] = $aired->format('H:i:s');

        // Upload conducteur
        if ($request->hasFile('conducteur')) {
            if ($episode->conducteur_path) {
                Storage::disk('public')->delete($episode->conducteur_path);
            }
            $validated['conducteur_path'] = $request->file('conducteur')->store('conducteurs', 'public');
        }

        $episode->update($validated);

        return redirect()->back()->with('success', 'Episode updated successfully!');
    }

    // ------------------- DELETE EPISODE -------------------
    public function destroy(Episode $episode)
{
    // Delete conducteur file if exists
    if ($episode->conducteur_path) {
        Storage::disk('public')->delete($episode->conducteur_path);
    }

    $episode->delete();

    return redirect()->back()->with('success', 'Episode deleted successfully!');
}

}
