<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\View\View;

class ContractShowController extends Controller
{
    public function __invoke(Contract $contract): View
    {
        $contract->load([
            'user',
            'vehicle',
            'order.financial',
            'order.paymentMethod',
            'assinaturaComprador',
            'assinaturaVendedor',
            'createdBy',
        ]);

        $summary = [
            'hasBuyerSignature' => (bool) $contract->assinaturaComprador?->assinado_em,
            'hasSellerSignature' => (bool) $contract->assinaturaVendedor?->assinado_em,
            'hasToken' => (bool) $contract->assinaturaComprador?->token_assinatura,
            'wasSent' => (bool) $contract->enviado_em,
        ];

        return view('admin.contracts.show', [
            'contract' => $contract,
            'summary' => $summary,
            'statusOptions' => Contract::statusLabels(),
        ]);
    }
}