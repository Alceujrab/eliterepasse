<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = [];

    protected $casts = [
        'verificado_em'   => 'datetime',
        'validade'        => 'date',
        'visivel_cliente' => 'boolean',
    ];

    // ─── Tipos ───────────────────────────────────────────────────────
    public static function tipoLabels(): array
    {
        return [
            'crv'              => '📋 CRV',
            'crlv'             => '📋 CRLV',
            'laudo_vistoria'   => '🔍 Laudo de Vistoria',
            'laudo_cautelar'   => '⚖️ Laudo Cautelar',
            'nota_fiscal'      => '🧾 Nota Fiscal',
            'historico_ipva'   => '💰 IPVA',
            'historico_multas' => '🚦 Histórico de Multas',
            'contrato_compra'  => '📄 Contrato de Compra',
            'cnh'              => '🪪 CNH',
            'outro'            => '📌 Outro',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'pendente'   => 'warning',
            'verificado' => 'success',
            'rejeitado'  => 'danger',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            'pendente'   => '⏳ Pendente',
            'verificado' => '✅ Verificado',
            'rejeitado'  => '❌ Rejeitado',
        ];
    }

    // ─── Helpers ─────────────────────────────────────────────────────
    public function getTamanhoFormatadoAttribute(): string
    {
        $bytes = $this->tamanho ?? 0;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function estaVencido(): bool
    {
        return $this->validade && $this->validade->isPast();
    }

    // ─── Relações ────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function verificadoPor()
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }
}
