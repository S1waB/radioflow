<?php

namespace App\Http\Controllers;

use App\Models\Radio;
use App\Models\Role;
use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RadioController extends Controller
{
    public function index()
    {
        $query = Radio::query()->with(['manager', 'teams']);

        if ($search = request()->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('Country', 'like', "%{$search}%")
                    ->orWhereHas('manager', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = request()->get('status')) {
            $query->where('status', $status);
        }

        $radios = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('radios.index', compact('radios'));
    }

    public function create()
    {
        $availableManagers = User::whereHas('role', function ($query) {
            $query->where('name', 'manager');
        })->whereDoesntHave('managedRadio')
            ->get();

        return view('radios.create', compact('availableManagers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:radios',
            'description' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'Country' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,desactive', // Fixed: changed 'inactive' to 'desactive'
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8048',
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('radio-logos', 'public');
        }

        $radio = Radio::create($data);

        // Create default teams for the radio
        $defaultTeams = [
            ['name' => 'Technical Team', 'description' => 'Technical operations and maintenance'],
            ['name' => 'Content Team', 'description' => 'Content creation and programming'],
            ['name' => 'Marketing Team', 'description' => 'Promotion and audience engagement'],
        ];

        foreach ($defaultTeams as $teamData) {
            Team::create(array_merge($teamData, ['radio_id' => $radio->id]));
        }

        return redirect()->route('radios.index')->with('success', 'Radio station created successfully with default teams.');
    }

    public function edit(Radio $radio)
    {
        // Get the current manager
        $currentManager = $radio->manager;

        // Get available managers (managers without radios)
        $availableManagers = User::whereHas('role', fn($q) => $q->where('name', 'manager'))
            ->whereDoesntHave('managedRadio')
            ->get();

        // Combine current manager with available managers, ensuring no duplicates
        $managers = $availableManagers->push($currentManager)->unique('id');

        return view('radios.edit', compact('radio', 'managers'));
    }

    public function update(Request $request, Radio $radio)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:radios,name,' . $radio->id,
            'description' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'Country' => 'required|string|max:255',
            'manager_id' => 'required|exists:users,id',
            'status' => 'required|in:active,desactive', // Fixed: changed 'inactive' to 'desactive'
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8048',
            'remove_logo' => 'nullable|in:0,1',
        ]);

        $data = $request->only([
            'name',
            'description',
            'phone_number',
            'address',
            'Country',
            'manager_id',
            'status',
        ]);

        if ($request->input('remove_logo') == '1' && $radio->logo_path) {
            Storage::disk('public')->delete($radio->logo_path);
            $data['logo_path'] = null;
        }

        if ($request->hasFile('logo')) {
            if ($radio->logo_path) {
                Storage::disk('public')->delete($radio->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('radio-logos', 'public');
        }

        $radio->update($data);

        return back()->with('success', 'Radio station updated successfully.');
    }

    public function destroy(Radio $radio)
    {
        if ($radio->logo_path) {
            Storage::disk('public')->delete($radio->logo_path);
        }

        // Delete associated teams
        $radio->teams()->delete();

        $radio->delete();

        return redirect()->route('radios.index')->with('success', 'Radio station and associated teams deleted successfully.');
    }

    public function changeStatus(Radio $radio)
    {
        $radio->update([
            'status' => $radio->status === 'active' ? 'desactive' : 'active'
        ]);

        return back()->with('success', 'Radio status updated successfully.');
    }

    public function show(Radio $radio, Request $request)
    {
        $radio->load(['manager', 'teams.users', 'teams.users.role']);

        // Base query for radio members
        $membersQuery = User::where('radio_id', $radio->id);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $membersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if ($request->filled('role')) {
            $membersQuery->where('role_id', $request->input('role'));
        }

        // Apply status filter
        if ($request->filled('status')) {
            $membersQuery->where('status', $request->input('status'));
        }

        // Get filtered members
        $filteredMembers = $membersQuery->with(['role', 'teams'])->get();

        // Other data
        $availableUsers = User::where(function ($q) use ($radio) {
            $q->whereNull('radio_id')
                ->orWhere(function ($q2) use ($radio) {
                    $q2->where('radio_id', $radio->id)
                        ->where('status', 'desactive');
                });
        })->where('id', '!=', $radio->manager_id)->get();

        $roles = Role::where('radio_id', $radio->id)
            ->orderBy('name')
            ->get();

        return view('radios.show', compact(
            'radio',
            'filteredMembers',
            'availableUsers',
            'roles'
        ));
    }

    public function addMember(Request $request, Radio $radio)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->radio_id && $user->radio_id != $radio->id && $user->status === 'active') {
            return back()->with('error', 'This user is already active in another radio station.');
        }

        $user->update([
            'radio_id' => $radio->id,
            'status' => 'active'
        ]);

        return back()->with('success', $user->status === 'desactive' ? 'Member reactivated successfully.' : 'Member added successfully.');
    }

    public function removeMember(Request $request, Radio $radio)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->radio_id != $radio->id) {
            return back()->with('error', 'This user does not belong to your radio station.');
        }

        if ($user->id === $radio->manager_id) {
            return back()->with('error', 'You cannot remove the manager of the radio station.');
        }

        // Remove user from all teams in this radio
        $radio->teams()->each(function ($team) use ($user) {
            $team->users()->detach($user->id);
        });

        $user->update(['status' => 'desactive']);

        return back()->with('success', 'Member removed and deactivated successfully.');
    }

    public function showAddMemberForm(Radio $radio)
    {
        $radio->load('users', 'manager');

        // Users who are not active in any radio OR were inactive in this radio
        $availableUsers = User::where(function ($q) use ($radio) {
            $q->whereNull('radio_id')
                ->orWhere(function ($q2) use ($radio) {
                    $q2->where('radio_id', $radio->id)
                        ->where('status', 'desactive');
                });
        })->get();

        // All roles except admin
        $roles = Role::whereNotIn('name', ['admin'])->orderBy('name')->get();

        return view('radios.add_member', compact('radio', 'availableUsers', 'roles'));
    }
}
