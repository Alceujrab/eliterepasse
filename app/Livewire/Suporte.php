<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Suporte extends Component
{
    use WithFileUploads;

    // ─── Estado ──────────────────────────────────────────────────────
    public ?int $activeTicketId = null;
    public bool $showNovoTicket = false;

    // ─── Novo Ticket ─────────────────────────────────────────────────
    #[Rule('required|min:5|max:255')]
    public string $titulo = '';

    #[Rule('required|in:duvida,problema_tecnico,financeiro,contrato,veiculo,outro')]
    public string $categoria = 'duvida';

    #[Rule('required|in:baixa,media,alta,urgente')]
    public string $prioridade = 'media';

    #[Rule('required|min:10')]
    public string $descricao = '';

    public $arquivos = [];

    // ─── Resposta ────────────────────────────────────────────────────
    #[Rule('required|min:2')]
    public string $newMessage = '';

    // ─── Handlers ────────────────────────────────────────────────────
    public function abrirNovoTicket(): void
    {
        $this->showNovoTicket = true;
        $this->activeTicketId = null;
    }

    public function cancelarNovoTicket(): void
    {
        $this->showNovoTicket = false;
        $this->reset(['titulo', 'categoria', 'prioridade', 'descricao', 'arquivos']);
    }

    public function criarTicket(): void
    {
        $this->validate([
            'titulo'    => 'required|min:5|max:255',
            'categoria' => 'required',
            'prioridade'=> 'required',
            'descricao' => 'required|min:10',
        ]);

        $ticket = Ticket::create([
            'user_id'    => auth()->id(),
            'titulo'     => $this->titulo,
            'categoria'  => $this->categoria,
            'prioridade' => $this->prioridade,
            'status'     => 'aberto',
            'prazo_resposta' => now()->addHours(Ticket::slaPorPrioridade($this->prioridade)),
        ]);

        // Gerar número
        $ticket->update(['numero' => $ticket->gerarNumero()]);

        // Mensagem inicial
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'mensagem'  => $this->descricao,
            'is_admin'  => false,
        ]);

        // Upload de arquivos
        foreach ($this->arquivos as $arquivo) {
            $path = $arquivo->store('tickets/' . $ticket->id, 'public');
            TicketAttachment::create([
                'ticket_id'         => $ticket->id,
                'ticket_message_id' => $message->id,
                'user_id'           => auth()->id(),
                'nome_original'     => $arquivo->getClientOriginalName(),
                'path'              => $path,
                'mime_type'         => $arquivo->getMimeType(),
                'tamanho'           => $arquivo->getSize(),
            ]);
        }

        $this->reset(['titulo', 'categoria', 'prioridade', 'descricao', 'arquivos']);
        $this->showNovoTicket = false;
        $this->activeTicketId = $ticket->id;

        $this->dispatch('ticket-criado');
    }

    public function selectTicket(int $id): void
    {
        $this->activeTicketId = $id;
        $this->showNovoTicket = false;
        $this->newMessage     = '';
    }

    public function enviarMensagem(): void
    {
        $this->validate(['newMessage' => 'required|min:2']);

        $ticket = Ticket::where('user_id', auth()->id())
            ->where('id', $this->activeTicketId)->first();

        if (! $ticket) return;

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'mensagem'  => $this->newMessage,
            'is_admin'  => false,
        ]);

        // Se estava aguardando cliente, voltar para em_atendimento
        if ($ticket->status === 'aguardando_cliente') {
            $ticket->update(['status' => 'em_atendimento']);
        }

        $this->newMessage = '';
    }

    public function render()
    {
        $tickets = Ticket::with('messages')
            ->where('user_id', auth()->id())
            ->orderByRaw("FIELD(status, 'aberto', 'em_atendimento', 'aguardando_cliente', 'resolvido', 'fechado')")
            ->latest()
            ->get();

        $activeTicket = $this->activeTicketId
            ? Ticket::with(['messages.user', 'attachments'])->find($this->activeTicketId)
            : null;

        return view('livewire.suporte', compact('tickets', 'activeTicket'));
    }
}
