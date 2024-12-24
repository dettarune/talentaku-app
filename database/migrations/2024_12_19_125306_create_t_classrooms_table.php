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
        Schema::create('t_classrooms', function (Blueprint $table) {
            $table->bigIncrements('CLSRM_ID')->autoIncrement();
            $table->string('CLSRM_NAME');
            $table->enum('CLSRM_TYPE', ['KB', 'SD'])->default('SD');
            $table->string('CLSRM_GRADE')->nullable()->default('-');
            $table->text('CLSRM_DESCRIPTION')->nullable()->default('-');

            // Timestamps for system fields
            $table->timestamp('SYS_CREATE_TIME')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('SYS_CREATE_USER')->nullable()->default('-');
            $table->timestamp('SYS_UPDATE_TIME')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('SYS_UPDATE_USER')->nullable()->default('-');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_classrooms');
    }
};
