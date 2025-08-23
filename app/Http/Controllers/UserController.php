<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Radio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $query = User::with(['role', 'radio'])->latest();

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
        // Get radio_id from query string if present
        $radio_id = request()->query('radio_id');

        return view('users.create', compact('roles', 'radios', 'radio_id'));
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
            'status' => 'required|in:active,inactive',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only([
            'name',
            'email',
            'phone_number',
            'address',
            'bio',
            'role_id',
            'radio_id'
        ]);

        $data['password'] = Hash::make($request->password);
        $data['status'] = $request->status === 'inactive' ? 'desactive' : 'active';

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo_path'] = $path;
        }

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }


    public function edit(User $user)
    {
        $roles = Role::orderBy('hierarchy_level')->get();
        $radios = Radio::where('status', 'active')->get();
        return view('users.edit', compact('user', 'roles', 'radios'));
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
            'status' => 'required|in:active,inactive', // Match your form values
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

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

        // Convert status to match database if needed
        if ($validated['status'] === 'inactive') {
            $validated['status'] = 'desactive';
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function changeStatus(User $user)
    {
        $user->update([
            'status' => $user->status === 'active' ? 'desactive' : 'active'
        ]);
        $user->save();
        return back()->with('success', 'User status updated successfully');
    }

public function updateRole(Request $request, User $user)
{
    $request->validate([
        'role_id' => 'nullable|exists:roles,id'
    ]);

    $user->update([
        'role_id' => $request->role_id ?: null
    ]);

    return back()->with('success', 'User role updated successfully.');
}

}
