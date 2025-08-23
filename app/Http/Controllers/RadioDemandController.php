<?php
// app/Http/Controllers/RadioDemandController.php
namespace App\Http\Controllers;

use App\Models\RadioDemand;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\RadioAccountCreated;
use App\Models\Radio;
use App\Models\User;
use Illuminate\Support\Str;

class RadioDemandController extends Controller
{
    public function index(Request $request)
    {
        $query = RadioDemand::query();

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('radio_name', 'like', "%{$search}%")
                    ->orWhere('manager_name', 'like', "%{$search}%")
                    ->orWhere('manager_email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $demands = $query->latest()->paginate(10);

        return view('radio-demands.index', compact('demands'));
    }

    public function create()
    {
        $roles = Role::orderBy('hierarchy_level')->get(['id', 'name']);
        return view('radio-demands.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'radio_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'founding_date' => 'required|date',
            'manager_name' => 'required|string|max:255',
            'manager_email' => 'required|email|max:255',
            'manager_phone' => 'required|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8048',
            'team_members' => 'required|array|min:5',
            'team_members.*.name' => 'required|string|max:255',
            'team_members.*.email' => 'required|email|max:255',
            'team_members.*.phone' => 'required|string|max:20',
            'team_members.*.role' => 'required|string|max:255',
        ]);

        $data = $request->except('logo');
        $data['team_members'] = json_encode($request->team_members);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('radio-demand-logos', 'public');
        }

        RadioDemand::create($data);

        return redirect('/')
            ->with('success', 'Your request has been submitted successfully. We will contact you soon.');
    }

    public function updateStatus(Request $request, RadioDemand $demand)
    {
        // Prevent changing status if already approved or rejected
        if (in_array($demand->status, ['approved', 'rejected'])) {
            return back()->with('error', 'This demand has already been processed and cannot be changed.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_process,approved,rejected'
        ]);

        $demand->update(['status' => $request->status]);

        // If approved, create radio station and accounts
        if ($request->status === 'approved') {
            $this->createRadioFromDemand($demand);
        }

        return back()->with('success', 'Demand status updated successfully.');
    }

    protected function createRadioFromDemand(RadioDemand $demand)
    {
        // Create the radio station
        $radio = Radio::create([
            'name' => $demand->radio_name,
            'description' => $demand->description,
            'manager_id' => null, // Will be set after creating manager
            'status' => 'active',
            'logo_path' => $demand->logo_path,
        ]);

        // Create manager account
        $managerPassword = Str::random(12);
        $managerRole = Role::where('name', 'manager')->first();

        $manager = User::create([
            'name' => $demand->manager_name,
            'email' => $demand->manager_email,
            'phone_number' => $demand->manager_phone,
            'password' => Hash::make($managerPassword),
            'role_id' => $managerRole->id,
            'radio_id' => $radio->id,
            'status' => 'active',
        ]);

        // Update radio with manager ID
        $radio->update(['manager_id' => $manager->id]);

        // Create team member accounts
        // Option 1: If it's already an array (from model casting)
        if (is_array($demand->team_members)) {
            // Treatment for array version
            foreach ($demand->team_members as $member) {
                echo $member['name'] . " - " . $member['role'] . "<br>";
            }
        }
        // Option 2: If it's JSON string (raw database value)
        else {
            $teamMembers = json_decode($demand->team_members, true);
            foreach ($teamMembers as $member) {
                echo $member['name'] . " - " . $member['role'] . "<br>";
            }
        }

        foreach ($teamMembers as $member) {
            $memberPassword = Str::random(12);
            $memberRole = Role::where('name', $member['role'])->first();

            if ($memberRole) {
                User::create([
                    'name' => $member['name'],
                    'email' => $member['email'],
                    'phone_number' => $member['phone'],
                    'password' => Hash::make($memberPassword),
                    'role_id' => $memberRole->id,
                    'radio_id' => $radio->id,
                    'status' => 'active',
                ]);

                // Send email to team member
                Mail::to($member['email'])->send(new RadioAccountCreated(
                    $member['name'],
                    $member['email'],
                    $memberPassword,
                    $radio->name,
                    $member['role']
                ));
            }
        }

        // Send email to manager
        Mail::to($demand->manager_email)->send(new RadioAccountCreated(
            $demand->manager_name,
            $demand->manager_email,
            $managerPassword,
            $radio->name,
            'Manager'
        ));
    }
    public function show(RadioDemand $demand)
    {
        return view('radio-demands.show', compact('demand'));
    }
    public function destroy(RadioDemand $demand)
    {
        if ($demand->logo_path) {
            Storage::disk('public')->delete($demand->logo_path);
        }
        $demand->delete();
        return back()->with('success', 'Demand deleted successfully.');
    }
}
