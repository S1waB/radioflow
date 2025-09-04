<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'team_id',
        'owner_id',
        'assigned_to',
        'radio_id',
        'status',
        'deadline',
        'task_docs'
    ];

    protected $casts = [
        'task_docs' => 'array', // <-- automatically cast JSON to array
    ];
    /**
     * Get the team that owns the task.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function assigned()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    // Status logic based on deadline
    public function updateStatusBasedOnDeadline()
    {
        if ($this->status !== 'done' && $this->deadline) {
            if (now()->gt($this->deadline)) {
                $this->status = now()->diffInMinutes($this->deadline) > 0 ? 'late' : 'expired';
                $this->save();
            }
        }
    }
}
