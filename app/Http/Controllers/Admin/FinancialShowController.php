<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Financial;
use Illuminate\View\View;

class FinancialShowController extends Controller
{
    public function __invoke(Financial $financial): View
    {
        $financial->load([
            'order.user',
            'order.vehicle',
            'order.paymentMethod',
            'order.contract',
            'criadoPor',
        ]);

        $summary = [
            'isOverdue' => $financial->esta_vencido,
            'isPaid' => $financial->status === 'pago',
            'hasInvoiceLink' => (bool) $financial->invoice_url,
            'hasBoletoLink' => (bool) $financial->boleto_url,
        ];

        return view('admin.financeiro.show', [
            'financial' => $financial,
            'summary' => $summary,
            'statusOptions' => Financial::statusLabels(),
            'paymentMethodOptions' => Financial::formasPagamento(),
        ]);
    }
}