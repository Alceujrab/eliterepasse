<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractsIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());

        $queryFactory = function () use ($status, $search) {
            return Contract::query()
                ->with(['user', 'vehicle', 'order', 'assinaturaComprador'])
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery
                            ->where('numero', 'like', "%{$search}%")
                            ->orWhere('forma_pagamento', 'like', "%{$search}%")
                            ->orWhereHas('user', fn ($userQuery) => $userQuery
                                ->where('razao_social', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('cnpj', 'like', "%{$search}%")
                            )
                            ->orWhereHas('vehicle', fn ($vehicleQuery) => $vehicleQuery
                                ->where('brand', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%")
                                ->orWhere('plate', 'like', "%{$search}%")
                            );
                    });
                });
        };

        $contracts = $queryFactory()
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'filteredTotal' => $contracts->total(),
            'draft' => $queryFactory()->where('status', 'rascunho')->count(),
            'waiting' => $queryFactory()->where('status', 'aguardando')->count(),
            'signed' => $queryFactory()->where('status', 'assinado')->count(),
            'cancelled' => $queryFactory()->where('status', 'cancelado')->count(),
            'grossVolume' => (float) $queryFactory()->sum('valor_contrato'),
        ];

        return view('admin.contracts.index', [
            'contracts' => $contracts,
            'status' => $status,
            'search' => $search,
            'summary' => $summary,
            'globalTotalContracts' => Contract::count(),
            'hasActiveFilters' => $status !== '' || $search !== '',
            'statusOptions' => Contract::statusLabels(),
        ]);
    }
}
