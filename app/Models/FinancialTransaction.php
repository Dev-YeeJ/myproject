<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'amount', 'type', 'category', 
        'status', 'requested_by', 'transaction_date'
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];
}