<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentData extends Model
{
    protected $guarded = [];

    protected $casts = [
        'dados' => 'array',
    ];

    public function payable()
    {
        return $this->morphTo();
    }
}
