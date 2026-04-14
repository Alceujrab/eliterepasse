@extends('admin.layouts.app')

@php
    $pageTitle = $contract->numero . ' · Contrato';
    $pageSubtitle = 'Workspace de assinatura com contexto de comprador, veiculo, token e evidencias da formalizacao.';
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
            <p class="admin-metric-label">Status atual</p>
            <p class="admin-metric-value text-[1.65rem]"><span class="admin-status-badge is-confirmed">{{ $statusOptions[$contract->status] ?? $contract->status }}</span></p>
            <p class="admin-metric-footnote">Criado em {{ $contract->created_at?->format('d/m/Y H:i') }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Valor contratual</p>
            <p class="admin-metric-value">R$ {{ number_format((float) $contract->valor_contrato, 2, ',', '.') }}</p>
            <p class="admin-metric-footnote">{{ $contract->forma_pagamento ?: 'Forma de pagamento nao informada' }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Envio</p>
            <p class="admin-metric-value">{{ $summary['wasSent'] ? 'Enviado' : 'Nao enviado' }}</p>
            <p class="admin-metric-footnote">{{ $contract->enviado_em?->format('d/m/Y H:i') ?? 'Sem disparo registrado' }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Assinatura do comprador</p>
            <p class="admin-metric-value">{{ $summary['hasBuyerSignature'] ? 'Recebida' : 'Pendente' }}</p>
            <p class="admin-metric-footnote">{{ $contract->assinado_em?->format('d/m/Y H:i') ?? 'Sem assinatura ainda' }}</p>
        </article>
    </section>

    <section class="mt-6 admin-detail-grid">
        <div class="admin-stack">
            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <span class="admin-tag admin-tag-new">detalhe v2</span>
                        <h2 class="mt-3 admin-section-title">Resumo do contrato</h2>
                        <p class="admin-section-note">Consolida comprador, veiculo, pedido vinculado e os metadados do processo de assinatura.</p>
                    </div>
                    <div class="admin-toolbar-actions">
                        <a href="{{ route('admin.v2.contracts.index') }}" class="admin-btn-soft">Voltar para fila</a>
                        <a href="/admin/contracts/{{ $contract->id }}" class="admin-btn-soft">Abrir legado</a>
                    </div>
                </div>

                <div class="admin-info-grid mt-5">
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Comprador</span>
                        <div class="admin-detail-value">{{ $contract->user?->razao_social ?? $contract->user?->name ?? 'Nao informado' }}</div>
                        <div class="admin-row-meta">{{ $contract->user?->cnpj ?? $contract->user?->email ?? 'Sem documento' }}</div>
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Veiculo</span>
                        <div class="admin-detail-value">{{ $contract->vehicle ? trim($contract->vehicle->brand . ' ' . $contract->vehicle->model . ' ' . $contract->vehicle->model_year) : 'Nao vinculado' }}</div>
                        <div class="admin-row-meta">{{ $contract->vehicle?->plate ?? 'Sem placa' }}</div>
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Pedido vinculado</span>
                        <div class="admin-detail-value">{{ $contract->order?->numero ?? 'Sem pedido' }}</div>
                        <div class="admin-row-meta">{{ $contract->order?->paymentMethod?->nome ?? $contract->order?->paymentMethod?->name ?? 'Pagamento nao informado' }}</div>
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Gerado por</span>
                        <div class="admin-detail-value">{{ $contract->createdBy?->name ?? 'Sistema' }}</div>
                        <div class="admin-row-meta">Template {{ $contract->template }}</div>
                    </article>
                </div>
            </section>

            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <h2 class="admin-section-title">Assinatura e evidencias</h2>
                        <p class="admin-section-note">Token, data, local e identificadores tecnicos da assinatura do comprador.</p>
                    </div>
                </div>

                <div class="admin-info-grid mt-5">
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Token comprador</span>
                        <div class="admin-detail-value">{{ $contract->assinaturaComprador?->token_assinatura ? 'Gerado' : 'Nao gerado' }}</div>
                        <div class="admin-row-meta">{{ $summary['hasToken'] ? 'Link seguro disponivel para disparo' : 'Sem link de assinatura' }}</div>
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Assinado em</span>
                        <div class="admin-detail-value">{{ $contract->assinado_em?->format('d/m/Y H:i') ?? 'Ainda nao assinado' }}</div>
                        <div class="admin-row-meta">{{ $contract->assinaturaComprador?->assinado_em?->format('d/m/Y H:i') ?? 'Sem confirmacao do comprador' }}</div>
                    </article>
                    <article class="admin-info-card md:col-span-2">
                        <span class="admin-detail-label">Local da assinatura</span>
                        <div class="admin-detail-value">{{ $contract->endereco_assinatura ?: ($contract->assinaturaComprador?->endereco_geo ?: 'Sem local registrado') }}</div>
                        <div class="admin-row-meta">IP {{ $contract->ip_assinatura ?: ($contract->assinaturaComprador?->ip ?: 'n/d') }}</div>
                    </article>
                </div>
            </section>

            <section class="admin-card">
                <h2 class="admin-section-title">Snapshots do contrato</h2>
                <p class="admin-section-note">Dados congelados do comprador e do veiculo no momento da geracao do contrato.</p>

                <div class="grid gap-4 xl:grid-cols-2 mt-4">
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Comprador snapshot</span>
                        <div class="admin-detail-value">{{ $contract->dados_comprador['razao_social'] ?? $contract->dados_comprador['name'] ?? 'Sem snapshot' }}</div>
                        <div class="admin-row-meta">{{ $contract->dados_comprador['cnpj'] ?? $contract->dados_comprador['email'] ?? 'Sem documento' }}</div>
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Veiculo snapshot</span>
                        <div class="admin-detail-value">{{ trim(($contract->dados_veiculo['brand'] ?? '') . ' ' . ($contract->dados_veiculo['model'] ?? '') . ' ' . ($contract->dados_veiculo['model_year'] ?? '')) ?: 'Sem snapshot' }}</div>
                        <div class="admin-row-meta">{{ $contract->dados_veiculo['plate'] ?? 'Sem placa' }}</div>
                    </article>
                </div>
            </section>
        </div>

        <aside class="admin-stack">
            <section class="admin-card">
                <span class="admin-tag admin-tag-new">acoes do contrato</span>
                <h2 class="mt-3 admin-section-title">Assinatura</h2>
                <p class="admin-section-note">Dispare novamente o link ou recupere a URL segura do comprador.</p>
                <div class="mt-4">
                    @include('admin.contracts.partials.actions', ['contract' => $contract])
                </div>
            </section>

            <section class="admin-card">
                <h2 class="admin-section-title">Checklist rapido</h2>
                <div class="mt-4 admin-stack text-sm text-slate-600">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $summary['hasToken'] ? 'Token do comprador disponivel.' : 'Contrato sem token de assinatura.' }}</div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $summary['wasSent'] ? 'Link de assinatura ja foi disparado.' : 'Contrato ainda nao foi enviado.' }}</div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $summary['hasBuyerSignature'] ? 'Assinatura do comprador registrada.' : 'Assinatura do comprador pendente.' }}</div>
                </div>
            </section>
        </aside>
    </section>
@endsection