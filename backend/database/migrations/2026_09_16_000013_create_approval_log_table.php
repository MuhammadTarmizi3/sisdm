<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_log', function (Blueprint $table) {
            $table->integerIncrements('ID_APPROVAL_LOG');
            $table->unsignedInteger('ID_PENGAJUAN');
            $table->unsignedInteger('ID_TAHAPAN');
            $table->unsignedInteger('ID_USER');
            $table->string('KEPUTUSAN', 32);
            $table->text('CATATAN')->nullable();
            $table->timestamp('WAKTU')->useCurrent();

            $table->foreign('ID_PENGAJUAN')->references('ID_PENGAJUAN')->on('pengajuan')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('ID_TAHAPAN')->references('ID_APPROVAL')->on('tahapan_approval')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('ID_USER')->references('USER_ID')->on('users')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_log');
    }
};
