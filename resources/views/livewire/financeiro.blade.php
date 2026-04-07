<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Financeiro & Faturamento') }}
            </h2>
            @if($company)
                <span class="text-sm text-gray-200 bg-white/10 px-3 py-1 rounded-full backdrop-blur-md border border-white/20">Loja Atual: {{ $company->razao_social }} ({{ $company->cnpj }})</span>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(!$company)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-xl shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="..." clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 font-medium">Conta sem CNPJ vinculado.</p>
                        </div>
                    </div>
                </div>
            @elseif($orders->isEmpty())
                <div class="bg-white p-10 rounded-xl shadow-sm text-center border border-gray-100">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Nenhum faturamento pendente ou histórico financeiro disponível</h3>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6">
                    @foreach($orders as $order)
                        <div class="bg-white overflow-hidden shadow-sm hover:shadow-md rounded-xl border border-gray-100 p-6 flex flex-col md:flex-row items-center justify-between transition">
                            
                            <!-- Esquerda: Resumo Pedido -->
                            <div class="flex flex-col mb-4 md:mb-0 w-full md:w-1/3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                                <h3 class="text-2xl font-black text-gray-900">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                     Gerado em {{ $order->created_at->format('d/m/Y') }}
                                </p>
                            </div>

                            <!-- Centro: Veiculos (Micro list) -->
                            <div class="flex-1 px-4 w-full md:w-auto mb-4 md:mb-0">
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 border-b pb-1">Itens Adquiridos</div>
                                <div class="space-y-1">
                                    @foreach($order->vehicles as $v)
                                        <div class="text-sm text-gray-700">🚙 {{ $v->brand }} {{ $v->model }} (Chassi: {{ substr($v->plate, -4) }})</div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Direita: Ações / Boleto -->
                            <div class="w-full md:w-1/3 flex flex-col items-end space-y-3">
                                @if($order->financial && $order->financial->status == 'em_aberto')
                                    <div class="bg-orange-50 border border-orange-200 rounded p-3 w-full text-center">
                                        <p class="text-xs text-orange-800 font-bold mb-2">VENCIMENTO PRÓXIMO</p>
                                        <div class="flex flex-col space-y-2">
                                            <a href="{{ $order->financial->boleto_url }}" target="_blank" class="inline-flex justify-center items-center px-4 py-2 bg-orange_cta text-white text-sm font-bold rounded shadow hover:bg-[#e06512] transition">
                                                Imprimir Boleto
                                            </a>
                                            <button class="inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-bold rounded shadow-sm hover:bg-gray-50 transition" onclick="alert('Código copiado!')">
                                                Copiar Código de Barras
                                            </button>
                                        </div>
                                    </div>
                                @elseif($order->financial && $order->financial->status == 'pago')
                                     <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800">
                                         ✓ PAGAMENTO CONFIRMADO
                                     </div>
                                     @if($order->financial->invoice_url)
                                         <a href="{{ $order->financial->invoice_url }}" target="_blank" class="text-sm text-primary hover:underline font-semibold flex items-center mt-2">
                                             <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                             Baixar XML / NF-e
                                         </a>
                                     @endif
                                @else
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-gray-100 text-gray-600">
                                         EM PROCESSAMENTO
                                     </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
