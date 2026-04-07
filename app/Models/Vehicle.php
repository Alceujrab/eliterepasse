<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'accessories' => 'array',
        'media' => 'array',
        'location' => 'array',
        'has_report' => 'boolean',
        'has_factory_warranty' => 'boolean',
        'is_on_sale' => 'boolean',
        'is_just_arrived' => 'boolean',
        'sale_price' => 'decimal:2',
        'fipe_price' => 'decimal:2',
        'profit_margin' => 'decimal:2',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
