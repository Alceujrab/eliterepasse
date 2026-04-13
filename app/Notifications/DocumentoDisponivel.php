<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\OrderShipment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentoDisponivel extends Notification
{
    public function __construct(
        public readonly OrderShipment $shipment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', \App\Channels\EvolutionWhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $nome = $notifiable->razao_social ?? $notifiable->name;
        $tipo = OrderShipment::tipoDocumentoLabels()[$this->shipment->tipo_documento] ?? $this->shipment->tipo_documento;
        $ordem = $this->shipment->order;
        $numeroPedido = $ordem?->numero ?? '—';
        $veiculo = $ordem?->vehicle
            ? "{$ordem->vehicle->brand} {$ordem->vehicle->model} {$ordem->vehicle->model_year}"
            : 'Veículo';

        $template = EmailTemplate::findBySlug('documento_disponivel');
        if ($template) {
            return $template->toMailMessage([
                'nome' => $nome,
                'tipo_documento' => $tipo,
                'numero_pedido' => $numeroPedido,
                'veiculo' => $veiculo,
                'portal_url' => url('/'),
            ]);
        }

        return (new MailMessage)
            ->subject("📥 Documento Disponível — {$tipo} — Elite Repasse")
            ->greeting("Olá, {$nome}!")
            ->line("O documento **{$tipo}** referente ao pedido **{$numeroPedido}** já está disponível para download.")
            ->line("🚗 **Veículo:** {$veiculo}")
            ->action('Acessar Meus Pedidos', url('/meus-pedidos'))
            ->line('Acesse o portal para baixar o documento.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $nome = $notifiable->razao_social ?? $notifiable->name;
        $tipo = OrderShipment::tipoDocumentoLabels()[$this->shipment->tipo_documento] ?? $this->shipment->tipo_documento;
        $ordem = $this->shipment->order;
        $numeroPedido = $ordem?->numero ?? '—';

        return "📥 *Documento Disponível!*\n\n"
            . "Olá, *{$nome}*!\n\n"
            . "O documento *{$tipo}* do pedido *{$numeroPedido}* já está disponível para download no portal.\n\n"
            . "👉 Acesse: " . url('/meus-pedidos') . "\n\n"
            . "_Elite Repasse — Portal B2B_";
    }

    public function toDatabase(object $notifiable): array
    {
        $tipo = OrderShipment::tipoDocumentoLabels()[$this->shipment->tipo_documento] ?? $this->shipment->tipo_documento;

        return [
            'tipo'     => 'documento_disponivel',
            'icone'    => '📥',
            'titulo'   => "Documento {$tipo} disponível!",
            'mensagem' => "O documento do pedido {$this->shipment->order?->numero} está pronto para download.",
            'url'      => '/meus-pedidos',
            'dados'    => [
                'shipment_id' => $this->shipment->id,
                'order_id'    => $this->shipment->order_id,
            ],
        ];
    }
}
