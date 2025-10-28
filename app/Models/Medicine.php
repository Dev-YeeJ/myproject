<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Make sure Carbon is imported

class Medicine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * !! THIS IS THE FIX !!
     * This array tells Laravel that it's safe to save
     * these fields from your form.
     */
    protected $fillable = [
        'item_name',
        'brand_name',
        'dosage',
        'quantity',
        'low_stock_threshold',
        'expiration_date',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'expiration_date' => 'date',
        'quantity' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    /**
     * Get the status of the medicine (Accessor).
     * This automatically calculates the 'status' for you
     * when you access $medicine->status in your blade file.
     */
    public function getStatusAttribute()
    {
        // Check for expiration first
        if ($this->expiration_date && Carbon::parse($this->expiration_date)->isPast()) {
            return 'Expired';
        }
        
        // Check for stock quantity
        if ($this->quantity == 0) {
            return 'Out of Stock';
        }

        // Check against the low stock threshold (use 10 as a fallback)
        $threshold = $this->low_stock_threshold ?? 10;
        if ($this->quantity < $threshold) {
            return 'Low Stock';
        }

        // Otherwise, it's in stock
        return 'In Stock';
    }
}