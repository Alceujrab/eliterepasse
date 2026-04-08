<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Document;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\ClienteAprovado;
use App\Notifications\ContratoParaAssinar;
use App\Notifications\DocumentoVerificado;
use App\Notifications\PedidoConfirmado;
use App\Notifications\TicketAtualizado;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Dispara todas as notificações para um pedido confirmado.
     */
    public function pedidoConfirmado(Order $order): void
    {
        try {
            $user = $order->user;
            if (! $user) return;

            $user->notify(new PedidoConfirmado($order));
        } catch (\Exception $e) {
            Log::error('NotificationService::pedidoConfirmado', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Notifica cliente que há contrato para assinar.
     */
    public function contratoParaAssinar(Contract $contract, string $linkAssinatura): void
    {
        try {
            $user = $contract->user;
            if (! $user) return;

            $user->notify(new ContratoParaAssinar($contract, $linkAssinatura));
        } catch (\Exception $e) {
            Log::error('NotificationService::contratoParaAssinar', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Notifica cliente que seu ticket recebeu uma resposta.
     */
    public function ticketAtualizado(Ticket $ticket, TicketMessage $message): void
    {
        try {
            $user = $ticket->user;
            if (! $user || $message->is_internal) return;

            $user->notify(new TicketAtualizado($ticket, $message));
        } catch (\Exception $e) {
            Log::error('NotificationService::ticketAtualizado', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Notifica cliente que seu cadastro foi aprovado.
     */
    public function clienteAprovado(User $user): void
    {
        try {
            $user->notify(new ClienteAprovado());
        } catch (\Exception $e) {
            Log::error('NotificationService::clienteAprovado', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Notifica cliente que seu documento foi verificado ou rejeitado.
     */
    public function documentoVerificado(Document $document): void
    {
        try {
            $user = $document->user;
            if (! $user) return;

            $user->notify(new DocumentoVerificado($document));
        } catch (\Exception $e) {
            Log::error('NotificationService::documentoVerificado', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Notifica todos os admins sobre um novo ticket aberto.
     */
    public function novoTicketParaAdmins(Ticket $ticket): void
    {
        try {
            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                $admin->notify(
                    \Illuminate\Support\Facades\Notification::getDefaultDriver() === 'database'
                        ? new class($ticket) extends \Illuminate\Notifications\Notification {
                            public function __construct(private Ticket $ticket) {}
                            public function via($n) { return ['database']; }
                            public function toDatabase($n): array {
                                return [
                                    'tipo'     => 'novo_ticket',
                                    'icone'    => '🎫',
                                    'titulo'   => "Novo chamado: {$this->ticket->numero}",
                                    'mensagem' => $this->ticket->titulo,
                                    'url'      => '/admin/tickets',
                                    'dados'    => ['ticket_id' => $this->ticket->id],
                                ];
                            }
                        }
                        : null
                );
            }
        } catch (\Exception $e) {
            Log::warning('NotificationService::novoTicketParaAdmins', ['error' => $e->getMessage()]);
        }
    }
}
