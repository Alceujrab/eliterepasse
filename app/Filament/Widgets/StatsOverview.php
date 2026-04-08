<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalVeiculos   = Vehicle::count();
        $disponiveis     = Vehicle::where('status', 'available')->count();
        $vendidos        = Vehicle::where('status', 'sold')->count();
        $reservados      = Vehicle::where('status', 'reserved')->count();
        $totalClientes   = User::where('is_admin', false)->count();
        $pendentes       = User::where('is_admin', false)->where('status', 'pendente')->count();
        $valorEstoque    = Vehicle::where('status', 'available')->sum('sale_price');

        // Pedidos
        $pedidosPendentes   = Order::where('status', 'pendente')->count();
        $pedidosConfirmados = Order::where('status', 'confirmado')->count();
        $faturadosMes       = Order::where('status', 'faturado')
            ->whereMonth('created_at', now()->month)->sum('valor_compra');

        return [
            Stat::make('Estoque Disponível', number_format($disponiveis))
                ->description($reservados . ' reservados · ' . $vendidos . ' vendidos')
                ->descriptionIcon('heroicon-m-truck')
                ->color($disponiveis > 0 ? 'success' : 'warning'),

            Stat::make('Valor em Estoque', 'R$ ' . number_format($valorEstoque, 0, ',', '.'))
                ->description('Veículos disponíveis')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pedidos Pendentes', number_format($pedidosPendentes))
                ->description($pedidosConfirmados . ' confirmados aguardando')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color($pedidosPendentes > 0 ? 'warning' : 'gray'),

            Stat::make('Faturado no Mês', 'R$ ' . number_format($faturadosMes, 0, ',', '.'))
                ->description(now()->translatedFormat('F/Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('Clientes', number_format($totalClientes))
                ->description($pendentes > 0 ? $pendentes . ' aguardando aprovação' : 'Todos aprovados ✅')
                ->descriptionIcon('heroicon-m-users')
                ->color($pendentes > 0 ? 'warning' : 'success'),
        ];
    }
}
