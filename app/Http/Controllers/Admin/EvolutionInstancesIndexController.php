<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvolutionInstance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvolutionInstancesIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $status = $request->string('status')->toString();
        $active = $request->string('active')->toString();

        $queryFactory = function () use ($search, $status, $active) {
            return EvolutionInstance::query()
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery
                            ->where('nome', 'like', "%{$search}%")
                            ->orWhere('instancia', 'like', "%{$search}%")
                            ->orWhere('url_base', 'like', "%{$search}%");
                    });
                })
                ->when($status !== '', fn ($query) => $query->where('status_conexao', $status))
                ->when($active !== '', fn ($query) => $query->where('ativo', $active === '1'));
        };

        $instances = $queryFactory()->orderByDesc('padrao')->orderBy('nome')->paginate(15)->withQueryString();

        $summary = [
            'filteredTotal' => $instances->total(),
            'connected' => $queryFactory()->where('status_conexao', 'open')->count(),
            'connecting' => $queryFactory()->where('status_conexao', 'connecting')->count(),
            'disconnected' => $queryFactory()->where('status_conexao', 'close')->count(),
            'default' => $queryFactory()->where('padrao', true)->count(),
        ];

        return view('admin.whatsapp-instancias.index', [
            'instances' => $instances,
            'search' => $search,
            'status' => $status,
            'active' => $active,
            'summary' => $summary,
            'globalTotalInstances' => EvolutionInstance::count(),
            'hasActiveFilters' => $search !== '' || $status !== '' || $active !== '',
            'statusOptions' => [
                'open' => 'Conectado',
                'connecting' => 'Conectando',
                'close' => 'Desconectado',
            ],
        ]);
    }
}