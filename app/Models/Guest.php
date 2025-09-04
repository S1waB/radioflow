<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'radio_id',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'description',
        'profile_photo',
    ];

    public function radio()
    {
        return $this->belongsTo(Radio::class);
    }
}
