<div class="w-full bg-[#f8fafc] min-h-screen">

    {{-- ─── Hero Header ───────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-[#1e3a5f] via-[#1a4f8e] to-[#0f2d4e] shadow-xl">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 rounded-full bg-white blur-3xl -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-orange-400 blur-3xl translate-y-1/2 -translate-x-1/2"></div>
        </div>

        <div class="relative max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div class="text-white">
                    <p class="text-orange-300 text-xs font-bold uppercase tracking-widest mb-1">Portal do Lojista</p>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight">
                        Meus Pedidos
                    </h1>
                    <p class="text-blue-200 text-sm mt-2 font-medium">
                        Acompanhe suas compras, contratos e documentos
                    </p>
                </div>

                {{-- Quick stats no header --}}
                <div class="flex gap-3 flex-wrap">
                    @php
                        $quickStats = [
                            ['label' => 'Total de Pedidos', 'value' => $totalPedidos, 'icon' => '🛒'],
                            ['label' => 'Total Investido', 'value' => 'R$ ' . number_format($totalGasto, 0, ',', '.'), 'icon' => '💰'],
                            ['label' => 'Esse Mês', 'value' => $pedidosMes . ' pedido' . ($pedidosMes != 1 ? 's' : ''), 'icon' => '📅'],
                        ];
                    @endphp
                    @foreach($quickStats as $qs)
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-3 text-center min-w-[120px]">
                            <div class="text-xl mb-0.5">{{ $qs['icon'] }}</div>
                            <div class="text-white font-black text-lg leading-none">{{ $qs['value'] }}</div>
                            <div class="text-blue-200 text-[11px] mt-0.5">{{ $qs['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">

            {{-- ─── COLUNA PRINCIPAL (3/4) ──────────────────────────── --}}
            <div class="xl:col-span-3 space-y-6">

                {{-- KPI Cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @php
                        $kpis = [
                            [
                                'label'    => 'Gasto no Mês',
                                'value'    => 'R$ ' . number_format($gastoMes, 0, ',', '.'),
                                'sub'      => $variacaoGasto !== null
                                    ? ($variacaoGasto >= 0 ? "▲ {$variacaoGasto}% vs mês anterior" : "▼ " . abs($variacaoGasto) . "% vs mês anterior")
                                    : 'Primeiro mês de compra',
                                'cor_sub'  => $variacaoGasto >= 0 ? 'text-green-600' : 'text-red-500',
                                'icon'     => '💳',
                                'gradient' => 'from-orange-500 to-orange-600',
                            ],
                            [
                                'label'    => 'Chamados Abertos',
                                'value'    => $ticketsAbertos,
                                'sub'      => $ticketsAbertos > 0 ? 'Clique para ver' : 'Tudo ok ✅',
                                'cor_sub'  => $ticketsAbertos > 0 ? 'text-red-500' : 'text-green-600',
                                'icon'     => '💬',
                                'gradient' => 'from-blue-500 to-blue-600',
                                'url'      => '/suporte',
                            ],
                            [
                                'label'    => 'Documentos',
                                'value'    => $documentosPendentes,
                                'sub'      => $documentosPendentes > 0 ? 'Pendentes de análise' : 'Todos verificados ✅',
                                'cor_sub'  => $documentosPendentes > 0 ? 'text-yellow-600' : 'text-green-600',
                                'icon'     => '📄',
                                'gradient' => 'from-indigo-500 to-indigo-600',
                            ],
                            [
                                'label'    => 'Contratos',
                                'value'    => $contratosPendentes,
                                'sub'      => $contratosPendentes > 0 ? 'Aguardando assinatura' : 'Nenhum pendente ✅',
                                'cor_sub'  => $contratosPendentes > 0 ? 'text-orange-500' : 'text-green-600',
                                'icon'     => '✍️',
                                'gradient' => 'from-purple-500 to-purple-600',
                            ],
                        ];
                    @endphp
                    @foreach($kpis as $kpi)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition group
                            {{ isset($kpi['url']) ? 'cursor-pointer' : '' }}"
                            @if(isset($kpi['url'])) onclick="window.location='{{ $kpi['url'] }}'" @endif>
                            <div class="h-1 bg-gradient-to-r {{ $kpi['gradient'] }}"></div>
                            <div class="p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">{{ $kpi['label'] }}</p>
                                        <p class="text-2xl font-black text-gray-900 leading-none">{{ $kpi['value'] }}</p>
                                        <p class="text-xs {{ $kpi['cor_sub'] }} mt-1.5 font-semibold">{{ $kpi['sub'] }}</p>
                                    </div>
                                    <div class="text-2xl opacity-70">{{ $kpi['icon'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Gráfico de gastos + filtros --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
                        <h2 class="text-base font-black text-gray-800">📈 Histórico de Compras (6 meses)</h2>
                        <div class="flex gap-1.5">
                            @foreach(['todos' => 'Todos', 'pendente' => '⏳ Pendente', 'confirmado' => '✅ Confirmado', 'cancelado' => '❌ Cancelado'] as $val => $label)
                                <button wire:click="$set('abaPedidos', '{{ $val }}')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition
                                        {{ $abaPedidos === $val
                                            ? 'bg-orange-500 text-white shadow-sm'
                                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div style="position:relative; height:200px;">
                        <canvas id="chartGastos"></canvas>
                    </div>
                </div>

                {{-- Timeline de Pedidos --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-base font-black text-gray-800">🛍️ Meus Pedidos</h2>
                        <span class="text-xs bg-gray-100 text-gray-500 px-3 py-1 rounded-full font-semibold">
                            {{ $pedidos->count() }} resultado{{ $pedidos->count() != 1 ? 's' : '' }}
                        </span>
                    </div>

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
                                    $statusBg = match($pedido->status) {
                                        'confirmado'  => 'bg-emerald-100 text-emerald-700',
                                        'faturado'    => 'bg-blue-100 text-blue-700',
                                        'cancelado'   => 'bg-red-100 text-red-700',
                                        'pendente'    => 'bg-yellow-100 text-yellow-700',
                                        default       => 'bg-gray-100 text-gray-600',
                                    };
                                    $statusLabel = \App\Models\Order::statusLabels()[$pedido->status] ?? $pedido->status;
                                    $veiculo = $pedido->vehicle;
                                @endphp
                                <div class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 transition">

                                    {{-- Ícone do veículo --}}
                                    <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-2xl">
                                        🚗
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center flex-wrap gap-2 mb-0.5">
                                            <span class="text-sm font-black text-gray-900 font-mono">{{ $pedido->numero }}</span>
                                            <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $statusBg }}">{{ $statusLabel }}</span>
                                        </div>
                                        @if($veiculo)
                                            <p class="text-sm font-semibold text-gray-700 truncate">
                                                {{ $veiculo->brand }} {{ $veiculo->model }} {{ $veiculo->model_year }}
                                            </p>
                                            <p class="text-xs text-gray-400">Placa: <span class="font-mono font-bold">{{ $veiculo->plate }}</span></p>
                                        @else
                                            <p class="text-sm text-gray-400">Veículo não encontrado</p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-1">{{ $pedido->created_at->format('d/m/Y H:i') }} · {{ $pedido->created_at->diffForHumans() }}</p>
                                    </div>

                                    {{-- Valor --}}
                                    <div class="flex-shrink-0 text-right">
                                        <p class="text-lg font-black text-gray-900">
                                            R$ {{ number_format($pedido->valor_compra, 0, ',', '.') }}
                                        </p>
                                        @if($pedido->paymentMethod)
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $pedido->paymentMethod->nome }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>{{-- /principal --}}

            {{-- ─── SIDEBAR DIREITA (1/4) ──────────────────────────── --}}
            <div class="space-y-5">

                {{-- Ações Rápidas --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-sm font-black text-gray-800 mb-4">⚡ Acesso Rápido</h3>
                    @php
                        $acoes = [
                            ['icon' => '🔍', 'label' => 'Ver Vitrine', 'sub' => 'Novos veículos', 'url' => route('dashboard'), 'cor' => 'orange'],
                            ['icon' => '💬', 'label' => 'Suporte', 'sub' => $ticketsAbertos . ' aberto(s)', 'url' => route('suporte'), 'cor' => 'blue'],
                            ['icon' => '🔔', 'label' => 'Notificações', 'sub' => 'Ver todas', 'url' => route('notificacoes'), 'cor' => 'purple'],
                            ['icon' => '❤️', 'label' => 'Favoritos', 'sub' => 'Salvos', 'url' => route('favoritos'), 'cor' => 'red'],
                            ['icon' => '💰', 'label' => 'Financeiro', 'sub' => 'Pagamentos', 'url' => route('financeiro'), 'cor' => 'green'],
                        ];
                    @endphp
                    <div class="space-y-2">
                        @foreach($acoes as $acao)
                            <a href="{{ $acao['url'] }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-200 transition group">
                                <div class="w-9 h-9 rounded-xl bg-{{ $acao['cor'] }}-100 flex items-center justify-center text-base flex-shrink-0">
                                    {{ $acao['icon'] }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-orange-600 transition leading-tight">{{ $acao['label'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $acao['sub'] }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-300 group-hover:text-orange-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Resumo financeiro rápido --}}
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg shadow-orange-500/20 p-5 text-white">
                    <h3 class="text-xs font-bold uppercase tracking-widest opacity-80 mb-4">Resumo Financeiro</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm opacity-90">Total investido</span>
                            <span class="font-black text-lg">R$ {{ number_format($totalGasto, 0, ',', '.') }}</span>
                        </div>
                        <div class="h-px bg-white/20"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm opacity-90">Este mês</span>
                            <span class="font-bold">R$ {{ number_format($gastoMes, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm opacity-90">Pedidos confirmados</span>
                            <span class="font-bold">{{ $pedidos->whereIn('status', ['confirmado','faturado'])->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm opacity-90">Pedidos no mês</span>
                            <span class="font-bold">{{ $pedidosMes }}</span>
                        </div>
                        @if($variacaoGasto !== null)
                            <div class="h-px bg-white/20"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs opacity-80">vs mês anterior</span>
                                <span class="font-black text-sm {{ $variacaoGasto >= 0 ? '' : 'text-red-200' }}">
                                    {{ $variacaoGasto >= 0 ? '▲' : '▼' }} {{ abs($variacaoGasto) }}%
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Status dos módulos --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-sm font-black text-gray-800 mb-4">📋 Status Geral</h3>
                    <div class="space-y-2.5">
                        @php
                            $statusItems = [
                                ['label' => 'Tickets abertos', 'value' => $ticketsAbertos, 'danger' => $ticketsAbertos > 0],
                                ['label' => 'Docs pendentes', 'value' => $documentosPendentes, 'danger' => $documentosPendentes > 0],
                                ['label' => 'Contratos p/ assinar', 'value' => $contratosPendentes, 'danger' => $contratosPendentes > 0],
                            ];
                        @endphp
                        @foreach($statusItems as $item)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ $item['label'] }}</span>
                                <span class="text-sm font-black {{ $item['danger'] ? 'text-red-500' : 'text-green-500' }}">
                                    {{ $item['value'] > 0 ? $item['value'] : '✅' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>{{-- /sidebar --}}

        </div>{{-- /grid --}}
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        const dadosGastos = @json($historicoGastos);

        function initGastosChart() {
            const ctx = document.getElementById('chartGastos')?.getContext('2d');
            if (!ctx || dadosGastos.length === 0) return;

            if (window._chartGastos) window._chartGastos.destroy();

            window._chartGastos = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dadosGastos.map(d => d.label),
                    datasets: [{
                        label: 'Gasto (R$)',
                        data: dadosGastos.map(d => d.total),
                        backgroundColor: 'rgba(249,115,22,0.8)',
                        borderColor: '#f97316',
                        borderWidth: 0,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            ticks: {
                                color: '#9ca3af',
                                callback: v => 'R$ ' + (v / 1000).toFixed(0) + 'k'
                            },
                            grid: { color: 'rgba(0,0,0,0.04)' }
                        },
                        x: {
                            ticks: { color: '#9ca3af' },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initGastosChart);
        document.addEventListener('livewire:navigated', initGastosChart);
        document.addEventListener('livewire:updated', initGastosChart);
    </script>

</div>
