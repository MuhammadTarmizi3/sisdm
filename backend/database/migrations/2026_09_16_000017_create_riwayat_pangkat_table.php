<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_pangkat', function (Blueprint $table) {
            $table->integerIncrements('ID_RIWAYAT_PANGKAT');
            $table->unsignedInteger('ID_PEGAWAI');
            $table->unsignedInteger('ID_PANGKAT');
            $table->date('TMT_PANGKAT')->nullable();
            $table->string('NOMER_SK', 50)->nullable();
            $table->date('TANGGAL_SK')->nullable();
            $table->string('FILE_SK', 255)->nullable();
            $table->string('SUMBER', 20)->default('manual_admin');
            $table->timestamp('CREATED_AT')->useCurrent();

            $table->foreign('ID_PEGAWAI')->references('ID_PEGAWAI')->on('pegawai')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ID_PANGKAT')->references('ID_PANGKAT')->on('pangkat_golongan')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_pangkat');
    }
};
