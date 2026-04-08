<?php

namespace App\Filament\Pages;

use App\Models\Financial;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page
{
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Painel de Controle';
    protected static string|\UnitEnum|null $navigationGroup = null;
    protected static ?int $navigationSort = -1;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    protected string $view = 'filament.pages.dashboard';

    // ─── Dados computados ─────────────────────────────────────────────

    public function getSaudacaoProperty(): string
    {
        $nome = Auth::user()?->name ?? 'Admin';
        $hora = (int) now()->format('H');
        $s = match(true) {
            $hora >= 5 && $hora < 12  => 'Bom dia',
            $hora >= 12 && $hora < 18 => 'Boa tarde',
            default                   => 'Boa noite',
        };
        return "{$s}, {$nome}! 👋";
    }

    public function getKpisProperty(): array
    {
        $mesAtual  = now()->month;
        $anoAtual  = now()->year;
        $mesPassado = now()->subMonth();

        // Veículos
        $totalVeiculos  = Vehicle::count();
        $disponiveis    = Vehicle::where('status', 'available')->count();
        $valorEstoque   = Vehicle::where('status', 'available')->sum('sale_price');

        // Pedidos
        $pedidosMes        = Order::whereMonth('created_at', $mesAtual)->whereYear('created_at', $anoAtual)->count();
        $pedidosMesPassado = Order::whereMonth('created_at', $mesPassado->month)->whereYear('created_at', $mesPassado->year)->count();
        $pedidosPendentes  = Order::where('status', 'pendente')->count();
        $faturadoMes       = Order::whereIn('status', ['faturado', 'confirmado'])
            ->whereMonth('created_at', $mesAtual)->whereYear('created_at', $anoAtual)
            ->sum('valor_compra');

        // Clientes
        $totalClientes    = User::where('is_admin', false)->count();
        $clientesPendentes = User::where('is_admin', false)->where('status', 'pendente')->count();
        $novosClientesMes = User::where('is_admin', false)
            ->whereMonth('created_at', $mesAtual)->whereYear('created_at', $anoAtual)->count();

        // Tickets
        $ticketsAbertos  = Ticket::where('status', 'aberto')->count();
        $ticketsUrgentes = Ticket::where('prioridade', 'urgente')->whereNotIn('status', ['resolvido', 'fechado'])->count();
        $ticketsWa       = Ticket::where('type', 'whatsapp')->where('status', 'aberto')->count();

        // Financeiro
        $aReceber   = Financial::where('status', 'em_aberto')->sum('valor');
        $vencidos   = Financial::where('status', 'em_aberto')->whereNotNull('data_vencimento')
            ->where('data_vencimento', '<', now())->count();
        $pagosMes   = Financial::where('status', 'pago')
            ->whereMonth('data_pagamento', $mesAtual)->whereYear('data_pagamento', $anoAtual)
            ->sum('valor');

        return compact(
            'totalVeiculos', 'disponiveis', 'valorEstoque',
            'pedidosMes', 'pedidosMesPassado', 'pedidosPendentes', 'faturadoMes',
            'totalClientes', 'clientesPendentes', 'novosClientesMes',
            'ticketsAbertos', 'ticketsUrgentes', 'ticketsWa',
            'aReceber', 'vencidos', 'pagosMes'
        );
    }

    public function getAlertasProperty(): array
    {
        $alertas = [];

        $clPend = User::where('is_admin', false)->where('status', 'pendente')->count();
        if ($clPend > 0) $alertas[] = ['tipo' => 'warning', 'msg' => "⏳ {$clPend} cliente(s) aguardando aprovação", 'url' => '/admin/users'];

        $tkUrg = Ticket::where('prioridade', 'urgente')->whereNotIn('status', ['resolvido', 'fechado'])->count();
        if ($tkUrg > 0) $alertas[] = ['tipo' => 'danger', 'msg' => "🔴 {$tkUrg} ticket(s) urgente(s) sem resolução", 'url' => '/admin/tickets'];

        $vencidos = Financial::where('status', 'em_aberto')->whereNotNull('data_vencimento')
            ->where('data_vencimento', '<', now())->count();
        if ($vencidos > 0) $alertas[] = ['tipo' => 'danger', 'msg' => "💰 {$vencidos} cobrança(s) vencida(s)", 'url' => '/admin/gestao-financeira'];

        $pedAtrasados = Order::where('status', 'pendente')->where('created_at', '<=', now()->subHours(48))->count();
        if ($pedAtrasados > 0) $alertas[] = ['tipo' => 'warning', 'msg' => "🛒 {$pedAtrasados} pedido(s) pendentes há +48h", 'url' => '/admin/orders'];

        return $alertas;
    }

    public function getAtividadesRecentesProperty()
    {
        // Últimas 10 ações do sistema
        return collect()
            ->merge(
                Order::with('user')->latest()->limit(5)->get()->map(fn ($o) => [
                    'icon' => '🛒', 'msg' => "Pedido {$o->numero} — " . ($o->user?->razao_social ?? $o->user?->name),
                    'status' => $o->status, 'data' => $o->created_at,
                ])
            )
            ->merge(
                Ticket::with('user')->latest()->limit(5)->get()->map(fn ($t) => [
                    'icon' => '🎫', 'msg' => mb_strimwidth($t->titulo, 0, 50, '...'),
                    'status' => $t->status, 'data' => $t->created_at,
                ])
            )
            ->merge(
                User::where('is_admin', false)->latest()->limit(3)->get()->map(fn ($u) => [
                    'icon' => '👤', 'msg' => "Novo cliente: " . ($u->razao_social ?? $u->name),
                    'status' => $u->status, 'data' => $u->created_at,
                ])
            )
            ->sortByDesc('data')
            ->take(10);
    }

    public function getGraficoMensalProperty(): array
    {
        $labels = [];
        $valores = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i);
            $labels[] = $mes->translatedFormat('M/y');
            $valores[] = (float) Order::whereMonth('created_at', $mes->month)
                ->whereYear('created_at', $mes->year)
                ->sum('valor_compra');
        }
        return ['labels' => $labels, 'valores' => $valores];
    }
}

