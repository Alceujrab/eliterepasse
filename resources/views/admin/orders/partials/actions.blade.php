<div class="admin-action-cluster">
    @if($order->status === \App\Models\Order::STATUS_PENDENTE)
        <form method="POST" action="{{ route('admin.v2.orders.confirm', $order) }}">
            @csrf
            <button type="submit" class="admin-btn-primary">Confirmar</button>
        </form>
    @endif

    @if($order->status === \App\Models\Order::STATUS_CONFIRMADO && ! $order->contract)
        <form method="POST" action="{{ route('admin.v2.orders.generate-contract', $order) }}">
            @csrf
            <button type="submit" class="admin-btn-primary">Gerar contrato</button>
        </form>
    @endif

    @if(in_array($order->status, [\App\Models\Order::STATUS_CONFIRMADO, \App\Models\Order::STATUS_FATURADO], true) && ! $order->financial)
        <form method="POST" action="{{ route('admin.v2.orders.generate-invoice', $order) }}">
            @csrf
            <button type="submit" class="admin-btn-soft">Gerar fatura</button>
        </form>
    @endif

    @if($order->status === \App\Models\Order::STATUS_FATURADO && $order->financial && $order->financial->status === 'em_aberto')
        <form method="POST" action="{{ route('admin.v2.orders.confirm-payment', $order) }}">
            @csrf
            <button type="submit" class="admin-btn-primary">Confirmar pagamento</button>
        </form>
    @endif

    @if(in_array($order->status, [\App\Models\Order::STATUS_PENDENTE, \App\Models\Order::STATUS_CONFIRMADO], true))
        <form method="POST" action="{{ route('admin.v2.orders.cancel', $order) }}">
            @csrf
            <button type="submit" class="admin-btn-danger">Cancelar</button>
        </form>
    @endif

    <a href="/admin/orders/{{ $order->id }}" class="admin-btn-soft">Ver legado</a>
</div>