<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteCounter extends Model
{
    protected $primaryKey = 'site_code';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'site_code',
        'last_no',
    ];

    protected function casts(): array
    {
        return [
            'last_no' => 'integer',
        ];
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_code', 'code');
    }
}
