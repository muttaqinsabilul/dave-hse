<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subcontractor extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_code',
        'nama',
        'bidang',
        'kontak',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_code', 'code');
    }

    public function scopeForSite(Builder $query, string $siteCode): Builder
    {
        return $query->where('site_code', $siteCode);
    }
}
