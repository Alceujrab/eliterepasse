<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Services\ContractService;
use Illuminate\Http\RedirectResponse;

class ContractActionController extends Controller
{
    public function sendToSign(Contract $contract, ContractService $contractService): RedirectResponse
    {
        if (! in_array($contract->status, ['rascunho', 'aguardando'], true)) {
            return back()->with('admin_warning', 'Contrato fora do estado permitido para envio.');
        }

        $success = $contractService->enviarLinkAssinatura($contract);

        if (! $success) {
            return back()->with('admin_warning', 'Falha ao enviar link de assinatura no WhatsApp.');
        }

        return back()->with('admin_success', "Link de assinatura enviado para o contrato {$contract->numero}.");
    }

    public function copyLink(Contract $contract): RedirectResponse
    {
        $token = $contract->assinaturaComprador?->token_assinatura;

        if (! $token) {
            return back()->with('admin_warning', 'Contrato sem token de assinatura disponível.');
        }

        $link = url("/contrato/assinar/{$token}");

        return back()->with('admin_success', "Link de assinatura: {$link}");
    }
}
