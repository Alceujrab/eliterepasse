<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Order;
use App\Models\Vehicle;
use App\Models\VehicleReport;
use Illuminate\View\View;

class VehicleShowController extends Controller
{
    public function __invoke(Vehicle $vehicle): View
    {
        $recentOrders = Order::query()
            ->with(['user', 'financial'])
            ->where('vehicle_id', $vehicle->id)
            ->latest()
            ->limit(6)
            ->get();

        $recentDocuments = Document::query()
            ->with(['user', 'verificadoPor'])
            ->where('vehicle_id', $vehicle->id)
            ->latest()
            ->limit(6)
            ->get();

        $recentReports = VehicleReport::query()
            ->with(['criadoPor', 'aprovadoPor'])
            ->where('vehicle_id', $vehicle->id)
            ->latest()
            ->limit(4)
            ->get();

        $summary = [
            'ordersTotal' => Order::query()->where('vehicle_id', $vehicle->id)->count(),
            'documentsTotal' => Document::query()->where('vehicle_id', $vehicle->id)->count(),
            'reportsTotal' => VehicleReport::query()->where('vehicle_id', $vehicle->id)->count(),
            'paidOrders' => Order::query()->where('vehicle_id', $vehicle->id)->where('status', Order::STATUS_PAGO)->count(),
        ];

        return view('admin.vehicles.show', [
            'vehicle' => $vehicle,
            'summary' => $summary,
            'recentOrders' => $recentOrders,
            'recentDocuments' => $recentDocuments,
            'recentReports' => $recentReports,
            'statusOptions' => Vehicle::statusLabels(),
            'orderStatusOptions' => Order::statusLabels(),
            'documentStatusOptions' => Document::statusLabels(),
            'documentTypeOptions' => Document::tipoLabels(),
            'reportStatusOptions' => VehicleReport::statusLabels(),
            'reportTypeOptions' => VehicleReport::tipoLabels(),
        ]);
    }
}