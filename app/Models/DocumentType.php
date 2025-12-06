<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'description', 'price', 'requires_payment', 'is_active', 'custom_fields'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'requires_payment' => 'boolean',
        'custom_fields' => 'array', // Automatically cast JSON to array
    ];
}