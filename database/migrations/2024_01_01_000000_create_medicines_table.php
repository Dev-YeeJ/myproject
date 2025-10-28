<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Use a named class that matches the filename
class CreateMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('brand_name')->nullable();
            $table->string('dosage');
            $table->integer('quantity');
            $table->integer('low_stock_threshold')->default(10);
            $table->date('expiration_date');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};

