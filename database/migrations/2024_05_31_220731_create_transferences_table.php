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
            $table->ulid()->primary();
            $table->timestamps();
            $table->ulid('payer');
            $table->foreign('payer_user_id')->references('id')->on('users');
            $table->ulid('payee');
            $table->foreign('payee_user_id')->references('id')->on('users');
            $table->unsignedBigInteger('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transferences', function (Blueprint $table) {
            $table->dropForeign(['payer_user_id']);
            $table->dropForeign(['payee_user_id']);
        });
        Schema::dropIfExists('transferences');
    }
};
