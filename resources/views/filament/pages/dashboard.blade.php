<x-filament-panels::page>

    @php
        $kpis    = $this->kpis;
        $alertas = $this->alertas;
        $grafico = $this->graficoMensal;
    @endphp

    {{-- ─── Hero ──────────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1a3a5c] via-[#1e4f8a] to-[#0f2d4e] p-6 mb-6 shadow-xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-blue-300 opacity-5 blur-3xl rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-orange-300 opacity-5 blur-3xl rounded-full translate-y-1/3 -translate-x-1/4"></div>

        <div class="relative flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div class="text-white">
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">{{ $this->saudacao }}</h1>
                <p class="text-blue-200 text-base mt-1">
                    📅 {{ now()->translatedFormat('l, d \\d\\e F \\d\\e Y') }}
                    · {{ $kpis['totalVeiculos'] }} veículos no sistema
                </p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="/admin/vehicles/create"
                    class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-black px-5 py-3 rounded-xl shadow-lg transition text-base">
                    🚗 Novo Veículo
                </a>
                <a href="/admin/gestao-financeira"
                    class="flex items-center gap-2 bg-white bg-opacity-15 border border-white border-opacity-20 text-white font-bold px-5 py-3 rounded-xl hover:bg-opacity-25 transition text-base">
                    💰 Financeiro
                </a>
            </div>
        </div>
    </div>

    {{-- ─── Alertas ───────────────────────────────────────────────────── --}}
    @if(count($alertas) > 0)
        <div class="space-y-2 mb-6">
            @foreach($alertas as $a)
                <a href="{{ $a['url'] }}"
                    class="flex items-center gap-3 px-5 py-3 rounded-xl border transition hover:shadow-sm
                        {{ $a['tipo'] === 'danger'
                            ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800'
                            : 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800' }}">
                    <p class="text-base font-bold {{ $a['tipo'] === 'danger' ? 'text-red-700 dark:text-red-300' : 'text-yellow-700 dark:text-yellow-300' }}">
                        {{ $a['msg'] }}
                    </p>
                    <svg class="w-4 h-4 ml-auto text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @endforeach
        </div>
    @endif

    {{-- ─── KPI Cards (linha 1 — Estoque + Vendas) ───────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        {{-- Estoque disponível --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-xl">🚗</div>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                    {{ $kpis['totalVeiculos'] }} total
                </span>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $kpis['disponiveis'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Veículos disponíveis</p>
        </div>

        {{-- Valor em estoque --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-xl">💎</div>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white">R$ {{ number_format($kpis['valorEstoque'], 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Valor em estoque</p>
        </div>

        {{-- Pedidos do mês --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-xl">🛒</div>
                @php
                    $diff = $kpis['pedidosMes'] - $kpis['pedidosMesPassado'];
                @endphp
                @if($diff !== 0)
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $diff > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        {{ $diff > 0 ? '↑' : '↓' }} {{ abs($diff) }}
                    </span>
                @endif
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $kpis['pedidosMes'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pedidos este mês</p>
        </div>

        {{-- Faturado no mês --}}
        <div class="bg-gradient-to-br from-[#1a3a5c] to-[#1e4f8a] rounded-2xl shadow-sm p-5 text-white">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-white bg-opacity-20 flex items-center justify-center text-xl">📈</div>
            </div>
            <p class="text-3xl font-black">R$ {{ number_format($kpis['faturadoMes'], 0, ',', '.') }}</p>
            <p class="text-sm text-blue-200 mt-1">Faturado em {{ now()->translatedFormat('F') }}</p>
        </div>
    </div>

    {{-- ─── KPI Cards (linha 2 — Clientes + Suporte + Financeiro) ──── --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        {{-- Clientes --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wide mb-2">👥 Clientes</p>
            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $kpis['totalClientes'] }}</p>
            <div class="flex gap-3 mt-2 text-sm">
                <span class="text-emerald-600 font-bold">+{{ $kpis['novosClientesMes'] }} este mês</span>
                @if($kpis['clientesPendentes'] > 0)
                    <span class="text-orange-500 font-bold">{{ $kpis['clientesPendentes'] }} pendente(s)</span>
                @endif
            </div>
        </div>

        {{-- Pedidos pendentes --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border {{ $kpis['pedidosPendentes'] > 0 ? 'border-orange-300 dark:border-orange-700' : 'border-gray-200 dark:border-gray-700' }} shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wide mb-2">⏳ Pendentes</p>
            <p class="text-3xl font-black {{ $kpis['pedidosPendentes'] > 0 ? 'text-orange-600' : 'text-gray-900 dark:text-white' }}">{{ $kpis['pedidosPendentes'] }}</p>
            <p class="text-sm text-gray-400 mt-2">Pedidos aguardando ação</p>
        </div>

        {{-- Tickets --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border {{ $kpis['ticketsUrgentes'] > 0 ? 'border-red-300 dark:border-red-700' : 'border-gray-200 dark:border-gray-700' }} shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wide mb-2">🎫 Tickets</p>
            <p class="text-3xl font-black {{ $kpis['ticketsUrgentes'] > 0 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">{{ $kpis['ticketsAbertos'] }}</p>
            <div class="flex gap-3 mt-2 text-sm">
                @if($kpis['ticketsUrgentes'] > 0) <span class="text-red-500 font-bold">🔴 {{ $kpis['ticketsUrgentes'] }} urgente(s)</span> @endif
                @if($kpis['ticketsWa'] > 0) <span class="text-green-500 font-bold">💬 {{ $kpis['ticketsWa'] }} WhatsApp</span> @endif
            </div>
        </div>

        {{-- A Receber --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border {{ $kpis['vencidos'] > 0 ? 'border-red-300 dark:border-red-700' : 'border-gray-200 dark:border-gray-700' }} shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wide mb-2">💰 A Receber</p>
            <p class="text-2xl font-black text-gray-900 dark:text-white">R$ {{ number_format($kpis['aReceber'], 0, ',', '.') }}</p>
            @if($kpis['vencidos'] > 0)
                <p class="text-sm text-red-500 font-bold mt-2">⚠️ {{ $kpis['vencidos'] }} vencido(s)</p>
            @endif
        </div>

        {{-- Recebido no mês --}}
        <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-200 dark:border-emerald-800 shadow-sm p-5">
            <p class="text-sm text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-wide mb-2">✅ Recebido</p>
            <p class="text-2xl font-black text-emerald-700 dark:text-emerald-300">R$ {{ number_format($kpis['pagosMes'], 0, ',', '.') }}</p>
            <p class="text-sm text-emerald-500 mt-2">Este mês</p>
        </div>
    </div>

    {{-- ─── Gráfico + Timeline ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        {{-- Gráfico de faturamento (2/3) --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-4">📊 Faturamento — Últimos 6 Meses</h3>
            <canvas id="revenueChart" class="w-full" style="height: 260px;"></canvas>
        </div>

        {{-- Timeline de atividades (1/3) --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5 overflow-hidden">
            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-4">⏱️ Atividades Recentes</h3>
            <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                @foreach($this->atividadesRecentes as $a)
                    <div class="flex gap-3 items-start">
                        <span class="text-lg flex-shrink-0 mt-0.5">{{ $a['icon'] }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-base text-gray-800 dark:text-gray-200 font-semibold truncate">{{ $a['msg'] }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                @php
                                    $statusBg = match($a['status'] ?? '') {
                                        'pendente', 'aberto'     => 'bg-yellow-100 text-yellow-700',
                                        'confirmado', 'resolvido'=> 'bg-emerald-100 text-emerald-700',
                                        'cancelado', 'fechado'   => 'bg-gray-100 text-gray-500',
                                        default                  => 'bg-blue-100 text-blue-700',
                                    };
                                @endphp
                                <span class="text-xs font-bold px-1.5 py-0.5 rounded-full {{ $statusBg }}">{{ $a['status'] }}</span>
                                <span class="text-xs text-gray-400">{{ $a['data']->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ─── Atalhos Rápidos ───────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-4">⚡ Ações Rápidas</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @php
                $atalhos = [
                    ['url' => '/admin/vehicles/create',    'icon' => '🚗', 'label' => 'Novo Veículo',    'color' => 'bg-emerald-50 border-emerald-200 hover:bg-emerald-100'],
                    ['url' => '/admin/orders',             'icon' => '🛒', 'label' => 'Ver Pedidos',     'color' => 'bg-orange-50 border-orange-200 hover:bg-orange-100'],
                    ['url' => '/admin/users',              'icon' => '👥', 'label' => 'Clientes',        'color' => 'bg-blue-50 border-blue-200 hover:bg-blue-100'],
                    ['url' => '/admin/tickets',            'icon' => '🎫', 'label' => 'Tickets',         'color' => 'bg-purple-50 border-purple-200 hover:bg-purple-100'],
                    ['url' => '/admin/whatsapp-inbox',     'icon' => '💬', 'label' => 'WhatsApp Inbox',  'color' => 'bg-green-50 border-green-200 hover:bg-green-100'],
                    ['url' => '/admin/gestao-financeira',  'icon' => '💰', 'label' => 'Cobranças',       'color' => 'bg-yellow-50 border-yellow-200 hover:bg-yellow-100'],
                    ['url' => '/admin/contracts',          'icon' => '📝', 'label' => 'Contratos',       'color' => 'bg-indigo-50 border-indigo-200 hover:bg-indigo-100'],
                    ['url' => '/admin/documents',          'icon' => '📄', 'label' => 'Documentos',      'color' => 'bg-cyan-50 border-cyan-200 hover:bg-cyan-100'],
                    ['url' => '/admin/vehicle-reports',    'icon' => '📋', 'label' => 'Laudos',          'color' => 'bg-teal-50 border-teal-200 hover:bg-teal-100'],
                    ['url' => '/admin/whatsapp-instancias','icon' => '📱', 'label' => 'WhatsApp Config', 'color' => 'bg-lime-50 border-lime-200 hover:bg-lime-100'],
                    ['url' => '/admin/configuracoes-gerais','icon'=> '⚙️', 'label' => 'Configurações',   'color' => 'bg-gray-50 border-gray-200 hover:bg-gray-100'],
                    ['url' => '/admin/relatorios',         'icon' => '📊', 'label' => 'Relatórios',      'color' => 'bg-rose-50 border-rose-200 hover:bg-rose-100'],
                ];
            @endphp
            @foreach($atalhos as $at)
                <a href="{{ $at['url'] }}"
                    class="flex flex-col items-center gap-2 p-4 rounded-xl border transition text-center {{ $at['color'] }}">
                    <span class="text-3xl">{{ $at['icon'] }}</span>
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $at['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ─── Chart.js ──────────────────────────────────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('revenueChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($grafico['labels']),
                    datasets: [{
                        label: 'Faturamento (R$)',
                        data: @json($grafico['valores']),
                        backgroundColor: [
                            'rgba(26, 58, 92, 0.15)',
                            'rgba(26, 58, 92, 0.25)',
                            'rgba(26, 58, 92, 0.35)',
                            'rgba(26, 58, 92, 0.50)',
                            'rgba(26, 58, 92, 0.70)',
                            'rgba(26, 58, 92, 0.90)',
                        ],
                        borderColor: 'rgba(26, 58, 92, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return 'R$ ' + ctx.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 0 });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.04)' },
                            ticks: {
                                callback: function(v) { return 'R$ ' + (v/1000).toFixed(0) + 'k'; },
                                font: { weight: 'bold', size: 13 }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { weight: 'bold', size: 13 } }
                        }
                    }
                }
            });
        });
    </script>

</x-filament-panels::page>
