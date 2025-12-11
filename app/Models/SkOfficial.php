<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkOfficial extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id', 
        'position', 
        'committee', 
        'term_start', 
        'term_end', 
        'is_active'
    ];

    protected $casts = [
        'term_start' => 'datetime',
        'term_end' => 'datetime',
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }
}