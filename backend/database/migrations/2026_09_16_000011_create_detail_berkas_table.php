<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_berkas', function (Blueprint $table) {
            $table->integerIncrements('ID_DETAIL_BERKAS');
            $table->unsignedInteger('ID_PENGAJUAN');
            $table->unsignedInteger('ID_PERSYARATAN');
            $table->string('FILE_PATH', 255)->nullable();
            $table->string('STATUS_VERIFIKASI', 32)->default('belum diperiksa');
            $table->string('CATATAN', 255)->nullable();
            $table->timestamps();

            $table->foreign('ID_PENGAJUAN')->references('ID_PENGAJUAN')->on('pengajuan')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ID_PERSYARATAN')->references('ID_PERSYARATAN')->on('persyaratan_master')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_berkas');
    }
};
