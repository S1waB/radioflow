<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Radio extends Model
{
    // Table name (optional if it matches plural of class name)
    protected $table = 'radios';

    protected $fillable = [
        'name',
        'description',
        'phone_number',
        'address',
        'Country',
        'status',
        'manager_id',
        'logo_path',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_DESACTIVE = 'desactive';

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isDesactive(): bool
    {
        return $this->status === self::STATUS_DESACTIVE;
    }

    // Radio has one manager (User)
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    //  Radio has many employees (users)
    public function team(): HasMany
    {
        return $this->hasMany(User::class, 'radio_id')->where('id', '<>', $this->manager_id);
    }
    // Use this to get logo_path by $radio->logo
    public function getLogoAttribute()
    {
        return $this->logo_path;
    }

    // Add this to get full URL by $radio->logo_url
    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }
}
