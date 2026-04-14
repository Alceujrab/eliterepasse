@extends('admin.layouts.app')

@php
    $pageTitle = 'Contratos (Admin v2)';
    $pageSubtitle = 'Gestão de assinaturas e rastreio de contratos no novo painel.';
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
            <p class="admin-metric-label">Contratos no filtro</p>
            <p class="admin-metric-value">{{ number_format($summary['filteredTotal']) }}</p>
            <p class="admin-metric-footnote">Base total: {{ number_format($globalTotalContracts) }} contratos</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Em rascunho</p>
            <p class="admin-metric-value">{{ number_format($summary['draft']) }}</p>
            <p class="admin-metric-footnote">Ainda sem disparo para assinatura</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Aguardando assinatura</p>
            <p class="admin-metric-value">{{ number_format($summary['waiting']) }}</p>
            <p class="admin-metric-footnote">Links enviados aguardando acao do comprador</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Assinados</p>
            <p class="admin-metric-value">{{ number_format($summary['signed']) }}</p>
            <p class="admin-metric-footnote">Fechados e com trilha de assinatura registrada</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Valor filtrado</p>
            <p class="admin-metric-value">R$ {{ number_format($summary['grossVolume'], 0, ',', '.') }}</p>
            <p class="admin-metric-footnote">Soma do valor contratual dentro do recorte atual</p>
        </article>
    </section>

    <section class="mt-6 admin-card">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag {{ $hasActiveFilters ? 'admin-tag-migration' : 'admin-tag-new' }}">{{ $hasActiveFilters ? 'fila filtrada' : 'assinaturas centrais' }}</span>
                <h2 class="mt-3 admin-section-title">Central de contratos</h2>
                <p class="admin-section-note">Monitore disparos, token de assinatura, status contratual e conversao para contratos assinados no mesmo fluxo.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.contracts.index', request()->query()) }}" class="admin-btn-soft">Atualizar</a>
                <a href="/admin/contracts" class="admin-btn-soft">Abrir legado</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.v2.contracts.index') }}" class="admin-filter-grid md:items-end">
            <div>
                <label for="contracts-q" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Busca</label>
                <input id="contracts-q" name="q" value="{{ $search }}" placeholder="Número, cliente, placa..."
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
            </div>

            <div>
                <label for="contracts-status" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Status</label>
                <select id="contracts-status" name="status"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
                    <option value="">Todos</option>
                    @foreach($statusOptions as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="admin-btn-primary">Filtrar</button>
                <a href="{{ route('admin.v2.contracts.index') }}" class="admin-btn-soft">Limpar</a>
            </div>
        </form>

        <div class="admin-quick-filters">
            <a href="{{ route('admin.v2.contracts.index', $search !== '' ? ['q' => $search] : []) }}" class="admin-filter-chip {{ $status === '' ? 'is-active' : '' }}">
                <span>Todos</span>
                <span>{{ number_format($globalTotalContracts) }}</span>
            </a>
            @foreach($statusOptions as $statusKey => $statusLabel)
                <a href="{{ route('admin.v2.contracts.index', array_filter(['status' => $statusKey, 'q' => $search !== '' ? $search : null])) }}" class="admin-filter-chip {{ $status === $statusKey ? 'is-active' : '' }}">
                    <span>{{ $statusLabel }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="admin-data-table-wrapper hidden lg:block">
        <table class="admin-data-table">
            <thead>
            <tr>
                <th>Contrato</th>
                <th>Comprador</th>
                <th>Veiculo</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Assinatura</th>
                <th>Acoes</th>
            </tr>
            </thead>
            <tbody>
            @forelse($contracts as $contract)
                @php
                    $buyerSignature = $contract->assinaturaComprador;
                    $vehicleLabel = $contract->vehicle
                        ? trim("{$contract->vehicle->brand} {$contract->vehicle->model} {$contract->vehicle->model_year}")
                        : 'Veiculo nao vinculado';
                @endphp
                <tr>
                    <td>
                        <div class="admin-row-title">{{ $contract->numero }}</div>
                        <div class="admin-row-meta">{{ $contract->created_at?->format('d/m/Y H:i') }} · template {{ $contract->template }}</div>
                    </td>
                    <td>
                        <div class="admin-row-title">{{ $contract->user?->razao_social ?? $contract->user?->name ?? '—' }}</div>
                        <div class="admin-row-meta">{{ $contract->user?->cnpj ?? $contract->user?->email }}</div>
                    </td>
                    <td>
                        <div class="admin-row-title">{{ $vehicleLabel }}</div>
                        <div class="admin-row-meta">{{ $contract->vehicle?->plate ?? 'Sem placa' }}</div>
                    </td>
                    <td>
                        <div class="admin-row-title text-emerald-700">R$ {{ number_format((float) $contract->valor_contrato, 2, ',', '.') }}</div>
                        <div class="admin-row-meta">{{ $contract->forma_pagamento ?: 'Forma de pagamento nao informada' }}</div>
                    </td>
                    <td>
                        <div class="admin-row-title">{{ $statusOptions[$contract->status] ?? $contract->status }}</div>
                        <div class="admin-row-meta">{{ $contract->enviado_em ? 'Enviado em ' . $contract->enviado_em->format('d/m/Y H:i') : 'Ainda nao enviado' }}</div>
                    </td>
                    <td>
                        <div class="admin-row-title">{{ $contract->assinado_em?->format('d/m/Y H:i') ?? 'Nao assinado' }}</div>
                        <div class="admin-row-meta">
                            @if($buyerSignature)
                                Token {{ $buyerSignature->token_assinatura ? 'gerado' : 'ausente' }}
                            @else
                                Sem slot de assinatura
                            @endif
                        </div>
                    </td>
                    <td>
                        @include('admin.contracts.partials.actions', ['contract' => $contract])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-3 py-6 text-center text-sm font-semibold text-slate-500">Nenhum contrato encontrado para o filtro atual.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section class="admin-mobile-list">
        @forelse($contracts as $contract)
            @php
                $vehicleLabel = $contract->vehicle
                    ? trim("{$contract->vehicle->brand} {$contract->vehicle->model} {$contract->vehicle->model_year}")
                    : 'Veiculo nao vinculado';
            @endphp
            <article class="admin-order-card">
                <div class="admin-order-card-header">
                    <div>
                        <h3 class="admin-row-title">{{ $contract->numero }}</h3>
                        <p class="admin-row-meta">{{ $contract->user?->razao_social ?? $contract->user?->name ?? 'Sem comprador' }}</p>
                    </div>
                    <span class="admin-status-badge is-confirmed">{{ $statusOptions[$contract->status] ?? $contract->status }}</span>
                </div>

                <div class="admin-order-card-grid">
                    <div>
                        <span class="admin-detail-label">Veiculo</span>
                        <span class="admin-detail-value">{{ $vehicleLabel }}</span>
                    </div>
                    <div>
                        <span class="admin-detail-label">Valor</span>
                        <span class="admin-detail-value">R$ {{ number_format((float) $contract->valor_contrato, 2, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="admin-detail-label">Enviado</span>
                        <span class="admin-detail-value">{{ $contract->enviado_em?->format('d/m/Y H:i') ?? 'Nao' }}</span>
                    </div>
                    <div>
                        <span class="admin-detail-label">Assinado</span>
                        <span class="admin-detail-value">{{ $contract->assinado_em?->format('d/m/Y H:i') ?? 'Nao' }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    @include('admin.contracts.partials.actions', ['contract' => $contract])
                </div>
            </article>
        @empty
            <article class="admin-empty-state">Nenhum contrato encontrado para o filtro atual.</article>
        @endforelse
    </section>

    <div class="mt-4">
        {{ $contracts->links() }}
    </div>
@endsection
