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
            ->whereHas('season', function ($q) use ($emissionId) {
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
            'aired_on' => 'required|date',
            'time' => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:10',
            'description' => 'nullable|string',
            'conducteur' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Combine date + time into Carbon
        $airedAt = Carbon::parse($validated['aired_on'] . ' ' . $validated['time']);

        // 1. Check uniqueness (no duplicate exact datetime)
        $exists = Episode::whereHas('season', function ($q) use ($emission) {
            $q->where('emission_id', $emission->id);
        })
            ->where('aired_on', $validated['aired_on'])
            ->where('time', $validated['time'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['time' => 'This date & time is already used by another episode.']);
        }

        // 2. Check 30 min gap with other episodes
        $conflict = Episode::whereHas('season', function ($q) use ($emission) {
            $q->where('emission_id', $emission->id);
        })
            ->get()
            ->first(function ($ep) use ($airedAt) {
                $epDateTime = Carbon::parse($ep->aired_on . ' ' . $ep->time);
                return $airedAt->between(
                    $epDateTime->copy()->subMinutes(30),
                    $epDateTime->copy()->addMinutes(30)
                );
            });

        if ($conflict) {
            return back()->with('time', 'Episodes must be at least 30 minutes apart.');
        }

        // Auto-assign episode number
        $validated['number'] = $season->episodes()->count() + 1;

        // Upload conducteur
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
    // Replace your update method with this one
   // ------------------- UPDATE EPISODE -------------------
    public function update(Request $request, Emission $emission, Season $season, Episode $episode)
    {
        $validated = $request->validate([
            'aired_datetime' => 'required|date',
            'duration_minutes' => 'required|integer|min:10',
            'description' => 'nullable|string',
            'conducteur' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $aired = Carbon::parse($validated['aired_datetime']);
        $validated['aired_on'] = $aired->toDateString();
        $validated['time'] = $aired->format('H:i:s');
        unset($validated['aired_datetime']);

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
    public function destroy(Emission $emission, Season $season, Episode $episode)
    {
        // Delete conducteur file if exists
        if ($episode->conducteur_path) {
            Storage::disk('public')->delete($episode->conducteur_path);
        }

        $episode->delete();

        return redirect()->back()->with('success', 'Episode deleted successfully!');
    }
}
