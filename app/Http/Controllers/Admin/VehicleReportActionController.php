<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleReport;
use App\Models\VehicleReportItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleReportActionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'tipo' => ['required', 'in:vistoria_entrada,cautelar,revisao,avaria'],
            'conclusao' => ['nullable', 'string', 'max:2000'],
            'recomendacoes' => ['nullable', 'string', 'max:2000'],
            'nota_geral' => ['nullable', 'integer', 'min:0', 'max:10'],
        ]);

        $lastNumero = VehicleReport::max('id') ?? 0;
        $numero = 'LV-' . str_pad($lastNumero + 1, 6, '0', STR_PAD_LEFT);

        $report = VehicleReport::create([
            'vehicle_id' => $validated['vehicle_id'],
            'criado_por' => Auth::id(),
            'numero' => $numero,
            'tipo' => $validated['tipo'],
            'status' => 'rascunho',
            'conclusao' => $validated['conclusao'] ?? null,
            'recomendacoes' => $validated['recomendacoes'] ?? null,
            'nota_geral' => $validated['nota_geral'] ?? null,
        ]);

        $checklist = VehicleReport::checklistPadrao();
        $ordem = 0;

        foreach ($checklist as $grupo => $items) {
            foreach ($items as $item) {
                VehicleReportItem::create([
                    'vehicle_report_id' => $report->id,
                    'grupo' => $grupo,
                    'item' => $item,
                    'resultado' => 'nao_avaliado',
                    'ordem' => $ordem++,
                ]);
            }
        }

        return redirect()->route('admin.v2.vehicle-reports.show', $report)
            ->with('admin_success', "Laudo {$numero} criado com checklist padrão. Preencha os itens.");
    }

    public function updateItems(Request $request, VehicleReport $vehicleReport): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:vehicle_report_items,id'],
            'items.*.resultado' => ['required', 'in:ok,atencao,reprovado,nao_avaliado'],
            'items.*.observacao' => ['nullable', 'string', 'max:500'],
            'conclusao' => ['nullable', 'string', 'max:2000'],
            'recomendacoes' => ['nullable', 'string', 'max:2000'],
            'nota_geral' => ['nullable', 'integer', 'min:0', 'max:10'],
        ]);

        foreach ($validated['items'] as $itemData) {
            VehicleReportItem::where('id', $itemData['id'])
                ->where('vehicle_report_id', $vehicleReport->id)
                ->update([
                    'resultado' => $itemData['resultado'],
                    'observacao' => $itemData['observacao'] ?? null,
                ]);
        }

        $vehicleReport->update([
            'conclusao' => $validated['conclusao'] ?? $vehicleReport->conclusao,
            'recomendacoes' => $validated['recomendacoes'] ?? $vehicleReport->recomendacoes,
            'nota_geral' => $validated['nota_geral'] ?? $vehicleReport->nota_geral,
        ]);

        return back()->with('admin_success', 'Checklist atualizado com sucesso.');
    }

    public function enviarRevisao(VehicleReport $vehicleReport): RedirectResponse
    {
        if ($vehicleReport->status !== 'rascunho') {
            return back()->with('admin_warning', 'Apenas laudos em rascunho podem ser enviados para revisão.');
        }

        $vehicleReport->update(['status' => 'em_revisao']);

        return back()->with('admin_success', "Laudo {$vehicleReport->numero} enviado para revisão.");
    }

    public function aprovar(VehicleReport $vehicleReport): RedirectResponse
    {
        if (! in_array($vehicleReport->status, ['em_revisao', 'rascunho'], true)) {
            return back()->with('admin_warning', 'Apenas laudos em revisão ou rascunho podem ser aprovados.');
        }

        $vehicleReport->update([
            'status' => 'aprovado',
            'aprovado_por' => Auth::id(),
            'aprovado_em' => now(),
        ]);

        return back()->with('admin_success', "Laudo {$vehicleReport->numero} aprovado.");
    }

    public function reprovar(Request $request, VehicleReport $vehicleReport): RedirectResponse
    {
        if (! in_array($vehicleReport->status, ['em_revisao', 'rascunho'], true)) {
            return back()->with('admin_warning', 'Apenas laudos em revisão ou rascunho podem ser reprovados.');
        }

        $vehicleReport->update([
            'status' => 'reprovado',
            'aprovado_por' => Auth::id(),
            'aprovado_em' => now(),
        ]);

        return back()->with('admin_warning', "Laudo {$vehicleReport->numero} reprovado.");
    }
}
