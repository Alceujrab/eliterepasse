<?php

namespace App\Notifications;

use App\Channels\EvolutionWhatsAppChannel;
use App\Models\EmailTemplate;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserApprovedNotification extends Notification
{

    public function via(mixed $notifiable): array
    {
        return ['mail', EvolutionWhatsAppChannel::class];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $nome = $notifiable->razao_social ?? $notifiable->nome_fantasia ?? $notifiable->name;

        $template = EmailTemplate::findBySlug('usuario_aprovado');
        if ($template) {
            return $template->toMailMessage([
                'nome' => $nome,
                'portal_url' => url('/'),
            ]);
        }

        return (new MailMessage)
            ->subject('✅ Sua conta foi aprovada — Portal Elite Repasse')
            ->greeting("Olá, {$nome}!")
            ->line('Temos o prazer em informar que sua conta no **Portal Elite Repasse** foi **aprovada** com sucesso.')
            ->line('Agora você tem acesso completo ao nosso catálogo de veículos disponíveis para repasse.')
            ->action('Acessar o Portal', url('/dashboard'))
            ->line('Se tiver alguma dúvida, entre em contato com nossa equipe.')
            ->salutation('Equipe Elite Repasse 🚗');
    }

    public function toWhatsApp(mixed $notifiable): ?string
    {
        $nome = $notifiable->razao_social ?? $notifiable->nome_fantasia ?? $notifiable->name;
        $url  = url('/dashboard');

        return "✅ *Portal Elite Repasse*\n\nOlá, {$nome}!\n\nSua conta foi *aprovada* com sucesso! Agora você tem acesso ao nosso catálogo exclusivo de veículos para repasse.\n\n👉 Acesse: {$url}\n\nQualquer dúvida, estamos à disposição!";
    }
}
