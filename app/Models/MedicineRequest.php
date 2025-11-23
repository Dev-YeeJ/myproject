<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'medicine_id',
        'quantity_requested',
        'status',
        'remarks',
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}