<?php

namespace App\Filament\Pages;

use App\Models\Vehicle;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Painel de Controle';
    protected static string|\UnitEnum|null $navigationGroup = null;
    protected static ?int $navigationSort = -1;

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 3,
            'xl' => 5,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getHeading(): string
    {
        $name = Auth::user()?->name ?? 'Administrador';
        $hora = now()->format('H');
        $saudacao = match (true) {
            $hora >= 5 && $hora < 12 => 'Bom dia',
            $hora >= 12 && $hora < 18 => 'Boa tarde',
            default => 'Boa noite',
        };
        return "{$saudacao}, {$name}! 👋";
    }

    public function getSubheading(): ?string
    {
        $total = Vehicle::count();
        $disponiveis = Vehicle::where('status', 'available')->count();
        return "Você tem {$disponiveis} veículos disponíveis de {$total} no estoque.";
    }
}
