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
        'emission_docs',
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
}
