<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('_settings', function (Blueprint $table) {
            $table->string('SET_ID',70);
            $table->string('SET_VALUE',300);
            $table->longText('SET_VALUE_TEXT')->nullable();
            $table->text('SET_INFO')->default(7);

            // Timestamps for system fields
            $table->enum('SET_DISPLAY_FORM', ['Y','N'])->default('N');
            $table->enum('SET_VALUE_DISPLAY_FORM', ['Y','N'])->default('N');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_settings');
    }
};
