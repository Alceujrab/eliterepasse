<?php

namespace App\Livewire;

use Livewire\Component;

use Livewire\Attributes\Layout;
use App\Models\Ticket;
use App\Models\TicketMessage;

#[Layout('layouts.app')]
class Suporte extends Component
{
    public $activeTicketId = null;
    public $newMessage = '';

    public function selectTicket($id)
    {
        $this->activeTicketId = $id;
    }

    public function sendMessage()
    {
        if (empty($this->newMessage) || !$this->activeTicketId) return;

        TicketMessage::create([
            'ticket_id' => $this->activeTicketId,
            'sender_type' => 'lojista',
            'message' => $this->newMessage,
        ]);

        $this->newMessage = '';
    }

    public function render()
    {
        $tickets = Ticket::with(['messages' => function ($query) {
            $query->latest();
        }])->where('user_id', auth()->id())->latest()->get();

        $activeTicket = $this->activeTicketId ? Ticket::with(['messages'])->find($this->activeTicketId) : null;

        return view('livewire.suporte', [
            'tickets' => $tickets,
            'activeTicket' => $activeTicket,
            'company' => auth()->user()->companies()->first()
        ]);
    }
}
