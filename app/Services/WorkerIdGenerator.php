<?php

namespace App\Services;

use App\Models\SiteCounter;
use Illuminate\Support\Facades\DB;

class WorkerIdGenerator
{
    public function next(string $siteCode): string
    {
        return DB::transaction(function () use ($siteCode): string {
            $counter = SiteCounter::where('site_code', $siteCode)->lockForUpdate()->firstOrFail();

            $counter->increment('last_no');

            return sprintf('%s-%03d', $siteCode, $counter->last_no);
        });
    }
}
