<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvolutionInstance;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsappInboxActionController extends Controller
{
    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($ticket->type === 'whatsapp', 404);

        $validated = $request->validate([
            'mensagem' => ['required', 'string', 'min:3'],
            'is_internal' => ['nullable', 'boolean'],
            'novo_status' => ['nullable', 'in:' . implode(',', array_keys(Ticket::statusLabels()))],
        ]);

        $isInternal = $request->boolean('is_internal');

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'mensagem' => $validated['mensagem'],
            'is_internal' => $isInternal,
            'is_admin' => true,
        ]);

        $updates = [];

        if (! $isInternal) {
            $phone = $ticket->user?->phone;
            $instance = EvolutionInstance::getPadrao();

            if ($phone && $instance) {
                $clientName = $ticket->user->razao_social ?? $ticket->user->name ?? 'Cliente';
                $sendResult = $instance->sendText($phone, "🔔 *Elite Repasse — Suporte*\n\nOlá, *{$clientName}*!\n\n{$validated['mensagem']}");

                if (! ($sendResult['success'] ?? false)) {
                    return back()->with('admin_warning', 'A mensagem foi salva no ticket, mas o envio no WhatsApp falhou.');
                }
            } else {
                return back()->with('admin_warning', 'A mensagem foi salva no ticket, mas nao ha telefone do cliente ou instancia padrao ativa.');
            }
        }

        if (! empty($validated['novo_status'])) {
            $updates['status'] = $validated['novo_status'];
        } elseif ($ticket->status === 'aberto') {
            $updates['status'] = 'aguardando_cliente';
        }

        if (($updates['status'] ?? null) === 'resolvido') {
            $updates['resolvido_em'] = now();
        }

        if (($updates['status'] ?? null) === 'fechado') {
            $updates['fechado_em'] = now();
        }

        if (! empty($updates)) {
            $ticket->update($updates);
        }

        return redirect()->route('admin.v2.whatsapp-inbox.index', ['ticket' => $ticket->id])
            ->with('admin_success', 'Resposta registrada no WhatsApp Inbox.');
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($ticket->type === 'whatsapp', 404);

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(Ticket::statusLabels()))],
        ]);

        $updates = ['status' => $validated['status']];

        if ($validated['status'] === 'resolvido') {
            $updates['resolvido_em'] = now();
        }

        if ($validated['status'] === 'fechado') {
            $updates['fechado_em'] = now();
        }

        if ($validated['status'] === 'aberto') {
            $updates['resolvido_em'] = null;
            $updates['fechado_em'] = null;
        }

        $ticket->update($updates);

        return redirect()->route('admin.v2.whatsapp-inbox.index', ['ticket' => $ticket->id])
            ->with('admin_success', 'Status da conversa atualizado.');
    }
}