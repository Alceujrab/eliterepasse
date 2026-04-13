<?php

namespace App\Notifications;

use App\Models\Contract;
use App\Models\EmailTemplate;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContratoAssinadoAdmin extends Notification
{

    public function __construct(
        public readonly Contract $contract
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $nome = $this->contract->dados_comprador['razao_social']
            ?? $this->contract->dados_comprador['name']
            ?? 'Cliente';
        $veiculo = implode(' ', array_filter([
            $this->contract->dados_veiculo['brand'] ?? '',
            $this->contract->dados_veiculo['model'] ?? '',
            $this->contract->dados_veiculo['model_year'] ?? '',
        ]));

        $template = EmailTemplate::findBySlug('contrato_assinado_admin');
        if ($template) {
            return $template->toMailMessage([
                'admin_nome' => $notifiable->name,
                'nome' => $nome,
                'numero' => $this->contract->numero,
                'veiculo' => $veiculo,
                'assinado_em' => $this->contract->assinado_em?->format('d/m/Y H:i'),
                'local' => $this->contract->endereco_assinatura ?? 'N/A',
                'ip' => $this->contract->ip_assinatura ?? 'N/A',
                'portal_url' => url('/'),
            ]);
        }

        return (new MailMessage)
            ->subject("✍️ Contrato {$this->contract->numero} Assinado pelo Cliente")
            ->greeting("Olá, {$notifiable->name}!")
            ->line("O contrato **{$this->contract->numero}** foi assinado pelo cliente **{$nome}**.")
            ->line("**Veículo:** {$veiculo}")
            ->line("**Assinado em:** " . $this->contract->assinado_em?->format('d/m/Y H:i'))
            ->line("**Local:** " . ($this->contract->endereco_assinatura ?? 'N/A'))
            ->line("**IP:** " . ($this->contract->ip_assinatura ?? 'N/A'))
            ->action('Ver Contrato no Admin', url('/admin/contracts'))
            ->line('O contrato assinado está disponível no painel.');
    }

    public function toDatabase(object $notifiable): array
    {
        $nome = $this->contract->dados_comprador['razao_social']
            ?? $this->contract->dados_comprador['name']
            ?? 'Cliente';

        return [
            'tipo'     => 'contrato_assinado_admin',
            'icone'    => '✍️',
            'titulo'   => "Contrato {$this->contract->numero} assinado",
            'mensagem' => "Cliente {$nome} assinou o contrato.",
            'url'      => '/admin/contracts',
            'dados'    => ['contract_id' => $this->contract->id],
        ];
    }
}
