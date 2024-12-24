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
        Schema::create('t_student_report_activities', function (Blueprint $table) {
                $table->bigIncrements('SRA_ID')->autoIncrement();
                $table->unsignedBigInteger('SR_ID');
                $table->string('ACTIVITY_TYPE',80)->default('-');
                $table->string('ACTIVITY_NAME', 100)->default('-');
                $table->enum('STATUS', ['MUNCUL', 'KURANG', 'BELUM MUNCUL'])->default('BELUM MUNCUL');
                $table->timestamp('SYS_CREATE_AT')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->string('SYS_CREATE_USER', 100)->default('-');
                $table->timestamp('SYS_UPDATE_AT')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->string('SYS_UPDATE_USER', 100)->default('-');

                // Foreign key relationship
                $table->foreign('SR_ID')->references('SR_ID')->on('t_student_reports')->onDelete('cascade');

                // Index for faster query
                $table->index(['SR_ID', 'ACTIVITY_TYPE']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_student_report_activities');
    }
};
