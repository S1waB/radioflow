<?php

namespace App\Http\Controllers;

use App\Models\Radio;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RadioController extends Controller
{
    public function index()
    {
        $query = Radio::query()->with(['manager', 'team']); // Eager load manager and team
        //  Search via GET
        if ($search = request()->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('Country', 'like', "%{$search}%")
                    ->orWhereHas('manager', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        //  Filter by status via GET
        if ($status = request()->get('status')) {
            $query->where('status', $status);
        }

        //  Paginate
        $radios = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('radios.index', compact('radios'));
    }

    public function create()
    {
        // Get users who have the 'manager' role and are not already assigned as radio managers
        $availableManagers = User::whereHas('role', function ($query) {
            $query->where('name', 'manager');
        })
            ->whereDoesntHave('managedRadio') // Assumes 'managedRadio' is the inverse relation from User -> Radio
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
            'status' => 'required|in:active,inactive',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8048', // <-- match input name here
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            // Store file and save path in 'logo_path' key
            $data['logo_path'] = $request->file('logo')->store('radio-logos', 'public');
        }

        Radio::create($data);

        return redirect()->route('radios.index')->with('success', 'Radio station created successfully.');
    }


    public function edit(Radio $radio)
    {
        // Get users with role 'manager' who don't manage other radios or manage this radio
        $managers = User::whereHas('role', fn($q) => $q->where('name', 'manager'))
            ->where(function ($query) use ($radio) {
                $query->whereDoesntHave('radio')  // users who manage no radio
                    ->orWhere('radio_id', $radio->id);  // or manage this radio
            })
            ->get();

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
            'status' => 'required|in:active,inactive',
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

        // Handle logo removal
        if ($request->input('remove_logo') == '1' && $radio->logo_path) {
            Storage::disk('public')->delete($radio->logo_path);
            $data['logo_path'] = null;
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($radio->logo_path) {
                Storage::disk('public')->delete($radio->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('radio-logos', 'public');
        }

        $radio->update($data);

        return redirect()->route('radios.index')->with('success', 'Radio station updated successfully.');
    }

    public function destroy(Radio $radio)
    {
        // Delete logo if exists
        if ($radio->logo) {
            Storage::disk('public')->delete($radio->logo);
        }
        $radio->delete();
        return redirect()->route('radios.index')->with('success', 'Radio station deleted successfully.');
    }

    public function changeStatus(Radio $radio)
    {
        $radio->update([
            'status' => $radio->status === 'active' ? 'desactive' : 'active'
        ]);

        return back()->with('success', 'Radio status updated successfully.');
    }

    public function show(Radio $radio)
    {
        $radio->load(['manager', 'team', 'team.role']);

        // Get active members of this radio
        $activeMembers = $radio->team()->where('status', 'active')->get();

        // Get inactive users who were previously members of this radio
        $inactiveMembers = User::where('radio_id', $radio->id)
            ->where('status', 'desactive')
            ->get();

        // Get users who don't belong to any radio (potential new members)
        $availableUsers = User::whereNull('radio_id')
            ->where('id', '!=', $radio->manager_id) // Exclude the manager
            ->where('status', 'active')
            ->get();

        // Get all roles except admin (or filter as needed)
        $roles = Role::whereNotIn('name', ['admin']) // Exclude admin role if needed
            ->orderBy('name')
            ->get();

        return view('radios.show', compact(
            'radio',
            'activeMembers',
            'inactiveMembers',
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

        // Check if user is already active in another radio
        if ($user->radio_id && $user->status === 'active') {
            return back()->with('error', 'This user is already active in another radio station.');
        }

        $user->update([
            'radio_id' => $radio->id,
            'status' => 'active'
        ]);

        return back()->with('success', 'Member added successfully.');
    }

    public function removeMember(Request $request, Radio $radio)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // Verify the user belongs to this radio
        if ($user->radio_id != $radio->id) {
            return back()->with('error', 'This user does not belong to your radio station.');
        }

        // Don't allow removing the manager
        if ($user->id === $radio->manager_id) {
            return back()->with('error', 'You cannot remove the manager of the radio station.');
        }

        $user->update([
            'status' => 'desactive'
        ]);

        return back()->with('success', 'Member removed and deactivated successfully.');
    }
}
