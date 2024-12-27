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
        Schema::create('t_student_reports', function (Blueprint $table) {
            $table->bigIncrements('SR_ID');
            $table->bigInteger('S_ID')->unsigned(); // students
            $table->bigInteger('U_ID')->unsigned()->nullable(); // role teache
            $table->text('SR_TITLE');
            $table->text('SR_CONTENT')->nullable();
            $table->timestamp('SR_DATE')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('SR_IS_READ', ['Y', 'N'])->default('N');

            // Timestamps for system fields
            $table->timestamp('SYS_CREATE_AT')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('SYS_CREATE_USER')->nullable()->default('-');
            $table->timestamp('SYS_UPDATE_AT')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('SYS_UPDATE_USER')->nullable()->default('-');

            // Foreign key constraints
            $table->foreign('S_ID')->references('S_ID')->on('t_students')->onDelete('cascade');
            $table->foreign('U_ID')->references('U_ID')->on('_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_student_reports');
    }
};
