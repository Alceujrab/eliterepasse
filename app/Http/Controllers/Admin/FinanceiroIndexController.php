<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Financial;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceiroIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $status = $request->string('status')->toString();
        $paymentMethod = $request->string('payment')->toString();
        $search = trim($request->string('q')->toString());
        $overdueOnly = $request->boolean('overdue');

        $queryFactory = function () use ($status, $paymentMethod, $search, $overdueOnly) {
            return Financial::query()
                ->with(['order.user', 'order.vehicle', 'order.paymentMethod', 'criadoPor'])
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($paymentMethod !== '', fn ($query) => $query->where('forma_pagamento', $paymentMethod))
                ->when($overdueOnly, fn ($query) => $query->where('status', 'em_aberto')->whereDate('data_vencimento', '<', now()->toDateString()))
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery
                            ->where('numero', 'like', "%{$search}%")
                            ->orWhere('numero_fatura', 'like', "%{$search}%")
                            ->orWhere('descricao', 'like', "%{$search}%")
                            ->orWhereHas('order', fn ($orderQuery) => $orderQuery->where('id', 'like', "%{$search}%"))
                            ->orWhereHas('order.user', fn ($userQuery) => $userQuery
                                ->where('razao_social', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('cnpj', 'like', "%{$search}%")
                            )
                            ->orWhereHas('order.vehicle', fn ($vehicleQuery) => $vehicleQuery
                                ->where('brand', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%")
                                ->orWhere('plate', 'like', "%{$search}%")
                            );
                    });
                });
        };

        $financials = $queryFactory()
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'filteredTotal' => $financials->total(),
            'openCount' => $queryFactory()->where('status', 'em_aberto')->count(),
            'openValue' => (float) $queryFactory()->where('status', 'em_aberto')->sum('valor'),
            'paidValue' => (float) $queryFactory()->where('status', 'pago')->sum('valor'),
            'overdueCount' => $queryFactory()->where('status', 'em_aberto')->whereDate('data_vencimento', '<', now()->toDateString())->count(),
            'overdueValue' => (float) $queryFactory()->where('status', 'em_aberto')->whereDate('data_vencimento', '<', now()->toDateString())->sum('valor'),
        ];

        $ordersWithoutFinancial = Order::query()
            ->doesntHave('financial')
            ->whereIn('status', [Order::STATUS_CONFIRMADO, Order::STATUS_FATURADO, Order::STATUS_AGUARD, Order::STATUS_PAGO])
            ->with(['user', 'vehicle', 'paymentMethod'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.financeiro.index', [
            'financials' => $financials,
            'summary' => $summary,
            'status' => $status,
            'paymentMethod' => $paymentMethod,
            'search' => $search,
            'overdueOnly' => $overdueOnly,
            'hasActiveFilters' => $status !== '' || $paymentMethod !== '' || $search !== '' || $overdueOnly,
            'globalTotalFinancials' => Financial::count(),
            'ordersWithoutFinancial' => $ordersWithoutFinancial,
            'statusOptions' => Financial::statusLabels(),
            'paymentMethodOptions' => Financial::formasPagamento(),
        ]);
    }
}