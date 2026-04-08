<div class="min-h-screen bg-[#f1f5f9]">

    {{-- ─── Header ─────────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-[#1a3a5c] to-[#1e4f8a] shadow-sm">
        <div class="max-w-4xl mx-auto px-6 py-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div class="text-white">
                <p class="text-orange-300 text-xs font-bold uppercase tracking-widest mb-1">Portal do Lojista</p>
                <h1 class="text-2xl font-black tracking-tight">🔔 Notificações</h1>
                <p class="text-blue-200 text-sm mt-1">
                    @if($naoLidas > 0)
                        <span class="bg-red-500 text-white text-xs font-black px-2 py-0.5 rounded-full mr-1">{{ $naoLidas }}</span>
                        não lida{{ $naoLidas > 1 ? 's' : '' }}
                    @else
                        Tudo em dia! ✅
                    @endif
                </p>
            </div>
            @if($naoLidas > 0)
                <button wire:click="marcarTodasLidas"
                    class="flex items-center gap-2 bg-white bg-opacity-10 border border-white border-opacity-20 text-white font-bold px-5 py-3 rounded-xl transition hover:bg-opacity-20 text-sm backdrop-blur-sm">
                    ✓ Marcar todas como lidas
                </button>
            @endif
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-6 py-6">

        {{-- Filtros --}}
        <div class="flex gap-2 mb-5">
            <button wire:click="$set('filtro', 'todas')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $filtro === 'todas' ? 'bg-[#1a3a5c] text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-300' }}">
                Todas
            </button>
            <button wire:click="$set('filtro', 'nao_lidas')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $filtro === 'nao_lidas' ? 'bg-[#1a3a5c] text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-300' }}">
                Não lidas
                @if($naoLidas > 0)
                    <span class="ml-1 bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full">{{ $naoLidas }}</span>
                @endif
            </button>
        </div>

        {{-- Lista --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            @forelse($notificacoes as $notif)
                @php
                    $dados    = $notif->data;
                    $lida     = ! is_null($notif->read_at);
                    $url      = $dados['url'] ?? '#';
                    $icone    = $dados['icone'] ?? '🔔';
                    $titulo   = $dados['titulo'] ?? 'Notificação';
                    $mensagem = $dados['mensagem'] ?? '';
                @endphp

                <div class="flex items-start gap-4 px-5 py-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition group {{ $lida ? '' : 'bg-orange-50/30' }}">
                    {{-- Ícone --}}
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-{{ $lida ? 'gray-100' : 'orange-100' }} flex items-center justify-center text-lg mt-0.5">
                        {{ $icone }}
                    </div>

                    {{-- Conteúdo --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <p class="text-sm {{ $lida ? 'text-gray-700 font-semibold' : 'text-gray-900 font-black' }}">
                                @if($url !== '#')
                                    <a href="{{ $url }}" wire:navigate wire:click="marcarLida('{{ $notif->id }}')" class="hover:text-orange-600 transition">{{ $titulo }}</a>
                                @else
                                    {{ $titulo }}
                                @endif
                            </p>
                            @if(! $lida)
                                <span class="w-2 h-2 rounded-full bg-orange-500 flex-shrink-0 animate-pulse"></span>
                            @endif
                        </div>
                        @if($mensagem)
                            <p class="text-sm text-gray-500 leading-relaxed">{{ $mensagem }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1.5">{{ $notif->created_at->format('d/m/Y H:i') }} · {{ $notif->created_at->diffForHumans() }}</p>
                    </div>

                    {{-- Ações --}}
                    <div class="flex items-center gap-1.5 flex-shrink-0 opacity-0 group-hover:opacity-100 transition">
                        @if(! $lida)
                            <button wire:click="marcarLida('{{ $notif->id }}')" title="Marcar como lida"
                                class="p-2 rounded-lg bg-emerald-50 text-emerald-500 hover:bg-emerald-100 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endif
                        <button wire:click="excluir('{{ $notif->id }}')" wire:confirm="Excluir esta notificação?" title="Excluir"
                            class="p-2 rounded-lg bg-red-50 text-red-400 hover:bg-red-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center">
                    <div class="text-4xl mb-3">🔕</div>
                    <p class="text-gray-400 font-semibold">Nenhuma notificação {{ $filtro === 'nao_lidas' ? 'não lida' : '' }}.</p>
                    <a href="{{ route('dashboard') }}" wire:navigate class="inline-block mt-4 text-sm font-bold text-orange-500 hover:text-orange-600 transition">← Voltar à Vitrine</a>
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

    {{-- Bottom Nav Mobile --}}
    <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 shadow-xl z-50">
        <div class="flex">
            @foreach([['dashboard','🏠','Vitrine'],['meus-pedidos','📋','Pedidos'],['financeiro','💳','Financeiro'],['suporte','💬','Suporte'],['favoritos','❤️','Favoritos']] as [$rt,$ico,$lbl])
                <a href="{{ route($rt) }}" wire:navigate
                    class="flex-1 flex flex-col items-center justify-center py-2.5 transition
                        {{ request()->routeIs($rt) ? 'text-[#1a3a5c]' : 'text-gray-400 hover:text-gray-600' }}">
                    <span class="text-lg leading-none">{{ $ico }}</span>
                    <span class="text-[9px] font-bold mt-0.5">{{ $lbl }}</span>
                </a>
            @endforeach
        </div>
    </nav>
    <div class="lg:hidden h-16"></div>
</div>
