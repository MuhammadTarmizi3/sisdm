<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TahapanApprovalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tahapan_approval')->insert([
            [
                'ID_LAYANAN' => 1,
                'URUTAN' => 1,
                'NAMA_TAHAP' => 'Verifikasi',
                'ID_ROLE_BERWENANG' => 3,
            ]
        ]);
    }
}
