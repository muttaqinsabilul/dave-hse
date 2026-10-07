<?php

namespace App\Services;

use App\Models\RiskMatrix;

class RiskLevelResolver
{
    /**
     * @return array{skor: int, level: string}
     */
    public function resolve(int $likelihood, int $severity): array
    {
        $cell = RiskMatrix::where('likelihood', $likelihood)->where('severity', $severity)->firstOrFail();

        return ['skor' => $cell->skor, 'level' => $cell->level];
    }
}
