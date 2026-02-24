<?php

namespace Modules\Pajak\Database\Seeders;

use Illuminate\Database\Seeder;

class PajakDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            MasterAkunPajakSeeder::class,
        ]);
    }
}
