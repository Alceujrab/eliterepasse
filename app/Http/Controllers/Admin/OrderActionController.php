<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Financial;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Services\ContractService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderActionController extends Controller
{
    public function confirm(Order $order, NotificationService $notificationService): RedirectResponse
    {
        if ($order->status !== Order::STATUS_PENDENTE) {
            return back()->with('admin_warning', 'Pedido fora do estado permitido para confirmação.');
        }

        DB::transaction(function () use ($order, $notificationService) {
            $order->update([
                'status' => Order::STATUS_CONFIRMADO,
                'confirmado_em' => now(),
                'confirmado_por' => Auth::id(),
            ]);

            $order->vehicle?->update(['status' => 'reserved']);

            OrderHistory::registrar($order->id, 'pedido_confirmado', Order::STATUS_PENDENTE, Order::STATUS_CONFIRMADO);
            $notificationService->pedidoConfirmado($order->fresh(['user', 'vehicle']));
        });

        return back()->with('admin_success', "Pedido {$order->numero} confirmado.");
    }

    public function generateContract(Order $order, ContractService $contractService, NotificationService $notificationService): RedirectResponse
    {
        if ($order->status !== Order::STATUS_CONFIRMADO || $order->contract) {
            return back()->with('admin_warning', 'Pedido fora do estado permitido para geração de contrato.');
        }

        DB::transaction(function () use ($order, $contractService, $notificationService) {
            $contract = $contractService->gerarDeOrdem($order);
            $token = $contract->assinaturaComprador?->token_assinatura;
            $signatureLink = $token ? route('contrato.assinar.show', $token) : route('dashboard');

            OrderHistory::registrar(
                $order->id,
                'contrato_gerado',
                null,
                null,
                "Contrato {$contract->numero} gerado"
            );

            $notificationService->contratoParaAssinar($contract, $signatureLink);
        });

        return back()->with('admin_success', "Contrato gerado para o pedido {$order->numero}.");
    }

    public function generateInvoice(Order $order, NotificationService $notificationService): RedirectResponse
    {
        if (! in_array($order->status, [Order::STATUS_CONFIRMADO, Order::STATUS_FATURADO], true) || $order->financial) {
            return back()->with('admin_warning', 'Pedido fora do estado permitido para gerar fatura.');
        }

        DB::transaction(function () use ($order, $notificationService) {
            $financial = Financial::create([
                'order_id' => $order->id,
                'numero' => Financial::gerarNumero(),
                'descricao' => 'Compra veículo ' . ($order->vehicle ? "{$order->vehicle->brand} {$order->vehicle->model}" : $order->numero),
                'valor' => $order->valor_compra,
                'forma_pagamento' => $order->paymentMethod?->slug ?? 'boleto',
                'data_vencimento' => now()->addDays(3)->toDateString(),
                'status' => 'em_aberto',
                'criado_por' => Auth::id(),
                'observacoes' => 'Gerado pelo Admin v2',
            ]);

            $previousStatus = $order->status;
            $order->update(['status' => Order::STATUS_FATURADO]);

            OrderHistory::registrar(
                $order->id,
                'fatura_gerada',
                $previousStatus,
                Order::STATUS_FATURADO,
                "Fatura {$financial->numero} gerada"
            );

            $notificationService->faturaGerada($financial->fresh('order.user'));
        });

        return back()->with('admin_success', "Fatura gerada para o pedido {$order->numero}.");
    }

    public function confirmPayment(Order $order, NotificationService $notificationService): RedirectResponse
    {
        $financial = $order->financial;

        if ($order->status !== Order::STATUS_FATURADO || ! $financial || $financial->status !== 'em_aberto') {
            return back()->with('admin_warning', 'Pedido fora do estado permitido para confirmação de pagamento.');
        }

        DB::transaction(function () use ($order, $financial, $notificationService) {
            $financial->update([
                'status' => 'pago',
                'data_pagamento' => now(),
            ]);

            $order->update(['status' => Order::STATUS_PAGO]);

            OrderHistory::registrar(
                $order->id,
                'pagamento_confirmado',
                Order::STATUS_FATURADO,
                Order::STATUS_PAGO,
                "Pagamento {$financial->numero} confirmado"
            );

            $notificationService->pagamentoConfirmado($financial->fresh('order.user'));
        });

        return back()->with('admin_success', "Pagamento confirmado para o pedido {$order->numero}.");
    }

    public function cancel(Order $order): RedirectResponse
    {
        if (! in_array($order->status, [Order::STATUS_PENDENTE, Order::STATUS_CONFIRMADO], true)) {
            return back()->with('admin_warning', 'Pedido fora do estado permitido para cancelamento.');
        }

        DB::transaction(function () use ($order) {
            $previousStatus = $order->status;
            $order->update(['status' => Order::STATUS_CANCELADO]);

            if ($previousStatus === Order::STATUS_CONFIRMADO) {
                $order->vehicle?->update(['status' => 'available']);
            }

            OrderHistory::registrar($order->id, 'pedido_cancelado', $previousStatus, Order::STATUS_CANCELADO);
        });

        return back()->with('admin_success', "Pedido {$order->numero} cancelado.");
    }
}
