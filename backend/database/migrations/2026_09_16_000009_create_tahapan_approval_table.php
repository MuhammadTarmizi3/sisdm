<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahapan_approval', function (Blueprint $table) {
            $table->integerIncrements('ID_APPROVAL');
            $table->unsignedInteger('ID_LAYANAN')->nullable();
            $table->integer('URUTAN')->nullable();
            $table->string('NAMA_TAHAP', 255)->nullable();
            $table->unsignedInteger('ID_ROLE_BERWENANG');

            $table->foreign('ID_LAYANAN')->references('ID_LAYANAN')->on('jenis_layanan')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('ID_ROLE_BERWENANG')->references('ID_ROLE')->on('roles')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahapan_approval');
    }
};
