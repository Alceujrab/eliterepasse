@extends('admin.layouts.app')

@php
    $pageTitle = 'Templates de e-mail';
    $pageSubtitle = 'Central de modelos transacionais com preview, controle de ativacao e slugs protegidos.';
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
            <p class="admin-metric-label">Templates no filtro</p>
            <p class="admin-metric-value">{{ number_format($summary['filteredTotal']) }}</p>
            <p class="admin-metric-footnote">Base total: {{ number_format($globalTotalTemplates) }} modelos</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Ativos</p>
            <p class="admin-metric-value">{{ number_format($summary['active']) }}</p>
            <p class="admin-metric-footnote">Usados pelo sistema quando o slug e encontrado</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Inativos</p>
            <p class="admin-metric-value">{{ number_format($summary['inactive']) }}</p>
            <p class="admin-metric-footnote">Fallback para conteudo padrao hardcoded</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Templates de sistema</p>
            <p class="admin-metric-value">{{ number_format($summary['system']) }}</p>
            <p class="admin-metric-footnote">Slugs protegidos contra exclusao logica no v2</p>
        </article>
    </section>

    <section class="mt-6 admin-card">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag {{ $hasActiveFilters ? 'admin-tag-migration' : 'admin-tag-new' }}">{{ $hasActiveFilters ? 'fila filtrada' : 'editor transacional' }}</span>
                <h2 class="mt-3 admin-section-title">Biblioteca de templates</h2>
                <p class="admin-section-note">Pesquise por slug, status ou assunto e abra o template para editar com preview operacional.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.email-templates.create') }}" class="admin-btn-primary">Novo template</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.v2.email-templates.index') }}" class="admin-filter-grid md:grid-cols-[1fr_220px_auto] md:items-end mt-5">
            <div>
                <label for="email-templates-q" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Busca</label>
                <input id="email-templates-q" name="q" value="{{ $search }}" placeholder="Nome, slug ou assunto"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
            </div>
            <div>
                <label for="email-templates-active" class="mb-1 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Status</label>
                <select id="email-templates-active" name="active" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium outline-none transition focus:border-blue-400">
                    <option value="">Todos</option>
                    <option value="1" @selected($active === '1')>Ativos</option>
                    <option value="0" @selected($active === '0')>Inativos</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="admin-btn-primary">Filtrar</button>
                <a href="{{ route('admin.v2.email-templates.index') }}" class="admin-btn-soft">Limpar</a>
            </div>
        </form>
    </section>

    <section class="admin-data-table-wrapper hidden lg:block">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>Template</th>
                    <th>Slug</th>
                    <th>Assunto</th>
                    <th>Status</th>
                    <th>Atualizado</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr>
                        <td>
                            <div class="admin-row-title">{{ $template->nome }}</div>
                            <div class="admin-row-meta">{{ $template->isSystemTemplate() ? 'Template de sistema' : 'Template customizado' }}</div>
                        </td>
                        <td>
                            <div class="admin-row-title font-mono">{{ $template->slug }}</div>
                        </td>
                        <td>
                            <div class="admin-row-title">{{ $template->assunto }}</div>
                        </td>
                        <td>
                            <div class="admin-row-title">{{ $template->ativo ? 'Ativo' : 'Inativo' }}</div>
                            <div class="admin-row-meta">{{ $template->ativo ? 'Usavel nas notificacoes' : 'Fallback no codigo' }}</div>
                        </td>
                        <td>
                            <div class="admin-row-title">{{ $template->updated_at?->format('d/m/Y H:i') }}</div>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.v2.email-templates.show', $template) }}" class="admin-btn-soft">Abrir v2</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center text-sm font-semibold text-slate-500">Nenhum template encontrado para o filtro atual.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="admin-mobile-list">
        @forelse($templates as $template)
            <article class="admin-order-card">
                <div class="admin-order-card-header">
                    <div>
                        <h3 class="admin-row-title">{{ $template->nome }}</h3>
                        <p class="admin-row-meta font-mono">{{ $template->slug }}</p>
                    </div>
                    <span class="admin-status-badge is-confirmed">{{ $template->ativo ? 'Ativo' : 'Inativo' }}</span>
                </div>
                <div class="admin-order-card-grid">
                    <div>
                        <span class="admin-detail-label">Assunto</span>
                        <span class="admin-detail-value">{{ $template->assunto }}</span>
                    </div>
                    <div>
                        <span class="admin-detail-label">Atualizado</span>
                        <span class="admin-detail-value">{{ $template->updated_at?->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('admin.v2.email-templates.show', $template) }}" class="admin-btn-soft">Abrir v2</a>
                </div>
            </article>
        @empty
            <article class="admin-empty-state">Nenhum template encontrado para o filtro atual.</article>
        @endforelse
    </section>

    <div class="mt-4">
        {{ $templates->links() }}
    </div>
@endsection