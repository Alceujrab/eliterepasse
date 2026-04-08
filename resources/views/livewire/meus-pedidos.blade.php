<div class="w-full min-h-screen bg-[#f1f5f9]">

    {{-- ─── Hero ─────────────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-[#1e3a5f] via-[#1a4f8e] to-[#0f2d4e]">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6">
                <div class="text-white">
                    <p class="text-orange-300 text-xs font-bold uppercase tracking-widest mb-1">Portal do Lojista</p>
                    <h1 class="text-3xl font-black tracking-tight">Meus Pedidos</h1>
                    <p class="text-blue-200 text-sm mt-1">Compras, contratos e histórico</p>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <div class="bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl px-4 py-3 text-center">
                        <div class="text-white font-black text-xl">{{ $totalPedidos }}</div>
                        <div class="text-blue-200 text-xs mt-0.5">Total Pedidos</div>
                    </div>
                    <div class="bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl px-4 py-3 text-center">
                        <div class="text-white font-black text-xl">R$ {{ number_format($totalGasto, 0, ',', '.') }}</div>
                        <div class="text-blue-200 text-xs mt-0.5">Total Investido</div>
                    </div>
                    <div class="bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl px-4 py-3 text-center">
                        <div class="text-white font-black text-xl">{{ $pedidosMes }}</div>
                        <div class="text-blue-200 text-xs mt-0.5">Esse Mês</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-6">

        {{-- ─── KPI Cards ─────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

            {{-- Gasto no Mês --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Gasto no Mês</p>
                    <span class="text-xl">💳</span>
                </div>
                <p class="text-2xl font-black text-gray-900">R$ {{ number_format($gastoMes, 0, ',', '.') }}</p>
                @if($variacaoGasto !== null)
                    <p class="text-xs mt-1 font-semibold {{ $variacaoGasto >= 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $variacaoGasto >= 0 ? '▲' : '▼' }} {{ abs($variacaoGasto) }}% vs mês anterior
                    </p>
                @else
                    <p class="text-xs mt-1 text-gray-400">Primeiro mês</p>
                @endif
            </div>

            {{-- Chamados --}}
            <a href="{{ route('suporte') }}" class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition block">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Chamados Abertos</p>
                    <span class="text-xl">💬</span>
                </div>
                <p class="text-2xl font-black text-gray-900">{{ $ticketsAbertos }}</p>
                <p class="text-xs mt-1 font-semibold {{ $ticketsAbertos > 0 ? 'text-red-500' : 'text-green-600' }}">
                    {{ $ticketsAbertos > 0 ? 'Clique para ver' : 'Tudo ok ✅' }}
                </p>
            </a>

            {{-- Documentos --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Documentos</p>
                    <span class="text-xl">📄</span>
                </div>
                <p class="text-2xl font-black text-gray-900">{{ $documentosPendentes }}</p>
                <p class="text-xs mt-1 font-semibold {{ $documentosPendentes > 0 ? 'text-yellow-600' : 'text-green-600' }}">
                    {{ $documentosPendentes > 0 ? 'Pendentes de análise' : 'Todos verificados ✅' }}
                </p>
            </div>

            {{-- Contratos --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Contratos</p>
                    <span class="text-xl">✍️</span>
                </div>
                <p class="text-2xl font-black text-gray-900">{{ $contratosPendentes }}</p>
                <p class="text-xs mt-1 font-semibold {{ $contratosPendentes > 0 ? 'text-orange-500' : 'text-green-600' }}">
                    {{ $contratosPendentes > 0 ? 'Aguardando assinatura' : 'Nenhum pendente ✅' }}
                </p>
            </div>

        </div>

        {{-- ─── Conteúdo Principal + Sidebar ──────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Coluna principal: gráfico + pedidos (2/3) --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Gráfico + Filtros --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <h2 class="font-black text-gray-800">📈 Histórico de Compras (6 meses)</h2>
                        <div class="flex gap-1.5 flex-wrap">
                            <button wire:click="$set('abaPedidos', 'todos')"
                                class="px-3 py-1.5 text-xs font-bold rounded-lg {{ $abaPedidos === 'todos' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                                Todos
                            </button>
                            <button wire:click="$set('abaPedidos', 'pendente')"
                                class="px-3 py-1.5 text-xs font-bold rounded-lg {{ $abaPedidos === 'pendente' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                                Pendente
                            </button>
                            <button wire:click="$set('abaPedidos', 'confirmado')"
                                class="px-3 py-1.5 text-xs font-bold rounded-lg {{ $abaPedidos === 'confirmado' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                                Confirmado
                            </button>
                            <button wire:click="$set('abaPedidos', 'cancelado')"
                                class="px-3 py-1.5 text-xs font-bold rounded-lg {{ $abaPedidos === 'cancelado' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                                Cancelado
                            </button>
                        </div>
                    </div>
                    <div style="height:200px; position:relative;">
                        <canvas id="chartGastos"></canvas>
                    </div>
                </div>

                {{-- Lista de Pedidos --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h2 class="font-black text-gray-800">🛍️ Meus Pedidos</h2>
                        <span class="text-xs bg-gray-100 text-gray-500 px-3 py-1 rounded-full font-semibold">
                            {{ $pedidos->count() }} resultado{{ $pedidos->count() != 1 ? 's' : '' }}
                        </span>
                    </div>

                    {{-- Flash --}}
                    @if(session()->has('message'))
                        <div class="mx-5 mt-4 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 text-sm font-bold text-emerald-700">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if($pedidos->isEmpty())
                        <div class="py-16 text-center">
                            <div class="text-5xl mb-3">🛒</div>
                            <p class="text-gray-400 font-semibold">Nenhum pedido encontrado.</p>
                            <a href="{{ route('dashboard') }}" class="inline-block mt-4 px-5 py-2.5 bg-orange-500 text-white rounded-xl text-sm font-bold hover:bg-orange-600 transition">
                                Ver Vitrine
                            </a>
                        </div>
                    @else
                        <div class="divide-y divide-gray-50">
                            @foreach($pedidos as $pedido)
                                @php
                                    $badgeClass = match($pedido->status) {
                                        'confirmado'      => 'bg-emerald-100 text-emerald-700',
                                        'faturado'        => 'bg-blue-100 text-blue-700',
                                        'cancelado'       => 'bg-red-100 text-red-700',
                                        'pendente'        => 'bg-yellow-100 text-yellow-700',
                                        'aguardando_pgto' => 'bg-purple-100 text-purple-700',
                                        default           => 'bg-gray-100 text-gray-600',
                                    };
                                    $statusLabel = \App\Models\Order::statusLabels()[$pedido->status] ?? $pedido->status;
                                    $v = $pedido->vehicle;
                                    $isOpen = $pedidoOpenId === $pedido->id;
                                    $media = $v ? (is_string($v->media) ? json_decode($v->media, true) : $v->media) : [];
                                    $thumb = is_array($media) && count($media) > 0 ? $media[0] : null;
                                @endphp

                                {{-- Linha principal --}}
                                <button wire:click="abrirDetalhe({{ $pedido->id }})"
                                    class="w-full flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition text-left {{ $isOpen ? 'bg-orange-50/50' : '' }}">

                                    {{-- Thumb do veículo --}}
                                    <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                                        @if($thumb)
                                            <img src="{{ $thumb }}" alt="" class="w-full h-full object-cover"/>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-2xl">🚗</div>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-sm font-black text-gray-900 font-mono">{{ $pedido->numero }}</span>
                                            <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $badgeClass }}">{{ $statusLabel }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700 font-semibold truncate mt-0.5">
                                            {{ $v ? "{$v->brand} {$v->model} {$v->model_year}" : 'Veículo não encontrado' }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $pedido->created_at->format('d/m/Y') }} · {{ $pedido->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-lg font-black text-gray-900">R$ {{ number_format($pedido->valor_compra, 0, ',', '.') }}</p>
                                        @if($pedido->paymentMethod)
                                            <p class="text-xs text-gray-400">{{ $pedido->paymentMethod->nome }}</p>
                                        @endif
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform {{ $isOpen ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                {{-- Painel expandido --}}
                                @if($isOpen)
                                    <div class="bg-gray-50 border-t border-gray-100 px-5 py-5">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                                            {{-- Timeline do pedido --}}
                                            <div>
                                                <h4 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-3">📋 Status do Pedido</h4>
                                                @php
                                                    $etapas = [
                                                        ['label' => 'Proposta enviada', 'data' => $pedido->created_at, 'done' => true],
                                                        ['label' => 'Análise da equipe', 'data' => null, 'done' => in_array($pedido->status, ['confirmado','faturado','aguardando_pgto'])],
                                                        ['label' => 'Pedido confirmado', 'data' => $pedido->confirmado_em, 'done' => in_array($pedido->status, ['confirmado','faturado','aguardando_pgto'])],
                                                        ['label' => 'Faturamento', 'data' => null, 'done' => $pedido->status === 'faturado'],
                                                    ];
                                                @endphp
                                                <div class="space-y-3">
                                                    @foreach($etapas as $idx => $etapa)
                                                        <div class="flex items-start gap-3">
                                                            <div class="flex flex-col items-center">
                                                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black
                                                                    {{ $etapa['done'] ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                                                                    {{ $etapa['done'] ? '✓' : ($idx + 1) }}
                                                                </div>
                                                                @if($idx < count($etapas) - 1)
                                                                    <div class="w-0.5 h-5 {{ $etapa['done'] ? 'bg-emerald-300' : 'bg-gray-200' }}"></div>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <p class="text-sm font-semibold {{ $etapa['done'] ? 'text-gray-800' : 'text-gray-400' }}">{{ $etapa['label'] }}</p>
                                                                @if($etapa['data'])
                                                                    <p class="text-xs text-gray-400">{{ $etapa['data']->format('d/m/Y H:i') }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    {{-- Cancelado --}}
                                                    @if($pedido->status === 'cancelado')
                                                        <div class="flex items-center gap-3 mt-1">
                                                            <div class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center text-xs font-black">✕</div>
                                                            <p class="text-sm font-bold text-red-600">Pedido Cancelado</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Ações e links --}}
                                            <div>
                                                <h4 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-3">⚡ Ações</h4>
                                                <div class="space-y-2">
                                                    @if($v)
                                                        <a href="{{ route('vehicle.details', $v->id) }}" wire:navigate
                                                            class="flex items-center gap-3 bg-white border border-gray-200 hover:border-blue-300 rounded-xl px-4 py-3 transition group w-full">
                                                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-sm">🚗</div>
                                                            <div class="flex-1">
                                                                <p class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition">Ver Veículo</p>
                                                                <p class="text-xs text-gray-400">{{ $v->brand }} {{ $v->model }}</p>
                                                            </div>
                                                        </a>
                                                    @endif

                                                    <a href="{{ route('financeiro') }}" wire:navigate
                                                        class="flex items-center gap-3 bg-white border border-gray-200 hover:border-green-300 rounded-xl px-4 py-3 transition group w-full">
                                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-sm">💰</div>
                                                        <div class="flex-1">
                                                            <p class="text-sm font-bold text-gray-800 group-hover:text-green-600 transition">Ver Financeiro</p>
                                                            <p class="text-xs text-gray-400">Boletos e faturas</p>
                                                        </div>
                                                    </a>

                                                    <a href="{{ route('suporte') }}" wire:navigate
                                                        class="flex items-center gap-3 bg-white border border-gray-200 hover:border-purple-300 rounded-xl px-4 py-3 transition group w-full">
                                                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center text-sm">💬</div>
                                                        <div class="flex-1">
                                                            <p class="text-sm font-bold text-gray-800 group-hover:text-purple-600 transition">Abrir Chamado</p>
                                                            <p class="text-xs text-gray-400">Sobre este pedido</p>
                                                        </div>
                                                    </a>

                                                    @if($pedido->status === 'pendente')
                                                        <button wire:click="cancelarPedido({{ $pedido->id }})"
                                                            wire:confirm="Tem certeza que deseja cancelar este pedido?"
                                                            class="flex items-center gap-3 bg-white border border-red-200 hover:border-red-400 rounded-xl px-4 py-3 transition group w-full text-left">
                                                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-sm">❌</div>
                                                            <div class="flex-1">
                                                                <p class="text-sm font-bold text-red-600 group-hover:text-red-700 transition">Cancelar Pedido</p>
                                                                <p class="text-xs text-red-400">Apenas pedidos pendentes</p>
                                                            </div>
                                                        </button>
                                                    @endif
                                                </div>

                                                {{-- Info extras --}}
                                                @if($pedido->observacoes)
                                                    <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3">
                                                        <p class="text-xs font-bold text-yellow-700 mb-1">📝 Observações</p>
                                                        <p class="text-sm text-yellow-800">{{ $pedido->observacoes }}</p>
                                                    </div>
                                                @endif

                                                {{-- FIPE --}}
                                                @if($pedido->valor_fipe && $pedido->valor_fipe > $pedido->valor_compra)
                                                    @php $desconto = round(($pedido->valor_fipe - $pedido->valor_compra) / $pedido->valor_fipe * 100); @endphp
                                                    <div class="mt-3 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 flex justify-between items-center">
                                                        <div>
                                                            <p class="text-xs font-bold text-emerald-700">↓ {{ $desconto }}% abaixo da FIPE</p>
                                                            <p class="text-xs text-emerald-600">FIPE: R$ {{ number_format($pedido->valor_fipe, 0, ',', '.') }}</p>
                                                        </div>
                                                        <p class="text-sm font-black text-emerald-700">- R$ {{ number_format($pedido->valor_fipe - $pedido->valor_compra, 0, ',', '.') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>{{-- /col-span-2 --}}

            {{-- Sidebar (1/3) --}}
            <div class="space-y-5">

                {{-- Resumo Financeiro --}}
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-5 text-white shadow-lg">
                    <p class="text-xs font-bold uppercase tracking-widest opacity-80 mb-4">Resumo Financeiro</p>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="opacity-90">Total investido</span>
                            <span class="font-black">R$ {{ number_format($totalGasto, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-white border-opacity-20 pt-3 flex justify-between">
                            <span class="opacity-90">Este mês</span>
                            <span class="font-bold">R$ {{ number_format($gastoMes, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="opacity-90">Confirmados</span>
                            <span class="font-bold">{{ $pedidos->whereIn('status', ['confirmado','faturado'])->count() }}</span>
                        </div>
                        @if($variacaoGasto !== null)
                            <div class="border-t border-white border-opacity-20 pt-3 flex justify-between">
                                <span class="opacity-80 text-xs">vs mês anterior</span>
                                <span class="font-black {{ $variacaoGasto >= 0 ? '' : 'text-red-200' }}">
                                    {{ $variacaoGasto >= 0 ? '▲' : '▼' }} {{ abs($variacaoGasto) }}%
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Acesso Rápido --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <h3 class="font-black text-gray-800 mb-4 text-sm">⚡ Acesso Rápido</h3>
                    <div class="space-y-1">
                        @foreach([
                            ['route' => 'dashboard',    'icon' => '🔍', 'label' => 'Ver Vitrine',    'sub' => 'Novos veículos',     'bg' => 'bg-orange-100'],
                            ['route' => 'financeiro',   'icon' => '💰', 'label' => 'Financeiro',     'sub' => 'Pagamentos',         'bg' => 'bg-green-100'],
                            ['route' => 'suporte',      'icon' => '💬', 'label' => 'Suporte',        'sub' => $ticketsAbertos . ' aberto(s)', 'bg' => 'bg-blue-100'],
                            ['route' => 'favoritos',    'icon' => '❤️', 'label' => 'Favoritos',      'sub' => 'Salvos',             'bg' => 'bg-red-100'],
                            ['route' => 'notificacoes', 'icon' => '🔔', 'label' => 'Notificações',   'sub' => 'Ver todas',          'bg' => 'bg-purple-100'],
                        ] as $link)
                            <a href="{{ route($link['route']) }}" wire:navigate
                                class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-200 transition group">
                                <div class="w-8 h-8 rounded-lg {{ $link['bg'] }} flex items-center justify-center text-sm flex-shrink-0">{{ $link['icon'] }}</div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-orange-600 transition leading-tight">{{ $link['label'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $link['sub'] }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Status Geral --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <h3 class="font-black text-gray-800 mb-4 text-sm">📋 Status Geral</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Tickets abertos</span>
                            <span class="font-black text-sm {{ $ticketsAbertos > 0 ? 'text-red-500' : 'text-green-500' }}">
                                {{ $ticketsAbertos > 0 ? $ticketsAbertos : '✅' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Docs pendentes</span>
                            <span class="font-black text-sm {{ $documentosPendentes > 0 ? 'text-yellow-500' : 'text-green-500' }}">
                                {{ $documentosPendentes > 0 ? $documentosPendentes : '✅' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Contratos p/ assinar</span>
                            <span class="font-black text-sm {{ $contratosPendentes > 0 ? 'text-orange-500' : 'text-green-500' }}">
                                {{ $contratosPendentes > 0 ? $contratosPendentes : '✅' }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>{{-- /sidebar --}}

        </div>{{-- /grid --}}
    </div>{{-- /container --}}

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

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        const gastosData = @json($historicoGastos);

        function initChart() {
            const ctx = document.getElementById('chartGastos');
            if (!ctx) return;
            if (window._chartGastos) { window._chartGastos.destroy(); }
            if (!gastosData.length) {
                ctx.parentElement.innerHTML = '<p class="text-center text-gray-400 text-sm pt-16">Nenhuma compra registrada ainda.</p>';
                return;
            }
            window._chartGastos = new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: gastosData.map(d => d.label),
                    datasets: [{
                        data: gastosData.map(d => d.total),
                        backgroundColor: '#f97316',
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { ticks: { color: '#9ca3af', callback: v => 'R$' + (v/1000).toFixed(0) + 'k' }, grid: { color: '#f1f5f9' } },
                        x: { ticks: { color: '#9ca3af' }, grid: { display: false } }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initChart);
        document.addEventListener('livewire:navigated', initChart);
        document.addEventListener('livewire:updated', initChart);
    </script>
</div>

