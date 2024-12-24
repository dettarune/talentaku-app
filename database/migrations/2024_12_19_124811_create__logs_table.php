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
        Schema::create('_logs', function (Blueprint $table) {
            $table->bigIncrements('L_ID');
            $table->string('L_SESSION');
            $table->dateTime('L_DATE');
            $table->string('L_CONTEXT');
            $table->bigInteger('L_SUBJECT_U_ID')->unsigned()->nullable();
            $table->text('L_INFO')->nullable()->default('-');
            $table->string('L_REMOTE_ADDRESS')->nullable()->default('-');

            $table->timestamp('SYS_CREATE_TIME')->default(DB::raw('CURRENT_TIMESTAMP'));

            // Foreign key constraint
            $table->foreign('L_SUBJECT_U_ID')->references('U_ID')->on('_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_logs');
    }
};
