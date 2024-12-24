<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_users', function (Blueprint $table) {
            $table->bigIncrements('U_ID')->autoIncrement();
            $table->string('U_NAME', 80);
            $table->string('U_PASSWORD_HASH', 80);
            $table->unsignedSmallInteger('UR_ID')->unsigned(); // _user_roles table
            $table->enum('U_SEX', ['Male', 'Female', 'Not Specified'])->nullable()->default('Not Specified');
            $table->string('U_LOGIN_TOKEN', 64)->nullable()->default('-');
            $table->timestamp('U_LOGIN_TIME')->nullable();
            $table->timestamp('U_LOGIN_EXPIRED_TIME')->nullable();
            $table->timestamp('U_LOGOUT_TIME')->nullable();
            $table->timestamp('SYS_CREATE_TIME')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('SYS_CREATE_USER',80)->default('-');
            $table->timestamp('SYS_UPDATE_TIME')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('SYS_UPDATE_USER',80)->nullable()->default('-');

            // Indeks untuk kolom yang sering diakses
            $table->index('U_NAME');
            $table->index('U_LOGIN_TOKEN');
            $table->index('UR_ID');

            $table->foreign('UR_ID')->references('UR_ID')->on('_user_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_users');
    }
};
