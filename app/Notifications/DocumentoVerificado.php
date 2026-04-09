<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentoVerificado extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Document $document
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status   = $this->document->status;
        $titulo   = $status === 'verificado' ? '✅ Documento Verificado' : '❌ Documento Rejeitado';
        $tipo     = Document::tipoLabels()[$this->document->tipo] ?? $this->document->tipo;
        $veiculo  = $this->document->vehicle
            ? "{$this->document->vehicle->brand} {$this->document->vehicle->model}"
            : 'Veículo';

        $nome = $notifiable->razao_social ?? $notifiable->name;

        $mail = (new MailMessage)
            ->subject("{$titulo} — Elite Repasse")
            ->greeting("Olá, {$nome}!")
            ->line("O documento **{$tipo}** do veículo **{$veiculo}** foi {$status}.");

        if ($status === 'rejeitado' && $this->document->motivo_rejeicao) {
            $mail->line("**Motivo:** {$this->document->motivo_rejeicao}");
            $mail->line('Por favor, envie o documento correto pelo portal.');
        }

        return $mail->action('Ver Documentos', url('/'));
    }

    public function toDatabase(object $notifiable): array
    {
        $tipo   = Document::tipoLabels()[$this->document->tipo] ?? $this->document->tipo;
        $status = $this->document->status;
        $icone  = $status === 'verificado' ? '✅' : '❌';

        return [
            'tipo'     => 'documento_' . $status,
            'icone'    => $icone,
            'titulo'   => "Documento {$tipo} {$status}",
            'mensagem' => $status === 'rejeitado' && $this->document->motivo_rejeicao
                ? "Motivo: {$this->document->motivo_rejeicao}"
                : 'Documento analisado pela equipe Elite Repasse.',
            'url'      => '/',
            'dados'    => ['document_id' => $this->document->id],
        ];
    }
}
