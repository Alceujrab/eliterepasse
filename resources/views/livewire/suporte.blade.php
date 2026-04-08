<div class="w-full bg-[#f1f5f9] min-h-screen">

    {{-- ─── Header ─────────────────────────────────────────────────── --}}
    <div class="page-hero">
        <div class="page-container py-8 sm:py-10 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div class="text-white">
                <p class="text-orange-300 text-sm font-bold uppercase tracking-widest mb-1">Portal do Lojista</p>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">💬 Central de Suporte</h1>
                <p class="text-blue-200 text-base mt-1">Abra chamados e acompanhe seu atendimento</p>
            </div>
            <button wire:click="abrirNovoTicket"
                class="btn-cta-md flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Abrir Chamado
            </button>
        </div>
    </div>

    <div class="page-container py-6 flex flex-col lg:flex-row gap-6">

        {{-- ─── Sidebar: Lista de Tickets ────────────────────────────── --}}
        <div class="w-full lg:w-[340px] flex-shrink-0 space-y-4">

            {{-- Filtros de status --}}
            <div class="flex gap-2 flex-wrap">
                @php
                    $filtros = [
                        'todos'           => ['label' => 'Todos',         'cor' => ''],
                        'aberto'          => ['label' => '🔴 Abertos',    'cor' => 'text-red-600'],
                        'em_atendimento'  => ['label' => '🟡 Em andamento','cor' => 'text-amber-600'],
                        'resolvido'       => ['label' => '🟢 Resolvidos', 'cor' => 'text-emerald-600'],
                    ];
                @endphp
                @foreach($filtros as $key => $f)
                    <button wire:click="$set('filtroStatus', '{{ $key }}')"
                        class="px-4 py-2 text-sm font-bold rounded-xl transition
                            {{ $filtroStatus === $key
                                ? 'bg-[#1a3a5c] text-white'
                                : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        {{ $f['label'] }}
                        @if(isset($contadores[$key]) && $contadores[$key] > 0)
                            <span class="ml-1 bg-white bg-opacity-20 text-xs px-2 py-0.5 rounded-full {{ $filtroStatus === $key ? '' : 'bg-gray-100' }}">{{ $contadores[$key] }}</span>
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- Lista --}}
            <div class="elite-card overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <span class="section-title !text-base">Meus Chamados</span>
                    <span class="badge bg-gray-100 text-gray-600">{{ $tickets->count() }}</span>
                </div>

                @forelse($tickets as $ticket)
                    <button wire:click="selectTicket({{ $ticket->id }})"
                        class="w-full text-left px-5 py-4 border-b border-gray-50 hover:bg-gray-50 transition flex gap-3 items-start
                            {{ $activeTicketId === $ticket->id ? 'bg-orange-50 border-l-4 border-l-orange-500' : '' }}">

                        {{-- Status dot --}}
                        <div class="flex-shrink-0 mt-1.5">
                            @php
                                $cor = match($ticket->status) {
                                    'aberto'             => 'bg-red-500',
                                    'em_atendimento'     => 'bg-amber-400',
                                    'aguardando_cliente' => 'bg-blue-400',
                                    'resolvido'          => 'bg-green-500',
                                    default              => 'bg-gray-400',
                                };
                            @endphp
                            <div class="w-3 h-3 rounded-full {{ $cor }} {{ $ticket->status === 'aberto' ? 'animate-pulse' : '' }}"></div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="text-base font-semibold text-gray-800 truncate">{{ $ticket->titulo }}</div>
                            <div class="text-sm text-gray-500 mt-0.5 flex items-center gap-2">
                                <span class="font-mono">{{ $ticket->numero }}</span>
                                <span>·</span>
                                <span>{{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                            {{-- SLA indicator --}}
                            @if($ticket->prazo_resposta && ! in_array($ticket->status, ['resolvido', 'fechado']))
                                @php $atrasado = now()->isAfter($ticket->prazo_resposta); @endphp
                                <div class="text-xs mt-1 font-bold {{ $atrasado ? 'text-red-500' : 'text-emerald-500' }}">
                                    @if($atrasado)
                                        ⚠️ SLA estourado ({{ $ticket->prazo_resposta->diffForHumans() }})
                                    @else
                                        ⏱️ Prazo: {{ $ticket->prazo_resposta->diffForHumans() }}
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="flex-shrink-0 flex flex-col items-end gap-1">
                            <span class="text-xs bg-gray-100 text-gray-500 font-bold px-2 py-0.5 rounded-full">
                                {{ $ticket->messages->count() }}
                            </span>
                            @if($ticket->avaliacao)
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="text-[9px] {{ $i <= $ticket->avaliacao ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </button>
                @empty
                    <div class="px-5 py-16 text-center text-base text-gray-400">
                        <div class="text-5xl mb-3">💬</div>
                        Nenhum chamado encontrado
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ─── Área principal ─────────────────────────────────────── --}}
        <div class="flex-1 min-w-0">

            {{-- Formulário de Novo Ticket --}}
            @if($showNovoTicket)
                <div class="elite-card p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="section-title">📝 Abrir Novo Chamado</h2>
                        <button wire:click="cancelarNovoTicket" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="criarTicket" class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider mb-1.5">Assunto *</label>
                            <input wire:model="titulo" type="text" placeholder="Descreva brevemente o problema..."
                                class="block w-full rounded-xl border border-gray-300 px-4 py-3 text-base focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <x-input-error :messages="$errors->get('titulo')" class="mt-1"/>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider mb-1.5">Categoria</label>
                                <select wire:model="categoria" class="block w-full rounded-xl border border-gray-300 px-4 py-3 text-base bg-white">
                                    <option value="duvida">❓ Dúvida</option>
                                    <option value="problema_tecnico">🔧 Problema Técnico</option>
                                    <option value="financeiro">💰 Financeiro</option>
                                    <option value="contrato">📄 Contrato</option>
                                    <option value="veiculo">🚗 Veículo</option>
                                    <option value="outro">📌 Outro</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider mb-1.5">Prioridade</label>
                                <select wire:model="prioridade" class="block w-full rounded-xl border border-gray-300 px-4 py-3 text-base bg-white">
                                    <option value="baixa">🟢 Baixa (72h)</option>
                                    <option value="media" selected>🟡 Média (24h)</option>
                                    <option value="alta">🟠 Alta (8h)</option>
                                    <option value="urgente">🔴 Urgente (2h)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider mb-1.5">Descrição detalhada *</label>
                            <textarea wire:model="descricao" rows="5" placeholder="Explique com detalhes o que está acontecendo..."
                                class="block w-full rounded-xl border border-gray-300 px-4 py-3 text-base focus:ring-2 focus:ring-orange-500 resize-none"></textarea>
                            <x-input-error :messages="$errors->get('descricao')" class="mt-1"/>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider mb-1.5">Anexos (opcional)</label>
                            <input wire:model="arquivos" type="file" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                class="block w-full text-base text-gray-500 file:mr-3 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                            <p class="text-sm text-gray-400 mt-1">PDF, JPG, PNG, DOC — máx. 5MB por arquivo</p>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="submit" class="btn-cta-lg flex-1 flex items-center justify-center gap-2">
                                🚀 Enviar Chamado
                            </button>
                            <button type="button" wire:click="cancelarNovoTicket"
                                class="px-6 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3.5 rounded-xl transition text-base">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

            {{-- ─── Conversa do Ticket Ativo ───────────────────────── --}}
            @elseif($activeTicket)
                <div class="elite-card overflow-hidden flex flex-col" style="height: calc(100vh - 200px); min-height: 500px;">

                    {{-- Header do ticket --}}
                    <div class="px-6 py-5 border-b border-gray-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span class="font-mono text-sm text-gray-400">{{ $activeTicket->numero }}</span>
                                    @php
                                        $cores = [
                                            'aberto'             => 'bg-red-100 text-red-700',
                                            'em_atendimento'     => 'bg-amber-100 text-amber-700',
                                            'aguardando_cliente' => 'bg-blue-100 text-blue-700',
                                            'resolvido'          => 'bg-green-100 text-green-700',
                                            'fechado'            => 'bg-gray-100 text-gray-600',
                                        ];
                                    @endphp
                                    <span class="badge {{ $cores[$activeTicket->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ \App\Models\Ticket::statusLabels()[$activeTicket->status] ?? $activeTicket->status }}
                                    </span>
                                    <span class="badge bg-gray-100 text-gray-600">
                                        {{ ucfirst($activeTicket->prioridade) }}
                                    </span>
                                    @if($activeTicket->categoria)
                                        <span class="badge bg-blue-50 text-blue-600">
                                            {{ \App\Models\Ticket::categoriaLabels()[$activeTicket->categoria] ?? $activeTicket->categoria }}
                                        </span>
                                    @endif
                                </div>
                                <h2 class="text-lg font-black text-gray-800">{{ $activeTicket->titulo }}</h2>
                            </div>

                            {{-- SLA badge --}}
                            @if($activeTicket->prazo_resposta && ! in_array($activeTicket->status, ['resolvido', 'fechado']))
                                @php $atrasado = now()->isAfter($activeTicket->prazo_resposta); @endphp
                                <div class="text-xs font-bold px-3 py-1.5 rounded-xl flex-shrink-0
                                    {{ $atrasado ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }}">
                                    @if($atrasado) ⚠️ SLA estourado @else ⏱️ {{ $activeTicket->prazo_resposta->diffForHumans() }} @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Mensagens --}}
                    <div class="flex-1 overflow-y-auto p-6 space-y-5" id="messages-area">
                        @foreach($activeTicket->messages as $msg)
                            <div class="flex {{ $msg->is_admin ? 'justify-start' : 'justify-end' }}">
                                <div class="max-w-[75%]">
                                    <div class="{{ $msg->is_admin
                                        ? 'bg-gray-100 text-gray-800 rounded-2xl rounded-tl-sm'
                                        : 'bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-2xl rounded-tr-sm' }} px-5 py-3.5 text-base leading-relaxed shadow-sm">
                                        @if($msg->is_admin && $msg->is_internal ?? false)
                                            <div class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                                                🔒 Nota Interna
                                            </div>
                                        @endif
                                        {!! nl2br(e($msg->mensagem)) !!}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1.5 {{ $msg->is_admin ? 'text-left' : 'text-right' }}">
                                        {{ $msg->is_admin ? '🛡️ Suporte Elite' : '👤 Você' }} · {{ $msg->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Input de resposta / Resolvido / Avaliação --}}
                    @if(! in_array($activeTicket->status, ['resolvido', 'fechado']))
                        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">
                            <form wire:submit="enviarMensagem" class="flex gap-3 items-end">
                                <textarea wire:model="newMessage" rows="2" placeholder="Digite sua mensagem..."
                                    class="flex-1 rounded-xl border border-gray-300 px-4 py-3 text-base focus:ring-2 focus:ring-orange-500 resize-none"></textarea>
                                <button type="submit"
                                    class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-3.5 rounded-xl transition flex-shrink-0 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">
                            {{-- Avaliação --}}
                            @if($activeTicket->status === 'resolvido' && !$activeTicket->avaliacao)
                                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-3">
                                    <p class="text-sm font-black text-emerald-800 mb-2">⭐ Como foi seu atendimento?</p>
                                    <div class="flex gap-1 mb-3">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button wire:click="$set('avaliacao', {{ $i }})"
                                                class="text-2xl transition hover:scale-110 {{ $avaliacao >= $i ? 'text-yellow-400' : 'text-gray-300' }}">★</button>
                                        @endfor
                                    </div>
                                    @if($avaliacao > 0)
                                        <textarea wire:model="avaliacaoComentario" rows="2" placeholder="Comentário opcional..."
                                            class="w-full rounded-xl border border-emerald-200 px-4 py-2 text-sm focus:ring-2 focus:ring-emerald-400 resize-none mb-2"></textarea>
                                        <button wire:click="avaliarTicket"
                                            class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 rounded-xl transition text-sm">
                                            Enviar Avaliação
                                        </button>
                                    @endif
                                </div>
                            @elseif($activeTicket->avaliacao)
                                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 mb-3 text-center">
                                    <div class="flex justify-center gap-0.5 mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="text-lg {{ $i <= $activeTicket->avaliacao ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                                        @endfor
                                    </div>
                                    <p class="text-xs text-emerald-700 font-semibold">Obrigado pela avaliação!</p>
                                </div>
                            @endif

                            <div class="flex gap-3">
                                <button wire:click="reabrirTicket"
                                    class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2">
                                    🔄 Reabrir Chamado
                                </button>
                                <button wire:click="abrirNovoTicket"
                                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2">
                                    ➕ Novo Chamado
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

            {{-- ─── Estado vazio ────────────────────────────────────── --}}
            @else
                <div class="elite-card flex flex-col items-center justify-center text-center py-24 px-6">
                    <div class="w-20 h-20 rounded-full bg-orange-100 flex items-center justify-center mb-5 text-4xl">💬</div>
                    <h3 class="text-xl font-black text-gray-800 mb-2">Selecione um chamado ou abra um novo</h3>
                    <p class="text-base text-gray-400 mb-6 max-w-sm">
                        Nossa equipe responde em até <strong>2 horas</strong> para chamados urgentes e <strong>24 horas</strong> para os demais.
                    </p>
                    <button wire:click="abrirNovoTicket" class="btn-cta-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Abrir Novo Chamado
                    </button>
                    <div class="grid grid-cols-3 gap-5 mt-10 max-w-md w-full">
                        <div class="text-center">
                            <p class="text-3xl font-black text-[#1a3a5c]">{{ $contadores['aberto'] ?? 0 }}</p>
                            <p class="text-sm text-gray-400">Abertos</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-black text-amber-500">{{ $contadores['em_atendimento'] ?? 0 }}</p>
                            <p class="text-sm text-gray-400">Em atendimento</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-black text-emerald-500">{{ $contadores['resolvido'] ?? 0 }}</p>
                            <p class="text-sm text-gray-400">Resolvidos</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Bottom nav agora no layout compartilhado --}}
</div>

{{-- Auto-scroll --}}
<script>
    document.addEventListener('livewire:navigated', () => scrollToBottom());
    document.addEventListener('livewire:updated', () => scrollToBottom());
    function scrollToBottom() {
        const el = document.getElementById('messages-area');
        if (el) el.scrollTop = el.scrollHeight;
    }
    scrollToBottom();
</script>
