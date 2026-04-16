<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleReport;
use Illuminate\View\View;

class VehicleReportShowController extends Controller
{
    public function __invoke(VehicleReport $vehicleReport): View
    {
        $vehicleReport->load(['vehicle', 'criadoPor', 'aprovadoPor', 'items' => fn ($q) => $q->orderBy('ordem')]);

        $grupos = $vehicleReport->items->groupBy('grupo');

        return view('admin.vehicle-reports.show', [
            'report' => $vehicleReport,
            'grupos' => $grupos,
            'statusLabels' => VehicleReport::statusLabels(),
            'tipoLabels' => VehicleReport::tipoLabels(),
        ]);
    }
}
