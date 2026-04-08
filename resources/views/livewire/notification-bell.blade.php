<div class="relative" x-data="{ open: @entangle('open') }">

    {{-- Sino / Botão --}}
    <button wire:click="toggle"
        class="relative p-2 rounded-xl text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition focus:outline-none">

        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        {{-- Badge de não lidas --}}
        @if($naoLidas > 0)
            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center leading-none {{ $naoLidas > 0 ? 'animate-pulse' : '' }}">
                {{ $naoLidas > 9 ? '9+' : $naoLidas }}
            </span>
        @endif
    </button>

    {{-- Dropdown de notificações --}}
    <div x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.outside="open = false"
        class="absolute right-0 top-full mt-2 w-[380px] bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden z-50"
        style="display: none;">

        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center gap-2">
                <span class="font-black text-gray-800">Notificações</span>
                @if($naoLidas > 0)
                    <span class="bg-red-100 text-red-600 text-xs font-black px-2 py-0.5 rounded-full">
                        {{ $naoLidas }} nova{{ $naoLidas > 1 ? 's' : '' }}
                    </span>
                @endif
            </div>
            @if($naoLidas > 0)
                <button wire:click="marcarTodasLidas" class="text-xs text-orange-500 hover:text-orange-700 font-semibold transition">
                    Marcar todas como lidas
                </button>
            @endif
        </div>

        {{-- Lista --}}
        <div class="max-h-[420px] overflow-y-auto divide-y divide-gray-50">
            @forelse($notificacoes as $notif)
                @php
                    $dados    = $notif->data;
                    $lida     = ! is_null($notif->read_at);
                    $url      = $dados['url'] ?? '#';
                    $icone    = $dados['icone'] ?? '🔔';
                    $titulo   = $dados['titulo'] ?? 'Notificação';
                    $mensagem = $dados['mensagem'] ?? '';
                @endphp

                <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition cursor-pointer {{ $lida ? 'opacity-60' : 'bg-orange-50/30' }}"
                    wire:click="marcarLida('{{ $notif->id }}')"
                    @if($url !== '#') onclick="window.location.href='{{ $url }}'" @endif>

                    {{-- Ícone --}}
                    <div class="flex-shrink-0 w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-base leading-none">
                        {{ $icone }}
                    </div>

                    {{-- Conteúdo --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 leading-tight {{ $lida ? '' : 'font-bold' }}">
                            {{ $titulo }}
                        </p>
                        @if($mensagem)
                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $mensagem }}</p>
                        @endif
                        <p class="text-[11px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>

                    {{-- Dot de não lida --}}
                    @if(! $lida)
                        <div class="flex-shrink-0 mt-1.5">
                            <div class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="px-4 py-10 text-center">
                    <div class="text-3xl mb-2">🔕</div>
                    <p class="text-sm text-gray-400">Nenhuma notificação ainda.</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($notificacoes->count() > 0)
            <div class="px-4 py-2.5 border-t border-gray-100 bg-gray-50 text-center">
                <a href="/notificacoes" class="text-xs text-orange-500 hover:text-orange-700 font-semibold transition">
                    Ver todas as notificações →
                </a>
            </div>
        @endif
    </div>

</div>
