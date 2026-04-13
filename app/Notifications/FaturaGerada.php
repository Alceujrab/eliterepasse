<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\Financial;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FaturaGerada extends Notification
{

    public function __construct(
        public readonly Financial $financial
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $numero     = $this->financial->numero;
        $valor      = 'R$ ' . number_format((float) $this->financial->valor, 2, ',', '.');
        $vencimento = $this->financial->data_vencimento?->format('d/m/Y') ?? '—';
        $nome       = $notifiable->razao_social ?? $notifiable->name;
        $formaPgto  = Financial::formasPagamento()[$this->financial->forma_pagamento] ?? $this->financial->forma_pagamento;

        $template = EmailTemplate::findBySlug('fatura_gerada');
        if ($template) {
            return $template->toMailMessage([
                'nome' => $nome,
                'numero' => $numero,
                'valor' => $valor,
                'vencimento' => $vencimento,
                'forma_pagamento' => $formaPgto,
                'portal_url' => url('/'),
            ]);
        }

        return (new MailMessage)
            ->subject("💰 Fatura {$numero} Gerada — Elite Repasse")
            ->greeting("Olá, {$nome}!")
            ->line("Uma nova fatura foi gerada para o seu pedido.")
            ->line("**Fatura:** {$numero}")
            ->line("**Valor:** {$valor}")
            ->line("**Vencimento:** {$vencimento}")
            ->line("**Forma de Pagamento:** {$formaPgto}")
            ->action('Ver Financeiro', url('/financeiro'))
            ->line('Efetue o pagamento até a data de vencimento para evitar atrasos.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'      => 'fatura_gerada',
            'icone'     => '💰',
            'titulo'    => "Fatura {$this->financial->numero} gerada!",
            'mensagem'  => 'Nova fatura disponível. Valor: R$ ' . number_format((float) $this->financial->valor, 2, ',', '.'),
            'url'       => '/financeiro',
            'dados'     => [
                'financial_id' => $this->financial->id,
                'numero'       => $this->financial->numero,
            ],
        ];
    }
}
