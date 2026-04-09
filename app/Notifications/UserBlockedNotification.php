<?php

namespace App\Notifications;

use App\Channels\EvolutionWhatsAppChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserBlockedNotification extends Notification
{

    public function via(mixed $notifiable): array
    {
        return ['mail', EvolutionWhatsAppChannel::class];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $nome = $notifiable->razao_social ?? $notifiable->nome_fantasia ?? $notifiable->name;

        return (new MailMessage)
            ->subject('⚠️ Sua conta foi suspensa — Portal Elite Repasse')
            ->greeting("Olá, {$nome}.")
            ->line('Informamos que seu acesso ao **Portal Elite Repasse** foi **suspenso temporariamente**.')
            ->line('Entre em contato com nossa equipe para mais informações.')
            ->action('Falar com Suporte', url('/suporte'))
            ->salutation('Equipe Elite Repasse');
    }

    public function toWhatsApp(mixed $notifiable): ?string
    {
        $nome = $notifiable->razao_social ?? $notifiable->nome_fantasia ?? $notifiable->name;

        return "⚠️ *Portal Elite Repasse*\n\nOlá, {$nome}.\n\nSeu acesso ao portal foi *suspenso temporariamente*. Entre em contato com nossa equipe para regularizar sua situação.\n\n📞 Suporte: " . url('/suporte');
    }
}
