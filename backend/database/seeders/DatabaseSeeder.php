<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            JenisLayananSeeder::class,
            PersyaratanMasterSeeder::class,
            TahapanApprovalSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
