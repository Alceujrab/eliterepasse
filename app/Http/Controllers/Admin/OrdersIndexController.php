<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\Filters\RememberedFilters;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdersIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = (new RememberedFilters($request, 'admin.orders.index'))
            ->remember(['status', 'q', 'per_page']);

        $status = (string) ($filters->get('status', ''));
        $search = trim((string) $filters->get('q', ''));
        $perPage = (int) ($filters->get('per_page', 15));
        $perPage = in_array($perPage, [15, 25, 50, 100], true) ? $perPage : 15;

        $queryFactory = function () use ($status, $search) {
            return Order::query()
                ->with(['user', 'vehicle', 'paymentMethod', 'financial', 'contract'])
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        if (preg_match('/^ord-(\d+)$/i', $search, $matches)) {
                            $subQuery->orWhere('id', (int) $matches[1]);
                        }

                        if (ctype_digit($search)) {
                            $subQuery->orWhere('id', (int) $search);
                        }

                        $subQuery
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
                });
        };

        $orders = $queryFactory()
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $summary = [
            'filteredTotal' => $orders->total(),
            'grossVolume' => (float) $queryFactory()->sum('valor_compra'),
            'pending' => $queryFactory()->where('status', Order::STATUS_PENDENTE)->count(),
            'awaitingPayment' => $queryFactory()->whereIn('status', [Order::STATUS_AGUARD, Order::STATUS_FATURADO])->count(),
            'paid' => $queryFactory()->where('status', Order::STATUS_PAGO)->count(),
            'newThisWeek' => $queryFactory()->where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
            'perPage' => $perPage,
            'summary' => $summary,
            'globalTotalOrders' => Order::count(),
            'hasActiveFilters' => $status !== '' || $search !== '',
            'statusOptions' => Order::statusLabels(),
        ]);
    }
}
