<?php
// app/Models/Team.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'radio_id'
    ];
    /**
     * Get the radio that owns the team.
     */
    public function radio()
    {
        return $this->belongsTo(Radio::class);
    }
    /**
     * The users that belong to the team.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the tasks for the team.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
