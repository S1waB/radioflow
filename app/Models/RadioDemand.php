<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadioDemand extends Model
{
    use HasFactory;

    // Table name (optional if it matches the plural of the model name)
    protected $table = 'radio_demands';

    // Fillable fields for mass assignment
    protected $fillable = [
        'radio_name',
        'description',
        'founding_date',
        'manager_name',
        'manager_email',
        'manager_phone',
        'logo_path',
        'team_members',
        'status',
    ];

    // Casts for specific columns
    protected $casts = [
        'founding_date' => 'date',
        'team_members' => 'array', // Laravel will automatically convert JSON to array
    ];
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROCESS = 'in_process';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public function isProcessed()
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_REJECTED]);
    }
}
