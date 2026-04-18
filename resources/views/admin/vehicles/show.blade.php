@extends('admin.layouts.app')

@php
    $pageTitle = ($vehicle->nome_completo ?: $vehicle->plate) . ' · Veiculo';
    $pageSubtitle = 'Workspace do veiculo com contexto comercial, estoque, laudos, documentos e pedidos relacionados.';

    $statusClassMap = [
        'available' => 'is-paid',
        'reserved' => 'is-awaiting',
        'sold' => 'is-cancelled',
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
            <p class="admin-metric-value text-[1.65rem]"><span class="admin-status-badge {{ $statusClassMap[$vehicle->status] ?? 'is-pending' }}">{{ $statusOptions[$vehicle->status] ?? $vehicle->status }}</span></p>
            <p class="admin-metric-footnote">Placa {{ $vehicle->plate }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Preco de venda</p>
            <p class="admin-metric-value">R$ {{ number_format((float) $vehicle->sale_price, 0, ',', '.') }}</p>
            <p class="admin-metric-footnote">FIPE {{ $vehicle->fipe_price ? 'R$ ' . number_format((float) $vehicle->fipe_price, 0, ',', '.') : 'nao informada' }}</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Pedidos</p>
            <p class="admin-metric-value">{{ number_format($summary['ordersTotal']) }}</p>
            <p class="admin-metric-footnote">{{ number_format($summary['paidOrders']) }} pagamento(s) confirmado(s)</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Documentos e laudos</p>
            <p class="admin-metric-value">{{ number_format($summary['documentsTotal']) }}</p>
            <p class="admin-metric-footnote">{{ number_format($summary['reportsTotal']) }} laudo(s) vinculado(s)</p>
        </article>
    </section>

    <section class="mt-6 admin-detail-grid">
        <div class="admin-stack">
            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <span class="admin-tag admin-tag-new">detalhe v2</span>
                        <h2 class="mt-3 admin-section-title">Resumo do veiculo</h2>
                        <p class="admin-section-note">Consolida identificacao, especificacoes, precificacao e sinais comerciais do estoque.</p>
                    </div>
                    <div class="admin-toolbar-actions">
                        <a href="{{ route('admin.v2.vehicles.edit', $vehicle) }}" class="admin-btn-primary">Editar veículo</a>
                        <a href="{{ route('admin.v2.vehicles.index') }}" class="admin-btn-soft">Voltar para fila</a>
                    </div>
                </div>

                <div class="admin-info-grid mt-5">
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Identificação</span>
                        <div class="admin-detail-value">{{ $vehicle->nome_completo ?: 'Veículo sem nome' }}</div>
                        <div class="admin-row-meta">{{ $vehicle->plate }} · {{ $vehicle->category ?: 'Categoria não informada' }}</div>
                        @if($vehicle->renavam || $vehicle->chassi)
                            <div class="admin-row-meta mt-1 font-mono text-xs">
                                @if($vehicle->renavam) Renavam: {{ $vehicle->renavam }} @endif
                                @if($vehicle->renavam && $vehicle->chassi) · @endif
                                @if($vehicle->chassi) Chassi: {{ $vehicle->chassi }} @endif
                            </div>
                        @endif
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Especificações</span>
                        <div class="admin-detail-value">{{ $vehicle->fuel_type ?: 'Combustível n/d' }} · {{ $vehicle->transmission ?: 'Câmbio n/d' }}</div>
                        <div class="admin-row-meta">{{ $vehicle->engine ?: 'Motor n/d' }} · {{ $vehicle->doors ?: '?' }} portas{{ $vehicle->steering ? ' · Dir. ' . $vehicle->steering : '' }}</div>
                        @if($vehicle->num_owners)
                            <div class="admin-row-meta mt-1">{{ $vehicle->num_owners }} dono(s)</div>
                        @endif
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Ano e km</span>
                        <div class="admin-detail-value">{{ $vehicle->manufacture_year }}/{{ $vehicle->model_year }}</div>
                        <div class="admin-row-meta">{{ number_format((int) $vehicle->mileage, 0, ',', '.') }} km · {{ $vehicle->color ?: 'Cor não informada' }}</div>
                    </article>
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Localização</span>
                        <div class="admin-detail-value">{{ $vehicle->location['name'] ?? 'Pátio não informado' }}</div>
                        <div class="admin-row-meta">{{ collect([$vehicle->location['city'] ?? null, $vehicle->location['state'] ?? null])->filter()->implode(' · ') ?: 'Sem cidade/UF' }}</div>
                    </article>
                    <article class="admin-info-card md:col-span-2">
                        <span class="admin-detail-label">Sinais comerciais</span>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @if($vehicle->is_on_sale)
                                <span class="admin-status-badge is-awaiting">Em oferta</span>
                            @endif
                            @if($vehicle->is_just_arrived)
                                <span class="admin-status-badge is-confirmed">Recém chegado</span>
                            @endif
                            @if($vehicle->has_report)
                                <span class="admin-status-badge is-paid">Com laudo</span>
                            @endif
                            @if($vehicle->has_factory_warranty)
                                <span class="admin-status-badge is-billed">Garantia de fábrica</span>
                            @endif
                            @if($vehicle->accepts_trade)
                                <span class="admin-status-badge is-confirmed">Aceita troca</span>
                            @endif
                            @if($vehicle->ipva_paid)
                                <span class="admin-status-badge is-paid">IPVA pago</span>
                            @endif
                            @if($vehicle->licensing_ok)
                                <span class="admin-status-badge is-paid">Licenciamento em dia</span>
                            @endif
                            @if($vehicle->is_armored)
                                <span class="admin-status-badge is-awaiting">Blindado</span>
                            @endif
                            @php
                                $hasAnyFlag = $vehicle->is_on_sale || $vehicle->is_just_arrived || $vehicle->has_report || $vehicle->has_factory_warranty || $vehicle->accepts_trade || $vehicle->ipva_paid || $vehicle->licensing_ok || $vehicle->is_armored;
                            @endphp
                            @if(! $hasAnyFlag)
                                <span class="admin-row-meta">Sem destaques comerciais marcados.</span>
                            @endif
                        </div>
                    </article>
                </div>

                @if($vehicle->description)
                    <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <span class="admin-detail-label">Descrição do anúncio</span>
                        <div class="mt-2 text-sm text-slate-700 whitespace-pre-line">{{ $vehicle->description }}</div>
                    </div>
                @endif

                @if($vehicle->video_url)
                    <div class="mt-4">
                        <span class="admin-detail-label">Vídeo</span>
                        <a href="{{ $vehicle->video_url }}" target="_blank" rel="noopener" class="mt-1 inline-flex items-center gap-2 text-sm text-blue-600 hover:underline">
                            {{ $vehicle->video_url }}
                        </a>
                    </div>
                @endif
                    </article>
                </div>
            </section>

            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <h2 class="admin-section-title">Pedidos e documentos recentes</h2>
                        <p class="admin-section-note">Veja rapidamente se este veiculo ja gerou operacao, exigiu documentos ou entrou em funil comercial.</p>
                    </div>
                </div>

                <div class="grid gap-4 xl:grid-cols-2">
                    <div class="admin-stack">
                        @forelse($recentOrders as $order)
                            <article class="admin-info-card">
                                <span class="admin-detail-label">{{ $order->numero }}</span>
                                <div class="admin-detail-value">{{ $order->user?->razao_social ?? $order->user?->name ?? 'Cliente nao informado' }}</div>
                                <div class="admin-row-meta">{{ $orderStatusOptions[$order->status] ?? $order->status }} · R$ {{ number_format((float) $order->valor_compra, 0, ',', '.') }}</div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <a href="{{ route('admin.v2.orders.show', $order) }}" class="admin-btn-soft">Abrir pedido</a>
                                </div>
                            </article>
                        @empty
                            <div class="admin-empty-state">Nenhum pedido recente para este veiculo.</div>
                        @endforelse
                    </div>

                    <div class="admin-stack">
                        @forelse($recentDocuments as $document)
                            <article class="admin-info-card">
                                <span class="admin-detail-label">{{ $documentTypeOptions[$document->tipo] ?? $document->tipo }}</span>
                                <div class="admin-detail-value">{{ $document->titulo ?: ($document->nome_original ?: 'Documento sem titulo') }}</div>
                                <div class="admin-row-meta">{{ $documentStatusOptions[$document->status] ?? $document->status }} · {{ $document->user?->razao_social ?? $document->user?->name ?? 'Sem cliente' }}</div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <a href="{{ route('admin.v2.documents.index', ['q' => $vehicle->plate]) }}" class="admin-btn-soft">Ver na fila</a>
                                </div>
                            </article>
                        @empty
                            <div class="admin-empty-state">Nenhum documento recente para este veiculo.</div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <h2 class="admin-section-title">Laudos recentes</h2>
                        <p class="admin-section-note">Historico de vistoria e cautelar para apoiar decisao comercial e status do estoque.</p>
                    </div>
                </div>

                @if($recentReports->isNotEmpty())
                    <div class="admin-shipment-list">
                        @foreach($recentReports as $report)
                            <article class="admin-shipment-card">
                                <div class="admin-toolbar">
                                    <div class="admin-toolbar-main">
                                        <span class="admin-status-badge {{ $report->status === 'aprovado' ? 'is-paid' : ($report->status === 'em_revisao' ? 'is-awaiting' : ($report->status === 'reprovado' ? 'is-cancelled' : 'is-pending')) }}">{{ $reportStatusOptions[$report->status] ?? $report->status }}</span>
                                        <h3 class="mt-3 admin-section-title text-base">{{ $report->numero ?? ('REL-' . $report->id) }}</h3>
                                        <p class="admin-section-note">{{ $reportTypeOptions[$report->tipo] ?? $report->tipo }} · nota {{ $report->nota_geral ?? 'n/d' }}</p>
                                    </div>
                                </div>
                                <div class="admin-info-grid mt-4">
                                    <div>
                                        <span class="admin-detail-label">Criado por</span>
                                        <span class="admin-detail-value">{{ $report->criadoPor?->name ?? 'Nao informado' }}</span>
                                    </div>
                                    <div>
                                        <span class="admin-detail-label">Aprovado por</span>
                                        <span class="admin-detail-value">{{ $report->aprovadoPor?->name ?? 'Nao aprovado' }}</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="admin-empty-state mt-4">Nenhum laudo recente vinculado a este veiculo.</div>
                @endif
            </section>
        </div>

        <aside class="admin-stack">
            <section class="admin-card">
                <span class="admin-tag admin-tag-new">acoes do estoque</span>
                <h2 class="mt-3 admin-section-title">Status do veiculo</h2>
                <p class="admin-section-note">Atualize rapidamente a disponibilidade para refletir a realidade do patio e do funil comercial.</p>
                <div class="mt-4">
                    @include('admin.vehicles.partials.actions', ['vehicle' => $vehicle])
                </div>
            </section>

            <section class="admin-card">
                <h2 class="admin-section-title">Checklist rápido</h2>
                <div class="mt-4 admin-stack text-sm text-slate-600">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $vehicle->has_report ? 'Veículo com laudo marcado no cadastro.' : 'Veículo sem flag de laudo no cadastro.' }}</div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $summary['ordersTotal'] > 0 ? 'Este veículo já possui histórico de pedidos na base.' : 'Ainda sem pedidos vinculados a este veículo.' }}</div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $vehicle->is_on_sale ? 'Em oferta para empurrão comercial.' : 'Sem oferta ativa no momento.' }}</div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">{{ $vehicle->ipva_paid ? 'IPVA pago.' : 'IPVA não confirmado.' }} {{ $vehicle->licensing_ok ? '· Licenciamento em dia.' : '· Licenciamento não confirmado.' }}</div>
                    @if($vehicle->renavam)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 font-mono text-xs">Renavam: {{ $vehicle->renavam }}</div>
                    @endif
                </div>
            </section>
        </aside>
    </section>
@endsection