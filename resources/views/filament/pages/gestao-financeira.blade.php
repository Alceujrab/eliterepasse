<x-filament-panels::page>

    {{-- ─── Hero ──────────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1a3a5c] via-[#1e4f8a] to-[#0f2d4e] p-6 mb-6 shadow-xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-blue-300 opacity-5 blur-3xl rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div class="flex items-center gap-4 text-white">
                <div class="w-12 h-12 rounded-2xl bg-white bg-opacity-20 flex items-center justify-center text-2xl">💰</div>
                <div>
                    <h1 class="text-3xl font-black tracking-tight">Gestão Financeira</h1>
                    <p class="text-blue-200 text-base">Cobranças, boletos, notas fiscais e extratos</p>
                </div>
            </div>
            <button wire:click="novaCobranca()"
                class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-black px-6 py-3.5 rounded-xl shadow-lg transition text-base">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Cobrança
            </button>
        </div>

        {{-- KPI mini --}}
        <div class="relative grid grid-cols-2 md:grid-cols-4 gap-3 mt-5">
            @php $kpis = $this->kpis; @endphp
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl px-4 py-3 border border-white border-opacity-10">
                <p class="text-blue-200 text-sm font-bold mb-1">Emitido no Mês</p>
                <p class="text-white font-black text-2xl">R$ {{ number_format($kpis['total_mes'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl px-4 py-3 border border-white border-opacity-10">
                <p class="text-blue-200 text-sm font-bold mb-1">A Receber</p>
                <p class="text-white font-black text-2xl">R$ {{ number_format($kpis['em_aberto'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl px-4 py-3 border border-white border-opacity-10">
                <p class="text-blue-200 text-sm font-bold mb-1">Recebido no Mês</p>
                <p class="text-white font-black text-2xl">R$ {{ number_format($kpis['pagos_mes'], 0, ',', '.') }}</p>
            </div>
            <div class="{{ $kpis['vencidos'] > 0 ? 'bg-red-500 bg-opacity-80' : 'bg-white bg-opacity-10' }} backdrop-blur-sm rounded-xl px-4 py-3 border border-white border-opacity-10">
                <p class="{{ $kpis['vencidos'] > 0 ? 'text-red-200' : 'text-blue-200' }} text-sm font-bold mb-1">Vencidos</p>
                <p class="text-white font-black text-2xl">{{ $kpis['vencidos'] }}</p>
            </div>
        </div>
    </div>

    {{-- ─── Alertas: Pedidos sem cobrança ──────────────────────────────── --}}
    @if($this->pedidosSemFinanciero->isNotEmpty())
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-2xl p-4 mb-5">
            <div class="flex items-start gap-3">
                <span class="text-2xl flex-shrink-0">⚠️</span>
                <div class="flex-1">
                    <p class="font-black text-yellow-800 dark:text-yellow-200 text-base mb-2">
                        {{ $this->pedidosSemFinanciero->count() }} pedido(s) confirmado(s) sem cobrança registrada
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($this->pedidosSemFinanciero as $ord)
                            <button wire:click="novaCobranca({{ $ord->id }})"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-yellow-200 dark:bg-yellow-800 hover:bg-yellow-300 dark:hover:bg-yellow-700 text-yellow-900 dark:text-yellow-100 rounded-xl text-sm font-bold transition">
                                ➕ {{ $ord->numero }} — {{ $ord->user?->razao_social ?? $ord->user?->name }}
                                (R$ {{ number_format($ord->valor_compra, 0, ',', '.') }})
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ─── Form Criar/Editar ─────────────────────────────────────────── --}}
    @if($showForm)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm mb-6 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-white dark:from-gray-700 dark:to-gray-800">
                <h2 class="font-black text-gray-800 dark:text-white">
                    {{ $editingId ? '✏️ Editar Cobrança' : '➕ Nova Cobrança' }}
                </h2>
                <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {{-- Pedido --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Pedido *</label>
                        <div class="flex gap-2">
                            <input wire:model="orderId" type="number" placeholder="ID do Pedido"
                                class="w-32 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono"/>
                            @if($orderId)
                                @php $ord = \App\Models\Order::with('user')->find($orderId); @endphp
                                @if($ord)
                                    <div class="flex-1 bg-blue-50 dark:bg-blue-900/20 rounded-xl px-4 py-3 border border-blue-200 dark:border-blue-700">
                                        <p class="text-sm font-black text-blue-800 dark:text-blue-200">{{ $ord->numero }}</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ $ord->user?->razao_social ?? $ord->user?->name }} · R$ {{ number_format($ord->valor_compra, 2, ',', '.') }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                        @error('orderId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Número da Fatura --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Nº da Fatura</label>
                        <input wire:model="numeroFatura" type="text" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                    </div>

                    {{-- Valor --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Valor (R$)</label>
                        <input wire:model="valor" type="number" step="0.01" placeholder="0,00"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                        @error('valor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Status</label>
                        <select wire:model="status" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach(\App\Models\Financial::statusLabels() as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Forma de Pagamento --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Forma de Pagamento</label>
                        <select wire:model="formaPagamento" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach(\App\Models\Financial::formasPagamento() as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Vencimento --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Vencimento</label>
                        <input wire:model="dataVencimento" type="date"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                        @error('dataVencimento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Pagamento --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Data Pagamento</label>
                        <input wire:model="dataPagamento" type="date"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                    </div>

                    {{-- Nº NF --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Nº Nota Fiscal</label>
                        <input wire:model="notaFiscalNumero" type="text" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                    </div>

                    {{-- URL Boleto --}}
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">URL do Boleto</label>
                        <input wire:model="boletoUrl" type="url" placeholder="https://..."
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                        @error('boletoUrl') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Linha Digitável --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Linha Digitável</label>
                        <input wire:model="digitableLine" type="text" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                    </div>

                    {{-- URL NF --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">URL Nota Fiscal</label>
                        <input wire:model="invoiceUrl" type="url" placeholder="https://..."
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                        @error('invoiceUrl') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Observações --}}
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Observações</label>
                        <textarea wire:model="observacoes" rows="2"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-gray-100 dark:border-gray-700">
                    <button wire:click="$set('showForm', false)"
                        class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm">
                        Cancelar
                    </button>
                    <button wire:click="salvar"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-black hover:opacity-90 transition text-sm shadow-lg shadow-blue-500/20">
                        {{ $editingId ? 'Salvar Alterações' : 'Criar Cobrança' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ─── Filtros + Busca ────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 mb-5">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="buscaCliente" type="text"
                    placeholder="Buscar por cliente, CNPJ ou pedido..."
                    class="w-full pl-9 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
            </div>
            <div class="flex gap-1.5 flex-wrap">
                @foreach(['todos' => 'Todos', 'em_aberto' => '🟡 Em Aberto', 'pago' => '🟢 Pagos', 'vencido' => '🔴 Vencidos', 'cancelado' => '⚫ Cancelados'] as $val => $label)
                    <button wire:click="$set('filtroStatus', '{{ $val }}')"
                        class="px-4 py-2.5 text-sm font-bold rounded-xl transition
                            {{ $filtroStatus === $val ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ─── Tabela de Cobranças ─────────────────────────────────────────── --}}
    @if($this->financiais->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm py-16 text-center">
            <div class="text-5xl mb-3">🧾</div>
            <p class="text-gray-400 font-semibold">Nenhuma cobrança encontrada.</p>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                            <th class="text-left px-5 py-3 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fatura</th>
                            <th class="text-left px-5 py-3 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cliente</th>
                            <th class="text-left px-5 py-3 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pedido</th>
                            <th class="text-right px-5 py-3 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor</th>
                            <th class="text-center px-5 py-3 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vencimento</th>
                            <th class="text-center px-5 py-3 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-center px-5 py-3 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Docs</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @foreach($this->financiais as $fin)
                            @php
                                $vencido = $fin->data_vencimento && now()->isAfter($fin->data_vencimento) && $fin->status === 'em_aberto';
                                $statusBg = match($fin->status) {
                                    'pago'      => 'bg-emerald-100 text-emerald-700',
                                    'vencido'   => 'bg-red-100 text-red-700',
                                    'cancelado' => 'bg-gray-100 text-gray-500',
                                    'estornado' => 'bg-blue-100 text-blue-700',
                                    default     => $vencido ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition {{ $vencido ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">

                                {{-- Fatura --}}
                                <td class="px-5 py-4">
                                    <p class="text-sm font-black text-gray-900 dark:text-white font-mono">{{ $fin->numero_fatura ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $fin->created_at->format('d/m/Y') }}</p>
                                </td>

                                {{-- Cliente --}}
                                <td class="px-5 py-4">
                                    <p class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                        {{ $fin->order?->user?->razao_social ?? $fin->order?->user?->name ?? '—' }}
                                    </p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $fin->order?->user?->cnpj ?? '' }}</p>
                                </td>

                                {{-- Pedido --}}
                                <td class="px-5 py-4">
                                    <p class="text-xs font-bold text-gray-600 dark:text-gray-400 font-mono">{{ $fin->order?->numero ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $fin->order?->vehicle?->brand }} {{ $fin->order?->vehicle?->model }}</p>
                                </td>

                                {{-- Valor --}}
                                <td class="px-5 py-4 text-right">
                                    <p class="text-sm font-black text-gray-900 dark:text-white">
                                        {{ $fin->valor ? 'R$ ' . number_format($fin->valor, 2, ',', '.') : '—' }}
                                    </p>
                                    @if($fin->forma_pagamento)
                                        <p class="text-xs text-gray-400">{{ \App\Models\Financial::formasPagamento()[$fin->forma_pagamento] ?? $fin->forma_pagamento }}</p>
                                    @endif
                                </td>

                                {{-- Vencimento --}}
                                <td class="px-5 py-4 text-center">
                                    @if($fin->data_vencimento)
                                        <p class="text-sm font-bold {{ $vencido ? 'text-red-600' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $fin->data_vencimento->format('d/m/Y') }}
                                        </p>
                                        @if($vencido)
                                            <p class="text-[10px] text-red-500 font-bold">⚠️ VENCIDO</p>
                                        @elseif($fin->data_vencimento->isFuture())
                                            <p class="text-[10px] text-gray-400">em {{ $fin->data_vencimento->diffForHumans() }}</p>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $statusBg }}">
                                        {{ \App\Models\Financial::statusLabels()[$fin->status] ?? $fin->status }}
                                    </span>
                                    @if($fin->data_pagamento)
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $fin->data_pagamento->format('d/m/Y') }}</p>
                                    @endif
                                </td>

                                {{-- Docs --}}
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($fin->boleto_url)
                                            <a href="{{ $fin->boleto_url }}" target="_blank"
                                                title="Ver Boleto"
                                                class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center text-sm hover:bg-orange-200 transition">
                                                🎫
                                            </a>
                                        @endif
                                        @if($fin->invoice_url)
                                            <a href="{{ $fin->invoice_url }}" target="_blank"
                                                title="Ver Nota Fiscal"
                                                class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center text-sm hover:bg-blue-200 transition">
                                                🧾
                                            </a>
                                        @endif
                                        @if(! $fin->boleto_url && ! $fin->invoice_url)
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Ações --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1.5 justify-end">
                                        @if($fin->status === 'em_aberto')
                                            <button wire:click="marcarPago({{ $fin->id }})"
                                                title="Marcar como Pago"
                                                class="px-2.5 py-1.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 rounded-lg text-xs font-bold transition">
                                                ✅ Pago
                                            </button>
                                        @endif
                                        <button wire:click="editarCobranca({{ $fin->id }})"
                                            class="w-7 h-7 bg-orange-100 text-orange-700 hover:bg-orange-200 rounded-lg flex items-center justify-center text-xs font-bold transition">
                                            ✏️
                                        </button>
                                        <button wire:click="excluir({{ $fin->id }})"
                                            wire:confirm="Excluir esta cobrança?"
                                            class="w-7 h-7 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg flex items-center justify-center text-xs font-bold transition">
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</x-filament-panels::page>
