<?php

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContratoParaAssinar extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Contract $contract,
        public readonly string  $linkAssinatura
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $veiculo = implode(' ', array_filter([
            $this->contract->dados_veiculo['brand'] ?? '',
            $this->contract->dados_veiculo['model'] ?? '',
            $this->contract->dados_veiculo['model_year'] ?? '',
        ]));

        return (new MailMessage)
            ->subject("✍️ Contrato {$this->contract->numero} — Assinatura Necessária")
            ->greeting("Olá, {$notifiable->razao_social ?? $notifiable->name}!")
            ->line("Seu contrato de compra e venda **{$this->contract->numero}** está pronto para assinatura.")
            ->line("**Veículo:** {$veiculo}")
            ->line("**Valor:** R$ " . number_format((float) $this->contract->valor_contrato, 2, ',', '.'))
            ->action('Assinar Contrato Agora', $this->linkAssinatura)
            ->line('⚠️ Este link expira em **72 horas**. Assine o quanto antes.')
            ->line('A assinatura registrará sua localização GPS como prova de autenticidade.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'     => 'contrato_para_assinar',
            'icone'    => '✍️',
            'titulo'   => "Contrato {$this->contract->numero} aguarda assinatura",
            'mensagem' => 'Clique para assinar seu contrato de compra e venda.',
            'url'      => $this->linkAssinatura,
            'dados'    => ['contract_id' => $this->contract->id],
        ];
    }
}
