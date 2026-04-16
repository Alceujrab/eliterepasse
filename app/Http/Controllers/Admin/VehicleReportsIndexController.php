<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleReportsIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $tipo = $request->string('tipo')->toString();
        $status = $request->string('status')->toString();

        $reports = VehicleReport::with(['vehicle', 'criadoPor'])
            ->when($search !== '', fn ($q) => $q->where('numero', 'like', "%{$search}%")
                ->orWhereHas('vehicle', fn ($v) => $v->where('model', 'like', "%{$search}%")->orWhere('plate', 'like', "%{$search}%"))
            )
            ->when($tipo !== '', fn ($q) => $q->where('tipo', $tipo))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => VehicleReport::count(),
            'rascunho' => VehicleReport::where('status', 'rascunho')->count(),
            'em_revisao' => VehicleReport::where('status', 'em_revisao')->count(),
            'aprovado' => VehicleReport::where('status', 'aprovado')->count(),
            'reprovado' => VehicleReport::where('status', 'reprovado')->count(),
        ];

        return view('admin.vehicle-reports.index', [
            'reports' => $reports,
            'search' => $search,
            'tipo' => $tipo,
            'status' => $status,
            'summary' => $summary,
            'hasActiveFilters' => $search !== '' || $tipo !== '' || $status !== '',
            'tipoLabels' => VehicleReport::tipoLabels(),
            'statusLabels' => VehicleReport::statusLabels(),
        ]);
    }
}
