@extends('admin.layouts.app')

@php
    $pageTitle = 'Pedidos (Admin v2)';
    $pageSubtitle = 'Listagem operacional com filtros e ações críticas sem dependência do Filament.';
@endphp

@section('content')
    @if(session('admin_success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('admin_success') }}
        </div>
    @endif

    @if(session('admin_warning'))
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">
            {{ session('admin_warning') }}
        </div>
    @endif

    <section class="admin-card">
        <form method="GET" action="{{ route('admin.v2.orders.index') }}" class="grid gap-3 md:grid-cols-[1fr_220px_auto] md:items-end">
            <div>
                <label for="orders-q" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Busca</label>
                <input
                    id="orders-q"
                    name="q"
                    value="{{ $search }}"
                    placeholder="ORD-000001, cliente, placa..."
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400"
                >
            </div>

            <div>
                <label for="orders-status" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Status</label>
                <select
                    id="orders-status"
                    name="status"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400"
                >
                    <option value="">Todos</option>
                    @foreach($statusOptions as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="admin-btn-primary">Filtrar</button>
                <a href="{{ route('admin.v2.orders.index') }}" class="admin-btn-soft">Limpar</a>
            </div>
        </form>
    </section>

    <section class="mt-4 admin-card overflow-x-auto">
        <table class="min-w-full border-separate border-spacing-y-2">
            <thead>
            <tr class="text-left text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">
                <th class="px-3 py-2">Pedido</th>
                <th class="px-3 py-2">Cliente</th>
                <th class="px-3 py-2">Veículo</th>
                <th class="px-3 py-2">Valor</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2">Criado</th>
                <th class="px-3 py-2">Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr class="rounded-xl border border-slate-200 bg-slate-50 align-top">
                    <td class="px-3 py-3 text-sm font-black text-slate-800">{{ $order->numero }}</td>
                    <td class="px-3 py-3 text-sm font-semibold text-slate-700">
                        {{ $order->user?->razao_social ?? $order->user?->name ?? '—' }}
                        <div class="text-xs font-medium text-slate-500">{{ $order->user?->cnpj ?? $order->user?->email }}</div>
                    </td>
                    <td class="px-3 py-3 text-sm font-semibold text-slate-700">
                        @if($order->vehicle)
                            {{ $order->vehicle->brand }} {{ $order->vehicle->model }} {{ $order->vehicle->model_year }}
                            <div class="text-xs font-medium text-slate-500">{{ $order->vehicle->plate }}</div>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-3 py-3 text-sm font-bold text-emerald-700">R$ {{ number_format((float) $order->valor_compra, 2, ',', '.') }}</td>
                    <td class="px-3 py-3 text-xs font-bold text-slate-700">{{ $statusOptions[$order->status] ?? $order->status }}</td>
                    <td class="px-3 py-3 text-xs font-semibold text-slate-500">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-3 py-3">
                        <div class="flex flex-wrap gap-2">
                            @if($order->status === \App\Models\Order::STATUS_PENDENTE)
                                <form method="POST" action="{{ route('admin.v2.orders.confirm', $order) }}">
                                    @csrf
                                    <button type="submit" class="admin-btn-soft">Confirmar</button>
                                </form>
                            @endif

                            @if($order->status === \App\Models\Order::STATUS_CONFIRMADO && ! $order->contract)
                                <form method="POST" action="{{ route('admin.v2.orders.generate-contract', $order) }}">
                                    @csrf
                                    <button type="submit" class="admin-btn-soft">Gerar Contrato</button>
                                </form>
                            @endif

                            @if(in_array($order->status, [\App\Models\Order::STATUS_CONFIRMADO, \App\Models\Order::STATUS_FATURADO], true) && ! $order->financial)
                                <form method="POST" action="{{ route('admin.v2.orders.generate-invoice', $order) }}">
                                    @csrf
                                    <button type="submit" class="admin-btn-soft">Gerar Fatura</button>
                                </form>
                            @endif

                            @if($order->status === \App\Models\Order::STATUS_FATURADO && $order->financial && $order->financial->status === 'em_aberto')
                                <form method="POST" action="{{ route('admin.v2.orders.confirm-payment', $order) }}">
                                    @csrf
                                    <button type="submit" class="admin-btn-soft">Confirmar Pagamento</button>
                                </form>
                            @endif

                            @if(in_array($order->status, [\App\Models\Order::STATUS_PENDENTE, \App\Models\Order::STATUS_CONFIRMADO], true))
                                <form method="POST" action="{{ route('admin.v2.orders.cancel', $order) }}">
                                    @csrf
                                    <button type="submit" class="admin-btn-soft">Cancelar</button>
                                </form>
                            @endif

                            <a href="/admin/orders/{{ $order->id }}" class="admin-btn-soft">Ver legado</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-3 py-6 text-center text-sm font-semibold text-slate-500">Nenhum pedido encontrado para o filtro atual.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </section>
@endsection
