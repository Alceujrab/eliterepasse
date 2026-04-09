<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Financial extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'financials';

    protected $casts = [
        'data_vencimento' => 'date',
        'data_pagamento'  => 'date',
        'valor'           => 'decimal:2',
    ];

    // ─── Labels ──────────────────────────────────────────────────────

    public static function statusLabels(): array
    {
        return [
            'em_aberto'  => '🟡 Em Aberto',
            'pago'       => '🟢 Pago',
            'vencido'    => '🔴 Vencido',
            'cancelado'  => '⚫ Cancelado',
            'estornado'  => '🔵 Estornado',
        ];
    }

    public static function formasPagamento(): array
    {
        return [
            'boleto'       => '🎫 Boleto',
            'pix'          => '⚡ PIX',
            'transferencia'=> '🏦 Transferência',
            'cartao'       => '💳 Cartão',
            'cheque'       => '📋 Cheque',
            'dinheiro'     => '💵 Dinheiro',
        ];
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    public function getEstaVencidoAttribute(): bool
    {
        return $this->data_vencimento
            && now()->isAfter($this->data_vencimento)
            && $this->status === 'em_aberto';
    }

    public function getStatusCorAttribute(): string
    {
        if ($this->esta_vencido) return 'danger';
        return match($this->status) {
            'pago'       => 'success',
            'vencido'    => 'danger',
            'estornado'  => 'info',
            'cancelado'  => 'gray',
            default      => 'warning',
        };
    }

    // ─── Geração de Número ───────────────────────────────────────────

    public static function gerarNumero(): string
    {
        $ano = now()->format('Y');
        $ultimo = static::where('numero', 'like', "FAT-{$ano}-%")
            ->orderByDesc('numero')
            ->value('numero');

        $seq = $ultimo
            ? ((int) substr($ultimo, -6)) + 1
            : 1;

        return "FAT-{$ano}-" . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }

    // ─── Relações ─────────────────────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }
}
