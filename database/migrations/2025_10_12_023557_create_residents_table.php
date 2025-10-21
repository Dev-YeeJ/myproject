<?php
// database/migrations/2024_01_01_000002_create_residents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->date('date_of_birth');
            $table->integer('age')->nullable();
            $table->enum('gender', ['Male', 'Female']);
            $table->enum('civil_status', ['Single', 'Married', 'Widowed', 'Separated', 'Divorced'])->default('Single');
            
            // Fixed: Use unsignedBigInteger for nullable foreign key with SET NULL
            $table->unsignedBigInteger('household_id')->nullable();
            
            $table->enum('household_status', ['Household Head', 'Member'])->default('Member');
            $table->string('address');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('occupation')->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->boolean('is_registered_voter')->default(false);
            $table->boolean('is_indigenous')->default(false);
            $table->boolean('is_pwd')->default(false);
            $table->boolean('is_senior_citizen')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add foreign key constraint separately
            $table->foreign('household_id')
                  ->references('id')
                  ->on('households')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};