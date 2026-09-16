<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersyaratanMasterSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('persyaratan_master')->insert([
            [
                'ID_LAYANAN' => 1,
                'NAMA_PERSYARATAN' => 'Fotokopi SK CPNS',
                'WAJIB' => 1,
                'URUTAN' => 1,
                'IS_ACTIVE' => 1,
            ],
            [
                'ID_LAYANAN' => 1,
                'NAMA_PERSYARATAN' => 'Fotokopi SK PNS',
                'WAJIB' => 1,
                'URUTAN' => 2,
                'IS_ACTIVE' => 1,
            ],
            [
                'ID_LAYANAN' => 1,
                'NAMA_PERSYARATAN' => 'Fotokopi SK Pangkat Terakhir',
                'WAJIB' => 1,
                'URUTAN' => 3,
                'IS_ACTIVE' => 1,
            ],
            [
                'ID_LAYANAN' => 1,
                'NAMA_PERSYARATAN' => 'Fotokopi SK KGB Terakhir',
                'WAJIB' => 1,
                'URUTAN' => 4,
                'IS_ACTIVE' => 1,
            ],
            [
                'ID_LAYANAN' => 1,
                'NAMA_PERSYARATAN' => 'Fotokopi DP3/SKP 2 Tahun Terakhir',
                'WAJIB' => 1,
                'URUTAN' => 5,
                'IS_ACTIVE' => 1,
            ],
            [
                'ID_LAYANAN' => 1,
                'NAMA_PERSYARATAN' => 'Fotokopi Surat Pernyataan Tidak Sedang Menjalani Hukuman Disiplin',
                'WAJIB' => 1,
                'URUTAN' => 6,
                'IS_ACTIVE' => 1,
            ],
            [
                'ID_LAYANAN' => 1,
                'NAMA_PERSYARATAN' => 'Dokumen Pendukung Lainnya',
                'WAJIB' => 0,
                'URUTAN' => 7,
                'IS_ACTIVE' => 1,
            ],
        ]);
    }
}
