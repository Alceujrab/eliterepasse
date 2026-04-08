<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'config' => 'array',
        'ativo'  => 'boolean',
    ];

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true)->orderBy('ordem');
    }

    /** Retorna os campos esperados para este método */
    public function getCamposAttribute(): array
    {
        return $this->config['campos'] ?? [];
    }
}
