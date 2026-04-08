<div class="w-full bg-[#f8fafc] min-h-screen">

    {{-- Header --}}
    <div class="w-full bg-gradient-to-r from-primary to-blue-900 relative overflow-hidden mb-6 shadow-sm">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex justify-between items-center">
            <div class="text-white z-10">
                <h1 class="text-3xl font-black tracking-tight italic uppercase leading-none">Notificações</h1>
                <p class="text-base font-medium mt-1 opacity-80">
                    @if($naoLidas > 0)
                        <span class="bg-red-500 text-white text-xs font-black px-2 py-0.5 rounded-full mr-2">{{ $naoLidas }}</span>
                    @endif
                    {{ $naoLidas > 0 ? "não lida{$naoLidas>1?'s':''}" : 'Tudo em dia!' }}
                </p>
            </div>
            @if($naoLidas > 0)
                <button wire:click="marcarTodasLidas"
                    class="flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white font-bold px-5 py-3 rounded-xl transition backdrop-blur-sm border border-white/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Marcar todas como lidas
                </button>
            @endif
        </div>
    </div>

    <div class="max-w-[900px] mx-auto px-4 sm:px-6 pb-16">

        {{-- Filtros --}}
        <div class="flex gap-2 mb-5">
            <button wire:click="$set('filtro', 'todas')"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $filtro === 'todas' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-orange-500' }}">
                Todas
            </button>
            <button wire:click="$set('filtro', 'nao_lidas')"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $filtro === 'nao_lidas' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-orange-500' }}">
                Não lidas
                @if($naoLidas > 0)
                    <span class="ml-1 bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full">{{ $naoLidas }}</span>
                @endif
            </button>
        </div>

        {{-- Lista --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden divide-y divide-gray-50">
            @forelse($notificacoes as $notif)
                @php
                    $dados    = $notif->data;
                    $lida     = ! is_null($notif->read_at);
                    $url      = $dados['url'] ?? '#';
                    $icone    = $dados['icone'] ?? '🔔';
                    $titulo   = $dados['titulo'] ?? 'Notificação';
                    $mensagem = $dados['mensagem'] ?? '';
                @endphp

                <div class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 transition group {{ $lida ? '' : 'bg-orange-50/40' }}">

                    {{-- Ícone --}}
                    <div class="flex-shrink-0 w-11 h-11 rounded-2xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-xl leading-none mt-0.5">
                        {{ $icone }}
                    </div>

                    {{-- Conteúdo --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <p class="text-sm font-{{ $lida ? 'semibold' : 'black' }} text-gray-800">
                                @if($url !== '#')
                                    <a href="{{ $url }}" class="hover:text-orange-600 transition">{{ $titulo }}</a>
                                @else
                                    {{ $titulo }}
                                @endif
                            </p>
                            @if(! $lida)
                                <span class="w-2 h-2 rounded-full bg-orange-500 flex-shrink-0"></span>
                            @endif
                        </div>
                        @if($mensagem)
                            <p class="text-sm text-gray-500">{{ $mensagem }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1.5">{{ $notif->created_at->format('d/m/Y H:i') }} · {{ $notif->created_at->diffForHumans() }}</p>
                    </div>

                    {{-- Ações --}}
                    <div class="flex items-center gap-2 flex-shrink-0 opacity-0 group-hover:opacity-100 transition">
                        @if(! $lida)
                            <button wire:click="marcarLida('{{ $notif->id }}')" title="Marcar como lida"
                                class="p-1.5 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        @endif
                        <button wire:click="excluir('{{ $notif->id }}')" title="Excluir"
                            class="p-1.5 rounded-lg bg-red-100 text-red-500 hover:bg-red-200 transition"
                            wire:confirm="Excluir esta notificação?">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center">
                    <div class="text-5xl mb-3">🔕</div>
                    <p class="text-gray-400 font-semibold">Nenhuma notificação {{ $filtro === 'nao_lidas' ? 'não lida' : '' }}.</p>
                </div>
            @endforelse
        </div>

        {{-- Paginação --}}
        @if($notificacoes->hasPages() ?? false)
            <div class="mt-6 flex justify-center">
                {{ $notificacoes->links() }}
            </div>
        @endif
    </div>

</div>
