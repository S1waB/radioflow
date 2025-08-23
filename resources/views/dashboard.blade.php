@extends('layouts.app')
@section('content')
@include('layouts.header')
@section('title', 'Dashboard')

<div class="p-6">
    <h2 class="text-2xl font-bold mb-6 text-primary">Dashboard Overview</h2>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">  {{-- changed to 4 cols --}}
        <div class="bg-white p-6 shadow rounded-lg border-l-4 border-primary">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Users</h3>
            <p class="text-3xl font-bold text-primary">{{ $userCount }}</p>
        </div>
        <div class="bg-white p-6 shadow rounded-lg border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Radios</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $radioCount }}</p>
        </div>
        <div class="bg-white p-6 shadow rounded-lg border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Active Radios</h3>
            <p class="text-3xl font-bold text-green-600">{{ $activeRadios }}</p>
        </div>
        <div class="bg-white p-6 shadow rounded-lg border-l-4 border-yellow-500">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Radio Demands</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $demandsByStatus->sum('count') }}</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">  {{-- changed to 3 cols --}}
        <!-- Users By Role Chart -->
        <div class="bg-white p-6 shadow rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-semibold text-gray-800">Users by Role</h4>
                <a href="{{ route('users.index') }}" class="text-sm text-primary hover:underline">View All</a>
            </div>
            <div class="h-64">
                <canvas id="usersByRoleChart"></canvas>
            </div>
        </div>

        <!-- Radios By Status Chart -->
        <div class="bg-white p-6 shadow rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-semibold text-gray-800">Radios by Status</h4>
                <a href="{{ route('radios.index') }}" class="text-sm text-primary hover:underline">View All</a>
            </div>
            <div class="h-64">
                <canvas id="radiosByStatusChart"></canvas>
            </div>
        </div>

        <!-- Demands By Status Chart -->
        <div class="bg-white p-6 shadow rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-semibold text-gray-800">Radio Demands by Status</h4>
                <a href="{{ route('radio-demands.index') }}" class="text-sm text-primary hover:underline">View All</a>
            </div>
            <div class="h-64">
                <canvas id="demandsByStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white p-6 shadow rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-semibold text-gray-800">Recent Activities</h4>
            <a href="#" class="text-sm text-primary hover:underline">View All</a>
        </div>
        <div class="space-y-4">
            @foreach ($recentActivities as $activity)
            <div class="flex items-start p-3 hover:bg-gray-50 rounded transition">
                <div class="flex-shrink-0 h-10 w-10 rounded-full {{ str_replace('text', 'bg', $activity['color']) }} bg-opacity-10 flex items-center justify-center {{ $activity['color'] }} mr-4">
                    <i class="{{ $activity['icon'] }}"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-800">{{ $activity['title'] }}</p>
                    <p class="text-sm text-gray-600">{{ $activity['description'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $activity['time'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Users by Role Chart
        const usersByRoleCtx = document.getElementById('usersByRoleChart').getContext('2d');
        new Chart(usersByRoleCtx, {
            type: 'bar',
            data: {
                labels: @json($usersByRole->pluck('name')),
                datasets: [{
                    label: 'Users Count',
                    data: @json($usersByRole->pluck('users_count')),
                    backgroundColor: '#0a2164',
                    borderColor: '#081a52',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Radios by Status Chart
        const radiosByStatusCtx = document.getElementById('radiosByStatusChart').getContext('2d');
        new Chart(radiosByStatusCtx, {
            type: 'bar',
            data: {
                labels: @json($radiosByStatus->pluck('status')),
                datasets: [{
                    label: 'Radios Count',
                    data: @json($radiosByStatus->pluck('count')),
                    backgroundColor: '#3b82f6',
                    borderColor: '#2563eb',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Radio Demands by Status Chart
        const demandsByStatusCtx = document.getElementById('demandsByStatusChart').getContext('2d');
        new Chart(demandsByStatusCtx, {
            type: 'bar',
            data: {
                labels: @json($demandsByStatus->pluck('status')),
                datasets: [{
                    label: 'Demands Count',
                    data: @json($demandsByStatus->pluck('count')),
                    backgroundColor: '#f59e0b', // amber-500
                    borderColor: '#d97706', // amber-600
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>

@endsection
