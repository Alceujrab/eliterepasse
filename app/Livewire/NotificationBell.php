<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $open = false;

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function marcarLida(string $id): void
    {
        auth()->user()?->notifications()->where('id', $id)->update(['read_at' => now()]);
    }

    public function marcarTodasLidas(): void
    {
        auth()->user()?->unreadNotifications()->update(['read_at' => now()]);
    }

    public function render()
    {
        $notificacoes  = auth()->user()?->notifications()->latest()->limit(20)->get() ?? collect();
        $naoLidas      = $notificacoes->whereNull('read_at')->count();

        return view('livewire.notification-bell', compact('notificacoes', 'naoLidas'));
    }
}
