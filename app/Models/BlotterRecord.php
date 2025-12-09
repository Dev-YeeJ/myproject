<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlotterRecord extends Model
{
    use HasFactory;

    protected $fillable = [
    'case_number', 'resident_id', 'date_reported', 'incident_type', 
    'complainant', 'respondent', 'location', 'priority', 
    'narrative', 'status', 'actions_taken'
];

public function resident()
{
    return $this->belongsTo(Resident::class);
}

    protected $casts = [
        'date_reported' => 'datetime',
    ];

    // Helper to generate Case Number
    public static function generateCaseNumber()
    {
        $year = date('Y');
        $lastRecord = self::whereYear('created_at', $year)->latest()->first();
        
        if (!$lastRecord) {
            return 'BLT-' . $year . '-001';
        }

        $lastNumber = intval(substr($lastRecord->case_number, -3));
        return 'BLT-' . $year . '-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }
}