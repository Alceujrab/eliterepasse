@extends('admin.layouts.app')

@php
    $pageTitle = 'Gestao financeira';
    $pageSubtitle = 'Fila operacional de cobrancas, vencimentos e conciliacao com pedidos faturados.';
@endphp

@section('content')
    @if(session('admin_success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('admin_success') }}</div>
    @endif

    @if(session('admin_warning'))
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">{{ session('admin_warning') }}</div>
    @endif

    <section class="admin-metrics-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Cobrancas no filtro</p>
            <p class="admin-metric-value">{{ number_format($summary['filteredTotal']) }}</p>
            <p class="admin-metric-footnote">Base total: {{ number_format($globalTotalFinancials) }} registros</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Em aberto</p>
            <p class="admin-metric-value">{{ number_format($summary['openCount']) }}</p>
            <p class="admin-metric-footnote">R$ {{ number_format($summary['openValue'], 0, ',', '.') }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Vencidas</p>
            <p class="admin-metric-value">{{ number_format($summary['overdueCount']) }}</p>
            <p class="admin-metric-footnote">R$ {{ number_format($summary['overdueValue'], 0, ',', '.') }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Pagas</p>
            <p class="admin-metric-value">R$ {{ number_format($summary['paidValue'], 0, ',', '.') }}</p>
            <p class="admin-metric-footnote">Recebimentos dentro do recorte atual</p>
        </article>
    </section>

    <section class="mt-6 admin-card">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag {{ $hasActiveFilters ? 'admin-tag-migration' : 'admin-tag-new' }}">{{ $hasActiveFilters ? 'fila filtrada' : 'operacao financeira' }}</span>
                <h2 class="mt-3 admin-section-title">Central de cobrancas</h2>
                <p class="admin-section-note">Acompanhe cobrancas emitidas, pontos de atraso e pedidos ainda sem fatura a partir do mesmo workspace.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.financeiro.index', request()->query()) }}" class="admin-btn-soft">Atualizar</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.v2.financeiro.index') }}" class="admin-filter-grid md:grid-cols-4 md:items-end">
            <div>
                <label for="financeiro-q" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Busca</label>
                <input id="financeiro-q" name="q" value="{{ $search }}" placeholder="Fatura, cliente, placa..."
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
            </div>
            <div>
                <label for="financeiro-status" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Status</label>
                <select id="financeiro-status" name="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
                    <option value="">Todos</option>
                    @foreach($statusOptions as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="financeiro-payment" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Pagamento</label>
                <select id="financeiro-payment" name="payment" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
                    <option value="">Todos</option>
                    @foreach($paymentMethodOptions as $paymentKey => $paymentLabel)
                        <option value="{{ $paymentKey }}" @selected($paymentMethod === $paymentKey)>{{ $paymentLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600">
                    <input type="checkbox" name="overdue" value="1" @checked($overdueOnly) class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    Somente vencidas
                </label>
                <button type="submit" class="admin-btn-primary">Filtrar</button>
                <a href="{{ route('admin.v2.financeiro.index') }}" class="admin-btn-soft">Limpar</a>
            </div>
        </form>
    </section>

    <section class="admin-data-table-wrapper hidden lg:block">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>Cobranca</th>
                    <th>Cliente</th>
                    <th>Pedido</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($financials as $financial)
                    @php
                        $order = $financial->order;
                        $vehicleLabel = $order?->vehicle ? trim($order->vehicle->brand . ' ' . $order->vehicle->model . ' ' . $order->vehicle->model_year) : 'Sem veiculo';
                    @endphp
                    <tr>
                        <td>
                            <div class="admin-row-title">{{ $financial->numero ?? $financial->numero_fatura ?? 'Sem numero' }}</div>
                            <div class="admin-row-meta">{{ $financial->descricao ?: 'Cobranca sem descricao' }}</div>
                        </td>
                        <td>
                            <div class="admin-row-title">{{ $order?->user?->razao_social ?? $order?->user?->name ?? 'Sem cliente' }}</div>
                            <div class="admin-row-meta">{{ $order?->user?->cnpj ?? $order?->user?->email ?? 'Sem documento' }}</div>
                        </td>
                        <td>
                            <div class="admin-row-title">{{ $order?->numero ?? 'Sem pedido' }}</div>
                            <div class="admin-row-meta">{{ $vehicleLabel }}</div>
                        </td>
                        <td>
                            <div class="admin-row-title text-emerald-700">R$ {{ number_format((float) $financial->valor, 2, ',', '.') }}</div>
                            <div class="admin-row-meta">{{ $paymentMethodOptions[$financial->forma_pagamento] ?? $financial->forma_pagamento ?? 'Forma nao informada' }}</div>
                        </td>
                        <td>
                            <div class="admin-row-title {{ $financial->esta_vencido ? 'text-red-600' : '' }}">{{ $financial->data_vencimento?->format('d/m/Y') ?? 'Sem vencimento' }}</div>
                            <div class="admin-row-meta">{{ $financial->data_pagamento?->format('d/m/Y') ? 'Pago em ' . $financial->data_pagamento->format('d/m/Y') : 'Sem pagamento registrado' }}</div>
                        </td>
                        <td>
                            <div class="admin-row-title">{{ $statusOptions[$financial->status] ?? $financial->status }}</div>
                            <div class="admin-row-meta">{{ $financial->criadoPor?->name ?? 'Sistema' }}</div>
                        </td>
                        <td>
                            @include('admin.financeiro.partials.actions', ['financial' => $financial])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-sm font-semibold text-slate-500">Nenhuma cobranca encontrada para o filtro atual.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="admin-mobile-list">
        @forelse($financials as $financial)
            @php($order = $financial->order)
            <article class="admin-order-card">
                <div class="admin-order-card-header">
                    <div>
                        <h3 class="admin-row-title">{{ $financial->numero ?? $financial->numero_fatura ?? 'Sem numero' }}</h3>
                        <p class="admin-row-meta">{{ $order?->user?->razao_social ?? $order?->user?->name ?? 'Sem cliente' }}</p>
                    </div>
                    <span class="admin-status-badge is-confirmed">{{ $statusOptions[$financial->status] ?? $financial->status }}</span>
                </div>

                <div class="admin-order-card-grid">
                    <div>
                        <span class="admin-detail-label">Valor</span>
                        <span class="admin-detail-value">R$ {{ number_format((float) $financial->valor, 2, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="admin-detail-label">Vencimento</span>
                        <span class="admin-detail-value">{{ $financial->data_vencimento?->format('d/m/Y') ?? 'Sem data' }}</span>
                    </div>
                    <div>
                        <span class="admin-detail-label">Pedido</span>
                        <span class="admin-detail-value">{{ $order?->numero ?? 'Sem pedido' }}</span>
                    </div>
                    <div>
                        <span class="admin-detail-label">Pagamento</span>
                        <span class="admin-detail-value">{{ $paymentMethodOptions[$financial->forma_pagamento] ?? $financial->forma_pagamento ?? 'n/d' }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    @include('admin.financeiro.partials.actions', ['financial' => $financial])
                </div>
            </article>
        @empty
            <article class="admin-empty-state">Nenhuma cobranca encontrada para o filtro atual.</article>
        @endforelse
    </section>

    <section class="mt-6 admin-card">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag admin-tag-migration">sem fatura</span>
                <h2 class="mt-3 admin-section-title">Pedidos prontos para faturar</h2>
                <p class="admin-section-note">Atalho operacional para gerar cobrancas nos pedidos que ja passaram do ponto de confirmacao.</p>
            </div>
        </div>

        <div class="grid gap-3 lg:grid-cols-2 xl:grid-cols-4 mt-4">
            @forelse($ordersWithoutFinancial as $order)
                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="admin-row-title">{{ $order->numero }}</div>
                    <div class="admin-row-meta mt-1">{{ $order->user?->razao_social ?? $order->user?->name ?? 'Sem cliente' }}</div>
                    <div class="admin-row-meta">{{ $order->vehicle ? trim($order->vehicle->brand . ' ' . $order->vehicle->model . ' ' . $order->vehicle->model_year) : 'Sem veiculo' }}</div>
                    <div class="mt-3 text-sm font-bold text-emerald-700">R$ {{ number_format((float) $order->valor_compra, 2, ',', '.') }}</div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.v2.orders.generate-invoice', $order) }}">
                            @csrf
                            <button type="submit" class="admin-btn-primary">Gerar fatura</button>
                        </form>
                        <a href="{{ route('admin.v2.orders.show', $order) }}" class="admin-btn-soft">Pedido</a>
                    </div>
                </article>
            @empty
                <article class="admin-empty-state lg:col-span-2 xl:col-span-4">Nenhum pedido pendente de faturamento neste momento.</article>
            @endforelse
        </div>
    </section>

    <div class="mt-4">
        {{ $financials->links() }}
    </div>
@endsection