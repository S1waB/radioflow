<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Radio;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $query = User::with(['role', 'radio', 'teams.radio'])->latest();

        // Search filter
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($role = request('role')) {
            $query->where('role_id', $role);
        }

        // Status filter
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $users = $query->paginate(10);
        $roles = Role::orderBy('hierarchy_level')->get();
        $radios = Radio::where('status', 'active')->get();

        return view('users.index', compact('users', 'roles', 'radios'));
    }

    public function create()
    {
        $roles = Role::orderBy('hierarchy_level')->get();
        $radios = Radio::where('status', 'active')->get();
        $teams = Team::with('radio')->get();
        
        // Get radio_id from query string if present
        $radio_id = request()->query('radio_id');

        return view('users.create', compact('roles', 'radios', 'teams', 'radio_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'role_id' => 'required|exists:roles,id',
            'radio_id' => 'nullable|exists:radios,id',
            'teams' => 'nullable|array',
            'teams.*' => 'exists:teams,id',
            'status' => 'required|in:active,desactive', // Fixed: changed 'inactive' to 'desactive'
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only([
            'name',
            'email',
            'phone_number',
            'address',
            'bio',
            'role_id',
            'radio_id',
            'status' // Added status to data array
        ]);

        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo_path'] = $path;
        }

        $user = User::create($data);

        // Assign teams (only if they belong to the selected radio)
        if ($request->has('teams') && $request->radio_id) {
            $validTeams = Team::where('radio_id', $request->radio_id)
                ->whereIn('id', $request->teams)
                ->pluck('id')
                ->toArray();
            
            $user->teams()->sync($validTeams);
        }

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('hierarchy_level')->get();
        $radios = Radio::where('status', 'active')->get();
        $teams = Team::with('radio')->get();
        
        return view('users.edit', compact('user', 'roles', 'radios', 'teams'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'role_id' => 'required|exists:roles,id',
            'radio_id' => 'nullable|exists:radios,id',
            'teams' => 'nullable|array',
            'teams.*' => 'exists:teams,id',
            'status' => 'required|in:active,desactive', // Fixed: changed 'inactive' to 'desactive'
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle password update if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed'
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old profile photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Store new photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo_path'] = $path;
        }

        $user->update($validated);

        // Update team assignments
        if ($request->has('teams') && $request->radio_id) {
            $validTeams = Team::where('radio_id', $request->radio_id)
                ->whereIn('id', $request->teams)
                ->pluck('id')
                ->toArray();
            
            $user->teams()->sync($validTeams);
        } else {
            $user->teams()->detach();
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Delete profile photo if exists
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        
        // Detach from teams before deletion
        $user->teams()->detach();
        
        $user->delete();
        
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function changeStatus(User $user)
    {
        $user->update([
            'status' => $user->status === 'active' ? 'desactive' : 'active'
        ]);
        
        return back()->with('success', 'User status updated successfully');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id' // Changed from nullable to required
        ]);

        $user->update([
            'role_id' => $request->role_id
        ]);

        return back()->with('success', 'User role updated successfully.');
    }
}