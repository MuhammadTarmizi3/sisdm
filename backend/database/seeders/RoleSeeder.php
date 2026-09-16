<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'ID_ROLE' => 1,
                'KODE_ROLE' => 'pegawai',
                'NAMA_ROLE' => 'Pegawai',
            ],
            [
                'ID_ROLE' => 2,
                'KODE_ROLE' => 'admin_kepegawaian',
                'NAMA_ROLE' => 'Admin Kepegawaian',
            ],
            [
                'ID_ROLE' => 3,
                'KODE_ROLE' => 'verifikator',
                'NAMA_ROLE' => 'Verifikator',
            ]
        ]);
    }
}
