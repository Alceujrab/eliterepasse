@extends('admin.layouts.app')

@php
    $pageTitle = ($financial->numero ?? $financial->numero_fatura ?? 'Cobranca') . ' · Financeiro';
    $pageSubtitle = 'Resumo da cobranca, pedido vinculado, vencimento e links operacionais.';
@endphp

@section('content')
    @if(session('admin_success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('admin_success') }}</div>
    @endif

    @if(session('admin_warning'))
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">{{ session('admin_warning') }}</div>
    @endif

    <section class="admin-summary-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Status</p>
            <p class="admin-metric-value text-[1.65rem]"><span class="admin-status-badge is-confirmed">{{ $statusOptions[$financial->status] ?? $financial->status }}</span></p>
            <p class="admin-metric-footnote">Criado em {{ $financial->created_at?->format('d/m/Y H:i') }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Valor</p>
            <p class="admin-metric-value">R$ {{ number_format((float) $financial->valor, 2, ',', '.') }}</p>
            <p class="admin-metric-footnote">{{ $paymentMethodOptions[$financial->forma_pagamento] ?? $financial->forma_pagamento ?? 'Forma nao informada' }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Vencimento</p>
            <p class="admin-metric-value {{ $summary['isOverdue'] ? 'text-red-600' : '' }}">{{ $financial->data_vencimento?->format('d/m/Y') ?? 'Sem data' }}</p>
            <p class="admin-metric-footnote">{{ $summary['isOverdue'] ? 'Cobranca vencida' : 'Dentro do prazo ou sem vencimento' }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Pagamento</p>
            <p class="admin-metric-value">{{ $summary['isPaid'] ? 'Confirmado' : 'Pendente' }}</p>
            <p class="admin-metric-footnote">{{ $financial->data_pagamento?->format('d/m/Y') ?? 'Sem baixa registrada' }}</p>
        </article>
    </section>

    <section class="mt-6 admin-detail-grid">
        <div class="admin-stack">
            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <span class="admin-tag admin-tag-new">detalhe v2</span>
                        <h2 class="mt-3 admin-section-title">Resumo da cobranca</h2>
                        <p class="admin-section-note">Consolida identificacao da fatura, pedido, cliente e origem operacional.</p>
                    </div>
                    <div class="admin-toolbar-actions">
                        <a href="{{ route('admin.v2.financeiro.index') }}" class="admin-btn-soft">Voltar para fila</a>
                    </div>
                </div>

                <div class="admin-info-grid mt-5">
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Numero</span>
                        <div class="admin-detail-value">{{ $financial->numero ?? $financial->numero_fatura ?? 'Sem numero' }}</div>
                        <div class="admin-row-meta">{{ $financial->descricao ?: 'Sem descricao' }}</div>
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Cliente</span>
                        <div class="admin-detail-value">{{ $financial->order?->user?->razao_social ?? $financial->order?->user?->name ?? 'Sem cliente' }}</div>
                        <div class="admin-row-meta">{{ $financial->order?->user?->cnpj ?? $financial->order?->user?->email ?? 'Sem documento' }}</div>
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Pedido</span>
                        <div class="admin-detail-value">{{ $financial->order?->numero ?? 'Sem pedido' }}</div>
                        <div class="admin-row-meta">{{ $financial->order?->vehicle ? trim($financial->order->vehicle->brand . ' ' . $financial->order->vehicle->model . ' ' . $financial->order->vehicle->model_year) : 'Sem veiculo' }}</div>
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Criado por</span>
                        <div class="admin-detail-value">{{ $financial->criadoPor?->name ?? 'Sistema' }}</div>
                        <div class="admin-row-meta">Status do pedido: {{ \App\Models\Order::statusLabels()[$financial->order?->status] ?? $financial->order?->status ?? 'n/d' }}</div>
                    </article>
                </div>
            </section>

            <section class="admin-card">
                <h2 class="admin-section-title">Canais e liquidacao</h2>
                <p class="admin-section-note">Links externos, linha digitavel e identificadores da cobranca.</p>

                <div class="admin-info-grid mt-5">
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Boleto</span>
                        <div class="admin-detail-value">{{ $summary['hasBoletoLink'] ? 'Disponivel' : 'Nao informado' }}</div>
                        <div class="admin-row-meta">{{ $financial->boleto_url ?: 'Sem URL de boleto' }}</div>
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Invoice</span>
                        <div class="admin-detail-value">{{ $summary['hasInvoiceLink'] ? 'Disponivel' : 'Nao informada' }}</div>
                        <div class="admin-row-meta">{{ $financial->invoice_url ?: 'Sem invoice URL' }}</div>
                    </article>
                    <article class="admin-info-card md:col-span-2">
                        <span class="admin-detail-label">Linha digitavel</span>
                        <div class="admin-detail-value break-all">{{ $financial->digitable_line ?: 'Nao informada' }}</div>
                        <div class="admin-row-meta">Nota fiscal {{ $financial->nota_fiscal_numero ?: 'nao vinculada' }}</div>
                    </article>
                </div>
            </section>
        </div>

        <aside class="admin-stack">
            <section class="admin-card">
                <span class="admin-tag admin-tag-new">acoes financeiras</span>
                <h2 class="mt-3 admin-section-title">Operacao</h2>
                <p class="admin-section-note">Abra o pedido, confirme a baixa ou navegue para os links da cobranca.</p>
                <div class="mt-4">
                    @include('admin.financeiro.partials.actions', ['financial' => $financial])
                </div>
            </section>

            <section class="admin-card">
                <h2 class="admin-section-title">Checklist rapido</h2>
                <div class="mt-4 admin-stack text-sm text-slate-600">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $summary['isPaid'] ? 'Pagamento ja conciliado.' : 'Pagamento ainda pendente.' }}</div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $summary['isOverdue'] ? 'Cobranca vencida e exige tratamento.' : 'Sem atraso identificado.' }}</div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $summary['hasBoletoLink'] || $summary['hasInvoiceLink'] ? 'Existe link externo de cobranca.' : 'Sem link externo cadastrado.' }}</div>
                </div>
            </section>
        </aside>
    </section>
@endsection