<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $fillable = ['radio_id', 'name', 'description', 'hierarchy_level'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function radio()
    {
        return $this->belongsTo(Radio::class);
    }
}
