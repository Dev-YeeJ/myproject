<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This adds the missing 'household_name' column to the 'households' table.
        // We place it "after" the 'id' column for good table structure.
        Schema::table('households', function (Blueprint $table) {
            $table->string('household_name')->after('id')->comment('A descriptive name for the household (e.g., Dela Cruz Family)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This allows you to "undo" the migration if needed.
        Schema::table('households', function (Blueprint $table) {
            $table->dropColumn('household_name');
        });
    }
};