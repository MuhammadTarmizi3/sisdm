<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->integerIncrements('ID_PENGAJUAN');
            $table->unsignedInteger('ID_PEGAWAI');
            $table->unsignedInteger('ID_LAYANAN')->nullable();
            $table->date('TANGGAL_PENGAJUAN')->nullable();
            $table->string('STATUS_PENGAJUAN', 32)->default('Diajukan');
            $table->text('CATATAN_VERIFIKATOR')->nullable();
            $table->string('NOMER_SK', 50)->nullable()->unique();
            $table->date('TANGGAL_SK')->nullable();
            $table->timestamps();

            $table->foreign('ID_PEGAWAI')->references('ID_PEGAWAI')->on('pegawai')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('ID_LAYANAN')->references('ID_LAYANAN')->on('jenis_layanan')->onDelete('restrict')->onUpdate('cascade');

            $table->index(['STATUS_PENGAJUAN', 'ID_LAYANAN']);
            $table->index(['ID_PEGAWAI', 'STATUS_PENGAJUAN']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
