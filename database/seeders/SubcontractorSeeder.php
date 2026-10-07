<?php

namespace Database\Seeders;

use App\Models\Subcontractor;
use Illuminate\Database\Seeder;

class SubcontractorSeeder extends Seeder
{
    public function run(): void
    {
        $mandorConfig = config('hse.mandor_per_site', []);

        foreach ($mandorConfig as $siteCode => $list) {
            foreach ($list as $nama) {
                Subcontractor::firstOrCreate(
                    ['site_code' => $siteCode, 'nama' => $nama],
                    [
                        'bidang' => str_contains(strtolower($nama), 'mandor') ? 'Pekerjaan Sipil & Finishing' : 'Struktur & Konstruksi Terpadu',
                        'kontak' => '08'.fake()->numerify('1#########'),
                    ]
                );
            }
        }
    }
}
