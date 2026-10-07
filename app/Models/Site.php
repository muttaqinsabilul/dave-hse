<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    /** @use HasFactory<SiteFactory> */
    use HasFactory;

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
    ];

    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class, 'site_code', 'code');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'site_code', 'code');
    }

    public function ibprReports(): HasMany
    {
        return $this->hasMany(IbprReport::class, 'site_code', 'code');
    }
}
