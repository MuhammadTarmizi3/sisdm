<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_kerja', function (Blueprint $table) {
            $table->integerIncrements('ID_UNIT');
            $table->string('NAMA_UNIT', 128)->unique();
            $table->char('TIPE_UNIT', 4)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_kerja');
    }
};
