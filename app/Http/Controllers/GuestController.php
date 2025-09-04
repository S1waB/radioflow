<?php
namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Radio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuestController extends Controller
{
    // List guests with search & optional filters
    public function index(Request $request, Radio $radio)
    {
        $search = $request->get('search');
        $query = Guest::where('radio_id', $radio->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $guests = $query->latest()->paginate(10)->withQueryString();

        return view('guests.index', compact('guests', 'radio', 'search'));
    }

    // Create guest
    public function store(Request $request, Radio $radio)
    {
        $data = $request->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'email'        => 'required|email|unique:guests,email',
            'phone_number' => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:500',
            'description'  => 'nullable|string|max:1000',
            'profile_photo'=> 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data['radio_id'] = $radio->id;

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('guest_photos', 'public');
        }

        Guest::create($data);

        return redirect()->back()->with('success', 'Guest created successfully!');
    }

    // Update guest
    public function update(Request $request, Radio $radio, Guest $guest)
    {
        $data = $request->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'email'        => 'required|email|unique:guests,email,' . $guest->id,
            'phone_number' => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:500',
            'description'  => 'nullable|string|max:1000',
            'profile_photo'=> 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($guest->profile_photo) {
                Storage::disk('public')->delete($guest->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('guest_photos', 'public');
        }

        $guest->update($data);

        return redirect()->back()->with('success', 'Guest updated successfully!');
    }

    // Delete guest
    public function destroy(Radio $radio, Guest $guest)
    {
        if ($guest->profile_photo) {
            Storage::disk('public')->delete($guest->profile_photo);
        }

        $guest->delete();

        return redirect()->back()->with('success', 'Guest deleted successfully!');
    }
}
