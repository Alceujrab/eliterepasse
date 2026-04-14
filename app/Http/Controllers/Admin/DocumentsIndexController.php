<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentsIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $status = $request->string('status')->toString();
        $type = $request->string('type')->toString();
        $search = trim($request->string('q')->toString());

        $queryFactory = function () use ($status, $type, $search) {
            return Document::query()
                ->with(['user', 'vehicle', 'verificadoPor'])
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($type !== '', fn ($query) => $query->where('tipo', $type))
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery
                            ->where('titulo', 'like', "%{$search}%")
                            ->orWhere('nome_original', 'like', "%{$search}%")
                            ->orWhereHas('user', fn ($userQuery) => $userQuery
                                ->where('razao_social', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
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

        $documents = $queryFactory()
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'filteredTotal' => $documents->total(),
            'pending' => $queryFactory()->where('status', 'pendente')->count(),
            'verified' => $queryFactory()->where('status', 'verificado')->count(),
            'rejected' => $queryFactory()->where('status', 'rejeitado')->count(),
            'expired' => $queryFactory()->whereDate('validade', '<', now()->toDateString())->count(),
            'visibleToClient' => $queryFactory()->where('visivel_cliente', true)->count(),
        ];

        $userOptions = User::query()
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

        return view('admin.documents.index', [
            'documents' => $documents,
            'status' => $status,
            'type' => $type,
            'search' => $search,
            'summary' => $summary,
            'globalTotalDocuments' => Document::count(),
            'hasActiveFilters' => $status !== '' || $type !== '' || $search !== '',
            'statusOptions' => Document::statusLabels(),
            'typeOptions' => Document::tipoLabels(),
            'userOptions' => $userOptions,
            'vehicleOptions' => $vehicleOptions,
        ]);
    }
}