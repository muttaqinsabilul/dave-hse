<?php

namespace Database\Seeders;

use App\Models\SiteCounter;
use Illuminate\Database\Seeder;

class SiteCounterSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['01', '02', '03', '04', '05'] as $siteCode) {
            SiteCounter::firstOrCreate(['site_code' => $siteCode], ['last_no' => 0]);
        }
    }
}
