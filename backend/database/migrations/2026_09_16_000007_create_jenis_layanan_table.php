<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_layanan', function (Blueprint $table) {
            $table->integerIncrements('ID_LAYANAN');
            $table->string('KODE_LAYANAN', 20)->unique();
            $table->string('NAMA_LAYANAN', 100);
            $table->string('KATEGORI', 50)->nullable();
            $table->boolean('STATUS_LAYANAN')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_layanan');
    }
};
