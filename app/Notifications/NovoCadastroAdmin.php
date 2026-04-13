<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NovoCadastroAdmin extends Notification
{

    public function __construct(
        public readonly User $cliente
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $nome = $this->cliente->razao_social ?? $this->cliente->nome_fantasia ?? $this->cliente->name;
        $cnpj = $this->cliente->cnpj ?? 'Não informado';
        $cidade = $this->cliente->cidade
            ? "{$this->cliente->cidade}/{$this->cliente->estado}"
            : 'Não informado';

        $template = EmailTemplate::findBySlug('novo_cadastro_admin');
        if ($template) {
            return $template->toMailMessage([
                'admin_nome' => $notifiable->name,
                'empresa' => $nome,
                'cnpj' => $cnpj,
                'cidade' => $cidade,
                'email' => $this->cliente->email,
                'whatsapp' => $this->cliente->phone ?? 'Não informado',
                'portal_url' => url('/'),
            ]);
        }

        return (new MailMessage)
            ->subject("🆕 Novo Cadastro — {$nome}")
            ->greeting("Olá, {$notifiable->name}!")
            ->line("Um novo lojista se cadastrou no portal e aguarda aprovação.")
            ->line("**Empresa:** {$nome}")
            ->line("**CNPJ:** {$cnpj}")
            ->line("**Cidade:** {$cidade}")
            ->line("**E-mail:** {$this->cliente->email}")
            ->line("**WhatsApp:** " . ($this->cliente->phone ?? 'Não informado'))
            ->action('Analisar no Admin', url('/admin/clients'))
            ->line('Acesse o painel para aprovar ou bloquear o acesso.');
    }

    public function toDatabase(object $notifiable): array
    {
        $nome = $this->cliente->razao_social ?? $this->cliente->nome_fantasia ?? $this->cliente->name;

        return [
            'tipo'     => 'novo_cadastro',
            'icone'    => '🆕',
            'titulo'   => "Novo cadastro: {$nome}",
            'mensagem' => 'Um novo lojista se cadastrou e aguarda aprovação.',
            'url'      => '/admin/clients',
            'dados'    => ['user_id' => $this->cliente->id],
        ];
    }
}
