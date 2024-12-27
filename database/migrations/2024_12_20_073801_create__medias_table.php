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
        Schema::create('_medias', function (Blueprint $table) {
            $table->uuid('MEDIA_ID')->primary();
            $table->string('MEDIA_MIME_TYPE', 50);
            $table->enum('MEDIA_CONTENT_TYPE', ['Base64', 'File']);
            $table->longText('MEDIA_CONTENT_VALUE');
            $table->timestamp('SYS_CREATE_AT')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('SYS_CREATED_USER', 100)->default('-');
            $table->timestamp('SYS_UPDATE_AT')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->string('SYS_UPDATED_USER', 100)->default('-');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_medias');
    }
};
