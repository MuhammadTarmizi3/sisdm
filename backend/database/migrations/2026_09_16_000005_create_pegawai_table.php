<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->integerIncrements('ID_PEGAWAI');
            $table->unsignedInteger('ID_UNIT')->nullable();
            $table->unsignedInteger('ID_JABATAN')->nullable();
            $table->unsignedInteger('ID_PANGKAT')->nullable();
            $table->char('NIP_NRP', 18)->unique();
            $table->string('NAMA_PEGAWAI', 128);
            $table->string('TEMPAT_LAHIR', 50)->nullable();
            $table->date('TGL_LAHIR')->nullable();
            $table->string('JENIS_KELAMIN', 20);
            $table->string('NOMER_HP', 13)->nullable();
            $table->string('STATUS_KEPEGAWAIAN', 20)->nullable();
            $table->date('TMT_CPNS')->nullable();
            $table->date('TMT_PNS')->nullable();
            $table->string('PENDIDIKAN_TERAKHIR', 50)->nullable();
            $table->string('FOTO', 255)->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at');

            $table->foreign('ID_UNIT')->references('ID_UNIT')->on('unit_kerja')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('ID_JABATAN')->references('ID_JABATAN')->on('jabatan')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('ID_PANGKAT')->references('ID_PANGKAT')->on('pangkat_golongan')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
