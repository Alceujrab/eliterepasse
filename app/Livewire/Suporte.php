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
            'user_id' => auth()->id(),
            'message' => $this->newMessage,
        ]);

        $this->newMessage = '';
    }

    public function render()
    {
        $company = auth()->user()->companies()->first();
        
        $tickets = $company ? Ticket::with(['order', 'messages' => function ($query) {
            $query->latest();
        }])->where('company_id', $company->id)->latest()->get() : collect();

        $activeTicket = $this->activeTicketId ? Ticket::with(['messages.user', 'order'])->find($this->activeTicketId) : null;

        return view('livewire.suporte', [
            'tickets' => $tickets,
            'activeTicket' => $activeTicket,
            'company' => $company
        ]);
    }
}
