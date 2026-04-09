<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PedidoConfirmado extends Notification implements ShouldQueue
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
        $numero  = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        $veiculo = $this->order->vehicle
            ? "{$this->order->vehicle->brand} {$this->order->vehicle->model} {$this->order->vehicle->model_year}"
            : 'Veículo';
        $valor = 'R$ ' . number_format((float) $this->order->valor_compra, 2, ',', '.');
        $nome = $notifiable->razao_social ?? $notifiable->name;

        return (new MailMessage)
            ->subject("✅ Pedido {$numero} Confirmado — Elite Repasse")
            ->greeting("Olá, {$nome}!")
            ->line("Seu pedido de compra **{$numero}** foi confirmado com sucesso.")
            ->line("**Veículo:** {$veiculo}")
            ->line("**Valor:** {$valor}")
            ->action('Ver Meus Pedidos', url('/meus-pedidos'))
            ->line('Em breve você receberá o contrato de compra e venda para assinatura.');
    }

    public function toDatabase(object $notifiable): array
    {
        $numero = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        return [
            'tipo'      => 'pedido_confirmado',
            'icone'     => '✅',
            'titulo'    => "Pedido {$numero} confirmado!",
            'mensagem'  => 'Seu pedido de compra foi confirmado. Aguarde o contrato.',
            'url'       => '/meus-pedidos',
            'dados'     => ['order_id' => $this->order->id, 'numero' => $numero],
        ];
    }
}
