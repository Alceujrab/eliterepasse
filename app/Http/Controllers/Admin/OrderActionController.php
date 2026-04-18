<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Financial;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderShipment;
use App\Services\ContractService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
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

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if (! in_array($order->status, [Order::STATUS_PENDENTE, Order::STATUS_CONFIRMADO], true)) {
            return back()->with('admin_warning', 'Pedido fora do estado permitido para cancelamento.');
        }

        $validated = $request->validate([
            'motivo' => ['nullable', 'string', 'min:3', 'max:500'],
        ]);
        $motivo = $validated['motivo'] ?? null;

        DB::transaction(function () use ($order, $motivo) {
            $previousStatus = $order->status;
            $order->update(['status' => Order::STATUS_CANCELADO]);

            if ($previousStatus === Order::STATUS_CONFIRMADO) {
                $order->vehicle?->update(['status' => 'available']);
            }

            OrderHistory::registrar(
                $order->id,
                'pedido_cancelado',
                $previousStatus,
                Order::STATUS_CANCELADO,
                $motivo ? "Pedido cancelado · Motivo: {$motivo}" : null,
                null,
                $motivo ? ['motivo' => $motivo] : null,
            );
        });

        return back()->with('admin_success', "Pedido {$order->numero} cancelado.");
    }

    public function publishDocument(Request $request, Order $order, NotificationService $notificationService): RedirectResponse
    {
        if (! in_array($order->status, [Order::STATUS_CONFIRMADO, Order::STATUS_FATURADO, Order::STATUS_PAGO], true)) {
            return back()->with('admin_warning', 'Pedido fora do estado permitido para disponibilizar documentos.');
        }

        $validated = $request->validate([
            'tipo_documento' => ['required', 'in:' . implode(',', array_keys(OrderShipment::tipoDocumentoLabels()))],
            'titulo' => ['nullable', 'string', 'max:255'],
            'arquivo' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'observacoes' => ['nullable', 'string'],
        ]);

        $arquivo = $validated['arquivo'];
        $path = $arquivo->store('shipments/documentos', 'public');

        $shipment = OrderShipment::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'tipo_documento' => $validated['tipo_documento'],
            'titulo' => $validated['titulo'] ?? null,
            'file_path' => $path,
            'nome_original' => $arquivo->getClientOriginalName(),
            'status' => 'disponivel',
            'observacoes' => $validated['observacoes'] ?? null,
        ]);

        $tipo = OrderShipment::tipoDocumentoLabels()[$validated['tipo_documento']] ?? $validated['tipo_documento'];

        OrderHistory::registrar(
            $order->id,
            'documento_disponivel',
            $order->status,
            $order->status,
            "Documento {$tipo} disponibilizado para o cliente",
            Auth::id(),
            ['shipment_id' => $shipment->id, 'tipo' => $validated['tipo_documento']]
        );

        $notificationService->documentoDisponivel($shipment->fresh(['order.user', 'user']));

        return back()->with('admin_success', 'Documento disponibilizado e cliente notificado.');
    }

    public function registerDispatch(Request $request, Order $order, NotificationService $notificationService): RedirectResponse
    {
        $validated = $request->validate([
            'shipment_id' => ['required', 'integer'],
            'metodo_envio' => ['required', 'in:' . implode(',', array_keys(OrderShipment::metodoEnvioLabels()))],
            'metodo_envio_detalhe' => ['nullable', 'string', 'max:255'],
            'codigo_rastreio' => ['nullable', 'string', 'max:255'],
            'comprovante_despacho' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'despachado_em' => ['required', 'date'],
            'observacoes' => ['nullable', 'string'],
        ]);

        $shipment = $order->shipments()->findOrFail($validated['shipment_id']);

        if ($shipment->status !== 'disponivel') {
            return back()->with('admin_warning', 'Somente documentos disponíveis podem ser despachados.');
        }

        $comprovantePath = $request->hasFile('comprovante_despacho')
            ? $request->file('comprovante_despacho')->store('shipments/comprovantes', 'public')
            : null;

        $shipment->update([
            'metodo_envio' => $validated['metodo_envio'],
            'metodo_envio_detalhe' => $validated['metodo_envio_detalhe'] ?? null,
            'codigo_rastreio' => $validated['codigo_rastreio'] ?? null,
            'comprovante_despacho_path' => $comprovantePath,
            'despachado_em' => $validated['despachado_em'],
            'status' => 'despachado',
            'observacoes' => $validated['observacoes'] ?? $shipment->observacoes,
        ]);

        $tipo = OrderShipment::tipoDocumentoLabels()[$shipment->tipo_documento] ?? $shipment->tipo_documento;
        $metodo = OrderShipment::metodoEnvioLabels()[$validated['metodo_envio']] ?? $validated['metodo_envio'];

        OrderHistory::registrar(
            $order->id,
            'documento_despachado',
            $order->status,
            $order->status,
            "Documento {$tipo} despachado via {$metodo}" . (($validated['codigo_rastreio'] ?? null) ? " — Rastreio: {$validated['codigo_rastreio']}" : ''),
            Auth::id(),
            [
                'shipment_id' => $shipment->id,
                'metodo_envio' => $validated['metodo_envio'],
                'codigo_rastreio' => $validated['codigo_rastreio'] ?? null,
            ]
        );

        $notificationService->documentoDespachado($shipment->fresh(['order.user', 'user']));

        return back()->with('admin_success', 'Despacho registrado e cliente notificado.');
    }

    public function resendShipmentNotification(Request $request, Order $order, NotificationService $notificationService): RedirectResponse
    {
        $validated = $request->validate([
            'shipment_id' => ['required', 'integer'],
        ]);

        $shipment = $order->shipments()->findOrFail($validated['shipment_id']);

        $notificationService->documentoDisponivel($shipment->fresh(['order.user', 'user']));

        return back()->with('admin_success', 'Notificação reenviada ao cliente.');
    }

    public function markShipmentDelivered(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'shipment_id' => ['required', 'integer'],
        ]);

        $shipment = $order->shipments()->findOrFail($validated['shipment_id']);

        if ($shipment->status !== 'despachado') {
            return back()->with('admin_warning', 'Somente documentos despachados podem ser marcados como entregues.');
        }

        $shipment->update(['status' => 'entregue']);

        $tipo = OrderShipment::tipoDocumentoLabels()[$shipment->tipo_documento] ?? $shipment->tipo_documento;

        OrderHistory::registrar(
            $order->id,
            'documento_entregue',
            $order->status,
            $order->status,
            "Documento {$tipo} entregue ao cliente",
            Auth::id(),
            ['shipment_id' => $shipment->id]
        );

        return back()->with('admin_success', 'Documento marcado como entregue.');
    }
}
