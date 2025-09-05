<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'emission_id',
        'number', // e.g., Season 1, 2...
        'description',
    ];

    public function radio()
    {
        return $this->belongsTo(Radio::class);
    }
    public function emission()
    {
        return $this->belongsTo(Emission::class);
    }
    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }
}
