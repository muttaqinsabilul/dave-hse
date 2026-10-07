<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SiteSeeder::class,
            RiskMatrixSeeder::class,
            SiteCounterSeeder::class,
            UserSeeder::class,
            SubcontractorSeeder::class,
        ]);

        if (app()->isLocal()) {
            $this->call([DemoSeeder::class]);
        }
    }
}
