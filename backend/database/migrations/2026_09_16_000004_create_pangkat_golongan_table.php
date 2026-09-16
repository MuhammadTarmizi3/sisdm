<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pangkat_golongan', function (Blueprint $table) {
            $table->integerIncrements('ID_PANGKAT');
            $table->string('NAMA_PANGKAT', 128);
            $table->string('GOLONGAN_RUANG', 128)->nullable();
            $table->string('URUTAN_TINGKAT', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pangkat_golongan');
    }
};
