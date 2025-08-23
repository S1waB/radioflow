<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Radio;
use App\Models\Role;
use App\Models\RadioDemand;

class DashboardController extends Controller
{
    public function index()
    {
        // Counts
        $userCount = User::count();
        $radioCount = Radio::count();
        $activeRadios = Radio::where('status', 'active')->count();

        // Get roles and their user counts
        $usersByRole = Role::withCount('users')->get();

        // Radios by status
        $radiosByStatus = Radio::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // RadioDemands counts by status
        $demandsByStatus = RadioDemand::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Recent activities
        $recentUsers = User::with('role')->latest()->take(3)->get();
        $recentRadios = Radio::latest()->take(3)->get();

        $recentActivities = collect()
            ->merge($recentUsers->map(function ($user) {
                return [
                    'icon' => 'fas fa-user-plus',
                    'title' => 'New user registered',
                    'description' => $user->name . ' (' . $user->role->name . ')',
                    'time' => $user->created_at->diffForHumans(),
                    'color' => 'text-blue-500',
                    'created_at' => $user->created_at,
                ];
            }))
            ->merge($recentRadios->map(function ($radio) {
                return [
                    'icon' => 'fas fa-broadcast-tower',
                    'title' => 'New radio added',
                    'description' => $radio->name,
                    'time' => $radio->created_at->diffForHumans(),
                    'color' => 'text-primary',
                    'created_at' => $radio->created_at,
                ];
            }))
            ->sortByDesc('created_at')
            ->take(4);

        return view('dashboard', [
            'userCount' => $userCount,
            'radioCount' => $radioCount,
            'activeRadios' => $activeRadios,
            'usersByRole' => $usersByRole,
            'radiosByStatus' => $radiosByStatus,
            'demandsByStatus' => $demandsByStatus,    // Added this
            'recentActivities' => $recentActivities,
        ]);
    }
}
