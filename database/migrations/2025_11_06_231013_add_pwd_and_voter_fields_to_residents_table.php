<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_pwd_and_voter_fields_to_residents_table.php

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
            // Field for Registered Voter
            $table->string('precinct_number')->nullable()->after('is_registered_voter')->comment('Voter precinct number, required if is_registered_voter is true');
            
            // Fields for PWD
            $table->string('pwd_id_number')->nullable()->after('is_pwd')->comment('PWD ID number, required if is_pwd is true');
            $table->string('disability_type')->nullable()->after('pwd_id_number')->comment('Type of disability, required if is_pwd is true');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn(['precinct_number', 'pwd_id_number', 'disability_type']);
        });
    }
};