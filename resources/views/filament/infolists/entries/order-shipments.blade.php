@php
    $record = $getRecord();
    $shipments = $record->shipments()->with('user')->latest()->get();
    $tipoLabels = \App\Models\OrderShipment::tipoDocumentoLabels();
    $metodoLabels = \App\Models\OrderShipment::metodoEnvioLabels();
    $statusLabels = \App\Models\OrderShipment::statusLabels();

    $statusColors = [
        'disponivel' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
        'despachado'  => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
        'entregue'    => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
    ];

    $statusIconBg = [
        'disponivel' => 'bg-blue-500',
        'despachado'  => 'bg-amber-500',
        'entregue'    => 'bg-green-600',
    ];

    $statusIcons = [
        'disponivel' => '📥',
        'despachado'  => '📦',
        'entregue'    => '✅',
    ];
@endphp

<div class="w-full space-y-4">
    @if($shipments->isEmpty())
        <p class="text-sm text-gray-400 italic">Nenhum documento enviado ainda.</p>
    @else
        @foreach($shipments as $shipment)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
                {{-- Header --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $statusIconBg[$shipment->status] ?? 'bg-gray-400' }} text-white flex items-center justify-center text-lg shadow-sm">
                            {{ $statusIcons[$shipment->status] ?? '📄' }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                {{ $tipoLabels[$shipment->tipo_documento] ?? $shipment->tipo_documento }}
                            </p>
                            @if($shipment->titulo)
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $shipment->titulo }}</p>
                            @endif
                        </div>
                    </div>

                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$shipment->status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $statusLabels[$shipment->status] ?? $shipment->status }}
                    </span>
                </div>

                {{-- Details Grid --}}
                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                    {{-- Documento --}}
                    @if($shipment->file_path)
                        <div>
                            <span class="text-gray-400 dark:text-gray-500 block">Documento</span>
                            <a href="{{ asset('storage/' . $shipment->file_path) }}"
                               target="_blank"
                               class="text-primary-600 hover:underline font-medium inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Baixar
                            </a>
                        </div>
                    @endif

                    {{-- Método de Envio --}}
                    @if($shipment->metodo_envio)
                        <div>
                            <span class="text-gray-400 dark:text-gray-500 block">Envio</span>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">
                                {{ $metodoLabels[$shipment->metodo_envio] ?? $shipment->metodo_envio }}
                                @if($shipment->metodo_envio_detalhe)
                                    <span class="text-gray-400">({{ $shipment->metodo_envio_detalhe }})</span>
                                @endif
                            </span>
                        </div>
                    @endif

                    {{-- Código de Rastreio --}}
                    @if($shipment->codigo_rastreio)
                        <div>
                            <span class="text-gray-400 dark:text-gray-500 block">Rastreio</span>
                            <span class="text-gray-700 dark:text-gray-300 font-mono font-bold">{{ $shipment->codigo_rastreio }}</span>
                        </div>
                    @endif

                    {{-- Data Despacho --}}
                    @if($shipment->despachado_em)
                        <div>
                            <span class="text-gray-400 dark:text-gray-500 block">Despachado em</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($shipment->despachado_em)->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>

                {{-- Comprovante de Despacho --}}
                @if($shipment->comprovante_despacho_path)
                    <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                        <a href="{{ asset('storage/' . $shipment->comprovante_despacho_path) }}"
                           target="_blank"
                           class="text-xs text-amber-600 hover:underline font-medium inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Ver Comprovante de Despacho
                        </a>
                    </div>
                @endif

                {{-- Observações --}}
                @if($shipment->observacoes)
                    <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                        <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $shipment->observacoes }}</p>
                    </div>
                @endif

                {{-- Footer --}}
                <div class="mt-2 flex items-center gap-3 text-[11px] text-gray-400 dark:text-gray-500">
                    <span>{{ $shipment->created_at->format('d/m/Y H:i') }}</span>
                    @if($shipment->user)
                        <span>por {{ $shipment->user->name }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
