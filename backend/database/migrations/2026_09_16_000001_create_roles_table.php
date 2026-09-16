<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->integerIncrements('ID_ROLE');
            $table->string('KODE_ROLE', 30)->unique();
            $table->string('NAMA_ROLE', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
