<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleReport extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'aprovado_em' => 'datetime',
    ];

    // ─── Status ──────────────────────────────────────────────────────
    public static function statusLabels(): array
    {
        return [
            'rascunho'   => '📝 Rascunho',
            'em_revisao' => '🔍 Em Revisão',
            'aprovado'   => '✅ Aprovado',
            'reprovado'  => '❌ Reprovado',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'rascunho'   => 'gray',
            'em_revisao' => 'warning',
            'aprovado'   => 'success',
            'reprovado'  => 'danger',
        ];
    }

    public static function tipoLabels(): array
    {
        return [
            'vistoria_entrada' => '🚗 Vistoria de Entrada',
            'cautelar'         => '⚖️ Laudo Cautelar',
            'revisao'          => '🔧 Revisão Periódica',
            'avaria'           => '⚠️ Registro de Avaria',
        ];
    }

    // ─── Templates de checklist por tipo ─────────────────────────────
    public static function checklistPadrao(string $tipo = 'vistoria_entrada'): array
    {
        $base = [
            ['grupo' => 'Motor',           'item' => 'Estado geral do motor'],
            ['grupo' => 'Motor',           'item' => 'Nível de óleo'],
            ['grupo' => 'Motor',           'item' => 'Nível de água/radiador'],
            ['grupo' => 'Motor',           'item' => 'Correia dentada'],
            ['grupo' => 'Motor',           'item' => 'Vazamento de óleo/fluidos'],
            ['grupo' => 'Transmissão',     'item' => 'Câmbio (operação)'],
            ['grupo' => 'Transmissão',     'item' => 'Embreagem (manual)'],
            ['grupo' => 'Suspensão',       'item' => 'Amortecedores dianteiros'],
            ['grupo' => 'Suspensão',       'item' => 'Amortecedores traseiros'],
            ['grupo' => 'Suspensão',       'item' => 'Buchas e braços'],
            ['grupo' => 'Freios',          'item' => 'Pastilhas/lonas dianteiras'],
            ['grupo' => 'Freios',          'item' => 'Pastilhas/lonas traseiras'],
            ['grupo' => 'Freios',          'item' => 'Discos/tambores'],
            ['grupo' => 'Freios',          'item' => 'Fluido de freio'],
            ['grupo' => 'Carroceria',      'item' => 'Lataria (amassados/riscos)'],
            ['grupo' => 'Carroceria',      'item' => 'Pintura (uniformidade)'],
            ['grupo' => 'Carroceria',      'item' => 'Para-choques dianteiro'],
            ['grupo' => 'Carroceria',      'item' => 'Para-choques traseiro'],
            ['grupo' => 'Vidros',          'item' => 'Para-brisa (trincas/riscos)'],
            ['grupo' => 'Vidros',          'item' => 'Vidros laterais'],
            ['grupo' => 'Interior',        'item' => 'Banco do motorista'],
            ['grupo' => 'Interior',        'item' => 'Bancos traseiros'],
            ['grupo' => 'Interior',        'item' => 'Painel (instrumentos)'],
            ['grupo' => 'Interior',        'item' => 'Ar condicionado'],
            ['grupo' => 'Elétrica',        'item' => 'Bateria'],
            ['grupo' => 'Elétrica',        'item' => 'Faróis dianteiros'],
            ['grupo' => 'Elétrica',        'item' => 'Lanternas traseiras'],
            ['grupo' => 'Elétrica',        'item' => 'Setas e pisca-alerta'],
            ['grupo' => 'Pneus/Rodas',     'item' => 'Pneu dianteiro esquerdo'],
            ['grupo' => 'Pneus/Rodas',     'item' => 'Pneu dianteiro direito'],
            ['grupo' => 'Pneus/Rodas',     'item' => 'Pneu traseiro esquerdo'],
            ['grupo' => 'Pneus/Rodas',     'item' => 'Pneu traseiro direito'],
            ['grupo' => 'Pneus/Rodas',     'item' => 'Estepe'],
            ['grupo' => 'Documentação',    'item' => 'CRV/CRLV'],
            ['grupo' => 'Documentação',    'item' => 'Multas pendentes'],
            ['grupo' => 'Documentação',    'item' => 'Recall pendente'],
        ];

        if ($tipo === 'cautelar') {
            $base[] = ['grupo' => 'Cautelar', 'item' => 'Número de chassi (confere)'];
            $base[] = ['grupo' => 'Cautelar', 'item' => 'Número de motor (confere)'];
            $base[] = ['grupo' => 'Cautelar', 'item' => 'Histórico de sinistros (consulta)'];
            $base[] = ['grupo' => 'Cautelar', 'item' => 'Restrição financeira/judicial'];
        }

        return $base;
    }

    public function gerarNumero(): string
    {
        return 'REL-' . now()->year . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getNotaColorAttribute(): string
    {
        $nota = $this->nota_geral ?? 0;
        if ($nota >= 8) return 'success';
        if ($nota >= 6) return 'warning';
        return 'danger';
    }

    // ─── Relações ────────────────────────────────────────────────────
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function aprovadoPor()
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }

    public function items()
    {
        return $this->hasMany(VehicleReportItem::class)->orderBy('ordem');
    }

    public function itemsPorGrupo(): \Illuminate\Support\Collection
    {
        return $this->items->groupBy('grupo');
    }

    // Estatísticas
    public function totalOk(): int
    {
        return $this->items->where('resultado', 'ok')->count();
    }

    public function totalAtencao(): int
    {
        return $this->items->where('resultado', 'atencao')->count();
    }

    public function totalReprovado(): int
    {
        return $this->items->where('resultado', 'reprovado')->count();
    }
}
