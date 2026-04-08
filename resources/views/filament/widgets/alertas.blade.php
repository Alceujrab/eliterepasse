<x-filament-widgets::widget>
    @php $alertas = $this->getAlertas(); @endphp

    @if(count($alertas) > 0)
        <div class="space-y-2">
            @foreach($alertas as $alerta)
                @php
                    $cores = [
                        'danger'  => 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200',
                        'warning' => 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200',
                        'info'    => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200',
                        'success' => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200',
                    ];
                    $linkCores = [
                        'danger'  => 'text-red-700 dark:text-red-300 hover:text-red-900 dark:hover:text-red-100',
                        'warning' => 'text-amber-700 dark:text-amber-300 hover:text-amber-900 dark:hover:text-amber-100',
                        'info'    => 'text-blue-700 dark:text-blue-300 hover:text-blue-900 dark:hover:text-blue-100',
                        'success' => 'text-green-700 dark:text-green-300 hover:text-green-900 dark:hover:text-green-100',
                    ];
                    $cor = $cores[$alerta['tipo']] ?? $cores['info'];
                    $linkCor = $linkCores[$alerta['tipo']] ?? $linkCores['info'];
                @endphp
                <div class="flex items-center justify-between px-4 py-3 rounded-xl border {{ $cor }}">
                    <div class="flex items-center gap-3">
                        <span class="text-lg leading-none flex-shrink-0">{{ $alerta['icone'] }}</span>
                        <span class="text-sm font-semibold">{{ $alerta['titulo'] }}</span>
                    </div>
                    <a href="{{ $alerta['url'] }}" class="text-xs font-bold underline {{ $linkCor }} flex-shrink-0 ml-4">
                        {{ $alerta['link'] }} →
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-widgets::widget>
