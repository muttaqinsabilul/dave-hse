<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskMatrix extends Model
{
    protected $table = 'risk_matrix';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'likelihood',
        'severity',
        'skor',
        'level',
    ];
}
