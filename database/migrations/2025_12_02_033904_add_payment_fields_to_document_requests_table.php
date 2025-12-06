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
    Schema::table('document_requests', function (Blueprint $table) {
        $table->string('payment_method')->nullable()->after('price'); // 'Cash', 'Online'
        $table->string('payment_reference_number')->nullable()->after('payment_method');
        $table->string('payment_proof')->nullable()->after('payment_reference_number'); // Path to image
    });
}

public function down()
{
    Schema::table('document_requests', function (Blueprint $table) {
        $table->dropColumn(['payment_method', 'payment_reference_number', 'payment_proof']);
    });
}
};
