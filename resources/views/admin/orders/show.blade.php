@extends('admin.layouts.app')

@php
    use App\Models\Contract;
    use App\Models\Order;
    use App\Models\OrderShipment;

    $pageTitle = $order->numero . ' · Pedido';
    $pageSubtitle = 'Workspace completo do pedido com contexto comercial, financeiro, documental e historico operacional.';

    $orderStatusClassMap = [
        Order::STATUS_PENDENTE => 'is-pending',
        Order::STATUS_AGUARD => 'is-awaiting',
        Order::STATUS_CONFIRMADO => 'is-confirmed',
        Order::STATUS_FATURADO => 'is-billed',
        Order::STATUS_PAGO => 'is-paid',
        Order::STATUS_CANCELADO => 'is-cancelled',
    ];

    $shipmentStatusClassMap = [
        'disponivel' => 'is-confirmed',
        'despachado' => 'is-awaiting',
        'entregue' => 'is-paid',
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

    <section class="admin-summary-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Status atual</p>
            <p class="admin-metric-value text-[1.65rem]">
                <span class="admin-status-badge {{ $orderStatusClassMap[$order->status] ?? 'is-pending' }}">{{ $statusOptions[$order->status] ?? $order->status }}</span>
            </p>
            <p class="admin-metric-footnote">Criado em {{ $order->created_at?->format('d/m/Y H:i') }}</p>
        </article>

        <article class="admin-metric-card">
            <p class="admin-metric-label">Valor do pedido</p>
            <p class="admin-metric-value">R$ {{ number_format((float) $order->valor_compra, 0, ',', '.') }}</p>
            <p class="admin-metric-footnote">FIPE: {{ $order->valor_fipe ? 'R$ ' . number_format((float) $order->valor_fipe, 0, ',', '.') : 'nao informado' }}</p>
        </article>

        <article class="admin-metric-card">
            <p class="admin-metric-label">Fluxo documental</p>
            <p class="admin-metric-value">{{ number_format($summary['shipmentsTotal']) }}</p>
            <p class="admin-metric-footnote">{{ $summary['shipmentsAvailable'] }} disponivel · {{ $summary['shipmentsDispatched'] }} despachado · {{ $summary['shipmentsDelivered'] }} entregue</p>
        </article>

        <article class="admin-metric-card">
            <p class="admin-metric-label">Historico</p>
            <p class="admin-metric-value">{{ number_format($summary['historyEntries']) }}</p>
            <p class="admin-metric-footnote">Eventos registrados do lifecycle do pedido</p>
        </article>
    </section>

    <section class="mt-6 admin-detail-grid">
        <div class="admin-stack">
            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <span class="admin-tag admin-tag-new">detalhe v2</span>
                        <h2 class="mt-3 admin-section-title">Resumo do pedido</h2>
                        <p class="admin-section-note">Consolida cliente, veiculo, financeiro, contrato e a trilha operacional que antes exigia navegar entre varias telas do admin legado.</p>
                    </div>
                    <div class="admin-toolbar-actions">
                        <a href="{{ route('admin.v2.orders.index') }}" class="admin-btn-soft">Voltar para fila</a>
                        <a href="/admin/orders/{{ $order->id }}" class="admin-btn-soft">Abrir legado</a>
                    </div>
                </div>

                <div class="admin-info-grid mt-5">
                    <article class="admin-info-card">
                        <span class="admin-detail-label">Cliente</span>
                        <div class="admin-detail-value">{{ $order->user?->razao_social ?? $order->user?->name ?? 'Nao identificado' }}</div>
                        <div class="admin-row-meta">{{ $order->user?->cnpj ?? $order->user?->email ?? 'Sem cadastro complementar' }}</div>
                    </article>

                    <article class="admin-info-card">
                        <span class="admin-detail-label">Veiculo</span>
                        <div class="admin-detail-value">{{ $order->vehicle ? trim("{$order->vehicle->brand} {$order->vehicle->model} {$order->vehicle->model_year}") : 'Nao vinculado' }}</div>
                        <div class="admin-row-meta">{{ $order->vehicle?->plate ?? 'Sem placa' }}</div>
                    </article>

                    <article class="admin-info-card">
                        <span class="admin-detail-label">Pagamento</span>
                        <div class="admin-detail-value">{{ $order->paymentMethod?->name ?? 'Nao informado' }}</div>
                        <div class="admin-row-meta">Confirmado em {{ $order->confirmado_em?->format('d/m/Y H:i') ?? 'ainda nao confirmado' }}</div>
                    </article>

                    <article class="admin-info-card">
                        <span class="admin-detail-label">Financeiro</span>
                        <div class="admin-detail-value">{{ $order->financial ? ($financialStatusLabels[$order->financial->status] ?? $order->financial->status) : 'Sem fatura' }}</div>
                        <div class="admin-row-meta">
                            @if($order->financial)
                                {{ $order->financial->numero }} · {{ $order->financial->data_vencimento?->format('d/m/Y') ?? 'Sem vencimento' }}
                            @else
                                Gere a fatura a partir das acoes do pedido.
                            @endif
                        </div>
                    </article>

                    <article class="admin-info-card md:col-span-2">
                        <span class="admin-detail-label">Contrato</span>
                        <div class="admin-detail-value">{{ $order->contract?->numero ?? 'Contrato ainda nao gerado' }}</div>
                        <div class="admin-row-meta">
                            @if($order->contract)
                                {{ Contract::statusLabels()[$order->contract->status] ?? $order->contract->status }}
                                @if($order->contract->assinaturaComprador)
                                    · token gerado para assinatura do comprador
                                @endif
                            @else
                                O pedido ainda nao tem contrato vinculado.
                            @endif
                        </div>
                    </article>
                </div>
            </section>

            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <h2 class="admin-section-title">Documentos e envio</h2>
                        <p class="admin-section-note">Disponibilize arquivos, registre despacho e acompanhe entrega sem sair do pedido.</p>
                    </div>
                </div>

                @if($order->shipments->isNotEmpty())
                    <div class="admin-shipment-list">
                        @foreach($order->shipments as $shipment)
                            <article class="admin-shipment-card">
                                <div class="admin-toolbar">
                                    <div class="admin-toolbar-main">
                                        <div class="flex flex-wrap gap-2">
                                            <span class="admin-status-badge {{ $shipmentStatusClassMap[$shipment->status] ?? 'is-pending' }}">{{ $shipmentStatusOptions[$shipment->status] ?? $shipment->status }}</span>
                                        </div>
                                        <h3 class="mt-3 admin-section-title text-base">{{ $shipmentTypeOptions[$shipment->tipo_documento] ?? $shipment->tipo_documento }}</h3>
                                        <p class="admin-section-note">{{ $shipment->titulo ?? $shipment->nome_original ?? 'Documento sem descricao' }}</p>
                                    </div>
                                    <div class="admin-toolbar-actions">
                                        @if($shipment->url_documento)
                                            <a href="{{ $shipment->url_documento }}" target="_blank" rel="noreferrer" class="admin-btn-soft">Arquivo</a>
                                        @endif
                                        @if($shipment->url_comprovante)
                                            <a href="{{ $shipment->url_comprovante }}" target="_blank" rel="noreferrer" class="admin-btn-soft">Comprovante</a>
                                        @endif
                                    </div>
                                </div>

                                <div class="admin-info-grid mt-4">
                                    <div>
                                        <span class="admin-detail-label">Status</span>
                                        <span class="admin-detail-value">{{ $shipmentStatusOptions[$shipment->status] ?? $shipment->status }}</span>
                                    </div>
                                    <div>
                                        <span class="admin-detail-label">Envio</span>
                                        <span class="admin-detail-value">{{ $shipment->metodo_envio ? ($shipmentMethodOptions[$shipment->metodo_envio] ?? $shipment->metodo_envio) : 'Nao despachado' }}</span>
                                    </div>
                                    <div>
                                        <span class="admin-detail-label">Rastreio</span>
                                        <span class="admin-detail-value">{{ $shipment->codigo_rastreio ?? 'Nao informado' }}</span>
                                    </div>
                                    <div>
                                        <span class="admin-detail-label">Despachado em</span>
                                        <span class="admin-detail-value">{{ $shipment->despachado_em?->format('d/m/Y H:i') ?? 'Ainda nao' }}</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="admin-empty-state mt-4">Nenhum documento operacional foi disponibilizado para este pedido.</div>
                @endif
            </section>

            <section class="admin-card">
                <h2 class="admin-section-title">Historico operacional</h2>
                <p class="admin-section-note">Linha do tempo consolidada do pedido, contratos, financeiro e documentos.</p>

                <div class="admin-timeline">
                    @forelse($order->histories as $history)
                        <article class="admin-timeline-item">
                            <div class="admin-toolbar">
                                <div class="admin-toolbar-main">
                                    <div class="admin-row-title">{{ $historyActionLabels[$history->acao] ?? $history->descricao ?? $history->acao }}</div>
                                    <div class="admin-row-meta">{{ $history->descricao }}</div>
                                </div>
                                <div class="admin-row-meta">{{ $history->created_at?->format('d/m/Y H:i') }}</div>
                            </div>

                            <div class="admin-info-grid mt-4">
                                <div>
                                    <span class="admin-detail-label">Status</span>
                                    <span class="admin-detail-value">{{ $history->status_de ?? '—' }} → {{ $history->status_para ?? '—' }}</span>
                                </div>
                                <div>
                                    <span class="admin-detail-label">Responsavel</span>
                                    <span class="admin-detail-value">{{ $history->user?->name ?? 'Sistema' }}</span>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="admin-empty-state">Ainda nao ha historico registrado para este pedido.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="admin-stack">
            <section class="admin-card">
                <span class="admin-tag admin-tag-new">acoes do pedido</span>
                <h2 class="mt-3 admin-section-title">Fluxo principal</h2>
                <p class="admin-section-note">Use as acoes abaixo para avancar o pedido pelo ciclo comercial e financeiro.</p>
                <div class="mt-4">
                    @include('admin.orders.partials.actions', ['order' => $order])
                </div>
            </section>

            <section class="admin-card">
                <span class="admin-tag admin-tag-new">disponibilizar</span>
                <h2 class="mt-3 admin-section-title">Novo documento</h2>
                <form method="POST" action="{{ route('admin.v2.orders.publish-document', $order) }}" enctype="multipart/form-data" class="mt-4 admin-stack">
                    @csrf
                    <div>
                        <label class="admin-field-label" for="shipment-type">Tipo</label>
                        <select id="shipment-type" name="tipo_documento" class="admin-select" required>
                            @foreach($shipmentTypeOptions as $typeKey => $typeLabel)
                                <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="admin-field-label" for="shipment-title">Titulo</label>
                        <input id="shipment-title" name="titulo" class="admin-input" placeholder="Ex: ATPV-e assinado">
                    </div>
                    <div>
                        <label class="admin-field-label" for="shipment-file">Arquivo</label>
                        <input id="shipment-file" type="file" name="arquivo" class="admin-file" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                    <div>
                        <label class="admin-field-label" for="shipment-note">Observacoes</label>
                        <textarea id="shipment-note" name="observacoes" class="admin-textarea" placeholder="Contexto do envio ou observacoes para o cliente."></textarea>
                    </div>
                    <button type="submit" class="admin-btn-primary">Disponibilizar documento</button>
                </form>
            </section>

            @if($order->shipments->where('status', 'disponivel')->isNotEmpty())
                <section class="admin-card">
                    <h2 class="admin-section-title">Registrar despacho</h2>
                    <form method="POST" action="{{ route('admin.v2.orders.register-dispatch', $order) }}" enctype="multipart/form-data" class="mt-4 admin-stack">
                        @csrf
                        <div>
                            <label class="admin-field-label" for="dispatch-shipment">Documento</label>
                            <select id="dispatch-shipment" name="shipment_id" class="admin-select" required>
                                @foreach($order->shipments->where('status', 'disponivel') as $shipment)
                                    <option value="{{ $shipment->id }}">{{ $shipmentTypeOptions[$shipment->tipo_documento] ?? $shipment->tipo_documento }} · {{ $shipment->titulo ?? $shipment->nome_original }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="admin-field-label" for="dispatch-method">Metodo</label>
                            <select id="dispatch-method" name="metodo_envio" class="admin-select" required>
                                @foreach($shipmentMethodOptions as $methodKey => $methodLabel)
                                    <option value="{{ $methodKey }}">{{ $methodLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="admin-form-grid">
                            <div>
                                <label class="admin-field-label" for="dispatch-tracking">Codigo de rastreio</label>
                                <input id="dispatch-tracking" name="codigo_rastreio" class="admin-input" placeholder="Opcional">
                            </div>
                            <div>
                                <label class="admin-field-label" for="dispatch-date">Despachado em</label>
                                <input id="dispatch-date" type="datetime-local" name="despachado_em" class="admin-input" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>
                        <div>
                            <label class="admin-field-label" for="dispatch-proof">Comprovante</label>
                            <input id="dispatch-proof" type="file" name="comprovante_despacho" class="admin-file" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <button type="submit" class="admin-btn-primary">Registrar despacho</button>
                    </form>
                </section>
            @endif

            @if($order->shipments->isNotEmpty())
                <section class="admin-card">
                    <h2 class="admin-section-title">Acao rapida de notificacao</h2>
                    <form method="POST" action="{{ route('admin.v2.orders.resend-shipment-notification', $order) }}" class="mt-4 admin-stack">
                        @csrf
                        <div>
                            <label class="admin-field-label" for="shipment-notify">Documento</label>
                            <select id="shipment-notify" name="shipment_id" class="admin-select" required>
                                @foreach($order->shipments as $shipment)
                                    <option value="{{ $shipment->id }}">{{ $shipmentTypeOptions[$shipment->tipo_documento] ?? $shipment->tipo_documento }} · {{ $shipment->titulo ?? $shipment->nome_original }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="admin-btn-soft">Reenviar notificacao</button>
                    </form>
                </section>
            @endif

            @if($order->shipments->where('status', 'despachado')->isNotEmpty())
                <section class="admin-card">
                    <h2 class="admin-section-title">Marcar entregue</h2>
                    <form method="POST" action="{{ route('admin.v2.orders.mark-shipment-delivered', $order) }}" class="mt-4 admin-stack">
                        @csrf
                        <div>
                            <label class="admin-field-label" for="shipment-delivered">Documento</label>
                            <select id="shipment-delivered" name="shipment_id" class="admin-select" required>
                                @foreach($order->shipments->where('status', 'despachado') as $shipment)
                                    <option value="{{ $shipment->id }}">{{ $shipmentTypeOptions[$shipment->tipo_documento] ?? $shipment->tipo_documento }} · {{ $shipment->codigo_rastreio ?? 'sem rastreio' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="admin-btn-soft">Confirmar entrega</button>
                    </form>
                </section>
            @endif
        </aside>
    </section>
@endsection