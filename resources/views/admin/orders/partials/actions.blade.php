@php
    $orderShowUrl = \Illuminate\Support\Facades\Route::has('admin.v2.orders.show')
        ? route('admin.v2.orders.show', $order)
        : url('/painel-admin/pedidos/' . $order->id);

    $isPendente = $order->status === \App\Models\Order::STATUS_PENDENTE;
    $isConfirmado = $order->status === \App\Models\Order::STATUS_CONFIRMADO;
    $isFaturado = $order->status === \App\Models\Order::STATUS_FATURADO;
    $cliente = $order->user?->razao_social ?? $order->user?->name ?? 'Cliente';
    $valor = 'R$ ' . number_format((float) $order->valor_compra, 2, ',', '.');
@endphp

<div class="admin-action-cluster">
    <a href="{{ $orderShowUrl }}" class="admin-btn-soft">Abrir v2</a>

    @if($isPendente)
        <x-admin.action-button
            :action="route('admin.v2.orders.confirm', $order)"
            label="Confirmar"
            variant="primary"
            confirm="Confirmar o pedido {{ $order->numero }}?"
            :confirmDetail="$cliente . ' · ' . $valor . '. O veiculo sera reservado.'"
            confirmLabel="Sim, confirmar pedido"
        />
    @endif

    @if($isConfirmado && ! $order->contract)
        <x-admin.action-button
            :action="route('admin.v2.orders.generate-contract', $order)"
            label="Gerar contrato"
            variant="primary"
            confirm="Gerar contrato para {{ $order->numero }}?"
            :confirmDetail="$cliente . ' · ' . $valor . '. O contrato sera enviado para assinatura.'"
            confirmLabel="Gerar contrato"
        />
    @endif

    @if(in_array($order->status, [\App\Models\Order::STATUS_CONFIRMADO, \App\Models\Order::STATUS_FATURADO], true) && ! $order->financial)
        <x-admin.action-button
            :action="route('admin.v2.orders.generate-invoice', $order)"
            label="Gerar fatura"
            variant="soft"
            confirm="Gerar fatura para {{ $order->numero }}?"
            :confirmDetail="$cliente . ' · ' . $valor . '. Uma cobranca sera emitida.'"
            confirmLabel="Gerar fatura"
        />
    @endif

    @if($isFaturado && $order->financial && $order->financial->status === 'em_aberto')
        <x-admin.action-button
            :action="route('admin.v2.orders.confirm-payment', $order)"
            label="Confirmar pagamento"
            variant="primary"
            confirm="Confirmar o recebimento de {{ $valor }}?"
            :confirmDetail="'Pedido ' . $order->numero . ' · ' . $cliente . '. Esta acao marca a fatura como paga e nao pode ser desfeita facilmente.'"
            confirmLabel="Sim, confirmar pagamento"
        />
    @endif

    @if(in_array($order->status, [\App\Models\Order::STATUS_PENDENTE, \App\Models\Order::STATUS_CONFIRMADO], true))
        <x-admin.action-button
            :action="route('admin.v2.orders.cancel', $order)"
            label="Cancelar"
            variant="danger"
            confirm="Cancelar o pedido {{ $order->numero }}?"
            :confirmDetail="'O cancelamento e irreversivel.' . ($isConfirmado ? ' O veiculo voltara para o estoque disponivel.' : '')"
            confirmLabel="Sim, cancelar pedido"
            cancelLabel="Manter pedido"
            reasonField="motivo"
            reasonLabel="Motivo do cancelamento"
            :reasonRequired="true"
        />
    @endif
</div>