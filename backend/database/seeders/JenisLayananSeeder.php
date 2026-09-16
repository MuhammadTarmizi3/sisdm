<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisLayananSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jenis_layanan')->insert([
            [
                'ID_LAYANAN' => 1,
                'KODE_LAYANAN' => 'KGB',
                'NAMA_LAYANAN' => 'Kenaikan Gaji Berkala',
                'KATEGORI' => 'Kepegawaian',
                'STATUS_LAYANAN' => 1,
            ]
        ]);
    }
}
