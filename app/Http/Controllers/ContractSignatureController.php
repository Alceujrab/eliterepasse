<?php

namespace App\Http\Controllers;

use App\Models\ContractSignature;
use App\Services\ContractService;
use Illuminate\Http\Request;

class ContractSignatureController extends Controller
{
    /**
     * Exibe a página de assinatura do contrato.
     */
    public function show(string $token)
    {
        $signature = ContractSignature::where('token_assinatura', $token)
            ->whereNull('assinado_em')
            ->with('contract.vehicle')
            ->firstOrFail();

        $contract = $signature->contract;

        // Contrato já cancelado ou expirado
        if ($contract->status === 'cancelado') {
            abort(410, 'Este contrato foi cancelado.');
        }

        return view('contratos.assinar', compact('signature', 'contract'));
    }

    /**
     * Processar a assinatura enviada.
     */
    public function store(Request $request, string $token)
    {
        $request->validate([
            'assinatura_base64' => ['required', 'string'],
            'lat'               => ['nullable', 'numeric'],
            'lng'               => ['nullable', 'numeric'],
        ]);

        $signature = ContractSignature::where('token_assinatura', $token)
            ->whereNull('assinado_em')
            ->with('contract')
            ->firstOrFail();

        $contract = $signature->contract;

        if ($contract->status === 'cancelado') {
            return back()->withErrors(['error' => 'Este contrato foi cancelado.']);
        }

        app(ContractService::class)->assinarContrato(
            $contract,
            $request->filled('lat') ? (float) $request->lat : null,
            $request->filled('lng') ? (float) $request->lng : null,
            $request->assinatura_base64,
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('contrato.assinado')->with('contrato_numero', $contract->numero);
    }
}
