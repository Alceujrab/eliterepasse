<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdersIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());

        $orders = Order::query()
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
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
            'statusOptions' => Order::statusLabels(),
        ]);
    }
}
