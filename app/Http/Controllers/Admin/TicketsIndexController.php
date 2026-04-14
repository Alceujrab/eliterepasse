<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketsIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $status = $request->string('status')->toString();
        $priority = $request->string('priority')->toString();
        $search = trim($request->string('q')->toString());
        $selectedTicketId = $request->integer('ticket');

        $queryFactory = function () use ($status, $priority, $search) {
            return Ticket::query()
                ->with(['user', 'atribuidoA', 'order', 'vehicle', 'messages.user'])
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($priority !== '', fn ($query) => $query->where('prioridade', $priority))
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery
                            ->where('numero', 'like', "%{$search}%")
                            ->orWhere('titulo', 'like', "%{$search}%")
                            ->orWhereHas('user', fn ($userQuery) => $userQuery
                                ->where('razao_social', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                            )
                            ->orWhereHas('vehicle', fn ($vehicleQuery) => $vehicleQuery
                                ->where('brand', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%")
                                ->orWhere('plate', 'like', "%{$search}%")
                            );
                    });
                });
        };

        $tickets = $queryFactory()
            ->orderByRaw("FIELD(status, 'aberto', 'em_atendimento', 'aguardando_cliente', 'resolvido', 'fechado')")
            ->latest()
            ->paginate(14)
            ->withQueryString();

        $selectedTicket = $selectedTicketId
            ? Ticket::query()->with(['user', 'atribuidoA', 'order', 'vehicle', 'messages.user'])->find($selectedTicketId)
            : $tickets->getCollection()->first();

        $summary = [
            'filteredTotal' => $tickets->total(),
            'open' => $queryFactory()->where('status', 'aberto')->count(),
            'inProgress' => $queryFactory()->where('status', 'em_atendimento')->count(),
            'waitingCustomer' => $queryFactory()->where('status', 'aguardando_cliente')->count(),
            'urgent' => $queryFactory()->where('prioridade', 'urgente')->whereNotIn('status', ['resolvido', 'fechado'])->count(),
            'overdue' => $queryFactory()->where('prazo_resposta', '<', now())->whereNotIn('status', ['resolvido', 'fechado'])->count(),
        ];

        $agentOptions = User::query()
            ->where('is_admin', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        $customerOptions = User::query()
            ->where('is_admin', false)
            ->orderByRaw('COALESCE(razao_social, name)')
            ->get(['id', 'razao_social', 'name', 'email'])
            ->mapWithKeys(fn (User $user) => [
                $user->id => trim(($user->razao_social ?? $user->name ?? 'Usuario') . ' · ' . ($user->email ?? '')),
            ]);

        $vehicleOptions = Vehicle::query()
            ->orderBy('brand')
            ->orderBy('model')
            ->get(['id', 'brand', 'model', 'model_year', 'plate'])
            ->mapWithKeys(fn (Vehicle $vehicle) => [
                $vehicle->id => trim("{$vehicle->brand} {$vehicle->model} {$vehicle->model_year} · {$vehicle->plate}"),
            ]);

        $orderOptions = Order::query()
            ->with('user')
            ->latest()
            ->limit(200)
            ->get()
            ->mapWithKeys(fn (Order $order) => [
                $order->id => trim("{$order->numero} · " . ($order->user?->razao_social ?? $order->user?->name ?? 'Cliente')),
            ]);

        return view('admin.tickets.index', [
            'tickets' => $tickets,
            'selectedTicket' => $selectedTicket,
            'status' => $status,
            'priority' => $priority,
            'search' => $search,
            'summary' => $summary,
            'globalTotalTickets' => Ticket::count(),
            'hasActiveFilters' => $status !== '' || $priority !== '' || $search !== '',
            'statusOptions' => Ticket::statusLabels(),
            'priorityOptions' => [
                'baixa' => 'Baixa',
                'media' => 'Media',
                'alta' => 'Alta',
                'urgente' => 'Urgente',
            ],
            'categoryOptions' => Ticket::categoriaLabels(),
            'agentOptions' => $agentOptions,
            'customerOptions' => $customerOptions,
            'vehicleOptions' => $vehicleOptions,
            'orderOptions' => $orderOptions,
        ]);
    }
}