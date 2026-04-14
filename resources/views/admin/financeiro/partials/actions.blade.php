<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.v2.financeiro.show', $financial) }}" class="admin-btn-soft">Abrir v2</a>

    @if($financial->order)
        <a href="{{ route('admin.v2.orders.show', $financial->order) }}" class="admin-btn-soft">Pedido</a>

        @if($financial->status === 'em_aberto' && $financial->order->status === \App\Models\Order::STATUS_FATURADO)
            <form method="POST" action="{{ route('admin.v2.orders.confirm-payment', $financial->order) }}">
                @csrf
                <button type="submit" class="admin-btn-primary">Confirmar pagamento</button>
            </form>
        @endif
    @endif

    @if($financial->boleto_url)
        <a href="{{ $financial->boleto_url }}" target="_blank" rel="noreferrer" class="admin-btn-soft">Boleto</a>
    @endif

    @if($financial->invoice_url)
        <a href="{{ $financial->invoice_url }}" target="_blank" rel="noreferrer" class="admin-btn-soft">Invoice</a>
    @endif

    <a href="/admin/gestao-financeira" class="admin-btn-soft">Legado</a>
</div>