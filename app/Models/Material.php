<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['emission_id', 'title', 'file_path'];

    public function emission()
    {
        return $this->belongsTo(Emission::class);
    }
}
