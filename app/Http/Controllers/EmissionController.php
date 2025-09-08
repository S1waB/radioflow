<?php

namespace App\Http\Controllers;

use App\Models\Emission;
use App\Models\Episode;
use App\Models\Radio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmissionController extends Controller
{
    // List all emissions of a radio
    public function index(Radio $radio)
    {
        $types = Emission::select('type')->distinct()->pluck('type'); // all distinct emission types
        $animateurs = $radio->members; // or however you get animateurs
        $emissions = Emission::where('radio_id', $radio->id);

        if ($search = request('search')) {
            $emissions->where('name', 'like', "%{$search}%");
        }

        if ($type = request('type')) {
            $emissions->where('type', $type);
        }

        $animateurs = User::whereHas('role', function ($query) use ($radio) {
            $query->where('radio_id', $radio->id)->where('name', 'animateur');
        })->get();



        $emissions = $emissions->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('emissions.index', compact('radio', 'emissions', 'types', 'animateurs'));
    }


    // Show emission details
    // Show emission details
    public function show(Radio $radio, Emission $emission)
    {
        $seasons = $emission->seasons()->orderBy('created_at', 'desc')->get();
        $lastSeason = $seasons->first();

        $episodesQuery = Episode::whereIn('season_id', $seasons->pluck('id'));

        // --- Search filter ---
        if ($search = request('search')) {
            $episodesQuery->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%");
            });
        }

        // --- Season filter ---
        if ($seasonId = request('season_id')) {
            $episodesQuery->where('season_id', $seasonId);
        }

        $episodes = $episodesQuery->orderBy('number', 'asc')->paginate(10)->withQueryString();

        $animateurs = User::whereHas('role', function ($query) use ($radio) {
            $query->where('radio_id', $radio->id)->where('name', 'animateur');
        })->get();

        return view('emissions.show', compact('radio', 'emission', 'seasons', 'episodes', 'lastSeason', 'animateurs'));
    }


    // Store new emission
    public function store(Request $request, Radio $radio)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'animateur_id' => 'nullable|exists:users,id',
            'type' => 'nullable|string|max:255',
            'duration_minutes' => 'nullable|integer',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('emission_logos', 'public');
        }

        $validated['radio_id'] = $radio->id;

        $emission = Emission::create($validated);


        // Automatically create the first season
        $emission->seasons()->create([
            'number' => 1,
            // description is optional
        ]);

        return back()->with('success', 'Emission created successfully with Season 1!');
    }

    // Update emission
    public function update(Request $request, Radio $radio, Emission $emission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'animateur_id' => 'nullable|exists:users,id',
            'type' => 'nullable|string|max:255',
            'duration_minutes' => 'nullable|integer',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($emission->logo_path) {
                Storage::disk('public')->delete($emission->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('emission_logos', 'public');
        }

        $emission->update($validated);

        return back()->with('success', 'Emission updated successfully!');
    }

    // Delete emission
    public function destroy(Radio $radio, Emission $emission)
    {
        $emission->delete();
        return back()->with('success', 'Emission deleted successfully!');
    }

    // app/Http/Controllers/EmissionController.php

    public function team(Radio $radio, Emission $emission)
    {
        // Ensure the emission belongs to this radio
        if ($emission->radio_id !== $radio->id) {
            abort(404);
        }

        $members = $emission->members; // your belongsToMany relation
        return view('emissions.team', compact('radio', 'emission', 'members'));
    }
}
