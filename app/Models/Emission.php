<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Emission extends Model
{
    use HasFactory;

    protected $fillable = [
        'radio_id',
        'animateur_id',
        'name',
        'type',
        'duration_minutes',
        'description',
        'logo_path',
    ];

    // Relation to the Radio
    public function radio()
    {
        return $this->belongsTo(Radio::class);
    }
    public function seasons()
    {
        return $this->hasMany(Season::class);
    }
    // Relation to the User/Animateur
    public function animateur()
    {
        return $this->belongsTo(User::class, 'animateur_id');
    }
    public function members()
    {
        return $this->belongsToMany(User::class, 'emission_user', 'emission_id', 'user_id')
            ->withTimestamps();
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'emission_task');
    }
  

    public function episodes()
    {
        return $this->hasManyThrough(Episode::class, Season::class);
    }
}
