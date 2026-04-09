<?php

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContratoAssinado extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Contract $contract
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $nome = $notifiable->razao_social ?? $notifiable->name;
        $veiculo = implode(' ', array_filter([
            $this->contract->dados_veiculo['brand'] ?? '',
            $this->contract->dados_veiculo['model'] ?? '',
            $this->contract->dados_veiculo['model_year'] ?? '',
        ]));
        $valor = 'R$ ' . number_format((float) $this->contract->valor_contrato, 2, ',', '.');
        $numero = $this->contract->numero;

        return (new MailMessage)
            ->subject("✅ Contrato {$numero} Assinado")
            ->greeting("Olá, {$nome}!")
            ->line("O contrato **{$numero}** foi assinado com sucesso!")
            ->line("**Veículo:** {$veiculo}")
            ->line("**Valor:** {$valor}")
            ->line("**Assinado em:** " . $this->contract->assinado_em?->format('d/m/Y H:i'))
            ->line("**Local:** " . ($this->contract->endereco_assinatura ?? 'N/A'))
            ->action('Ver Meus Documentos', url('/meus-documentos'))
            ->line('O contrato assinado está disponível na área de documentos do portal.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'     => 'contrato_assinado',
            'icone'    => '✅',
            'titulo'   => "Contrato {$this->contract->numero} assinado!",
            'mensagem' => 'Seu contrato foi assinado. Acesse a área de documentos para download.',
            'url'      => '/meus-documentos',
            'dados'    => ['contract_id' => $this->contract->id],
        ];
    }
}
