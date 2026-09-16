<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $idPegawai = DB::table('pegawai')->insertGetId([
            'NIP_NRP' => '000000000000000000',
            'NAMA_PEGAWAI' => 'Administrator',
            'JENIS_KELAMIN' => 'L',
        ]);

        DB::table('users')->insert([
            'ID_ROLE' => 2,
            'USERNAME' => 'admin',
            'PASSWORD' => Hash::make('password123'),
            'IS_ACTIVE' => 1,
            'ID_PEGAWAI' => $idPegawai,
        ]);
    }
}
