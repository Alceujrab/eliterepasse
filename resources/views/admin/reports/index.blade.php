@extends('admin.layouts.app')

@php
    $pageTitle = 'Relatorios e analises';
    $pageSubtitle = 'Indicadores de estoque, receita, conversao e crescimento em um unico workspace.';
    $maxRevenue = max(1, $revenueSeries->max('total') ?? 1);
    $maxClients = max(1, $newClientsSeries->max('quantity') ?? 1);
@endphp

@section('content')
    <section class="admin-metrics-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Receita no periodo</p>
            <p class="admin-metric-value">R$ {{ number_format($financeSummary['grossRevenue'], 0, ',', '.') }}</p>
            <p class="admin-metric-footnote">Ticket medio: R$ {{ number_format($financeSummary['averageTicket'], 0, ',', '.') }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Conversao operacional</p>
            <p class="admin-metric-value">{{ number_format($commercialSummary['conversionRate'], 1, ',', '.') }}%</p>
            <p class="admin-metric-footnote">Base: {{ number_format($financeSummary['ordersTotal']) }} pedidos no periodo</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Estoque disponivel</p>
            <p class="admin-metric-value">{{ number_format($inventorySummary['available']) }}</p>
            <p class="admin-metric-footnote">R$ {{ number_format($inventorySummary['availableValue'], 0, ',', '.') }} em estoque aberto</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Financeiro em risco</p>
            <p class="admin-metric-value">{{ number_format($financeSummary['overdueFinancials']) }}</p>
            <p class="admin-metric-footnote">Cobrancas vencidas para tratamento</p>
        </article>
    </section>

    <section class="mt-6 admin-card">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag admin-tag-new">analytics v2</span>
                <h2 class="mt-3 admin-section-title">Painel analitico</h2>
                <p class="admin-section-note">Periodo analisado de {{ $start->format('d/m/Y') }} ate {{ $end->format('d/m/Y') }}.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.reports.export-csv', ['periodo' => $periodDays]) }}" class="admin-btn-primary">Exportar CSV</a>
                <a href="{{ route('admin.v2.reports.export-pdf', ['periodo' => $periodDays]) }}" class="admin-btn-soft">Exportar PDF</a>
                <a href="/admin/relatorios" class="admin-btn-soft">Abrir legado</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.v2.reports.index') }}" class="mt-5 flex flex-wrap items-end gap-3">
            <div>
                <label for="periodo" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Periodo</label>
                <select id="periodo" name="periodo" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
                    @foreach([7 => '7 dias', 15 => '15 dias', 30 => '30 dias', 60 => '60 dias', 90 => '90 dias'] as $value => $label)
                        <option value="{{ $value }}" @selected($periodDays === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="admin-btn-primary">Atualizar</button>
        </form>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
        <article class="admin-card">
            <h2 class="admin-section-title">Receita por dia</h2>
            <p class="admin-section-note">Volume faturado em pedidos confirmados, faturados e pagos.</p>

            <div class="mt-5 grid h-72 grid-cols-[repeat(auto-fit,minmax(32px,1fr))] items-end gap-3">
                @forelse($revenueSeries as $point)
                    <div class="flex flex-col items-center gap-2">
                        <span class="text-[11px] font-bold text-slate-400">{{ $point['quantity'] }}</span>
                        <div class="w-full rounded-t-2xl bg-gradient-to-t from-blue-600 via-sky-500 to-cyan-300" style="height: {{ max(16, (int) round(($point['total'] / $maxRevenue) * 220)) }}px"></div>
                        <span class="text-[11px] font-bold text-slate-500">{{ $point['label'] }}</span>
                    </div>
                @empty
                    <div class="col-span-full admin-empty-state">Nao houve faturamento no periodo selecionado.</div>
                @endforelse
            </div>
        </article>

        <article class="admin-card">
            <h2 class="admin-section-title">Crescimento de clientes</h2>
            <p class="admin-section-note">Novos lojistas por dia dentro da janela analisada.</p>

            <div class="mt-5 admin-stack">
                @forelse($newClientsSeries as $point)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm font-semibold text-slate-600">
                            <span>{{ $point['label'] }}</span>
                            <span>{{ $point['quantity'] }}</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 via-orange-400 to-rose-500" style="width: {{ max(6, (int) round(($point['quantity'] / $maxClients) * 100)) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="admin-empty-state">Sem novos clientes no periodo selecionado.</div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-3">
        <article class="admin-card">
            <span class="admin-tag admin-tag-new">estoque</span>
            <h2 class="mt-3 admin-section-title">Resumo de estoque</h2>
            <div class="mt-4 admin-stack text-sm text-slate-600">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Total cadastrado: <strong>{{ number_format($inventorySummary['total']) }}</strong></div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Reservados: <strong>{{ number_format($inventorySummary['reserved']) }}</strong> · Vendidos: <strong>{{ number_format($inventorySummary['sold']) }}</strong></div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Preco medio do estoque aberto: <strong>R$ {{ number_format($inventorySummary['averageValue'], 0, ',', '.') }}</strong></div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Veiculos abaixo da FIPE: <strong>{{ number_format($inventorySummary['belowFipe']) }}</strong></div>
            </div>
        </article>

        <article class="admin-card">
            <span class="admin-tag admin-tag-new">financeiro</span>
            <h2 class="mt-3 admin-section-title">Resumo financeiro</h2>
            <div class="mt-4 admin-stack text-sm text-slate-600">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Pedidos confirmados: <strong>{{ number_format($financeSummary['confirmed']) }}</strong></div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Faturados: <strong>{{ number_format($financeSummary['invoiced']) }}</strong> · Pagos: <strong>{{ number_format($financeSummary['paid']) }}</strong></div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Cancelados: <strong>{{ number_format($financeSummary['cancelled']) }}</strong></div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Financeiros em aberto: <strong>{{ number_format($financeSummary['openFinancials']) }}</strong></div>
            </div>
        </article>

        <article class="admin-card">
            <span class="admin-tag admin-tag-new">comercial</span>
            <h2 class="mt-3 admin-section-title">Sinais comerciais</h2>
            <div class="mt-4 admin-stack text-sm text-slate-600">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Novos clientes: <strong>{{ number_format($commercialSummary['newClients']) }}</strong></div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Tickets urgentes ativos: <strong>{{ number_format($commercialSummary['urgentTickets']) }}</strong></div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Conversao operacional: <strong>{{ number_format($commercialSummary['conversionRate'], 1, ',', '.') }}%</strong></div>
            </div>
        </article>
    </section>

    <section class="mt-6 admin-card">
        <h2 class="admin-section-title">Top vendas por veiculo</h2>
        <p class="admin-section-note">Ranking por quantidade e valor vendido no periodo.</p>

        <div class="mt-4 admin-data-table-wrapper">
            <table class="admin-data-table">
                <thead>
                    <tr>
                        <th>Veiculo</th>
                        <th>Placa</th>
                        <th>Quantidade</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topSales as $sale)
                        <tr>
                            <td><div class="admin-row-title">{{ $sale['vehicle'] }}</div></td>
                            <td><div class="admin-row-meta">{{ $sale['plate'] }}</div></td>
                            <td><div class="admin-row-title">{{ number_format($sale['quantity']) }}</div></td>
                            <td><div class="admin-row-title text-emerald-700">R$ {{ number_format($sale['total'], 2, ',', '.') }}</div></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-6 text-center text-sm font-semibold text-slate-500">Sem vendas suficientes para ranking.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection