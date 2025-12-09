<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'amount', 
        'type', 
        'category', 
        'status', 
        'requested_by', 
        'transaction_date',
        'project_id' // <--- Added this
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}