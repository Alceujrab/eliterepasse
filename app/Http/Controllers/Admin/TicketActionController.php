<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketActionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titulo' => ['required', 'string', 'min:5', 'max:255'],
            'categoria' => ['required', 'in:' . implode(',', array_keys(Ticket::categoriaLabels()))],
            'prioridade' => ['required', 'in:baixa,media,alta,urgente'],
            'user_id' => ['required', 'exists:users,id'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'atribuido_a' => ['nullable', 'exists:users,id'],
            'descricao' => ['required', 'string', 'min:10'],
        ]);

        $ticket = Ticket::create([
            'user_id' => $validated['user_id'],
            'vehicle_id' => $validated['vehicle_id'] ?? null,
            'order_id' => $validated['order_id'] ?? null,
            'titulo' => $validated['titulo'],
            'categoria' => $validated['categoria'],
            'prioridade' => $validated['prioridade'],
            'status' => $validated['atribuido_a'] ? 'em_atendimento' : 'aberto',
            'atribuido_a' => $validated['atribuido_a'] ?? null,
            'atribuido_em' => ($validated['atribuido_a'] ?? null) ? now() : null,
            'prazo_resposta' => now()->addHours(Ticket::slaPorPrioridade($validated['prioridade'])),
            'type' => 'admin_manual',
        ]);

        $ticket->update(['numero' => $ticket->gerarNumero()]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'mensagem' => $validated['descricao'],
            'is_internal' => false,
            'is_admin' => true,
        ]);

        return redirect()->route('admin.v2.tickets.index', ['ticket' => $ticket->id])
            ->with('admin_success', "Ticket {$ticket->numero} criado no novo painel.");
    }

    public function reply(Request $request, Ticket $ticket, NotificationService $notificationService): RedirectResponse
    {
        $validated = $request->validate([
            'mensagem' => ['required', 'string', 'min:2'],
            'novo_status' => ['nullable', 'in:' . implode(',', array_keys(Ticket::statusLabels()))],
            'is_internal' => ['nullable', 'boolean'],
        ]);

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'mensagem' => $validated['mensagem'],
            'is_internal' => $request->boolean('is_internal'),
            'is_admin' => true,
        ]);

        $updates = [];
        $newStatus = $validated['novo_status'] ?? null;

        if ($newStatus) {
            $updates['status'] = $newStatus;
        } elseif ($ticket->status === 'aberto') {
            $updates['status'] = 'em_atendimento';
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

        if (! $request->boolean('is_internal')) {
            $notificationService->ticketAtualizado($ticket->fresh('user'), $message->fresh('user'));
        }

        return back()->with('admin_success', "Resposta enviada no ticket {$ticket->numero}.");
    }

    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'atribuido_a' => ['required', 'exists:users,id'],
        ]);

        $ticket->update([
            'atribuido_a' => $validated['atribuido_a'],
            'atribuido_em' => now(),
            'status' => $ticket->status === 'aberto' ? 'em_atendimento' : $ticket->status,
        ]);

        return back()->with('admin_success', "Ticket {$ticket->numero} atribuido com sucesso.");
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
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
            $updates['prazo_resposta'] = now()->addHours(Ticket::slaPorPrioridade($ticket->prioridade));
        }

        $ticket->update($updates);

        return back()->with('admin_success', "Status do ticket {$ticket->numero} atualizado.");
    }
}