<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_jabatan', function (Blueprint $table) {
            $table->integerIncrements('ID_RIWAYAT');
            $table->unsignedInteger('ID_PEGAWAI');
            $table->unsignedInteger('ID_JABATAN');
            $table->date('TMT_JABATAN')->nullable();
            $table->string('NOMER_SK', 50)->nullable();
            $table->date('TANGGAL_SK')->nullable();
            $table->string('FILE_SK', 255)->nullable();
            $table->string('SUMBER', 20)->default('manual_admin');
            $table->timestamp('CREATED_AT')->useCurrent();

            $table->foreign('ID_PEGAWAI')->references('ID_PEGAWAI')->on('pegawai')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ID_JABATAN')->references('ID_JABATAN')->on('jabatan')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_jabatan');
    }
};
