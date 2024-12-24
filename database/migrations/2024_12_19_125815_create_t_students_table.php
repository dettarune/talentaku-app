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
        Schema::create('t_students', function (Blueprint $table) {
            $table->bigIncrements('S_ID');
            $table->string('STUDENT_NAME',80);
            $table->string('STUDENT_ROLL_NUMBER',80)->nullable()->default(0);
            $table->bigInteger('STUDENT_PARENT_U_ID')->unsigned()->nullable();
            $table->enum('STUDENT_SEX', ['male', 'female', 'Not Specified'])->nullable()->default('Not Specified');
            $table->bigInteger('CLSRM_ID')->unsigned()->nullable();
            $table->string('STUDENT_IMAGE_PROFILE', 100)->nullable()->default('');

            // Timestamps for system fields
            $table->timestamp('SYS_CREATE_AT')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('SYS_CREATE_USER')->nullable()->default('-');
            $table->timestamp('SYS_UPDATE_AT')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('SYS_UPDATE_USER')->nullable()->default('-');

            // Foreign key constraints
            $table->foreign('STUDENT_PARENT_U_ID')->references('U_ID')->on('_users')->onDelete('set null');
            $table->foreign('CLSRM_ID')->references('CLSRM_ID')->on('t_classrooms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_students');
    }
};
