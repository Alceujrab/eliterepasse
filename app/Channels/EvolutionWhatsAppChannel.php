<?php

namespace App\Channels;

use App\Models\EvolutionInstance;
use Illuminate\Notifications\Notification;

class EvolutionWhatsAppChannel
{
    /**
     * Envia a notificação via Evolution GO WhatsApp.
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $phone = $notifiable->phone ?? $notifiable->whatsapp ?? null;

        if (! $phone) {
            return; // Sem número, sem envio
        }

        $message = $notification->toWhatsApp($notifiable);

        if (! $message) {
            return;
        }

        $instance = EvolutionInstance::getPadrao();

        if (! $instance) {
            \Log::warning('EvolutionWhatsAppChannel: Nenhuma instância padrão configurada.');
            return;
        }

        // Formata número: remove tudo exceto dígitos, garante DDI 55
        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) <= 11) {
            $phone = '55' . $phone;
        }

        $result = $instance->sendText($phone, $message);

        if (! $result['success']) {
            \Log::error('EvolutionWhatsAppChannel: Falha ao enviar WhatsApp', $result);
        }
    }
}
