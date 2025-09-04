<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Radio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function index()
    {
        $query = Team::with(['radio', 'users'])->latest();

        // Search filter
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('radio', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Radio filter
        if ($radio_id = request('radio_id')) {
            $query->where('radio_id', $radio_id);
        }

        $teams = $query->paginate(10);
        $radios = Radio::where('status', 'active')->get();

        return view('teams.index', compact('teams', 'radios'));
    }

    public function create()
    {
        $radios = Radio::where('status', 'active')->get();
        return view('teams.create', compact('radios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'radio_id' => 'required|exists:radios,id',
        ]);

        Team::create($request->only(['name', 'description', 'radio_id']));

        return back()->with('success', 'Team created successfully.');
    }
    public function show(Team $team)
    {
        $team->load([
            'radio',
            'users' => function ($query) {
                $query->with('role');
            },
            'tasks' => function ($query) {
                $query->with(['owner', 'assigned', 'comments']); // ✅ use correct relationships
            }
        ]);

        $availableMembers = User::where('radio_id', $team->radio_id)
            ->where('status', 'active')
            ->whereDoesntHave('teams', function ($query) use ($team) {
                $query->where('teams.id', $team->id);
            })
            ->get();

        return view('teams.show', compact('team', 'availableMembers'));
    }


    public function edit(Team $team)
    {
        $radios = Radio::where('status', 'active')->get();
        return view('teams.edit', compact('team', 'radios'));
    }

    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'radio_id' => 'required|exists:radios,id',
        ]);

        $team->update($request->only(['name', 'description', 'radio_id']));

        return redirect()->route('teams.index')->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        // Detach all users from the team
        $team->users()->detach();

        // Delete the team
        $team->delete();

        return redirect()->route('teams.index')->with('success', 'Team deleted successfully.');
    }

    public function addMember(Request $request, Team $team)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // Check if user belongs to the same radio as the team
        if ($user->radio_id !== $team->radio_id) {
            return back()->with('error', 'User must belong to the same radio as the team.');
        }

        // Check if user is already in the team
        if ($team->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User is already a member of this team.');
        }

        $team->users()->attach($user->id);

        return back()->with('success', 'User added to team successfully.');
    }

    public function removeMember(Request $request, Team $team)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $team->users()->detach($request->user_id);

        return back()->with('success', 'User removed from team successfully.');
    }

  public function getMembers(Team $team)
{
    $members = $team->users->map(function($user) {
        return [
            'id' => $user->id,
            'name' => $user->name
        ];
    });
    
    return response()->json($members);
}
}
