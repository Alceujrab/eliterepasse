<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAtualizado extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Ticket        $ticket,
        public readonly TicketMessage $message
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("💬 Resposta no Chamado {$this->ticket->numero} — Elite Repasse")
            ->greeting("Olá, {$notifiable->razao_social ?? $notifiable->name}!")
            ->line("Seu chamado de suporte **{$this->ticket->numero}** recebeu uma nova resposta.")
            ->line("**Assunto:** {$this->ticket->titulo}")
            ->line("**Resposta:** {$this->message->mensagem}")
            ->action('Ver Chamado', url('/suporte'))
            ->line('Acesse o portal para responder ou verificar o status do seu chamado.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'     => 'ticket_atualizado',
            'icone'    => '💬',
            'titulo'   => "Resposta no chamado {$this->ticket->numero}",
            'mensagem' => mb_strimwidth($this->message->mensagem, 0, 80, '...'),
            'url'      => '/suporte',
            'dados'    => ['ticket_id' => $this->ticket->id],
        ];
    }
}
