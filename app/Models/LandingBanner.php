<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingBanner extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
