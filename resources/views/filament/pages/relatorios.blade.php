<x-filament-panels::page>

    {{-- ─── Filtro de Período ─────────────────────────────────────── --}}
    <div class="flex items-center gap-4 mb-6">
        <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Período:</span>
        <div class="flex gap-2 flex-wrap">
            @foreach([7 => '7 dias', 30 => '30 dias', 60 => '60 dias', 90 => '90 dias', 365 => '1 ano'] as $val => $label)
                <button wire:click="$set('periodo', '{{ $val }}')" wire:then="filtrar"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition-all
                        {{ $this->periodo == $val
                            ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20'
                            : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:border-orange-500 hover:text-orange-500' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ─── Cards de Resumo Financeiro ────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        @php
            $cards = [
                ['label' => 'Pedidos no Período', 'value' => number_format($this->resumoFinanceiro['pedidos_total']), 'icon' => '🛒', 'color' => 'blue'],
                ['label' => 'Confirmados', 'value' => number_format($this->resumoFinanceiro['pedidos_confirmados']), 'icon' => '✅', 'color' => 'green'],
                ['label' => 'Faturados', 'value' => number_format($this->resumoFinanceiro['pedidos_faturados']), 'icon' => '📄', 'color' => 'indigo'],
                ['label' => 'Cancelados', 'value' => number_format($this->resumoFinanceiro['pedidos_cancelados']), 'icon' => '❌', 'color' => 'red'],
                ['label' => 'Receita Total', 'value' => 'R$ ' . number_format($this->resumoFinanceiro['receita_total'], 0, ',', '.'), 'icon' => '💰', 'color' => 'green'],
                ['label' => 'Ticket Médio', 'value' => 'R$ ' . number_format($this->resumoFinanceiro['ticket_medio'], 0, ',', '.'), 'icon' => '📊', 'color' => 'orange'],
            ];
        @endphp

        @foreach($cards as $card)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                <div class="text-2xl mb-1">{{ $card['icon'] }}</div>
                <div class="text-lg font-black text-gray-900 dark:text-white">{{ $card['value'] }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ─── Gráficos ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">

        {{-- Receita por dia --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">📈 Receita por Dia (R$)</h3>
            <div style="position:relative; height: 220px;">
                <canvas id="chartReceita"></canvas>
            </div>
        </div>

        {{-- Clientes novos por dia --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">👥 Novos Clientes por Dia</h3>
            <div style="position:relative; height: 220px;">
                <canvas id="chartClientes"></canvas>
            </div>
        </div>
    </div>

    {{-- ─── Resumo Estoque + Top Vendas ────────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">

        {{-- Estoque --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">🚗 Resumo do Estoque</h3>
            <div class="space-y-3">
                @php
                    $estoqueItems = [
                        ['label' => 'Total cadastrado', 'value' => $this->resumoEstoque['total'], 'color' => 'gray'],
                        ['label' => 'Disponíveis', 'value' => $this->resumoEstoque['disponivel'], 'color' => 'green'],
                        ['label' => 'Reservados', 'value' => $this->resumoEstoque['reservado'], 'color' => 'yellow'],
                        ['label' => 'Vendidos', 'value' => $this->resumoEstoque['vendido'], 'color' => 'blue'],
                        ['label' => 'Abaixo da FIPE', 'value' => $this->resumoEstoque['abaixo_fipe'], 'color' => 'orange'],
                    ];
                @endphp
                @foreach($estoqueItems as $item)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $item['label'] }}</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($item['value']) }}</span>
                    </div>
                    @if (!$loop->last)<div class="h-px bg-gray-100 dark:bg-gray-700"></div>@endif
                @endforeach
                <div class="h-px bg-gray-100 dark:bg-gray-700"></div>
                <div class="flex items-center justify-between pt-1">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Valor em estoque</span>
                    <span class="text-sm font-black text-green-600">
                        R$ {{ number_format($this->resumoEstoque['valor_total'], 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Preço médio</span>
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">
                        R$ {{ number_format($this->resumoEstoque['valor_medio'], 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Gráfico donut estoque --}}
            <div class="mt-5" style="position:relative; height: 160px;">
                <canvas id="chartEstoque"></canvas>
            </div>
        </div>

        {{-- Top Vendas --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">🏆 Top Veículos Vendidos</h3>
            @if(count($this->topVendas) > 0)
                <div class="space-y-2">
                    @foreach($this->topVendas as $i => $item)
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 text-xs font-black flex items-center justify-center flex-shrink-0">
                                {{ $i + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $item['veiculo'] }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $item['placa'] }}</div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="text-sm font-black text-green-600">R$ {{ number_format($item['total'], 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-400">{{ $item['qtd'] }}x venda</div>
                            </div>
                        </div>
                        @if (!$loop->last)<div class="h-px bg-gray-50 dark:bg-gray-700"></div>@endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-400 text-sm">
                    Nenhuma venda no período selecionado.
                </div>
            @endif
        </div>
    </div>

    {{-- ─── Scripts Chart.js ────────────────────────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>

    <script>
        document.addEventListener('livewire:navigated', () => initCharts());
        document.addEventListener('livewire:updated', () => {
            Object.values(Chart.instances).forEach(c => c.destroy());
            initCharts();
        });

        function initCharts() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#9ca3af' : '#6b7280';
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)';

            // ─ Receita por dia ─
            const dadosReceita = @json($this->dadosGrafico);
            const ctxReceita = document.getElementById('chartReceita')?.getContext('2d');
            if (ctxReceita && dadosReceita.length > 0) {
                new Chart(ctxReceita, {
                    type: 'bar',
                    data: {
                        labels: dadosReceita.map(d => d.data),
                        datasets: [{
                            label: 'Receita (R$)',
                            data: dadosReceita.map(d => d.total),
                            backgroundColor: 'rgba(249,115,22,0.7)',
                            borderColor: '#f97316',
                            borderWidth: 1,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { ticks: { color: textColor, callback: v => 'R$ ' + (v/1000).toFixed(0) + 'k' }, grid: { color: gridColor } },
                            x: { ticks: { color: textColor }, grid: { display: false } }
                        }
                    }
                });
            }

            // ─ Clientes novos ─
            const dadosClientes = @json($this->clientesNovos);
            const ctxClientes = document.getElementById('chartClientes')?.getContext('2d');
            if (ctxClientes && dadosClientes.length > 0) {
                new Chart(ctxClientes, {
                    type: 'line',
                    data: {
                        labels: dadosClientes.map(d => d.data),
                        datasets: [{
                            label: 'Novos Clientes',
                            data: dadosClientes.map(d => d.qtd),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.1)',
                            fill: true, tension: 0.4, pointRadius: 4,
                            pointBackgroundColor: '#3b82f6',
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { ticks: { color: textColor, stepSize: 1 }, grid: { color: gridColor } },
                            x: { ticks: { color: textColor }, grid: { display: false } }
                        }
                    }
                });
            }

            // ─ Donut Estoque ─
            const est = @json($this->resumoEstoque);
            const ctxEst = document.getElementById('chartEstoque')?.getContext('2d');
            if (ctxEst) {
                new Chart(ctxEst, {
                    type: 'doughnut',
                    data: {
                        labels: ['Disponível', 'Reservado', 'Vendido'],
                        datasets: [{
                            data: [est.disponivel, est.reservado, est.vendido],
                            backgroundColor: ['#22c55e', '#f59e0b', '#3b82f6'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '70%',
                        plugins: {
                            legend: { position: 'right', labels: { color: textColor, boxWidth: 12 } }
                        }
                    }
                });
            }
        }

        initCharts();
    </script>

</x-filament-panels::page>
