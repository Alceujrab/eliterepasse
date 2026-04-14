@extends('admin.layouts.app')

@php
    $pageTitle = 'WhatsApp Inbox';
    $pageSubtitle = 'Fila de atendimento em cima dos tickets do WhatsApp com thread, resposta e controle de status.';

    $statusClassMap = [
        'aberto' => 'is-cancelled',
        'em_atendimento' => 'is-awaiting',
        'aguardando_cliente' => 'is-confirmed',
        'resolvido' => 'is-paid',
        'fechado' => 'is-pending',
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
            <p class="admin-metric-label">Conversas no filtro</p>
            <p class="admin-metric-value">{{ number_format($summary['filteredTotal']) }}</p>
            <p class="admin-metric-footnote">Base total: {{ number_format($globalTotalTickets) }} tickets WhatsApp</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Abertos</p>
            <p class="admin-metric-value">{{ number_format($summary['open']) }}</p>
            <p class="admin-metric-footnote">Demandas aguardando triagem</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Em atendimento</p>
            <p class="admin-metric-value">{{ number_format($summary['inProgress']) }}</p>
            <p class="admin-metric-footnote">Conversas já assumidas pelo time</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Aguardando cliente</p>
            <p class="admin-metric-value">{{ number_format($summary['waitingCustomer']) }}</p>
            <p class="admin-metric-footnote">Resposta enviada, retorno pendente</p>
        </article>
        <article class="admin-metric-card">
            <p class="admin-metric-label">Resolvidos</p>
            <p class="admin-metric-value">{{ number_format($summary['resolved']) }}</p>
            <p class="admin-metric-footnote">Já tratados pela equipe</p>
        </article>
    </section>

    <section class="mt-6 admin-card">
        <div class="admin-toolbar">
            <div class="admin-toolbar-main">
                <span class="admin-tag {{ $hasActiveFilters ? 'admin-tag-migration' : 'admin-tag-new' }}">{{ $hasActiveFilters ? 'fila filtrada' : 'inbox ativa' }}</span>
                <h2 class="mt-3 admin-section-title">Central de conversas</h2>
                <p class="admin-section-note">Acompanhe tickets recebidos via webhook da Evolution e responda no mesmo workspace.</p>
            </div>
            <div class="admin-toolbar-actions">
                <a href="{{ route('admin.v2.whatsapp-inbox.index', request()->query()) }}" class="admin-btn-soft">Atualizar</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.v2.whatsapp-inbox.index') }}" class="admin-filter-grid md:grid-cols-[1fr_240px_auto] md:items-end mt-5">
            <div>
                <label for="wa-inbox-q" class="admin-field-label">Busca</label>
                <input id="wa-inbox-q" name="q" value="{{ $search }}" placeholder="Cliente, telefone, ticket ou mensagem" class="admin-input">
            </div>
            <div>
                <label for="wa-inbox-status" class="admin-field-label">Status</label>
                <select id="wa-inbox-status" name="status" class="admin-select">
                    @foreach($statusOptions as $statusKey => $statusLabel)
                        <option value="{{ $statusKey === 'aberto' ? '' : $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="admin-btn-primary">Filtrar</button>
                <a href="{{ route('admin.v2.whatsapp-inbox.index') }}" class="admin-btn-soft">Limpar</a>
            </div>
        </form>
    </section>

    <section class="mt-6 admin-ticket-layout">
        <aside class="admin-ticket-list">
            @forelse($tickets as $ticketItem)
                @php
                    $statusClass = $statusClassMap[$ticketItem->status] ?? 'is-pending';
                    $lastMessage = $ticketItem->messages->last();
                    $userLabel = $ticketItem->user?->razao_social ?? $ticketItem->user?->name ?? 'Contato sem cadastro';
                @endphp
                <a href="{{ route('admin.v2.whatsapp-inbox.index', array_filter(['ticket' => $ticketItem->id, 'status' => $status !== 'aberto' ? $status : null, 'q' => $search !== '' ? $search : null])) }}" class="admin-ticket-list-item {{ $selectedTicket && $selectedTicket->id === $ticketItem->id ? 'is-active' : '' }}">
                    <div class="admin-ticket-header">
                        <div>
                            <div class="admin-row-title">{{ $userLabel }}</div>
                            <div class="admin-row-meta">{{ $ticketItem->numero ?: 'Sem numero' }}</div>
                        </div>
                        <span class="admin-status-badge {{ $statusClass }}">{{ $statusOptions[$ticketItem->status] ?? $ticketItem->status }}</span>
                    </div>
                    <div class="admin-row-meta">{{ $ticketItem->user?->phone ?? 'Telefone nao cadastrado' }}</div>
                    <div class="mt-2 text-sm font-medium text-slate-600">{{ $lastMessage?->mensagem ?? $ticketItem->titulo }}</div>
                    <div class="mt-2 admin-row-meta">{{ $lastMessage?->created_at?->diffForHumans() ?? $ticketItem->created_at?->diffForHumans() }}</div>
                </a>
            @empty
                <div class="admin-empty-state">Nenhuma conversa encontrada para o filtro atual.</div>
            @endforelse

            <div class="mt-4">
                {{ $tickets->links() }}
            </div>
        </aside>

        <section class="admin-card">
            @if($selectedTicket)
                @php
                    $selectedStatusClass = $statusClassMap[$selectedTicket->status] ?? 'is-pending';
                    $selectedUserLabel = $selectedTicket->user?->razao_social ?? $selectedTicket->user?->name ?? 'Contato sem cadastro';
                    $selectedPhone = $selectedTicket->user?->phone;
                @endphp
                <div class="admin-toolbar">
                    <div class="admin-toolbar-main">
                        <div class="flex flex-wrap gap-2">
                            <span class="admin-status-badge {{ $selectedStatusClass }}">{{ $statusOptions[$selectedTicket->status] ?? $selectedTicket->status }}</span>
                        </div>
                        <h2 class="mt-3 admin-section-title">{{ $selectedUserLabel }}</h2>
                        <p class="admin-section-note">{{ $selectedTicket->numero ?: 'Sem numero' }} · {{ $selectedPhone ?: 'Telefone nao cadastrado' }}</p>
                    </div>
                    <div class="admin-toolbar-actions">
                        @if($selectedPhone)
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', '55' . $selectedPhone) }}" target="_blank" class="admin-btn-primary">Abrir WA</a>
                        @endif
                    </div>
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    <div>
                        <span class="admin-detail-label">Cliente</span>
                        <span class="admin-detail-value">{{ $selectedUserLabel }}</span>
                    </div>
                    <div>
                        <span class="admin-detail-label">Criado em</span>
                        <span class="admin-detail-value">{{ $selectedTicket->created_at?->format('d/m/Y H:i') ?? 'Sem data' }}</span>
                    </div>
                    <div>
                        <span class="admin-detail-label">Assunto do ticket</span>
                        <span class="admin-detail-value">{{ $selectedTicket->titulo ?: 'Sem assunto' }}</span>
                    </div>
                </div>

                <div class="mt-5 admin-ticket-thread">
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
                        <div class="admin-empty-state">Essa conversa ainda nao possui mensagens.</div>
                    @endforelse
                </div>

                <div class="mt-6 grid gap-4 xl:grid-cols-2">
                    <form method="POST" action="{{ route('admin.v2.whatsapp-inbox.reply', $selectedTicket) }}" class="admin-card !p-4">
                        @csrf
                        <h3 class="admin-section-title">Responder conversa</h3>
                        <p class="admin-section-note">A resposta normal tenta envio pela instancia padrao. Nota interna nao dispara WhatsApp.</p>
                        <div class="mt-4">
                            <label class="admin-field-label" for="whatsapp-reply">Mensagem</label>
                            <textarea id="whatsapp-reply" name="mensagem" class="admin-textarea" placeholder="Digite a resposta para o cliente." required></textarea>
                        </div>
                        <div class="mt-3 admin-form-grid">
                            <div>
                                <label class="admin-field-label" for="whatsapp-new-status">Novo status</label>
                                <select id="whatsapp-new-status" name="novo_status" class="admin-select">
                                    <option value="">Manter fluxo automatico</option>
                                    <option value="aguardando_cliente">Aguardar cliente</option>
                                    <option value="em_atendimento">Em atendimento</option>
                                    <option value="resolvido">Resolvido</option>
                                    <option value="fechado">Fechado</option>
                                </select>
                            </div>
                            <label class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 xl:mt-6">
                                <input type="hidden" name="is_internal" value="0">
                                <input type="checkbox" name="is_internal" value="1" class="h-4 w-4 rounded border-slate-300 text-blue-600">
                                Nota interna
                            </label>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="admin-btn-primary">Enviar resposta</button>
                        </div>
                    </form>

                    <div class="admin-stack">
                        <form method="POST" action="{{ route('admin.v2.whatsapp-inbox.update-status', $selectedTicket) }}" class="admin-card !p-4">
                            @csrf
                            <h3 class="admin-section-title">Atualizar status</h3>
                            <div class="mt-4">
                                <label class="admin-field-label" for="whatsapp-status">Status</label>
                                <select id="whatsapp-status" name="status" class="admin-select">
                                    <option value="aberto" @selected($selectedTicket->status === 'aberto')>Abertos</option>
                                    <option value="em_atendimento" @selected($selectedTicket->status === 'em_atendimento')>Em atendimento</option>
                                    <option value="aguardando_cliente" @selected($selectedTicket->status === 'aguardando_cliente')>Aguardando cliente</option>
                                    <option value="resolvido" @selected($selectedTicket->status === 'resolvido')>Resolvidos</option>
                                    <option value="fechado" @selected($selectedTicket->status === 'fechado')>Fechados</option>
                                </select>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button type="submit" class="admin-btn-soft">Salvar status</button>
                            </div>
                        </form>

                        <section class="admin-card !p-4">
                            <h3 class="admin-section-title">Webhook Evolution</h3>
                            <p class="admin-section-note">Mensagens entram por webhook e viram tickets do tipo whatsapp no sistema.</p>
                            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 break-all">{{ url('/webhook/evolution') }}</div>
                        </section>
                    </div>
                </div>
            @else
                <div class="admin-empty-state">
                    Nenhuma conversa disponivel. Configure o webhook da Evolution e aguarde novas mensagens.
                </div>
            @endif
        </section>
    </section>
@endsection