<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'atribuido_em'  => 'datetime',
        'resolvido_em'  => 'datetime',
        'fechado_em'    => 'datetime',
        'prazo_resposta'=> 'datetime',
    ];

    // ─── Status ─────────────────────────────────────────────────────
    public static function statusLabels(): array
    {
        return [
            'aberto'             => '🔴 Aberto',
            'em_atendimento'     => '🟡 Em Atendimento',
            'aguardando_cliente' => '🔵 Aguardando Cliente',
            'resolvido'          => '🟢 Resolvido',
            'fechado'            => '⚫ Fechado',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'aberto'             => 'danger',
            'em_atendimento'     => 'warning',
            'aguardando_cliente' => 'info',
            'resolvido'          => 'success',
            'fechado'            => 'gray',
        ];
    }

    public static function prioridadeColors(): array
    {
        return [
            'baixa'   => 'gray',
            'media'   => 'info',
            'alta'    => 'warning',
            'urgente' => 'danger',
        ];
    }

    public static function categoriaLabels(): array
    {
        return [
            'duvida'           => '❓ Dúvida',
            'problema_tecnico' => '🔧 Problema Técnico',
            'financeiro'       => '💰 Financeiro',
            'contrato'         => '📄 Contrato',
            'veiculo'          => '🚗 Veículo',
            'outro'            => '📌 Outro',
        ];
    }

    /** SLA em horas por prioridade */
    public static function slaPorPrioridade(string $prioridade): int
    {
        return match ($prioridade) {
            'urgente' => 2,
            'alta'    => 8,
            'media'   => 24,
            default   => 72,
        };
    }

    public function gerarNumero(): string
    {
        return 'TKT-' . now()->year . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    // ─── Helpers ─────────────────────────────────────────────────────
    public function estaAtrasado(): bool
    {
        return $this->prazo_resposta && now()->isAfter($this->prazo_resposta)
            && ! in_array($this->status, ['resolvido', 'fechado']);
    }

    // ─── Relações ────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function atribuidoA()
    {
        return $this->belongsTo(User::class, 'atribuido_a');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at');
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }
}
