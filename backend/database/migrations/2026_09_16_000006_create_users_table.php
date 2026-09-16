<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integerIncrements('USER_ID');
            $table->unsignedInteger('ID_PEGAWAI')->nullable()->unique();
            $table->unsignedInteger('ID_ROLE');
            $table->string('USERNAME', 50)->unique();
            $table->string('PASSWORD', 255);
            $table->boolean('IS_ACTIVE')->default(true);
            $table->timestamps();

            $table->foreign('ID_PEGAWAI')->references('ID_PEGAWAI')->on('pegawai')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('ID_ROLE')->references('ID_ROLE')->on('roles')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
