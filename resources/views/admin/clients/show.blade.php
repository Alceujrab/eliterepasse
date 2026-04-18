@extends('admin.layouts.app')

@php
    use App\Models\Order;

    $pageTitle = ($client->razao_social ?? $client->nome_fantasia ?? $client->name ?? 'Cliente') . ' · Cliente';
    $pageSubtitle = 'Workspace consolidado do lojista com dados cadastrais, aprovacao e atividade operacional recente.';

    $statusClassMap = [
        'pendente' => 'is-awaiting',
        'ativo' => 'is-paid',
        'bloqueado' => 'is-cancelled',
    ];

    $orderStatusClassMap = [
        Order::STATUS_PENDENTE => 'is-pending',
        Order::STATUS_AGUARD => 'is-awaiting',
        Order::STATUS_CONFIRMADO => 'is-confirmed',
        Order::STATUS_FATURADO => 'is-billed',
        Order::STATUS_PAGO => 'is-paid',
        Order::STATUS_CANCELADO => 'is-cancelled',
    ];
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
            <p class="admin-metric-value text-[1.65rem]"><span class="admin-status-badge {{ $statusClassMap[$client->status] ?? 'is-pending' }}">{{ ucfirst($client->status ?? 'sem status') }}</span></p>
            <p class="admin-metric-footnote">Cadastro em {{ $client->created_at?->format('d/m/Y H:i') }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Pedidos</p>
            <p class="admin-metric-value">{{ number_format($summary['ordersTotal']) }}</p>
            <p class="admin-metric-footnote">{{ number_format($summary['paidOrders']) }} pagamento(s) confirmado(s)</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Tickets abertos</p>
            <p class="admin-metric-value">{{ number_format($summary['openTickets']) }}</p>
            <p class="admin-metric-footnote">Demandas ainda em curso com o time interno</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Documentos</p>
            <p class="admin-metric-value">{{ number_format($summary['visibleDocuments']) }}</p>
            <p class="admin-metric-footnote">{{ number_format($summary['pendingDocuments']) }} pendente(s) de triagem</p>
        </article>
    </section>

    <section class="mt-6 admin-detail-grid">
        <div class="admin-stack">
            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <span class="admin-tag admin-tag-new">detalhe v2</span>
                        <h2 class="mt-3 admin-section-title">Resumo cadastral</h2>
                        <p class="admin-section-note">Dados principais do lojista para validacao comercial, contato e liberacao operacional.</p>
                    </div>
                    <div class="admin-toolbar-actions">
                        <a href="{{ route('admin.v2.clients.edit', $client) }}" class="admin-btn-primary">Editar cliente</a>
                        <a href="{{ route('admin.v2.clients.index') }}" class="admin-btn-soft">Voltar para fila</a>
                    </div>
                </div>

                <div class="admin-info-grid mt-5">
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Empresa</span>
                        <div class="admin-detail-value">{{ $client->razao_social ?? $client->nome_fantasia ?? 'Nao informada' }}</div>
                        <div class="admin-row-meta">{{ $client->cnpj ?: 'Sem CNPJ' }}</div>
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Responsavel</span>
                        <div class="admin-detail-value">{{ $client->name ?: 'Nao informado' }}</div>
                        <div class="admin-row-meta">{{ $client->cpf ?: 'Sem CPF' }}</div>
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Contato</span>
                        <div class="admin-detail-value">{{ $client->email ?: 'Sem e-mail' }}</div>
                        <div class="admin-row-meta">{{ $client->phone ?: 'Sem telefone' }}</div>
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Aprovacao</span>
                        <div class="admin-detail-value">{{ $client->aprovado_em ? $client->aprovado_em->format('d/m/Y H:i') : 'Ainda nao aprovado' }}</div>
                        <div class="admin-row-meta">{{ $client->approvedBy?->name ? 'Por ' . $client->approvedBy->name : 'Sem aprovador definido' }}</div>
                    </article>
                    <article class="admin-info-card md:col-span-2">
                        <span class="admin-detail-label">Endereco</span>
                        <div class="admin-detail-value">{{ collect([$client->logradouro, $client->numero ? 'nº ' . $client->numero : null, $client->complemento, $client->bairro])->filter()->implode(', ') ?: 'Endereco nao informado' }}</div>
                        <div class="admin-row-meta">{{ collect([$client->cidade, $client->estado, $client->cep ? 'CEP ' . $client->cep : null])->filter()->implode(' · ') ?: 'Sem localizacao cadastrada' }}</div>
                    </article>
                    <article class="admin-info-card md:col-span-2">
                        <span class="admin-detail-label">Observacoes</span>
                        <div class="admin-detail-value">{{ $client->observacoes ?: 'Sem observacoes internas registradas.' }}</div>
                    </article>
                </div>
            </section>

            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <h2 class="admin-section-title">Pedidos recentes</h2>
                        <p class="admin-section-note">Ultima atividade comercial do cliente para apoiar decisao de aprovacao ou bloqueio.</p>
                    </div>
                </div>

                @if($recentOrders->isNotEmpty())
                    <div class="admin-shipment-list">
                        @foreach($recentOrders as $order)
                            <article class="admin-shipment-card">
                                <div class="admin-toolbar">
                                    <div class="admin-toolbar-main">
                                        <span class="admin-status-badge {{ $orderStatusClassMap[$order->status] ?? 'is-pending' }}">{{ $orderStatusOptions[$order->status] ?? $order->status }}</span>
                                        <h3 class="mt-3 admin-section-title text-base">{{ $order->numero }}</h3>
                                        <p class="admin-section-note">{{ $order->vehicle ? trim($order->vehicle->brand . ' ' . $order->vehicle->model . ' ' . $order->vehicle->model_year) : 'Sem veiculo vinculado' }}</p>
                                    </div>
                                    <div class="admin-toolbar-actions">
                                        <a href="{{ route('admin.v2.orders.show', $order) }}" class="admin-btn-soft">Abrir pedido</a>
                                    </div>
                                </div>

                                <div class="admin-info-grid mt-4">
                                    <div>
                                        <span class="admin-detail-label">Valor</span>
                                        <span class="admin-detail-value">R$ {{ number_format((float) $order->valor_compra, 0, ',', '.') }}</span>
                                    </div>
                                    <div>
                                        <span class="admin-detail-label">Financeiro</span>
                                        <span class="admin-detail-value">{{ $order->financial?->status ?? 'Sem fatura' }}</span>
                                    </div>
                                    <div>
                                        <span class="admin-detail-label">Criado em</span>
                                        <span class="admin-detail-value">{{ $order->created_at?->format('d/m/Y H:i') ?? 'Sem data' }}</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="admin-empty-state mt-4">Este cliente ainda nao possui pedidos registrados.</div>
                @endif
            </section>

            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <h2 class="admin-section-title">Tickets e documentos recentes</h2>
                        <p class="admin-section-note">Acompanhe atritos de atendimento e pendencias documentais no mesmo workspace.</p>
                    </div>
                </div>

                <div class="grid gap-4 xl:grid-cols-2">
                    <div class="admin-stack">
                        @forelse($recentTickets as $ticket)
                            <article class="admin-info-card">
                                <span class="admin-detail-label">{{ $ticket->numero ?: 'Ticket sem numero' }}</span>
                                <div class="admin-detail-value">{{ $ticket->titulo ?: 'Sem assunto' }}</div>
                                <div class="admin-row-meta">{{ $ticketStatusOptions[$ticket->status] ?? $ticket->status }} · {{ $ticket->atribuidoA?->name ?? 'Nao atribuido' }}</div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <a href="{{ route('admin.v2.tickets.index', ['ticket' => $ticket->id]) }}" class="admin-btn-soft">Abrir ticket</a>
                                    @if($ticket->order)
                                        <a href="{{ route('admin.v2.orders.show', $ticket->order) }}" class="admin-btn-soft">Pedido vinculado</a>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="admin-empty-state">Nenhum ticket recente para este cliente.</div>
                        @endforelse
                    </div>

                    <div class="admin-stack">
                        @forelse($recentDocuments as $document)
                            <article class="admin-info-card">
                                <span class="admin-detail-label">{{ $documentTypeOptions[$document->tipo] ?? $document->tipo }}</span>
                                <div class="admin-detail-value">{{ $document->titulo ?: ($document->nome_original ?: 'Documento sem titulo') }}</div>
                                <div class="admin-row-meta">{{ $documentStatusOptions[$document->status] ?? $document->status }} · {{ $document->vehicle?->plate ?? 'Sem placa' }}</div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @if($document->url)
                                        <a href="{{ $document->url }}" target="_blank" rel="noreferrer" class="admin-btn-soft">Arquivo</a>
                                    @endif
                                    <a href="{{ route('admin.v2.documents.index', ['q' => $client->email ?: $client->name]) }}" class="admin-btn-soft">Ver na fila</a>
                                </div>
                            </article>
                        @empty
                            <div class="admin-empty-state">Nenhum documento recente para este cliente.</div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <aside class="admin-stack">
            <section class="admin-card">
                <span class="admin-tag admin-tag-new">acoes do cliente</span>
                <h2 class="mt-3 admin-section-title">Aprovacao e bloqueio</h2>
                <p class="admin-section-note">Libere ou suspenda o acesso do lojista sem sair do contexto operacional.</p>
                <div class="mt-4">
                    @include('admin.clients.partials.actions', ['client' => $client])
                </div>
            </section>

            <section class="admin-card">
                <h2 class="admin-section-title">Checklist comercial</h2>
                <div class="mt-4 admin-stack text-sm text-slate-600">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $summary['pendingDocuments'] > 0 ? 'Ainda existem documentos pendentes para triagem.' : 'Nao ha pendencias documentais abertas.' }}</div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $summary['openTickets'] > 0 ? 'Existem tickets abertos que podem impactar a aprovacao.' : 'Nao existem tickets abertos no momento.' }}</div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $summary['ordersTotal'] > 0 ? 'O cliente ja possui historico de pedidos na base.' : 'Ainda sem historico comercial registrado.' }}</div>
                </div>
            </section>
        </aside>
    </section>
@endsection