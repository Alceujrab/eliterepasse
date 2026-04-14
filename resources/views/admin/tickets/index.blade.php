@extends('admin.layouts.app')

@php
    use App\Models\Ticket;

    $pageTitle = 'Tickets (Admin v2)';
    $pageSubtitle = 'Workspace de atendimento com fila priorizada, SLA e resposta operacional no mesmo fluxo.';

    $statusClassMap = [
        'aberto' => 'is-cancelled',
        'em_atendimento' => 'is-awaiting',
        'aguardando_cliente' => 'is-confirmed',
        'resolvido' => 'is-paid',
        'fechado' => 'is-pending',
    ];

    $priorityBadgeMap = [
        'baixa' => 'is-pending',
        'media' => 'is-confirmed',
        'alta' => 'is-awaiting',
        'urgente' => 'is-cancelled',
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
            <p class="admin-metric-label">Tickets no filtro</p>
            <p class="admin-metric-value">{{ number_format($summary['filteredTotal']) }}</p>
            <p class="admin-metric-footnote">Base total: {{ number_format($globalTotalTickets) }} chamados</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Abertos</p>
            <p class="admin-metric-value">{{ number_format($summary['open']) }}</p>
            <p class="admin-metric-footnote">Ainda sem atendimento assumido</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Em atendimento</p>
            <p class="admin-metric-value">{{ number_format($summary['inProgress']) }}</p>
            <p class="admin-metric-footnote">Fila ativa do time interno</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Urgentes</p>
            <p class="admin-metric-value">{{ number_format($summary['urgent']) }}</p>
            <p class="admin-metric-footnote">Chamados com maior risco operacional</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">SLA estourado</p>
            <p class="admin-metric-value">{{ number_format($summary['overdue']) }}</p>
            <p class="admin-metric-footnote">Precisam de tratamento imediato</p>
        </article>
    </section>

    <section class="mt-6 admin-card">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag {{ $hasActiveFilters ? 'admin-tag-migration' : 'admin-tag-new' }}">{{ $hasActiveFilters ? 'fila filtrada' : 'central ativa' }}</span>
                <h2 class="mt-3 admin-section-title">Central de tickets</h2>
                <p class="admin-section-note">Monitore, atribua e responda chamados sem sair do fluxo principal do atendimento.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.tickets.index', request()->query()) }}" class="admin-btn-soft">Atualizar</a>
                <a href="/admin/tickets" class="admin-btn-soft">Abrir legado</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.v2.tickets.index') }}" class="admin-filter-grid-wide md:items-end">
            <div>
                <label for="tickets-q" class="admin-field-label">Busca</label>
                <input id="tickets-q" name="q" value="{{ $search }}" placeholder="TKT-2026, cliente, placa, assunto..." class="admin-input">
            </div>
            <div>
                <label for="tickets-status" class="admin-field-label">Status</label>
                <select id="tickets-status" name="status" class="admin-select">
                    <option value="">Todos</option>
                    @foreach($statusOptions as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="tickets-priority" class="admin-field-label">Prioridade</label>
                <select id="tickets-priority" name="priority" class="admin-select">
                    <option value="">Todas</option>
                    @foreach($priorityOptions as $priorityKey => $priorityLabel)
                        <option value="{{ $priorityKey }}" @selected($priority === $priorityKey)>{{ $priorityLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="admin-btn-primary">Filtrar</button>
                <a href="{{ route('admin.v2.tickets.index') }}" class="admin-btn-soft">Limpar</a>
            </div>
        </form>
    </section>

    <section class="mt-6 admin-split-grid">
        <aside class="admin-card">
            <span class="admin-tag admin-tag-new">novo ticket</span>
            <h2 class="mt-3 admin-section-title">Abrir chamado interno</h2>
            <p class="admin-section-note">Cadastre um ticket já com cliente, prioridade e agente inicial quando a equipe identificar a demanda antes do portal.</p>

            <form method="POST" action="{{ route('admin.v2.tickets.store') }}" class="mt-5 admin-stack">
                @csrf
                <div>
                    <label for="ticket-titulo" class="admin-field-label">Assunto</label>
                    <input id="ticket-titulo" name="titulo" value="{{ old('titulo') }}" class="admin-input" placeholder="Descreva brevemente o problema" required>
                </div>

                <div class="admin-form-grid">
                    <div>
                        <label for="ticket-categoria" class="admin-field-label">Categoria</label>
                        <select id="ticket-categoria" name="categoria" class="admin-select" required>
                            @foreach($categoryOptions as $categoryKey => $categoryLabel)
                                <option value="{{ $categoryKey }}" @selected(old('categoria', 'duvida') === $categoryKey)>{{ $categoryLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="ticket-prioridade-create" class="admin-field-label">Prioridade</label>
                        <select id="ticket-prioridade-create" name="prioridade" class="admin-select" required>
                            @foreach($priorityOptions as $priorityKey => $priorityLabel)
                                <option value="{{ $priorityKey }}" @selected(old('prioridade', 'media') === $priorityKey)>{{ $priorityLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="ticket-user" class="admin-field-label">Cliente</label>
                    <select id="ticket-user" name="user_id" class="admin-select" required>
                        <option value="">Selecione</option>
                        @foreach($customerOptions as $userId => $userLabel)
                            <option value="{{ $userId }}" @selected((string) old('user_id') === (string) $userId)>{{ $userLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="admin-form-grid">
                    <div>
                        <label for="ticket-order" class="admin-field-label">Pedido</label>
                        <select id="ticket-order" name="order_id" class="admin-select">
                            <option value="">Sem vinculo</option>
                            @foreach($orderOptions as $orderId => $orderLabel)
                                <option value="{{ $orderId }}" @selected((string) old('order_id') === (string) $orderId)>{{ $orderLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="ticket-vehicle" class="admin-field-label">Veiculo</label>
                        <select id="ticket-vehicle" name="vehicle_id" class="admin-select">
                            <option value="">Sem vinculo</option>
                            @foreach($vehicleOptions as $vehicleId => $vehicleLabel)
                                <option value="{{ $vehicleId }}" @selected((string) old('vehicle_id') === (string) $vehicleId)>{{ $vehicleLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="ticket-agent" class="admin-field-label">Agente responsavel</label>
                    <select id="ticket-agent" name="atribuido_a" class="admin-select">
                        <option value="">Nao atribuir agora</option>
                        @foreach($agentOptions as $agentId => $agentName)
                            <option value="{{ $agentId }}" @selected((string) old('atribuido_a') === (string) $agentId)>{{ $agentName }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="ticket-descricao" class="admin-field-label">Mensagem inicial</label>
                    <textarea id="ticket-descricao" name="descricao" class="admin-textarea" placeholder="Contexto inicial do atendimento, erro relatado ou necessidade do cliente." required>{{ old('descricao') }}</textarea>
                </div>

                <button type="submit" class="admin-btn-primary">Criar ticket</button>
            </form>
        </aside>

        <div class="admin-stack">
            <section class="admin-ticket-layout">
                <div class="admin-ticket-list">
                    @forelse($tickets as $ticketItem)
                        @php
                            $statusClass = $statusClassMap[$ticketItem->status] ?? 'is-pending';
                            $priorityClass = $priorityBadgeMap[$ticketItem->prioridade] ?? 'is-pending';
                        @endphp
                        <a href="{{ route('admin.v2.tickets.index', array_filter(['ticket' => $ticketItem->id, 'status' => $status !== '' ? $status : null, 'priority' => $priority !== '' ? $priority : null, 'q' => $search !== '' ? $search : null])) }}" class="admin-ticket-list-item {{ $selectedTicket && $selectedTicket->id === $ticketItem->id ? 'is-active' : '' }}">
                            <div class="admin-ticket-header">
                                <div>
                                    <div class="admin-row-title">{{ $ticketItem->numero ?: 'Sem numero' }}</div>
                                    <div class="admin-row-meta">{{ $ticketItem->titulo ?: 'Sem assunto' }}</div>
                                </div>
                                <span class="admin-status-badge {{ $priorityClass }}">{{ strtoupper($ticketItem->prioridade) }}</span>
                            </div>
                            <div class="admin-row-meta">{{ $ticketItem->user?->razao_social ?? $ticketItem->user?->name ?? 'Sem cliente' }}</div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span class="admin-status-badge {{ $statusClass }}">{{ $statusOptions[$ticketItem->status] ?? $ticketItem->status }}</span>
                                @if($ticketItem->estaAtrasado())
                                    <span class="admin-status-badge is-cancelled">SLA atrasado</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="admin-empty-state">Nenhum ticket encontrado para o filtro atual.</div>
                    @endforelse
                </div>

                <section class="admin-card">
                    @if($selectedTicket)
                        @php
                            $selectedStatusClass = $statusClassMap[$selectedTicket->status] ?? 'is-pending';
                            $selectedPriorityClass = $priorityBadgeMap[$selectedTicket->prioridade] ?? 'is-pending';
                        @endphp
                        <div class="admin-toolbar">
                            <div class="admin-toolbar-main">
                                <div class="flex flex-wrap gap-2">
                                    <span class="admin-status-badge {{ $selectedPriorityClass }}">{{ strtoupper($selectedTicket->prioridade) }}</span>
                                    <span class="admin-status-badge {{ $selectedStatusClass }}">{{ $statusOptions[$selectedTicket->status] ?? $selectedTicket->status }}</span>
                                </div>
                                <h2 class="mt-3 admin-section-title">{{ $selectedTicket->numero ?: 'Ticket sem numero' }}</h2>
                                <p class="admin-section-note">{{ $selectedTicket->titulo ?: 'Sem assunto' }}</p>
                            </div>
                            <div class="admin-toolbar-actions">
                                <a href="/admin/tickets/{{ $selectedTicket->id }}" class="admin-btn-soft">Ver legado</a>
                            </div>
                        </div>

                        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                            <div>
                                <span class="admin-detail-label">Cliente</span>
                                <span class="admin-detail-value">{{ $selectedTicket->user?->razao_social ?? $selectedTicket->user?->name ?? 'Nao vinculado' }}</span>
                            </div>
                            <div>
                                <span class="admin-detail-label">Agente</span>
                                <span class="admin-detail-value">{{ $selectedTicket->atribuidoA?->name ?? 'Nao atribuido' }}</span>
                            </div>
                            <div>
                                <span class="admin-detail-label">Categoria</span>
                                <span class="admin-detail-value">{{ $categoryOptions[$selectedTicket->categoria] ?? $selectedTicket->categoria }}</span>
                            </div>
                            <div>
                                <span class="admin-detail-label">SLA</span>
                                <span class="admin-detail-value">{{ $selectedTicket->prazo_resposta?->format('d/m/Y H:i') ?? 'Nao definido' }}</span>
                            </div>
                        </div>

                        <div class="mt-4 admin-ticket-thread">
                            @forelse($selectedTicket->messages as $message)
                                <article class="admin-thread-bubble {{ $message->is_admin ? 'is-admin' : '' }} {{ $message->is_internal ? 'is-internal' : '' }}">
                                    <div class="admin-thread-meta">
                                        {{ $message->user?->name ?? ($message->is_admin ? 'Admin' : 'Cliente') }} · {{ $message->created_at?->format('d/m/Y H:i') }}
                                        @if($message->is_internal)
                                            · nota interna
                                        @endif
                                    </div>
                                    <div class="admin-thread-text">{{ $message->mensagem }}</div>
                                </article>
                            @empty
                                <div class="admin-empty-state">Este ticket ainda nao possui mensagens.</div>
                            @endforelse
                        </div>

                        <div class="mt-6 grid gap-4 xl:grid-cols-2">
                            <form method="POST" action="{{ route('admin.v2.tickets.reply', $selectedTicket) }}" class="admin-card !p-4">
                                @csrf
                                <h3 class="admin-section-title">Responder ticket</h3>
                                <p class="admin-section-note">Envie uma resposta ao cliente ou registre uma nota interna.</p>
                                <div class="mt-4">
                                    <label class="admin-field-label" for="reply-message">Mensagem</label>
                                    <textarea id="reply-message" name="mensagem" class="admin-textarea" placeholder="Digite a resposta ou orientacao para o cliente." required></textarea>
                                </div>
                                <div class="mt-3 admin-form-grid">
                                    <div>
                                        <label class="admin-field-label" for="reply-status">Novo status</label>
                                        <select id="reply-status" name="novo_status" class="admin-select">
                                            <option value="">Manter atual</option>
                                            @foreach($statusOptions as $statusKey => $statusLabel)
                                                <option value="{{ $statusKey }}">{{ $statusLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 xl:mt-6">
                                        <input type="hidden" name="is_internal" value="0">
                                        <input type="checkbox" name="is_internal" value="1" class="h-4 w-4 rounded border-slate-300 text-blue-600">
                                        Nota interna
                                    </label>
                                </div>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <button type="submit" class="admin-btn-primary">Enviar resposta</button>
                                </div>
                            </form>

                            <div class="admin-stack">
                                <form method="POST" action="{{ route('admin.v2.tickets.assign', $selectedTicket) }}" class="admin-card !p-4">
                                    @csrf
                                    <h3 class="admin-section-title">Atribuir agente</h3>
                                    <div class="mt-4">
                                        <label class="admin-field-label" for="assign-agent">Responsavel</label>
                                        <select id="assign-agent" name="atribuido_a" class="admin-select" required>
                                            @foreach($agentOptions as $agentId => $agentName)
                                                <option value="{{ $agentId }}" @selected((string) $selectedTicket->atribuido_a === (string) $agentId)>{{ $agentName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" class="admin-btn-soft">Salvar atribuicao</button>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('admin.v2.tickets.status', $selectedTicket) }}" class="admin-card !p-4">
                                    @csrf
                                    <h3 class="admin-section-title">Atualizar status</h3>
                                    <div class="mt-4">
                                        <label class="admin-field-label" for="ticket-status-update">Status</label>
                                        <select id="ticket-status-update" name="status" class="admin-select" required>
                                            @foreach($statusOptions as $statusKey => $statusLabel)
                                                <option value="{{ $statusKey }}" @selected($selectedTicket->status === $statusKey)>{{ $statusLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <button type="submit" class="admin-btn-soft">Aplicar status</button>
                                        @if($selectedTicket->order)
                                            <a href="{{ route('admin.v2.orders.index', ['q' => $selectedTicket->order->numero]) }}" class="admin-btn-soft">Ir para pedido</a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="admin-empty-state">Nenhum ticket selecionado. Escolha um item da fila para abrir o workspace de atendimento.</div>
                    @endif
                </section>
            </section>

            <div>
                {{ $tickets->links() }}
            </div>
        </div>
    </section>
@endsection