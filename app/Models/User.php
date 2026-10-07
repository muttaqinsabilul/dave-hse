<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'role',
        'site_code',
        'name',
        'pin',
    ];

    protected $hidden = [
        'pin',
    ];

    protected function casts(): array
    {
        return [
            'pin' => 'hashed',
        ];
    }

    #[Scope]
    protected function admins(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    #[Scope]
    protected function inspectors(Builder $query): Builder
    {
        return $query->where('role', 'inspector');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_code', 'code');
    }

    public function healthChecks(): HasMany
    {
        return $this->hasMany(HealthCheck::class, 'inspector_id', 'id');
    }

    public function ibprReports(): HasMany
    {
        return $this->hasMany(IbprReport::class, 'inspector_id', 'id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInspector(): bool
    {
        return $this->role === 'inspector';
    }

    public function canAccessSite(string $siteCode): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->site_code === $siteCode;
    }
}
