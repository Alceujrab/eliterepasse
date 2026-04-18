@extends('admin.layouts.app')

@php
    use App\Models\Financial;
    use App\Models\Order;

    $pageTitle = 'Pedidos (Admin v2)';
    $pageSubtitle = 'Fila operacional com foco em decisao rapida, contexto financeiro e proximos passos por pedido.';

    $statusClassMap = [
        Order::STATUS_PENDENTE => 'is-pending',
        Order::STATUS_AGUARD => 'is-awaiting',
        Order::STATUS_CONFIRMADO => 'is-confirmed',
        Order::STATUS_FATURADO => 'is-billed',
        Order::STATUS_PAGO => 'is-paid',
        Order::STATUS_CANCELADO => 'is-cancelled',
    ];

    $financialStatusLabels = Financial::statusLabels();
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

    <section class="admin-metrics-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Pedidos no filtro</p>
            <p class="admin-metric-value">{{ number_format($summary['filteredTotal']) }}</p>
            <p class="admin-metric-footnote">Base total: {{ number_format($globalTotalOrders) }} pedidos</p>
        </article>

        <article class="admin-metric-card">
            <p class="admin-metric-label">Volume filtrado</p>
            <p class="admin-metric-value">R$ {{ number_format($summary['grossVolume'], 0, ',', '.') }}</p>
            <p class="admin-metric-footnote">Soma de valor de compra dos pedidos encontrados</p>
        </article>

        <article class="admin-metric-card">
            <p class="admin-metric-label">Pendentes</p>
            <p class="admin-metric-value">{{ number_format($summary['pending']) }}</p>
            <p class="admin-metric-footnote">Pedidos aguardando primeira decisao operacional</p>
        </article>

        <article class="admin-metric-card">
            <p class="admin-metric-label">Em cobranca</p>
            <p class="admin-metric-value">{{ number_format($summary['awaitingPayment']) }}</p>
            <p class="admin-metric-footnote">Confirmados ou faturados que exigem sequencia financeira</p>
        </article>

        <article class="admin-metric-card">
            <p class="admin-metric-label">Novos 7 dias</p>
            <p class="admin-metric-value">{{ number_format($summary['newThisWeek']) }}</p>
            <p class="admin-metric-footnote">Pedidos criados na ultima semana dentro do filtro</p>
        </article>
    </section>

    <section class="mt-6 admin-card">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag {{ $hasActiveFilters ? 'admin-tag-migration' : 'admin-tag-new' }}">
                    {{ $hasActiveFilters ? 'filtro ativo' : 'fila completa' }}
                </span>
                <h2 class="mt-3 admin-section-title">Fila de pedidos</h2>
                <p class="admin-section-note">
                    Mostrando {{ number_format($summary['filteredTotal']) }} pedido(s)
                    @if($hasActiveFilters)
                        com recorte aplicado por busca ou status.
                    @else
                        com visao geral do pipeline comercial e financeiro.
                    @endif
                </p>
            </div>

            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.orders.index', request()->query()) }}" class="admin-btn-soft">Atualizar</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.v2.orders.index') }}" class="admin-filter-grid md:items-end">
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

            <div>
                <label for="orders-per-page" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Por pagina</label>
                <select
                    id="orders-per-page"
                    name="per_page"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400"
                >
                    @foreach([15, 25, 50, 100] as $option)
                        <option value="{{ $option }}" @selected(($perPage ?? 15) === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="orders-sort" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Ordenar por</label>
                <select
                    id="orders-sort"
                    name="sort"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400"
                >
                    @php
                        $sortOptions = [
                            'created_at' => 'Mais recentes',
                            'id' => 'Numero do pedido',
                            'valor_compra' => 'Valor',
                            'status' => 'Status',
                        ];
                    @endphp
                    @foreach($sortOptions as $key => $label)
                        <option value="{{ $key }}" @selected(($sort ?? 'created_at') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="orders-direction" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Sentido</label>
                <select
                    id="orders-direction"
                    name="direction"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400"
                >
                    <option value="desc" @selected(($direction ?? 'desc') === 'desc')>Decrescente</option>
                    <option value="asc" @selected(($direction ?? 'desc') === 'asc')>Crescente</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="admin-btn-primary">Filtrar</button>
                <a href="{{ route('admin.v2.orders.index', ['reset' => 1]) }}" class="admin-btn-soft" title="Limpa filtros memorizados desta tela">Limpar</a>
            </div>
        </form>

        <div class="admin-quick-filters">
            <a href="{{ route('admin.v2.orders.index', $search !== '' ? ['q' => $search] : []) }}" class="admin-filter-chip {{ $status === '' ? 'is-active' : '' }}">
                <span>Todos</span>
                <span>{{ number_format($globalTotalOrders) }}</span>
            </a>

            @foreach($statusOptions as $statusKey => $statusLabel)
                <a
                    href="{{ route('admin.v2.orders.index', array_filter(['status' => $statusKey, 'q' => $search !== '' ? $search : null])) }}"
                    class="admin-filter-chip {{ $status === $statusKey ? 'is-active' : '' }}"
                >
                    <span>{{ $statusLabel }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="admin-data-table-wrapper hidden lg:block">
        <table class="admin-data-table">
            <thead>
            <tr>
                <th>Pedido</th>
                <th>Cliente</th>
                <th>Veiculo</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Financeiro</th>
                <th>Acoes</th>
            </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                @php
                    $statusLabel = $statusOptions[$order->status] ?? $order->status;
                    $statusClass = $statusClassMap[$order->status] ?? 'is-pending';
                    $financialLabel = $order->financial
                        ? ($financialStatusLabels[$order->financial->status] ?? $order->financial->status)
                        : 'Sem fatura';
                    $contractState = $order->contract ? 'Contrato gerado' : 'Contrato pendente';
                    $vehicleLabel = $order->vehicle
                        ? trim(sprintf('%s %s %s', $order->vehicle->brand, $order->vehicle->model, $order->vehicle->model_year))
                        : 'Veiculo nao vinculado';
                    $customerLabel = $order->user?->razao_social ?? $order->user?->name ?? 'Cliente nao identificado';
                @endphp
                <tr>
                    <td>
                        <div class="admin-row-title">{{ $order->numero }}</div>
                        <div class="admin-row-meta">Criado em {{ $order->created_at?->format('d/m/Y H:i') }} · ID {{ $order->id }}</div>
                    </td>
                    <td>
                        <div class="admin-row-title">{{ $customerLabel }}</div>
                        <div class="admin-row-meta">{{ $order->user?->cnpj ?? $order->user?->email ?? 'Sem documento cadastrado' }}</div>
                    </td>
                    <td>
                        <div class="admin-row-title">{{ $vehicleLabel }}</div>
                        <div class="admin-row-meta">{{ $order->vehicle?->plate ?? 'Sem placa' }}</div>
                    </td>
                    <td>
                        <div class="admin-row-title text-emerald-700">R$ {{ number_format((float) $order->valor_compra, 2, ',', '.') }}</div>
                        <div class="admin-row-meta">Metodo: {{ $order->paymentMethod?->name ?? 'Nao informado' }}</div>
                    </td>
                    <td>
                        <span class="admin-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        <div class="admin-row-meta">{{ $contractState }}</div>
                    </td>
                    <td>
                        <div class="admin-row-title">{{ $financialLabel }}</div>
                        <div class="admin-row-meta">
                            @if($order->financial && $order->financial->data_vencimento)
                                Vence em {{ $order->financial->data_vencimento->format('d/m/Y') }}
                            @elseif($order->financial && $order->financial->data_pagamento)
                                Pago em {{ $order->financial->data_pagamento->format('d/m/Y') }}
                            @else
                                Nenhum titulo gerado
                            @endif
                        </div>
                    </td>
                    <td>
                        @include('admin.orders.partials.actions', ['order' => $order])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-sm font-semibold text-slate-500">
                        Nenhum pedido encontrado para o filtro atual.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section class="admin-mobile-list">
        @forelse($orders as $order)
            @php
                $statusLabel = $statusOptions[$order->status] ?? $order->status;
                $statusClass = $statusClassMap[$order->status] ?? 'is-pending';
                $financialLabel = $order->financial
                    ? ($financialStatusLabels[$order->financial->status] ?? $order->financial->status)
                    : 'Sem fatura';
                $customerLabel = $order->user?->razao_social ?? $order->user?->name ?? 'Cliente nao identificado';
                $vehicleLabel = $order->vehicle
                    ? trim(sprintf('%s %s %s', $order->vehicle->brand, $order->vehicle->model, $order->vehicle->model_year))
                    : 'Veiculo nao vinculado';
            @endphp

            <article class="admin-order-card">
                <div class="admin-order-card-header">
                    <div>
                        <h3 class="admin-row-title">{{ $order->numero }}</h3>
                        <p class="admin-row-meta">{{ $order->created_at?->format('d/m/Y H:i') }}</p>
                    </div>

                    <span class="admin-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>

                <div class="admin-order-card-grid">
                    <div>
                        <span class="admin-detail-label">Cliente</span>
                        <span class="admin-detail-value">{{ $customerLabel }}</span>
                    </div>

                    <div>
                        <span class="admin-detail-label">Valor</span>
                        <span class="admin-detail-value">R$ {{ number_format((float) $order->valor_compra, 2, ',', '.') }}</span>
                    </div>

                    <div>
                        <span class="admin-detail-label">Veiculo</span>
                        <span class="admin-detail-value">{{ $vehicleLabel }}</span>
                    </div>

                    <div>
                        <span class="admin-detail-label">Financeiro</span>
                        <span class="admin-detail-value">{{ $financialLabel }}</span>
                    </div>
                </div>

                <div class="admin-row-meta">
                    {{ $order->contract ? 'Contrato gerado' : 'Contrato pendente' }} · {{ $order->vehicle?->plate ?? 'Sem placa' }}
                </div>

                <div class="mt-4">
                    @include('admin.orders.partials.actions', ['order' => $order])
                </div>
            </article>
        @empty
            <article class="admin-order-card text-center text-sm font-semibold text-slate-500">
                Nenhum pedido encontrado para o filtro atual.
            </article>
        @endforelse
    </section>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
@endsection
