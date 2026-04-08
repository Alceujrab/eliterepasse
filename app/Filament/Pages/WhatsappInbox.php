<?php

namespace App\Filament\Pages;

use App\Models\EvolutionInstance;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Services\EvolutionService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class WhatsappInbox extends Page
{
    protected string $view = 'filament.pages.whatsapp-inbox';

    protected static string|\UnitEnum|null $navigationGroup = 'Comunicação';

    protected static ?string $navigationLabel = 'WhatsApp — Inbox';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static ?string $title = 'Caixa de Entrada — WhatsApp';

    protected static ?int $navigationSort = 3;

    // ─── Ticket aberto na conversa ────────────────────────────────────
    public ?int    $ticketAbertoId  = null;
    public string  $respostaTexto   = '';
    public bool    $isInternal      = false;
    public string  $filtroStatus    = 'aberto';

    public static function getNavigationBadge(): ?string
    {
        $n = Ticket::where('type', 'whatsapp')
            ->where('status', 'aberto')
            ->count();
        return $n > 0 ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    // ─── Listar tickets WhatsApp ──────────────────────────────────────

    public function getTicketsProperty()
    {
        return Ticket::where('type', 'whatsapp')
            ->when($this->filtroStatus !== 'todos', fn ($q) => $q->where('status', $this->filtroStatus))
            ->with(['user', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->latest()
            ->get();
    }

    public function getTicketAbertoProperty(): ?Ticket
    {
        if (! $this->ticketAbertoId) return null;
        return Ticket::with(['user', 'messages.user'])->find($this->ticketAbertoId);
    }

    public function abrirTicket(int $id): void
    {
        $this->ticketAbertoId = $id;
        $this->respostaTexto  = '';
    }

    // ─── Responder via WhatsApp ───────────────────────────────────────

    public function responder(): void
    {
        $this->validate(['respostaTexto' => 'required|min:3']);

        $ticket = Ticket::findOrFail($this->ticketAbertoId);

        // Salva a mensagem na thread
        TicketMessage::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => auth()->id(),
            'mensagem'    => $this->respostaTexto,
            'is_internal' => $this->isInternal,
            'is_admin'    => true,
        ]);

        // Envia via WhatsApp se não for nota interna e o cliente tiver telefone
        if (! $this->isInternal) {
            $phone = $ticket->user?->phone;

            if ($phone) {
                $inst = EvolutionInstance::getPadrao();
                if ($inst) {
                    $nomeLojista = $ticket->user->razao_social ?? $ticket->user->name;
                    $inst->sendText($phone, "🔔 *Elite Repasse — Suporte*\n\nOlá, *{$nomeLojista}*!\n\n{$this->respostaTexto}");
                }
            }
        }

        // Atualiza status do ticket
        if ($ticket->status === 'aberto') {
            $ticket->update(['status' => 'aguardando_cliente']);
        }

        Notification::make()->title('Resposta enviada!')->success()->send();
        $this->respostaTexto = '';
        $this->isInternal    = false;
    }

    // ─── Ações de Ticket ─────────────────────────────────────────────

    public function resolverTicket(int $id): void
    {
        Ticket::findOrFail($id)->update([
            'status'      => 'resolvido',
            'resolvido_em' => now(),
        ]);
        Notification::make()->title('Ticket marcado como resolvido.')->success()->send();
    }

    public function reabrirTicket(int $id): void
    {
        Ticket::findOrFail($id)->update(['status' => 'aberto']);
        Notification::make()->title('Ticket reaberto.')->warning()->send();
    }

    public function fecharTicket(int $id): void
    {
        Ticket::findOrFail($id)->update([
            'status'    => 'fechado',
            'fechado_em' => now(),
        ]);
        if ($this->ticketAbertoId === $id) {
            $this->ticketAbertoId = null;
        }
        Notification::make()->title('Ticket fechado.')->success()->send();
    }
}
