<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalVeiculos   = Vehicle::count();
        $disponíveis     = Vehicle::where('status', 'available')->count();
        $vendidos        = Vehicle::where('status', 'sold')->count();
        $totalClientes   = User::where('is_admin', false)->count();
        $avalorTotal     = Vehicle::where('status', 'available')->sum('sale_price');
        $emOferta        = Vehicle::where('is_on_sale', true)->count();

        return [
            Stat::make('Total de Veículos', number_format($totalVeiculos))
                ->description('Cadastrados no sistema')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),

            Stat::make('Disponíveis', number_format($disponíveis))
                ->description($vendidos . ' vendidos este mês')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Em Oferta', number_format($emOferta))
                ->description('Com desconto especial')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning'),

            Stat::make('Valor em Estoque', 'R$ ' . number_format($avalorTotal, 2, ',', '.'))
                ->description('Veículos disponíveis')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Clientes', number_format($totalClientes))
                ->description('Lojistas cadastrados')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
