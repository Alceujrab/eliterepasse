@extends('admin.layouts.app')

@php
    $pageTitle = 'Clientes (Admin v2)';
    $pageSubtitle = 'Workspace comercial para triagem de cadastro, aprovacao e bloqueio com contexto operacional do lojista.';

    $statusClassMap = [
        'pendente' => 'is-awaiting',
        'ativo' => 'is-paid',
        'bloqueado' => 'is-cancelled',
    ];
@endphp

@section('content')
    @if(session('admin_success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('admin_success') }}</div>
    @endif

    @if(session('admin_warning'))
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">{{ session('admin_warning') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <section class="admin-metrics-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Clientes no filtro</p>
            <p class="admin-metric-value">{{ number_format($summary['filteredTotal']) }}</p>
            <p class="admin-metric-footnote">Base total: {{ number_format($globalTotalClients) }} cadastros</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Pendentes</p>
            <p class="admin-metric-value">{{ number_format($summary['pending']) }}</p>
            <p class="admin-metric-footnote">Cadastros aguardando liberacao comercial</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Ativos</p>
            <p class="admin-metric-value">{{ number_format($summary['active']) }}</p>
            <p class="admin-metric-footnote">Lojistas com acesso operacional liberado</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Bloqueados</p>
            <p class="admin-metric-value">{{ number_format($summary['blocked']) }}</p>
            <p class="admin-metric-footnote">Contas travadas para revisao do time</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Aprovados hoje</p>
            <p class="admin-metric-value">{{ number_format($summary['approvedToday']) }}</p>
            <p class="admin-metric-footnote">Movimento diario de onboarding</p>
        </article>
    </section>

    <section class="mt-6 admin-card">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag {{ $hasActiveFilters ? 'admin-tag-migration' : 'admin-tag-new' }}">{{ $hasActiveFilters ? 'fila filtrada' : 'cadastro comercial' }}</span>
                <h2 class="mt-3 admin-section-title">Central de clientes</h2>
                <p class="admin-section-note">Priorize aprovacao, trate bloqueios e identifique rapidamente quem ja esta operando com pedidos, tickets e documentos.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.clients.index', request()->query()) }}" class="admin-btn-soft">Atualizar</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.v2.clients.index') }}" class="admin-filter-grid-wide md:items-end">
            <div>
                <label for="clients-q" class="admin-field-label">Busca</label>
                <input id="clients-q" name="q" value="{{ $search }}" placeholder="Empresa, responsavel, email, CNPJ, cidade..." class="admin-input">
            </div>
            <div>
                <label for="clients-status" class="admin-field-label">Status</label>
                <select id="clients-status" name="status" class="admin-select">
                    <option value="">Todos</option>
                    @foreach($statusOptions as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="clients-approval" class="admin-field-label">Aprovacao</label>
                <select id="clients-approval" name="approval" class="admin-select">
                    <option value="">Todos</option>
                    @foreach($approvalOptions as $approvalKey => $approvalLabel)
                        <option value="{{ $approvalKey }}" @selected($approval === $approvalKey)>{{ $approvalLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="admin-btn-primary">Filtrar</button>
                <a href="{{ route('admin.v2.clients.index') }}" class="admin-btn-soft">Limpar</a>
            </div>
        </form>

        <div class="admin-quick-filters">
            <a href="{{ route('admin.v2.clients.index', array_filter(['q' => $search !== '' ? $search : null, 'approval' => $approval !== '' ? $approval : null])) }}" class="admin-filter-chip {{ $status === '' ? 'is-active' : '' }}">
                <span>Todos</span>
                <span>{{ number_format($globalTotalClients) }}</span>
            </a>
            @foreach($statusOptions as $statusKey => $statusLabel)
                <a href="{{ route('admin.v2.clients.index', array_filter(['status' => $statusKey, 'q' => $search !== '' ? $search : null, 'approval' => $approval !== '' ? $approval : null])) }}" class="admin-filter-chip {{ $status === $statusKey ? 'is-active' : '' }}">
                    <span>{{ $statusLabel }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="mt-6 admin-split-grid">
        <div class="admin-stack">
            <section class="admin-data-table-wrapper hidden lg:block">
                <table class="admin-data-table">
                    <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>Contato</th>
                        <th>Cidade/UF</th>
                        <th>Operacao</th>
                        <th>Status</th>
                        <th>Acoes</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($clients as $client)
                        @php
                            $statusLabel = $statusOptions[$client->status] ?? $client->status;
                            $statusClass = $statusClassMap[$client->status] ?? 'is-pending';
                            $companyLabel = $client->razao_social ?? $client->nome_fantasia ?? $client->name ?? 'Cliente sem nome';
                        @endphp
                        <tr>
                            <td>
                                <div class="admin-row-title">{{ $companyLabel }}</div>
                                <div class="admin-row-meta">{{ $client->nome_fantasia ?: ($client->name ?: 'Sem fantasia') }} · {{ $client->cnpj ?: ($client->cpf ?: 'Sem documento') }}</div>
                            </td>
                            <td>
                                <div class="admin-row-title">{{ $client->name ?: 'Responsavel nao informado' }}</div>
                                <div class="admin-row-meta">{{ $client->email ?: 'Sem e-mail' }} · {{ $client->phone ?: 'Sem telefone' }}</div>
                            </td>
                            <td>
                                <div class="admin-row-title">{{ $client->cidade ?: 'Cidade nao informada' }}</div>
                                <div class="admin-row-meta">{{ $client->estado ?: 'UF nao informada' }}</div>
                            </td>
                            <td>
                                <div class="admin-row-title">{{ number_format($client->orders_count) }} pedidos · {{ number_format($client->tickets_count) }} tickets · {{ number_format($client->documents_count) }} docs</div>
                                <div class="admin-row-meta">{{ number_format($client->open_tickets_count) }} ticket(s) aberto(s) · {{ number_format($client->pending_documents_count) }} doc(s) pendente(s)</div>
                            </td>
                            <td>
                                <span class="admin-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                <div class="admin-row-meta">
                                    @if($client->aprovado_em)
                                        Aprovado em {{ $client->aprovado_em->format('d/m/Y H:i') }}
                                        @if($client->approvedBy)
                                            · por {{ $client->approvedBy->name }}
                                        @endif
                                    @else
                                        Cadastro aguardando aprovacao
                                    @endif
                                </div>
                            </td>
                            <td>
                                @include('admin.clients.partials.actions', ['client' => $client])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm font-semibold text-slate-500">Nenhum cliente encontrado para o filtro atual.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </section>

            <section class="admin-mobile-list">
                @forelse($clients as $client)
                    @php
                        $statusLabel = $statusOptions[$client->status] ?? $client->status;
                        $statusClass = $statusClassMap[$client->status] ?? 'is-pending';
                        $companyLabel = $client->razao_social ?? $client->nome_fantasia ?? $client->name ?? 'Cliente sem nome';
                    @endphp
                    <article class="admin-order-card">
                        <div class="admin-order-card-header">
                            <div>
                                <h3 class="admin-row-title">{{ $companyLabel }}</h3>
                                <p class="admin-row-meta">{{ $client->email ?: 'Sem e-mail' }}</p>
                            </div>
                            <span class="admin-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>

                        <div class="admin-order-card-grid">
                            <div>
                                <span class="admin-detail-label">Responsavel</span>
                                <span class="admin-detail-value">{{ $client->name ?: 'Nao informado' }}</span>
                            </div>
                            <div>
                                <span class="admin-detail-label">Cidade</span>
                                <span class="admin-detail-value">{{ $client->cidade ?: 'Nao informada' }}{{ $client->estado ? ' / ' . $client->estado : '' }}</span>
                            </div>
                            <div>
                                <span class="admin-detail-label">Pedidos</span>
                                <span class="admin-detail-value">{{ number_format($client->orders_count) }}</span>
                            </div>
                            <div>
                                <span class="admin-detail-label">Tickets abertos</span>
                                <span class="admin-detail-value">{{ number_format($client->open_tickets_count) }}</span>
                            </div>
                        </div>

                        <div class="admin-row-meta">{{ $client->cnpj ?: ($client->cpf ?: 'Sem documento') }} · {{ $client->phone ?: 'Sem telefone' }}</div>

                        <div class="mt-4">
                            @include('admin.clients.partials.actions', ['client' => $client])
                        </div>
                    </article>
                @empty
                    <article class="admin-empty-state">Nenhum cliente encontrado para o filtro atual.</article>
                @endforelse
            </section>

            <div class="mt-4">
                {{ $clients->links() }}
            </div>
        </div>

        <aside class="admin-card">
            <span class="admin-tag admin-tag-new">visao operacional</span>
            <h2 class="mt-3 admin-section-title">Panorama do onboarding</h2>
            <p class="admin-section-note">Use estes números para priorizar cadastros com maior impacto no time comercial e de suporte.</p>

            <div class="mt-5 admin-stack">
                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="admin-detail-label">Tickets abertos na base</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($globalOpenTickets) }}</p>
                    <p class="mt-2 text-sm text-slate-500">Clientes com pendencias de atendimento tendem a exigir contato ativo antes da aprovacao final.</p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="admin-detail-label">Documentos pendentes</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($globalPendingDocuments) }}</p>
                    <p class="mt-2 text-sm text-slate-500">Triagem documental atrasada costuma travar a liberacao comercial do cliente.</p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="admin-detail-label">Pedidos associados</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($globalOrders) }}</p>
                    <p class="mt-2 text-sm text-slate-500">A base ja possui clientes com historico comercial suficiente para qualificar reativacoes e bloqueios.</p>
                </article>

                <article class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    {{ number_format($summary['withOpenTickets']) }} cliente(s) possuem ticket em aberto. Avalie atendimento e documentacao antes de bloquear uma conta ativa.
                </article>
            </div>
        </aside>
    </section>
@endsection