@php
    $record = $getRecord();
    $histories = $record->histories()->with('user')->latest()->get();
    $icons = \App\Models\OrderHistory::acaoIcons();
    $labels = \App\Models\OrderHistory::acaoLabels();

    $corAcao = [
        'pedido_criado'         => 'bg-blue-500',
        'pedido_confirmado'     => 'bg-emerald-500',
        'contrato_gerado'       => 'bg-indigo-500',
        'contrato_assinado'     => 'bg-purple-500',
        'fatura_gerada'         => 'bg-amber-500',
        'pagamento_confirmado'  => 'bg-green-600',
        'pedido_cancelado'      => 'bg-red-500',
    ];
@endphp

<div class="w-full">
    @if($histories->isEmpty())
        <p class="text-sm text-gray-400 italic">Nenhum evento registrado.</p>
    @else
        <div class="relative space-y-0">
            @foreach($histories as $hist)
                @php
                    $icon = $icons[$hist->acao] ?? '📌';
                    $label = $hist->descricao ?? ($labels[$hist->acao] ?? $hist->acao);
                    $bg = $corAcao[$hist->acao] ?? 'bg-gray-400';
                    $isLast = $loop->last;
                @endphp
                <div class="flex gap-4 pb-6 relative">
                    {{-- Linha vertical --}}
                    @if(! $isLast)
                        <div class="absolute left-[18px] top-9 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                    @endif

                    {{-- Ícone --}}
                    <div class="relative z-10 flex-shrink-0 w-9 h-9 rounded-full {{ $bg }} text-white flex items-center justify-center text-base shadow-sm">
                        {{ $icon }}
                    </div>

                    {{-- Conteúdo --}}
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $label }}</p>

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $hist->created_at->format('d/m/Y H:i') }}
                            </span>

                            @if($hist->user)
                                <span class="text-xs text-gray-400 dark:text-gray-500">
                                    por {{ $hist->user->name }}
                                </span>
                            @endif

                            @if($hist->status_de && $hist->status_para)
                                <span class="text-xs font-mono bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded px-1.5 py-0.5">
                                    {{ $hist->status_de }} → {{ $hist->status_para }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
