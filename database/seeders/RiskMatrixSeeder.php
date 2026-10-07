<?php

namespace Database\Seeders;

use App\Models\RiskMatrix;
use Illuminate\Database\Seeder;

class RiskMatrixSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            1 => ['LOW', 'LOW', 'LOW', 'MEDIUM', 'MEDIUM'],
            2 => ['LOW', 'MEDIUM', 'MEDIUM', 'HIGH', 'HIGH'],
            3 => ['LOW', 'MEDIUM', 'HIGH', 'HIGH', 'EXTREME'],
            4 => ['MEDIUM', 'HIGH', 'HIGH', 'HIGH', 'EXTREME'],
            5 => ['MEDIUM', 'HIGH', 'EXTREME', 'EXTREME', 'EXTREME'],
        ];

        foreach ($levels as $likelihood => $row) {
            foreach ($row as $i => $level) {
                $severity = $i + 1;

                RiskMatrix::updateOrCreate(
                    ['likelihood' => $likelihood, 'severity' => $severity],
                    ['skor' => $likelihood * $severity, 'level' => $level]
                );
            }
        }
    }
}
