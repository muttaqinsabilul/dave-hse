<?php

namespace Database\Seeders;

use App\Models\HealthCheck;
use App\Models\IbprReport;
use App\Models\Site;
use App\Models\User;
use App\Models\Worker;
use App\Services\HealthStatusResolver;
use App\Services\RiskLevelResolver;
use App\Services\WorkerIdGenerator;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $ids = app(WorkerIdGenerator::class);
        $health = app(HealthStatusResolver::class);
        $risk = app(RiskLevelResolver::class);

        $names = ['Budi Santoso', 'Agus Wijaya', 'Slamet Riyadi', 'Dedi Kurniawan', 'Rina Marlina'];
        $jobs = ['Tukang Las', 'Tukang Batu', 'Kenek', 'Operator Alat Berat', 'Tukang Listrik'];

        foreach (Site::all() as $siteIndex => $site) {
            $inspector = User::where('role', 'inspector')->where('site_code', $site->code)->firstOrFail();
            $mandors = config('hse.mandor_per_site.'.$site->code);

            for ($i = 0; $i < 4; $i++) {
                $worker = Worker::create([
                    'id' => $ids->next($site->code),
                    'site_code' => $site->code,
                    'nama' => $names[($siteIndex + $i) % count($names)],
                    'jenis_pekerjaan' => $jobs[($siteIndex + $i) % count($jobs)],
                    'mandor_subkon' => $mandors[($siteIndex + $i) % count($mandors)],
                    'foto_path' => 'workers/placeholder.png',
                    'usia' => 25 + $i * 3,
                    'asal' => 'Jawa Timur',
                    'riwayat_penyakit' => $i === 0 ? 'Hipertensi ringan' : null,
                    'tanggal_regis' => today()->subDays(30 + $i * 10),
                ]);

                $sistol = $i === 0 ? 145 : 118;
                $diastol = $i === 0 ? 95 : 78;
                $suhu = 36.6 + $i * 0.1;

                HealthCheck::create([
                    'worker_id' => $worker->id,
                    'tanggal' => today(),
                    'sistol' => $sistol,
                    'diastol' => $diastol,
                    'suhu' => $suhu,
                    'inspector_id' => $inspector->id,
                    'status' => $health->resolve($sistol, $diastol, $suhu),
                ]);
            }

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
