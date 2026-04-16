<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class BroadcastNotification extends Notification
{
    public function __construct(
        public readonly string $titulo,
        public readonly string $mensagem,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => $this->titulo,
            'mensagem' => $this->mensagem,
            'tipo' => 'broadcast',
        ];
    }
}
