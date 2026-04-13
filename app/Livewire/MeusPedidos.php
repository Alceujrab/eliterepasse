<?php

namespace App\Livewire;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MeusPedidos extends Component
{
    public string $abaPedidos = 'todos';
    public ?int   $pedidoOpenId = null;

    public function abrirDetalhe(int $id): void
    {
        $this->pedidoOpenId = $this->pedidoOpenId === $id ? null : $id;
    }

    public function cancelarPedido(int $id): void
    {
        $pedido = Order::where('user_id', auth()->id())->where('id', $id)->firstOrFail();

        if (in_array($pedido->status, ['pendente'])) {
            $pedido->update(['status' => 'cancelado']);
            session()->flash('message', "Pedido {$pedido->numero} cancelado.");
        }
    }

    public function render()
    {
        $user    = auth()->user();
        $userId  = $user->id;

        // ─── Pedidos (filtrado por aba) ───────────────────────────────
        $query = Order::with(['vehicle', 'paymentMethod', 'contract', 'histories', 'shipments'])
            ->where('user_id', $userId)
            ->latest();

        if ($this->abaPedidos !== 'todos') {
            $query->where('status', $this->abaPedidos);
        }

        $pedidos = $query->get();

        // ─── KPIs do Lojista ──────────────────────────────────────────
        $totalGasto = Order::where('user_id', $userId)
            ->whereIn('status', ['confirmado', 'faturado', 'pago'])
            ->sum('valor_compra');

        $totalPedidos = Order::where('user_id', $userId)->count();

        $pedidosMes = Order::where('user_id', $userId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $gastoMes = Order::where('user_id', $userId)
            ->whereIn('status', ['confirmado', 'faturado', 'pago'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('valor_compra');

        $gastoMesPassado = Order::where('user_id', $userId)
            ->whereIn('status', ['confirmado', 'faturado', 'pago'])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('valor_compra');

        $ticketsAbertos = Ticket::where('user_id', $userId)
            ->whereIn('status', ['aberto', 'em_atendimento'])
            ->count();

        $documentosPendentes = Document::where('user_id', $userId)
            ->where('status', 'pendente')
            ->count();

        $contratosPendentes = 0;
        if (class_exists(Contract::class)) {
            $contratosPendentes = Contract::where('user_id', $userId)
                ->where('status', 'pendente')
                ->count();
        }

        // ─── Histórico de gastos por mês (últimos 6 meses) ────────────
        $historicoGastos = Order::where('user_id', $userId)
            ->whereIn('status', ['confirmado', 'faturado', 'pago'])
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->select(
                DB::raw('YEAR(created_at) as ano'),
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('SUM(valor_compra) as total'),
                DB::raw('COUNT(*) as qtd')
            )
            ->groupBy('ano', 'mes')
            ->orderBy('ano')->orderBy('mes')
            ->get()
            ->map(fn ($r) => [
                'label' => Carbon::create($r->ano, $r->mes)->translatedFormat('M/y'),
                'total' => (float) $r->total,
                'qtd'   => (int) $r->qtd,
            ]);

        // ─── Variação vs mês passado ──────────────────────────────────
        $variacaoGasto = $gastoMesPassado > 0
            ? round((($gastoMes - $gastoMesPassado) / $gastoMesPassado) * 100, 1)
            : null;

        return view('livewire.meus-pedidos', compact(
            'pedidos',
            'totalGasto',
            'totalPedidos',
            'pedidosMes',
            'gastoMes',
            'variacaoGasto',
            'ticketsAbertos',
            'documentosPendentes',
            'contratosPendentes',
            'historicoGastos',
        ));
    }
}
