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

        $contracts = Contract::query()
            ->with(['user', 'vehicle', 'assinaturaComprador'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('numero', 'like', "%{$search}%")
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
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.contracts.index', [
            'contracts' => $contracts,
            'status' => $status,
            'search' => $search,
            'statusOptions' => Contract::statusLabels(),
        ]);
    }
}
