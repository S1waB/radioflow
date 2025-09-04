<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'phone_number',
        'address',
        'bio',
        'role_id',
        'radio_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_path',
    ];
    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    /**
     * Get the tasks for the user.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }
    /**
     * Relationship: User belongs to a role.
     */

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }


    /**
     * Relationship: User belongs to a radio (nullable).
     */
    public function radio()
    {
        return $this->belongsTo(Radio::class);
    }
    public function isActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isDesactive($query)
    {
        return $query->where('status', 'desactive');
    }
    /**
     * Check if the user is a global admin (no radio).
     */
    public function isAdmin()
    {
        return optional($this->role)->name === 'admin';
    }
    public function managedRadio()
    {
        return $this->hasOne(Radio::class, 'manager_id');
    }
    /**
     * Accessor for full profile photo URL.
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : null; // Return null if no image, let Blade handle the fallback
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
