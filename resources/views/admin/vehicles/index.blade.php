@extends('admin.layouts.app')

@php
    $pageTitle = 'Veículos (Admin v2)';
    $pageSubtitle = 'Central de estoque com foco em disponibilidade, preço, destaques comerciais e proximos desdobramentos operacionais.';

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

    <section class="admin-metrics-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Veiculos no filtro</p>
            <p class="admin-metric-value">{{ number_format($summary['filteredTotal']) }}</p>
            <p class="admin-metric-footnote">Base total: {{ number_format($globalTotalVehicles) }} unidades</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Disponiveis</p>
            <p class="admin-metric-value">{{ number_format($summary['available']) }}</p>
            <p class="admin-metric-footnote">Prontos para venda no portal e no comercial</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Reservados</p>
            <p class="admin-metric-value">{{ number_format($summary['reserved']) }}</p>
            <p class="admin-metric-footnote">Travados por negociacao ou pedido em andamento</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Vendidos</p>
            <p class="admin-metric-value">{{ number_format($summary['sold']) }}</p>
            <p class="admin-metric-footnote">Ja convertidos em operacao fechada</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Valor em estoque</p>
            <p class="admin-metric-value">R$ {{ number_format($summary['stockValue'], 0, ',', '.') }}</p>
            <p class="admin-metric-footnote">Somente veiculos disponiveis no recorte atual</p>
        </article>
    </section>

    <section class="mt-6 admin-card">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag {{ $hasActiveFilters ? 'admin-tag-migration' : 'admin-tag-new' }}">{{ $hasActiveFilters ? 'estoque filtrado' : 'estoque central' }}</span>
                <h2 class="mt-3 admin-section-title">Central de veiculos</h2>
                <p class="admin-section-note">Monitore disponibilidade, valor e sinais comerciais sem depender do Filament para a operacao diaria do estoque.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.vehicles.create') }}" class="admin-btn-primary">Novo veículo</a>
                <a href="{{ route('admin.v2.vehicles.index', request()->query()) }}" class="admin-btn-soft">Atualizar</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.v2.vehicles.index') }}" class="admin-filter-grid-wide md:items-end">
            <div>
                <label for="vehicles-q" class="admin-field-label">Busca</label>
                <input id="vehicles-q" name="q" value="{{ $search }}" placeholder="Placa, marca, modelo, versao, cor..." class="admin-input">
            </div>
            <div>
                <label for="vehicles-status" class="admin-field-label">Status</label>
                <select id="vehicles-status" name="status" class="admin-select">
                    <option value="">Todos</option>
                    @foreach($statusOptions as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="vehicles-brand" class="admin-field-label">Marca</label>
                <select id="vehicles-brand" name="brand" class="admin-select">
                    <option value="">Todas</option>
                    @foreach($brandOptions as $brandKey => $brandLabel)
                        <option value="{{ $brandKey }}" @selected($brand === $brandKey)>{{ $brandLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="vehicles-highlight" class="admin-field-label">Destaque</label>
                <select id="vehicles-highlight" name="highlight" class="admin-select">
                    <option value="">Todos</option>
                    @foreach($highlightOptions as $highlightKey => $highlightLabel)
                        <option value="{{ $highlightKey }}" @selected($highlight === $highlightKey)>{{ $highlightLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="admin-btn-primary">Filtrar</button>
                <a href="{{ route('admin.v2.vehicles.index') }}" class="admin-btn-soft">Limpar</a>
            </div>
        </form>

        <div class="admin-quick-filters">
            <a href="{{ route('admin.v2.vehicles.index', array_filter(['q' => $search !== '' ? $search : null, 'brand' => $brand !== '' ? $brand : null, 'highlight' => $highlight !== '' ? $highlight : null])) }}" class="admin-filter-chip {{ $status === '' ? 'is-active' : '' }}">
                <span>Todos</span>
                <span>{{ number_format($globalTotalVehicles) }}</span>
            </a>
            @foreach($statusOptions as $statusKey => $statusLabel)
                <a href="{{ route('admin.v2.vehicles.index', array_filter(['status' => $statusKey, 'q' => $search !== '' ? $search : null, 'brand' => $brand !== '' ? $brand : null, 'highlight' => $highlight !== '' ? $highlight : null])) }}" class="admin-filter-chip {{ $status === $statusKey ? 'is-active' : '' }}">
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
                        <th>Veiculo</th>
                        <th>Preco</th>
                        <th>Estoque</th>
                        <th>Destaques</th>
                        <th>Status</th>
                        <th>Acoes</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($vehicles as $vehicle)
                        @php
                            $statusLabel = $statusOptions[$vehicle->status] ?? $vehicle->status;
                            $statusClass = $statusClassMap[$vehicle->status] ?? 'is-pending';
                            $locationLabel = collect([$vehicle->location['name'] ?? null, $vehicle->location['city'] ?? null, $vehicle->location['state'] ?? null])->filter()->implode(' · ');
                        @endphp
                        <tr>
                            <td>
                                <div class="admin-row-title">{{ $vehicle->nome_completo ?: 'Veiculo sem nome' }}</div>
                                <div class="admin-row-meta">{{ $vehicle->plate }} · {{ $vehicle->color ?: 'Cor nao informada' }} · {{ number_format((int) $vehicle->mileage, 0, ',', '.') }} km</div>
                            </td>
                            <td>
                                <div class="admin-row-title text-emerald-700">R$ {{ number_format((float) $vehicle->sale_price, 0, ',', '.') }}</div>
                                <div class="admin-row-meta">FIPE: {{ $vehicle->fipe_price ? 'R$ ' . number_format((float) $vehicle->fipe_price, 0, ',', '.') : 'nao informada' }}</div>
                            </td>
                            <td>
                                <div class="admin-row-title">{{ $vehicle->orders_count }} pedido(s) · {{ $vehicle->documents_count }} doc(s)</div>
                                <div class="admin-row-meta">{{ $locationLabel !== '' ? $locationLabel : 'Sem localizacao definida' }}</div>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    @if($vehicle->is_on_sale)
                                        <span class="admin-status-badge is-awaiting">Oferta</span>
                                    @endif
                                    @if($vehicle->is_just_arrived)
                                        <span class="admin-status-badge is-confirmed">Recem chegado</span>
                                    @endif
                                    @if($vehicle->has_report)
                                        <span class="admin-status-badge is-paid">Laudo</span>
                                    @endif
                                    @if($vehicle->has_factory_warranty)
                                        <span class="admin-status-badge is-billed">Garantia</span>
                                    @endif
                                </div>
                                <div class="admin-row-meta">{{ $vehicle->reports_count }} laudo(s) registrado(s)</div>
                            </td>
                            <td>
                                <span class="admin-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                <div class="admin-row-meta">{{ $vehicle->category ?: 'Categoria nao informada' }}</div>
                            </td>
                            <td>
                                @include('admin.vehicles.partials.actions', ['vehicle' => $vehicle])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm font-semibold text-slate-500">Nenhum veiculo encontrado para o filtro atual.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </section>

            <section class="admin-mobile-list">
                @forelse($vehicles as $vehicle)
                    @php
                        $statusLabel = $statusOptions[$vehicle->status] ?? $vehicle->status;
                        $statusClass = $statusClassMap[$vehicle->status] ?? 'is-pending';
                    @endphp
                    <article class="admin-order-card">
                        <div class="admin-order-card-header">
                            <div>
                                <h3 class="admin-row-title">{{ $vehicle->nome_completo ?: 'Veiculo sem nome' }}</h3>
                                <p class="admin-row-meta">{{ $vehicle->plate }} · {{ $vehicle->category ?: 'Sem categoria' }}</p>
                            </div>
                            <span class="admin-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>

                        <div class="admin-order-card-grid">
                            <div>
                                <span class="admin-detail-label">Preco</span>
                                <span class="admin-detail-value">R$ {{ number_format((float) $vehicle->sale_price, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="admin-detail-label">Km</span>
                                <span class="admin-detail-value">{{ number_format((int) $vehicle->mileage, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="admin-detail-label">Pedidos</span>
                                <span class="admin-detail-value">{{ number_format($vehicle->orders_count) }}</span>
                            </div>
                            <div>
                                <span class="admin-detail-label">Laudos</span>
                                <span class="admin-detail-value">{{ number_format($vehicle->reports_count) }}</span>
                            </div>
                        </div>

                        <div class="admin-row-meta">{{ $vehicle->color ?: 'Cor nao informada' }} · {{ $vehicle->fuel_type ?: 'Combustivel nao informado' }} · {{ $vehicle->transmission ?: 'Cambio nao informado' }}</div>

                        <div class="mt-4">
                            @include('admin.vehicles.partials.actions', ['vehicle' => $vehicle])
                        </div>
                    </article>
                @empty
                    <article class="admin-empty-state">Nenhum veiculo encontrado para o filtro atual.</article>
                @endforelse
            </section>

            <div class="mt-4">
                {{ $vehicles->links() }}
            </div>
        </div>

        <aside class="admin-card">
            <span class="admin-tag admin-tag-new">leitura comercial</span>
            <h2 class="mt-3 admin-section-title">Destaques do estoque</h2>
            <p class="admin-section-note">Use este resumo para decidir precificacao, empurrar ofertas e organizar o pipeline do estoque.</p>

            <div class="mt-5 admin-stack">
                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="admin-detail-label">Em oferta</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($summary['onSale']) }}</p>
                    <p class="mt-2 text-sm text-slate-500">Veiculos com apelo comercial imediato para campanhas e empurrao do time.</p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="admin-detail-label">Cobertura de laudos</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($vehicles->getCollection()->where('has_report', true)->count()) }}</p>
                    <p class="mt-2 text-sm text-slate-500">No recorte atual, estes veiculos ja tem laudo marcado e destravam mais rapido a venda.</p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="admin-detail-label">Marcas no filtro</p>
                    <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($vehicles->getCollection()->pluck('brand')->filter()->unique()->count()) }}</p>
                    <p class="mt-2 text-sm text-slate-500">Ajuda a medir concentracao do mix no estoque visivel ao time.</p>
                </article>
            </div>
        </aside>
    </section>
@endsection