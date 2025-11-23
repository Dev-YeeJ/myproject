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
    // Add the new user_id column
    // It's nullable so existing residents don't cause an error
    // 'after' is optional but keeps the table clean
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->after('id');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            //
        });
    }
};
