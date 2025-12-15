<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('health_programs', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->string('location');
        $table->dateTime('schedule_date');
        $table->string('organizer')->nullable(); // e.g., Dr. Cruz or Committee on Health
        $table->string('status')->default('Upcoming'); // Options: Upcoming, Completed, Cancelled
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_programs');
    }
};
