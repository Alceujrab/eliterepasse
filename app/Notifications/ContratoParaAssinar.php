<?php

namespace App\Notifications;

use App\Models\Contract;
use App\Models\EmailTemplate;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContratoParaAssinar extends Notification
{

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

        $nome = $notifiable->razao_social ?? $notifiable->name;
        $valor = 'R$ ' . number_format((float) $this->contract->valor_contrato, 2, ',', '.');

        $template = EmailTemplate::findBySlug('contrato_para_assinar');
        if ($template) {
            return $template->toMailMessage([
                'nome' => $nome,
                'numero' => $this->contract->numero,
                'veiculo' => $veiculo,
                'valor' => $valor,
                'link_assinatura' => $this->linkAssinatura,
            ]);
        }

        return (new MailMessage)
            ->subject("✍️ Contrato {$this->contract->numero} — Assinatura Necessária")
            ->greeting("Olá, {$nome}!")
            ->line("Seu contrato de compra e venda **{$this->contract->numero}** está pronto para assinatura.")
            ->line("**Veículo:** {$veiculo}")
            ->line("**Valor:** {$valor}")
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
