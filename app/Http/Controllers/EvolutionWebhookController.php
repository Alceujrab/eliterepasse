<?php

namespace App\Http\Controllers;

use App\Models\EvolutionInstance;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Services\EvolutionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Evolution GO — Webhook Handler
 *
 * Registre a URL no Evolution GO:
 *   POST {url_base}/instance/connect com webhookUrl = url('/webhook/evolution')
 *
 * O Evolution GO envia eventos como:
 *   { "event": "messages.upsert", "data": { "key": {...}, "message": {...}, "pushName": "..." } }
 */
class EvolutionWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $event = $request->input('event');
        $data  = $request->input('data', []);

        Log::channel('daily')->info('Evolution GO Webhook', [
            'event' => $event,
            'from'  => $request->ip(),
        ]);

        return match ($event) {
            'messages.upsert'  => $this->handleMessage($data),
            'connection.update' => $this->handleConnectionUpdate($data),
            default            => response()->json(['ok' => true]),
        };
    }

    // ─── Mensagem Recebida ────────────────────────────────────────────

    private function handleMessage(array $data): \Illuminate\Http\JsonResponse
    {
        try {
            // Ignorar mensagens enviadas pelo próprio bot
            $fromMe = $data['key']['fromMe'] ?? false;
            if ($fromMe) {
                return response()->json(['ok' => true, 'skipped' => 'fromMe']);
            }

            // Ignorar mensagens de grupos
            $remoteJid = $data['key']['remoteJid'] ?? '';
            if (str_contains($remoteJid, '@g.us')) {
                return response()->json(['ok' => true, 'skipped' => 'group']);
            }

            // Extrair número e nome
            $phone      = preg_replace('/\D/', '', $remoteJid);
            $pushName   = $data['pushName'] ?? null;
            $messageId  = $data['key']['id'] ?? null;

            // Extrair texto da mensagem
            $texto = $this->extractMessageText($data['message'] ?? []);
            if (! $texto) {
                return response()->json(['ok' => true, 'skipped' => 'no_text']);
            }

            // Localizar o usuário pelo telefone
            $user = User::where('phone', 'LIKE', '%' . substr($phone, -10))->first();

            // Criar ou reabrir ticket WhatsApp do usuário
            $ticket = $this->encontrarOuCriarTicket($user, $phone, $pushName, $texto);

            // Adicionar mensagem ao ticket
            TicketMessage::create([
                'ticket_id'          => $ticket->id,
                'user_id'            => $user?->id,
                'mensagem'           => $texto,
                'is_internal'        => false,
                'is_admin'           => false,
                'whatsapp_message_id' => $messageId,
            ]);

            // Puxar ticket para "aguardando atendimento" se estava fechado
            if (in_array($ticket->status, ['resolvido', 'fechado'])) {
                $ticket->update(['status' => 'aberto']);
            }

            Log::info("WhatsApp → Ticket {$ticket->numero}: {$texto}");

        } catch (\Exception $e) {
            Log::error('EvolutionWebhook::handleMessage error', ['error' => $e->getMessage()]);
        }

        return response()->json(['ok' => true]);
    }

    // ─── Atualização de Conexão ───────────────────────────────────────

    private function handleConnectionUpdate(array $data): \Illuminate\Http\JsonResponse
    {
        $state = $data['state'] ?? null;

        if ($state) {
            // Atualizar status da instância padrão
            $inst = EvolutionInstance::getPadrao();
            if ($inst) {
                $inst->update([
                    'status_conexao' => $state,
                    'verificado_em'  => now(),
                ]);
                Log::info("Evolution GO connection state updated: {$state}");
            }
        }

        return response()->json(['ok' => true]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────

    /**
     * Encontra ticket WhatsApp aberto ou cria um novo.
     * Agrupa por telefone: 1 ticket por conversa ativa.
     */
    private function encontrarOuCriarTicket(?User $user, string $phone, ?string $pushName, string $primeiroTexto): Ticket
    {
        // Ticket aberto recente do mesmo telefone (últimas 72h)
        $existente = Ticket::where('type', 'whatsapp')
            ->where(function ($q) use ($user, $phone) {
                if ($user) {
                    $q->where('user_id', $user->id);
                } else {
                    // Ticket anônimo: buscar pelo telefone no título
                    $q->where('titulo', 'LIKE', "%{$phone}%");
                }
            })
            ->whereNotIn('status', ['fechado'])
            ->where('created_at', '>=', now()->subHours(72))
            ->latest()
            ->first();

        if ($existente) {
            return $existente;
        }

        // Criar novo ticket
        $nome    = $pushName ?? ($user?->razao_social ?? $user?->name ?? 'Sem nome') . " ({$phone})";
        $titulo  = "💬 WhatsApp: " . mb_strimwidth($primeiroTexto, 0, 80, '...');

        $ticket = Ticket::create([
            'titulo'        => $titulo,
            'prioridade'    => 'media',
            'categoria'     => 'duvida',
            'type'          => 'whatsapp',
            'status'        => 'aberto',
            'user_id'       => $user?->id,
            'prazo_resposta' => now()->addHours(24),
        ]);

        $ticket->update(['numero' => $ticket->gerarNumero()]);

        return $ticket;
    }

    /**
     * Extrai texto de diferentes tipos de mensagem WhatsApp.
     */
    private function extractMessageText(array $message): ?string
    {
        // Texto simples
        if (isset($message['conversation'])) {
            return $message['conversation'];
        }

        // Texto estendido (preview de link, etc.)
        if (isset($message['extendedTextMessage']['text'])) {
            return $message['extendedTextMessage']['text'];
        }

        // Imagem com legenda
        if (isset($message['imageMessage']['caption'])) {
            return '📷 ' . $message['imageMessage']['caption'];
        }

        // Vídeo com legenda
        if (isset($message['videoMessage']['caption'])) {
            return '🎥 ' . $message['videoMessage']['caption'];
        }

        // Áudio
        if (isset($message['audioMessage'])) {
            return '🎤 [Mensagem de voz]';
        }

        // Documento
        if (isset($message['documentMessage'])) {
            $name = $message['documentMessage']['fileName'] ?? 'arquivo';
            return '📎 [Documento: ' . $name . ']';
        }

        // Sticker
        if (isset($message['stickerMessage'])) {
            return '🫡 [Sticker]';
        }

        return null;
    }
}
