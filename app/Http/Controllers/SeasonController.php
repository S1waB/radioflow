<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\Emission;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    /**
     * List seasons of an emission
     */
    public function index(Emission $emission)
    {
        $seasons = $emission->seasons()->withCount('episodes')->get();

        return view('seasons.index', compact('emission','seasons'));
    }

    /**
     * Auto-create a new season for an emission
     */
    public function store(Emission $emission)
    {
        $lastSeason = $emission->seasons()->orderByDesc('number')->first();
        $newNumber = $lastSeason ? $lastSeason->number + 1 : 1;

        $season = $emission->seasons()->create([
            'number' => $newNumber,
        ]);

        return back()->with('success',"Season {$season->number} created!");
    }

    /**
     * Show season details with episodes
     */
    public function show(Emission $emission, Season $season)
    {
        $season->load('episodes');
        return view('seasons.show', compact('emission','season'));
    }

    /**
     * Update season info
     */
    public function update(Request $request, Emission $emission, Season $season)
    {
        $validated = $request->validate([
            'starts_at' => 'nullable|date',
            'ends_at'   => 'nullable|date|after_or_equal:starts_at',
        ]);

        $season->update($validated);

        return back()->with('success',"Season {$season->number} updated!");
    }

    /**
     * Delete season
     */
    public function destroy(Emission $emission, Season $season)
    {
        $season->delete();
        return back()->with('success',"Season {$season->number} deleted!");
    }
}
