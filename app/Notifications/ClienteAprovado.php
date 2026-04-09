<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClienteAprovado extends Notification
{

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $nome = $notifiable->razao_social ?? $notifiable->nome_fantasia ?? $notifiable->name;

        return (new MailMessage)
            ->subject('🎉 Cadastro Aprovado — Elite Repasse')
            ->greeting("Boas-vindas, {$nome}!")
            ->line('Seu cadastro no **Portal B2B Elite Repasse** foi aprovado!')
            ->line('Agora você tem acesso completo à nossa vitrine de veículos com descontos exclusivos.')
            ->line('---')
            ->line('**Seus dados de acesso:**')
            ->line("📧 **E-mail:** {$notifiable->email}")
            ->line('🔑 **Senha:** A mesma que você definiu no cadastro')
            ->line('🌐 **Portal:** ' . url('/'))
            ->line('---')
            ->action('Acessar o Portal Agora', url('/'))
            ->line('📞 Dúvidas? Abra um chamado na central de suporte a qualquer momento.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'     => 'cliente_aprovado',
            'icone'    => '🎉',
            'titulo'   => 'Cadastro aprovado!',
            'mensagem' => 'Bem-vindo ao Portal Elite Repasse. Acesse a vitrine agora.',
            'url'      => '/',
            'dados'    => [],
        ];
    }
}
