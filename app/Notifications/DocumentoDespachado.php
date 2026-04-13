<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\OrderShipment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentoDespachado extends Notification
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
        $metodo = OrderShipment::metodoEnvioLabels()[$this->shipment->metodo_envio] ?? $this->shipment->metodo_envio;
        $rastreio = $this->shipment->codigo_rastreio ?? '—';
        $detalhe = $this->shipment->metodo_envio_detalhe;
        $dataDespacho = $this->shipment->despachado_em?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i');

        $template = EmailTemplate::findBySlug('documento_despachado');
        if ($template) {
            return $template->toMailMessage([
                'nome' => $nome,
                'tipo_documento' => $tipo,
                'numero_pedido' => $numeroPedido,
                'metodo_envio' => $metodo,
                'metodo_envio_detalhe' => $detalhe ?? '',
                'codigo_rastreio' => $rastreio,
                'data_despacho' => $dataDespacho,
                'portal_url' => url('/'),
            ]);
        }

        $mail = (new MailMessage)
            ->subject("📦 Documento Despachado — {$tipo} — Elite Repasse")
            ->greeting("Olá, {$nome}!")
            ->line("O documento **{$tipo}** do pedido **{$numeroPedido}** foi despachado!")
            ->line("📦 **Enviado via:** {$metodo}" . ($detalhe ? " — {$detalhe}" : ''))
            ->line("📅 **Data do despacho:** {$dataDespacho}");

        if ($this->shipment->codigo_rastreio) {
            $mail->line("🔍 **Código de rastreio:** {$rastreio}");
        }

        return $mail
            ->action('Acompanhar Pedido', url('/meus-pedidos'))
            ->line('Você será notificado quando o documento for entregue.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $nome = $notifiable->razao_social ?? $notifiable->name;
        $tipo = OrderShipment::tipoDocumentoLabels()[$this->shipment->tipo_documento] ?? $this->shipment->tipo_documento;
        $ordem = $this->shipment->order;
        $numeroPedido = $ordem?->numero ?? '—';
        $metodo = OrderShipment::metodoEnvioLabels()[$this->shipment->metodo_envio] ?? $this->shipment->metodo_envio;
        $rastreio = $this->shipment->codigo_rastreio;
        $detalhe = $this->shipment->metodo_envio_detalhe;
        $dataDespacho = $this->shipment->despachado_em?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i');

        $msg = "📦 *Documento Despachado!*\n\n"
            . "Olá, *{$nome}*!\n\n"
            . "O documento *{$tipo}* do pedido *{$numeroPedido}* foi enviado.\n\n"
            . "📦 *Via:* {$metodo}" . ($detalhe ? " — {$detalhe}" : '') . "\n"
            . "📅 *Data:* {$dataDespacho}\n";

        if ($rastreio) {
            $msg .= "🔍 *Rastreio:* {$rastreio}\n";
        }

        $msg .= "\n👉 Acompanhe: " . url('/meus-pedidos') . "\n\n"
            . "_Elite Repasse — Portal B2B_";

        return $msg;
    }

    public function toDatabase(object $notifiable): array
    {
        $tipo = OrderShipment::tipoDocumentoLabels()[$this->shipment->tipo_documento] ?? $this->shipment->tipo_documento;
        $metodo = OrderShipment::metodoEnvioLabels()[$this->shipment->metodo_envio] ?? $this->shipment->metodo_envio;

        return [
            'tipo'     => 'documento_despachado',
            'icone'    => '📦',
            'titulo'   => "Documento {$tipo} despachado!",
            'mensagem' => "Enviado via {$metodo}." . ($this->shipment->codigo_rastreio ? " Rastreio: {$this->shipment->codigo_rastreio}" : ''),
            'url'      => '/meus-pedidos',
            'dados'    => [
                'shipment_id'     => $this->shipment->id,
                'order_id'        => $this->shipment->order_id,
                'codigo_rastreio' => $this->shipment->codigo_rastreio,
            ],
        ];
    }
}
