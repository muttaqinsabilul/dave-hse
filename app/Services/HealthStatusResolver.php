<?php

namespace App\Services;

class HealthStatusResolver
{
    public function resolve(int $sistol, int $diastol, float $suhu): string
    {
        $normal = config('hse.tensi_normal');

        $sistolOk = $sistol >= $normal['sistol_min'] && $sistol <= $normal['sistol_max'];
        $diastolOk = $diastol >= $normal['diastol_min'] && $diastol <= $normal['diastol_max'];
        $suhuOk = $suhu >= $normal['suhu_min'] && $suhu <= $normal['suhu_max'];

        return $sistolOk && $diastolOk && $suhuOk ? 'NORMAL' : 'FLAG';
    }
}
