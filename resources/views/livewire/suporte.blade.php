<div class="w-full bg-[#f8fafc] min-h-screen">

    {{-- Header --}}
    <div class="w-full bg-gradient-to-r from-primary to-blue-900 relative overflow-hidden mb-6 shadow-sm">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex justify-between items-center">
            <div class="text-white z-10">
                <h1 class="text-3xl font-black tracking-tight italic uppercase leading-none">Central de Suporte</h1>
                <p class="text-base font-medium mt-1 opacity-80">Abra chamados e acompanhe seu atendimento</p>
            </div>
            <button wire:click="abrirNovoTicket"
                class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-bold px-5 py-3 rounded-xl transition shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Abrir Chamado
            </button>
        </div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pb-16 flex flex-col lg:flex-row gap-6">

        {{-- ─── Sidebar: Lista de Tickets ────────────────────────────── --}}
        <div class="w-full lg:w-[340px] flex-shrink-0">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <span class="font-bold text-gray-800 text-sm">Meus Chamados</span>
                    <span class="text-xs text-gray-400">{{ $tickets->count() }} total</span>
                </div>

                @forelse($tickets as $ticket)
                    <button wire:click="selectTicket({{ $ticket->id }})"
                        class="w-full text-left px-4 py-3.5 border-b border-gray-50 hover:bg-gray-50 transition flex gap-3 items-start
                            {{ $activeTicketId === $ticket->id ? 'bg-orange-50 border-l-4 border-l-orange-500' : '' }}">

                        {{-- Status dot --}}
                        <div class="flex-shrink-0 mt-1">
                            @php
                                $cor = match($ticket->status) {
                                    'aberto' => 'bg-red-500',
                                    'em_atendimento' => 'bg-amber-400',
                                    'aguardando_cliente' => 'bg-blue-400',
                                    'resolvido' => 'bg-green-500',
                                    default => 'bg-gray-400',
                                };
                            @endphp
                            <div class="w-2.5 h-2.5 rounded-full {{ $cor }} {{ $ticket->status === 'aberto' ? 'animate-pulse' : '' }}"></div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-gray-800 truncate">{{ $ticket->titulo }}</div>
                            <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-2">
                                <span class="font-mono">{{ $ticket->numero }}</span>
                                <span>·</span>
                                <span>{{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        @if($ticket->messages->where('is_admin', true)->count() > 0)
                            <div class="flex-shrink-0">
                                <span class="text-xs bg-orange-100 text-orange-700 font-bold px-2 py-0.5 rounded-full">
                                    {{ $ticket->messages->count() }}
                                </span>
                            </div>
                        @endif
                    </button>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Nenhum chamado aberto
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ─── Área principal ─────────────────────────────────────── --}}
        <div class="flex-1 min-w-0">

            {{-- Formulário de Novo Ticket --}}
            @if($showNovoTicket)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg font-black text-gray-800">Abrir Novo Chamado</h2>
                        <button wire:click="cancelarNovoTicket" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="criarTicket" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Assunto *</label>
                            <input wire:model="titulo" type="text" placeholder="Descreva brevemente o problema..."
                                class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            <x-input-error :messages="$errors->get('titulo')" class="mt-1"/>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Categoria</label>
                                <select wire:model="categoria" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm bg-white">
                                    <option value="duvida">❓ Dúvida</option>
                                    <option value="problema_tecnico">🔧 Problema Técnico</option>
                                    <option value="financeiro">💰 Financeiro</option>
                                    <option value="contrato">📄 Contrato</option>
                                    <option value="veiculo">🚗 Veículo</option>
                                    <option value="outro">📌 Outro</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Prioridade</label>
                                <select wire:model="prioridade" class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm bg-white">
                                    <option value="baixa">🟢 Baixa</option>
                                    <option value="media" selected>🟡 Média</option>
                                    <option value="alta">🟠 Alta</option>
                                    <option value="urgente">🔴 Urgente</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Descrição detalhada *</label>
                            <textarea wire:model="descricao" rows="5" placeholder="Explique com detalhes o que está acontecendo..."
                                class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500"></textarea>
                            <x-input-error :messages="$errors->get('descricao')" class="mt-1"/>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Anexos (opcional)</label>
                            <input wire:model="arquivos" type="file" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG, DOC — máx. 5MB por arquivo</p>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="submit"
                                class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Enviar Chamado
                            </button>
                            <button type="button" wire:click="cancelarNovoTicket"
                                class="px-5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

            {{-- ─── Conversa do Ticket Ativo ───────────────────────── --}}
            @elseif($activeTicket)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col" style="height: calc(100vh - 220px); min-height: 500px;">

                    {{-- Header do ticket --}}
                    <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono text-xs text-gray-400">{{ $activeTicket->numero }}</span>
                                @php
                                    $cores = ['aberto' => 'bg-red-100 text-red-700', 'em_atendimento' => 'bg-amber-100 text-amber-700', 'aguardando_cliente' => 'bg-blue-100 text-blue-700', 'resolvido' => 'bg-green-100 text-green-700', 'fechado' => 'bg-gray-100 text-gray-600'];
                                @endphp
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $cores[$activeTicket->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ \App\Models\Ticket::statusLabels()[$activeTicket->status] ?? $activeTicket->status }}
                                </span>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                    {{ ucfirst($activeTicket->prioridade) }}
                                </span>
                            </div>
                            <h2 class="text-base font-black text-gray-800">{{ $activeTicket->titulo }}</h2>
                        </div>
                    </div>

                    {{-- Mensagens --}}
                    <div class="flex-1 overflow-y-auto p-5 space-y-4" id="messages-area">
                        @foreach($activeTicket->messages as $msg)
                            <div class="flex {{ $msg->is_admin ? 'justify-start' : 'justify-end' }}">
                                <div class="max-w-[75%]">
                                    <div class="{{ $msg->is_admin
                                        ? 'bg-gray-100 text-gray-800 rounded-2xl rounded-tl-sm'
                                        : 'bg-orange-500 text-white rounded-2xl rounded-tr-sm' }} px-4 py-3 text-sm leading-relaxed">
                                        @if($msg->is_admin && $msg->is_internal)
                                            <div class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                                Nota Interna
                                            </div>
                                        @endif
                                        {{ $msg->mensagem }}
                                    </div>
                                    <div class="text-[11px] text-gray-400 mt-1 {{ $msg->is_admin ? 'text-left' : 'text-right' }}">
                                        {{ $msg->is_admin ? 'Suporte Elite' : 'Você' }} · {{ $msg->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Input de resposta --}}
                    @if(! in_array($activeTicket->status, ['resolvido', 'fechado']))
                        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                            <form wire:submit="enviarMensagem" class="flex gap-3 items-end">
                                <textarea wire:model="newMessage" rows="2" placeholder="Digite sua mensagem..."
                                    class="flex-1 rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 resize-none"></textarea>
                                <button type="submit"
                                    class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-5 py-3 rounded-xl transition flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50 text-center text-sm text-gray-400">
                            🔒 Este chamado foi {{ $activeTicket->status }}. Abra um novo para mais dúvidas.
                        </div>
                    @endif
                </div>

            {{-- ─── Estado vazio ────────────────────────────────────── --}}
            @else
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col items-center justify-center text-center py-20 px-6">
                    <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Selecione um chamado ou abra um novo</h3>
                    <p class="text-sm text-gray-400 mb-6 max-w-sm">
                        Nossa equipe responde em até <strong>2 horas</strong> para chamados urgentes e <strong>24 horas</strong> para os demais.
                    </p>
                    <button wire:click="abrirNovoTicket"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-3 rounded-xl transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Abrir Novo Chamado
                    </button>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Auto-scroll para o fim das mensagens --}}
<script>
    document.addEventListener('livewire:navigated', () => scrollToBottom());
    document.addEventListener('livewire:updated', () => scrollToBottom());
    function scrollToBottom() {
        const el = document.getElementById('messages-area');
        if (el) el.scrollTop = el.scrollHeight;
    }
    scrollToBottom();
</script>
