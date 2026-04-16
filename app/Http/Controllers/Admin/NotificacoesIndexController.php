<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificacoesIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $tipo = $request->string('tipo')->toString();
        $periodo = $request->string('periodo')->toString();

        $query = DatabaseNotification::query()->latest();

        if ($tipo !== '') {
            $query->where('type', 'like', "%{$tipo}%");
        }

        if ($periodo !== '') {
            $query->when($periodo === 'hoje', fn ($q) => $q->whereDate('created_at', today()))
                ->when($periodo === 'semana', fn ($q) => $q->where('created_at', '>=', now()->subWeek()))
                ->when($periodo === '30dias', fn ($q) => $q->where('created_at', '>=', now()->subDays(30)));
        }

        $notifications = $query->paginate(25)->withQueryString();

        $summary = [
            'hoje' => DatabaseNotification::whereDate('created_at', today())->count(),
            'semana' => DatabaseNotification::where('created_at', '>=', now()->subWeek())->count(),
            'nao_lidas' => DatabaseNotification::whereNull('read_at')->count(),
            'clientes_pendentes' => User::where('role', 'client')->where('status', 'pendente')->count(),
        ];

        $tipoFiltros = [
            '' => 'Todas',
            'pedido' => 'Pedidos',
            'contrato' => 'Contratos',
            'chamado' => 'Chamados',
            'aprovacao' => 'Aprovações',
            'documento' => 'Documentos',
            'ManualNotification' => 'Manual',
            'BroadcastNotification' => 'Broadcast',
        ];

        return view('admin.notificacoes.index', [
            'notifications' => $notifications,
            'tipo' => $tipo,
            'periodo' => $periodo,
            'summary' => $summary,
            'tipoFiltros' => $tipoFiltros,
        ]);
    }
}
