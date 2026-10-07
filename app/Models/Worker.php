<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Worker extends Model
{
    /** @use HasFactory<WorkerFactory> */
    use HasFactory;

    public const STATUS_ONSITE = 'ONSITE';

    public const STATUS_OFFSITE = 'OFFSITE';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'site_code',
        'nama',
        'jenis_pekerjaan',
        'mandor_subkon',
        'foto_path',
        'usia',
        'asal',
        'riwayat_penyakit',
        'status_lokasi',
        'tanggal_regis',
    ];

    protected function casts(): array
    {
        return [
            'usia' => 'integer',
            'tanggal_regis' => 'date',
        ];
    }

    public function isOnsite(): bool
    {
        return ($this->status_lokasi ?? 'ONSITE') === 'ONSITE';
    }

    #[Scope]
    protected function onsite(Builder $query): Builder
    {
        return $query->where('status_lokasi', 'ONSITE');
    }

    #[Scope]
    protected function offsite(Builder $query): Builder
    {
        return $query->where('status_lokasi', 'OFFSITE');
    }

    #[Scope]
    protected function forSite(Builder $query, string $siteCode): Builder
    {
        return $query->where('site_code', $siteCode);
    }

    #[Scope]
    protected function search(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term) {
            $q->where('nama', 'like', "%{$term}%")->orWhere('id', 'like', "%{$term}%");
        });
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_code', 'code');
    }

    public function healthChecks(): HasMany
    {
        return $this->hasMany(HealthCheck::class, 'worker_id', 'id');
    }

    protected function lamaBekerjaHari(): Attribute
    {
        return Attribute::get(function (): int {
            if (! $this->tanggal_regis || $this->tanggal_regis->isFuture()) {
                return 1;
            }

            return (int) $this->tanggal_regis->startOfDay()->diffInDays(now()->startOfDay()) + 1;
        });
    }

    protected function fotoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! empty($this->foto_path) && Storage::disk('public')->exists($this->foto_path)) {
                return Storage::url($this->foto_path);
            }

            return null;
        });
    }
}
