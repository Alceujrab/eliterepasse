<?php

namespace App\Notifications;

use App\Models\Financial;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PagamentoConfirmado extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Financial $financial
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $numero = $this->financial->numero;
        $valor  = 'R$ ' . number_format((float) $this->financial->valor, 2, ',', '.');
        $nome   = $notifiable->razao_social ?? $notifiable->name;

        return (new MailMessage)
            ->subject("✅ Pagamento Confirmado — Fatura {$numero} — Elite Repasse")
            ->greeting("Olá, {$nome}!")
            ->line("O pagamento da fatura **{$numero}** foi confirmado com sucesso.")
            ->line("**Valor:** {$valor}")
            ->line("**Data do pagamento:** " . ($this->financial->data_pagamento?->format('d/m/Y') ?? now()->format('d/m/Y')))
            ->action('Ver Financeiro', url('/financeiro'))
            ->line('Obrigado pela confiança! Acompanhe seus pedidos pelo Portal B2B.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'      => 'pagamento_confirmado',
            'icone'     => '💚',
            'titulo'    => "Pagamento {$this->financial->numero} confirmado!",
            'mensagem'  => 'Seu pagamento de R$ ' . number_format((float) $this->financial->valor, 2, ',', '.') . ' foi confirmado.',
            'url'       => '/financeiro',
            'dados'     => [
                'financial_id' => $this->financial->id,
                'numero'       => $this->financial->numero,
            ],
        ];
    }
}
