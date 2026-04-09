<?php

namespace App\Livewire;

use App\Models\Financial;
use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Financeiro extends Component
{
    public string $filtro     = 'todos';
    public string $busca      = '';
    public ?int   $pedidoOpen = null; // Modal de detalhes

    // ─── Dados computados ─────────────────────────────────────────────

    public function getPedidosProperty()
    {
        $user = auth()->user();

        return Order::with(['vehicle', 'financial', 'paymentMethod'])
            ->where('user_id', $user->id)
            ->when($this->filtro !== 'todos', fn ($q) => $q->where('status', $this->filtro))
            ->when($this->busca, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('numero', 'LIKE', "%{$this->busca}%")
                       ->orWhereHas('vehicle', fn ($v) =>
                            $v->where('brand', 'LIKE', "%{$this->busca}%")
                              ->orWhere('model', 'LIKE', "%{$this->busca}%")
                       )
                       ->orWhereHas('financial', fn ($f) =>
                            $f->where('numero', 'LIKE', "%{$this->busca}%")
                       );
                });
            })
            ->latest()
            ->get();
    }

    public function getTotalInvestidoProperty(): float
    {
        return Order::where('user_id', auth()->id())
            ->whereIn('status', ['confirmado', 'faturado'])
            ->sum('valor_compra');
    }

    public function getTotalPendenteProperty(): float
    {
        return Financial::whereHas('order', fn ($q) => $q->where('user_id', auth()->id()))
            ->where('status', 'em_aberto')
            ->sum('valor');
    }

    public function getTotalMesProperty(): float
    {
        return Order::where('user_id', auth()->id())
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('valor_compra');
    }

    public function getCountPorStatusProperty(): array
    {
        return Order::where('user_id', auth()->id())
            ->selectRaw('status, COUNT(*) as total, SUM(valor_compra) as soma')
            ->groupBy('status')
            ->get()
            ->keyBy('status')
            ->toArray();
    }

    // ─── UI ──────────────────────────────────────────────────────────

    public function abrirDetalhe(int $id): void
    {
        $this->pedidoOpen = $this->pedidoOpen === $id ? null : $id;
    }

    public function render()
    {
        return view('livewire.financeiro');
    }
}
