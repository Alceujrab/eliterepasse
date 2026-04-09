<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NovoPedidoAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $cliente = $this->order->user;
        $vehicle = $this->order->vehicle;
        $numero = $this->order->numero;
        $nome = $cliente?->razao_social ?? $cliente?->nome_fantasia ?? $cliente?->name ?? 'Cliente';
        $veiculo = $vehicle ? "{$vehicle->brand} {$vehicle->model} {$vehicle->model_year}" : 'Veículo';
        $valor = 'R$ ' . number_format((float) $this->order->valor_compra, 2, ',', '.');

        return (new MailMessage)
            ->subject("🛒 Novo Pedido {$numero} — {$nome}")
            ->greeting("Olá, {$notifiable->name}!")
            ->line("Um novo pedido de compra foi registrado no portal.")
            ->line("**Pedido:** {$numero}")
            ->line("**Cliente:** {$nome}")
            ->line("**Veículo:** {$veiculo}")
            ->line("**Valor:** {$valor}")
            ->action('Ver Pedido no Admin', url('/admin/orders'))
            ->line('Acesse o painel para confirmar ou processar o pedido.');
    }

    public function toDatabase(object $notifiable): array
    {
        $numero = $this->order->numero;
        $nome = $this->order->user?->razao_social ?? $this->order->user?->name ?? 'Cliente';

        return [
            'tipo'     => 'novo_pedido',
            'icone'    => '🛒',
            'titulo'   => "Novo pedido {$numero}",
            'mensagem' => "Pedido de {$nome} aguarda confirmação.",
            'url'      => '/admin/orders',
            'dados'    => ['order_id' => $this->order->id],
        ];
    }
}
