<div class="w-full min-h-screen bg-[#f1f5f9]">

    {{-- ─── Hero ─────────────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-[#1a3a5c] via-[#1e4f8a] to-[#0f2d4e]">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div class="text-white">
                    <p class="text-orange-300 text-xs font-bold uppercase tracking-widest mb-1">Portal B2B</p>
                    <h1 class="text-3xl font-black tracking-tight">Financeiro</h1>
                    <p class="text-blue-200 text-sm mt-1">Extratos, faturas e cobranças</p>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <div class="bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl px-4 py-3 text-center min-w-[110px]">
                        <div class="text-white font-black text-lg">R$ {{ number_format($this->totalInvestido, 0, ',', '.') }}</div>
                        <div class="text-blue-200 text-xs mt-0.5">Total Investido</div>
                    </div>
                    <div class="bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl px-4 py-3 text-center min-w-[110px]">
                        <div class="text-white font-black text-lg">R$ {{ number_format($this->totalMes, 0, ',', '.') }}</div>
                        <div class="text-blue-200 text-xs mt-0.5">Este Mês</div>
                    </div>
                    @if($this->totalPendente > 0)
                        <div class="bg-red-500 bg-opacity-80 border border-red-400 rounded-xl px-4 py-3 text-center min-w-[110px]">
                            <div class="text-white font-black text-lg">R$ {{ number_format($this->totalPendente, 0, ',', '.') }}</div>
                            <div class="text-red-200 text-xs mt-0.5">⚠️ A Pagar</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="page-container py-6">

        {{-- ─── KPI Cards ──────────────────────────────────────────────── --}}
        @php
            $statusCard = [
                'confirmado'      => ['label' => 'Confirmados',  'emoji' => '✅', 'color' => 'border-emerald-300 bg-emerald-50', 'val' => 'success'],
                'faturado'        => ['label' => 'Faturados',    'emoji' => '🧾', 'color' => 'border-blue-300 bg-blue-50',    'val' => 'info'],
                'aguardando_pgto' => ['label' => 'Aguard. Pgt.', 'emoji' => '⏳', 'color' => 'border-orange-300 bg-orange-50','val' => 'warning'],
                'cancelado'       => ['label' => 'Cancelados',   'emoji' => '❌', 'color' => 'border-red-300 bg-red-50',      'val' => 'danger'],
            ];
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            @foreach($statusCard as $status => $cfg)
                @php
                    $dados = $this->countPorStatus[$status] ?? ['total' => 0, 'soma' => 0];
                @endphp
                <button wire:click="$set('filtro', '{{ $filtro === $status ? 'todos' : $status }}')"
                    class="elite-card border {{ $cfg['color'] }} {{ $filtro === $status ? 'ring-2 ring-offset-2 ring-blue-500' : '' }} p-5 text-left hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-2">
                        <p class="kpi-label">{{ $cfg['label'] }}</p>
                        <span class="text-2xl">{{ $cfg['emoji'] }}</span>
                    </div>
                    <p class="kpi-value">{{ $dados['total'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500 font-semibold mt-0.5">
                        R$ {{ number_format($dados['soma'] ?? 0, 0, ',', '.') }}
                    </p>
                </button>
            @endforeach
        </div>

        {{-- ─── Filtros + Busca ────────────────────────────────────────── --}}
        <div class="elite-card p-5 mb-5">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="busca"
                        type="text"
                        placeholder="Buscar por número, marca ou modelo..."
                        class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                </div>
                <div class="flex gap-2 flex-wrap">
                    @foreach(['todos' => 'Todos', 'confirmado' => '✅ Confirmados', 'faturado' => '🧾 Faturados', 'aguardando_pgto' => '⏳ Pendentes', 'cancelado' => '❌ Cancelados'] as $val => $label)
                        <button wire:click="$set('filtro', '{{ $val }}')"
                            class="px-4 py-2.5 text-sm font-bold rounded-xl transition
                                {{ $filtro === $val ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ─── Lista de Pedidos ────────────────────────────────────────── --}}
        @if($this->pedidos->isEmpty())
            <div class="elite-card py-24 text-center">
                <div class="text-6xl mb-4">💳</div>
                <p class="text-lg text-gray-400 font-semibold">Nenhum registro financeiro encontrado.</p>
                @if($busca || $filtro !== 'todos')
                    <button wire:click="$set('filtro','todos'); $set('busca','')"
                        class="btn-cta-md mt-6">
                        Limpar filtros
                    </button>
                @endif
            </div>
        @else
            <div class="space-y-4">
                @foreach($this->pedidos as $pedido)
                    @php
                        $fin = $pedido->financial;
                        $v   = $pedido->vehicle;
                        $badgeClass = match($pedido->status) {
                            'confirmado'      => 'bg-emerald-100 text-emerald-700',
                            'faturado'        => 'bg-blue-100 text-blue-700',
                            'pago'            => 'bg-emerald-100 text-emerald-700',
                            'cancelado'       => 'bg-red-100 text-red-700',
                            'pendente'        => 'bg-yellow-100 text-yellow-700',
                            'aguardando_pgto' => 'bg-orange-100 text-orange-700',
                            default           => 'bg-gray-100 text-gray-600',
                        };
                        $statusLabel = \App\Models\Order::statusLabels()[$pedido->status] ?? $pedido->status;
                        $open        = $pedidoOpen === $pedido->id;
                    @endphp

                    <div class="elite-card overflow-hidden transition">

                        {{-- Linha principal --}}
                        <button wire:click="abrirDetalhe({{ $pedido->id }})"
                            class="w-full flex items-center gap-4 px-6 py-5 hover:bg-gray-50 transition text-left">

                            {{-- Ícone do status --}}
                            <div class="w-14 h-14 rounded-2xl flex-shrink-0 flex items-center justify-center text-2xl
                                {{ $pedido->status === 'confirmado' ? 'bg-emerald-100' : ($pedido->status === 'aguardando_pgto' ? 'bg-orange-100' : 'bg-gray-100') }}">
                                {{ $pedido->status === 'faturado' ? '🧾' : ($pedido->status === 'aguardando_pgto' ? '⏳' : ($pedido->status === 'cancelado' ? '❌' : '🚗')) }}
                            </div>

                            {{-- Dados do pedido --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                    <span class="text-base font-black text-gray-900 font-mono">{{ $pedido->numero }}</span>
                                    <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                                    @if($fin?->boleto_url)
                                        <span class="badge bg-purple-100 text-purple-700">🔗 Boleto</span>
                                    @endif
                                </div>
                                <p class="text-base text-gray-700 font-semibold truncate">
                                    {{ $v ? "{$v->brand} {$v->model} {$v->model_year}" : 'Veículo não encontrado' }}
                                    @if($v?->plate) · <span class="font-mono text-gray-500">{{ $v->plate }}</span> @endif
                                </p>
                                <p class="text-sm text-gray-400 mt-0.5">
                                    {{ $pedido->created_at->format('d/m/Y') }}
                                    @if($pedido->paymentMethod) · {{ $pedido->paymentMethod->nome }} @endif
                                    @if($pedido->confirmado_em) · Confirmado em {{ $pedido->confirmado_em->format('d/m/Y') }} @endif
                                </p>
                            </div>

                            {{-- Valor --}}
                            <div class="text-right flex-shrink-0">
                                <p class="text-2xl font-black text-gray-900">R$ {{ number_format($pedido->valor_compra, 0, ',', '.') }}</p>
                                @if($pedido->valor_fipe)
                                    <p class="text-xs text-gray-400 mt-0.5">FIPE: R$ {{ number_format($pedido->valor_fipe, 0, ',', '.') }}</p>
                                    @php $desc = (($pedido->valor_fipe - $pedido->valor_compra) / $pedido->valor_fipe) * 100; @endphp
                                    @if($desc > 0)
                                        <p class="text-xs text-emerald-600 font-bold">↓ {{ number_format($desc, 1) }}% abaixo FIPE</p>
                                    @endif
                                @endif
                            </div>

                            {{-- Chevron --}}
                            <svg class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0 {{ $open ? 'rotate-180' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Painel expansível de cobrança --}}
                        @if($open)
                            <div class="border-t border-gray-100 bg-gray-50 px-5 py-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                                    {{-- Dados Financeiros --}}
                                    <div>
                                        <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-3">💳 Dados de Cobrança</h3>
                                        @if($fin && $fin->numero)
                                            <div class="space-y-2.5">
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">Fatura</span>
                                                    <span class="font-bold font-mono text-gray-800">{{ $fin->numero }}</span>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">Valor</span>
                                                    <span class="font-bold text-gray-800">R$ {{ number_format($fin->valor, 2, ',', '.') }}</span>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">Status</span>
                                                    @php
                                                        $finStatusBadge = match($fin->status) {
                                                            'pago'      => 'bg-emerald-100 text-emerald-700',
                                                            'vencido'   => 'bg-red-100 text-red-700',
                                                            'estornado' => 'bg-blue-100 text-blue-700',
                                                            'cancelado' => 'bg-gray-100 text-gray-600',
                                                            default     => 'bg-yellow-100 text-yellow-700',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $finStatusBadge }}">{{ \App\Models\Financial::statusLabels()[$fin->status] ?? $fin->status }}</span>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">Forma de Pagamento</span>
                                                    <span class="font-semibold text-gray-700">{{ \App\Models\Financial::formasPagamento()[$fin->forma_pagamento] ?? $fin->forma_pagamento ?? '—' }}</span>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">Vencimento</span>
                                                    <span class="font-semibold {{ $fin->esta_vencido ? 'text-red-600' : 'text-gray-700' }}">
                                                        {{ $fin->data_vencimento?->format('d/m/Y') ?? '—' }}
                                                        @if($fin->esta_vencido) <span class="text-xs text-red-500">⚠️ Vencido</span> @endif
                                                    </span>
                                                </div>
                                                @if($fin->data_pagamento)
                                                    <div class="flex justify-between text-sm">
                                                        <span class="text-gray-500">Data Pagamento</span>
                                                        <span class="font-semibold text-emerald-600">{{ $fin->data_pagamento->format('d/m/Y') }}</span>
                                                    </div>
                                                @endif
                                                @if($fin->observacoes)
                                                    <div class="mt-2 bg-blue-50 border border-blue-200 rounded-xl px-3 py-2 text-xs text-blue-700">
                                                        💬 {{ $fin->observacoes }}
                                                    </div>
                                                @endif
                                                @if($fin->digitable_line)
                                                    <div>
                                                        <p class="text-xs text-gray-500 mb-1">Linha Digitável</p>
                                                        <div class="flex items-center gap-2 bg-white rounded-xl border border-gray-200 px-3 py-2">
                                                            <code class="text-xs text-gray-700 flex-1 break-all">{{ $fin->digitable_line }}</code>
                                                            <button onclick="navigator.clipboard.writeText('{{ $fin->digitable_line }}')"
                                                                class="text-xs bg-blue-100 text-blue-700 font-bold px-2 py-1 rounded-lg hover:bg-blue-200 transition flex-shrink-0">
                                                                Copiar
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($fin)
                                            <div class="space-y-2.5">
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">Status do Pagamento</span>
                                                    <span class="font-bold text-gray-800">{{ ucfirst($fin->status ?? '-') }}</span>
                                                </div>
                                                @if($fin->digitable_line)
                                                    <div>
                                                        <p class="text-xs text-gray-500 mb-1">Linha Digitável</p>
                                                        <div class="flex items-center gap-2 bg-white rounded-xl border border-gray-200 px-3 py-2">
                                                            <code class="text-xs text-gray-700 flex-1 break-all">{{ $fin->digitable_line }}</code>
                                                            <button onclick="navigator.clipboard.writeText('{{ $fin->digitable_line }}')"
                                                                class="text-xs bg-blue-100 text-blue-700 font-bold px-2 py-1 rounded-lg hover:bg-blue-200 transition flex-shrink-0">
                                                                Copiar
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-400">Nenhum dado financeiro registrado.</p>
                                        @endif
                                    </div>

                                    {{-- Ações / Documentos --}}
                                    <div>
                                        <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-3">📄 Documentos e Ações</h3>
                                        <div class="space-y-2">
                                            @if($fin?->boleto_url)
                                                <a href="{{ $fin->boleto_url }}" target="_blank"
                                                    class="flex items-center gap-3 w-full bg-white border border-orange-200 hover:border-orange-400 rounded-xl px-4 py-3 transition group">
                                                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center text-lg flex-shrink-0">🎫</div>
                                                    <div class="flex-1">
                                                        <p class="text-sm font-bold text-gray-800 group-hover:text-orange-600 transition">Ver Boleto</p>
                                                        <p class="text-xs text-gray-400">Abre em nova aba</p>
                                                    </div>
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                    </svg>
                                                </a>
                                            @endif

                                            @if($fin?->invoice_url)
                                                <a href="{{ $fin->invoice_url }}" target="_blank"
                                                    class="flex items-center gap-3 w-full bg-white border border-blue-200 hover:border-blue-400 rounded-xl px-4 py-3 transition group">
                                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-lg flex-shrink-0">🧾</div>
                                                    <div class="flex-1">
                                                        <p class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition">Ver Nota Fiscal</p>
                                                        <p class="text-xs text-gray-400">Abre em nova aba</p>
                                                    </div>
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                    </svg>
                                                </a>
                                            @endif

                                            {{-- Suporte --}}
                                            <a href="{{ route('suporte') }}"
                                                class="flex items-center gap-3 w-full bg-white border border-gray-200 hover:border-gray-300 rounded-xl px-4 py-3 transition group">
                                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-lg flex-shrink-0">💬</div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-bold text-gray-800 group-hover:text-gray-600 transition">Abrir Chamado Financeiro</p>
                                                    <p class="text-xs text-gray-400">Dúvidas sobre este pedido</p>
                                                </div>
                                            </a>

                                            @if(! $fin?->boleto_url && ! $fin?->invoice_url)
                                                <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 text-xs text-yellow-700">
                                                    ⏳ Os documentos financeiros serão disponibilizados após a confirmação do pedido.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Resumo do veículo --}}
                                @if($v)
                                    <div class="mt-5 pt-4 border-t border-gray-200 grid grid-cols-2 md:grid-cols-4 gap-3">
                                        <div class="bg-white rounded-xl border border-gray-100 px-3 py-2 text-center">
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wide">Marca</p>
                                            <p class="text-sm font-black text-gray-800">{{ $v->brand }}</p>
                                        </div>
                                        <div class="bg-white rounded-xl border border-gray-100 px-3 py-2 text-center">
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wide">Modelo</p>
                                            <p class="text-sm font-black text-gray-800">{{ $v->model }}</p>
                                        </div>
                                        <div class="bg-white rounded-xl border border-gray-100 px-3 py-2 text-center">
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wide">Ano</p>
                                            <p class="text-sm font-black text-gray-800">{{ $v->model_year }}</p>
                                        </div>
                                        <div class="bg-white rounded-xl border border-gray-100 px-3 py-2 text-center">
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wide">Placa</p>
                                            <p class="text-sm font-black text-gray-800 font-mono">{{ $v->plate ?? '—' }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ─── Resumo Anual com Gráfico ───────────────────────────────── --}}
        @php
            $totalAnual    = \App\Models\Order::where('user_id', auth()->id())->whereYear('created_at', now()->year)->sum('valor_compra');
            $quantAnual    = \App\Models\Order::where('user_id', auth()->id())->whereYear('created_at', now()->year)->count();
            $mediaAnual    = $quantAnual > 0 ? $totalAnual / $quantAnual : 0;
        @endphp

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="kpi-card text-center">
                <p class="kpi-label mb-2">Total em {{ now()->year }}</p>
                <p class="kpi-value">R$ {{ number_format($totalAnual, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-400 mt-1">{{ $quantAnual }} pedido(s) no ano</p>
            </div>
            <div class="kpi-card text-center">
                <p class="kpi-label mb-2">Ticket Médio</p>
                <p class="kpi-value">R$ {{ number_format($mediaAnual, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-400 mt-1">por pedido</p>
            </div>
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-center text-white">
                <p class="text-sm text-orange-200 uppercase tracking-widest font-bold mb-2">Desconto vs FIPE</p>
                @php
                    $totalFipe  = \App\Models\Order::where('user_id', auth()->id())->whereNotNull('valor_fipe')->sum('valor_fipe');
                    $totalCompra= \App\Models\Order::where('user_id', auth()->id())->whereNotNull('valor_fipe')->sum('valor_compra');
                    $economia   = $totalFipe - $totalCompra;
                @endphp
                <p class="text-3xl sm:text-4xl font-black">R$ {{ number_format($economia, 0, ',', '.') }}</p>
                <p class="text-sm text-orange-200 mt-1">economizados vs tabela FIPE</p>
            </div>
        </div>

    </div>

    {{-- Bottom nav agora no layout compartilhado --}}
</div>
