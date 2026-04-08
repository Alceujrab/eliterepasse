<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'dados_comprador' => 'array',
        'dados_veiculo'   => 'array',
        'dados_pagamento' => 'array',
        'valor_contrato'  => 'decimal:2',
        'enviado_em'      => 'datetime',
        'assinado_em'     => 'datetime',
        'assinado_admin_em' => 'datetime',
    ];

    // ─── Status ─────────────────────────────────────────────────────
    public static function statusLabels(): array
    {
        return [
            'rascunho'   => '📝 Rascunho',
            'aguardando' => '⏳ Aguardando Assinatura',
            'assinado'   => '✅ Assinado',
            'cancelado'  => '❌ Cancelado',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'rascunho'   => 'gray',
            'aguardando' => 'warning',
            'assinado'   => 'success',
            'cancelado'  => 'danger',
        ];
    }

    // ─── Relações ────────────────────────────────────────────────────
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function signatures()
    {
        return $this->hasMany(ContractSignature::class);
    }

    public function assinaturaComprador()
    {
        return $this->hasOne(ContractSignature::class)->where('tipo', 'comprador');
    }

    public function assinaturaVendedor()
    {
        return $this->hasOne(ContractSignature::class)->where('tipo', 'vendedor');
    }

    // ─── Helpers ─────────────────────────────────────────────────────
    public function isAssinado(): bool
    {
        return $this->status === 'assinado';
    }

    public function getUrlAssinaturaAttribute(): string
    {
        return url('/contrato/assinar/' . $this->hash_verificacao);
    }

    /** Gera numero sequencial tipo CONT-2026-000001 */
    public static function gerarNumero(): string
    {
        $ano    = now()->year;
        $ultimo = static::whereYear('created_at', $ano)->max('id') ?? 0;
        return 'CONT-' . $ano . '-' . str_pad($ultimo + 1, 6, '0', STR_PAD_LEFT);
    }
}
