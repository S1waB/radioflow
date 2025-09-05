<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'season_id',
        'name',
        'number', // e.g., Episode 1, 2...
        'duration_minutes',
        'description',
        'animateur_id',
        'episode_docs',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function emission()
    {
        return $this->season->emission(); // optional helper
    }

    public function animateur()
    {
        return $this->belongsTo(User::class, 'animateur_id');
    }
}
