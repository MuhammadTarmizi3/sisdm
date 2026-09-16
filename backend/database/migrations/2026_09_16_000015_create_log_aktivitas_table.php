<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->integerIncrements('ID_LOG');
            $table->unsignedInteger('ID_USER');
            $table->string('AKTIVITAS', 255)->nullable();
            $table->string('ENTITY_TYPE', 50)->nullable();
            $table->integer('ENTITY_ID')->nullable();
            $table->timestamp('WAKTU')->useCurrent();

            $table->foreign('ID_USER')->references('USER_ID')->on('users')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};
