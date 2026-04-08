<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Notifications\DatabaseNotification;

class AlertasWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    // Sem heading para não poluir
    protected function getStats(): array
    {
        $ticketsUrgentes   = Ticket::where('prioridade', 'urgente')->where('status', 'aberto')->count();
        $clientesPendentes = User::where('status', 'pendente')->where('is_admin', false)->count();
        $docsPendentes     = Document::where('status', 'pendente')->count();
        $pedidosAtrasados  = Order::where('status', 'pendente')
            ->where('created_at', '<=', now()->subHours(24))
            ->count();
        $notifsNaoLidas    = DatabaseNotification::whereNull('read_at')->count();

        return [
            Stat::make('🔴 Tickets Urgentes', $ticketsUrgentes)
                ->description('Abertos sem resposta')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($ticketsUrgentes > 0 ? 'danger' : 'success')
                ->url('/admin/tickets'),

            Stat::make('⏳ Clientes Pendentes', $clientesPendentes)
                ->description('Aguardando aprovação')
                ->descriptionIcon('heroicon-o-clock')
                ->color($clientesPendentes > 0 ? 'warning' : 'success')
                ->url('/admin/users'),

            Stat::make('📄 Documentos', $docsPendentes)
                ->description('Pendentes de verificação')
                ->descriptionIcon('heroicon-o-document-magnifying-glass')
                ->color($docsPendentes > 0 ? 'warning' : 'success')
                ->url('/admin/documents'),

            Stat::make('🛒 Pedidos Parados', $pedidosAtrasados)
                ->description('Pendentes há +24h')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color($pedidosAtrasados > 0 ? 'warning' : 'success')
                ->url('/admin/orders'),

            Stat::make('🔔 Notif. Não Lidas', $notifsNaoLidas)
                ->description('De clientes')
                ->descriptionIcon('heroicon-o-bell')
                ->color($notifsNaoLidas > 0 ? 'info' : 'success')
                ->url('/admin/central-notificacoes'),
        ];
    }
}
