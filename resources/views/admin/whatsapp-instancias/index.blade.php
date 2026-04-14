@extends('admin.layouts.app')

@php
    $pageTitle = 'WhatsApp Instancias';
    $pageSubtitle = 'Operacao da Evolution com foco em conectividade, QR Code e diagnostico rapido.';
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
            <p class="admin-metric-label">Instancias no filtro</p>
            <p class="admin-metric-value">{{ number_format($summary['filteredTotal']) }}</p>
            <p class="admin-metric-footnote">Base total: {{ number_format($globalTotalInstances) }} instancias</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Conectadas</p>
            <p class="admin-metric-value">{{ number_format($summary['connected']) }}</p>
            <p class="admin-metric-footnote">Sessao aberta e pronta para envio</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Conectando</p>
            <p class="admin-metric-value">{{ number_format($summary['connecting']) }}</p>
            <p class="admin-metric-footnote">Aguardando QR ou pareamento</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Padrao</p>
            <p class="admin-metric-value">{{ number_format($summary['default']) }}</p>
            <p class="admin-metric-footnote">Instancia principal do sistema</p>
        </article>
    </section>

    <section class="mt-6 admin-card">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag {{ $hasActiveFilters ? 'admin-tag-migration' : 'admin-tag-new' }}">{{ $hasActiveFilters ? 'fila filtrada' : 'evolution central' }}</span>
                <h2 class="mt-3 admin-section-title">Central de instancias</h2>
                <p class="admin-section-note">Cadastre, teste, abra QR Code e monitore status das instancias usadas pelas notificacoes e inbox.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.whatsapp-instancias.create') }}" class="admin-btn-primary">Nova instancia</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.v2.whatsapp-instancias.index') }}" class="admin-filter-grid md:grid-cols-[1fr_220px_220px_auto] md:items-end mt-5">
            <div>
                <label for="wa-q" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Busca</label>
                <input id="wa-q" name="q" value="{{ $search }}" placeholder="Nome, instancia ou URL"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
            </div>
            <div>
                <label for="wa-status" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Status</label>
                <select id="wa-status" name="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
                    <option value="">Todos</option>
                    @foreach($statusOptions as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="wa-active" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Ativa</label>
                <select id="wa-active" name="active" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
                    <option value="">Todas</option>
                    <option value="1" @selected($active === '1')>Ativas</option>
                    <option value="0" @selected($active === '0')>Inativas</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="admin-btn-primary">Filtrar</button>
                <a href="{{ route('admin.v2.whatsapp-instancias.index') }}" class="admin-btn-soft">Limpar</a>
            </div>
        </form>
    </section>

    <section class="admin-data-table-wrapper hidden lg:block">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>Instancia</th>
                    <th>Evolution ID</th>
                    <th>URL Base</th>
                    <th>Status</th>
                    <th>Flags</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($instances as $instance)
                    <tr>
                        <td>
                            <div class="admin-row-title">{{ $instance->nome }}</div>
                            <div class="admin-row-meta">{{ $instance->verificado_em?->diffForHumans() ?? 'Nunca verificada' }}</div>
                        </td>
                        <td>
                            <div class="admin-row-title font-mono">{{ $instance->instancia }}</div>
                        </td>
                        <td>
                            <div class="admin-row-title">{{ $instance->url_base }}</div>
                        </td>
                        <td>
                            <div class="admin-row-title">{{ $instance->status_label }}</div>
                            <div class="admin-row-meta">{{ $instance->status_conexao ?: 'nao verificado' }}</div>
                        </td>
                        <td>
                            <div class="admin-row-title">{{ $instance->ativo ? 'Ativa' : 'Inativa' }}</div>
                            <div class="admin-row-meta">{{ $instance->padrao ? 'Instancia padrao' : 'Instancia secundaria' }}</div>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.v2.whatsapp-instancias.show', $instance) }}" class="admin-btn-soft">Abrir v2</a>
                                <form method="POST" action="{{ route('admin.v2.whatsapp-instancias.test-connection', $instance) }}">@csrf<button type="submit" class="admin-btn-soft">Testar</button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center text-sm font-semibold text-slate-500">Nenhuma instancia encontrada para o filtro atual.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="admin-mobile-list">
        @forelse($instances as $instance)
            <article class="admin-order-card">
                <div class="admin-order-card-header">
                    <div>
                        <h3 class="admin-row-title">{{ $instance->nome }}</h3>
                        <p class="admin-row-meta font-mono">{{ $instance->instancia }}</p>
                    </div>
                    <span class="admin-status-badge is-confirmed">{{ $instance->status_label }}</span>
                </div>
                <div class="admin-order-card-grid">
                    <div>
                        <span class="admin-detail-label">URL</span>
                        <span class="admin-detail-value">{{ $instance->url_base }}</span>
                    </div>
                    <div>
                        <span class="admin-detail-label">Flags</span>
                        <span class="admin-detail-value">{{ $instance->padrao ? 'Padrao' : 'Secundaria' }} · {{ $instance->ativo ? 'Ativa' : 'Inativa' }}</span>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('admin.v2.whatsapp-instancias.show', $instance) }}" class="admin-btn-soft">Abrir v2</a>
                    <form method="POST" action="{{ route('admin.v2.whatsapp-instancias.test-connection', $instance) }}">@csrf<button type="submit" class="admin-btn-soft">Testar</button></form>
                </div>
            </article>
        @empty
            <article class="admin-empty-state">Nenhuma instancia encontrada para o filtro atual.</article>
        @endforelse
    </section>

    <div class="mt-4">
        {{ $instances->links() }}
    </div>
@endsection