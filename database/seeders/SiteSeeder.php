<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        $sites = [
            ['code' => '01', 'name' => 'Jember'],
            ['code' => '02', 'name' => 'Situbondo'],
            ['code' => '03', 'name' => 'Bondowoso'],
            ['code' => '04', 'name' => 'Probolinggo'],
            ['code' => '05', 'name' => 'Lumajang'],
        ];

        foreach ($sites as $site) {
            Site::updateOrCreate(['code' => $site['code']], $site);
        }
    }
}
