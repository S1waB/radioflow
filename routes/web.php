<?php

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmissionController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\RadioController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RadioDemandController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Models\Emission;
use App\Models\Radio;
use App\Models\Role;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as ControllersAuthenticatedSessionController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->name('newsletter.subscribe');

// Guest Routes (Unauthenticated)
Route::middleware('guest')->group(function () {
    // Registration Routes
    Route::get('/register', function () {
        return view('auth.register', [
            'roles' => Role::whereNotIn('name', ['admin', 'directeur'])
                ->orderBy('hierarchy_level')
                ->get(['id', 'name']),
            'radios' => Radio::where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name'])
        ]);
    })->name('register');

    Route::post('/register', [RegistrationController::class, 'store'])
        ->name('register.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified'
    ])
    ->name('logout');

// Public route for submitting demands
Route::get('/radio-demand', [RadioDemandController::class, 'create'])->name('radio-demand.create');
Route::post('/radio-demand', [RadioDemandController::class, 'store'])->name('radio-demand.store');

// Authenticated Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::put('/{user}/change-status', [UserController::class, 'changeStatus'])->name('change-status');
        Route::put('/{user}/update-role', [UserController::class, 'updateRole'])->name('update-role');
    });

    // Tasks Management
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('{task}', [TaskController::class, 'show'])->name('show');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::post('/{task}/update-status', [TaskController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        Route::delete('/{task}/remove-file', [TaskController::class, 'removeFile'])->name('remove-file');
        Route::post('/{task}/comment', [TaskController::class, 'addComment'])->name('comment');
    });

    // Radio Management
    Route::prefix('radios')->name('radios.')->group(function () {
        Route::get('/', [RadioController::class, 'index'])->name('index');
        Route::get('/create', [RadioController::class, 'create'])->name('create');
        Route::post('/', [RadioController::class, 'store'])->name('store');
        Route::get('/{radio}/edit', [RadioController::class, 'edit'])->name('edit');
        Route::put('/{radio}', [RadioController::class, 'update'])->name('update');
        Route::delete('/{radio}', [RadioController::class, 'destroy'])->name('destroy');
        Route::put('/{radio}/change-status', [RadioController::class, 'changeStatus'])->name('change-status');

        Route::get('/{radio}', [RadioController::class, 'show'])->name('show');
        Route::post('/{radio}/add-member', [RadioController::class, 'addMember'])->name('add-member');
        Route::post('/{radio}/remove-member', [RadioController::class, 'removeMember'])->name('remove-member');


        // Nested songs
        Route::get('/{radio}/songs', [SongController::class, 'index'])->name('songs.index');
        Route::post('/{radio}/songs', [SongController::class, 'store'])->name('songs.store');
        Route::put('/{radio}/songs/{song}', [SongController::class, 'update'])->name('songs.update');
        Route::delete('/{radio}/songs/{song}', [SongController::class, 'destroy'])->name('songs.destroy');
        Route::get('/{radio}/songs/{song}', [SongController::class, 'show'])->name('songs.show');

        // Nested guests route
        Route::get('/{radio}/guests', [GuestController::class, 'index'])->name('guests.index');  // this is radios.guests.index
        Route::post('/{radio}/guests', [GuestController::class, 'store'])->name('guests.store');
        Route::put('/{radio}/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
        Route::delete('/{radio}/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');

        // Emissions CRUD
         Route::resource('{radio}/emissions', EmissionController::class);

        // Assign members to emission
        Route::post('emissions/{emission}/members', [EmissionController::class, 'addMember'])->name('emissions.members.add');
        Route::delete('emissions/{emission}/members/{user}', [EmissionController::class, 'removeMember'])->name('emissions.members.remove');

        // Create tasks for emission members
        Route::resource('emissions.tasks', TaskController::class)->shallow();

        // Seasons & Episodes nested under emission
        Route::prefix('emissions/{emission}')->group(function () {

            // Seasons CRUD
            Route::resource('seasons', SeasonController::class);

            // Episodes nested under season
            Route::prefix('seasons/{season}')->group(function () {
                Route::resource('episodes', EpisodeController::class);

                // Guests, Songs, Materials handled inside EpisodeController
                Route::post('episodes/{episode}/guests', [EpisodeController::class, 'addGuest'])->name('episodes.guests.add');
                Route::delete('episodes/{episode}/guests/{guest}', [EpisodeController::class, 'removeGuest'])->name('episodes.guests.remove');

                Route::post('episodes/{episode}/songs', [EpisodeController::class, 'addSong'])->name('episodes.songs.add');
                Route::delete('episodes/{episode}/songs/{song}', [EpisodeController::class, 'removeSong'])->name('episodes.songs.remove');

                Route::post('episodes/{episode}/materials', [EpisodeController::class, 'addMaterial'])->name('episodes.materials.add');
                Route::delete('episodes/{episode}/materials/{material}', [EpisodeController::class, 'removeMaterial'])->name('episodes.materials.remove');
            });
        });

    });


    // Team Management
    Route::prefix('teams')->name('teams.')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::get('/create', [TeamController::class, 'create'])->name('create');
        Route::post('/', [TeamController::class, 'store'])->name('store');
        Route::get('/{team}', [TeamController::class, 'show'])->name('show');
        Route::get('/{team}/edit', [TeamController::class, 'edit'])->name('edit');
        Route::put('/{team}', [TeamController::class, 'update'])->name('update');
        Route::delete('/{team}', [TeamController::class, 'destroy'])->name('destroy');
        Route::get('/{team}/members', [TeamController::class, 'getMembers'])->name('teams.members');
        Route::post('/{team}/add-member', [TeamController::class, 'addMember'])->name('add-member');
        Route::post('/{team}/remove-member', [TeamController::class, 'removeMember'])->name('remove-member');
    });

    // Radio Demand Management
    Route::prefix('radio-demands')->name('radio-demands.')->group(function () {
        Route::get('/', [RadioDemandController::class, 'index'])->name('index');
        Route::get('/{demand}', [RadioDemandController::class, 'show'])->name('show');
        Route::put('/{demand}/status', [RadioDemandController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{demand}', [RadioDemandController::class, 'destroy'])->name('destroy');
    });

    // Role Management (Admin Only)
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    });
});
