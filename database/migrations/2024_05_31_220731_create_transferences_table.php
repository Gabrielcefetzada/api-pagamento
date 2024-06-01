<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transferences', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->timestamps();
            $table->unsignedBigInteger('payer_id');
            $table->foreign('payer_id')->references('id')->on('users');
            $table->unsignedBigInteger('payee_id');
            $table->foreign('payee_id')->references('id')->on('users');
            $table->unsignedBigInteger('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transferences', function (Blueprint $table) {
            $table->dropForeign(['payer_id']);
            $table->dropForeign(['payee_id']);
        });
        Schema::dropIfExists('transferences');
    }
};
