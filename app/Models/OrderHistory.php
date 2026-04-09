<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    protected $guarded = [];

    protected $casts = [
        'dados' => 'array',
    ];

    // ─── Labels ───────────────────────────────────────────────────────

    public static function acaoLabels(): array
    {
        return [
            'pedido_criado'        => '🛒 Pedido criado',
            'pedido_confirmado'    => '✅ Pedido confirmado',
            'contrato_gerado'     => '📄 Contrato gerado',
            'contrato_assinado'   => '✍️ Contrato assinado',
            'fatura_gerada'       => '💰 Fatura gerada',
            'pagamento_confirmado'=> '💚 Pagamento confirmado',
            'pedido_cancelado'    => '❌ Pedido cancelado',
        ];
    }

    public static function acaoIcons(): array
    {
        return [
            'pedido_criado'        => '🛒',
            'pedido_confirmado'    => '✅',
            'contrato_gerado'     => '📄',
            'contrato_assinado'   => '✍️',
            'fatura_gerada'       => '💰',
            'pagamento_confirmado'=> '💚',
            'pedido_cancelado'    => '❌',
        ];
    }

    // ─── Helper para registrar ────────────────────────────────────────

    public static function registrar(
        int $orderId,
        string $acao,
        ?string $statusDe = null,
        ?string $statusPara = null,
        ?string $descricao = null,
        ?int $userId = null,
        ?array $dados = null
    ): static {
        return static::create([
            'order_id'    => $orderId,
            'acao'        => $acao,
            'status_de'   => $statusDe,
            'status_para' => $statusPara,
            'descricao'   => $descricao ?? (static::acaoLabels()[$acao] ?? $acao),
            'user_id'     => $userId ?? auth()->id(),
            'dados'       => $dados,
        ]);
    }

    // ─── Relações ─────────────────────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
