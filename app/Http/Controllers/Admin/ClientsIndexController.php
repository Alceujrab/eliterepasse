<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientsIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $status = $request->string('status')->toString();
        $approval = $request->string('approval')->toString();
        $search = trim($request->string('q')->toString());

        $queryFactory = function () use ($status, $approval, $search): Builder {
            return User::query()
                ->where('is_admin', false)
                ->with('approvedBy:id,name')
                ->withCount(['orders', 'documents', 'tickets'])
                ->withCount([
                    'tickets as open_tickets_count' => fn (Builder $query) => $query->whereNotIn('status', ['resolvido', 'fechado']),
                    'documents as pending_documents_count' => fn (Builder $query) => $query->where('status', 'pendente'),
                ])
                ->when($status !== '', fn (Builder $query) => $query->where('status', $status))
                ->when($approval !== '', function (Builder $query) use ($approval) {
                    return match ($approval) {
                        'approved' => $query->whereNotNull('aprovado_em'),
                        'waiting' => $query->whereNull('aprovado_em'),
                        default => $query,
                    };
                })
                ->when($search !== '', function (Builder $query) use ($search) {
                    $query->where(function (Builder $subQuery) use ($search) {
                        $subQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('cpf', 'like', "%{$search}%")
                            ->orWhere('razao_social', 'like', "%{$search}%")
                            ->orWhere('nome_fantasia', 'like', "%{$search}%")
                            ->orWhere('cnpj', 'like', "%{$search}%")
                            ->orWhere('cidade', 'like', "%{$search}%");
                    });
                });
        };

        $clients = $queryFactory()
            ->orderByRaw("FIELD(status, 'pendente', 'ativo', 'bloqueado')")
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $baseClientQuery = User::query()->where('is_admin', false);

        $summary = [
            'filteredTotal' => $clients->total(),
            'pending' => $queryFactory()->where('status', 'pendente')->count(),
            'active' => $queryFactory()->where('status', 'ativo')->count(),
            'blocked' => $queryFactory()->where('status', 'bloqueado')->count(),
            'approvedToday' => $baseClientQuery->clone()->whereDate('aprovado_em', today())->count(),
            'withOpenTickets' => $baseClientQuery->clone()->whereHas('tickets', fn (Builder $query) => $query->whereNotIn('status', ['resolvido', 'fechado']))->count(),
        ];

        return view('admin.clients.index', [
            'clients' => $clients,
            'status' => $status,
            'approval' => $approval,
            'search' => $search,
            'summary' => $summary,
            'globalTotalClients' => User::query()->where('is_admin', false)->count(),
            'hasActiveFilters' => $status !== '' || $approval !== '' || $search !== '',
            'statusOptions' => [
                'pendente' => '⏳ Pendente',
                'ativo' => '✅ Ativo',
                'bloqueado' => '🚫 Bloqueado',
            ],
            'approvalOptions' => [
                'waiting' => 'Sem aprovacao',
                'approved' => 'Ja aprovados',
            ],
            'globalOpenTickets' => Ticket::query()->whereHas('user', fn (Builder $query) => $query->where('is_admin', false))->whereNotIn('status', ['resolvido', 'fechado'])->count(),
            'globalPendingDocuments' => Document::query()->whereHas('user', fn (Builder $query) => $query->where('is_admin', false))->where('status', 'pendente')->count(),
            'globalOrders' => Order::query()->whereHas('user', fn (Builder $query) => $query->where('is_admin', false))->count(),
        ]);
    }
}