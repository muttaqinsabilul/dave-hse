<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IbprReport extends Model
{
    /** @use HasFactory<IbprReportFactory> */
    use HasFactory;

    protected $fillable = [
        'site_code',
        'tanggal_realtime',
        'kegiatan',
        'bahaya',
        'risiko',
        'likelihood',
        'severity',
        'skor',
        'level',
        'pengendalian',
        'inspector_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_realtime' => 'datetime',
            'likelihood' => 'integer',
            'severity' => 'integer',
            'skor' => 'integer',
        ];
    }

    #[Scope]
    protected function forSite(Builder $query, string $siteCode): Builder
    {
        return $query->where('site_code', $siteCode);
    }

    #[Scope]
    protected function highRisk(Builder $query): Builder
    {
        return $query->whereIn('level', ['HIGH', 'EXTREME']);
    }

    #[Scope]
    protected function recent(Builder $query, int $days = 30): Builder
    {
        return $query->where('tanggal_realtime', '>=', now()->subDays($days));
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_code', 'code');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id', 'id');
    }
}
