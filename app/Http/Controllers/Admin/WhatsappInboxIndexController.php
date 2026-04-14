<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsappInboxIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());
        $selectedTicketId = $request->integer('ticket');

        $queryFactory = function () use ($status, $search) {
            return Ticket::query()
                ->where('type', 'whatsapp')
                ->with(['user', 'messages.user'])
                ->when($status !== '' && $status !== 'todos', fn ($query) => $query->where('status', $status))
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery
                            ->where('numero', 'like', "%{$search}%")
                            ->orWhere('titulo', 'like', "%{$search}%")
                            ->orWhereHas('user', fn ($userQuery) => $userQuery
                                ->where('razao_social', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                            )
                            ->orWhereHas('messages', fn ($messageQuery) => $messageQuery
                                ->where('mensagem', 'like', "%{$search}%")
                            );
                    });
                });
        };

        $tickets = $queryFactory()
            ->orderByRaw("FIELD(status, 'aberto', 'em_atendimento', 'aguardando_cliente', 'resolvido', 'fechado')")
            ->latest()
            ->paginate(16)
            ->withQueryString();

        $selectedTicket = $selectedTicketId
            ? Ticket::query()->where('type', 'whatsapp')->with(['user', 'messages.user'])->find($selectedTicketId)
            : $tickets->getCollection()->first();

        $summary = [
            'filteredTotal' => $tickets->total(),
            'open' => $queryFactory()->where('status', 'aberto')->count(),
            'waitingCustomer' => $queryFactory()->where('status', 'aguardando_cliente')->count(),
            'inProgress' => $queryFactory()->where('status', 'em_atendimento')->count(),
            'resolved' => $queryFactory()->where('status', 'resolvido')->count(),
        ];

        return view('admin.whatsapp-inbox.index', [
            'tickets' => $tickets,
            'selectedTicket' => $selectedTicket,
            'status' => $status === '' ? 'aberto' : $status,
            'search' => $search,
            'summary' => $summary,
            'globalTotalTickets' => Ticket::query()->where('type', 'whatsapp')->count(),
            'hasActiveFilters' => $search !== '' || ($status !== '' && $status !== 'aberto'),
            'statusOptions' => [
                'aberto' => 'Abertos',
                'em_atendimento' => 'Em atendimento',
                'aguardando_cliente' => 'Aguardando cliente',
                'resolvido' => 'Resolvidos',
                'fechado' => 'Fechados',
                'todos' => 'Todos',
            ],
        ]);
    }
}