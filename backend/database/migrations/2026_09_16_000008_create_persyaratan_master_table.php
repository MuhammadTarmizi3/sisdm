<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persyaratan_master', function (Blueprint $table) {
            $table->integerIncrements('ID_PERSYARATAN');
            $table->unsignedInteger('ID_LAYANAN');
            $table->string('NAMA_PERSYARATAN', 150);
            $table->boolean('WAJIB')->default(true);
            $table->integer('URUTAN')->nullable();
            $table->boolean('IS_ACTIVE')->default(true);

            $table->foreign('ID_LAYANAN')->references('ID_LAYANAN')->on('jenis_layanan')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persyaratan_master');
    }
};
