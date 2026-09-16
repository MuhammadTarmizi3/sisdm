<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_kgb', function (Blueprint $table) {
            $table->unsignedInteger('ID_PENGAJUAN')->primary();
            $table->integer('GAJI_POKOK_LAMA')->nullable();
            $table->integer('GAJI_POKOK_BARU')->nullable();
            $table->date('TMT_KGB_BERIKUTNYA')->nullable();

            $table->foreign('ID_PENGAJUAN')->references('ID_PENGAJUAN')->on('pengajuan')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_kgb');
    }
};
