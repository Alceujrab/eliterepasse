@extends('admin.layouts.app')

@php
    use App\Models\Document;

    $pageTitle = 'Documentos (Admin v2)';
    $pageSubtitle = 'Triagem operacional de anexos com foco em verificacao, rejeicao orientada e upload rapido pelo time interno.';

    $statusClassMap = [
        'pendente' => 'is-awaiting',
        'verificado' => 'is-paid',
        'rejeitado' => 'is-cancelled',
    ];
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

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="admin-metrics-grid">
        <article class="admin-metric-card">
            <p class="admin-metric-label">Documentos no filtro</p>
            <p class="admin-metric-value">{{ number_format($summary['filteredTotal']) }}</p>
            <p class="admin-metric-footnote">Base total: {{ number_format($globalTotalDocuments) }} anexos</p>
        </article>

        <article class="admin-metric-card">
            <p class="admin-metric-label">Pendentes</p>
            <p class="admin-metric-value">{{ number_format($summary['pending']) }}</p>
            <p class="admin-metric-footnote">Fila que ainda exige triagem manual</p>
        </article>

        <article class="admin-metric-card">
            <p class="admin-metric-label">Verificados</p>
            <p class="admin-metric-value">{{ number_format($summary['verified']) }}</p>
            <p class="admin-metric-footnote">Aprovados e prontos para uso operacional</p>
        </article>

        <article class="admin-metric-card">
            <p class="admin-metric-label">Rejeitados</p>
            <p class="admin-metric-value">{{ number_format($summary['rejected']) }}</p>
            <p class="admin-metric-footnote">Com retorno ao cliente ou ajuste pendente</p>
        </article>

        <article class="admin-metric-card">
            <p class="admin-metric-label">Visiveis ao cliente</p>
            <p class="admin-metric-value">{{ number_format($summary['visibleToClient']) }}</p>
            <p class="admin-metric-footnote">Disponiveis para download no portal</p>
        </article>
    </section>

    <section class="mt-6 admin-split-grid">
        <div class="admin-stack">
            <section class="admin-card">
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <span class="admin-tag {{ $hasActiveFilters ? 'admin-tag-migration' : 'admin-tag-new' }}">
                            {{ $hasActiveFilters ? 'fila filtrada' : 'triagem central' }}
                        </span>
                        <h2 class="mt-3 admin-section-title">Central de documentos</h2>
                        <p class="admin-section-note">Controle anexos por tipo, status, validade e exposicao ao cliente sem depender do Filament.</p>
                    </div>

                    <div class="admin-toolbar-actions">
                        <a href="{{ route('admin.v2.documents.index', request()->query()) }}" class="admin-btn-soft">Atualizar</a>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.v2.documents.index') }}" class="admin-filter-grid-wide md:items-end">
                    <div>
                        <label for="documents-q" class="admin-field-label">Busca</label>
                        <input id="documents-q" name="q" value="{{ $search }}" placeholder="Cliente, email, placa, titulo..." class="admin-input">
                    </div>

                    <div>
                        <label for="documents-status" class="admin-field-label">Status</label>
                        <select id="documents-status" name="status" class="admin-select">
                            <option value="">Todos</option>
                            @foreach($statusOptions as $statusKey => $statusLabel)
                                <option value="{{ $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="documents-type" class="admin-field-label">Tipo</label>
                        <select id="documents-type" name="type" class="admin-select">
                            <option value="">Todos</option>
                            @foreach($typeOptions as $typeKey => $typeLabel)
                                <option value="{{ $typeKey }}" @selected($type === $typeKey)>{{ $typeLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="admin-btn-primary">Filtrar</button>
                        <a href="{{ route('admin.v2.documents.index') }}" class="admin-btn-soft">Limpar</a>
                    </div>
                </form>

                <div class="admin-quick-filters">
                    <a href="{{ route('admin.v2.documents.index', array_filter(['q' => $search !== '' ? $search : null, 'type' => $type !== '' ? $type : null])) }}" class="admin-filter-chip {{ $status === '' ? 'is-active' : '' }}">
                        <span>Todos</span>
                        <span>{{ number_format($globalTotalDocuments) }}</span>
                    </a>
                    @foreach($statusOptions as $statusKey => $statusLabel)
                        <a href="{{ route('admin.v2.documents.index', array_filter(['status' => $statusKey, 'q' => $search !== '' ? $search : null, 'type' => $type !== '' ? $type : null])) }}" class="admin-filter-chip {{ $status === $statusKey ? 'is-active' : '' }}">
                            <span>{{ $statusLabel }}</span>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="admin-data-table-wrapper hidden lg:block">
                <table class="admin-data-table">
                    <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Cliente</th>
                        <th>Veiculo</th>
                        <th>Status</th>
                        <th>Validade</th>
                        <th>Acoes</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($documents as $document)
                        @php
                            $statusLabel = $statusOptions[$document->status] ?? $document->status;
                            $statusClass = $statusClassMap[$document->status] ?? 'is-pending';
                            $typeLabel = $typeOptions[$document->tipo] ?? $document->tipo;
                        @endphp
                        <tr>
                            <td>
                                <div class="admin-row-title">{{ $document->titulo }}</div>
                                <div class="admin-row-meta">{{ $typeLabel }} · {{ $document->nome_original ?? 'Arquivo sem nome original' }}</div>
                            </td>
                            <td>
                                <div class="admin-row-title">{{ $document->user?->razao_social ?? $document->user?->name ?? 'Sem cliente vinculado' }}</div>
                                <div class="admin-row-meta">{{ $document->user?->email ?? 'Sem e-mail' }}</div>
                            </td>
                            <td>
                                <div class="admin-row-title">{{ $document->vehicle ? trim("{$document->vehicle->brand} {$document->vehicle->model} {$document->vehicle->model_year}") : 'Sem veiculo vinculado' }}</div>
                                <div class="admin-row-meta">{{ $document->vehicle?->plate ?? 'Sem placa' }}</div>
                            </td>
                            <td>
                                <span class="admin-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                <div class="admin-row-meta">
                                    {{ $document->visivel_cliente ? 'Visivel ao cliente' : 'Uso interno' }}
                                    @if($document->verificado_em)
                                        · {{ $document->verificado_em->format('d/m/Y H:i') }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="admin-row-title">{{ $document->validade?->format('d/m/Y') ?? 'Sem validade' }}</div>
                                <div class="admin-row-meta">{{ $document->estaVencido() ? 'Documento vencido' : ($document->tamanho_formatado ?? 'Sem tamanho') }}</div>
                            </td>
                            <td>
                                @include('admin.documents.partials.actions', ['document' => $document, 'statusOptions' => $statusOptions])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm font-semibold text-slate-500">Nenhum documento encontrado para o filtro atual.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </section>

            <section class="admin-mobile-list">
                @forelse($documents as $document)
                    @php
                        $statusLabel = $statusOptions[$document->status] ?? $document->status;
                        $statusClass = $statusClassMap[$document->status] ?? 'is-pending';
                        $typeLabel = $typeOptions[$document->tipo] ?? $document->tipo;
                    @endphp
                    <article class="admin-order-card">
                        <div class="admin-order-card-header">
                            <div>
                                <h3 class="admin-row-title">{{ $document->titulo }}</h3>
                                <p class="admin-row-meta">{{ $typeLabel }}</p>
                            </div>
                            <span class="admin-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>

                        <div class="admin-order-card-grid">
                            <div>
                                <span class="admin-detail-label">Cliente</span>
                                <span class="admin-detail-value">{{ $document->user?->razao_social ?? $document->user?->name ?? 'Nao vinculado' }}</span>
                            </div>
                            <div>
                                <span class="admin-detail-label">Veiculo</span>
                                <span class="admin-detail-value">{{ $document->vehicle?->plate ?? 'Sem placa' }}</span>
                            </div>
                            <div>
                                <span class="admin-detail-label">Validade</span>
                                <span class="admin-detail-value">{{ $document->validade?->format('d/m/Y') ?? 'Sem validade' }}</span>
                            </div>
                            <div>
                                <span class="admin-detail-label">Arquivo</span>
                                <span class="admin-detail-value">{{ $document->tamanho_formatado }}</span>
                            </div>
                        </div>

                        <div class="admin-row-meta">{{ $document->visivel_cliente ? 'Disponivel para o cliente' : 'Uso interno' }} · {{ $document->nome_original ?? 'Sem nome original' }}</div>

                        <div class="mt-4">
                            @include('admin.documents.partials.actions', ['document' => $document, 'statusOptions' => $statusOptions])
                        </div>
                    </article>
                @empty
                    <article class="admin-empty-state">Nenhum documento encontrado para o filtro atual.</article>
                @endforelse
            </section>

            <div class="mt-4">
                {{ $documents->links() }}
            </div>
        </div>

        <aside class="admin-card">
            <span class="admin-tag admin-tag-new">upload rapido</span>
            <h2 class="mt-3 admin-section-title">Cadastrar documento</h2>
            <p class="admin-section-note">Inclua o arquivo no pipeline com classificacao, vinculo e visibilidade corretos desde o primeiro envio.</p>

            <form method="POST" action="{{ route('admin.v2.documents.store') }}" enctype="multipart/form-data" class="mt-5 admin-stack">
                @csrf

                <div class="admin-form-grid">
                    <div>
                        <label for="doc-tipo" class="admin-field-label">Tipo</label>
                        <select id="doc-tipo" name="tipo" class="admin-select" required>
                            @foreach($typeOptions as $typeKey => $typeLabel)
                                <option value="{{ $typeKey }}" @selected(old('tipo') === $typeKey)>{{ $typeLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="doc-status" class="admin-field-label">Status inicial</label>
                        <select id="doc-status" name="status" class="admin-select" required>
                            @foreach($statusOptions as $statusKey => $statusLabel)
                                <option value="{{ $statusKey }}" @selected(old('status', 'verificado') === $statusKey)>{{ $statusLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="doc-titulo" class="admin-field-label">Titulo interno</label>
                    <input id="doc-titulo" name="titulo" value="{{ old('titulo') }}" class="admin-input" placeholder="Ex: ATPV-e assinado" required>
                </div>

                <div class="admin-form-grid">
                    <div>
                        <label for="doc-user" class="admin-field-label">Cliente</label>
                        <select id="doc-user" name="user_id" class="admin-select">
                            <option value="">Sem vinculo</option>
                            @foreach($userOptions as $userId => $userLabel)
                                <option value="{{ $userId }}" @selected((string) old('user_id') === (string) $userId)>{{ $userLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="doc-vehicle" class="admin-field-label">Veiculo</label>
                        <select id="doc-vehicle" name="vehicle_id" class="admin-select">
                            <option value="">Sem vinculo</option>
                            @foreach($vehicleOptions as $vehicleId => $vehicleLabel)
                                <option value="{{ $vehicleId }}" @selected((string) old('vehicle_id') === (string) $vehicleId)>{{ $vehicleLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="admin-form-grid">
                    <div>
                        <label for="doc-validade" class="admin-field-label">Validade</label>
                        <input id="doc-validade" type="date" name="validade" value="{{ old('validade') }}" class="admin-input">
                    </div>

                    <div>
                        <label for="doc-arquivo" class="admin-field-label">Arquivo</label>
                        <input id="doc-arquivo" type="file" name="arquivo" class="admin-file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <p class="admin-helper">PDF, JPG ou PNG com ate 5MB.</p>
                    </div>
                </div>

                <div>
                    <label for="doc-observacoes" class="admin-field-label">Observacoes</label>
                    <textarea id="doc-observacoes" name="observacoes" class="admin-textarea" placeholder="Contexto interno, excecoes ou orientacoes para a triagem.">{{ old('observacoes') }}</textarea>
                </div>

                <label class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                    <input type="hidden" name="visivel_cliente" value="0">
                    <input type="checkbox" name="visivel_cliente" value="1" class="h-4 w-4 rounded border-slate-300 text-blue-600" @checked(old('visivel_cliente', '1') === '1')>
                    Visivel para download do cliente no portal
                </label>

                <button type="submit" class="admin-btn-primary">Cadastrar documento</button>
            </form>
        </aside>
    </section>
@endsection