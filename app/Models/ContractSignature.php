<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractSignature extends Model
{
    protected $guarded = [];

    protected $casts = [
        'assinado_em' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isAssinado(): bool
    {
        return ! is_null($this->assinado_em);
    }
}
