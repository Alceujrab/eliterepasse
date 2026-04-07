<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'faq' => 'array',
    ];
}
