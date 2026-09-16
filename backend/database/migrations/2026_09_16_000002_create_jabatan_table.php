<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatan', function (Blueprint $table) {
            $table->integerIncrements('ID_JABATAN');
            $table->string('NAMA_JABATAN', 128)->unique();
            $table->string('JENIS_JABATAN', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};
