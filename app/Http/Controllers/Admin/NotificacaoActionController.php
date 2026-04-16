<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;

class NotificacaoActionController extends Controller
{
    public function enviarManual(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'mensagem' => ['required', 'string', 'max:2000'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        $user->notify(new \App\Notifications\ManualNotification(
            $validated['titulo'],
            $validated['mensagem'],
        ));

        return back()->with('admin_success', "Notificação enviada para {$user->name}.");
    }

    public function enviarBroadcast(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'mensagem' => ['required', 'string', 'max:2000'],
        ]);

        $users = User::where('role', 'client')->where('status', 'ativo')->get();

        if ($users->isEmpty()) {
            return back()->with('admin_warning', 'Nenhum cliente ativo para receber broadcast.');
        }

        Notification::send($users, new \App\Notifications\BroadcastNotification(
            $validated['titulo'],
            $validated['mensagem'],
        ));

        return back()->with('admin_success', "Broadcast enviado para {$users->count()} clientes ativos.");
    }

    public function marcarTodasLidas(): RedirectResponse
    {
        $updated = DatabaseNotification::whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('admin_success', "{$updated} notificações marcadas como lidas.");
    }
}
