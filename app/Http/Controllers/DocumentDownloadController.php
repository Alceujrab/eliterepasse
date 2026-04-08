<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentDownloadController extends Controller
{
    public function downloadContract(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        // Apenas o dono ou um admin pode baixar
        if ($contract->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Acesso negado ao documento.');
        }

        if ($contract->status !== 'assinado') {
            abort(404, 'O contrato ainda não foi assinado.');
        }

        $fileName = "contratos/{$contract->numero}_" . Str::slug($contract->dados_comprador['razao_social'] ?? $contract->dados_comprador['name']) . ".pdf";

        if (!Storage::disk('local')->exists($fileName)) {
            // Se o arquivo físico não existir, tenta gerar na hora (caso tenha sido assinado antes da feature de PDF)
            $signature = $contract->assinaturaComprador;
            if ($signature) {
                app(\App\Services\ContractService::class)->gerarPdfContrato($contract, $signature);
            } else {
                abort(404, 'Arquivo de contrato não encontrado.');
            }
        }

        return Storage::disk('local')->download($fileName, "Contrato_{$contract->numero}.pdf");
    }
}
