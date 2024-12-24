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
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('SESSION_TOKEN');
            $table->foreignId('U_ID')->nullable()->index();
            $table->dateTime('SESSION_CREATED_AT');
            $table->dateTime('SESSION_EXPIRED_AT');
            $table->string('SESSION_IP', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
