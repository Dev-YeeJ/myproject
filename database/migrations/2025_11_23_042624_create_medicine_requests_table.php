<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medicine_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained()->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_requested');
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected, Claimed
            $table->text('remarks')->nullable(); // For rejection reason or pickup instructions
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medicine_requests');
    }
};