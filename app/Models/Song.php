<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'note',
        'file',
        'status',
        'suggester_id',
        'radio_id',
    ];

    public function suggester() {
        return $this->belongsTo(User::class, 'suggester_id');
    }

    public function radio() {
        return $this->belongsTo(Radio::class);
    }
}
