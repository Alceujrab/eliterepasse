<x-filament-panels::page>

    {{-- ─── Hero ─────────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#128C7E] via-[#075E54] to-[#064940] p-5 mb-5 shadow-xl">
        <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-white opacity-5 blur-3xl -translate-y-1/2 translate-x-1/4"></div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4 text-white">
                <div class="w-12 h-12 rounded-2xl bg-white bg-opacity-20 flex items-center justify-center text-2xl">📩</div>
                <div>
                    <h1 class="text-3xl font-black tracking-tight">Caixa de Entrada — WhatsApp</h1>
                    <p class="text-green-200 text-base">Evolution GO · Mensagens recebidas via WhatsApp</p>
                </div>
            </div>
            <div class="flex gap-3 flex-wrap">
                @php
                    $total  = $this->tickets->count();
                    $aberto = Ticket::where('type','whatsapp')->where('status','aberto')->count();
                @endphp
                <div class="bg-white bg-opacity-15 backdrop-blur-sm px-4 py-2.5 rounded-xl text-white text-base font-semibold">
                    📋 {{ $total }} conversa(s)
                </div>
                <div class="bg-red-400 bg-opacity-80 px-4 py-2.5 rounded-xl text-white text-base font-bold">
                    🔴 {{ $aberto }} aberta(s)
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Layout: lista + conversa ───────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5" style="min-height: 70vh;">

        {{-- ─── Lista de Conversas (1/3) ──────────────────────────────── --}}
        <div class="elite-card overflow-hidden flex flex-col">

            {{-- Filtros --}}
            <div class="px-4 pt-4 pb-3 border-b border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-2 gap-1.5">
                    @foreach(['aberto' => '🔴 Abertos', 'aguardando_cliente' => '🔵 Aguardando', 'resolvido' => '🟢 Resolvidos', 'todos' => '📋 Todos'] as $val => $label)
                        <button wire:click="$set('filtroStatus', '{{ $val }}')"
                            class="text-sm font-bold py-2 rounded-lg transition
                                {{ $filtroStatus === $val
                                    ? 'bg-[#128C7E] text-white'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Lista --}}
            <div class="flex-1 overflow-y-auto divide-y divide-gray-50 dark:divide-gray-700" style="max-height: 65vh;">
                @forelse($this->tickets as $t)
                    @php
                        $lastMsg    = $t->messages->first();
                        $isSelected = $ticketAbertoId === $t->id;
                        $statusBg   = match($t->status) {
                            'aberto'             => 'bg-red-100 text-red-700',
                            'aguardando_cliente' => 'bg-blue-100 text-blue-700',
                            'resolvido'          => 'bg-green-100 text-green-700',
                            'fechado'            => 'bg-gray-100 text-gray-500',
                            default              => 'bg-yellow-100 text-yellow-700',
                        };
                    @endphp
                    <button wire:click="abrirTicket({{ $t->id }})"
                        class="w-full text-left px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition
                            {{ $isSelected ? 'bg-green-50 dark:bg-green-900/20 border-l-4 border-[#128C7E]' : '' }}">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-[#128C7E] bg-opacity-10 flex items-center justify-center text-sm flex-shrink-0">
                                    {{ strtoupper(substr($t->user?->razao_social ?? $t->user?->name ?? '?', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-gray-900 dark:text-white truncate">
                                        {{ $t->user?->razao_social ?? $t->user?->name ?? 'Cliente WhatsApp' }}
                                    </p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $t->numero }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full flex-shrink-0 {{ $statusBg }}">
                                {{ Ticket::statusLabels()[$t->status] ?? $t->status }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-1 pl-11">
                            {{ $lastMsg?->mensagem ?? mb_strimwidth($t->titulo, 0, 60, '...') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1 pl-11">{{ $t->created_at->diffForHumans() }}</p>
                    </button>
                @empty
                    <div class="py-12 text-center text-gray-400">
                        <div class="text-5xl mb-2">📭</div>
                        <p class="text-base font-semibold">Nenhuma conversa</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ─── Área da Conversa (2/3) ─────────────────────────────────── --}}
        <div class="lg:col-span-2 elite-card overflow-hidden flex flex-col">

            @if($this->ticketAberto)
                @php $ticket = $this->ticketAberto; @endphp

                {{-- Header da conversa --}}
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-green-50 to-white dark:from-gray-700 dark:to-gray-800">
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#128C7E] text-white flex items-center justify-center font-black text-lg">
                                {{ strtoupper(substr($ticket->user?->razao_social ?? $ticket->user?->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-black text-gray-900 dark:text-white">
                                    {{ $ticket->user?->razao_social ?? $ticket->user?->name ?? 'Cliente WhatsApp' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    📞 {{ $ticket->user?->phone ?? 'Telefone não cadastrado' }}
                                    · {{ $ticket->numero }}
                                </p>
                            </div>
                        </div>

                        {{-- Ações do ticket --}}
                        <div class="flex gap-2 flex-wrap">
                            @if(in_array($ticket->status, ['aberto', 'aguardando_cliente', 'em_atendimento']))
                                <button wire:click="resolverTicket({{ $ticket->id }})"
                                    class="px-3 py-1.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-lg text-xs font-bold hover:bg-green-200 transition">
                                    ✅ Resolver
                                </button>
                                <button wire:click="fecharTicket({{ $ticket->id }})"
                                    class="px-3 py-1.5 bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg text-xs font-bold hover:bg-gray-200 transition">
                                    🔒 Fechar
                                </button>
                            @elseif($ticket->status === 'resolvido')
                                <button wire:click="reabrirTicket({{ $ticket->id }})"
                                    class="px-3 py-1.5 bg-orange-100 text-orange-700 rounded-lg text-xs font-bold hover:bg-orange-200 transition">
                                    🔄 Reabrir
                                </button>
                            @endif

                            @if($ticket->user)
                                <a href="{{ $ticket->user->phone ? 'https://wa.me/' . preg_replace('/\D/', '', '55' . $ticket->user->phone) : '#' }}"
                                    target="_blank"
                                    class="px-3 py-1.5 bg-[#25D366] text-white rounded-lg text-xs font-bold hover:opacity-90 transition flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    Abrir WA
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Thread de mensagens --}}
                <div class="flex-1 overflow-y-auto p-5 space-y-3" style="max-height: 45vh;" id="thread-scroll">
                    @foreach($ticket->messages as $msg)
                        @php
                            $isAdmin = $msg->is_admin;
                            $isInternal = $msg->is_internal;
                        @endphp
                        <div class="flex {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] {{ $isAdmin ? 'order-2' : '' }}">
                                <div class="px-4 py-3 rounded-2xl text-base leading-relaxed
                                    {{ $isInternal
                                        ? 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200'
                                        : ($isAdmin
                                            ? 'bg-[#128C7E] text-white'
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200') }}"
                                    style="white-space: pre-line;">{{ $isInternal ? '🔒 ' : '' }}{{ $msg->mensagem }}</div>
                                <div class="flex items-center gap-1.5 mt-1 {{ $isAdmin ? 'justify-end' : '' }}">
                                    <p class="text-xs text-gray-400">
                                        {{ $isAdmin ? ($msg->user?->name ?? 'Admin') : ($ticket->user?->razao_social ?? 'Cliente') }}
                                        · {{ $msg->created_at->format('d/m H:i') }}
                                        @if($isInternal) · 🔒 Nota interna @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($ticket->messages->isEmpty())
                        <div class="py-8 text-center text-gray-400">
                            <div class="text-3xl mb-2">💬</div>
                            <p class="text-sm">Nenhuma mensagem ainda.</p>
                        </div>
                    @endif
                </div>

                {{-- Caixa de resposta --}}
                @if(! in_array($ticket->status, ['fechado']))
                    <div class="border-t border-gray-100 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-700/30">
                        <div class="flex items-center gap-3 mb-2">
                            <label class="flex items-center gap-2 text-xs font-semibold text-gray-600 dark:text-gray-400 cursor-pointer select-none">
                                <input wire:model="isInternal" type="checkbox" class="w-3.5 h-3.5 accent-yellow-500 rounded"/>
                                🔒 Nota interna (não enviará WhatsApp)
                            </label>
                        </div>
                        <div class="flex gap-3">
                            <textarea wire:model="respostaTexto"
                                placeholder="{{ $isInternal ? 'Escreva uma nota interna...' : 'Responder via WhatsApp...' }}"
                                rows="3"
                                class="flex-1 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-base focus:ring-2 focus:ring-[#128C7E] focus:border-transparent resize-none"></textarea>
                            <button wire:click="responder"
                                class="self-end px-5 py-3 rounded-xl font-black text-base transition shadow-lg
                                    {{ $isInternal
                                        ? 'bg-yellow-400 hover:bg-yellow-500 text-yellow-900'
                                        : 'bg-[#128C7E] hover:bg-[#0a7a6e] text-white shadow-green-700/20' }}">
                                {{ $isInternal ? '💾 Salvar' : '📤 Enviar' }}
                            </button>
                        </div>
                        @error('respostaTexto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                @else
                    <div class="border-t border-gray-100 dark:border-gray-700 p-4 text-center text-sm text-gray-400">
                        🔒 Ticket fechado. <button wire:click="reabrirTicket({{ $ticket->id }})" class="text-orange-500 font-bold hover:underline">Reabrir para responder.</button>
                    </div>
                @endif

            @else
                {{-- Placeholder vazio --}}
                <div class="flex-1 flex items-center justify-center flex-col gap-4 text-gray-300 dark:text-gray-600">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <div class="text-center">
                        <p class="font-bold text-xl text-gray-400 dark:text-gray-500">Selecione uma conversa</p>
                        <p class="text-base text-gray-400 dark:text-gray-600">As mensagens recebidas via WhatsApp aparecerão à esquerda</p>
                    </div>

                    {{-- Info do webhook --}}
                    <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-4 text-left max-w-md w-full mx-6">
                        <p class="text-xs font-black text-blue-700 dark:text-blue-300 mb-2">⚙️ URL do Webhook (Evolution GO)</p>
                        <div class="flex items-center gap-2 bg-white dark:bg-gray-800 rounded-xl px-3 py-2">
                            <code class="text-xs text-[#128C7E] dark:text-green-400 break-all flex-1">{{ url('/webhook/evolution') }}</code>
                            <button onclick="navigator.clipboard.writeText('{{ url('/webhook/evolution') }}')"
                                class="text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 px-2 py-1 rounded-lg font-bold text-gray-600 dark:text-gray-300 flex-shrink-0">
                                Copiar
                            </button>
                        </div>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                            Configure esta URL no painel Evolution GO em <strong>Instâncias → Webhooks → Messages</strong>, ativando o evento <strong>messages.upsert</strong>.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Auto-scroll ao abrir --}}
    <script>
        document.addEventListener('livewire:updated', function() {
            const el = document.getElementById('thread-scroll');
            if (el) el.scrollTop = el.scrollHeight;
        });
    </script>

</x-filament-panels::page>
