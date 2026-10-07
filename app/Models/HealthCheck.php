<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthCheck extends Model
{
    /** @use HasFactory<HealthCheckFactory> */
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'tanggal',
        'sistol',
        'diastol',
        'suhu',
        'inspector_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'sistol' => 'integer',
            'diastol' => 'integer',
            'suhu' => 'decimal:1',
        ];
    }

    #[Scope]
    protected function today(Builder $query): Builder
    {
        return $query->whereDate('tanggal', today());
    }

    #[Scope]
    protected function flagged(Builder $query): Builder
    {
        return $query->where('status', 'FLAG');
    }

    #[Scope]
    protected function forSite(Builder $query, string $siteCode): Builder
    {
        return $query->whereHas('worker', fn (Builder $q) => $q->forSite($siteCode));
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'worker_id', 'id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id', 'id');
    }
}
