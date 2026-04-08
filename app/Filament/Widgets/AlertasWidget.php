<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Notifications\DatabaseNotification;

class AlertasWidget extends Widget
{
    protected string $view = 'filament.widgets.alertas';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 0; // mostra primeiro

    public function getAlertas(): array
    {
        $alertas = [];

        // Tickets urgentes abertos
        $ticketsUrgentes = Ticket::where('prioridade', 'urgente')
            ->where('status', 'aberto')
            ->count();
        if ($ticketsUrgentes > 0) {
            $alertas[] = [
                'tipo'   => 'danger',
                'icone'  => '🔴',
                'titulo' => "{$ticketsUrgentes} chamado(s) urgente(s) sem resposta",
                'url'    => route('filament.admin.resources.tickets.index'),
                'link'   => 'Ver Chamados',
            ];
        }

        // Clientes aguardando aprovação
        $clientesPendentes = User::where('aprovado', false)
            ->where('is_admin', false)
            ->count();
        if ($clientesPendentes > 0) {
            $alertas[] = [
                'tipo'   => 'warning',
                'icone'  => '⏳',
                'titulo' => "{$clientesPendentes} cliente(s) aguardando aprovação",
                'url'    => route('filament.admin.resources.users.index'),
                'link'   => 'Aprovar Clientes',
            ];
        }

        // Documentos pendentes de verificação
        $docsPendentes = Document::where('status', 'pendente')->count();
        if ($docsPendentes > 0) {
            $alertas[] = [
                'tipo'   => 'warning',
                'icone'  => '📄',
                'titulo' => "{$docsPendentes} documento(s) aguardando verificação",
                'url'    => route('filament.admin.resources.documents.index'),
                'link'   => 'Ver Documentos',
            ];
        }

        // Pedidos pendentes há mais de 24h
        $pedidosAtrasados = Order::where('status', 'pendente')
            ->where('created_at', '<=', now()->subHours(24))
            ->count();
        if ($pedidosAtrasados > 0) {
            $alertas[] = [
                'tipo'   => 'warning',
                'icone'  => '🛒',
                'titulo' => "{$pedidosAtrasados} pedido(s) pendente(s) há mais de 24h",
                'url'    => route('filament.admin.resources.orders.index'),
                'link'   => 'Ver Pedidos',
            ];
        }

        // Notificações não lidas dos clientes (acumuladas)
        $notifsNaoLidas = DatabaseNotification::whereNull('read_at')->count();
        if ($notifsNaoLidas > 50) {
            $alertas[] = [
                'tipo'   => 'info',
                'icone'  => '🔔',
                'titulo' => "{$notifsNaoLidas} notificações de clientes não lidas",
                'url'    => route('filament.admin.pages.central-notificacoes'),
                'link'   => 'Ver Central',
            ];
        }

        return $alertas;
    }
}
