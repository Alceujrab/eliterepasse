<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Meus Pedidos') }}
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
                    <p class="text-sm text-yellow-700 font-medium">Conta sem CNPJ vinculado.</p>
                </div>
            @elseif($orders->isEmpty())
                <div class="bg-white p-10 rounded-xl shadow-sm text-center border border-gray-100">
                    <p class="mt-2 text-sm text-gray-500">Nenhum pedido realizado até o momento.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($orders as $order)
                        <div class="bg-white overflow-hidden shadow-sm hover:shadow-md rounded-xl border border-gray-100 p-6 transition">
                            <div class="flex justify-between items-center border-b pb-4 mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h3>
                                    <p class="text-sm text-gray-500">Emitido em {{ $order->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-blue-100 text-blue-800 uppercase tracking-widest shadow-sm">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-4">
                                @foreach($order->vehicles as $v)
                                    <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                                        <div class="w-20 h-20 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0 border border-gray-200">
                                            @if($v->image_url)
                                                <img src="{{ $v->image_url }}" alt="Car" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-full h-full text-gray-400 p-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-900 text-lg">{{ $v->brand }} {{ $v->model }}</h4>
                                            <p class="text-sm text-gray-500">Ano: {{ $v->year }} | Chassi final: <span class="font-bold text-gray-800">{{ substr($v->plate, -4) }}</span></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-black text-gray-900 tracking-tight">R$ {{ number_format($v->price, 2, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-6 text-right pt-4 border-t flex justify-between items-end">
                                <a href="{{ route('suporte') }}" class="text-sm text-primary hover:text-blue-800 font-bold underline transition">Abrir chamado sobre este pedido</a>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium mb-1">Total Negociado</p>
                                    <p class="text-3xl font-black text-gray-900 tracking-tighter">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
