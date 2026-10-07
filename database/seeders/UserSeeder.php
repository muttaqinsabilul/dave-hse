<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['id' => 'ADM-001'],
            ['role' => 'admin', 'site_code' => null, 'name' => 'David Prasetyo (HSE Admin)', 'pin' => null]
        );

        $inspectors = [
            '01' => 'Budi Santoso, S.T.',
            '02' => 'Agus Setiawan, S.T.',
            '03' => 'Dedi Kurniawan, S.T.',
            '04' => 'Hendra Pratama, S.T.',
            '05' => 'Rian Hidayat, S.T.',
        ];

        foreach ($inspectors as $siteCode => $name) {
            User::updateOrCreate(
                ['id' => "INS-{$siteCode}-001"],
                ['role' => 'inspector', 'site_code' => $siteCode, 'name' => $name, 'pin' => null]
            );
        }
    }
}
