<div class="flex flex-wrap gap-2">
    @php
        $isValidLink = function (?string $url): bool {
            if (! $url) return false;
            $url = trim($url);
            if ($url === '') return false;
            if (! preg_match('#^https?://#i', $url)) return false;
            // Filtra placeholders / seeds: w3.org/dummy*, example.com, localhost, etc.
            if (preg_match('#(w3\.org/.*dummy|example\.com|/dummy\.pdf$)#i', $url)) return false;
            return true;
        };
    @endphp

    <a href="{{ route('admin.v2.financeiro.show', $financial) }}" class="admin-btn-soft">Abrir v2</a>

    @if($financial->order)
        <a href="{{ route('admin.v2.orders.show', $financial->order) }}" class="admin-btn-soft">Pedido</a>

        @if($financial->status === 'em_aberto' && $financial->order->status === \App\Models\Order::STATUS_FATURADO)
            <x-admin.action-button
                :action="route('admin.v2.orders.confirm-payment', $financial->order)"
                label="Confirmar pagamento"
                variant="primary"
                confirm="Confirmar recebimento da fatura {{ $financial->numero }}?"
                confirmDetail="A fatura sera marcada como paga e o cliente sera notificado."
                confirmLabel="Sim, confirmar"
            />
        @endif
    @endif

    @if($isValidLink($financial->boleto_url))
        <a href="{{ $financial->boleto_url }}" target="_blank" rel="noreferrer" class="admin-btn-soft">Boleto</a>
    @endif

    @if($isValidLink($financial->invoice_url))
        <a href="{{ $financial->invoice_url }}" target="_blank" rel="noreferrer" class="admin-btn-soft">Invoice</a>
    @endif
</div>