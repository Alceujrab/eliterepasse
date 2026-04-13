<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderShipment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'despachado_em' => 'datetime',
    ];

    // ─── Tipos de Documento ──────────────────────────────────────────
    public static function tipoDocumentoLabels(): array
    {
        return [
            'atpv'           => '📋 ATPV',
            'atpve'          => '📋 ATPV-e',
            'transferencia'  => '📄 Documento de Transferência',
            'outro'          => '📌 Outro',
        ];
    }

    // ─── Métodos de Envio ────────────────────────────────────────────
    public static function metodoEnvioLabels(): array
    {
        return [
            'correios'       => '📦 Correios',
            'transportadora' => '🚚 Transportadora',
            'motoboy'        => '🏍️ Motoboy',
            'retirada'       => '🏢 Retirada no Local',
            'outro'          => '📌 Outro',
        ];
    }

    // ─── Status ──────────────────────────────────────────────────────
    public static function statusLabels(): array
    {
        return [
            'disponivel'  => '📥 Disponível para Download',
            'despachado'  => '📦 Despachado',
            'entregue'    => '✅ Entregue',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'disponivel'  => 'info',
            'despachado'  => 'warning',
            'entregue'    => 'success',
        ];
    }

    // ─── Helpers ─────────────────────────────────────────────────────
    public function getUrlDocumentoAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function getUrlComprovanteAttribute(): ?string
    {
        return $this->comprovante_despacho_path
            ? asset('storage/' . $this->comprovante_despacho_path)
            : null;
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
}
