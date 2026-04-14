<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Document;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $metrics = [
            'clientes' => User::query()->where('is_admin', false)->count(),
            'clientesPendentes' => User::query()->where('is_admin', false)->where('status', 'pendente')->count(),
            'veiculosDisponiveis' => Vehicle::query()->where('status', 'available')->count(),
            'pedidosPendentes' => Order::query()->where('status', 'pendente')->count(),
            'contratosAguardando' => Contract::query()->whereIn('status', ['rascunho', 'aguardando'])->count(),
            'ticketsUrgentes' => Ticket::query()->where('prioridade', 'alta')->whereNotIn('status', ['resolvido', 'fechado'])->count(),
            'documentosPendentes' => Document::query()->where('status', 'pendente')->count(),
        ];

        $quickModules = collect(config('admin_panel.modules', []))
            ->filter(fn (array $module) => ($module['show_in_quick_access'] ?? false) === true)
            ->map(fn (array $module, string $key) => [
                'key' => $key,
                'data' => $module,
            ])
            ->values();

        return view('admin.dashboard', [
            'metrics' => $metrics,
            'quickModules' => $quickModules,
        ]);
    }
}
