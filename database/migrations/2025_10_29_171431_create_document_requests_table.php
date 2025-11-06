<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->nullable()->constrained('residents')->onDelete('set null');
            $table->string('tracking_number')->unique();
            $table->string('document_type');
            $table->string('purpose');
            $table->decimal('price', 8, 2)->default(0);
            $table->string('priority')->default('Normal');
            $table->string('payment_status')->default('Unpaid'); // e.g., Unpaid, Paid, Waived
            $table->string('status')->default('Pending'); // e.g., Pending, Processing, Ready, Completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};