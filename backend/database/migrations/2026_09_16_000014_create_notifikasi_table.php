<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->integerIncrements('ID_NOTIFIKASI');
            $table->unsignedInteger('USER_ID');
            $table->string('JUDUL', 150)->nullable();
            $table->text('PESAN')->nullable();
            $table->string('JENIS', 32)->nullable();
            $table->boolean('IS_READ')->default(false);
            $table->timestamp('CREATED_AT')->useCurrent();

            $table->foreign('USER_ID')->references('USER_ID')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->index(['USER_ID', 'IS_READ']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
