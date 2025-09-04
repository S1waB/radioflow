<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $radioId = $request->get('radio_id') ?? auth()->user()->radio_id;

        $query = Role::query();

        if ($radioId) {
            $query->where('radio_id', $radioId);
        } else {
            $query->orWhereNull('radio_id'); // global roles for admin
        }

        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('level')) {
            $query->where('hierarchy_level', (int) $request->get('level'));
        }

        $roles = $query->orderBy('hierarchy_level')->paginate(10)->appends($request->query());

        $levels = Role::query()
            ->when($radioId, fn($q) => $q->where('radio_id', $radioId))
            ->orWhereNull('radio_id')
            ->select('hierarchy_level')
            ->distinct()
            ->orderBy('hierarchy_level')
            ->pluck('hierarchy_level');

        return view('roles.index', compact('roles', 'levels'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hierarchy_level' => 'required|integer|min:1',
            'radio_id' => 'nullable|exists:radios,id',
        ]);

        Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'hierarchy_level' => $request->hierarchy_level,
            'radio_id' => $request->radio_id, // link to the radio
        ]);

        return redirect()->back()->with('success', 'Role created successfully.');
    }


    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string',
            'hierarchy_level' => 'required|integer|min:1',
        ]);

        $role->update($request->all());

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
