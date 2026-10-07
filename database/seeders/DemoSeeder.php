<?php

namespace Database\Seeders;

use App\Models\IbprReport;
use App\Models\Site;
use App\Models\User;
use App\Services\RiskLevelResolver;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $risk = app(RiskLevelResolver::class);

        foreach (Site::all() as $site) {
            $inspector = User::where('role', 'inspector')->where('site_code', $site->code)->firstOrFail();

            $pairs = [[2, 3], [4, 4], [3, 5]];

            foreach ($pairs as $j => [$likelihood, $severity]) {
                $resolved = $risk->resolve($likelihood, $severity);

                IbprReport::create([
                    'site_code' => $site->code,
                    'tanggal_realtime' => now()->subDays($j),
                    'kegiatan' => 'Pengelasan struktur baja',
                    'bahaya' => 'Percikan api dan asap las',
                    'risiko' => 'Luka bakar dan gangguan pernapasan',
                    'likelihood' => $likelihood,
                    'severity' => $severity,
                    'skor' => $resolved['skor'],
                    'level' => $resolved['level'],
                    'pengendalian' => 'APD lengkap + APAR di titik kerja',
                    'inspector_id' => $inspector->id,
                ]);
            }
        }
    }
}
