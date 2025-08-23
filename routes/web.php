<?php

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\RadioController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RadioDemandController;
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

    // User Management (Admin Only)

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
    });

    // Radio Demand Management
    Route::prefix('radiradio-demands')->name('radio-demands.')->group(function () {
        Route::get('/{demand}', [RadioDemandController::class, 'show'])->name('show');
        Route::get('/', [RadioDemandController::class, 'index'])->name('index');
        Route::put('/{demand}/status', [RadioDemandController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{demand}', [RadioDemandController::class, 'destroy'])->name('destroy');
    });

    // Role Management (Admin Only)
    Route::middleware(['can:admin'])->group(function () {
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
});
