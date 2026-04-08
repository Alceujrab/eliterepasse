<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MinhasNotificacoes extends Component
{
    public string $filtro = 'todas'; // todas | nao_lidas

    public function marcarLida(string $id): void
    {
        auth()->user()?->notifications()->where('id', $id)->update(['read_at' => now()]);
    }

    public function marcarTodasLidas(): void
    {
        auth()->user()?->unreadNotifications()->update(['read_at' => now()]);
    }

    public function excluir(string $id): void
    {
        auth()->user()?->notifications()->where('id', $id)->delete();
    }

    public function render()
    {
        $query = auth()->user()?->notifications()->latest();

        if ($this->filtro === 'nao_lidas') {
            $query = auth()->user()?->unreadNotifications()->latest();
        }

        $notificacoes = $query?->paginate(20) ?? collect();
        $naoLidas     = auth()->user()?->unreadNotifications()->count() ?? 0;

        return view('livewire.minhas-notificacoes', compact('notificacoes', 'naoLidas'));
    }
}
