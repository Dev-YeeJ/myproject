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
        Schema::table('residents', function (Blueprint $table) {
            // Only add 'is_active' since 'last_name' already exists
            if (!Schema::hasColumn('residents', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }

            // Optional: only add 'last_name' if it doesn't exist
            if (!Schema::hasColumn('residents', 'last_name')) {
                $table->string('last_name')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            if (Schema::hasColumn('residents', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('residents', 'last_name')) {
                $table->dropColumn('last_name');
            }
        });
    }
};
