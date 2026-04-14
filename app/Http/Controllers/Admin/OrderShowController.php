<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Financial;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderShipment;
use Illuminate\View\View;

class OrderShowController extends Controller
{
    public function __invoke(Order $order): View
    {
        $order->load([
            'user',
            'vehicle',
            'paymentMethod',
            'financial',
            'contract.assinaturaComprador',
            'histories.user',
            'shipments.user',
        ]);

        $summary = [
            'shipmentsTotal' => $order->shipments->count(),
            'shipmentsAvailable' => $order->shipments->where('status', 'disponivel')->count(),
            'shipmentsDispatched' => $order->shipments->where('status', 'despachado')->count(),
            'shipmentsDelivered' => $order->shipments->where('status', 'entregue')->count(),
            'historyEntries' => $order->histories->count(),
        ];

        return view('admin.orders.show', [
            'order' => $order,
            'summary' => $summary,
            'statusOptions' => Order::statusLabels(),
            'shipmentTypeOptions' => OrderShipment::tipoDocumentoLabels(),
            'shipmentMethodOptions' => OrderShipment::metodoEnvioLabels(),
            'shipmentStatusOptions' => OrderShipment::statusLabels(),
            'historyActionLabels' => OrderHistory::acaoLabels(),
            'financialStatusLabels' => Financial::statusLabels(),
        ]);
    }
}