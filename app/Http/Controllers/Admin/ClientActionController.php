<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserBlockedNotification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientActionController extends Controller
{
    public function approve(User $client, NotificationService $notificationService): RedirectResponse
    {
        if ($client->is_admin) {
            abort(404);
        }

        if ($client->status === 'ativo') {
            return back()->with('admin_warning', 'Este cliente ja esta aprovado.');
        }

        DB::transaction(function () use ($client, $notificationService) {
            $client->update([
                'status' => 'ativo',
                'aprovado_em' => now(),
                'aprovado_por' => Auth::id(),
            ]);

            $notificationService->clienteAprovado($client->fresh());
        });

        return back()->with('admin_success', 'Cliente aprovado e notificado com sucesso.');
    }

    public function block(User $client): RedirectResponse
    {
        if ($client->is_admin) {
            abort(404);
        }

        if ($client->status === 'bloqueado') {
            return back()->with('admin_warning', 'Este cliente ja esta bloqueado.');
        }

        DB::transaction(function () use ($client) {
            $client->update(['status' => 'bloqueado']);
            $client->notify(new UserBlockedNotification());
        });

        return back()->with('admin_success', 'Acesso do cliente bloqueado.');
    }
}